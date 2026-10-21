<?php

namespace App\Modules\Ordering;

use App\Modules\ModuleServiceProvider;

final class OrderingServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        // Binding layanan modul Ordering ditambahkan pada tahap implementasi.
    }

    protected function moduleAlias(): string
    {
        return 'ordering';
    }
}
