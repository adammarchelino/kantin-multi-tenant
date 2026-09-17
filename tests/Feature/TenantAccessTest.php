<?php

use App\Models\Canteen;
use App\Models\Tenant;
use App\Models\User;

beforeEach(function () {
    $canteen = Canteen::create([
        'code' => 'KNT-01',
        'name' => 'Kantin Utama',
        'slug' => 'kantin-pusat',
    ]);

    Tenant::create([
        'canteen_id' => $canteen->id,
        'code' => 'TNT-01',
        'slug' => 'kantin-pusat',
        'display_name' => 'Kantin Pusat',
        'status' => 'active',
    ]);
});

test('guest redirected to login on tenant route', function () {
    $response = $this->get('/tenant/kantin-pusat/dashboard');

    $response->assertRedirect('/login');
});

test('verified user can open tenant dashboard', function () {
    $user = User::factory()->create(['role' => 'tenant']);

    $response = $this->actingAs($user)->get('/tenant/kantin-pusat/dashboard');

    $response->assertOk();
});
