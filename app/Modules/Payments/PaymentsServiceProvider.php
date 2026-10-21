<?php

namespace App\Modules\Payments;

use App\Modules\ModuleServiceProvider;

final class PaymentsServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        // Binding layanan modul Payments ditambahkan pada tahap implementasi.
    }

    protected function moduleAlias(): string
    {
        return 'payments';
    }
}
