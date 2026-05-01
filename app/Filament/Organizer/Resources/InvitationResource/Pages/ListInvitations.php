<?php

namespace App\Filament\Organizer\Resources\InvitationResource\Pages;

use App\Filament\Organizer\Resources\InvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationResource::class;

    protected static ?string $title = 'الدعوات';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إرسال دعوة جديدة'),
        ];
    }
}
