<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can access profile', function () {

    $user = User::factory()->create();

    $response = $this->actingAs($user)
                     ->getJson('/api/auth/user');

    $response->assertOk();
});

test('guest cannot access profile', function () {

    $response = $this->getJson('/api/auth/user');

    $response->assertStatus(401);
});
