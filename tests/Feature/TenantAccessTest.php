<?php

use App\Models\User;

test('guest redirected to login on tenant route', function () {
    $response = $this->get('/tenant/kantin-pusat/dashboard');

    $response->assertRedirect('/login');
});

test('verified user can open tenant dashboard', function () {
    $user = User::factory()->create(['role' => 'tenant']);

    $response = $this->actingAs($user)->get('/tenant/kantin-pusat/dashboard');

    $response->assertOk();
});
