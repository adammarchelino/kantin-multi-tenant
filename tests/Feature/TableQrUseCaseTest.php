<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\DiningTable;
use App\Models\User;
use App\Models\UserCanteenRole;
use App\Modules\Admin\Services\QrTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableQrUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private function manager(Canteen $canteen): User
    {
        $user = User::factory()->create(['role' => 'admin', 'status' => 'active', 'email_verified_at' => now()]);
        UserCanteenRole::create(['user_id' => $user->id, 'canteen_id' => $canteen->id, 'role' => 'manager']);

        return $user;
    }

    public function test_manager_can_create_table_and_issue_one_time_printable_qr(): void
    {
        $canteen = Canteen::factory()->create();
        $this->actingAs($this->manager($canteen));
        $this->post(route('admin.tables.store'), ['code' => 'A-1', 'label' => 'Meja A1', 'zone' => 'Indoor'])->assertRedirect(route('admin.tables.index'));
        $table = DiningTable::where('canteen_id', $canteen->id)->firstOrFail();
        $this->post(route('admin.tables.rotate', $table))->assertRedirect(route('admin.tables.qr', $table));
        $page = $this->get(route('admin.tables.qr', $table))->assertOk()->assertSee('<svg', false)->assertSee('Unduh QR (SVG)')->assertSee('/q/', false);
        $this->assertStringNotContainsString('/q/'.$table->id.'"', $page->getContent());
        $this->get(route('admin.tables.qr', $table))->assertDontSee('<svg', false)->assertSee('Tidak ada QR baru');
    }

    public function test_rotating_token_revokes_previous_token(): void
    {
        $table = DiningTable::factory()->create();
        $service = app(QrTokenService::class);
        $old = $service->issue($table);
        $new = $service->rotate($table);
        $this->assertNotSame($old, $new);
        $this->get(route('customer.scan', ['token' => $old]))->assertNotFound();
        $this->get(route('customer.scan', ['token' => $new]))->assertRedirect();
    }

    public function test_other_canteen_manager_cannot_view_or_change_table(): void
    {
        $table = DiningTable::factory()->create();
        $this->actingAs($this->manager(Canteen::factory()->create()));
        $this->post(route('admin.tables.rotate', $table))->assertForbidden();
    }
}
