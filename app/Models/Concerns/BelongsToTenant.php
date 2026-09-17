<?php

namespace App\Models\Concerns;

use App\Support\Tenancy\TenantContext;
use Exception;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // 1. Global Scope: Menambahkan filter `where tenant_id = ...` pada query normal
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $context = app(TenantContext::class);

            if ($context->has()) {
                $builder->where(
                    $builder->qualifyColumn('tenant_id'),
                    $context->id()
                );
            } else {
                // Fail closed: mencegah diam-diam menjadi query lintas tenant jika context kosong
                $builder->whereRaw('1 = 0');
            }
        });

        // 2. Event Creating: Auto-fill tenant_id saat membuat data baru
        static::creating(function ($model): void {
            $context = app(TenantContext::class);

            if ($context->has()) {
                $model->tenant_id = $context->id();
            } else {
                // Menggagalkan operasi tulis ketika context belum terisi
                throw new Exception('TenantContext kosong! Tidak bisa menyimpan data tenant-owned.');
            }
        });
    }
}
