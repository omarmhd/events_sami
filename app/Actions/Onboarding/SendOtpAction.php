<?php

namespace App\Actions\Onboarding;

use App\Services\OtpService;

class SendOtpAction
{
    public function __construct(private OtpService $otpService)
    {
    }

    public function execute(string $email, string $ip): void
    {
        $this->otpService->issue($email, $ip);
    }
}

