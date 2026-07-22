<?php

namespace App\Http\Requests\Medicacion;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicacionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('medicacion_edit');
    }

    public function rules()
    {
        return [
            'paciente_id' => ['exists:pacientes,id'],
        ];
    }
}