<?php

namespace App\Providers;

use App\Services\PublicUrlService;
use App\Services\SubscriptionService;
use App\Services\TenantBrandingService;
use App\Tenancy\TenantContext;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ensure MyCLabs\Enum is autoloaded — it's a dependency of phpoffice/phpspreadsheet
        // but may not be registered in the Composer autoloader on some environments.
        spl_autoload_register(function (string $class): void {
            if (str_starts_with($class, 'MyCLabs\\Enum\\')) {
                $relative = str_replace(['MyCLabs\\Enum\\', '\\'], ['', '/'], $class);
                $file = base_path('vendor/myclabs/php-enum/src/' . $relative . '.php');
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }, prepend: true);

        // Tenant context singleton — shared across every request.
        $this->app->singleton(TenantContext::class, function () {
            return new TenantContext();
        });

        // TenantBrandingService singleton — resolves per-tenant branding once per request.
        $this->app->singleton(TenantBrandingService::class, function ($app) {
            return new TenantBrandingService();
        });

        // PublicUrlService singleton — generates public-facing URLs with plan-gated
        // subdomain support. Inject this wherever invitation/RSVP links are built.
        $this->app->singleton(PublicUrlService::class, function ($app) {
            return new PublicUrlService($app->make(SubscriptionService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // NOTE: Platform UI chrome (sidebar, mobile header, auth pages, system layout)
        // always reads the logo/name from SystemSetting (admin panel) — not from tenant
        // CompanyBranding. CompanyBranding is ONLY used for per-tenant email templates.
        // TenantBrandingService is therefore NOT shared globally; it is called explicitly
        // by EmailSettingsController and EmailTemplateService where needed.
    }
}
