<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('user can logout', function () {

    $user = User::factory()->create();

    Sanctum::actingAs($user); // creates proper Sanctum token

    $response = $this->postJson('/api/auth/logout');

    $response->assertOk();

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id
    ]);
});
