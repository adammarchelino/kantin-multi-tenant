<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    public function test_active_matching_role_is_recognized(): void
    {
        $user = new User;
        $user->role = 'admin';
        $user->status = 'active';

        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isTenantOperator());
    }

    public function test_suspended_or_unassigned_user_has_no_role(): void
    {
        $user = new User;
        $user->role = 'tenant';
        $user->status = 'suspended';

        $this->assertFalse($user->hasRole('tenant'));
        $this->assertFalse($user->isActive());

        $user->role = null;
        $user->status = 'active';

        $this->assertFalse($user->hasRole('admin'));
    }
}
