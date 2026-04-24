<?php

namespace App\Services;

use App\DTOs\CreateUserDTO;
use App\DTOs\RegisterDTO;
use App\DTOs\UpdateUserDTO;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{
    public function registerUser(RegisterDTO $dto): User
    {
        return User::create([
            'name'       => $dto->name,
            'email'      => $dto->email,
            'birth_date' => Carbon::createFromFormat('d/m/Y', $dto->birthDate)->toDateString(),
            'password'   => Hash::make($dto->password),
        ]);
    }

    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function listUsers(array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->when(
                !empty($filters['search']),
                fn ($q) => $q->where(function ($sub) use ($filters) {
                    $sub->where('name', 'like', "%{$filters['search']}%")
                        ->orWhere('email', 'like', "%{$filters['search']}%");
                })
            )
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function createUser(CreateUserDTO $dto): User
    {
        $avatarPath = $this->storeAvatar($dto->avatar);

        return User::create([
            'name'       => $dto->name,
            'email'      => $dto->email,
            'birth_date' => Carbon::createFromFormat('d/m/Y', $dto->birthDate)->toDateString(),
            'password'   => Hash::make($dto->password),
            'avatar'     => $avatarPath,
        ]);
    }

    public function updateUser(User $user, UpdateUserDTO $dto): User
    {
        $data = [
            'name'       => $dto->name,
            'email'      => $dto->email,
            'birth_date' => Carbon::createFromFormat('d/m/Y', $dto->birthDate)->toDateString(),
        ];

        if ($dto->password) {
            $data['password'] = Hash::make($dto->password);
        }

        if ($dto->avatar) {
            $this->deleteAvatarFile($user->avatar);
            $data['avatar'] = $this->storeAvatar($dto->avatar);
        }

        $user->update($data);

        return $user->fresh();
    }

    public function deleteUser(User $user): void
    {
        $this->deleteAvatarFile($user->avatar);
        $user->delete();
    }

    private function storeAvatar(mixed $file): ?string
    {
        if (!$file) return null;

        $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('avatars', $name, 'public');
    }

    private function deleteAvatarFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
