<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('registers a new user and returns token', function () {
    $response = $this->postJson('/api/register', [
        'name'       => 'Ana Cardoso',
        'email'      => 'ana@test.com',
        'password'   => 'secret123',
        'birth_date' => '14/03/1992',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
});

it('logs in with valid credentials and returns token', function () {
    $user = User::factory()->create([
        'email'    => 'ana@test.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email'    => 'ana@test.com',
        'password' => 'secret123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
});

it('returns 401 for invalid credentials', function () {
    User::factory()->create(['email' => 'ana@test.com']);

    $this->postJson('/api/login', [
        'email'    => 'ana@test.com',
        'password' => 'wrongpass',
    ])->assertUnauthorized();
});

it('returns authenticated user on /users/me', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/users/me')
        ->assertOk()
        ->assertJsonFragment(['email' => $user->email]);
});

it('logs out and returns 204', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/logout')
        ->assertNoContent();
});

it('resets password when email exists', function () {
    User::factory()->create([
        'email'    => 'ana@test.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $this->postJson('/api/forgot-password', [
        'email'    => 'ana@test.com',
        'password' => 'newpassword123',
    ])->assertOk()->assertJson(['message' => 'Senha redefinida com sucesso.']);

    $user = User::where('email', 'ana@test.com')->first();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

it('returns 422 when email does not exist on forgot-password', function () {
    $this->postJson('/api/forgot-password', [
        'email'    => 'naoexiste@test.com',
        'password' => 'qualquersenha',
    ])->assertUnprocessable()
      ->assertJsonFragment(['message' => 'E-mail não encontrado.']);
});

it('returns 422 for invalid data on forgot-password', function () {
    $this->postJson('/api/forgot-password', [
        'email'    => 'nao-e-um-email',
        'password' => '123',
    ])->assertUnprocessable()
      ->assertJsonValidationErrors(['email', 'password']);
});
