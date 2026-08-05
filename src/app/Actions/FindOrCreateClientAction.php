<?php

namespace App\Actions;

use App\Enums\CommercialStatus;
use App\Enums\ContactRole;
use App\Models\Client;
use App\Models\ClientContact;

/**
 * Busca un Client existente por el email de contacto, o crea uno nuevo.
 * Un Client puede tener varias Licenses/Hostings/Tenants (ej. Municipalidad
 * con Loteos + hosting) — por eso se busca antes de crear. Usada por
 * Admin\TenantController y HostingOrderController.
 */
class FindOrCreateClientAction
{
    public function execute(string $name, string $email): Client
    {
        $existingContact = ClientContact::where('email', $email)->first();

        if ($existingContact) {
            return $existingContact->client;
        }

        $client = Client::create([
            'name'              => $name,
            'commercial_status' => CommercialStatus::Activo,
        ]);

        ClientContact::create([
            'client_id'  => $client->id,
            'name'       => $name,
            'email'      => $email,
            'role'       => ContactRole::Dueno,
            'is_primary' => true,
        ]);

        return $client;
    }
}
