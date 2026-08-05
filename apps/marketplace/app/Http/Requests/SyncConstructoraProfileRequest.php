<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncConstructoraProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'max:255'],
            'constructora_id' => ['required', 'integer'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
