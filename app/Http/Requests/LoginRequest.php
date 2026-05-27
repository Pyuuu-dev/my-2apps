<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:1024'],
            'cf-turnstile-response' => ['required', 'string', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Selesaikan verifikasi keamanan terlebih dahulu.',
        ];
    }

    public function attributes(): array
    {
        return [
            'cf-turnstile-response' => 'verifikasi keamanan',
        ];
    }
}
