<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Services\SubscriptionService;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ResolvePublicTenant
 * ─────────────────────────────────────────────────────────────────────────────
 * Resolves the tenant company from a subdomain on PUBLIC routes (invitation
 * pages, RSVP, public event registration) — without requiring authentication.
 *
 * This middleware is separate from ResolveTenantFromSubdomain (which runs
 * globally) because public routes need an ADDITIONAL guard:
 *
 *   • If the request arrives on a subdomain (e.g. acme.maaninvite.com):
 *       – Verify the company has `custom_subdomain` feature enabled.
 *       – If not, redirect to the canonical main-domain URL for the same path.
 *
 *   • If the request arrives on the main domain (maaninvite.com):
 *       – Pass through normally; the token/slug identifies the company.
 *
 * The TenantContext is already populated by ResolveTenantFromSubdomain (which
 * runs first on every request), so this middleware only needs to add the
 * plan-feature check layer.
 *
 * REGISTRATION:
 *   Add 'public.tenant' alias in app/Http/Kernel.php routeMiddleware,
 *   then apply to the public route group in routes/front.php.
 */
class ResolvePublicTenant
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningInConsole()) {
            return $next($request);
        }

        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);

        // If the global middleware already found an organization via subdomain,
        // verify that this company is allowed to use subdomain-based public URLs.
        if ($tenantContext->hasOrganization()) {
            $company = $tenantContext->organization();

            $hasSubdomainFeature = $this->subscriptionService->featureEnabled(
                $company,
                'custom_subdomain'
            );

            if (!$hasSubdomainFeature) {
                // Company doesn't have the plan feature — redirect to main-domain URL.
                $mainDomainBase = rtrim(config('app.url'), '/');
                $canonicalUrl   = $mainDomainBase . '/' . ltrim($request->getPathInfo(), '/');

                if ($request->getQueryString()) {
                    $canonicalUrl .= '?' . $request->getQueryString();
                }

                return redirect($canonicalUrl, 301);
            }
        }

        // Main-domain or subdomain with valid feature — continue.
        return $next($request);
    }
}
