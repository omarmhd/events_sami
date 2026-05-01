<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyBranding;
use App\Models\SystemSetting;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Auth;

/**
 * TenantBrandingService
 * ─────────────────────────────────────────────────────────────────────────────
 * Single authoritative source of branding data for any given request.
 *
 * Resolution priority:
 *   1. If a Company is known (authenticated user OR tenant context from subdomain)
 *      → return that company's CompanyBranding record (creating a stub if none yet).
 *   2. Otherwise (system admin panel, unauthenticated, CLI)
 *      → return platform-level defaults from SystemSetting.
 *
 * WHY THIS EXISTS:
 *   Previously SystemSetting::get('platform_logo_url') was used inside tenant-
 *   facing views, causing ALL tenants to share one branding. This service fixes
 *   that by routing every branding read through the company-scoped record first.
 *
 * USAGE IN BLADE:
 *   @php $branding = app(\App\Services\TenantBrandingService::class)->resolve(); @endphp
 *   {{ $branding->brand_name }}
 *   {{ $branding->logo_url }}
 *
 * USAGE FROM CONTROLLERS / SERVICES:
 *   $branding = app(TenantBrandingService::class)->forCompany($company);
 *   $branding = app(TenantBrandingService::class)->resolve();
 */
class TenantBrandingService
{
    /**
     * Platform-level defaults (used when no company context exists).
     * These come from system_settings seeded by the admin panel.
     */
    public function platformDefaults(): array
    {
        return [
            'brand_name'        => SystemSetting::get('platform_name',      config('app.name', 'Platform')),
            'logo_url'          => SystemSetting::get('platform_logo_url',   ''),
            'header_image_url'  => null,
            'primary_color'     => SystemSetting::get('primary_color',       '#0f8f83'),
            'secondary_color'   => SystemSetting::get('secondary_color',     '#f59e0b'),
            'sender_name'       => SystemSetting::get('platform_name',       config('app.name', 'Platform')),
            'sender_email'      => config('mail.from.address', 'noreply@platform.com'),
            'reply_to_email'    => null,
            'header_html'       => null,
            'footer_html'       => null,
            'footer_text'       => null,
        ];
    }

    /**
     * Resolve the correct branding for the current HTTP request.
     *
     * Returns a CompanyBranding model when a company context is available,
     * or a "virtual" CompanyBranding (not persisted) filled with platform
     * defaults when running in system/admin mode.
     */
    public function resolve(): CompanyBranding
    {
        // 1. Try authenticated user's company first (most common path for dashboard).
        if (Auth::check()) {
            $company = Auth::user()->company ?? Auth::user()->organization ?? null;
            if ($company instanceof Company) {
                return $this->forCompany($company);
            }
        }

        // 2. Try tenant context (set by ResolveTenantFromSubdomain middleware).
        $tenantContext = app(TenantContext::class);
        if ($tenantContext->hasOrganization()) {
            return $this->forCompany($tenantContext->organization());
        }

        // 3. Fall back to system / platform-level defaults.
        return $this->platformBranding();
    }

    /**
     * Resolve branding for a specific company.
     *
     * Creates a CompanyBranding stub with sensible defaults if none exists yet.
     * The stub is ONLY created when the company has a branding record missing
     * (first-time setup). It will NOT overwrite existing records.
     */
    public function forCompany(Company $company): CompanyBranding
    {
        // Inherit platform-level defaults from SystemSetting so admin config
        // is reflected in new tenants without hardcoded values.
        $platformEmail = SystemSetting::get('platform_sender_email',
                            config('mail.from.address', 'noreply@platform.com'));

        $branding = CompanyBranding::firstOrCreate(
            ['company_id' => $company->id],
            [
                'brand_name'      => $company->name,
                'primary_color'   => SystemSetting::get('primary_color', '#0f8f83'),
                'secondary_color' => SystemSetting::get('secondary_color', '#f59e0b'),
                'sender_name'     => $company->name,
                'sender_email'    => $company->billing_email
                                        ?: $company->contact_email
                                        ?: $platformEmail,
            ]
        );

        return $branding;
    }

    /**
     * Build a non-persisted CompanyBranding instance carrying platform defaults.
     * Used for the system admin panel and any request with no company context.
     */
    public function platformBranding(): CompanyBranding
    {
        $defaults = $this->platformDefaults();
        $obj = new CompanyBranding();
        foreach ($defaults as $key => $value) {
            $obj->$key = $value;
        }
        return $obj;
    }

    /**
     * Return a plain array of branding values for the given company
     * (or platform defaults if null). Useful for passing to Blade views.
     */
    public function toArray(?Company $company = null): array
    {
        $branding = $company ? $this->forCompany($company) : $this->resolve();

        return [
            'brand_name'       => $branding->brand_name      ?: '',
            'logo_url'         => $branding->logo_url        ?: '',
            'header_image_url' => $branding->header_image_url ?: '',
            'primary_color'    => $branding->primary_color    ?: '#0f8f83',
            'secondary_color'  => $branding->secondary_color  ?: '#f59e0b',
            'sender_name'      => $branding->sender_name      ?: '',
            'sender_email'     => $branding->sender_email     ?: '',
            'reply_to_email'   => $branding->reply_to_email   ?: '',
            'header_html'      => $branding->header_html      ?: '',
            'footer_html'      => $branding->footer_html      ?: '',
            'footer_text'      => $branding->footer_text      ?: '',
        ];
    }
}
