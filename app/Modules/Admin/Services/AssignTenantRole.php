<?php

namespace App\Modules\Admin\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenantRole;
use DomainException;
use Illuminate\Support\Facades\DB;

final class AssignTenantRole
{
    public function __construct(private AuditLogger $audit) {}

    public function assign(Tenant $tenant, User $user, string $role): UserTenantRole
    {
        return DB::transaction(function () use ($tenant, $user, $role): UserTenantRole {
            $membership = UserTenantRole::firstOrCreate(['user_id' => $user->id, 'tenant_id' => $tenant->id, 'role' => $role]);
            $this->audit->record('user_tenant_role', $membership->id, 'assigned', null,
                ['user_id' => $user->id, 'role' => $role], $tenant->id, $tenant->canteen_id);

            return $membership;
        });
    }

    public function remove(Tenant $tenant, User $user, string $role): void
    {
        DB::transaction(function () use ($tenant, $user, $role): void {
            $owners = UserTenantRole::where('tenant_id', $tenant->id)->where('role', 'owner')->lockForUpdate()->count();
            $membership = UserTenantRole::where(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'role' => $role])->first();
            if ($membership && $role === 'owner' && $owners <= 1) {
                throw new DomainException('Owner terakhir tidak dapat dicabut.');
            }
            $membership?->delete();
            $this->audit->record('user_tenant_role', $membership?->id, 'removed', ['user_id' => $user->id, 'role' => $role], null, $tenant->id, $tenant->canteen_id);
        });
    }
}
