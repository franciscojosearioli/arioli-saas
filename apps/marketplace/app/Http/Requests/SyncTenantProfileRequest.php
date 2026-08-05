<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncTenantProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'max:255'],
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
