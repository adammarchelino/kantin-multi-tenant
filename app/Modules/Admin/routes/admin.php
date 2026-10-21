<?php

use App\Modules\Admin\Http\Controllers\AdminBankAccountController;
use App\Modules\Admin\Http\Controllers\AdminCommissionController;
use App\Modules\Admin\Http\Controllers\AdminTenantController;
use App\Modules\Admin\Http\Controllers\AdminTenantRoleController;
use App\Modules\Admin\Http\Controllers\AdminTenantStatusController;
use App\Support\Routing\PortalRoutes;
use Illuminate\Support\Facades\Route;

PortalRoutes::admin(function (): void {
    Route::get('/tenants', [AdminTenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [AdminTenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [AdminTenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}', [AdminTenantController::class, 'edit'])->name('tenants.edit');
    Route::post('/tenants/{tenant}/status', [AdminTenantStatusController::class, 'update'])->name('tenants.status');
    Route::post('/tenants/{tenant}/commission', [AdminCommissionController::class, 'store'])->name('tenants.commission.store');
    Route::post('/tenants/{tenant}/roles', [AdminTenantRoleController::class, 'store'])->name('tenants.roles.store');
    Route::delete('/tenants/{tenant}/roles/{user}', [AdminTenantRoleController::class, 'destroy'])->name('tenants.roles.destroy');
    Route::post('/tenants/{tenant}/bank-accounts/{account}', [AdminBankAccountController::class, 'update'])->name('tenants.bank-accounts.update');
});
