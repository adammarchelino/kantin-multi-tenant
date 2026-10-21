<?php

namespace App\Modules\Kitchen;

use App\Modules\ModuleServiceProvider;

final class KitchenServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        // Binding layanan modul Kitchen ditambahkan pada tahap implementasi.
    }

    protected function moduleAlias(): string
    {
        return 'kitchen';
    }
}
