<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;

class ResolveTenantFromSubdomain
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->runningInConsole()) {
            return $next($request);
        }

        $host = strtolower((string) $request->getHost());
        $baseDomain = strtolower((string) config('tenancy.base_domain'));
        $adminSubdomain = strtolower((string) config('tenancy.admin_subdomain'));
        $allowUnknownHosts = (bool) config('tenancy.allow_unknown_hosts', true);

        /** @var TenantContext $tenant */
        $tenant = app(TenantContext::class);

        if ($host === "{$adminSubdomain}.{$baseDomain}") {
            $tenant->setSystem($host);
            return $next($request);
        }

        if ($baseDomain !== '' && str_ends_with($host, ".{$baseDomain}")) {
            $subdomain = substr($host, 0, -1 * (strlen($baseDomain) + 1));

            if ($subdomain === '' || str_contains($subdomain, '.')) {
                abort(404);
            }

            $organization = Company::query()
                ->where('subdomain', $subdomain)
                ->whereIn('status', ['active', 'trial'])
                ->first();

            if (!$organization) {
                abort(404, 'Organization not found for subdomain.');
            }

            $tenant->setOrganization($organization, $host, $subdomain);
            return $next($request);
        }

        if ($allowUnknownHosts) {
            $tenant->clear($host);
            return $next($request);
        }

        abort(404);
    }
}

