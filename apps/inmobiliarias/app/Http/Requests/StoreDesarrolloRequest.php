<?php

namespace App\Http\Requests;

use App\Models\Desarrollo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDesarrolloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Desarrollo::class);
    }

    public function rules(): array
    {
        return [
            'constructora_id' => ['nullable', 'exists:constructoras,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(['loteo', 'barrio_cerrado', 'edificio', 'emprendimiento'])],
            'descripcion' => ['nullable', 'string'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'plano_maestro' => ['nullable', 'string', 'max:255'],
        ];
    }
}
