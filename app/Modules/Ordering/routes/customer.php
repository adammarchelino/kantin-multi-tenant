<?php

use App\Modules\Ordering\Http\Controllers\CustomerWelcomeController;
use App\Support\Routing\PortalRoutes;
use Illuminate\Support\Facades\Route;

PortalRoutes::customer(function (): void {
    Route::get('/meja', CustomerWelcomeController::class)->name('welcome');
});
