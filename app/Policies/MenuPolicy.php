<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;
use App\Support\Tenancy\TenantContext;

class MenuPolicy
{
    private function ownedByActiveTenant(Menu $menu): bool
    {
        return app(TenantContext::class)->idOrNull() === (int) $menu->tenant_id;
    }

    public function view(User $user, Menu $menu): bool
    {
        return $this->ownedByActiveTenant($menu);
    }

    public function update(User $user, Menu $menu): bool
    {
        return $this->ownedByActiveTenant($menu);
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $this->ownedByActiveTenant($menu);
    }
}
