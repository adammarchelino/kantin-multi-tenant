<?php

namespace App\Support\Routing;

use Closure;
use Illuminate\Support\Facades\Route;

/** Defines the shared URL, name, and middleware contract for each portal. */
final class PortalRoutes
{
    public static function customer(Closure|string $routes): void
    {
        Route::middleware('web')
            ->prefix('kantin/{canteen}')
            ->name('customer.')
            ->group($routes);
    }

    public static function tenant(Closure|string $routes): void
    {
        Route::middleware(['web', 'auth', 'verified', 'tenant'])
            ->prefix('tenant/{tenant:slug}')
            ->name('tenant.')
            ->scopeBindings()
            ->group($routes);
    }

    public static function admin(Closure|string $routes): void
    {
        Route::middleware(['web', 'auth', 'verified', 'role:admin'])
            ->prefix('admin')
            ->name('admin.')
            ->group($routes);
    }
}
