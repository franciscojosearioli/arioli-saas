<?php

namespace App\Http\Requests\Paciente;

use App\Models\Paciente;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdatePacienteRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('paciente_edit');
    }

    public function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            
        ];
    }
}