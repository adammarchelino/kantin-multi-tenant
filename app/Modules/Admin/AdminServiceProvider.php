<?php

namespace App\Modules\Admin;

use App\Modules\ModuleServiceProvider;

final class AdminServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        // Binding layanan modul Admin ditambahkan pada tahap implementasi.
    }

    protected function moduleAlias(): string
    {
        return 'admin';
    }
}
