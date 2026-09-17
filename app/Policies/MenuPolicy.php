<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;
use App\Support\Tenancy\TenantContext;

class MenuPolicy
{
    // Fungsi bantuan untuk mengecek apakah menu ini milik tenant yang sedang diakses
    protected function isMenuOwnedByActiveTenant(Menu $menu): bool
    {
        $context = app(TenantContext::class);

        return $context->has() && $menu->tenant_id === $context->id();
    }

    public function view(User $user, Menu $menu): bool
    {
        return $this->isMenuOwnedByActiveTenant($menu);
    }

    public function update(User $user, Menu $menu): bool
    {
        return $this->isMenuOwnedByActiveTenant($menu);
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $this->isMenuOwnedByActiveTenant($menu);
    }
}
