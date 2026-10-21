<?php

namespace App\Modules\Ordering;

use App\Modules\ModuleServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

final class OrderingServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        // Binding layanan modul Ordering ditambahkan pada tahap implementasi.
    }

    public function boot(): void
    {
        parent::boot();
        RateLimiter::for('qr-scan', fn (Request $request): Limit => Limit::perMinute(30)->by($request->ip() ?? 'unknown'));
    }

    protected function moduleAlias(): string
    {
        return 'ordering';
    }
}
