<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * CheckSubscriptionStatus
 *
 * Intercepts every authenticated subscriber request and evaluates the
 * company's current status + subscription state. Depending on the outcome
 * the request is either allowed through, redirected to the paywall, or
 * redirected to the subscription-expired renew page.
 *
 * Exempt routes (accessible even when suspended / expired):
 *  - billing.*           (paywall, renewal request, upgrade)
 *  - subscription.*      (paywall page)
 *  - logout
 *  - system.*            (admin panel – system admins are always exempt)
 *  - impersonation routes
 */
class CheckSubscriptionStatus
{
    /**
     * Route name prefixes that are always allowed through regardless of
     * subscription / suspension state.
     */
    private const EXEMPT_PREFIXES = [
        'billing.',
        'subscription.',   // covers both subscription.show-paywall AND subscription.expired
        'system.',
        'logout',
        'lang.',
        'onboarding.',
        'system.impersonation',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Unauthenticated requests are handled by the 'auth' middleware.
        if (!$user) {
            return $next($request);
        }

        // System admins are never blocked.
        if ($user->isSystemAdmin()) {
            return $next($request);
        }

        // Impersonation sessions pass through.
        if ($request->session()->has('impersonator_id')) {
            return $next($request);
        }

        // Exempt routes pass through.
        $routeName = $request->route()?->getName() ?? '';
        foreach (self::EXEMPT_PREFIXES as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return $next($request);
            }
        }

        $company = $user->company;

        // No company yet — onboarding will handle it.
        if (!$company) {
            return $next($request);
        }

        // ── 1. Company-level suspension ──────────────────────────────────────
        //    The administrator suspended the company entirely (company.status = suspended).
        //    We redirect to the paywall with reason=suspended so the subscriber
        //    can see the reason and submit a reactivation request.
        // ── 1. Company-level suspension ──────────────────────────────────────
        //    Administrator suspended the company → paywall (suspended reason).
        if ($company->status === 'suspended') {
            return redirect()
                ->route('subscription.expired', ['reason' => 'suspended'])
                ->with('warning', 'حسابك موقوف مؤقتاً. تواصل مع فريق الدعم لإعادة التفعيل.');
        }

        // ── 2. Subscription-level expiry / termination ───────────────────────
        $subscription = $company->activeSubscription();

        if (!$subscription) {
            $latestSub = $company->latestSubscription;

            $reason = 'no_subscription';
            if ($latestSub) {
                $reason = match ($latestSub->status) {
                    'terminated' => 'terminated',
                    'expired'    => 'subscription_ended',
                    'suspended'  => 'suspended',
                    default      => 'no_subscription',
                };
            }

            // Terminated / expired → full renewal page; others → paywall.
            $route = in_array($reason, ['terminated', 'subscription_ended'])
                ? 'subscription.expired'
                : 'subscription.show-paywall';

            return redirect()
                ->route($route, ['reason' => $reason])
                ->with('warning', 'لا يوجد اشتراك نشط. يرجى تجديد اشتراكك للمتابعة.');
        }

        // Check if an active subscription's period has elapsed.
        if ($subscription->ends_at && $subscription->ends_at->isPast()) {
            if ($subscription->status === 'active') {
                $subscription->update(['status' => 'expired']);
                $company->update(['status' => 'suspended']);
            }

            return redirect()
                ->route('subscription.expired', ['reason' => 'subscription_ended'])
                ->with('warning', 'انتهت مدة اشتراكك. يرجى التجديد للمتابعة.');
        }

        // ── 3. Trial expiry ──────────────────────────────────────────────────
        if ($subscription->isTrial() && $subscription->trial_ends_at && $subscription->trial_ends_at->isPast()) {
            return redirect()
                ->route('subscription.expired', ['reason' => 'trial_expired'])
                ->with('warning', 'انتهت الفترة التجريبية. اختر خطة للاستمرار.');
        }

        return $next($request);
    }
}
