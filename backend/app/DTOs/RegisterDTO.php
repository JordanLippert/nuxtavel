<?php

namespace App\DTOs;

use App\Http\Requests\RegisterRequest;

final class RegisterDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $birthDate,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            birthDate: $request->validated('birth_date'),
        );
    }
}
