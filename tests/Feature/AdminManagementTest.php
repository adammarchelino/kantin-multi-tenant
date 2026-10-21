<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Canteen;
use App\Models\CommissionScheme;
use App\Models\Tenant;
use App\Models\TenantBalance;
use App\Models\TenantBankAccount;
use App\Models\User;
use App\Models\UserCanteenRole;
use App\Models\UserTenantRole;
use App\Modules\Admin\Services\AssignTenantRole;
use App\Modules\Admin\Services\ChangeCommissionSchedule;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private function manager(Canteen $canteen): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        UserCanteenRole::create(['user_id' => $user->id, 'canteen_id' => $canteen->id, 'role' => 'manager']);

        return $user;
    }

    public function test_only_a_canteen_manager_can_open_tenant_administration(): void
    {
        $canteen = Canteen::factory()->create();
        $manager = $this->manager($canteen);
        $this->assertSame('admin', $manager->role);
        $this->assertSame('active', $manager->status);
        $this->assertTrue($manager->isActive());
        $this->assertSame(1, $manager->canteenRoles()->count());
        $this->actingAs($manager)->get(route('admin.tenants.index'))->assertOk();
        $outsider = User::factory()->create(['role' => 'admin']);
        $this->actingAs($outsider)->get(route('admin.tenants.index'))->assertForbidden();
    }

    public function test_onboarding_creates_tenant_owner_balance_commission_and_masked_bank_account(): void
    {
        Notification::fake();
        $this->actingAs($this->manager(Canteen::factory()->create()));
        $this->post(route('admin.tenants.store'), [
            'display_name' => 'Warung Rina', 'code' => 'RINA', 'slug' => 'warung-rina', 'commission_rate' => '10',
            'pic_name' => 'Rina Sari', 'pic_email' => 'rina@example.test', 'bank_code' => 'BCA',
            'account_holder' => 'Rina Sari', 'account_number' => '1234567890',
        ])->assertRedirect(route('admin.tenants.index'));

        $tenant = Tenant::where('code', 'RINA')->firstOrFail();
        $owner = User::where('email', 'rina@example.test')->firstOrFail();
        $this->assertSame('tenant', $owner->role);
        $this->assertTrue(UserTenantRole::where(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'role' => 'owner'])->exists());
        $this->assertTrue(TenantBalance::where('tenant_id', $tenant->id)->exists());
        $this->assertSame('0.1000', CommissionScheme::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->value('commission_rate'));
        $account = TenantBankAccount::where('tenant_id', $tenant->id)->firstOrFail();
        $this->assertSame('7890', $account->account_last4);
        $this->assertSame('1234567890', $account->account_number_cipher);
        $this->assertStringNotContainsString('1234567890', (string) DB::table('tenant_bank_accounts')->where('id', $account->id)->value('account_number_cipher'));
        $this->assertTrue(AuditLog::where(['entity' => 'tenant', 'entity_id' => (string) $tenant->id, 'action' => 'created'])->exists());
    }

    public function test_commission_schedule_closes_previous_version_and_preserves_history(): void
    {
        $tenant = Tenant::factory()->create();
        $first = CommissionScheme::factory()->create(['tenant_id' => $tenant->id, 'valid_from' => now()->subDay(), 'commission_rate' => 0.1]);
        $effectiveAt = now()->addDay();
        $second = app(ChangeCommissionSchedule::class)->handle($tenant, 15, $effectiveAt);
        $this->assertEquals($effectiveAt->timestamp, $first->fresh()->valid_to->timestamp);
        $this->assertNull($second->valid_to);
        $this->assertSame('0.1500', $second->commission_rate);
        $atBoundary = CommissionScheme::withoutGlobalScope('tenant')->effectiveAt($effectiveAt)->firstOrFail();
        $this->assertSame($second->id, $atBoundary->id);
        $this->assertDatabaseHas('audit_logs', ['entity' => 'commission_scheme', 'entity_id' => (string) $second->id]);
    }

    public function test_database_rejects_two_active_commission_schemes_for_one_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $attributes = ['tenant_id' => $tenant->id, 'commission_rate' => 0.1, 'valid_from' => now(), 'valid_to' => null, 'created_at' => now(), 'updated_at' => now()];
        DB::table('commission_schemes')->insert($attributes);
        $this->expectException(QueryException::class);
        DB::table('commission_schemes')->insert($attributes);
    }

    public function test_database_rejects_two_primary_bank_accounts_for_one_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $accounts = [
            ['tenant_id' => $tenant->id, 'account_number_cipher' => encrypt('111111'), 'account_last4' => '1111', 'bank_code' => 'BCA', 'account_holder' => 'A', 'status' => 'unverified', 'is_primary' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => $tenant->id, 'account_number_cipher' => encrypt('222222'), 'account_last4' => '2222', 'bank_code' => 'BNI', 'account_holder' => 'B', 'status' => 'unverified', 'is_primary' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('tenant_bank_accounts')->insert($accounts[0]);
        $this->expectException(QueryException::class);
        DB::table('tenant_bank_accounts')->insert($accounts[1]);
    }

    public function test_manager_can_verify_primary_bank_account_and_action_is_audited(): void
    {
        $canteen = Canteen::factory()->create();
        $manager = $this->manager($canteen);
        $tenant = Tenant::factory()->create(['canteen_id' => $canteen->id]);
        $account = (new TenantBankAccount)->forceFill([
            'tenant_id' => $tenant->id, 'account_number_cipher' => '1234567890', 'account_last4' => '7890',
            'bank_code' => 'BCA', 'account_holder' => 'Rina', 'status' => 'unverified', 'is_primary' => true,
        ]);
        $account->save();

        $this->actingAs($manager)->post(route('admin.tenants.bank-accounts.update', [$tenant, $account]), ['status' => 'verified'])->assertRedirect();
        $this->assertSame('verified', $account->fresh()->status);
        $this->assertSame($manager->id, $account->fresh()->verified_by);
        $this->assertDatabaseHas('audit_logs', ['entity' => 'tenant_bank_account', 'action' => 'verification_verified']);
    }

    public function test_audit_logs_cannot_be_updated_or_deleted_at_database_level(): void
    {
        $log = AuditLog::create(['entity' => 'tenant', 'action' => 'created', 'logged_at' => now()]);
        try {
            DB::table('audit_logs')->where('id', $log->id)->update(['action' => 'changed']);
            $this->fail('Audit update harus ditolak database.');
        } catch (QueryException) {
            $this->assertDatabaseHas('audit_logs', ['id' => $log->id, 'action' => 'created']);
        }
    }

    public function test_last_owner_role_cannot_be_removed(): void
    {
        $tenant = Tenant::factory()->create();
        $owner = User::factory()->create();
        app(AssignTenantRole::class)->assign($tenant, $owner, 'owner');
        $this->expectException(DomainException::class);
        app(AssignTenantRole::class)->remove($tenant, $owner, 'owner');
    }
}
