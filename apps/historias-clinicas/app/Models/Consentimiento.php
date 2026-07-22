<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Consentimiento extends Model
{
    use SoftDeletes;

    protected $table = 'consentimientos';

    protected $fillable = [
        'paciente_id',
        'tipo_id',
        'firmado_paciente',
        'firmado_paciente_at',
        'firma_paciente_img',
        'token',
        'token_expires_at',
        'token_email',
        'firmado_profesional',
        'firmado_profesional_at',
        'firmado_por_id',
        'pdf_path',
    ];

    protected $casts = [
        'firmado_paciente'     => 'boolean',
        'firmado_profesional'  => 'boolean',
        'firmado_paciente_at'  => 'datetime',
        'firmado_profesional_at' => 'datetime',
        'token_expires_at'     => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function tipo()
    {
        return $this->belongsTo(TipoConsentimiento::class, 'tipo_id');
    }

    public function firmadoPor()
    {
        return $this->belongsTo(User::class, 'firmado_por_id');
    }

    public function isFullySigned(): bool
    {
        if ($this->tipo && $this->tipo->requiere_firma_profesional) {
            return $this->firmado_paciente && $this->firmado_profesional;
        }
        return (bool) $this->firmado_paciente;
    }

    public function tokenValido(): bool
    {
        return $this->token && $this->token_expires_at && $this->token_expires_at->isFuture();
    }

    public function pdfUrl(): ?string
    {
        if ($this->pdf_path && Storage::disk('public')->exists($this->pdf_path)) {
            return asset('storage/' . $this->pdf_path);
        }
        return null;
    }
}
