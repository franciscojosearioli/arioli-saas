<?php

namespace App\Http\Requests;

use App\Models\Constructora;
use Illuminate\Foundation\Http\FormRequest;

class StoreConstructoraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Constructora::class);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ];
    }
}
