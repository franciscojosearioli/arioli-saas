<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;
use Stancl\Tenancy\Database\Models\Tenant;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos tenants y usuarios para crear tickets de prueba
        $tenants = Tenant::all();
        $admins = User::whereNull('tenant_id')->get();

        foreach ($tenants as $tenant) {
            $tenantUsers = User::where('tenant_id', $tenant->id)->get();

            if ($tenantUsers->count() > 0) {
                // Crear algunos tickets de prueba para cada tenant
                $ticketsData = [
                    [
                        'title' => 'No puedo acceder al sistema',
                        'description' => 'Cuando intento ingresar al sistema con mis credenciales, me aparece un error de "Usuario o contraseña incorrectos". Ya intenté resetear la contraseña pero sigue sin funcionar.',
                        'status' => 'abierto',
                        'priority' => 'alta',
                        'category' => 'tecnico',
                        'user_id' => $tenantUsers->first()->id,
                        'tenant_id' => $tenant->id,
                    ],
                    [
                        'title' => 'Error al guardar los datos',
                        'description' => 'Cada vez que intento guardar un nuevo lote en el sistema, me aparece un error 500. Esto empezó a pasar desde ayer y no puedo cargar nada nuevo.',
                        'status' => 'en_progreso',
                        'priority' => 'critica',
                        'category' => 'tecnico',
                        'user_id' => $tenantUsers->first()->id,
                        'tenant_id' => $tenant->id,
                        'assigned_to' => $admins->count() > 0 ? $admins->first()->id : null,
                    ],
                    [
                        'title' => 'Consulta sobre facturación',
                        'description' => 'Hola, tengo una duda sobre mi factura del mes pasado. Me cobraron $5000 pero según mi plan debería ser $3500. ¿Podrían revisar esto?',
                        'status' => 'esperando_cliente',
                        'priority' => 'media',
                        'category' => 'facturacion',
                        'user_id' => $tenantUsers->first()->id,
                        'tenant_id' => $tenant->id,
                        'assigned_to' => $admins->count() > 1 ? $admins->skip(1)->first()->id : null,
                        'admin_notes' => 'Se revisó la factura, efectivamente había un error en el cálculo. Se emitió una nota de crédito.',
                    ],
                    [
                        'title' => '¿Cómo configurar las notificaciones por email?',
                        'description' => 'Me gustaría recibir notificaciones por email cuando hay pagos vencidos, pero no encuentro la opción en el panel. ¿Podrían ayudarme a configurar esto?',
                        'status' => 'resuelto',
                        'priority' => 'baja',
                        'category' => 'configuracion',
                        'user_id' => $tenantUsers->first()->id,
                        'tenant_id' => $tenant->id,
                        'resolved_at' => now()->subDays(2),
                        'assigned_to' => $admins->count() > 0 ? $admins->first()->id : null,
                        'admin_notes' => 'Se le explicó al usuario cómo configurar las notificaciones desde el panel de configuración.',
                    ],
                    [
                        'title' => 'Sistema muy lento',
                        'description' => 'El sistema está muy lento desde hace unos días. Las páginas tardan mucho en cargar y a veces se queda colgado.',
                        'status' => 'cerrado',
                        'priority' => 'media',
                        'category' => 'tecnico',
                        'user_id' => $tenantUsers->first()->id,
                        'tenant_id' => $tenant->id,
                        'assigned_to' => $admins->count() > 0 ? $admins->first()->id : null,
                        'admin_notes' => 'Se optimizó la base de datos y se resolvieron los problemas de performance.',
                        'created_at' => now()->subWeek(),
                        'updated_at' => now()->subDays(5),
                    ],
                ];

                foreach ($ticketsData as $ticketData) {
                    Ticket::create($ticketData);
                }
            }

            // Solo crear tickets para el primer tenant para no llenar demasiado la BD
            break;
        }
    }
}