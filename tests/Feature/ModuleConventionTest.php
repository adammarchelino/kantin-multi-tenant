<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenantRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\Fixtures\Modules\Probe\ProbeServiceProvider;
use Tests\TestCase;

class ModuleConventionTest extends TestCase
{
    use RefreshDatabase;

    private function registerProbeModule(): void
    {
        $this->app->register(ProbeServiceProvider::class);
        Route::getRoutes()->refreshNameLookups();
    }

    public function test_each_module_has_a_view_namespace(): void
    {
        $hints = View::getFinder()->getHints();

        foreach (['Admin', 'Catalog', 'Ordering', 'Payments', 'Kitchen', 'Reporting'] as $module) {
            $alias = strtolower($module);
            $this->assertArrayHasKey($alias, $hints);
            $this->assertContains(
                realpath(app_path("Modules/{$module}/resources/views")),
                array_map('realpath', $hints[$alias]),
            );
        }
    }

    public function test_module_route_is_loaded_with_tenant_security_and_module_view(): void
    {
        $this->registerProbeModule();

        $route = Route::getRoutes()->getByName('tenant.probe');
        $this->assertNotNull($route);
        $this->assertSame('tenant/{tenant}/probe', $route->uri());
        $this->assertSame('slug', $route->bindingFieldFor('tenant'));
        $this->assertTrue($route->enforcesScopedBindings());

        foreach (['web', 'auth', 'verified', 'tenant'] as $middleware) {
            $this->assertContains($middleware, $route->gatherMiddleware());
        }

        $user = User::factory()->create();
        $user->forceFill(['role' => 'tenant', 'status' => 'active'])->save();
        $tenant = Tenant::factory()->create();
        UserTenantRole::create(['user_id' => $user->id, 'tenant_id' => $tenant->id, 'role' => 'operator']);
        $this->actingAs($user);

        $this->get(route('tenant.probe', ['tenant' => $tenant->slug]))
            ->assertOk()
            ->assertSee('Probe page')
            ->assertSee('Probe count: 0');
    }

    public function test_module_livewire_component_is_resolved_by_module_alias(): void
    {
        $this->registerProbeModule();

        Livewire::test('probe::counter')
            ->assertSee('Probe count: 0')
            ->call('increment')
            ->assertSee('Probe count: 1');
    }

    public function test_every_named_portal_route_has_the_required_middleware(): void
    {
        $portals = [
            'tenant.' => ['web', 'auth', 'verified', 'tenant'],
            'admin.' => ['web', 'auth', 'verified', 'role:admin'],
            'customer.' => ['web'],
        ];
        $checked = 0;

        /** @var RoutingRoute $route */
        foreach (Route::getRoutes() as $route) {
            foreach ($portals as $prefix => $required) {
                if (str_starts_with((string) $route->getName(), $prefix)) {
                    foreach ($required as $middleware) {
                        $this->assertContains($middleware, $route->gatherMiddleware());
                    }
                    $checked++;
                }
            }
        }

        $this->assertGreaterThanOrEqual(3, $checked);
    }
}
