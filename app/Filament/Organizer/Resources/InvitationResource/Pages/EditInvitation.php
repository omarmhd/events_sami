<?php

namespace App\Filament\Organizer\Resources\InvitationResource\Pages;

use App\Filament\Organizer\Resources\InvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvitation extends EditRecord
{
    protected static string $resource = InvitationResource::class;

    protected static ?string $title = 'تعديل الدعوة';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('حذف'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
