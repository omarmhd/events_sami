<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

use App\Models\Company;
use App\Models\SubscriptionInvoice;
use App\Services\SubscriptionManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrganizerSubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionManagementService $subscriptionService,
    ) {}

    /**
     * Show subscription page
     */
    public function show()
    {
        $company = Auth::user()->company;
        $subscription = $company->activeSubscription;

        return view('subscriber.subscriptions.show', [
            'company' => $company,
            'subscription' => $subscription,
        ]);
    }

    /**
     * Show upgrade page
     */
    public function showUpgradePage($plan = null)
    {
        $company = Auth::user()->company;
        $currentSubscription = $company->activeSubscription;
        $remainingTrialDays = $this->subscriptionService->getRemainingTrialDays($currentSubscription);

        $plans = [
            'starter' => [
                'name' => 'الخطة الأساسية',
                'price' => 99,
                'annual_price' => 1188,
                'events_limit' => 12,
                'invites_limit' => 100,
                'features' => [
                    'إنشاء 12 فعالية سنوية',
                    'حتى 100 دعوة لكل فعالية',
                    '5 جيجابايت تخزين',
                    'دعم البريد الإلكتروني',
                ],
            ],
            'professional' => [
                'name' => 'الخطة المتقدمة',
                'price' => 299,
                'annual_price' => 3588,
                'events_limit' => 100,
                'invites_limit' => 1000,
                'features' => [
                    'إنشاء 100 فعالية سنوية',
                    'حتى 1000 دعوة لكل فعالية',
                    '100 جيجابايت تخزين',
                    'استيراد CSV جماعي',
                    'إعادة إرسال جماعي',
                    'مجال مخصص',
                    'دعم الأولوية',
                ],
                'best_value' => true,
            ],
            'enterprise' => [
                'name' => 'خطة المؤسسات',
                'price' => 'custom',
                'annual_price' => 'custom',
                'events_limit' => 'unlimited',
                'invites_limit' => 'unlimited',
                'features' => [
                    'فعاليات غير محدودة',
                    'دعوات غير محدودة',
                    'تخزين غير محدود',
                    'تخصيص برمجي',
                    'تكامل SSO',
                    'API وصول',
                    'حساب مخصص',
                ],
                'cta' => 'اتصل بنا',
            ],
        ];

        return view('subscriber.subscriptions.upgrade', [
            'company' => $company,
            'subscription' => $currentSubscription,
            'remainingTrialDays' => $remainingTrialDays,
            'plans' => $plans,
            'selectedPlan' => $plan,
        ]);
    }

    /**
     * Process plan upgrade
     */
    public function upgradeToProPlan(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:starter,professional,enterprise',
            'payment_method' => 'nullable|string',
        ]);

        $company = Auth::user()->company;
        $currentSubscription = $company->activeSubscription;

        // Check if already on this plan or higher
        $planHierarchy = ['starter', 'professional', 'enterprise'];

        // Calculate prorated cost if upgrading mid-cycle
        $proratedCost = $this->subscriptionService->calculateProratedUpgradeCost(
            $currentSubscription,
            $validated['plan']
        );

        // Generate invoice
        $invoice = $this->generateInvoice(
            company: $company,
            plan: $validated['plan'],
            amount: $proratedCost,
            description: 'ترقية إلى ' . $this->getPlanName($validated['plan']),
        );

        return response()->json([
            'success' => true,
            'invoice_id' => $invoice->id,
            'amount' => $proratedCost,
            'message' => 'جاهز للدفع',
            'next_step' => 'payment',
        ]);
    }

    /**
     * Process payment and activate subscription
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|integer',
            'payment_method' => 'required|string',
            'payment_token' => 'nullable|string',
        ]);

        $invoice = SubscriptionInvoice::findOrFail($validated['invoice_id']);
        $company = $invoice->company;

        // TODO: Process payment with payment gateway (Stripe, PayPal, etc)

        // Mark invoice as paid
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $validated['payment_method'],
        ]);

        // TODO: Update subscription to new plan

        return response()->json([
            'success' => true,
            'message' => 'تم الدفع بنجاح وتصعيد الاشتراك',
            'redirect' => route('organizer.dashboard'),
        ]);
    }

    /**
     * Show the dedicated subscription-expired renewal page.
     *
     * This page is shown when a subscriber's subscription has expired or been
     * terminated. The subscriber is authenticated (session preserved) but all
     * platform features are blocked by the CheckSubscriptionStatus middleware.
     * They can view their account info, update contact details, and submit a
     * renewal request to the admin team.
     */
    public function showExpiredPage()
    {
        $user    = Auth::user();
        $company = $user?->company;

        $plans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->where('code', '!=', 'trial')
            ->orderBy('sort_order')
            ->get();

        return view('subscriber.subscriptions.expired', [
            'user'    => $user,
            'company' => $company,
            'plans'   => $plans,
        ]);
    }

    /**
     * Show paywall when limits exceeded or subscription ended/suspended.
     */
    public function showPaywall()
    {
        $company = Auth::user()?->company;

        // Pass actual SubscriptionPlan models so the view can use featureList()
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->where('code', '!=', 'trial')
            ->orderBy('sort_order')
            ->get();

        return view('subscriber.subscriptions.paywall', [
            'company' => $company,
            'plans'   => $plans,
        ]);
    }

    /**
     * Get subscription usage
     */
    public function getUsage()
    {
        $company = Auth::user()->company;
        $subscription = $company->activeSubscription;

        $limits = $this->subscriptionService->getUsageLimits($subscription);

        // Get actual usage
        $eventsCount = $company->events()->whereYear('created_at', Carbon::now()->year)->count();
        $invitesCount = $company->invitations()->count();

        return response()->json([
            'limits' => $limits,
            'usage' => [
                'events' => $eventsCount,
                'invites' => $invitesCount,
            ],
            'percentage' => [
                'events' => round(($eventsCount / $limits['annual_events']) * 100, 2),
                'invites' => round(($invitesCount / $limits['max_invites_per_event']) * 100, 2),
            ],
        ]);
    }

    /**
     * Generate invoice
     */
    private function generateInvoice(Company $company, string $plan, float $amount, string $description): SubscriptionInvoice
    {
        $invoiceNumber = 'INV-' . $company->id . '-' . now()->format('YmdHis');

        return SubscriptionInvoice::create([
            'company_id' => $company->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $amount,
            'description' => $description,
            'status' => 'issued',
            'issued_at' => now(),
            'due_at' => now()->addDays(30),
        ]);
    }

    private function getPlanName(string $planCode): string
    {
        return match ($planCode) {
            'starter' => 'الخطة الأساسية',
            'professional' => 'الخطة المتقدمة',
            'enterprise' => 'خطة المؤسسات',
            default => $planCode,
        };
    }
}
