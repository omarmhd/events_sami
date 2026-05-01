<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

class TicketPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff', 'validator']);
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $this->sameOrganization($user, $ticket);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $this->sameOrganization($user, $ticket)
            && ($user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff', 'validator']));
    }
}

