<?php

namespace App\Filament\Organizer\Widgets;

use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $company = $user->company;

        $eventsCount = Event::where('company_id', $company->id)->count();
        $invitationsCount = EventInvitation::where('company_id', $company->id)->count();
        $ticketsCount = Ticket::where('company_id', $company->id)->count();

        $acceptedInvitations = EventInvitation::where('company_id', $company->id)
            ->where('status', 'accepted')
            ->count();

        return [
            Stat::make('الأحداث', $eventsCount)
                ->description('إجمالي الأحداث المنظمة')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->chart($this->getEventsChart()),

            Stat::make('الدعوات', $invitationsCount)
                ->description('إجمالي الدعوات المرسلة')
                ->descriptionIcon('heroicon-m-inbox')
                ->color('info'),

            Stat::make('التذاكر', $ticketsCount)
                ->description('إجمالي التذاكر المصدرة')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning')
                ->chart($this->getTicketsChart()),

            Stat::make('المقبولة', $acceptedInvitations)
                ->description('الدعوات المقبولة')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }

    private function getEventsChart(): array
    {
        $user = Auth::user();
        $company = $user->company;

        $data = Event::where('company_id', $company->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('count', 'month')
            ->toArray();

        return array_values($data);
    }

    private function getTicketsChart(): array
    {
        $user = Auth::user();
        $company = $user->company;

        $data = Ticket::where('company_id', $company->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('count', 'month')
            ->toArray();

        return array_values($data);
    }
}
