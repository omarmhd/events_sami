<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureCompanyContext;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OrganizerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('organizer')
            ->path(config('tenancy.organizer_admin_path', 'admin'))
            ->login()
            ->authGuard('web')
            ->discoverResources(in: app_path('Filament/Organizer/Resources'), for: 'App\\Filament\\Organizer\\Resources')
            ->discoverPages(in: app_path('Filament/Organizer/Pages'), for: 'App\\Filament\\Organizer\\Pages')
            ->discoverWidgets(in: app_path('Filament/Organizer/Widgets'), for: 'App\\Filament\\Organizer\\Widgets')
            ->widgets([
                \App\Filament\Organizer\Widgets\StatsOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureCompanyContext::class,
                \App\Http\Middleware\CheckSubscriptionStatus::class,
            ])
            ->brandName('منصة Maan Invite')
            ->logo(asset('assets/logo.png'))
            ->darkMode(false)
            ->font('Cairo')
            ->colors([
                'primary' => '#6366f1',
                'success' => '#10b981',
                'warning' => '#f59e0b',
                'danger' => '#ef4444',
            ]);
    }
}
