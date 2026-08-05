<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropiedadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('propiedad'));
    }

    public function rules(): array
    {
        return [
            'desarrollo_id' => ['nullable', 'exists:desarrollos,id'],
            'propietario_id' => ['nullable', 'exists:clientes,id'],
            'tipo' => ['sometimes', 'required', Rule::in([
                'loteo', 'casa', 'departamento', 'local', 'oficina', 'galpon', 'campo', 'cochera',
            ])],
            'titulo' => ['sometimes', 'required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['sometimes', Rule::in(['disponible', 'reservado', 'vendido', 'alquilado'])],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['sometimes', Rule::in(['ARS', 'USD'])],
            'superficie_cubierta' => ['nullable', 'numeric', 'min:0'],
            'superficie_total' => ['nullable', 'numeric', 'min:0'],
            'ambientes' => ['nullable', 'integer', 'min:0', 'max:255'],
            'dormitorios' => ['nullable', 'integer', 'min:0', 'max:255'],
            'banos' => ['nullable', 'integer', 'min:0', 'max:255'],
            'cocheras' => ['nullable', 'integer', 'min:0', 'max:255'],
            'manzana' => [
                'nullable', 'string', 'max:50',
                Rule::unique('propiedades')->ignore($this->route('propiedad'))->where(fn ($q) => $q
                    ->where('desarrollo_id', $this->input('desarrollo_id'))
                    ->where('numero_lote', $this->input('numero_lote'))),
            ],
            'numero_lote' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'servicios' => ['nullable', 'array'],
            'servicios.*' => ['string', 'max:100'],
            'caracteristicas_destacadas' => ['nullable', 'array'],
            'caracteristicas_destacadas.*' => ['string', 'max:100'],
            'atributos' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'manzana.unique' => 'Ya existe un lote con ese número en esa manzana, dentro de este desarrollo.',
        ];
    }
}
