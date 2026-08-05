<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización real es el token compartido (middleware api.key,
        // ver bootstrap/app.php) — este endpoint no tiene usuarios propios.
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'max:255'],
            'propiedad_id' => ['required', 'integer'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['required', 'string', 'size:3'],
            'tipo_operacion' => ['nullable', Rule::in(['venta', 'alquiler', 'reserva'])],
            'tipo_propiedad' => ['required', 'string', 'max:30'],
            'estado' => ['required', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'superficie_cubierta' => ['nullable', 'numeric', 'min:0'],
            'superficie_total' => ['nullable', 'numeric', 'min:0'],
            'ambientes' => ['nullable', 'integer', 'min:0', 'max:255'],
            'dormitorios' => ['nullable', 'integer', 'min:0', 'max:255'],
            'banos' => ['nullable', 'integer', 'min:0', 'max:255'],
            'cocheras' => ['nullable', 'integer', 'min:0', 'max:255'],
            'servicios' => ['nullable', 'array'],
            'caracteristicas_destacadas' => ['nullable', 'array'],
            'nombre_desarrollo' => ['nullable', 'string', 'max:255'],
            'galeria' => ['nullable', 'array'],
        ];
    }
}
