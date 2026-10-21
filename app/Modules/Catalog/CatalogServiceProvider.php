<?php

namespace App\Modules\Catalog;

use App\Modules\ModuleServiceProvider;

final class CatalogServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        // Binding layanan modul Catalog ditambahkan pada tahap implementasi.
    }

    protected function moduleAlias(): string
    {
        return 'catalog';
    }
}
