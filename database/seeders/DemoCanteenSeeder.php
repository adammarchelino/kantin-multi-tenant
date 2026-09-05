<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DemoCanteenSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kantin Utama
        $canteen = Canteen::updateOrCreate(
            ['code' => 'KNT-PUSAT'],
            ['name' => 'Kantin Pusat Poliwangi', 'slug' => 'kantin-pusat']
        );

        // 2. Tenant 1: Kedai Kopi
        $tenant1 = Tenant::updateOrCreate(
            ['canteen_id' => $canteen->id, 'code' => 'TNT-KOPI'],
            ['slug' => 'kedai-kopi', 'display_name' => 'Kedai Kopi Senja', 'status' => 'active']
        );

        Menu::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'name' => 'Kopi Hitam Tubruk'],
            ['price_amount' => 8000, 'is_available' => true]
        );
        Menu::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'name' => 'Es Kopi Susu'],
            ['price_amount' => 12000, 'is_available' => true]
        );

        // 3. Tenant 2: Warung Ayam
        $tenant2 = Tenant::updateOrCreate(
            ['canteen_id' => $canteen->id, 'code' => 'TNT-AYAM'],
            ['slug' => 'warung-ayam', 'display_name' => 'Warung Ayam Geprek', 'status' => 'active']
        );

        Menu::updateOrCreate(
            ['tenant_id' => $tenant2->id, 'name' => 'Ayam Geprek Level 3'],
            ['price_amount' => 15000, 'is_available' => true]
        );
    }
}
