<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ];
    }

    public function messages(): array{
        return [
            'email.required' => 'El correo es obligatorio.',
            'email.string' => 'El correo debe de ser tipo texto.',
            'email.email' => 'El formato del correo es incorrecto.',
            'email.max' => 'El correo no puede superar los 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria',
            'password.string' => 'La contraseña debe de contener letras.',
        ];
    }
}
