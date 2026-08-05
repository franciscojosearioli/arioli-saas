<?php

namespace App\Http\Requests;

use App\Models\Contrato;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Contrato::class);
    }

    public function rules(): array
    {
        return [
            'operacion_id' => ['required', 'exists:operaciones,id'],
            'estado' => ['sometimes', Rule::in(['borrador', 'firmado', 'vencido', 'renovado'])],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after:fecha_inicio'],
            'clausulas' => ['nullable', 'string'],
            'notas' => ['nullable', 'string'],
        ];
    }
}
