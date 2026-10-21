<?php

namespace Tests\Feature;

use App\Jobs\CountTenantMenus;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Tenant;
use App\Models\TenantOrder;
use App\Models\User;
use App\Models\UserTenantRole;
use App\Models\Withdrawal;
use App\Modules\Catalog\Services\PublicCatalogQuery;
use App\Policies\MenuPolicy;
use App\Policies\TenantOrderPolicy;
use App\Policies\WithdrawalPolicy;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{Tenant, Collection<int, Menu>} */
    private function tenantWithMenus(int $count, ?Canteen $canteen = null, string $status = 'active'): array
    {
        $canteen ??= Canteen::factory()->create();
        $tenant = Tenant::factory()->create(['canteen_id' => $canteen->id, 'status' => $status]);
        $category = MenuCategory::factory()->create(['tenant_id' => $tenant->id]);
        $menus = Menu::factory()->count($count)->create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
        ]);

        return [$tenant, $menus];
    }

    private function memberOf(Tenant $tenant, string $status = 'active'): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'tenant', 'status' => $status, 'email_verified_at' => now()])->save();
        UserTenantRole::create(['user_id' => $user->id, 'tenant_id' => $tenant->id, 'role' => 'operator']);

        return $user;
    }

    public function test_global_scope_returns_only_active_tenant_rows(): void
    {
        [$tenantA] = $this->tenantWithMenus(2);
        $this->tenantWithMenus(3);
        app(TenantContext::class)->set($tenantA);

        $this->assertSame(2, Menu::query()->count());
        $this->assertTrue(Menu::query()->get()->every(fn (Menu $menu): bool => (int) $menu->tenant_id === $tenantA->id));
        $this->assertSame(1, MenuCategory::query()->count());
    }

    public function test_without_context_scope_is_not_applied(): void
    {
        $this->tenantWithMenus(2);
        $this->tenantWithMenus(3);

        // Internal portal routes always set context; cross-tenant reads need a reviewed service.
        $this->assertSame(5, Menu::query()->count());
    }

    public function test_context_is_reset_between_scoped_lifecycles(): void
    {
        [$tenant] = $this->tenantWithMenus(1);
        app(TenantContext::class)->set($tenant);
        $this->assertTrue(app(TenantContext::class)->has());

        $this->app->forgetScopedInstances();

        $this->assertFalse(app(TenantContext::class)->has());
    }

    public function test_tenant_context_id_requires_a_tenant(): void
    {
        $this->expectException(\RuntimeException::class);
        app(TenantContext::class)->id();
    }

    public function test_tenant_id_is_taken_from_context_when_creating_model(): void
    {
        [$tenantA] = $this->tenantWithMenus(0);
        [$tenantB] = $this->tenantWithMenus(0);
        app(TenantContext::class)->set($tenantA);

        $category = MenuCategory::forceCreate([
            'tenant_id' => $tenantB->id,
            'name' => 'Owned by context',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertSame($tenantA->id, (int) $category->tenant_id);
    }

    public function test_member_can_read_only_own_tenant_menu_index(): void
    {
        [$tenantA] = $this->tenantWithMenus(2);
        $this->tenantWithMenus(3);

        $response = $this->actingAs($this->memberOf($tenantA))
            ->getJson(route('tenant.menus.index', ['tenant' => $tenantA->slug]));

        $response->assertOk();
        $this->assertCount(2, $response->json('menus'));
        $this->assertFalse(app(TenantContext::class)->has(), 'Middleware harus membersihkan context setelah request.');
    }

    public function test_scoped_binding_returns_not_found_for_another_tenants_menu(): void
    {
        [$tenantA] = $this->tenantWithMenus(1);
        [, $menusB] = $this->tenantWithMenus(1);
        $menuB = $menusB->sole();

        $this->actingAs($this->memberOf($tenantA))
            ->getJson(route('tenant.menus.show', ['tenant' => $tenantA->slug, 'menu' => $menuB->id]))
            ->assertNotFound();
    }

    public function test_member_can_update_own_menu(): void
    {
        [$tenant] = $this->tenantWithMenus(1);
        $menu = $tenant->menus()->firstOrFail();

        $this->actingAs($this->memberOf($tenant))
            ->patchJson(route('tenant.menus.update', ['tenant' => $tenant->slug, 'menu' => $menu->id]), [
                'is_available' => false,
            ])
            ->assertOk()
            ->assertJson(['ok' => true, 'is_available' => false]);

        $this->assertFalse($menu->refresh()->is_available);
    }

    public function test_policy_denies_records_outside_active_tenant(): void
    {
        [$tenantA] = $this->tenantWithMenus(0);
        [$tenantB] = $this->tenantWithMenus(1);
        $menuB = $tenantB->menus()->firstOrFail();
        $orderB = new TenantOrder;
        $orderB->setAttribute('tenant_id', $tenantB->id);
        $withdrawalB = new Withdrawal;
        $withdrawalB->setAttribute('tenant_id', $tenantB->id);
        app(TenantContext::class)->set($tenantA);
        $user = new User;

        $this->assertFalse((new MenuPolicy)->update($user, $menuB));
        $this->assertFalse((new TenantOrderPolicy)->update($user, $orderB));
        $this->assertFalse((new WithdrawalPolicy)->update($user, $withdrawalB));
    }

    public function test_tenant_middleware_requires_active_tenant_membership(): void
    {
        [$tenantA] = $this->tenantWithMenus(1);
        $tenantB = Tenant::factory()->create();
        $this->actingAs($this->memberOf($tenantA))
            ->get(route('tenant.dashboard', ['tenant' => $tenantB->slug]))
            ->assertForbidden();

        $inactiveTenant = Tenant::factory()->suspended()->create();
        UserTenantRole::create([
            'user_id' => auth()->id(), 'tenant_id' => $inactiveTenant->id, 'role' => 'operator',
        ]);
        $this->get(route('tenant.dashboard', ['tenant' => $inactiveTenant->slug]))->assertForbidden();
    }

    public function test_public_catalog_bypass_filters_canteen_and_tenant_status(): void
    {
        $canteenA = Canteen::factory()->create();
        $this->tenantWithMenus(2, $canteenA);
        $this->tenantWithMenus(1, $canteenA, 'suspended');
        $canteenB = Canteen::factory()->create();
        $this->tenantWithMenus(4, $canteenB);
        $inactiveCanteen = Canteen::factory()->create(['status' => 'inactive']);
        $this->tenantWithMenus(2, $inactiveCanteen);

        $menus = app(PublicCatalogQuery::class)->forCanteen($canteenA);

        $this->assertCount(2, $menus);
        $this->assertTrue($menus->every(fn (Menu $menu): bool => $menu->is_available));
    }

    public function test_queue_job_sets_and_clears_context_for_each_tenant(): void
    {
        [$tenantA] = $this->tenantWithMenus(2);
        [$tenantB] = $this->tenantWithMenus(3);
        $context = app(TenantContext::class);
        $jobA = new CountTenantMenus($tenantA->id);
        $jobA->handle($context);
        $this->assertSame(2, $jobA->result);
        $this->assertFalse($context->has());

        $jobB = new CountTenantMenus($tenantB->id);
        $jobB->handle($context);
        $this->assertSame(3, $jobB->result);
        $this->assertFalse($context->has());
    }
}
