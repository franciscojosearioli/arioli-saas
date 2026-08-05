<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Documento;
use App\Models\Operacion;
use App\Models\Propiedad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentoRequest extends FormRequest
{
    // Whitelist explícita — nunca aceptar un nombre de clase PHP crudo del
    // cliente para un campo polimórfico (documentable_type).
    public const TIPOS_DOCUMENTABLE = [
        'propiedad' => Propiedad::class,
        'operacion' => Operacion::class,
        'contrato' => Contrato::class,
        'cliente' => Cliente::class,
    ];

    public function authorize(): bool
    {
        return $this->user()->can('create', Documento::class);
    }

    public function rules(): array
    {
        return [
            'documentable_type' => ['required', Rule::in(array_keys(self::TIPOS_DOCUMENTABLE))],
            'documentable_id' => ['required', 'integer'],
            'tipo' => ['required', Rule::in(['boleto', 'escritura', 'dni', 'comprobante', 'contrato', 'otro'])],
            'nombre' => ['required', 'string', 'max:255'],
            'archivo' => ['required', 'string', 'max:255'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ];
    }

    public function documentableClass(): string
    {
        return self::tipoAClase($this->input('documentable_type'));
    }

    public static function tipoAClase(string $tipo): ?string
    {
        return self::TIPOS_DOCUMENTABLE[$tipo] ?? null;
    }
}
