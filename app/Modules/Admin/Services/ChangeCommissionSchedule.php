<?php

namespace App\Modules\Admin\Services;

use App\Models\CommissionScheme;
use App\Models\Tenant;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

final class ChangeCommissionSchedule
{
    public function __construct(private AuditLogger $audit) {}

    public function handle(Tenant $tenant, float $percent, CarbonInterface $effectiveAt): CommissionScheme
    {
        return DB::transaction(function () use ($tenant, $percent, $effectiveAt): CommissionScheme {
            $active = CommissionScheme::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)
                ->whereNull('valid_to')->lockForUpdate()->first();
            if ($active && $effectiveAt->lessThanOrEqualTo($active->valid_from)) {
                throw new DomainException('Tanggal berlaku harus setelah komisi aktif dimulai.');
            }
            $before = $active?->only(['commission_rate', 'valid_from', 'valid_to']);
            $active?->forceFill(['valid_to' => $effectiveAt])->save();
            $new = (new CommissionScheme)->forceFill([
                'tenant_id' => $tenant->id, 'commission_rate' => $percent / 100,
                'valid_from' => $effectiveAt, 'valid_to' => null,
            ]);
            $new->save();
            $this->audit->record('commission_scheme', $new->id, 'scheduled', $before,
                $new->only(['commission_rate', 'valid_from']), $tenant->id, $tenant->canteen_id);

            return $new;
        });
    }
}
