<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncDesarrolloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'max:255'],
            'desarrollo_id' => ['required', 'integer'],
            'constructora_id' => ['nullable', 'integer'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(['loteo', 'barrio_cerrado', 'edificio', 'emprendimiento'])],
            'descripcion' => ['nullable', 'string'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'plano_maestro' => ['nullable', 'string', 'max:255'],
            // WKT del polígono general — se guarda aparte, nunca por
            // asignación directa del atributo (ver Desarrollo::guardarUbicacion).
            'ubicacion_wkt' => ['nullable', 'string', 'regex:/^POLYGON\(\([-\d.,\s]+\)\)$/i'],
        ];
    }
}
