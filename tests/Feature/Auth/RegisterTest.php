<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register with valid data', function () {

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Kevin',
        'email' => 'kevin@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'access_token',
            'token_type',
        ]);

    // user created in database
    $this->assertDatabaseHas('users', [
        'email' => 'kevin@example.com',
    ]);

    // token created
    $user = User::where('email', 'kevin@example.com')->first();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

test('cannot register with existing email', function () {

    $user = User::factory()->create();

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Kevin',
        'email' => $user->email,
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registration requires name email and password', function () {

    $response = $this->postJson('/api/auth/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'email',
            'password',
        ]);
});

test('password must be confirmed', function () {

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Kevin',
        'email' => 'kevin@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'wrong',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('password must meet complexity requirements', function () {

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Kevin',
        'email' => 'kevin@example.com',
        'password' => 'password', // weak password
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
