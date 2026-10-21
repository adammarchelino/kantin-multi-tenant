<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use RuntimeException;

/** Holds the active tenant for one request or queue-job lifecycle. */
final class TenantContext
{
    private ?Tenant $tenant = null;

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }

    public function tenant(): Tenant
    {
        return $this->tenant ?? throw new RuntimeException('TenantContext belum diisi.');
    }

    public function id(): int
    {
        return (int) $this->tenant()->getKey();
    }

    public function idOrNull(): ?int
    {
        return $this->tenant === null ? null : (int) $this->tenant->getKey();
    }
}
