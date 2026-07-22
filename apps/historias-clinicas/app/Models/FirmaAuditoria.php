<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirmaAuditoria extends Model
{
    protected $table = 'firma_auditorias';

    protected $fillable = [
        'informe_id', 'user_id', 'firmado_at', 'ip_address', 'version_documento',
    ];

    protected $casts = [
        'firmado_at' => 'datetime',
    ];

    public function informe()
    {
        return $this->belongsTo(Informe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
