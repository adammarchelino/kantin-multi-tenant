<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\UserTenantRole;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Resolves the tenant route, verifies the operator and clears request context. */
final class SetTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->route('tenant');

        if (! $tenant instanceof Tenant) {
            $tenant = Tenant::query()->where('slug', (string) $tenant)->first();
        }

        if (! $tenant instanceof Tenant) {
            abort(404);
        }

        if (! $tenant->isActive()) {
            abort(403);
        }

        $user = $request->user();

        if ($user === null || ! $user->hasRole('tenant')) {
            abort(403);
        }

        $isMember = UserTenantRole::query()
            ->where('user_id', $user->getKey())
            ->where('tenant_id', $tenant->getKey())
            ->exists();

        if (! $isMember) {
            abort(403);
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
