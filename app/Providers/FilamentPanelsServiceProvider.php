<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FilamentPanelsServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (!class_exists(\Filament\PanelProvider::class)) {
            return;
        }

        $this->app->register(\App\Providers\Filament\SystemPanelProvider::class);
        $this->app->register(\App\Providers\Filament\OrganizerPanelProvider::class);
    }
}

