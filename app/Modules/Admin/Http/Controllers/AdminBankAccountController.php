<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantBankAccount;
use App\Modules\Admin\Http\Requests\VerifyBankAccountRequest;
use App\Modules\Admin\Services\ManageBankAccount;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class AdminBankAccountController extends Controller
{
    use AuthorizesRequests;

    public function update(VerifyBankAccountRequest $request, Tenant $tenant, TenantBankAccount $account, ManageBankAccount $service): RedirectResponse
    {
        abort_unless($account->tenant_id === $tenant->id, 404);
        $data = $request->validated();
        $service->verify($account, $data['status']);

        return back()->with('status', 'Status rekening diperbarui.');
    }
}
