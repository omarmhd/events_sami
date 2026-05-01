<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

class EventPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff', 'validator']);
    }

    public function view(User $user, Event $event): bool
    {
        return $this->sameOrganization($user, $event);
    }

    public function create(User $user): bool
    {
        return $user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff']);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->sameOrganization($user, $event)
            && ($user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff']));
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->sameOrganization($user, $event)
            && ($user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin']));
    }
}

