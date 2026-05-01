<?php

namespace App\Filament\Organizer\Resources\InvitationResource\Pages;

use App\Filament\Organizer\Resources\InvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvitation extends ViewRecord
{
    protected static string $resource = InvitationResource::class;

    protected static ?string $title = 'عرض الدعوة';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('تعديل'),
        ];
    }
}
