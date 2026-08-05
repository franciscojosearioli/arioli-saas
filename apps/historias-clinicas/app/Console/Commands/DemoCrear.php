<?php

namespace App\Console\Commands;

use App\Platform\Contracts\Services\ProvisionDemoServiceContract;
use Illuminate\Console\Command;

/**
 * Etapa 6.3.1 + 6.4 (ver docs/ARQUITECTURA_MODULAR.md) — envoltorio CLI
 * fino sobre ProvisionDemoService: el caso de uso completo (crear tenant,
 * migrar, seed, Perfil, activar) vive en el servicio, no acá, porque
 * ahora también lo dispara el flujo público HTTP (Etapa 6.4).
 */
class DemoCrear extends Command
{
    protected $signature = 'demo:crear
        {perfil : Clave de un Perfil en config/platform/perfiles.php}
        {--horas=24 : Horas hasta expirar, desde que queda activa}';

    protected $description = 'Provisiona una DemoInstance nueva (manual, Etapa 6.3.1)';

    public function __construct(private ProvisionDemoServiceContract $provisionDemoService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $perfilKey = $this->argument('perfil');

        if (! config('perfiles.' . $perfilKey)) {
            $this->error("Perfil '{$perfilKey}' no existe en config/platform/perfiles.php.");
            return self::FAILURE;
        }

        $demo = $this->provisionDemoService->provisionar(
            $perfilKey,
            null,
            null,
            (int) $this->option('horas')
        );

        if ($demo->status === 'error') {
            $this->error("DemoInstance #{$demo->id} quedó en 'error': {$demo->error_message}");
            return self::FAILURE;
        }

        $this->info("DemoInstance #{$demo->id} activa: tenant '{$demo->tenant_key}', expira " . $demo->expires_at->toDateTimeString() . '.');

        return self::SUCCESS;
    }
}
