<?php

namespace App\Http\Requests;

use App\Models\Cuota;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Cuota::class);
    }

    public function rules(): array
    {
        return [
            'operacion_id' => ['required', 'exists:operaciones,id'],
            'numero' => [
                'required', 'integer', 'min:1',
                Rule::unique('cuotas')->where(fn ($q) => $q->where('operacion_id', $this->input('operacion_id'))),
            ],
            'fecha_vencimiento' => ['required', 'date'],
            'monto' => ['required', 'numeric', 'min:0'],
            'moneda' => ['sometimes', Rule::in(['ARS', 'USD'])],
        ];
    }
}
