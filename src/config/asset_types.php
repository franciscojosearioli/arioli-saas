<?php

// Clases que implementan App\Contracts\AssetInterface. El scheduler de
// recordatorios (Etapa 4) itera esta lista sin if/else por tipo — agregar un
// activo nuevo (SSL, VPS, EmailAccount...) es una clase + una línea acá.

return [
    \App\Models\ClientDomain::class,
    \App\Models\Hosting::class,
    \App\Models\SslCertificate::class,
    \App\Models\CloudflareService::class,
];
