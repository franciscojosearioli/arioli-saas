<?php

// Providers registrados para la sección CRM del dashboard (mismo patrón de registro
// que settings_tabs.php / contract_placeholders.php). Agregar un widget nuevo es
// crear la clase + sumarla acá, sin tocar la vista ni el route de /dashboard.
//
// Dominio/Hosting/SSL/Cloudflare deliberadamente NO están acá — son monitoreo
// operativo de infraestructura, no señal de negocio; quedan disponibles para
// una futura pantalla de "Salud de infraestructura" pero no compiten por
// espacio en el dashboard principal (ver reorganización 2026-07-28).
// ChargeDashboardProvider tampoco: sus métricas de plata se promovieron a la
// sección "Finanzas" (cards grandes en admin.dashboard), no al grid genérico.
return [
    \App\Services\Dashboard\ClientDashboardProvider::class,
    \App\Services\Dashboard\ContractDashboardProvider::class,
    \App\Services\Dashboard\QuoteDashboardProvider::class,
];
