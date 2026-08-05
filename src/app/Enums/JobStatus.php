<?php

namespace App\Enums;

enum JobStatus: string
{
    case Presupuestado = 'presupuestado';
    case Aprobado = 'aprobado';
    case EnProgreso = 'en_progreso';
    case Completado = 'completado';
    case Facturado = 'facturado';
    case Cancelado = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Presupuestado => 'Presupuestado',
            self::Aprobado => 'Aprobado',
            self::EnProgreso => 'En progreso',
            self::Completado => 'Completado',
            self::Facturado => 'Facturado',
            self::Cancelado => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Presupuestado => 'gray',
            self::Aprobado => 'amber',
            self::EnProgreso => 'amber',
            self::Completado => 'green',
            self::Facturado => 'green',
            self::Cancelado => 'red',
        };
    }
}
