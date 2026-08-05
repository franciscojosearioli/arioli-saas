<?php

namespace App\Http\Requests;

use App\Models\Operacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOperacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Operacion::class);
    }

    public function rules(): array
    {
        return [
            'propiedad_id' => ['required', 'exists:propiedades,id'],
            'agente_id' => ['nullable', 'exists:users,id'],
            'tipo' => ['required', Rule::in(['venta', 'alquiler', 'reserva'])],
            'fecha_inicio' => ['required', 'date'],
            'monto' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['sometimes', Rule::in(['ARS', 'USD'])],
            'indice_actualizacion' => ['nullable', Rule::in(['ICL', 'IPC'])],
            'notas' => ['nullable', 'string'],
        ];
    }
}
