<?php

namespace App\Modules\Admin\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

final class ChangeTenantStatus
{
    public function __construct(private AuditLogger $audit) {}

    public function handle(Tenant $tenant, string $status): void
    {
        DB::transaction(function () use ($tenant, $status): void {
            $before = ['status' => $tenant->status];
            $tenant->forceFill(['status' => $status])->save();
            $this->audit->record('tenant', $tenant->id, $status === 'inactive' ? 'deactivated' : 'activated', $before,
                ['status' => $status], $tenant->id, $tenant->canteen_id);
        });
    }
}
