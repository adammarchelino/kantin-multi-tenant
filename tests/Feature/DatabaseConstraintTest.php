<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\CommissionScheme;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use App\Models\Tenant;
use Database\Seeders\DemoCanteenSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class DatabaseConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_cannot_reference_another_tenants_category(): void
    {
        [$tenantA, $tenantB] = $this->twoTenants();
        $categoryB = MenuCategory::factory()->create(['tenant_id' => $tenantB->id]);

        $this->expectException(QueryException::class);
        Menu::factory()->create(['tenant_id' => $tenantA->id, 'category_id' => $categoryB->id]);
    }

    public function test_modifier_option_cannot_reference_another_tenants_group(): void
    {
        [$tenantA, $tenantB] = $this->twoTenants();
        $groupB = ModifierGroup::factory()->create(['tenant_id' => $tenantB->id]);

        $this->expectException(QueryException::class);
        ModifierOption::factory()->create(['tenant_id' => $tenantA->id, 'group_id' => $groupB->id]);
    }

    public function test_duplicate_tenant_code_in_same_canteen_is_rejected(): void
    {
        $canteen = Canteen::factory()->create();
        Tenant::factory()->create(['canteen_id' => $canteen->id, 'code' => 'DUP']);

        $this->expectException(QueryException::class);
        Tenant::factory()->create(['canteen_id' => $canteen->id, 'code' => 'DUP']);
    }

    public function test_stock_movement_idempotency_key_is_unique(): void
    {
        $canteen = Canteen::factory()->create();
        $tenant = Tenant::factory()->create(['canteen_id' => $canteen->id]);
        $menu = Menu::factory()->create(['tenant_id' => $tenant->id]);
        $row = fn (): array => [
            'tenant_id' => $tenant->id, 'menu_id' => $menu->id, 'idempotency_key' => 'stock-key-1',
            'type' => 'sale', 'quantity_delta' => -1, 'created_at' => now(), 'updated_at' => now(),
        ];
        DB::table('menu_stock_movements')->insert($row());

        $this->expectException(QueryException::class);
        DB::table('menu_stock_movements')->insert($row());
    }

    public function test_parent_order_can_contain_two_tenant_orders_and_has_no_tenant_id(): void
    {
        $this->assertFalse(Schema::hasColumn('orders', 'tenant_id'));
        [$tenantA, $tenantB] = $this->twoTenants();
        $commissionA = CommissionScheme::factory()->create(['tenant_id' => $tenantA->id]);
        $commissionB = CommissionScheme::factory()->create(['tenant_id' => $tenantB->id]);
        $orderId = DB::table('orders')->insertGetId([
            'public_id' => (string) Str::uuid(), 'order_number' => 'ORD-TEST-1',
            'canteen_id' => $tenantA->canteen_id, 'checkout_key' => 'checkout-test-1',
            'tracking_token_hash' => random_bytes(32), 'status' => 'pending', 'grand_total_amount' => 50000,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        foreach ([[$tenantA, $commissionA], [$tenantB, $commissionB]] as [$tenant, $commission]) {
            DB::table('tenant_orders')->insert([
                'order_id' => $orderId, 'tenant_id' => $tenant->id, 'commission_id' => $commission->id,
                'status' => 'pending', 'commission_rate_snapshot' => 0.1500, 'subtotal_amount' => 25000,
                'commission_amount' => 3750, 'net_amount' => 21250, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $this->assertSame(2, DB::table('tenant_orders')->where('order_id', $orderId)->count());
    }

    public function test_money_columns_cast_to_integer(): void
    {
        $canteen = Canteen::factory()->create();
        $tenant = Tenant::factory()->create(['canteen_id' => $canteen->id]);
        $menu = Menu::factory()->create(['tenant_id' => $tenant->id, 'base_price' => 15000]);

        $this->assertSame(15000, $menu->refresh()->base_price);
    }

    public function test_demo_seeder_is_idempotent_and_creates_two_tenants(): void
    {
        $seeder = app(DemoCanteenSeeder::class);
        $seeder->run();
        $seeder->run();

        $this->assertDatabaseCount('canteens', 1);
        $this->assertDatabaseCount('tenants', 2);
        $this->assertDatabaseCount('dining_tables', 3);
        $this->assertDatabaseCount('menus', 4);
        $this->assertDatabaseCount('menu_modifier_groups', 4);
        $this->assertDatabaseCount('modifier_groups', 2);
        $this->assertDatabaseCount('modifier_options', 4);
    }

    /** @return array{Tenant, Tenant} */
    private function twoTenants(): array
    {
        $canteen = Canteen::factory()->create();

        return [
            Tenant::factory()->create(['canteen_id' => $canteen->id]),
            Tenant::factory()->create(['canteen_id' => $canteen->id]),
        ];
    }
}
