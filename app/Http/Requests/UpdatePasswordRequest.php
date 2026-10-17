<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'current_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed', //Requiere un campo de password_confirmation en el form
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'La contraseña actual es obligatoria',
            'current_password.min' => 'La contraseña requiere de al menos 8 caracteres',
            'current_password.string' => 'La contraseña debe ser de tipo texto',
            'password.required' => 'La nueva contraseña es obligatoria',
            'password.string' => 'La nueva contraseña debe ser de tipo texto',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'La nueva contraseña no coincide',
        ];
    }
}
