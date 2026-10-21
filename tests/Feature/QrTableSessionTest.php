<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\CustomerSession;
use App\Models\DiningTable;
use App\Models\TableQrToken;
use App\Modules\Admin\Services\QrTokenService;
use App\Support\Tokens\OpaqueToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class QrTableSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_scan_creates_distinct_anonymous_session_cookie_and_redirects(): void
    {
        $canteen = Canteen::factory()->create();
        $table = DiningTable::factory()->create(['canteen_id' => $canteen->id]);
        $plain = app(QrTokenService::class)->issue($table);

        $response = $this->get(route('customer.scan', ['token' => $plain]));
        $response->assertRedirect(route('customer.welcome', ['canteen' => $canteen->slug]))->assertCookie('customer_session');
        $cookie = $response->getCookie('customer_session');
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());
        $session = CustomerSession::firstOrFail();
        $this->assertSame($table->id, $session->dining_table_id);
        $this->assertSame($canteen->id, $session->canteen_id);
        $this->assertSame(32, strlen($session->session_token_hash));
    }

    public function test_invalid_revoked_inactive_and_numeric_tokens_share_generic_404(): void
    {
        $this->get(route('customer.scan', ['token' => 'invalid']))->assertNotFound()->assertSee('hubungi petugas kantin');
        $canteen = Canteen::factory()->create();
        $table = DiningTable::factory()->create(['canteen_id' => $canteen->id]);
        $token = app(QrTokenService::class)->issue($table);
        app(QrTokenService::class)->rotate($table);
        $this->get(route('customer.scan', ['token' => $token]))->assertNotFound();
        $this->get(route('customer.scan', ['token' => (string) $table->id]))->assertNotFound();
        $table->update(['status' => 'inactive']);
        $active = TableQrToken::where('status', 'active')->firstOrFail();
        $plain = OpaqueToken::issue()['plain'];
        $active->forceFill(['token_hash' => OpaqueToken::hash($plain)])->save();
        $this->get(route('customer.scan', ['token' => $plain]))->assertNotFound();
    }

    public function test_expired_and_inactive_canteen_qrs_are_rejected(): void
    {
        $canteen = Canteen::factory()->create();
        $table = DiningTable::factory()->create(['canteen_id' => $canteen->id]);
        $expired = app(QrTokenService::class)->issue($table, now()->subMinute());
        $this->get(route('customer.scan', ['token' => $expired]))->assertNotFound();
        $canteen->update(['status' => 'inactive']);
        $active = app(QrTokenService::class)->issue($table);
        $this->get(route('customer.scan', ['token' => $active]))->assertNotFound();
    }

    public function test_qr_scan_route_is_rate_limited(): void
    {
        RateLimiter::clear('127.0.0.1');
        for ($attempt = 0; $attempt < 30; $attempt++) {
            $this->get('/q/invalid-'.$attempt)->assertNotFound();
        }
        $this->get('/q/one-too-many')->assertTooManyRequests();
    }
}
