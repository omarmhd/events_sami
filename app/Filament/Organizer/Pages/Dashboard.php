<?php

namespace App\Filament\Organizer\Pages;

use App\Filament\Organizer\Widgets\StatsOverviewWidget;
use App\Filament\Organizer\Widgets\UpcomingEventsWidget;
use App\Filament\Organizer\Widgets\RecentTicketsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'لوحة التحكم';
    protected static ?string $navigationLabel = 'لوحة التحكم';

    public function getHeading(): string
    {
        return 'مرحباً بك في لوحة التحكم';
    }

    public function getSubHeading(): string | null
    {
        return 'إدارة أحداثك وتذاكرك بسهولة';
    }

    /**
     * @return array<class-string<\Filament\Widgets\Widget> | \Filament\Widgets\Widget>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }

    /**
     * @return array<int | string | \Filament\Widgets\Widget>
     */
    public function getWidgets(): array
    {
        return [
            UpcomingEventsWidget::class,
            RecentTicketsWidget::class,
        ];
    }

    /**
     * @return int | string | array<string, int | string>
     */
    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'lg' => 2,
        ];
    }
}
