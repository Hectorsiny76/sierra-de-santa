<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'required|numeric|digits_between:10,12',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array{
        return [
            'first_name.required' => 'Los nombres son obligatorios',
            'first_name.string' => 'Los nombres debe ser de tipo texto',
            'first_name.max' => 'Los nombres en conjunto deben tener máximo 50 caracteres',
            'last_name.required' => 'Los apellidos son obligatorios',
            'last_name.string' => 'Los apellidos deben de ser de tipo texto',
            'last_name.max' => 'Los apellidos en conjunto deben de tener máximo 50 caracteres',
            'phone.required' => 'El teléfono es obligatorio',
            'phone.numeric' => 'El teléfono debe ser de tipo numerico',
            'phone.digits_between' => 'El teléfono debe de tener al menos 10 y máximo 12 caracteres',
            'email.required' => 'El correo es obligatorio',
            'email.string' => 'El correo debe ser de tipo texto',
            'email.email' => 'El correo debe ser de tipo mail',
            'email.unique' => 'El correo ya se encuentra registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe de tener al menos 8 caracteres',
            'password.string' => 'La contraseña debe de ser de tipo texto'
        ];
    }
}
