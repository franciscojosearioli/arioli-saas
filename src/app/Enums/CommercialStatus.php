<?php

namespace App\Enums;

enum CommercialStatus: string
{
    case Prospecto = 'prospecto';
    case PresupuestoEnviado = 'presupuesto_enviado';
    case Activo = 'activo';
    case Suspendido = 'suspendido';
    case ExCliente = 'ex_cliente';

    public function label(): string
    {
        return match ($this) {
            self::Prospecto => 'Prospecto',
            self::PresupuestoEnviado => 'Presupuesto enviado',
            self::Activo => 'Activo',
            self::Suspendido => 'Suspendido',
            self::ExCliente => 'Ex cliente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Prospecto => 'gray',
            self::PresupuestoEnviado => 'amber',
            self::Activo => 'green',
            self::Suspendido => 'red',
            self::ExCliente => 'gray',
        };
    }
}
