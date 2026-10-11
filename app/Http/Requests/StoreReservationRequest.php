<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'package_id' => ['sometimes', 'integer', 'exists:packages,id'],
        ];
    }

    public function messages(): array{
        return [
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha.',
            'end_date.required' => 'La fecha termino es obligatoria.',
            'end_date.date' => 'La fecha termino debe ser una fecha.',
            'end_date.after' => 'La fecha termino debe ser al menos un dia después de la fecha de inicio.',
            'guest_count.required' => 'La cantidad de personas es obligatoria.',
            'guest_count.integer' => 'La cantidad de personas debe ser un numero.',
            'guest_count.min' => 'Al menos una persona debe ser registrada.',
            'room_id.required' => 'El id del cuarto es obligatorio.',
            'room_id.integer' => 'El id del cuarto debe ser un numero.',
            'room_id.exists' => 'El id del cuarto no existe.',
            'package_id.integer' => 'El id del paquete debe ser un numero.',
            'package_id.exists' => 'El id del paquete no existe.',
        ];
    }
}
