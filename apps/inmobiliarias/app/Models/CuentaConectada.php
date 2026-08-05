<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaConectada extends Model
{
    use HasFactory;

    protected $table = 'cuentas_conectadas';

    protected $fillable = [
        'canal',
        'external_account_id',
        'external_account_name',
        'access_token',
        'scopes',
        'token_expira_en',
        'estado',
        'ultimo_error',
    ];

    protected function casts(): array
    {
        return [
            // §09: "nunca las credenciales de login del tenant, solo el
            // token" — y ese token va cifrado en reposo.
            'access_token' => 'encrypted',
            'scopes' => 'array',
            'token_expira_en' => 'datetime',
        ];
    }

    public function requiereReconexion(): bool
    {
        return $this->estado === 'requiere_reconexion';
    }

    public function marcarRequiereReconexion(string $motivo): void
    {
        $this->forceFill([
            'estado' => 'requiere_reconexion',
            'ultimo_error' => $motivo,
        ])->save();
    }

    /**
     * §09: "un job diario revisa vencimientos próximos" — el margen es a
     * propósito más amplio que "ya venció": avisar antes de que se corte
     * el canal, no después.
     */
    public function estaPorVencer(int $diasDeMargen = 7): bool
    {
        return $this->token_expira_en !== null
            && $this->token_expira_en->isFuture()
            && now()->diffInDays($this->token_expira_en, false) <= $diasDeMargen;
    }
}
