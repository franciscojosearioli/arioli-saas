<?php

namespace App\Http\Requests;

use App\Models\Pago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Pago::class);
    }

    public function rules(): array
    {
        return [
            'cuota_id' => ['required', 'exists:cuotas,id'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'fecha' => ['required', 'date'],
            'medio_pago' => ['required', Rule::in(['efectivo', 'transferencia', 'cheque', 'tarjeta', 'otro'])],
            'notas' => ['nullable', 'string'],
        ];
    }
}
