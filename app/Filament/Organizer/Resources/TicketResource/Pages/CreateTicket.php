<?php

namespace App\Filament\Organizer\Resources\TicketResource\Pages;

use App\Filament\Organizer\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected static ?string $title = 'إنشاء تذكرة جديدة';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
