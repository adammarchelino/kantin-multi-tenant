<?php

namespace App\Policies;

use App\Models\TenantOrder;
use App\Models\User;
use App\Support\Tenancy\TenantContext;

class TenantOrderPolicy
{
    public function view(User $user, TenantOrder $order): bool
    {
        return app(TenantContext::class)->idOrNull() === (int) $order->tenant_id;
    }

    public function update(User $user, TenantOrder $order): bool
    {
        return app(TenantContext::class)->idOrNull() === (int) $order->tenant_id;
    }
}
