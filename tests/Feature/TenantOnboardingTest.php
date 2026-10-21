<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserCanteenRole;
use App\Modules\Catalog\Services\PublicCatalogQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TenantOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function manager(Canteen $canteen): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        UserCanteenRole::create(['user_id' => $user->id, 'canteen_id' => $canteen->id, 'role' => 'manager']);

        return $user;
    }

    /** @return array<string, string> */
    private function payload(): array
    {
        return [
            'display_name' => 'Warung Rina', 'code' => 'RINA', 'slug' => 'warung-rina', 'commission_rate' => '10',
            'pic_name' => 'Rina Sari', 'pic_email' => 'rina@example.test', 'bank_code' => 'BCA',
            'account_holder' => 'Rina Sari', 'account_number' => '1234567890',
        ];
    }

    public function test_management_page_displays_owner_primary_account_and_commission(): void
    {
        Notification::fake();
        $canteen = Canteen::factory()->create();
        $this->actingAs($this->manager($canteen))->post(route('admin.tenants.store'), $this->payload());
        $this->get(route('admin.tenants.index'))->assertOk()->assertSee('Warung Rina')
            ->assertSee('Rina Sari')->assertSee('BCA ••7890')->assertSee('10.00%');
        $tenant = Tenant::where('code', 'RINA')->firstOrFail();
        $this->get(route('admin.tenants.edit', $tenant))->assertOk()->assertSee('Komisi dan riwayat perubahan')->assertSee('Pemilik dan operator');
    }

    public function test_deactivating_tenant_hides_menus_and_keeps_tenant_records(): void
    {
        $canteen = Canteen::factory()->create();
        $tenant = Tenant::factory()->create(['canteen_id' => $canteen->id, 'status' => 'active']);
        $menu = Menu::factory()->create(['tenant_id' => $tenant->id, 'is_available' => true]);
        $this->actingAs($this->manager($canteen))
            ->post(route('admin.tenants.status', $tenant), ['status' => 'inactive'])->assertRedirect(route('admin.tenants.index'));
        $this->assertSame('inactive', $tenant->fresh()->status);
        $this->assertFalse(app(PublicCatalogQuery::class)->forCanteen($canteen)->contains('id', $menu->id));
        $this->assertDatabaseHas('menus', ['id' => $menu->id]);
        $this->get(route('admin.tenants.index'))->assertSee('NONAKTIF')->assertSee('Aktifkan');
    }

    public function test_manager_of_another_canteen_cannot_deactivate_tenant(): void
    {
        $tenant = Tenant::factory()->create(['canteen_id' => Canteen::factory()->create()->id]);
        $outsider = $this->manager(Canteen::factory()->create());
        $this->actingAs($outsider)->post(route('admin.tenants.status', $tenant), ['status' => 'inactive'])->assertForbidden();
        $this->assertSame('active', $tenant->fresh()->status);
    }
}
