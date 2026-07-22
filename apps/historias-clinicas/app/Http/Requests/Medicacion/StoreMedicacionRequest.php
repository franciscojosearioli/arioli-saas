<?php

namespace App\Http\Requests\Medicacion;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicacionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('medicacion_create');
    }

    public function rules()
    {
        return [
            'paciente_id' => ['required', 'exists:pacientes,id'],
        ];
    }
}