<?php

namespace App\Filament\Organizer\Resources\EventResource\Pages;

use App\Filament\Organizer\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected static ?string $title = 'الأحداث';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء حدث جديد'),
        ];
    }
}
