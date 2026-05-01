<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use App\Support\FeatureRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckFeatureAccess
 *
 * Protects individual routes / route groups based on plan-level feature flags.
 *
 * Usage in routes/web.php:
 *
 *   Route::middleware(['feature:teams'])->group(function () { ... });
 *   Route::middleware(['feature:visual_identity'])->group(function () { ... });
 *
 * The middleware parameter is the canonical feature key (or any registered alias
 * from FeatureRegistry::ALIASES).
 *
 * When the feature is disabled the user is redirected to the feature-unavailable
 * page which reads the feature metadata from config/features.php for display.
 */
class CheckFeatureAccess
{
    public function __construct(private SubscriptionService $subscriptionService)
    {
    }

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        // Not logged in — let the auth middleware handle it.
        if (!$user) {
            return $next($request);
        }

        $company = $user->company;

        // No company context (e.g. system admin) — pass through.
        if (!$company) {
            return $next($request);
        }

        // Normalise via FeatureRegistry aliases.
        $featureKey = FeatureRegistry::normalize($feature);

        // Check via SubscriptionService (respects FeatureAccess overrides).
        $enabled = $this->subscriptionService->featureEnabled($company, $featureKey);

        if ($enabled) {
            return $next($request);
        }

        // AJAX / JSON requests get a 403 JSON response.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'هذه الميزة غير متاحة في خطتك الحالية.',
                'feature' => $featureKey,
            ], 403);
        }

        // Redirect to the feature-unavailable page, carrying the feature key.
        return redirect()->route('feature.unavailable', ['feature' => $featureKey]);
    }
}
