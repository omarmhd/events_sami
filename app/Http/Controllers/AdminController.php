<?php

namespace App\Http\Controllers;

use App\Exports\TicketExport;
use App\Models\Employee;
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
        $checked_public = Ticket::where("checked_in_at", "<>", null)->count();//عدد الحضور
        $checked_emp= Ticket::where("checked_in_at", "<>", null)->where("is_children", "no")->count();//عدد الحضور الموظفين
        $checked_ch= Ticket::where("checked_in_at", "<>", null)->where("is_children", "yes")->count();//عدد الحضور اطفال
        $tickets_all= Ticket::count();//عدد تذاكر الجميع
        $tickets_emp= Ticket::where("is_children", "no")->count();//عدد تذاكر الجميع   طفال
        $tickets_ch= Ticket::where("is_children", "yes")->count();//عدد تذاكر الجميع   طفال

        return view("admin.statistics",compact(["checked_public","checked_emp","checked_ch","tickets_all","tickets_emp","tickets_ch"]));
    }

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
