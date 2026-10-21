<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenantRole;
use App\Modules\Admin\AdminServiceProvider;
use App\Modules\Catalog\CatalogServiceProvider;
use App\Modules\Kitchen\KitchenServiceProvider;
use App\Modules\Ordering\OrderingServiceProvider;
use App\Modules\Payments\PaymentsServiceProvider;
use App\Modules\Reporting\ReportingServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ServiceProvider;
use Tests\TestCase;

class ModularFoundationTest extends TestCase
{
    use RefreshDatabase;

    private function user(?string $role, string $status = 'active'): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role, 'status' => $status])->save();

        return $user;
    }

    public function test_customer_route_is_public_and_uses_customer_shell(): void
    {
        $this->get(route('customer.home', ['canteen' => 'demo']))
            ->assertOk()
            ->assertSee('Selamat datang')
            ->assertSee('max-w-7xl', false)
            ->assertSee('md:grid-cols-', false);
    }

    public function test_guests_are_redirected_from_internal_portals(): void
    {
        $tenant = Tenant::factory()->create();

        $this->get(route('tenant.dashboard', ['tenant' => $tenant->slug]))
            ->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_tenant_and_admin_roles_are_limited_to_their_portals(): void
    {
        $tenantUser = $this->user('tenant');
        $tenant = Tenant::factory()->create();
        UserTenantRole::create(['user_id' => $tenantUser->id, 'tenant_id' => $tenant->id, 'role' => 'operator']);

        $this->actingAs($tenantUser)
            ->get(route('tenant.dashboard', ['tenant' => $tenant->slug]))
            ->assertOk()
            ->assertSee($tenant->display_name)
            ->assertSee('data-tenant-sidebar-toggle', false)
            ->assertSee('data-tenant-sidebar-close', false)
            ->assertSee('data-tenant-sidebar-backdrop', false);

        $this->get(route('admin.dashboard'))->assertForbidden();

        $this->actingAs($this->user('admin'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Pengelola');

        $this->get(route('tenant.dashboard', ['tenant' => $tenant->slug]))->assertForbidden();
    }

    public function test_missing_role_and_suspended_accounts_are_forbidden(): void
    {
        $this->actingAs($this->user(null))
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $tenantUser = $this->user('tenant', 'suspended');
        $tenant = Tenant::factory()->create();
        UserTenantRole::create(['user_id' => $tenantUser->id, 'tenant_id' => $tenant->id, 'role' => 'operator']);

        $this->actingAs($tenantUser)
            ->get(route('tenant.dashboard', ['tenant' => $tenant->slug]))
            ->assertForbidden();
    }

    public function test_all_six_module_providers_are_registered(): void
    {
        $providers = array_map('get_class', app()->getProviders(ServiceProvider::class));

        foreach ([
            AdminServiceProvider::class,
            CatalogServiceProvider::class,
            OrderingServiceProvider::class,
            PaymentsServiceProvider::class,
            KitchenServiceProvider::class,
            ReportingServiceProvider::class,
        ] as $provider) {
            $this->assertContains($provider, $providers);
        }
    }

    public function test_demo_accounts_are_seeded_with_active_portal_roles(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@kantin.test',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'tenant@kantin.test',
            'role' => 'tenant',
            'status' => 'active',
        ]);
        $this->assertDatabaseCount('users', 3);
    }
}
