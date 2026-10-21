<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\CustomerSession;
use App\Models\DiningTable;
use App\Models\Tenant;
use App\Models\TenantOperatingHour;
use App\Modules\Admin\Services\QrTokenService;
use App\Modules\Catalog\Services\TenantOpeningHours;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CustomerScanUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_uses_table_from_anonymous_session(): void
    {
        $canteen = Canteen::factory()->create();
        $table = DiningTable::factory()->create(['canteen_id' => $canteen->id, 'label' => 'Meja 12', 'zone' => 'Zona A']);
        $tenant = Tenant::factory()->create(['canteen_id' => $canteen->id, 'display_name' => 'Warung Rina']);
        foreach (range(0, 6) as $day) {
            (new TenantOperatingHour)->forceFill(['tenant_id' => $tenant->id, 'day_of_week' => $day, 'opens_at' => '07:00:00', 'closes_at' => '20:00:00'])->save();
        }
        $token = app(QrTokenService::class)->issue($table);
        $scan = $this->get(route('customer.scan', ['token' => $token]));
        $cookie = $scan->getCookie('customer_session');
        $this->withCookie('customer_session', $cookie->getValue())->get(route('customer.welcome', ['canteen' => $canteen->slug]))
            ->assertOk()->assertSee('Meja 12')->assertSee('Zona A')->assertSee('Warung Rina')->assertSee('Lihat semua menu');
    }

    public function test_closed_canteen_does_not_create_customer_session(): void
    {
        $this->travelTo(Carbon::parse('2026-09-24 22:00', 'Asia/Jakarta'));
        $canteen = Canteen::factory()->create();
        $table = DiningTable::factory()->create(['canteen_id' => $canteen->id]);
        $tenant = Tenant::factory()->create(['canteen_id' => $canteen->id]);
        foreach (range(0, 6) as $day) {
            (new TenantOperatingHour)->forceFill(['tenant_id' => $tenant->id, 'day_of_week' => $day, 'opens_at' => '07:00:00', 'closes_at' => '20:00:00'])->save();
        }
        $token = app(QrTokenService::class)->issue($table);
        $this->get(route('customer.scan', ['token' => $token]))->assertOk()->assertSee('Kantin sedang tutup')->assertCookieMissing('customer_session');
        $this->assertSame(0, CustomerSession::count());
    }

    public function test_unconfigured_hours_mean_open_and_overnight_schedule_is_supported(): void
    {
        $canteen = Canteen::factory()->create();
        $tenant = Tenant::factory()->create(['canteen_id' => $canteen->id]);
        $hours = app(TenantOpeningHours::class);
        $this->assertTrue($hours->isOpen($tenant, Carbon::parse('2026-09-24 12:00', 'Asia/Jakarta')));
        (new TenantOperatingHour)->forceFill(['tenant_id' => $tenant->id, 'day_of_week' => 4, 'opens_at' => '18:00:00', 'closes_at' => '02:00:00'])->save();
        $this->assertTrue($hours->isOpen($tenant, Carbon::parse('2026-09-24 23:30', 'Asia/Jakarta')));
        $this->assertTrue($hours->isOpen($tenant, Carbon::parse('2026-09-25 01:00', 'Asia/Jakarta')));
        $this->assertFalse($hours->isOpen($tenant, Carbon::parse('2026-09-24 12:00', 'Asia/Jakarta')));
    }
}
