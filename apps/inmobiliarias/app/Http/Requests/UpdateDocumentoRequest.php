<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('documento'));
    }

    public function rules(): array
    {
        return [
            'tipo' => ['sometimes', Rule::in(['boleto', 'escritura', 'dni', 'comprobante', 'contrato', 'otro'])],
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ];
    }
}
