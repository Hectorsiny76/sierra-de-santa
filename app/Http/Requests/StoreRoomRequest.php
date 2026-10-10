<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:rooms,name',
            'slug' => 'required|string|max:255|unique:rooms,slug',
            'general_description' => 'required|string|max:255',
            'characteristics' => 'required|array',
            'price' => 'required|numeric|digits_between:1,4',
            'room_type_id' => 'required|integer|exists:room_types,id',
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no puede superar los 255 caracteres',
            'name.unique' => 'El nombre ya existe',
            'slug.required' => 'El slug es requerido',
            'slug.string' => 'El slug debe ser una cadena de texto',
            'slug.max' => 'El slug no puede superar los 255 caracteres',
            'slug.unique' => 'El slug ya existe',
            'general_description.required' => 'Una descripción general es requerida',
            'general_description.string' => 'La descripción general debe ser una cadena de texto',
            'general_description.max' => 'La descripción general no puede superar los 255 caracteres',
            'characteristics.required' => 'Al menos una característica es requerida',
            'characteristics.array' => 'Las características deben de ser de tipo array',
            'price.required' => 'El precio es requerido',
            'price.numeric' => 'El precio debe ser un numero',
            'price.digits_between' => 'El precio debe ser mayor que 1 y menor a 4',
            'room_type_id.required' => 'El id del tipo de cuarto es requerido',
            'room_type_id.integer' => 'El id del tipo de cuarto debe ser un entero',
            'room_type_id.exists' => 'El id del tipo de cuarto no existe',
        ];
    }
}
