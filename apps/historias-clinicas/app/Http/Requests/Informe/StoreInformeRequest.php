<?php

namespace App\Http\Requests\Informe;

use App\Models\Informe;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInformeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('informe_create');
    }

    public function rules()
    {
        return [
            'redaccion_informe' => [
                'string',
                'nullable',
            ],
        ];
    }
}