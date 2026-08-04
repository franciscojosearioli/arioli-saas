<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Roles base de un tenant, definidos en §07 del Artifact de
     * arquitectura. Los permisos concretos se suman módulo por módulo a
     * medida que cada fase los introduce — acá solo se siembra la
     * jerarquía de roles; no hay permisos que inventar todavía porque no
     * hay módulos de negocio (eso es Fase 1 en adelante).
     */
    public function run(): void
    {
        collect([
            'admin',          // Owner/Admin — todo el tenant
            'agente',         // Agente/Vendedor — sus propios leads y operaciones
            'administrativo', // Finanzas: cuotas, pagos, caja
            'solo-lectura',   // Dashboard y reportes
            'portal',         // Propietario/inquilino — sus propias operaciones
        ])->each(fn (string $role) => Role::findOrCreate($role, 'web'));
    }
}
