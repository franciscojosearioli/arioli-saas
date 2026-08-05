<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('contrato'));
    }

    public function rules(): array
    {
        return [
            'estado' => ['sometimes', Rule::in(['borrador', 'firmado', 'vencido', 'renovado'])],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after:fecha_inicio'],
            'clausulas' => ['nullable', 'string'],
            'notas' => ['nullable', 'string'],
        ];
    }
}
