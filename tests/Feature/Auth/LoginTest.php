<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
    ->assertJsonStructure([
        'access_token',
        'token_type',
        'user',
    ])
    ->assertJson([
        'user' => [
            'email' => 'test@example.com',
        ],
    ]);

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);

});

test('login fails with wrong credentials', function () {

    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'wrong',
    ]);

    $response->assertStatus(401);
});

test('login requires email and password', function () {

    $response = $this->postJson('/api/auth/login', []);

    $response->assertStatus(422)
    ->assertJsonValidationErrors(['email', 'password']);
});
