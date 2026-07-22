<?php

namespace App\Http\Requests\Informe;

use App\Models\Informe;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyInformeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('informe_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:informes,id',
        ];
    }
}