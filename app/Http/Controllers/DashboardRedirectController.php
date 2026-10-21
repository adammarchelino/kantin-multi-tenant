<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class DashboardRedirectController extends Controller
{
    public function __invoke(): RedirectResponse|Response
    {
        $user = request()->user();
        abort_unless($user !== null, 401);
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isTenantOperator()) {
            $membership = $user->tenantRoles()->whereIn('role', ['owner', 'manager', 'operator', 'cashier', 'finance', 'viewer'])
                ->whereHas('tenant', fn ($query) => $query->where('status', 'active'))->first();
            abort_unless($membership, 403);

            return redirect()->route('tenant.dashboard', $membership->tenant?->slug);
        }

        return response()->view('dashboard');
    }
}
