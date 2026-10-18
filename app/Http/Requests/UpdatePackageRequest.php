<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('packages', 'name')->ignore($this->route('package'))],
            'short_description' => ['sometimes', 'string', 'max:255'],
            'long_description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'integer', 'gt:100', 'lt:9999'],
            'characteristics' => ['sometimes', 'array'],
        ];
    }

    public function messages(): array{
        return [
            'name.string' => 'El nombre debe de ser tipo texto.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'short_description.string' => 'La descripción corta debe de ser tipo texto.',
            'short_description.max' => 'La descripción corta no puede superar los 255 caracteres.',
            'long_description.string' => 'La descripción larga corta debe de ser tipo texto.',
            'price.integer' => 'El precio debe ser un entero.',
            'price.gt' => 'El precio debe ser mayor a 100.',
            'price.lte' => 'El precio debe ser menor a 9999.',
            'characteristics.array' => 'Las características deben ser de tipo array.',
        ];
    }
}
