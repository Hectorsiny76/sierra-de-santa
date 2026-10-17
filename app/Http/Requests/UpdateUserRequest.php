<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'sometimes|string|max:50',
            'last_name' => 'sometimes|string|max:50',
            'phone' => 'sometimes|numeric|digits_between:10,12',
        ];
    }

    public function messages(): array{
        return [
            'first_name.string' => 'Los nombres debe ser de tipo texto',
            'first_name.max' => 'Los nombres en conjunto deben tener máximo 50 caracteres',
            'last_name.string' => 'Los apellidos deben de ser de tipo texto',
            'last_name.max' => 'Los apellidos en conjunto deben de tener máximo 50 caracteres',
            'phone.numeric' => 'El teléfono debe ser de tipo numérico',
            'phone.digits_between' => 'El teléfono debe de tener al menos 10 y máximo 12 caracteres',
        ];
    }
}
