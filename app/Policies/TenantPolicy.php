<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

class TenantPolicy
{
    /** @param list<string> $roles */
    private function hasCanteenRole(User $user, array $roles, ?Tenant $tenant = null): bool
    {
        if (! $user->isActive() || ! $user->isAdmin()) {
            return false;
        }

        return $user->canteenRoles()->whereIn('role', $roles)
            ->when($tenant, fn ($q) => $q->where('canteen_id', $tenant->canteen_id))
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $this->hasCanteenRole($user, ['owner', 'manager', 'finance', 'viewer']);
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $this->hasCanteenRole($user, ['owner', 'manager', 'finance', 'viewer'], $tenant);
    }

    public function create(User $user): bool
    {
        return $this->hasCanteenRole($user, ['owner', 'manager']);
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $this->hasCanteenRole($user, ['owner', 'manager'], $tenant);
    }

    public function assignRole(User $user, Tenant $tenant): bool
    {
        return $this->hasCanteenRole($user, ['owner', 'manager'], $tenant);
    }

    public function scheduleCommission(User $user, Tenant $tenant): bool
    {
        return $this->hasCanteenRole($user, ['owner', 'manager', 'finance'], $tenant);
    }

    public function manageBank(User $user, Tenant $tenant): bool
    {
        return $this->hasCanteenRole($user, ['owner', 'manager', 'finance'], $tenant);
    }
}
