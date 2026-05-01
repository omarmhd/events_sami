<?php

namespace App\Filament\Organizer\Resources\EventResource\Pages;

use App\Filament\Organizer\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $title = 'إنشاء حدث جديد';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
