<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDesarrolloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('desarrollo'));
    }

    public function rules(): array
    {
        return [
            'constructora_id' => ['nullable', 'exists:constructoras,id'],
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'tipo' => ['sometimes', 'required', Rule::in(['loteo', 'barrio_cerrado', 'edificio', 'emprendimiento'])],
            'descripcion' => ['nullable', 'string'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'plano_maestro' => ['nullable', 'string', 'max:255'],
        ];
    }
}
