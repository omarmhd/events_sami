<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Event;

/**
 * PublicUrlService
 * ─────────────────────────────────────────────────────────────────────────────
 * Single authoritative source for generating public-facing URLs.
 *
 * When a company has the `custom_subdomain` plan feature enabled AND has a
 * non-empty `subdomain` field, all public invitation / RSVP / registration
 * links are prefixed with that tenant's subdomain:
 *
 *   https://{subdomain}.maaninvite.com/rsvp/{token}
 *   https://{subdomain}.maaninvite.com/events/{slug}
 *
 * Otherwise the main-domain URL is used:
 *
 *   https://maaninvite.com/rsvp/{token}
 *
 * WHY THIS EXISTS:
 *   Subdomain-based public pages are a plan-gated enterprise feature. This
 *   service encapsulates the routing decision so controllers, email services,
 *   and Blade templates never need to duplicate the logic.
 *
 * USAGE:
 *   $urlService = app(PublicUrlService::class);
 *
 *   // Invitation RSVP link (sent by email)
 *   $link = $urlService->rsvpUrl($company, $token);
 *
 *   // Public event registration page link
 *   $link = $urlService->publicEventUrl($company, $eventSlug);
 *
 *   // Digital pass / ticket link
 *   $link = $urlService->passUrl($company, $token);
 */
class PublicUrlService
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Generate the RSVP / invitation view link for a given token.
     */
    public function rsvpUrl(Company $company, string $token): string
    {
        return $this->buildUrl($company, "/rsvp/{$token}");
    }

    /**
     * Generate the invitation response link (older route alias).
     */
    public function inviteUrl(Company $company, string $token): string
    {
        return $this->buildUrl($company, "/invites/{$token}");
    }

    /**
     * Generate the public event registration page URL.
     */
    public function publicEventUrl(Company $company, string $eventSlug): string
    {
        return $this->buildUrl($company, "/events/{$eventSlug}");
    }

    /**
     * Generate the digital pass / ticket URL.
     */
    public function passUrl(Company $company, string $token): string
    {
        return $this->buildUrl($company, "/tickets/{$token}");
    }

    /**
     * Generate the PDF ticket download URL.
     */
    public function downloadPdfUrl(Company $company, string $token): string
    {
        return $this->buildUrl($company, "/downloadTicketsPdf/{$token}");
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Determine whether this company should use subdomain-prefixed public URLs.
     *
     * Conditions:
     *   1. The company has a non-empty `subdomain` field set.
     *   2. The `custom_subdomain` feature is enabled on their current plan.
     */
    public function usesSubdomain(Company $company): bool
    {
        if (empty($company->subdomain)) {
            return false;
        }

        return $this->subscriptionService->featureEnabled($company, 'custom_subdomain');
    }

    /**
     * Build the full public URL for a given path.
     *
     * When the company is eligible for subdomain routing, the URL is prefixed
     * with the tenant subdomain. Otherwise the configured app URL is used.
     */
    public function buildUrl(Company $company, string $path): string
    {
        if ($this->usesSubdomain($company)) {
            $baseDomain = config('tenancy.base_domain', parse_url(config('app.url'), PHP_URL_HOST));
            $scheme     = config('app.url') && str_starts_with(config('app.url'), 'https') ? 'https' : 'http';
            $base       = "{$scheme}://{$company->subdomain}.{$baseDomain}";
        } else {
            $base = rtrim(config('app.url'), '/');
        }

        return $base . '/' . ltrim($path, '/');
    }
}
