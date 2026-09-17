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
        // 1. Baca parameter dari route (misal URL-nya /kantin/{tenant:slug})
        // Jika pakai route model binding, $request->route('tenant') sudah berupa object Tenant.
        // Jika belum, kita query manual berdasarkan slug-nya.
        $tenantRoute = $request->route('tenant');
        $tenant = $tenantRoute instanceof Tenant ? $tenantRoute : Tenant::where('slug', $tenantRoute)->first();

        // 2. Tolak dengan 403 bila tenant tidak ditemukan atau berstatus nonaktif
        if (! $tenant || $tenant->status !== 'active') { // Sesuaikan nama kolom status dengan database kamu
            abort(403, 'Tenant tidak aktif atau tidak ditemukan.');
        }

        // 3. Periksa role pengguna pada tenant itu
        // (Sesuaikan metode pengecekan role ini dengan relasi user-tenant yang dibuat di Modul 3)
        $user = $request->user();
        if (! $user || ! $user->belongsToTenant($tenant)) {
            abort(403, 'Anda tidak memiliki akses ke tenant ini.');
        }

        // 4. Isi TenantContext sebelum meneruskan request
        $context = app(TenantContext::class);
        $context->set($tenant);

        // 5. Teruskan request, lalu wajib bersihkan (clear) context di blok finally
        try {
            return $next($request);
        } finally {
            $context->clear();
        }
    }
}
