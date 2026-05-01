<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login'); // تأكد من وجود هذا الملف
    }

    public function showAdminLoginForm()
    {
        return view('auth.login', [
            'adminMode' => true,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = $request->user();
            $user->update([
                'last_login_at' => Carbon::now(),
            ]);

            if ($user->isSystemAdmin()) {
                return redirect()->intended('/admin');
            }

            $company = $user->company;
            $subscription = $company?->activeSubscription();

            if (
                $subscription
                && $subscription->isTrial()
                && (!$subscription->trial_ends_at || !$subscription->trial_ends_at->isPast())
            ) {
                return redirect()->route('onboarding.plans');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
