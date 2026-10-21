<?php

namespace App\Modules\Catalog\Services;

use App\Models\Canteen;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/** Explicit, reviewable cross-tenant query for one public canteen catalog. */
final class PublicCatalogQuery
{
    /** @return Collection<int, Menu> */
    public function forCanteen(Canteen $canteen): Collection
    {
        return Menu::query()
            ->withoutGlobalScope('tenant')
            ->where('is_available', true)
            ->whereHas('tenant', function (Builder $query) use ($canteen): void {
                $query->where('canteen_id', $canteen->getKey())
                    ->where('status', 'active')
                    ->whereHas('canteen', fn (Builder $canteenQuery) => $canteenQuery->where('status', 'active'));
            })
            ->orderBy('name')
            ->get();
    }
}
