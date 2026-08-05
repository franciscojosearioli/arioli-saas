<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Cliente::class);
    }

    public function rules(): array
    {
        return [
            'tipo_persona' => ['required', Rule::in(['fisica', 'juridica'])],
            'nombre' => ['required', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'notas' => ['nullable', 'string'],
        ];
    }
}
