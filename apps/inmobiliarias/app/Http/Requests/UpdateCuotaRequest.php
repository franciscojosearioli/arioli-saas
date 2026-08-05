<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('cuota'));
    }

    public function rules(): array
    {
        return [
            'fecha_vencimiento' => ['sometimes', 'required', 'date'],
            'monto' => ['sometimes', 'required', 'numeric', 'min:0'],
            'moneda' => ['sometimes', Rule::in(['ARS', 'USD'])],
        ];
    }
}
