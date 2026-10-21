<?php

namespace App\Modules\Admin\Services;

use App\Models\Canteen;
use App\Models\CommissionScheme;
use App\Models\Tenant;
use App\Models\TenantBalance;
use App\Models\TenantBankAccount;
use App\Models\User;
use App\Models\UserTenantRole;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateTenant
{
    public function __construct(private AuditLogger $audit) {}

    public function handle(Canteen $canteen, array $data): Tenant
    {
        if (User::query()->where('email', $data['pic_email'])->exists()) {
            throw ValidationException::withMessages(['pic_email' => 'Surel penanggung jawab sudah terdaftar.']);
        }

        [$tenant, $owner] = DB::transaction(function () use ($canteen, $data): array {
            $tenant = (new Tenant)->forceFill([
                'canteen_id' => $canteen->id, 'display_name' => $data['display_name'],
                'code' => $data['code'], 'slug' => $data['slug'], 'status' => 'active',
            ]);
            $tenant->save();
            $owner = (new User)->forceFill([
                'name' => $data['pic_name'], 'email' => $data['pic_email'],
                'password' => Str::random(48), 'role' => 'tenant', 'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $owner->save();
            UserTenantRole::create(['user_id' => $owner->id, 'tenant_id' => $tenant->id, 'role' => 'owner']);
            (new TenantBalance)->forceFill(['tenant_id' => $tenant->id, 'available_amount' => 0, 'held_amount' => 0])->save();
            (new CommissionScheme)->forceFill([
                'tenant_id' => $tenant->id, 'commission_rate' => $data['commission_rate'] / 100,
                'valid_from' => now(), 'valid_to' => null,
            ])->save();
            $number = preg_replace('/\s+/', '', $data['account_number']);
            (new TenantBankAccount)->forceFill([
                'tenant_id' => $tenant->id, 'account_number_cipher' => $number,
                'account_last4' => substr($number, -4), 'bank_code' => $data['bank_code'],
                'account_holder' => $data['account_holder'], 'status' => 'unverified', 'is_primary' => true,
            ])->save();
            $this->audit->record('tenant', $tenant->id, 'created', null, [
                'display_name' => $tenant->display_name, 'code' => $tenant->code,
                'owner_email' => $owner->email, 'bank_code' => $data['bank_code'],
                'account_last4' => substr($number, -4), 'commission_rate' => $data['commission_rate'],
            ], $tenant->id, $canteen->id);

            return [$tenant, $owner];
        });

        $owner->notify(new ResetPassword(Password::createToken($owner)));

        return $tenant;
    }
}
