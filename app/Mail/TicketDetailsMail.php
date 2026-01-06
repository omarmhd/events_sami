<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TicketDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;



    public $tickets; // ملاحظة: تم تغيير المتغير ليكون مجموعة من التذاكر
    public $employee;
    public $event;

    public function __construct($tickets, $employee,$event)
    {
        $this->tickets = $tickets;
        $this->employee = $employee;
        $this->event=$event;
    }

    public function build()
    {
        $mail = $this->view('email')
        ->subject('Ticket Details')   // الموضوع
        ->with([
            'tickets' => $this->tickets,
            'employee' => $this->employee,
            'event' => $this->event,
        ]);

        foreach ($this->tickets as $index => $ticket) {
            $barcodePath = Storage::disk('public')->path("qr_codess/".$ticket->barcode);

            if (file_exists($barcodePath)) {

                $mail->attach($barcodePath, [
                    'mime' => 'image/png',
                    'as' => 'ticket_qr_' . ($index + 1) . '.png',
                ]);
            }

            // تحديد مسار الصورة باستخدام التخزين
//            $barcodePath = Storage::disk('public')->path("qr_codess/".$ticket->barcode);
//
//            // التأكد من وجود الصورة في التخزين
//            if (file_exists($barcodePath)) {
//                // إرفاق الصورة من التخزين
//                $mail->attachFromStorage($barcodePath, 'ticket_qr_' . ($index + 1), [
//                    'mime' => 'image/png',  // تحديد نوع الصورة إذا كانت PNG
//                    'as' => 'ticket_qr_' . ($index + 1) . '.png',  // اسم الصورة عند الإرفاق
//                ]);
//            }
        }

        return $mail;
    }

    /*
        public function build()
        {
            $mail = $this->view('email')  // عرض البريد الإلكتروني
            ->subject('Ticket Details')  // الموضوع
            ->with([
                'tickets' => $this->tickets,
                'employee' => $this->employee,
            ]);


            // هنا نقوم بإرفاق الصور لجميع التذاكر

            foreach ($this->tickets as $index => $ticket) {

    //            $mail->attach(public_path($ticket->barcode), [
    //                'as' => $ticket->barcode . '.jpg',  // الاسم المرفق
    //                'mime' => 'image/jpeg',
    //                'contentId' => 'ticket_qr_' . ($index + 1) // Content-ID الذي سنستخدمه في الـ CID
    //
    //            ]);
            }

            return $mail;


        }*/
}
