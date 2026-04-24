<?php

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('has users table with correct columns', function () {
    expect(
        \Schema::hasColumns('users', [
            'id', 'name', 'email', 'birth_date', 'password', 'avatar', 'created_at', 'updated_at',
        ])
    )->toBeTrue();
});

it('creates a user from DTO', function () {
    $dto = new CreateUserDTO(
        name: 'Ana Cardoso',
        email: 'ana@test.com',
        birthDate: '14/03/1992',
        password: 'secret123',
    );

    $user = app(UserService::class)->createUser($dto);

    expect($user->name)->toBe('Ana Cardoso')
        ->and($user->email)->toBe('ana@test.com')
        ->and($user->birth_date->format('d/m/Y'))->toBe('14/03/1992')
        ->and($user->avatar)->toBeNull();
});

it('creates a user with avatar', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('photo.jpg');

    $dto = new CreateUserDTO(
        name: 'Rafael',
        email: 'rafael@test.com',
        birthDate: '02/11/1988',
        password: 'secret123',
        avatar: $file,
    );

    $user = app(UserService::class)->createUser($dto);

    expect($user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

it('updates a user from DTO', function () {
    $user = User::factory()->create(['name' => 'Old Name']);

    $dto = new UpdateUserDTO(
        name: 'New Name',
        email: $user->email,
        birthDate: '14/03/1992',
    );

    $updated = app(UserService::class)->updateUser($user, $dto);

    expect($updated->name)->toBe('New Name');
});

it('deletes a user and removes avatar', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('photo.jpg');
    $path = $file->store('avatars', 'public');

    $user = User::factory()->create(['avatar' => $path]);

    app(UserService::class)->deleteUser($user);

    expect(User::find($user->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('lists users filtered by name', function () {
    User::factory()->create(['name' => 'Ana Cardoso']);
    User::factory()->create(['name' => 'Rafael Mendes']);

    $result = app(UserService::class)->listUsers(['search' => 'Ana']);

    expect($result->total())->toBe(1)
        ->and($result->items()[0]->name)->toBe('Ana Cardoso');
});

it('lists users paginated', function () {
    $actor = User::factory()->create();
    User::factory()->count(3)->create();

    $this->actingAs($actor, 'sanctum')
        ->getJson('/api/users')
        ->assertOk()
        ->assertJsonStructure(['data', 'meta' => ['total', 'current_page']]);
});

it('shows a single user', function () {
    $actor  = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor, 'sanctum')
        ->getJson("/api/users/{$target->id}")
        ->assertOk()
        ->assertJsonFragment(['id' => $target->id]);
});

it('creates a user via API', function () {
    $actor = User::factory()->create();

    $this->actingAs($actor, 'sanctum')
        ->postJson('/api/users', [
            'name'       => 'Marina Torres',
            'email'      => 'marina@test.com',
            'password'   => 'secret123',
            'birth_date' => '30/09/1997',
        ])
        ->assertCreated()
        ->assertJsonFragment(['name' => 'Marina Torres']);
});

it('updates a user via API', function () {
    $actor  = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor, 'sanctum')
        ->putJson("/api/users/{$target->id}", [
            'name'       => 'Updated Name',
            'email'      => $target->email,
            'birth_date' => '14/03/1992',
        ])
        ->assertOk()
        ->assertJsonFragment(['name' => 'Updated Name']);
});

it('deletes a user via API', function () {
    $actor  = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor, 'sanctum')
        ->deleteJson("/api/users/{$target->id}")
        ->assertNoContent();

    expect(User::find($target->id))->toBeNull();
});
