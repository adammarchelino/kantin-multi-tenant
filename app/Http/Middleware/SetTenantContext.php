<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantRoute = $request->route('tenant');
        $tenant = $tenantRoute instanceof Tenant
            ? $tenantRoute
            : Tenant::query()->where('slug', $tenantRoute)->first();

        if (! $tenant || $tenant->status !== 'active') {
            abort(403, 'Tenant tidak aktif atau tidak ditemukan.');
        }

        $user = $request->user();
        if (! $user || ! $user->belongsToTenant($tenant)) {
            abort(403, 'Anda tidak memiliki akses ke tenant ini.');
        }

        $context = app(TenantContext::class);
        $context->set($tenant);

        try {
            return $next($request);
        } finally {
            $context->clear();
        }
    }
}
