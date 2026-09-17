<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;

class TenantContext
{
    // Menyimpan state tenant aktif
    protected ?Tenant $tenant = null;

    // Mengisi context dengan tenant aktif
    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    // Membersihkan context setelah request/job selesai
    public function clear(): void
    {
        $this->tenant = null;
    }

    // Mengecek apakah ada tenant aktif di context
    public function has(): bool
    {
        return $this->tenant !== null;
    }

    // Mengembalikan object model Tenant aktif
    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }

    // Mengembalikan ID tenant aktif
    public function id()
    {
        return $this->tenant?->id;
    }
}
