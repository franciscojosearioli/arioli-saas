<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Lead::class);
    }

    public function rules(): array
    {
        return [
            'agente_id' => ['nullable', 'exists:users,id'],
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'propiedad_id' => ['nullable', 'exists:propiedades,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'origen' => ['required', Rule::in(['storefront', 'formulario', 'whatsapp', 'referido', 'otro'])],
            'estado' => ['sometimes', Rule::in(['nuevo', 'contactado', 'calificado', 'convertido', 'perdido'])],
            'interes' => ['nullable', 'array'],
            'notas' => ['nullable', 'string'],
        ];
    }
}
