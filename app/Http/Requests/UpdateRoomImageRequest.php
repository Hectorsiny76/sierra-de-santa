<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomImageRequest extends FormRequest
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
            'description' => 'nullable|string|max:255',
            'image' => 'sometimes|image|max:2048',
        ];
    }

    public function messages(): array{
        return [
            'description.string' => 'La descripción debe de ser una cadena de caracteres.',
            'description.max' => 'La descripción no puede superar los 255 caracteres.',
            'image.image' => 'La imagen no es una imagen.',
            'image.max' => 'La imagen no puede superar los 2MB.',
        ];
    }
}
