<?php

use App\Modules\Ordering\Http\Controllers\ResolveTableQrController;
use App\Support\Routing\PortalRoutes;
use Illuminate\Support\Facades\Route;

PortalRoutes::web(function (): void {
    Route::get('/q/{token}', ResolveTableQrController::class)->middleware('throttle:qr-scan')->name('customer.scan');
});
