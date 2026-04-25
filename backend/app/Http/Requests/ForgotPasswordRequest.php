<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email:filter', 'regex:/\.[a-zA-Z]{2,}$/'],
            'password' => ['required', Password::min(8)->letters()->numbers()], // -> mixedCase() exige maiúscula + minúscula, mais seguro porém mais restritivo
        ];
    }
}
