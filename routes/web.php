<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::prefix('tenant/{tenant:slug}')
        ->scopeBindings()
        ->name('tenant.')
        ->group(base_path('routes/tenant.php'));

    Route::prefix('admin')
        ->name('admin.')
        ->group(base_path('routes/admin.php'));
});

Route::prefix('kantin/{canteen:slug}')
    ->name('customer.')
    ->group(base_path('routes/customer.php'));