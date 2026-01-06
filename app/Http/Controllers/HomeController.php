<?php

namespace App\Http\Controllers;

use App\Mail\TicketDetailsMail;
use App\Models\Employee;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller{

/*
    public function save(Request $request)
    {
        $count = $request->count_children;


        $employee = Employee::create([
            "name" => $request->name,
            "email" => $request->email,
            "employee_number" => $request->employee_number
        ]);

        $tickets = [];


        $ticket = Ticket::create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_number' => $employee->employee_number,
            "is_children" => "no",
            "event_name" => "event1",
            "description" => "event1",
        ]);


        $qrCodePath = $ticket->generateQrCode();
        $ticket->update(['barcode' => $qrCodePath]);


        $tickets[] = $ticket;


        for ($i = 0; $i < $count; $i++) {
            $ticket = Ticket::create([
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'employee_number' => $employee->employee_number,
                "is_children" => "yes",
                "event_name" => "event1",
                "description" => "event1",
            ]);


            $qrCodePath = $ticket->generateQrCode();
            $ticket->update(['barcode' => $qrCodePath]);


            $tickets[] = $ticket;
        }


        $emailContent = "تم إنشاء التذاكر بنجاح:\n\n";
        foreach ($tickets as $ticket) {
            $emailContent .= "التذكرة الخاصة بالموظف: " . $ticket->employee_name . "\n";
            $emailContent .= "رقم الموظف: " . $ticket->employee_number . "\n";
            $emailContent .= "الحدث: " . $ticket->event_name . "\n";
            $emailContent .= "QR Code: " . $ticket->barcode . "\n\n";
        }


        Mail::send([], [], function ($message) use ($employee, $emailContent, $tickets) {
            $message->to($employee->email)
                ->subject('تم إنشاء التذاكر بنجاح')
                ->setBody($emailContent, 'text/plain');


            foreach ($tickets as $ticket) {
                $qrCodePath = public_path($ticket->barcode);
                if (file_exists($qrCodePath)) {
                    $message->attach($qrCodePath, [
                        'as' => basename($qrCodePath),
                        'mime' => 'image/png',
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Ticket created and email sent successfully!']);
    }
*/


// تأكد من استدعاء الكلاسات الضرورية

    public function save(Request $request)
    {
        // 1. تحديث التحقق (Validation) ليشمل بيانات الزوار
        $val = Validator::make($request->all(), [
            "name" => "required|string",
            "position" => "required|string",
            "nationality" => "required|string",
            "email" => [
                "required",
                "email",
                function ($attribute, $value, $fail) {
                    $exceptions = ['yousef.designer@gmail.com', 'yousef.rsh@gmail.com', "omarmhd19988@gmail.com", "omar@sami-aec.site"];
                    if (in_array($value, $exceptions)) {
                        return;
                    }
                    // التحقق من الدومين (اختياري حسب رغبتك)
                    // if (!preg_match('/^[\w\.-]+@aecl\.com$/i', $value)) {
                    //    $fail("The $attribute must be a valid email address.");
                    // }
                }
            ],
            // التحقق من مصفوفة الزوار
            "guests" => "array|nullable",
            "guests.*.name" => "required_if:has_plus_one,yes|string",
            "guests.*.email" => "required_if:has_plus_one,yes|email",
            "guests.*.position" => "required_if:has_plus_one,yes|string",
            "guests.*.nationality" => "required_if:has_plus_one,yes|string",
        ]);

        if ($val->fails()) {
            return response()->json([
                "message" => $val->errors()->first(),
                "status" => false
            ], 200);
        }

        $existingEmployee = Employee::where("email", $request->email)->first();
        $allowedEmails = ["yousef.designer@gmail.com", "HOWAILH@aecl.com", "omarmhd19988@gmail.com", "omar@sami-aec.site"];

        if ($existingEmployee && !in_array($request->email, $allowedEmails)) {
            return response()->json(["status" => false, "message" => "You cannot register again, you are already registered."]);
        }
        DB::beginTransaction();

        try {
        $event = DB::table("events")->where("name", "SAMI-AEC")->first();

        $employee = Employee::create([
            "name" => $request->name,
            "email" => $request->email,
            "position" => $request->position,
            "nationality" => $request->nationality,
            "employee_number" => $request->employee_number ?? 0,
            "event_name" => $event->name
        ]);


        $mainTicket = Ticket::create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_email' => $employee->email,
            'employee_number' => $employee->employee_number,
            'position' => $request->position,
            'nationality' => $request->nationality,
            "type" => 'employee',
            "event_name" => $event->name,
            "description" => $event->description,
            "date" => $event->date,
            "from_time" => $event->from_time,
            "to_time" => $event->to_time,
            "barcode" => null
        ]);

        // توليد QR
        $qrCodePath = $mainTicket->generateQrCode();
        if ($qrCodePath) {
            $mainTicket->barcode = $qrCodePath;
            $mainTicket->save();
        }

        Mail::to($employee->email)->send(new TicketDetailsMail([$mainTicket], $employee, $event));


            if ($request->has_plus_one === 'yes' && $request->guests && is_array($request->guests)) {

                foreach ($request->guests as $guestData) {

                    // 1. استخدام firstOrCreate لمنع التكرار
                    $guestTicket = Ticket::firstOrCreate(
                        [
                            // الشروط الفريدة (التي نتحقق بناء عليها)
                            'employee_email' => $guestData['email'],
                            'event_name' => $event->name, // يفضل استخدام event_id لو متاح
                            'type' => 'guest',
                        ],
                        [
                            // البيانات التي يتم إدخالها فقط إذا كانت التذكرة جديدة
                            'employee_id' => $employee->id,
                            'employee_name' => $guestData['name'],
                            'position' => $guestData['position'],
                            'nationality' => $guestData['nationality'],
                            'employee_number' => null,
                            "description" => $event->description,
                            "date" => $event->date,
                            "from_time" => $event->from_time,
                            "to_time" => $event->to_time,
                            "barcode" => null
                        ]
                    );

                    // 2. التحقق هل تم إنشاء التذكرة الآن أم أنها كانت موجودة سابقاً؟
                    // خاصية wasRecentlyCreated تعود بـ true إذا تم إنشاء السجل للتو
                    if ($guestTicket->wasRecentlyCreated) {

                        // توليد الباركود فقط للتذاكر الجديدة
                        $guestQrPath = $guestTicket->generateQrCode();
                        if ($guestQrPath) {
                            $guestTicket->barcode = $guestQrPath;
                            $guestTicket->save();
                        }

                        // إرسال الإيميل فقط للتذاكر الجديدة (لتجنب إزعاج الضيف بإيميلات مكررة)
                        try {
                            Mail::to($guestData['email'])->send(new TicketDetailsMail([$guestTicket], $employee, $event));
                        } catch (\Exception $e) {
                            \Log::error("Failed to send email to guest: " . $guestData['email']);
                        }
                    }
                }
            }
//        if ($request->has_plus_one === 'yes' && $request->guests && is_array($request->guests)) {
//
//            foreach ($request->guests as $guestData) {
//                $guestTicket = Ticket::create([
//                    'employee_id' => $employee->id, // نربطها بالموظف الرئيسي كمرجع
//                    'employee_name' => $guestData['name'], // اسم الزائر
//                    'employee_email' => $guestData['email'], // إيميل الزائر
//                    'position' => $guestData['position'],
//                    'nationality' => $guestData['nationality'],
//                    'employee_number' => null, // الزائر ليس لديه رقم وظيفي عادة
//                    "type" => "guest",
//                    "event_name" => $event->name,
//                    "description" => $event->description,
//                    "date" => $event->date,
//                    "from_time" => $event->from_time,
//                    "to_time" => $event->to_time,
//                    "barcode" => null
//                ]);
//
//                $guestQrPath = $guestTicket->generateQrCode();
//                if ($guestQrPath) {
//                    $guestTicket->barcode = $guestQrPath;
//                    $guestTicket->save();
//                }
//
//                try {
//                    Mail::to($guestData['email'])->send(new TicketDetailsMail([$guestTicket], $employee, $event));
//                } catch (\Exception $e) {
//                    // يمكنك تسجيل الخطأ هنا إذا فشل إرسال إيميل لزائر معين ولكن لا توقف العملية
//                    \Log::error("Failed to send email to guest: " . $guestData['email']);
//                }
//            }
//        }

        return response()->json([
            "status" => true,
            'message' => 'Registration successful! Tickets have been sent to all email addresses.'
        ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error("Registration Error: " . $e->getMessage());

            return response()->json([
                "status" => false,
                "message" => "Something went wrong during registration. Please try again. " . $e->getMessage()
            ], 500);
        }
    }
    public function resendTickets(Request $request)
    {
        // 1. التحقق من صحة البيانات
        // في حال الفشل، لارافيل سيعيد المستخدم تلقائياً للصفحة السابقة مع الأخطاء
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        try {
            // 2. جلب البيانات
            $employee = Employee::findOrFail($request->employee_id);
            $tickets = Ticket::where('employee_id', $employee->id)->get();

            // التحقق من وجود Event (تحسين بسيط للتحقق من وجوده)
            $event = DB::table("events")->where("name", $employee->event_name)->first();

            // في حال عدم وجود تذاكر، نرجع رسالة خطأ
            if ($tickets->isEmpty()) {
                return redirect()->back()->with('error', 'No tickets found for this employee.');
            }

            // 3. إرسال الإيميل
            Mail::to($employee->email)->send(new TicketDetailsMail($tickets, $employee, $event));

            // 4. إرجاع رسالة نجاح
            return redirect()->back()->with('success', 'Tickets have been resent successfully to ' . $employee->email);

        } catch (\Exception $e) {
            // 5. في حال حدوث خطأ تقني
            return redirect()->back()->with('error', 'Error while resending: ' . $e->getMessage());
        }
    }

    public function check_email(Request $request){
        $emp=Employee::where("email",$request->email)->first();

        if ($emp and !in_array($request->email,["yousef.designer@gmail.com","HOWAILH@aecl.com","omarmhd19988@gmail.com","omar@sami-aec.site"])){
            return response()->json(["status"=>false,'message' => 'This email has already been registered before']);
        }
 }

}

