<?php

namespace App\Http\Requests\Medicacion;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMedicacionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('medicacion_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:medicaciones,id',
        ];
    }
    
}