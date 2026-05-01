<?php

namespace App\Filament\Organizer\Resources\InvitationResource\Pages;

use App\Filament\Organizer\Resources\InvitationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvitation extends CreateRecord
{
    protected static string $resource = InvitationResource::class;

    protected static ?string $title = 'إرسال دعوة جديدة';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
