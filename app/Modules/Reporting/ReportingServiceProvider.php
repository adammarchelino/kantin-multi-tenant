<?php

namespace App\Modules\Reporting;

use App\Modules\ModuleServiceProvider;

final class ReportingServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        // Binding layanan modul Reporting ditambahkan pada tahap implementasi.
    }

    protected function moduleAlias(): string
    {
        return 'reporting';
    }
}
