<?php

namespace App\Http\Requests\Informe;

use App\Models\InformeTipo;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyInformeTipoRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('informe_tipo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:informes_tipos,id',
        ];
    }
}