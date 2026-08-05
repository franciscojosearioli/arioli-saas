<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePublicacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('publicacion'));
    }

    public function rules(): array
    {
        return [
            'destacada' => ['sometimes', 'boolean'],
            'destacada_hasta' => ['nullable', 'date'],
        ];
    }
}
