<?php

namespace App\Modules\Catalog\Services;

use App\Models\Canteen;
use App\Models\Tenant;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final class TenantOpeningHours
{
    public function activeTenants(Canteen $canteen): Collection
    {
        return Tenant::where('canteen_id', $canteen->id)->where('status', 'active')
            ->with(['operatingHours' => fn ($query) => $query->withoutGlobalScope('tenant')])
            ->orderBy('display_name')->get();
    }

    public function isOpen(Tenant $tenant, ?CarbonInterface $at = null): bool
    {
        $hours = $this->hours($tenant);
        if ($hours->isEmpty()) {
            return true;
        }
        $local = ($at ?? now())->copy()->setTimezone('Asia/Jakarta');
        $today = $hours->firstWhere('day_of_week', $local->dayOfWeek);
        $time = $local->format('H:i:s');
        if ($today && $this->contains($time, $today->opens_at, $today->closes_at)) {
            return true;
        }
        // Rentang yang melewati tengah malam berlanjut dari hari sebelumnya.
        $previous = $hours->firstWhere('day_of_week', ($local->dayOfWeek + 6) % 7);

        return $previous && $previous->opens_at > $previous->closes_at && $time < $previous->closes_at;
    }

    public function canteenIsOpen(Canteen $canteen): bool
    {
        $tenants = $this->activeTenants($canteen);
        if ($tenants->isEmpty()) {
            return true;
        }
        foreach ($tenants as $tenant) {
            if ($this->isOpen($tenant)) {
                return true;
            }
        }

        return false;
    }

    public function todayLabel(Tenant $tenant): ?string
    {
        $row = $this->hours($tenant)->firstWhere('day_of_week', now('Asia/Jakarta')->dayOfWeek);

        return $row ? $this->fmt($row->opens_at).'–'.$this->fmt($row->closes_at) : null;
    }

    public function opensAtToday(Tenant $tenant): ?string
    {
        $row = $this->hours($tenant)->firstWhere('day_of_week', now('Asia/Jakarta')->dayOfWeek);

        return $row ? $this->fmt($row->opens_at) : null;
    }

    private function hours(Tenant $tenant): Collection
    {
        return $tenant->relationLoaded('operatingHours') ? $tenant->operatingHours : $tenant->operatingHours()->withoutGlobalScope('tenant')->get();
    }

    private function contains(string $time, string $open, string $close): bool
    {
        return $open <= $close ? $time >= $open && $time < $close : $time >= $open;
    }

    private function fmt(string $value): string
    {
        return str_replace(':', '.', substr($value, 0, 5));
    }
}
