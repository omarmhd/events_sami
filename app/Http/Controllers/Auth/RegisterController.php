<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Onboarding\CompleteOnboardingAction;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * عرض نموذج إنشاء حساب جديد مع الخطط المتاحة.
     */
    public function showRegistrationForm()
    {
        return view('auth.register', [
            'plans' => SubscriptionPlan::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'trialDays'         => config('subscription.trial.days', SubscriptionService::TRIAL_DAYS),
            'trialInviteLimit'  => config('subscription.trial.invites_limit', SubscriptionService::TRIAL_MAX_INVITEES_PER_EVENT),
        ]);
    }

    /**
     * معالجة التسجيل: إنشاء المستخدم + مساحة العمل في خطوة واحدة.
     */
    public function register(Request $request, CompleteOnboardingAction $completeOnboarding)
    {
        $request->validate([
            // بيانات الحساب الشخصي
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'    => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],

            // بيانات مساحة العمل
            'company_name'            => ['required', 'string', 'max:160'],
            'subdomain'               => ['nullable', 'alpha_dash', 'min:3', 'max:40', 'unique:companies,subdomain'],
            'annual_events_estimate'  => ['nullable', 'integer', 'min:1', 'max:10000'],
            'timezone'                => ['nullable', 'string', 'max:64'],
            'preferred_plan_code'     => ['nullable', 'exists:subscription_plans,code'],
        ], [
            // رسائل الخطأ بالعربية
            'name.required'                    => 'الاسم الكامل مطلوب.',
            'email.required'                   => 'البريد الإلكتروني مطلوب.',
            'email.email'                      => 'يرجى إدخال بريد إلكتروني صحيح.',
            'email.unique'                     => 'هذا البريد الإلكتروني مسجّل بالفعل.',
            'phone.required'                   => 'رقم الجوال مطلوب.',
            'password.required'                => 'كلمة المرور مطلوبة.',
            'password.min'                     => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.',
            'password.confirmed'               => 'كلمتا المرور غير متطابقتين.',
            'terms.accepted'                   => 'يجب الموافقة على شروط الاستخدام للمتابعة.',
            'company_name.required'            => 'اسم الجهة أو الشركة مطلوب.',
            'subdomain.alpha_dash'             => 'يجب أن يحتوي النطاق الفرعي على أحرف إنجليزية وأرقام وشرطات فقط.',
            'subdomain.min'                    => 'يجب أن يتكون النطاق الفرعي من 3 أحرف على الأقل.',
            'subdomain.unique'                 => 'هذا النطاق الفرعي محجوز بالفعل، جرب اسمًا آخر.',
            'annual_events_estimate.required'  => 'عدد الفعاليات المتوقعة مطلوب.',
            'annual_events_estimate.integer'   => 'يجب أن يكون عدد الفعاليات رقمًا صحيحًا.',
        ]);

        // 1. إنشاء المستخدم
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 2. إكمال إعداد مساحة العمل مباشرةً (نفس Action المستخدم في مسار OTP)
        $completeOnboarding->execute($user, [
            'name'                   => $request->name,
            'phone'                  => $request->phone,
            'company_name'           => $request->company_name,
            'subdomain'              => $request->subdomain,
            'annual_events_estimate' => $request->input('annual_events_estimate', 5),
            'timezone'               => $request->timezone ?: 'Asia/Riyadh',
            'preferred_plan_code'    => $request->preferred_plan_code,
        ]);

        return redirect()->route('onboarding.plans')
            ->with('success', 'مرحباً بك! تم إنشاء مساحة العمل بنجاح. راجع خطط الاشتراك للمتابعة.');
    }
}
