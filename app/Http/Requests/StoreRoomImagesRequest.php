<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomImagesRequest extends FormRequest
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
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|max:2048',
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array{
        return [
            'images.required' => 'Las imágenes son obligatorias.',
            'images.array' => 'Las imágenes deben estar contenidas en una array.',
            'images.min' => 'Al menos debe haber una imagen en el array.',
            'images.max' => 'No se puede ingresar más de 10 imágenes.',
            'images.*.image' => 'La imagen no es una imagen.',
            'images.*.max' => 'La imagen no puede superar los 2MB.',
        ];
    }
}
