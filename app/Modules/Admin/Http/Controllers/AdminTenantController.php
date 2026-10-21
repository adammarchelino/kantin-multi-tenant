<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Canteen;
use App\Models\CommissionScheme;
use App\Models\Tenant;
use App\Models\UserCanteenRole;
use App\Modules\Admin\Http\Requests\StoreTenantRequest;
use App\Modules\Admin\Services\CreateTenant;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminTenantController extends Controller
{
    use AuthorizesRequests;

    private function canteen(): Canteen
    {
        $membership = UserCanteenRole::where('user_id', auth()->id())->where('role', 'manager')->firstOrFail();

        return Canteen::findOrFail($membership->canteen_id);
    }

    public function index(): View
    {
        $this->authorize('viewAny', Tenant::class);
        $tenants = Tenant::withoutGlobalScope('tenant')->where('canteen_id', $this->canteen()->id)
            ->with(['tenantRoles.user', 'bankAccounts', 'commissionSchemes' => fn ($q) => $q->whereNull('valid_to')])
            ->orderBy('display_name')->get();

        return view('admin::tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        $this->authorize('create', Tenant::class);

        return view('admin::tenants.create');
    }

    public function store(StoreTenantRequest $request, CreateTenant $service): RedirectResponse
    {
        $canteen = $this->canteen();
        $data = $request->validated();
        $request->validate([
            'code' => ['unique:tenants,code,NULL,id,canteen_id,'.$canteen->id],
            'slug' => ['unique:tenants,slug,NULL,id,canteen_id,'.$canteen->id],
        ]);
        $service->handle($canteen, $data);

        return redirect()->route('admin.tenants.index')->with('status', 'Tenant berhasil didaftarkan. Tautan atur kata sandi dikirim ke email penanggung jawab.');
    }

    public function edit(Tenant $tenant): View
    {
        $this->authorize('view', $tenant);
        app(TenantContext::class)->clear();
        $tenant->load(['tenantRoles.user', 'bankAccounts']);
        $commissions = CommissionScheme::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->orderByDesc('valid_from')->get();
        $audit = AuditLog::with('actor')->where('entity', 'commission_scheme')->where('tenant_id', $tenant->id)->latest('logged_at')->get();

        return view('admin::tenants.edit', compact('tenant', 'commissions', 'audit'));
    }
}
