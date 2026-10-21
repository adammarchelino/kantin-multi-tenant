<?php

namespace App\Jobs;

use App\Models\Menu;
use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;

/** Example tenant job: carry an identifier and rebuild context inside handle(). */
class CountTenantMenus implements ShouldQueue
{
    use Queueable;

    public ?int $result = null;

    public function __construct(public int $tenantId) {}

    public function handle(TenantContext $context): void
    {
        $context->clear();

        try {
            $tenant = Tenant::query()->findOrFail($this->tenantId);

            if (! $tenant->isActive()) {
                throw new RuntimeException('Tenant job tidak aktif.');
            }

            $context->set($tenant);
            $this->result = Menu::query()->count();
        } finally {
            $context->clear();
        }
    }
}
