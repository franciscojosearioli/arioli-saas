<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'agendas';

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin'    => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    protected $fillable = [
        'paciente_id',
        'especialidad_id',
        'profesional_tipo',
        'profesional_id',
        'profesional_externo_nombre',
        'profesional_externo_email',
        'modalidad',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'motivo',
        'estado',
        'notas',
        'link_virtual',
        'recordatorio_enviado',
        'creado_por',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function getColorEstado(): string
    {
        return match ($this->estado) {
            'pendiente'  => '#f0ad4e',
            'confirmado' => '#28a745',
            'cancelado'  => '#dc3545',
            'realizado'  => '#6c757d',
            default      => '#007bff',
        };
    }

    public function getEmailNotificacion(): ?string
    {
        if ($this->profesional_tipo === 'registrado' && $this->profesional) {
            return $this->profesional->email;
        }
        return $this->profesional_externo_email;
    }

    public function getNombreProfesional(): string
    {
        if ($this->profesional_tipo === 'registrado' && $this->profesional) {
            return $this->profesional->name;
        }
        return $this->profesional_externo_nombre ?? 'Sin asignar';
    }

    public static function estadosLabels(): array
    {
        return [
            'pendiente'  => 'Pendiente',
            'confirmado' => 'Confirmado',
            'cancelado'  => 'Cancelado',
            'realizado'  => 'Realizado',
        ];
    }

    public static function modalidadesLabels(): array
    {
        return [
            'presencial' => 'Presencial',
            'virtual'    => 'Virtual',
        ];
    }
}
