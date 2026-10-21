<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenantRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_tenant_user_is_redirected_to_their_active_tenant_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::factory()->create(['status' => 'active']);
        UserTenantRole::create(['user_id' => $user->id, 'tenant_id' => $tenant->id, 'role' => 'owner']);

        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('tenant.dashboard', $tenant->slug));
    }
}
