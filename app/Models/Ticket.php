<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Ticket extends Model
{
    use HasFactory;
    protected $guarded=[""];
/*
    public function generateQrCode()
    {
        // إنشاء QR Code
        $qrCode = new QrCode($this->id); // يمكن استخدام أي قيمة لتوليد الـ QR Code
        $writer = new PngWriter();

        // تحديد مسار حفظ الصورة داخل مجلد public

        // تحديد مسار الحفظ داخل مجلد public
        $folderPath = storage_path("app/public/qr_codess"); // المسار داخل مجلد الـ Storage

// تحقق إذا كان المجلد موجودًا، وإذا لم يكن موجودًا، قم بإنشائه
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true); // إنشاء المجلد إذا لم يكن موجودًا
        }
        $filePath = storage_path("qr-codess/ticket_{$this->id}.png");

        // حفظ الصورة
        $result = $writer->write($qrCode, null, null);

        // استخراج الصورة المُولدة
        $imageResource = $result->getImage();

        // حفظ الصورة إلى المسار المحدد
//        imagepng($imageResource, $filePath);
        Storage::disk('public')->put($filePath, $imageResource);  // $imageContents هو محتوى الصورة

        // تحرير الذاكرة بعد الحفظ
        imagedestroy($imageResource);


// ثم حفظ الصورة
        // حفظ الـ QR Code كصورة PNG


        return "ticket_{$this->id}.png"; // إرجاع مسار الصورة
    }*/
/*
    public function generateQrCode()
    {
        // إنشاء QR Code
        $qrCode = new QrCode($this->id); // يمكن استخدام أي قيمة لتوليد الـ QR Code
        $writer = new PngWriter();

        // تحديد مسار الحفظ داخل مجلد public ضمن الـ Storage
        $folderPath = storage_path('app/public/qr_codess'); // المسار داخل مجلد الـ Storage

        // تحقق إذا كان المجلد موجودًا، وإذا لم يكن موجودًا، قم بإنشائه
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true); // إنشاء المجلد إذا لم يكن موجودًا
        }

        // مسار الحفظ داخل الـ Storage
        $filePath = "qr_codess/ticket_{$this->id}.png"; // تخزينه داخل storage/app/public/qr_codess

        // إنشاء الصورة باستخدام QrCode
        $result = $writer->write($qrCode, null, null);

        // استخراج الصورة المُولدة
        $imageResource = $result->getImage();

        // بدء عملية تخزين الصورة في الـ Storage
        ob_start(); // بداية تخزين الصورة في الذاكرة
        imagepng($imageResource); // رسم الصورة باستخدام PHP
        $imageContents = ob_get_contents(); // الحصول على المحتوى المخزن في الذاكرة
        ob_end_clean(); // إنهاء تخزين الصورة في الذاكرة

        // حفظ الصورة إلى الـ Storage
        Storage::disk('public')->put($filePath, $imageContents); // حفظ الصورة داخل الـ Storage

        // تحرير الذاكرة بعد الحفظ
        imagedestroy($imageResource);

        // إرجاع اسم الملف الذي تم تخزينه
        return "ticket_{$this->id}.png";
    }*/

    public function generateQrCode()
    {
        // إنشاء QR Code
        $qrCode = new QrCode($this->id); // يمكن استخدام أي قيمة لتوليد الـ QR Code
        $writer = new PngWriter();

        // تحديد مسار الحفظ داخل مجلد public ضمن الـ Storage
        $folderPath = storage_path('app/public/qr_codess'); // المسار داخل مجلد الـ Storage

        // تحقق إذا كان المجلد موجودًا، وإذا لم يكن موجودًا، قم بإنشائه
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true); // إنشاء المجلد إذا لم يكن موجودًا
        }

        // مسار الحفظ داخل الـ Storage
        $filePath = "qr_codess/ticket_{$this->id}.png"; // تخزينه داخل storage/app/public/qr_codess

        // إنشاء الصورة باستخدام QrCode
        $result = $writer->write($qrCode, null, null);

        // استخراج الصورة المُولدة
        $imageResource = $result->getImage();

        // بدء عملية تخزين الصورة في الذاكرة
        ob_start(); // بداية تخزين الصورة في الذاكرة
        imagepng($imageResource); // رسم الصورة باستخدام PHP
        $imageContents = ob_get_contents(); // الحصول على المحتوى المخزن في الذاكرة
        ob_end_clean(); // إنهاء تخزين الصورة في الذاكرة

        // حفظ الصورة إلى الـ Storage
        Storage::disk('public')->put($filePath, $imageContents); // حفظ الصورة داخل الـ Storage

        // تحرير الذاكرة بعد الحفظ
        imagedestroy($imageResource);

        // إرجاع اسم الملف الذي تم تخزينه
        return "ticket_{$this->id}.png";
    }

}
