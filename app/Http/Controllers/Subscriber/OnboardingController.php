<?php

namespace App\Http\Controllers\Subscriber;

use App\Actions\Onboarding\CompleteOnboardingAction;
use App\Actions\Onboarding\SendOtpAction;
use App\Actions\Onboarding\VerifyOtpAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\CompleteOnboardingRequest;
use App\Http\Requests\Onboarding\SendOtpRequest;
use App\Http\Requests\Onboarding\VerifyOtpRequest;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function showOtpForm()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $company = $user->company;

            if (!$user->organization_id && !$user->company_id) {
                return redirect()->route('register');
            }

            if (!$company || !$company->onboarding_completed_at) {
                return redirect()->route('register');
            }

            $settings = $company->settings ?? [];
            if (empty($settings['plans_step_seen'])) {
                return redirect()->route('onboarding.plans');
            }

            return redirect()->route('dashboard.index');
        }

        return view('subscriber.onboarding.otp-login');
    }

    public function sendOtp(SendOtpRequest $request, SendOtpAction $sendOtpAction)
    {
        $data  = $request->validated();
        $email = strtolower(trim($data['email']));

        // Detect whether this email belongs to an existing user and what state they're in.
        // We pass a context hint to the verify page so the messaging is accurate.
        $existingUser = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        $userContext = 'new'; // default: new registration
        if ($existingUser) {
            $company = $existingUser->company;
            if ($company && $company->onboarding_completed_at) {
                $userContext = 'returning'; // full account → login
            } else {
                $userContext = 'incomplete'; // started OTP before, never finished org profile
            }
        }

        $sendOtpAction->execute($email, $request->ip());

        $request->session()->put('onboarding_email', $email);
        $request->session()->put('onboarding_user_context', $userContext);

        return redirect()->route('onboarding.verify.form');
    }

    public function showVerifyForm(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();

            if (!$user->organization_id && !$user->company_id) {
                return redirect()->route('register');
            }

            if (!$user->company || !$user->company->onboarding_completed_at) {
                return redirect()->route('register');
            }
        }

        $email = strtolower(trim((string) $request->session()->get('onboarding_email')));

        if (!$email) {
            return redirect()->route('onboarding.otp.form');
        }

        $userContext = $request->session()->get('onboarding_user_context', 'new');

        return view('subscriber.onboarding.verify-otp', [
            'email'       => $email,
            'userContext' => $userContext,
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request, VerifyOtpAction $verifyOtpAction)
    {
        $data = $request->validated();
        $sessionEmail = strtolower(trim((string) $request->session()->get('onboarding_email', '')));
        $email = $sessionEmail ?: strtolower(trim($data['email']));

        if (!$email) {
            return redirect()->route('onboarding.otp.form');
        }

        $result = $verifyOtpAction->execute($email, $data['otp']);

        if (!$result['ok']) {
            return back()->withErrors([
                'otp' => $result['message'],
            ])->withInput(['email' => $email]);
        }

        $user    = $result['user'];
        $company = $user->company;

        // ── Case 1: No company linked yet → new user, go complete profile ──
        if (!$user->organization_id && !$user->company_id) {
            return redirect()->route('register')
                ->with('warning', 'يرجى استكمال تسجيل بيانات المنظمة أولاً.');
        }

        // ── Case 2: Company exists but onboarding not finished ──
        if (!$company || !$company->onboarding_completed_at) {
            return redirect()->route('register')
                ->with('warning', 'يرجى استكمال بيانات المنظمة للمتابعة.');
        }

        // ── Case 3: Onboarding complete but plans step never seen → plans page ──
        // We persist this flag in company.settings so it survives across sessions.
        $settings = $company->settings ?? [];
        if (empty($settings['plans_step_seen'])) {
            return redirect()->route('onboarding.plans');
        }

        if ($company->status === 'suspended') {
            return redirect()->route('subscription.expired', ['reason' => 'suspended'])
                ->with('warning', 'حسابك موقوف مؤقتاً. تواصل مع فريق الدعم لإعادة التفعيل.');
        }

        $subscription = $company->activeSubscription();

        if (!$subscription) {
            return redirect()->route('subscription.expired', ['reason' => 'no_subscription'])
                ->with('warning', 'لا يوجد اشتراك نشط. يرجى تجديد اشتراكك للمتابعة.');
        }

        if ($subscription->ends_at && $subscription->ends_at->isPast()) {
            return redirect()->route('subscription.expired', ['reason' => 'subscription_ended'])
                ->with('warning', 'انتهت مدة اشتراكك. يرجى التجديد للمتابعة.');
        }

        if ($subscription->isTrial() && $subscription->trial_ends_at && $subscription->trial_ends_at->isPast()) {
            return redirect()->route('subscription.expired', ['reason' => 'trial_expired'])
                ->with('warning', 'انتهت الفترة التجريبية. اختر خطة للاستمرار.');
        }

        if ($subscription->isTrial()) {
            return redirect()->route('onboarding.plans');
        }

        // ── Case 4: Returning user — subscription middleware will intercept
        //    if expired/suspended and redirect to subscription.expired.
        //    Otherwise go straight to dashboard.
        return redirect()->route('dashboard.index');
    }

    public function showProfileForm(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('onboarding.otp.form');
        }

        return view('subscriber.onboarding.profile', [
            'user' => $user,
            'company' => $user->company,
            'plans' => SubscriptionPlan::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'trialDays' => config('subscription.trial.days', SubscriptionService::TRIAL_DAYS),
            'trialInviteLimit' => config('subscription.trial.invites_limit', SubscriptionService::TRIAL_MAX_INVITEES_PER_EVENT),
        ]);
    }

    public function saveProfile(CompleteOnboardingRequest $request, CompleteOnboardingAction $completeOnboardingAction)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('onboarding.otp.form');
        }

        $completeOnboardingAction->execute($user, $request->validated());

        // After profile setup, take the user to the plans selection step
        // so they can review their trial benefits and optionally request an upgrade.
        return redirect()->route('onboarding.plans')
            ->with('success', 'تم إنشاء مساحتك بنجاح! اختر خطة الاشتراك المناسبة.');
    }

    /**
     * Show the plan-selection step in the onboarding flow.
     *
     * The subscriber is already authenticated and on a trial subscription.
     * They can:
     *  - Review their trial details (days remaining, limits).
     *  - Choose a paid plan → submits a contact request for the admin team.
     *  - Click "Next / Skip" → goes straight to the dashboard on the trial plan.
     */
    public function showPlansStep(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('onboarding.otp.form');
        }

        $company = $user->company;

        if (!$company || !$company->onboarding_completed_at) {
            return redirect()->route('register');
        }

        // Persist the flag in company.settings so subsequent logins go straight
        // to the dashboard rather than back to the plans step.
        $settings = $company->settings ?? [];
        if (empty($settings['plans_step_seen'])) {
            $settings['plans_step_seen'] = true;
            $company->update(['settings' => $settings]);
        }

        // Paid plans only — exclude trial (already shown in the banner).
        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->where('code', '!=', 'trial')
            ->orderBy('sort_order')
            ->get();

        $trialDays        = (int) config('subscription.trial.days', SubscriptionService::TRIAL_DAYS);
        $trialInviteLimit = (int) config('subscription.trial.invites_limit', SubscriptionService::TRIAL_MAX_INVITEES_PER_EVENT);

        // When did the trial actually start?
        $trialStartedAt = $company->trial_started_at ?? now();
        $trialEndsAt    = $company->trial_ends_at ?? $trialStartedAt->copy()->addDays($trialDays);

        return view('subscriber.onboarding.plans', [
            'user'             => $user,
            'company'          => $company,
            'plans'            => $plans,
            'trialDays'        => $trialDays,
            'trialInviteLimit' => $trialInviteLimit,
            'trialEndsAt'      => $trialEndsAt,
        ]);
    }
}



