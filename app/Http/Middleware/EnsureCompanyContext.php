<?php

namespace App\Http\Middleware;

use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;

class EnsureCompanyContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        /** @var TenantContext $tenant */
        $tenant = app(TenantContext::class);

        if (!$user) {
            return redirect()->route('login');
        }

        if ($tenant->isSystem()) {
            if ($request->session()->has('impersonator_id')) {
                return $next($request);
            }

            if (!$user->isSystemAdmin()) {
                abort(403, 'Only system admins can access this domain.');
            }

            return $next($request);
        }

        if (!$user->company_id && !$user->organization_id) {
            return redirect()->route('onboarding.profile');
        }

        $userOrganizationId = $user->organization_id ?: $user->company_id;

        if ($tenant->hasOrganization() && $tenant->organizationId() !== (int) $userOrganizationId) {
            abort(403, 'Cross-tenant access denied.');
        }

        if ($user->company && !$user->company->onboarding_completed_at) {
            return redirect()->route('onboarding.profile');
        }

        return $next($request);
    }
}
