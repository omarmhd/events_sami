<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

use App\Exports\TicketExport;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventAccessPass;
use App\Models\EventInvitation;
use App\Models\InvitationQr;
use App\Models\Ticket;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function index(Request $request, SubscriptionService $subscriptionService)
    {
        $user = $request->user();
        $company = $user ? $user->company : null;

        $subscription = null;
        if ($company) {
            $subscription = $subscriptionService->activeSubscriptionFor($company);
        }

        $companyId = $company?->id;

        $eventsCount = $company
            ? Event::where('company_id', $companyId)->count()
            : Event::count();

        $invitationsCount = $company
            ? EventInvitation::where('company_id', $companyId)->count()
            : EventInvitation::count();

        // Accepted invitations count for acceptance rate KPI
        $acceptedCount = $company
            ? EventInvitation::where('company_id', $companyId)->where('status', 'accepted')->count()
            : EventInvitation::where('status', 'accepted')->count();

        // Checked-in (QR scanned) count
        $checkedInCount = $company
            ? \App\Models\InvitationQr::whereHas('invitation', fn($q) => $q->where('company_id', $companyId))
                ->where('is_used', true)->where('type', 'invitation')->count()
            : \App\Models\InvitationQr::where('is_used', true)->where('type', 'invitation')->count();

        // Fetch the 5 most recent events for the dashboard "Recent Events" section.
        // Scoped to the authenticated user's company for proper multi-tenant isolation.
        $recentEvents = $company
            ? Event::select('id', 'title', 'name', 'event_type', 'date', 'location_name', 'status')
                ->where('company_id', $companyId)
                ->orderByDesc('id')
                ->limit(5)
                ->get()
                ->map(fn($e) => (object)[
                    'name'       => $e->title ?: $e->name,
                    'type'       => $e->event_type,
                    'event_date' => $e->date,
                    'location'   => $e->location_name,
                    'status'     => $e->status,
                ])
            : collect();

        return view("subscriber.panel.index", compact(
            'company',
            'subscription',
            'eventsCount',
            'invitationsCount',
            'acceptedCount',
            'checkedInCount',
            'recentEvents'
        ));
    }
    public function all_emps(Request $request)
    {

        $query = Employee::query();

        if ($request->filled('searchInput')) {
            $search = $request->input('searchInput');

            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('employee_number', 'LIKE', "%{$search}%");

            });
        }
        $emps = $query->latest()->paginate(10);
        $emps->appends($request->all());

        return view("subscriber.panel.emps", compact("emps"));
    }


    public function checked_in(Request $request,$id=null){
        if (!$request->qrData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR data'
            ]);
        }

        $invitationQr = InvitationQr::where('token',$request->qrData)->first();
        $userCompanyId = $request->user() ? $request->user()->company_id : null;

        if ($invitationQr) {
            if ($userCompanyId && $invitationQr->invitation && (int) $invitationQr->invitation->company_id !== (int) $userCompanyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR does not belong to your company'
                ], 403);
            }

            if ($invitationQr->is_used) {
                return response()->json([
                    'success' => false,
                    'message' => 'This guest has already checked in'
                ]);
            }

            $invitationQr->update([
                'is_used' => true,
                'used_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful'
            ],200);
        }

        $accessPass = EventAccessPass::where('token', $request->qrData)->first();

        if (!$accessPass) {
            return response()->json([
                'success' => false,
                'message' => 'QR code is not valid'
            ]);
        }

        if ($userCompanyId && (int) $accessPass->company_id !== (int) $userCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'QR does not belong to your company'
            ], 403);
        }

        if ($accessPass->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'This QR code has already been used'
            ]);
        }

        $accessPass->update([
            'is_used' => true,
            'used_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful'
        ],200);


//        if($request->qrData){
//            $ticket=Ticket::where("id",$request->qrData??$id)->update([
//                "checked_in_at"=>Carbon::now()
//            ]);
//            return response()->json(["success"=>true,"message"=>"success"]);
//        }
//        if($id){
//            $ticket=Ticket::where("id",$id)->update([
//                "checked_in_at"=>Carbon::now()
//            ]);
//            return back();
//
//        }

    }


    public function search_on_ticket(Request $request){
        if($request->searchInput){
            $tickets=Ticket::where("checked_in_at",null)
                ->where("employee_email",$request->searchInput)
                ->orwhere("employee_number",$request->searchInput)
                ->orWhere("employee_name","like","%".$request->searchInput."%")
                ->orWhere("employee_id",$request->searchInput)->
                get();
            return view("subscriber.panel.register_attendance",compact("tickets"));
        }
        return view("subscriber.panel.register_attendance");

    }

    public function attendance_list(Request $request){
        $tickets=Ticket::where("checked_in_at","<>",null)->get();

        if($request->searchInput){
            $tickets = Ticket::where("checked_in_at", "<>", null)
                ->where("is_children", "no")
                ->where(function($query) use ($request) {
                    $query->where("employee_email", $request->searchInput)
                        ->orWhere("employee_number", $request->searchInput)
                        ->orWhere("employee_name", $request->searchInput)
                        ->orWhere("employee_id", $request->searchInput);
                })
                ->get();

        }
        return view("subscriber.panel.attendance_list",compact("tickets"));
    }

    public function statistics(Request $request)
    {
        $user      = auth()->user();
        $companyId = $user?->company_id;

        // ── 1. جلب قائمة الفعاليات للفلتر ─────────────────────────────────────
        $events = \App\Models\Event::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->orderByDesc('start_datetime')
            ->get(['id', 'name', 'start_datetime', 'date']);

        // ── 2. قراءة الفلاتر من الـ Request ────────────────────────────────────
        $selectedEventId = $request->input('event_id');
        $dateFrom        = $request->input('date_from');
        $dateTo          = $request->input('date_to');

        // ── 3. بناء دالة مشتركة لتطبيق الفلاتر على أي Query ──────────────────
        $applyFilters = function ($query, string $table = 'event_invitations') use (
            $companyId, $selectedEventId, $dateFrom, $dateTo
        ) {
            if ($companyId) {
                $query->where("{$table}.company_id", $companyId);
            }
            if ($selectedEventId) {
                $query->where("{$table}.event_id", $selectedEventId);
            }
            if ($dateFrom) {
                $query->whereDate("{$table}.created_at", '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate("{$table}.created_at", '<=', $dateTo);
            }
            return $query;
        };

        // ── 4. إحصائيات الدعوات ─────────────────────────────────────────────────
        $invitationQuery = EventInvitation::query();
        $applyFilters($invitationQuery);

        $invitationStats = $invitationQuery->selectRaw('
            count(*)                                                        as total,
            sum(case when status = "pending"  then 1 else 0 end)           as pending,
            sum(case when status = "accepted" then 1 else 0 end)           as accepted,
            sum(case when status = "declined" then 1 else 0 end)           as declined,
            sum(case when status = "maybe"    then 1 else 0 end)           as maybe,
            coalesce(sum(allowed_guests), 0)                               as total_seats_allocated,
            coalesce(sum(selected_guests), 0)                              as total_guests_confirmed
        ')->first();

        // ── 5. إحصائيات التذاكر والحضور ─────────────────────────────────────────
        // دالة مساعدة تُعيد query مُصفَّى بدون أي select مسبق
        $qrBase = function () use ($companyId, $selectedEventId, $dateFrom, $dateTo) {
            $q = InvitationQr::query()
                ->join('event_invitations', 'invitation_qrs.event_invitation_id', '=', 'event_invitations.id');

            if ($companyId)       { $q->where('event_invitations.company_id', $companyId); }
            if ($selectedEventId) { $q->where('event_invitations.event_id', $selectedEventId); }
            if ($dateFrom)        { $q->whereDate('event_invitations.created_at', '>=', $dateFrom); }
            if ($dateTo)          { $q->whereDate('event_invitations.created_at', '<=', $dateTo); }

            return $q;
        };

        // selectRaw فقط — بدون select('invitation_qrs.*') لتفادي only_full_group_by
        $ticketStats = $qrBase()->selectRaw('
            count(*)                                                                              as total_issued,
            sum(case when invitation_qrs.is_used = 1 then 1 else 0 end)                         as total_checked_in,
            sum(case when invitation_qrs.type = "main" then 1 else 0 end)                       as main_issued,
            sum(case when invitation_qrs.type = "main"  and invitation_qrs.is_used = 1 then 1 else 0 end) as main_checked_in,
            sum(case when invitation_qrs.type = "guest" then 1 else 0 end)                      as guest_issued,
            sum(case when invitation_qrs.type = "guest" and invitation_qrs.is_used = 1 then 1 else 0 end) as guest_checked_in
        ')->first();

        // ── 6. منحنى الوصول (Arrival Timeline) ───────────────────────────────────
        $arrivalTimeline = $qrBase()
            ->where('invitation_qrs.is_used', true)
            ->selectRaw('HOUR(invitation_qrs.used_at) as hour, count(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // ── 7. توزيع الدعوات بالتاريخ (Daily Trend) ──────────────────────────────
        $dailyInvitations = EventInvitation::query();
        $applyFilters($dailyInvitations);
        $dailyInvitations = $dailyInvitations
            ->selectRaw('DATE(created_at) as day, count(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // ── 8. الحسابات المشتقة ───────────────────────────────────────────────────
        $attendanceRate = $ticketStats->total_issued > 0
            ? round(($ticketStats->total_checked_in / $ticketStats->total_issued) * 100, 1)
            : 0;

        $responseRate = $invitationStats->total > 0
            ? round((($invitationStats->accepted + $invitationStats->declined) / $invitationStats->total) * 100, 1)
            : 0;

        $acceptanceRate = $invitationStats->total > 0
            ? round(($invitationStats->accepted / $invitationStats->total) * 100, 1)
            : 0;

        // ── 9. الفعالية المختارة حاليًا ───────────────────────────────────────────
        $selectedEvent = $selectedEventId
            ? $events->firstWhere('id', $selectedEventId)
            : null;

        return view('subscriber.panel.statistics', compact(
            'events',
            'selectedEvent',
            'selectedEventId',
            'dateFrom',
            'dateTo',
            'invitationStats',
            'ticketStats',
            'arrivalTimeline',
            'dailyInvitations',
            'attendanceRate',
            'responseRate',
            'acceptanceRate'
        ));
    }

    public function export(Request $request)
    {
        // Scope export to the authenticated user's company (multi-tenant isolation)
        $companyId = $request->user()?->company_id;

        try {
            $filename = 'Invitations_' . now()->format('Y-m-d') . '.xlsx';
            return Excel::download(new TicketExport($companyId), $filename);
        } catch (\Exception $e) {
            \Log::error('Invitations export failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء التصدير، يرجى المحاولة مرة أخرى.');
        }
    }


}
