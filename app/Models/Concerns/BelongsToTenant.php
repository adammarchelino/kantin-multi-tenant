<?php

namespace App\Models\Concerns;

use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Adds tenant filtering and assigns ownership from the active context on create. */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $context = app(TenantContext::class);

            if ($context->has()) {
                $builder->where($builder->getModel()->qualifyColumn('tenant_id'), $context->id());
            }
        });

        static::creating(function (Model $model): void {
            $context = app(TenantContext::class);

            if ($context->has()) {
                // Context wins over any tenant_id supplied by application code.
                $model->setAttribute('tenant_id', $context->id());
            }
        });
    }
}
