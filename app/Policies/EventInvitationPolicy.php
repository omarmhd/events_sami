<?php

namespace App\Policies;

use App\Models\EventInvitation;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

class EventInvitationPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff', 'validator']);
    }

    public function view(User $user, EventInvitation $invitation): bool
    {
        return $this->sameOrganization($user, $invitation);
    }

    public function create(User $user): bool
    {
        return $user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff']);
    }

    public function update(User $user, EventInvitation $invitation): bool
    {
        return $this->sameOrganization($user, $invitation)
            && ($user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff']));
    }

    public function delete(User $user, EventInvitation $invitation): bool
    {
        return $this->sameOrganization($user, $invitation)
            && ($user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin']));
    }
}

