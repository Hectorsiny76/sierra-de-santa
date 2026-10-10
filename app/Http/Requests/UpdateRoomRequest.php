<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|unique:rooms,name',$this->room->name,
            'slug' => 'sometimes|string|max:255|unique:rooms,slug',$this->room->slug,
            'general_description' => 'sometimes|string|max:255',
            'characteristics' => 'sometimes|array',
            'price' => 'sometimes|numeric|digits_between:1,4',
            'room_type_id' => 'sometimes|integer|exists:room_types,id',
        ];
    }

    public function messages(): array{
        return [
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no puede superar los 255 caracteres',
            'name.unique' => 'El nombre ya existe',
            'slug.string' => 'El slug debe ser una cadena de texto',
            'slug.max' => 'El slug no puede superar los 255 caracteres',
            'slug.unique' => 'El slug ya existe',
            'general_description.string' => 'La descripción general debe ser una cadena de texto',
            'general_description.max' => 'La descripción general no puede superar los 255 caracteres',
            'characteristics.array' => 'Las características deben de ser de tipo array',
            'price.numeric' => 'El precio debe ser un numero',
            'price.digits_between' => 'El precio debe ser mayor que 1 y menor a 4',
            'room_type_id.integer' => 'El id del tipo de cuarto debe ser un entero',
            'room_type_id.exists' => 'El id del tipo de cuarto no existe',
        ];
    }
}
