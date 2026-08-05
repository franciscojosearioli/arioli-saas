<?php

namespace App\Http\Requests;

use App\Models\Configuracion;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Configuracion::actual());
    }

    public function rules(): array
    {
        return [
            'comision_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sitio_web_url' => ['nullable', 'url', 'max:255'],
            'sitio_web_api_key' => ['nullable', 'string', 'max:255'],
        ];
    }
}
