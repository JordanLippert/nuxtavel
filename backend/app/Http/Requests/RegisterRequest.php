<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email:filter', 'regex:/\.[a-zA-Z]{2,}$/', 'unique:users,email'],
            'password'   => ['required', Password::min(8)],
            'birth_date' => ['required', 'date_format:d/m/Y'],
        ];
    }
}
