<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_cannot_access_other_tenant_data()
    {
        // 1. Buat Canteen dengan menyertakan 'code'
        $canteen = Canteen::create([
            'code' => 'KNT-01',
            'name' => 'Kantin Utama',
            'slug' => 'kantin-utama',
        ]);

        // 2. Buat Tenant A dan Tenant B
        $tenantA = Tenant::create([
            'canteen_id' => $canteen->id,
            'code' => 'TNT-A',
            'slug' => 'kantin-a',
            'display_name' => 'Kantin A',
            'status' => 'active',
        ]);

        $tenantB = Tenant::create([
            'canteen_id' => $canteen->id,
            'code' => 'TNT-B',
            'slug' => 'kantin-b',
            'display_name' => 'Kantin B',
            'status' => 'active',
        ]);

        // 3. Buat User untuk simulasi login
        $user = User::factory()->create();

        // 4. Buat Menu milik Tenant B sambil mengaktifkan context tenant B
        $context = app(TenantContext::class);
        $context->set($tenantB);

        try {
            $menuB = Menu::withoutGlobalScopes()->create([
                'tenant_id' => $tenantB->id,
                'name' => 'Menu Milik Kantin B',
                'price_amount' => 15000,
                'is_available' => true,
            ]);
        } finally {
            $context->clear();
        }

        // 5. Coba akses menu Tenant B lewat jalur URL Tenant A
        $response = $this->actingAs($user)
            ->get("/tenant/{$tenantA->slug}/menus/{$menuB->id}/edit");

        // 6. Validasi bahwa akses diblokir (403 atau 404)
        $this->assertTrue(
            in_array($response->status(), [403, 404]),
            'Keamanan jebol! Akses lintas tenant menghasilkan status: '.$response->status()
        );
    }
}
