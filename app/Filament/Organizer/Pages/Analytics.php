<?php

namespace App\Filament\Organizer\Pages;

use App\Models\Company;
use App\Services\EventAnalyticsService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Analytics extends Page
{
    protected static ?string $title = 'الإحصائيات';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'الإحصائيات';
    protected static string $view = 'filament.pages.analytics';

    public ?Company $company = null;
    public array $stats = [];
    public array $chartData = [];

    public function mount(): void
    {
        $this->company = Auth::user()->company;
        $analyticsService = app(EventAnalyticsService::class);
        $this->stats = $analyticsService->getCompanyDashboardStats($this->company);
    }

    public function getHeading(): string
    {
        return 'إحصائيات شاملة لفعالياتك';
    }

    public function getSubHeading(): string | null
    {
        return 'عرض تفصيلي لأداء جميع الفعاليات والدعوات';
    }
}
