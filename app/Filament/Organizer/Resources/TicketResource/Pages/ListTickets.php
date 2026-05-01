<?php

namespace App\Filament\Organizer\Resources\TicketResource\Pages;

use App\Filament\Organizer\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected static ?string $title = 'التذاكر';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء تذكرة جديدة'),
        ];
    }
}
