<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:packages,name',
            'short_description' => 'required|string|max:255',
            'long_description' => 'required|string',
            'price' => 'required|digits_between:1,4',
            'characteristics' => 'required|array',
        ];
    }

    public function messages(): array{
        return [
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'name.required' => 'El nombre es requerido.',
            'short_description.required' => 'La descripción corta es requerida.',
            'short_description.max' => 'La descripción corta no puede superar los 255 caracteres.',
            'long_description.required' => 'La descripción larga es requerida.',
            'price.required' => 'El precio es requerido.',
            'price.digits_between' => 'El precio no puede superar los 4 dígitos.',
            'characteristics.required' => 'Al menos una característica es requerida.',
        ];
    }
}
