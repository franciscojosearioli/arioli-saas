<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOperacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('operacion'));
    }

    public function rules(): array
    {
        return [
            'agente_id' => ['nullable', 'exists:users,id'],
            'tipo' => ['sometimes', 'required', Rule::in(['venta', 'alquiler', 'reserva'])],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'monto' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['sometimes', Rule::in(['ARS', 'USD'])],
            'indice_actualizacion' => ['nullable', Rule::in(['ICL', 'IPC'])],
            'notas' => ['nullable', 'string'],
        ];
    }
}
