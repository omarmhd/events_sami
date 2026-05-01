<?php

namespace App\Filament\Organizer\Resources\TicketResource\Pages;

use App\Filament\Organizer\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected static ?string $title = 'عرض التذكرة';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('تعديل'),
        ];
    }
}
