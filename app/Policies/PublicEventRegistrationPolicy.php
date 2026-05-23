<?php

namespace App\Policies;

use App\Models\PublicEventRegistration;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

class PublicEventRegistrationPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff', 'validator']);
    }

    public function view(User $user, PublicEventRegistration $registration): bool
    {
        return $this->sameOrganization($user, $registration);
    }

    public function update(User $user, PublicEventRegistration $registration): bool
    {
        return $this->sameOrganization($user, $registration)
            && ($user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff']));
    }

    public function delete(User $user, PublicEventRegistration $registration): bool
    {
        return $this->sameOrganization($user, $registration)
            && ($user->isSystemAdmin() || $this->hasRole($user, ['organizer_owner', 'organizer_admin', 'staff']));
    }
}

