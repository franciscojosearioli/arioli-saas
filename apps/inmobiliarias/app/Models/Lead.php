<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'agente_id',
        'cliente_id',
        'propiedad_id',
        'nombre',
        'email',
        'telefono',
        'origen',
        'estado',
        'interes',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'interes' => 'array',
        ];
    }

    public function agente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agente_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    /**
     * LeadConvertido (§05): pasa a Cliente en una Operación. Acá, sin
     * Operación todavía (Fase 2), solo vincula el Lead a un Cliente y
     * marca el estado.
     */
    public function convertir(Cliente $cliente): void
    {
        $this->update([
            'cliente_id' => $cliente->id,
            'estado' => 'convertido',
        ]);
    }
}
