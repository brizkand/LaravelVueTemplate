<?php

use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('user can login with valid hrmis credentials', function () {

    $response = $this->postJson('/api/auth/login', [
        'username' => 'holgadok',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'access_token',
            'token_type',
        ]);

    $this->assertDatabaseHas('users', [
        'employee_number' => 22013,
        'username' => 'holgadok',
    ]);

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_type' => User::class,
    ]);

});

test('login fails with wrong credentials', function () {

    $response = $this->postJson('/api/auth/login', [
        'username' => 'holgadok',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJsonStructure([
            'errors' => [
                'username'
            ]
        ]);

});

test('login requires username and password', function () {

    $response = $this->postJson('/api/auth/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['username', 'password']);

});
