<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\InvitationQr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceManagement extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user() ? $request->user()->company_id : null;

        // Load events for the filter dropdown
        $eventsQuery = Event::query();
        if ($companyId) {
            $eventsQuery->where('company_id', $companyId);
        }
        $events = $eventsQuery->orderByDesc('created_at')->get();

        $query = EventInvitation::with(['InvitationQrs', 'event'])
            ->whereHas('InvitationQrs');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('searchInput')) {
            $search = $request->searchInput;

            $query->where(function ($q) use ($search) {
                $q->where('invitee_name', 'like', "%$search%")
                    ->orWhere('invitee_email', 'like', "%$search%")
                    ->orWhere('invitee_phone', 'like', "%$search%")
                    ->orWhere('invitee_position', 'like', "%$search%")
                    ->orWhere('invitee_nationality', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            });
        }

        $statsQuery = InvitationQr::query();
        if ($companyId) {
            $statsQuery->whereHas('invitation', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }
        if ($request->filled('event_id')) {
            $statsQuery->whereHas('invitation', function ($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        $stats = $statsQuery->selectRaw("
            COUNT(*) as total,
            SUM(is_used = 1) as checked,
            SUM(is_used = 0) as not_checked
        ")->first();

        $rows = $query->latest()->paginate(15);





        return view('subscriber.panel.attendance_management.index', compact('rows', 'stats', 'events'));
    }
    public function checked_in(Request $request){



        $invitationQr = InvitationQr::find($request->id);

        if (!$invitationQr) {
            return redirect()->back()->with("error", "Ticket not found");
        }

        $companyId = $request->user() ? $request->user()->company_id : null;
        if ($companyId && $invitationQr->invitation && (int) $invitationQr->invitation->company_id !== (int) $companyId) {
            return redirect()->back()->with("error", "Unauthorized check-in");
        }


        // تم استخدامه مسبقًا
        if ($invitationQr->is_used) {
            return redirect()->back()->with("error","This guest has already checked in");

        }

        // تسجيل الحضور
        $invitationQr->update([
            'is_used' => true,
            'used_at' => Carbon::now()
        ]);


        return redirect()->back()->with("success","Check-in successful");


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


}
