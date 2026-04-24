<?php

namespace App\DTOs;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\UploadedFile;

final class UpdateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $birthDate,
        public readonly ?string $password = null,
        public readonly ?UploadedFile $avatar = null,
    ) {}

    public static function fromRequest(UpdateUserRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            birthDate: $request->validated('birth_date'),
            password: $request->validated('password'),
            avatar: $request->file('avatar'),
        );
    }
}
