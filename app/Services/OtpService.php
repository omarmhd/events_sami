<?php

namespace App\Services;

use App\Mail\OtpCodeMail;
use App\Models\OtpVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function issue($email, $ipAddress)
    {
        $email = strtolower(trim((string) $email));

        OtpVerification::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('consumed_at')
            ->delete();

        $code = (string) random_int(100000, 999999);

        $otp = OtpVerification::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_ip' => $ipAddress,
        ]);

        Mail::to($email)->send(new OtpCodeMail($code));

        return $otp;
    }

    public function verify($email, $code)
    {
        $email = strtolower(trim((string) $email));

        $otp = OtpVerification::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$otp) {
            return [
                'ok' => false,
                'message' => 'No active OTP found. Please request a new code.',
            ];
        }

        if ($otp->isExpired()) {
            return [
                'ok' => false,
                'message' => 'OTP expired. Please request a new code.',
            ];
        }

        if ($otp->attempts >= 5) {
            return [
                'ok' => false,
                'message' => 'Too many attempts. Request a new OTP.',
            ];
        }

        $otp->increment('attempts');

        if (!Hash::check($code, $otp->code_hash)) {
            return [
                'ok' => false,
                'message' => 'Invalid OTP code.',
            ];
        }

        $otp->update(['consumed_at' => Carbon::now()]);

        return [
            'ok' => true,
            'otp' => $otp,
        ];
    }
}
