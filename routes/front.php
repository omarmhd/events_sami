<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|──────────────────────────────────────────────────────────────────────────────
| Front / Public Routes
|──────────────────────────────────────────────────────────────────────────────
| These routes are publicly accessible (no auth required). They serve:
|   • Invitation RSVP pages
|   • Public event registration forms
|   • Digital pass / ticket views
|   • PDF ticket downloads
|
| Subdomain routing (plan-gated):
|   When a company has the `custom_subdomain` feature enabled on their plan
|   and a non-empty `subdomain` field, all public links are generated with
|   their subdomain prefix (e.g. https://acme.maaninvite.com/rsvp/{token}).
|
|   The `public.tenant` middleware (ResolvePublicTenant) enforces this:
|   if a request arrives on a subdomain whose company does NOT have the
|   feature enabled, it redirects to the canonical main-domain URL.
|
|   Generating these URLs: use PublicUrlService (app/Services/PublicUrlService.php)
|   from any controller or email service — never hardcode URLs directly.
|
| ResolveTenantFromSubdomain (global, registered in Kernel.php middlewareGroups)
| already runs before these routes, so $tenantContext is always populated.
|──────────────────────────────────────────────────────────────────────────────
*/

// ── Internal / non-subdomain front pages ──────────────────────────────────
// Landing page and misc front pages run on the main domain only.
Route::name('front.')->group(function () {
    Route::get('/front',                [HomeController::class, 'landingPage'])   ->name('landing');
    Route::get('/front/events',         [HomeController::class, 'eventListPage']) ->name('events.index');
    Route::get('/front/events/response',[HomeController::class, 'eventResponsePage'])->name('events.response');
});

// ── Public routes (subdomain-aware) ───────────────────────────────────────
// All routes below support tenant-subdomain access. The `public.tenant`
// middleware validates the plan feature and redirects if not entitled.
Route::middleware(['public.tenant'])->group(function () {

    // ── Invitation / RSVP ─────────────────────────────────────────────────
    // Accessible on main domain:   https://maaninvite.com/rsvp/{token}
    // Accessible on subdomain:     https://acme.maaninvite.com/rsvp/{token}
    Route::get( '/rsvp/{token}',          [HomeController::class, 'showByToken'])->name('rsvp.show');
    Route::post('/rsvp/{token}/submit',   [HomeController::class, 'submit'])     ->name('rsvp.submit');

    // Legacy route alias (same handlers)
    Route::get( '/invites/{token}',       [HomeController::class, 'showByToken'])->name('invites.show');
    Route::post('/invites/{token}/respond',[HomeController::class, 'submit'])    ->name('invites.respond');

    // ── PDF ticket download ────────────────────────────────────────────────
    Route::get('/downloadTicketsPdf/{token}', [HomeController::class, 'downloadPdf'])->name('downloadPdf');

    // ── Public event registration ──────────────────────────────────────────
    // Accessible on main domain:   https://maaninvite.com/public/events/{slug}
    // Accessible on subdomain:     https://acme.maaninvite.com/public/events/{slug}
    Route::get( '/public/events/{eventSlug}', [HomeController::class, 'publicForm'])      ->name('public.events.register');
    Route::post('/public/events/{eventSlug}', [HomeController::class, 'submitPublicForm'])->name('public.events.submit');

    // Digital pass / ticket
    Route::get('/public/pass/{token}', [HomeController::class, 'showPass'])->name('public.pass.show');

    // Short-form event registration URLs (no /public/ prefix)
    Route::get('/events/{eventSlug}', [HomeController::class, 'publicForm'])
        ->where('eventSlug', '^(?!create$)(?!edit$)[A-Za-z0-9_-]+$')
        ->name('events.public.show');

    Route::post('/events/{eventSlug}/register', [HomeController::class, 'submitPublicForm'])
        ->where('eventSlug', '^(?!create$)(?!edit$)[A-Za-z0-9_-]+$')
        ->name('events.public.register');

    // Digital ticket viewer (short URL)
    Route::get('/tickets/{token}', [HomeController::class, 'showPass'])->name('tickets.show');
});
