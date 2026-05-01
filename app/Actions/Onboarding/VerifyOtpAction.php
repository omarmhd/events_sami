<?php

namespace App\Actions\Onboarding;

use App\Models\User;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VerifyOtpAction
{
    public function __construct(private OtpService $otpService)
    {
    }

    public function execute(string $email, string $otpCode): array
    {
        $normalizedEmail = strtolower(trim($email));

        $result = $this->otpService->verify($normalizedEmail, $otpCode);

        if (!($result['ok'] ?? false)) {
            return $result;
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->first();

        if (!$user) {
            $user = User::create([
                'email' => $normalizedEmail,
                'name' => strstr($normalizedEmail, '@', true),
                'password' => Hash::make(bin2hex(random_bytes(16))),
                'role' => 'organizer_owner',
                'is_system_admin' => false,
            ]);
        }

        Auth::login($user);
        $user->update(['last_login_at' => Carbon::now()]);

        return [
            'ok' => true,
            'user' => $user,
        ];
    }
}

