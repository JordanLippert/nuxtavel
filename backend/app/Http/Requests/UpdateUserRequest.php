<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email:filter', 'regex:/\.[a-zA-Z]{2,}$/', "unique:users,email,{$userId}"],
            'password'   => ['nullable', Password::min(8)],
            'birth_date' => ['required', 'date_format:d/m/Y'],
            'role'       => ['nullable', 'string', 'in:Admin,Editor,Viewer'],
            'status'     => ['nullable', 'string', 'in:Ativo,Inativo'],
            'avatar'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
