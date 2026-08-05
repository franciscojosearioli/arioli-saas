<?php

namespace App\Http\Requests;

use App\Models\ArqueoCaja;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArqueoCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ArqueoCaja::class);
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date', Rule::unique('arqueos_caja', 'fecha')],
            'monto_contado' => ['required', 'numeric', 'min:0'],
            'notas' => ['nullable', 'string'],
        ];
    }
}
