<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Modules\Admin\Http\Requests\AssignTenantRoleRequest;
use App\Modules\Admin\Services\AssignTenantRole;
use DomainException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminTenantRoleController extends Controller
{
    use AuthorizesRequests;

    public function store(AssignTenantRoleRequest $request, Tenant $tenant, AssignTenantRole $service): RedirectResponse
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->firstOrFail();
        $service->assign($tenant, $user, $data['role']);

        return back()->with('status', 'Role tenant ditugaskan.');
    }

    public function destroy(Request $request, Tenant $tenant, User $user, AssignTenantRole $service): RedirectResponse
    {
        $this->authorize('assignRole', $tenant);
        $data = $request->validate(['role' => ['required', 'in:owner,manager,operator,cashier,viewer,finance']]);
        try {
            $service->remove($tenant, $user, $data['role']);
        } catch (DomainException $exception) {
            return back()->withErrors(['role' => $exception->getMessage()]);
        }

        return back()->with('status', 'Role dicabut.');
    }
}
