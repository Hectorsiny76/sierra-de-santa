<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomTypeRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|unique:room_types,name',
            'description' => 'sometimes|string|max:255',
            'max_capacity' => 'sometimes|integer|max:12',
            'image' => 'sometimes|image|max:2048',
        ];
    }

    public function messages(): array{
        return [
            'name.max' => 'El nombre no puede superar los 255 caracteres',
            'name.unique' => 'El nombre del tipo de cuarto ya existe',
            'description.max' => 'La descripción no puede superar los 255 caracteres',
            'max_capacity.integer' => 'La capacidad maxima del tipo de cuarto debe ser un numero',
            'max_capacity.max' => 'La capacidad del tipo de cuarto no puede ser mayor a 12',
            'image.max' => 'La imagen no puede superar los 2MB',
        ];
    }
}
