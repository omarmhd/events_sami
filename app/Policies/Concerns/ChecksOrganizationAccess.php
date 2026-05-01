<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksOrganizationAccess
{
    protected function sameOrganization(User $user, $record): bool
    {
        if ($user->isSystemAdmin()) {
            return true;
        }

        $userOrganizationId = $user->organization_id ?: $user->company_id;
        $recordOrganizationId = $record->organization_id ?? $record->company_id ?? null;

        return $userOrganizationId !== null && (int) $userOrganizationId === (int) $recordOrganizationId;
    }

    protected function hasRole(User $user, array $roles): bool
    {
        return in_array($user->role, $roles, true);
    }
}

