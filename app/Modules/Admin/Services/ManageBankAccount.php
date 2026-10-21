<?php

namespace App\Modules\Admin\Services;

use App\Models\TenantBankAccount;
use Illuminate\Support\Facades\DB;

final class ManageBankAccount
{
    public function __construct(private AuditLogger $audit) {}

    public function verify(TenantBankAccount $account, string $status): void
    {
        DB::transaction(function () use ($account, $status): void {
            $account->forceFill(['status' => $status, 'verified_by' => auth()->id()])->save();
            $this->audit->record('tenant_bank_account', $account->id, 'verification_'.$status,
                null, ['status' => $status, 'account_last4' => $account->account_last4], $account->tenant_id, $account->tenant->canteen_id);
        });
    }

    public function makePrimary(TenantBankAccount $account): void
    {
        DB::transaction(function () use ($account): void {
            TenantBankAccount::where('tenant_id', $account->tenant_id)->update(['is_primary' => false]);
            $account->forceFill(['is_primary' => true])->save();
            $this->audit->record('tenant_bank_account', $account->id, 'made_primary', null,
                ['account_last4' => $account->account_last4], $account->tenant_id, $account->tenant->canteen_id);
        });
    }
}
