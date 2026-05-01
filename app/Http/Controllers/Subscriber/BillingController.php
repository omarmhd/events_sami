<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\BillingContactRequest;
use App\Models\SubscriptionNeedsAssessment;
use App\Models\SubscriptionPlan;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    public function upgrade(Request $request, SubscriptionService $subscriptionService)
    {
        $company = $request->user()->company;
        if (!$company) {
            return redirect()->route('system.dashboard');
        }
        $subscription = $subscriptionService->activeSubscriptionFor($company);

        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->where('code', '!=', 'trial')
            ->orderBy('sort_order')
            ->get();

        $recommendedPlanCode = $request->session()->get('recommended_plan_code');

        return view('subscriber.subscriptions.billing-upgrade', [
            'company' => $company,
            'subscription' => $subscription,
            'plans' => $plans,
            'trialDaysLeft' => $subscriptionService->trialEndsInDays($company),
            'recommendedPlanCode' => $recommendedPlanCode,
        ]);
    }

    public function assessNeeds(Request $request, SubscriptionService $subscriptionService)
    {
        $company = $request->user()->company;
        if (!$company) {
            return redirect()->route('system.dashboard');
        }

        $data = $request->validate([
            'annual_events' => ['required', 'integer', 'min:1', 'max:5000'],
            'average_attendance' => ['required', 'integer', 'min:1', 'max:100000'],
            'needs_customization' => ['required', Rule::in(['yes', 'no'])],
        ]);

        $recommendedPlanCode = $subscriptionService->recommendPlanCode(
            (int) $data['annual_events'],
            (int) $data['average_attendance'],
            $data['needs_customization'] === 'yes'
        );

        SubscriptionNeedsAssessment::create([
            'company_id' => $company->id,
            'annual_events' => (int) $data['annual_events'],
            'average_attendance' => (int) $data['average_attendance'],
            'needs_customization' => $data['needs_customization'] === 'yes',
            'recommended_plan_code' => $recommendedPlanCode,
            'answered_at' => now(),
        ]);

        $request->session()->put('recommended_plan_code', $recommendedPlanCode);

        if ($recommendedPlanCode === 'enterprise') {
            return redirect()->route('billing.upgrade')
                ->with('success', 'Based on your needs, Enterprise is the best fit. Please contact sales.');
        }

        return redirect()->route('billing.upgrade')
            ->with('success', 'Plan recommendation generated successfully.');
    }

    public function switchPlan(Request $request, SubscriptionService $subscriptionService, BillingService $billingService)
    {
        $company = $request->user()->company;
        if (!$company) {
            return redirect()->route('system.dashboard');
        }

        $data = $request->validate([
            'plan_code' => ['required', 'string', Rule::exists('subscription_plans', 'code')],
            'is_prorated_upgrade' => ['nullable', 'boolean'],
        ]);

        $plan = SubscriptionPlan::where('code', $data['plan_code'])->firstOrFail();

        if ($plan->code === 'enterprise') {
            return redirect()->route('billing.upgrade')
                ->with('success', 'Enterprise plan selected. Our sales team will contact you shortly.');
        }

        $subscription = $subscriptionService->switchCompanyPlan($company, $plan, 'active');

        if ((float) $plan->annual_price > 0) {
            $billingService->createAnnualInvoice($company, $subscription, (float) $plan->annual_price);
        }

        $proratedMessage = $request->boolean('is_prorated_upgrade')
            ? 'Prorated upgrade applied successfully.'
            : 'Subscription upgraded successfully.';

        return redirect()->route('dashboard.index')->with('success', $proratedMessage);
    }

    /**
     * One-click renewal / reactivation request.
     * No form input required — we auto-fill from the authenticated user + company.
     * This handles: subscription_ended, trial_expired, event_limit, suspended.
     */
    public function requestRenewal(Request $request)
    {
        $user    = $request->user();
        $company = $user?->company;

        if (!$user || !$company) {
            return redirect()->route('login');
        }

        $reason = $request->input('reason', 'renewal');

        // Map reason to a descriptive plan_code / message
        $planCode = $company->current_plan_code ?? 'renewal';
        $messageMap = [
            'trial_expired'      => 'انتهت الفترة التجريبية — المشترك يطلب الترقية لخطة مدفوعة.',
            'subscription_ended' => 'انتهت مدة الاشتراك — المشترك يطلب التجديد.',
            'suspended'          => 'الحساب موقوف — المشترك يطلب رفع التعليق وإعادة التفعيل.',
            'event_limit'        => 'تجاوز حد الفعاليات — المشترك يطلب توسعة الاشتراك.',
            'no_subscription'    => 'لا يوجد اشتراك — المشترك يطلب تفعيل خطة.',
        ];

        // Avoid duplicate pending requests for the same company/reason
        $exists = BillingContactRequest::where('company_id', $company->id)
            ->where('status', 'pending')
            ->where('plan_code', $planCode)
            ->exists();

        if (!$exists) {
            BillingContactRequest::create([
                'company_id'    => $company->id,
                'user_id'       => $user->id,
                'plan_code'     => $planCode,
                'contact_name'  => $user->name,
                'contact_email' => $user->email ?? $company->contact_email,
                'contact_phone' => $company->phone ?? null,
                'message'       => ($messageMap[$reason] ?? 'طلب تجديد اشتراك.') . ' [' . $company->name . ']',
                'status'        => 'pending',
            ]);
        }

        // Route to the richer expired-page for hard expiry/termination reasons,
        // and to the paywall for suspended/limit reasons.
        $redirectRoute = in_array($reason, ['subscription_ended', 'terminated', 'trial_expired', 'no_subscription'])
            ? 'subscription.expired'
            : 'subscription.show-paywall';

        return redirect()
            ->route($redirectRoute, ['reason' => $reason])
            ->with('renewal_sent', true)
            ->with('renewal_email', $user->email ?? $company->contact_email);
    }

    /**
     * Save a contact/plan-request from the billing upgrade page.
     * No payment is taken — the team follows up manually.
     */
    public function submitContactRequest(Request $request)
    {
        $company = $request->user()->company;
        if (!$company) {
            return redirect()->route('system.dashboard');
        }

        $data = $request->validate([
            'plan_code'     => ['required', 'string', Rule::exists('subscription_plans', 'code')],
            'contact_name'  => ['required', 'string', 'max:120'],
            'contact_email' => ['required', 'email', 'max:180'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'message'       => ['nullable', 'string', 'max:1000'],
        ]);

        BillingContactRequest::create([
            'company_id'    => $company->id,
            'user_id'       => $request->user()->id,
            'plan_code'     => $data['plan_code'],
            'contact_name'  => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'] ?? null,
            'message'       => $data['message'] ?? null,
            'status'        => 'pending',
        ]);

        return redirect()->route('billing.upgrade')
            ->with('success', 'تم إرسال طلبك بنجاح! سيتواصل معك فريقنا لإتمام عملية الدفع.');
    }
}



