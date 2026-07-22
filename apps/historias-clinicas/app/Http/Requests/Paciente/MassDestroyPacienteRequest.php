<?php

namespace App\Http\Requests\Paciente;

use App\Models\Paciente;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyPacienteRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('paciente_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:pacientes,id',
        ];
    }
    
}