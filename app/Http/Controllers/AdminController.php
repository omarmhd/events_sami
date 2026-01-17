<?php

namespace App\Http\Controllers;

use App\Exports\TicketExport;
use App\Models\Employee;
use App\Models\EventInvitation;
use App\Models\InvitationQr;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public  function index(){
        return view("admin.index");
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

        return view("admin.emps", compact("emps"));
    }


    public function checked_in(Request $request,$id=null){
        if (!$request->qrData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR data'
            ]);
        }

        $invitationQr = InvitationQr::where('token',$request->qrData)->first();


        if (!$invitationQr) {
            return response()->json([
                'success' => false,
                'message' => 'QR code is not valid'
            ]);
        }

        // تم استخدامه مسبقًا
        if ($invitationQr->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'This guest has already checked in'
            ]);
        }

        // تسجيل الحضور
        $invitationQr->update([
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
            return view("admin.register_attendance",compact("tickets"));
        }
        return view("admin.register_attendance");

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
        return view("admin.attendance_list",compact("tickets"));
    }
    public function statistics(){
        // 1. إحصائيات الدعوات (Status Breakdown)
        $invitationStats = EventInvitation::selectRaw('
            count(*) as total,
            sum(case when status = "pending" then 1 else 0 end) as pending,
            sum(case when status = "accepted" then 1 else 0 end) as accepted,
            sum(case when status = "declined" then 1 else 0 end) as declined,
            sum(case when status = "maybe" then 1 else 0 end) as maybe,
            sum(allowed_guests) as total_seats_allocated,
            sum(selected_guests) as total_guests_confirmed
        ')->first();

        // 2. إحصائيات التذاكر والحضور الفعلي (Tickets & Attendance)
        $ticketStats = InvitationQr::selectRaw('
            count(*) as total_issued,
            sum(case when is_used = 1 then 1 else 0 end) as total_checked_in,
$invitationStats->accepted + $invitationStats->total_guests_confirmed
            sum(case when type = "main" then 1 else 0 end) as main_issued,
            sum(case when type = "main" and is_used = 1 then 1 else 0 end) as main_checked_in,

            sum(case when type = "guest" then 1 else 0 end) as guest_issued,
            sum(case when type = "guest" and is_used = 1 then 1 else 0 end) as guest_checked_in
        ')->first();

        // 3. تحليل أوقات الوصول (Peak Hours) - آخر 24 ساعة أو حسب الحدث
        $arrivalTimeline = InvitationQr::where('is_used', true)
            ->selectRaw('HOUR(used_at) as hour, count(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // حساب النسب المئوية لتسهيل العرض
        $attendanceRate = $ticketStats->total_issued > 0
            ? round(($ticketStats->total_checked_in / $ticketStats->total_issued) * 100, 1)
            : 0;

        $responseRate = $invitationStats->total > 0
            ? round((($invitationStats->accepted + $invitationStats->declined) / $invitationStats->total) * 100, 1)
            : 0;

        return view('admin.statistics', compact(
            'invitationStats',
            'ticketStats',
            'arrivalTimeline',
            'attendanceRate',
            'responseRate'
        ));    }

    public function export()
    {
        $data = $this->someData;

        try {
            return Excel::download(new TicketExport(), 'tickets.xlsx');
        } catch (\Exception $e) {
            // تسجيل الخطأ
            \Log::error($e);
            // إعادة توجيه المستخدم أو إظهار رسالة خطأ
            return redirect()->back()->with('error', 'حدث خطأ أثناء التصدير');
        }
    }
    private $someData;

}
