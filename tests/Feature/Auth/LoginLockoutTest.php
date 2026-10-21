<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_sixth_failure_locks_account_even_when_next_password_is_correct(): void
    {
        $user = User::factory()->create(['email' => 'tenant@example.test']);
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong-password'])->assertSessionHasErrors('email');
        }
        $locked = $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertStringContainsString('Akun terkunci sementara', $locked->getSession()->get('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_login_screen_is_indonesian_and_session_is_eight_hours(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk ke akun')->assertSee('Alamat email');
        $this->assertSame(480, config('session.lifetime'));
    }

    public function test_nonexistent_and_inactive_accounts_receive_the_same_login_error(): void
    {
        $inactive = User::factory()->create(['email' => 'inactive@example.test', 'status' => 'inactive']);
        $first = $this->post(route('login.store'), ['email' => 'missing@example.test', 'password' => 'secret'])->assertSessionHasErrors('email');
        $second = $this->post(route('login.store'), ['email' => $inactive->email, 'password' => 'secret'])->assertSessionHasErrors('email');
        $this->assertSame($first->getSession()->get('errors')->first('email'), $second->getSession()->get('errors')->first('email'));
    }
}
