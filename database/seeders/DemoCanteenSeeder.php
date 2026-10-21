<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\CommissionScheme;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use App\Models\Tenant;
use App\Models\TenantBalance;
use App\Models\User;
use App\Models\UserTenantRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCanteenSeeder extends Seeder
{
    public function run(): void
    {
        $canteen = $this->persist(Canteen::firstOrNew(['code' => 'KTN-PUSAT']), ['code' => 'KTN-PUSAT'], [
            'slug' => 'kantin-pusat', 'name' => 'Kantin Pusat', 'tax_rate' => 0.1000,
            'service_fee_rate' => 0.0200, 'status' => 'active',
        ]);

        foreach ([['M01', 'Meja 1', 'Indoor'], ['M02', 'Meja 2', 'Indoor'], ['M03', 'Meja 3', 'Outdoor']] as [$code, $label, $zone]) {
            $this->persist(DiningTable::firstOrNew(['canteen_id' => $canteen->id, 'code' => $code]), ['canteen_id' => $canteen->id, 'code' => $code], [
                'label' => $label, 'zone' => $zone, 'status' => 'active',
            ]);
        }

        $blueprints = [
            'AYAM' => ['Ayam Geprek Mantul', 'ayam-pusat', 'Paket Ayam', [['Geprek Original', 15000], ['Geprek Keju', 20000]], 'Level Pedas', [['Level 1', 0], ['Level 5', 2000]]],
            'KOPI' => ['Kopi Kita', 'kopi-pusat', 'Kopi Susu', [['Kopi Susu Gula Aren', 18000], ['Americano', 16000]], 'Ukuran', [['Regular', 0], ['Large', 5000]]],
        ];

        /** @var array{AYAM?: Tenant, KOPI?: Tenant} $tenants */
        $tenants = [];
        foreach ($blueprints as $code => [$display, $slug, $categoryName, $menus, $groupName, $options]) {
            $tenant = $this->persist(Tenant::withTrashed()->firstOrNew(['canteen_id' => $canteen->id, 'code' => $code]), ['canteen_id' => $canteen->id, 'code' => $code], [
                'slug' => $slug, 'display_name' => $display, 'status' => 'active', 'deleted_at' => null,
            ]);
            $tenants[$code] = $tenant;

            $this->persist(TenantBalance::firstOrNew(['tenant_id' => $tenant->id]), ['tenant_id' => $tenant->id], ['available_amount' => 0, 'held_amount' => 0]);
            $this->persist(CommissionScheme::firstOrNew(['tenant_id' => $tenant->id, 'valid_from' => now()->startOfYear()]), ['tenant_id' => $tenant->id, 'valid_from' => now()->startOfYear()], [
                'commission_rate' => 0.1500, 'valid_to' => null,
            ]);
            $category = $this->persist(MenuCategory::firstOrNew(['tenant_id' => $tenant->id, 'name' => $categoryName]), ['tenant_id' => $tenant->id, 'name' => $categoryName], [
                'sort_order' => 1, 'is_active' => true,
            ]);
            $group = $this->persist(ModifierGroup::firstOrNew(['tenant_id' => $tenant->id, 'name' => $groupName]), ['tenant_id' => $tenant->id, 'name' => $groupName], [
                'min_select' => 1, 'max_select' => 1, 'is_active' => true,
            ]);

            foreach ($menus as $i => [$name, $price]) {
                $menu = $this->persist(Menu::firstOrNew(['tenant_id' => $tenant->id, 'name' => $name]), ['tenant_id' => $tenant->id, 'name' => $name], [
                    'category_id' => $category->id, 'base_price' => $price, 'stock_qty' => 100,
                    'is_available' => true, 'prep_minutes' => 10 + $i,
                ]);
                DB::table('menu_modifier_groups')->updateOrInsert(
                    ['tenant_id' => $tenant->id, 'menu_id' => $menu->id, 'modifier_group_id' => $group->id],
                    ['sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                );
            }

            foreach ($options as [$name, $delta]) {
                $this->persist(ModifierOption::firstOrNew(['tenant_id' => $tenant->id, 'group_id' => $group->id, 'name' => $name]), ['tenant_id' => $tenant->id, 'group_id' => $group->id, 'name' => $name], [
                    'price_delta' => $delta, 'stock_qty' => 100, 'is_available' => true,
                ]);
            }
        }

        $operator = User::where('email', 'tenant@kantin.test')->first();
        if ($operator !== null && isset($tenants['AYAM'])) {
            $this->persist(UserTenantRole::firstOrNew([
                'user_id' => $operator->id, 'tenant_id' => $tenants['AYAM']->id, 'role' => 'operator',
            ]), [
                'user_id' => $operator->id, 'tenant_id' => $tenants['AYAM']->id, 'role' => 'operator',
            ], []);
        }
    }

    /**
     * @template TModel of Model
     *
     * @param  TModel  $model
     * @param  array<string, mixed>  $identity
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    private function persist(Model $model, array $identity, array $attributes): Model
    {
        // forceFill penting karena identity sering berisi FK yang memang guarded.
        $model->forceFill($identity + $attributes)->save();

        return $model;
    }
}
