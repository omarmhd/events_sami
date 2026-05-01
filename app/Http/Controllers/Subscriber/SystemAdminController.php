<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\BillingContactRequest;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SystemAdminController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    //  DASHBOARD
    // ──────────────────────────────────────────────────────────────────────────

    public function dashboard()
    {
        // ── Revenue ────────────────────────────────────────────────────────────
        $totalRevenue = SubscriptionInvoice::where('status', 'paid')->sum('total_amount');
        $monthRevenue = SubscriptionInvoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');

        // ── Counts ─────────────────────────────────────────────────────────────
        $companiesCount          = Company::count();
        $activeTrialsCount       = CompanySubscription::where('status', 'trial')->count();
        $activePaidCount         = CompanySubscription::where('status', 'active')->count();
        $subscriptionsCount      = CompanySubscription::count();
        $activeSubscriptionsCount= CompanySubscription::whereIn('status', ['active', 'trial'])->count();
        $usersCount              = User::count();
        $organizerUsersCount     = User::whereNotNull('company_id')->count();

        // ── Invoice stats ──────────────────────────────────────────────────────
        $totalInvoices    = SubscriptionInvoice::count();
        $paidInvoices     = SubscriptionInvoice::where('status', 'paid')->count();
        $unpaidInvoices   = SubscriptionInvoice::whereIn('status', ['pending', 'unpaid'])->count();
        $overdueInvoices  = SubscriptionInvoice::whereIn('status', ['pending', 'unpaid'])
            ->where('due_at', '<', now())->count();

        // ── Plan distribution ──────────────────────────────────────────────────
        $planDistribution = SubscriptionPlan::withCount([
            'subscriptions as active_count' => fn($q) => $q->where('status', 'active'),
            'subscriptions as trial_count'  => fn($q) => $q->where('status', 'trial'),
        ])->orderBy('sort_order')->get();

        // ── Monthly revenue chart (last 6 months) ──────────────────────────────
        $monthlyRevenue = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $amount = SubscriptionInvoice::where('status', 'paid')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('total_amount');
            return [
                'label'  => $date->format('M Y'),
                'amount' => round($amount, 2),
            ];
        });

        // ── Recent companies ───────────────────────────────────────────────────
        $recentCompanies = Company::with(['latestSubscription.plan'])
            ->latest()->take(6)->get();

        // ── Pending renewal / contact requests ─────────────────────────────────
        $pendingRenewals = BillingContactRequest::with('company')
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $pendingRenewalsCount = BillingContactRequest::where('status', 'pending')->count();

        return view('subscriber.system.dashboard', compact(
            'companiesCount', 'activeTrialsCount', 'activePaidCount',
            'subscriptionsCount', 'activeSubscriptionsCount',
            'usersCount', 'organizerUsersCount',
            'totalRevenue', 'monthRevenue',
            'totalInvoices', 'paidInvoices', 'unpaidInvoices', 'overdueInvoices',
            'planDistribution', 'monthlyRevenue', 'recentCompanies',
            'pendingRenewals', 'pendingRenewalsCount'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  COMPANIES (SUBSCRIBERS)
    // ──────────────────────────────────────────────────────────────────────────

    public function companies(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $companies = Company::query()
            ->with(['owner', 'latestSubscription.plan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('subdomain', 'like', '%' . $search . '%')
                        ->orWhere('contact_email', 'like', '%' . $search . '%');
                });
            })
            ->latest('id')
            ->paginate(20);

        $plans = SubscriptionPlan::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('subscriber.system.companies', [
            'companies' => $companies,
            'plans'     => $plans,
            'search'    => $search,
        ]);
    }

    public function storeCompany(Request $request, SubscriptionService $subscriptionService)
    {
        $data = $request->validate([
            'organization_name'     => ['required', 'string', 'max:160'],
            'owner_name'            => ['required', 'string', 'max:120'],
            'owner_email'           => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'subdomain'             => ['required', 'alpha_dash', 'min:3', 'max:40', 'unique:companies,subdomain'],
            'annual_events_estimate'=> ['nullable', 'integer', 'min:1', 'max:100000'],
            'status'                => ['required', Rule::in(['trial', 'active', 'suspended'])],
        ]);

        DB::transaction(function () use ($data, $subscriptionService) {
            $company = Company::create([
                'name'                  => $data['organization_name'],
                'contact_email'         => $data['owner_email'],
                'phone'                 => $data['phone'] ?? null,
                'subdomain'             => strtolower($data['subdomain']),
                'status'                => $data['status'],
                'annual_events_estimate'=> $data['annual_events_estimate'] ?? null,
                'billing_email'         => $data['owner_email'],
                'timezone'              => 'Asia/Riyadh',
                'trial_started_at'      => Carbon::now(),
                'trial_ends_at'         => Carbon::now()->addDays(SubscriptionService::TRIAL_DAYS),
                'onboarding_completed_at' => Carbon::now(),
            ]);

            $owner = User::create([
                'name'           => $data['owner_name'],
                'email'          => $data['owner_email'],
                'password'       => Hash::make(Str::random(40)),
                'role'           => 'organizer_owner',
                'is_system_admin'=> false,
                'organization_id'=> $company->id,
                'company_id'     => $company->id,
                'phone'          => $data['phone'] ?? null,
            ]);

            $company->update(['owner_user_id' => $owner->id]);
            $subscriptionService->ensureCompanySubscription($company);
        });

        return back()->with('success', 'تم إنشاء المنظمة بنجاح.');
    }

    public function updateCompany(Request $request, Company $company)
    {
        $data = $request->validate([
            'organization_name'     => ['required', 'string', 'max:160'],
            'contact_email'         => ['nullable', 'email', 'max:190'],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'subdomain'             => ['required', 'alpha_dash', 'min:3', 'max:40', Rule::unique('companies', 'subdomain')->ignore($company->id)],
            'status'                => ['required', Rule::in(['active', 'trial', 'suspended'])],
            'annual_events_estimate'=> ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        $company->update([
            'name'                  => $data['organization_name'],
            'contact_email'         => $data['contact_email'] ?? $company->contact_email,
            'phone'                 => $data['phone'] ?? null,
            'subdomain'             => strtolower($data['subdomain']),
            'status'                => $data['status'],
            'annual_events_estimate'=> $data['annual_events_estimate'] ?? null,
        ]);

        return back()->with('success', 'تم تحديث المنظمة بنجاح.');
    }

    public function updateCompanyStatus(Request $request, Company $company)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended', 'trial'])],
        ]);

        $company->update(['status' => $data['status']]);

        return back()->with('success', 'تم تحديث حالة المنظمة.');
    }

    public function forceSubscriptionPlan(Request $request, Company $company, SubscriptionService $subscriptionService)
    {
        $data = $request->validate([
            'plan_code' => ['required', Rule::exists('subscription_plans', 'code')],
            'status'    => ['required', Rule::in(['trial', 'active'])],
        ]);

        $plan = SubscriptionPlan::where('code', $data['plan_code'])->firstOrFail();
        $subscriptionService->switchCompanyPlan($company, $plan, $data['status']);

        return back()->with('success', 'تم تحديث خطة الاشتراك.');
    }

    /**
     * Terminate a company's active subscription immediately.
     *
     * This sets the subscription status to 'terminated', records who terminated it
     * and when, then suspends the company account so the CheckSubscriptionStatus
     * middleware redirects the subscriber to the paywall on their next request.
     *
     * An optional admin note can be stored for audit purposes.
     */
    public function terminateSubscription(Request $request, Company $company)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $subscription = $company->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->latest('id')
            ->first();

        if (!$subscription) {
            return back()->with('error', 'لا يوجد اشتراك نشط لهذه المنظمة.');
        }

        DB::transaction(function () use ($subscription, $company, $data) {
            $subscription->update([
                'status'   => 'terminated',
                'ends_at'  => now(),
                'metadata' => array_merge($subscription->metadata ?? [], [
                    'terminated_at'     => now()->toISOString(),
                    'terminated_by'     => Auth::id(),
                    'termination_reason'=> $data['reason'] ?? null,
                ]),
            ]);

            // Suspend the company so the middleware catches the next login.
            $company->update(['status' => 'suspended']);
        });

        return back()->with('success', 'تم إنهاء اشتراك المنظمة وتعليق حسابها بنجاح.');
    }

    public function impersonate(Company $company)
    {
        $target = $company->owner ?: User::query()
            ->where(function ($query) use ($company) {
                $query->where('organization_id', $company->id)
                      ->orWhere('company_id', $company->id);
            })
            ->whereIn('role', ['organizer_owner', 'organizer_admin'])
            ->first();

        if (!$target) {
            return back()->with('error', 'لا يوجد مستخدم منظم لهذه المنظمة.');
        }

        session(['impersonator_id' => Auth::id()]);
        Auth::login($target);

        return redirect()->route('dashboard.index')->with('success', 'تم تفعيل وضع الانتحال.');
    }

    public function leaveImpersonation(Request $request)
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard.index');
        }

        $impersonator = User::find($impersonatorId);
        if (!$impersonator) {
            Auth::logout();
            return redirect()->route('login');
        }

        Auth::login($impersonator);
        return redirect()->route('system.dashboard')->with('success', 'تم العودة إلى حساب المشرف.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  SUBSCRIPTIONS
    // ──────────────────────────────────────────────────────────────────────────

    public function subscriptions()
    {
        $subscriptions = CompanySubscription::query()
            ->with(['company', 'plan'])
            ->latest('id')
            ->paginate(25);

        $plans = SubscriptionPlan::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('subscriber.system.subscriptions', [
            'subscriptions' => $subscriptions,
            'plans'         => $plans,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  SYSTEM USERS
    // ──────────────────────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->with('company')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->latest('id')
            ->paginate(25);

        return view('subscriber.system.users', [
            'users'  => $users,
            'search' => $search,
        ]);
    }

    public function storeSystemUser(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['super_admin', 'saas_admin'])],
        ]);

        User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'role'           => $data['role'],
            'is_system_admin'=> true,
            'company_id'     => null,
            'organization_id'=> null,
        ]);

        return back()->with('success', 'تم إنشاء مستخدم النظام.');
    }

    public function updateSystemUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'role'     => ['required', Rule::in(['super_admin', 'saas_admin'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name           = $data['name'];
        $user->role           = $data['role'];
        $user->is_system_admin= true;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        return back()->with('success', 'تم تحديث مستخدم النظام.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  PLAN MANAGEMENT
    // ──────────────────────────────────────────────────────────────────────────

    public function plans()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->orderBy('sort_order')->get();

        return view('subscriber.system.plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'code'                   => ['required', 'string', 'max:40', 'alpha_dash', 'unique:subscription_plans,code'],
            'name'                   => ['required', 'string', 'max:120'],
            'description'            => ['nullable', 'string', 'max:500'],
            'annual_price'           => ['required', 'numeric', 'min:0'],
            'per_event_price'        => ['nullable', 'numeric', 'min:0'],
            'annual_event_limit'     => ['nullable', 'integer', 'min:0'],
            'per_event_invitee_limit'=> ['nullable', 'integer', 'min:0'],
            'includes_csv_import'    => ['nullable', 'boolean'],
            'includes_bulk_resend'   => ['nullable', 'boolean'],
            'includes_customization' => ['nullable', 'boolean'],
            'highlight_label'        => ['nullable', 'string', 'max:60'],
            'is_active'              => ['nullable', 'boolean'],
            'sort_order'             => ['nullable', 'integer', 'min:0'],
        ]);

        // Checkboxes default to false if absent
        $data['includes_csv_import']    = $request->boolean('includes_csv_import');
        $data['includes_bulk_resend']   = $request->boolean('includes_bulk_resend');
        $data['includes_customization'] = $request->boolean('includes_customization');
        $data['is_active']              = $request->boolean('is_active', true);

        // Dynamic JSON features from the feature builder
        $data['features'] = $this->parseFeaturesJson($request->input('features_json'));

        SubscriptionPlan::create($data);

        return back()->with('success', 'تم إنشاء الخطة بنجاح.');
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name'                   => ['required', 'string', 'max:120'],
            'description'            => ['nullable', 'string', 'max:500'],
            'annual_price'           => ['required', 'numeric', 'min:0'],
            'per_event_price'        => ['nullable', 'numeric', 'min:0'],
            'annual_event_limit'     => ['nullable', 'integer', 'min:0'],
            'per_event_invitee_limit'=> ['nullable', 'integer', 'min:0'],
            'includes_csv_import'    => ['nullable', 'boolean'],
            'includes_bulk_resend'   => ['nullable', 'boolean'],
            'includes_customization' => ['nullable', 'boolean'],
            'highlight_label'        => ['nullable', 'string', 'max:60'],
            'is_active'              => ['nullable', 'boolean'],
            'sort_order'             => ['nullable', 'integer', 'min:0'],
        ]);

        $data['includes_csv_import']    = $request->boolean('includes_csv_import');
        $data['includes_bulk_resend']   = $request->boolean('includes_bulk_resend');
        $data['includes_customization'] = $request->boolean('includes_customization');
        $data['is_active']              = $request->boolean('is_active', true);

        // Dynamic JSON features from the feature builder
        $data['features'] = $this->parseFeaturesJson($request->input('features_json'));

        $plan->update($data);

        return back()->with('success', 'تم تحديث الخطة بنجاح.');
    }

    /**
     * Parse and sanitize the JSON features string from the feature builder.
     *
     * Each feature row is saved as:
     * {
     *   "key":     "registration_forms",   ← canonical key (used by hasFeature / featureLimit)
     *   "label":   "نماذج التسجيل",
     *   "icon":    "fas fa-wpforms",
     *   "enabled": true,
     *   "limit":   5                        ← null = unlimited (used by featureLimit)
     * }
     *
     * Rules:
     *  • key   → lowercase alphanumeric + underscore only (matches FeatureRegistry keys)
     *  • label → required, trimmed
     *  • icon  → any fa class string; default if empty
     *  • enabled → boolean
     *  • limit → positive integer or null; only meaningful for features that support limits
     *             (currently: registration_forms). For others it's stored but ignored.
     */
    private function parseFeaturesJson(?string $json): ?array
    {
        if (empty(trim((string) $json))) return null;

        try {
            $features = json_decode($json, true, 5, JSON_THROW_ON_ERROR);
            if (!is_array($features)) return null;

            return collect($features)
                ->filter(fn($f) => is_array($f) && !empty(trim($f['label'] ?? '')))
                ->map(function ($f) {
                    // Sanitize key: lowercase, only a-z 0-9 _
                    $key = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($f['key'] ?? '')));

                    // Limit: only store a positive int, otherwise null
                    $limit = null;
                    if (isset($f['limit']) && $f['limit'] !== '' && $f['limit'] !== null) {
                        $limitVal = (int) $f['limit'];
                        if ($limitVal > 0) $limit = $limitVal;
                    }

                    return [
                        'key'     => $key ?: null,
                        'label'   => trim($f['label']),
                        'icon'    => !empty($f['icon']) ? $f['icon'] : 'fas fa-circle-check',
                        'enabled' => (bool) ($f['enabled'] ?? true),
                        'limit'   => $limit,
                    ];
                })
                ->values()
                ->toArray();

        } catch (\Throwable) {
            return null;
        }
    }

    public function destroyPlan(SubscriptionPlan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'لا يمكن حذف خطة مرتبطة باشتراكات نشطة.');
        }

        $plan->delete();
        return back()->with('success', 'تم حذف الخطة.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  RENEWAL / CONTACT REQUESTS
    // ──────────────────────────────────────────────────────────────────────────

    public function renewalRequests(Request $request)
    {
        $filter = $request->query('status', 'pending');

        $requests = BillingContactRequest::with('company')
            ->when($filter !== 'all', fn($q) => $q->where('status', $filter))
            ->latest()
            ->paginate(20);

        return view('subscriber.system.renewal-requests', [
            'requests' => $requests,
            'filter'   => $filter,
        ]);
    }

    public function markRenewalContacted(Request $request, BillingContactRequest $billingRequest)
    {
        $billingRequest->update([
            'status'       => 'contacted',
            'contacted_at' => now(),
        ]);

        return back()->with('success', "تم تحديد طلب {$billingRequest->contact_name} كـ \"تم التواصل\".");
    }

    public function dismissRenewalRequest(BillingContactRequest $billingRequest)
    {
        $billingRequest->update(['status' => 'dismissed']);
        return back()->with('success', 'تم أرشفة الطلب.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  INVOICE MANAGEMENT
    // ──────────────────────────────────────────────────────────────────────────

    public function invoices(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        $invoices = SubscriptionInvoice::query()
            ->with(['company', 'subscription.plan'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('invoice_number', 'like', '%' . $search . '%')
                       ->orWhereHas('company', fn($c) => $c->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(25);

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $plans     = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();

        $stats = [
            'total'   => SubscriptionInvoice::count(),
            'paid'    => SubscriptionInvoice::where('status', 'paid')->count(),
            'pending' => SubscriptionInvoice::whereIn('status', ['pending', 'unpaid'])->count(),
            'overdue' => SubscriptionInvoice::whereIn('status', ['pending', 'unpaid'])->where('due_at', '<', now())->count(),
        ];

        return view('subscriber.system.invoices', compact('invoices', 'companies', 'plans', 'stats', 'search', 'status'));
    }

    public function storeInvoice(Request $request, BillingService $billingService)
    {
        $data = $request->validate([
            'company_id'     => ['required', 'integer', 'exists:companies,id'],
            'plan_id'        => ['required', 'integer', 'exists:subscription_plans,id'],
            'amount'         => ['required', 'numeric', 'min:0'],
            'due_at'         => ['required', 'date'],
            'status'         => ['required', Rule::in(['pending', 'paid', 'unpaid'])],
            'auto_activate'  => ['nullable', 'boolean'],
            'payment_method' => ['nullable', 'string', 'max:60'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $company = Company::findOrFail($data['company_id']);
        $plan    = SubscriptionPlan::findOrFail($data['plan_id']);

        // Find or create the company's subscription for this plan
        $subscription = CompanySubscription::where('company_id', $company->id)
            ->latest('id')->first();

        if (!$subscription) {
            $subscription = CompanySubscription::create([
                'company_id'   => $company->id,
                'plan_id'      => $plan->id,
                'status'       => 'pending',
                'starts_at'    => now(),
                'ends_at'      => now()->addYear(),
            ]);
        }

        $amount  = (float) $data['amount'];
        $tax     = round($amount * 0.15, 2);
        $total   = round($amount + $tax, 2);

        $invoice = SubscriptionInvoice::create([
            'company_subscription_id' => $subscription->id,
            'company_id'              => $company->id,
            'invoice_number'          => 'INV-' . $company->id . '-' . now()->format('YmdHis'),
            'amount'                  => $amount,
            'tax_amount'              => $tax,
            'total_amount'            => $total,
            'currency'                => 'SAR',
            'status'                  => $data['status'],
            'issued_at'               => now(),
            'paid_at'                 => $data['status'] === 'paid' ? now() : null,
            'due_at'                  => $data['due_at'],
            'payload'                 => [
                'payment_method' => $data['payment_method'] ?? null,
                'notes'          => $data['notes'] ?? null,
                'plan_code'      => $plan->code,
                'type'           => 'manual_invoice',
            ],
        ]);

        // Auto-activate subscription if invoice is paid and checkbox was checked
        if ($data['status'] === 'paid' && $request->boolean('auto_activate')) {
            $subscription->update([
                'plan_id'   => $plan->id,
                'status'    => 'active',
                'starts_at' => now(),
                'ends_at'   => now()->addYear(),
            ]);

            $company->update(['current_plan_code' => $plan->code]);
        }

        return back()->with('success', "تم إنشاء الفاتورة {$invoice->invoice_number} بنجاح.");
    }

    public function markInvoicePaid(Request $request, SubscriptionInvoice $invoice)
    {
        $data = $request->validate([
            'payment_method' => ['nullable', 'string', 'max:60'],
            'auto_activate'  => ['nullable', 'boolean'],
        ]);

        $invoice->update([
            'status'  => 'paid',
            'paid_at' => now(),
            'payload' => array_merge($invoice->payload ?? [], [
                'payment_method' => $data['payment_method'] ?? 'manual',
                'marked_paid_by' => auth()->id(),
            ]),
        ]);

        // Auto-activate subscription if requested
        if ($request->boolean('auto_activate') && $invoice->subscription) {
            $sub  = $invoice->subscription;
            $plan = $sub->plan;

            $sub->update([
                'status'   => 'active',
                'starts_at'=> now(),
                'ends_at'  => now()->addYear(),
            ]);

            if ($plan && $invoice->company) {
                $invoice->company->update(['current_plan_code' => $plan->code]);
            }
        }

        return back()->with('success', 'تم تحديث حالة الفاتورة إلى مدفوعة.');
    }

    public function destroyInvoice(SubscriptionInvoice $invoice)
    {
        $invoice->delete();
        return back()->with('success', 'تم حذف الفاتورة.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  SYSTEM SETTINGS
    // ──────────────────────────────────────────────────────────────────────────

    public function settings()
    {
        $settings = SystemSetting::allKeyed();

        return view('subscriber.system.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'platform_name'      => ['nullable', 'string', 'max:120'],
            'platform_logo_url'  => ['nullable', 'string', 'max:500'],
            'platform_logo_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:4096'],
            'primary_color'      => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'secondary_color'    => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'support_email'      => ['nullable', 'email', 'max:190'],
            'trial_days'         => ['nullable', 'integer', 'min:1', 'max:365'],
            'maintenance_mode'   => ['nullable', 'boolean'],
        ]);

        // User clicked X on the logo → delete file + clear DB record.
        if ($request->input('clear_logo') === '1') {
            $oldLogo = SystemSetting::get('platform_logo_url');
            if ($oldLogo) {
                $rel = ltrim((string) parse_url($oldLogo, PHP_URL_PATH), '/');
                if (str_starts_with($rel, 'uploads/system/') && file_exists(public_path($rel))) {
                    @unlink(public_path($rel));
                }
            }
            SystemSetting::where('key', 'platform_logo_url')->delete();
            // Don't process the URL field if user asked to clear.
            unset($data['platform_logo_url']);
        }

        // Handle logo file upload — overwrites any manually typed URL.
        if ($request->hasFile('platform_logo_file')) {
            $uploadDir = public_path('uploads/system');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Delete the previous uploaded logo when it lives in our managed folder.
            $oldLogo = SystemSetting::get('platform_logo_url');
            if ($oldLogo) {
                $oldRelPath = ltrim((string) parse_url($oldLogo, PHP_URL_PATH), '/');
                if (str_starts_with($oldRelPath, 'uploads/system/')) {
                    $oldAbs = public_path($oldRelPath);
                    if (file_exists($oldAbs)) {
                        @unlink($oldAbs);
                    }
                }
            }

            $file     = $request->file('platform_logo_file');
            $filename = 'logo_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['platform_logo_url'] = asset('uploads/system/' . $filename);
        }

        unset($data['platform_logo_file']);

        $groups = [
            'platform_name'     => 'branding',
            'platform_logo_url' => 'branding',
            'primary_color'     => 'branding',
            'secondary_color'   => 'branding',
            'support_email'     => 'contact',
            'trial_days'        => 'limits',
            'maintenance_mode'  => 'system',
        ];

        foreach ($data as $key => $value) {
            // Save any non-null value; empty string clears the setting cleanly.
            if ($value !== null) {
                if ($value === '') {
                    // User cleared the field — remove the record so there is no stale value.
                    SystemSetting::where('key', $key)->delete();
                } else {
                    SystemSetting::set($key, $value, $groups[$key] ?? 'general');
                }
            }
        }

        // maintenance_mode is a checkbox — always persist it explicitly.
        SystemSetting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0', 'system');

        return back()->with('success', 'تم حفظ إعدادات النظام بنجاح.');
    }

    public function clearLogo()
    {
        // Delete the physical file if it was uploaded to our server.
        // Match any URL that resolves to a path inside public/uploads/system.
        $oldLogo = SystemSetting::get('platform_logo_url');

        if ($oldLogo) {
            $urlPath = ltrim((string) parse_url($oldLogo, PHP_URL_PATH), '/');

            // Only delete files that live in our managed upload directory.
            if (str_starts_with($urlPath, 'uploads/system/') || str_contains($urlPath, 'uploads/system/')) {
                $absolutePath = public_path($urlPath);
                if (file_exists($absolutePath)) {
                    @unlink($absolutePath);
                }
            }
        }

        // Remove the record entirely so the component falls back to the text logo cleanly.
        \App\Models\SystemSetting::where('key', 'platform_logo_url')->delete();

        return back()->with('success', 'تم حذف الشعار بنجاح.');
    }
}
