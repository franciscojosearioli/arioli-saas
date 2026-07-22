<?php

namespace App\Http\Requests\Informe;

use App\Models\InformeTipo;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInformeTipoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('informe_tipo_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}