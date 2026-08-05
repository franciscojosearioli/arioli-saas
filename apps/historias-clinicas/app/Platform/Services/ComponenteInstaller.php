<?php

namespace App\Platform\Services;

use App\Models\InformeTipo;
use App\Models\PlantillaDocumento;
use App\Models\PlantillaDocumentoVersion;
use App\Platform\Contracts\Services\CapabilityInstallerContract;
use App\Platform\Contracts\Services\ComponenteInstallerContract;
use App\Platform\Contracts\Services\ExtensionInstallerContract;
use App\Platform\Contracts\Services\FieldVisibilityInstallerContract;
use App\Platform\DTO\Componente;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Orquestador. Un Componente solo declara datos (capabilities,
 * fieldVisibilitySeed, tiposDocumentoSeed, configuracionInicial) — toda la
 * inteligencia de CÓMO se aplica vive acá, delegada a sub-instaladores.
 *
 * aplicarTiposDocumento()/aplicarConfiguracionInicial() quedan como métodos
 * privados (no clases propias) a propósito: ningún Componente real declara
 * todavía esos dos seeds con contenido (salud_mental usa solo
 * fieldVisibilitySeed) — se promueven a clases separadas recién cuando un
 * segundo consumidor real las necesite, mismo criterio que ya se aplicó
 * con FieldVisibilityInstaller y CapabilityInstaller.
 */
class ComponenteInstaller implements ComponenteInstallerContract
{
    public function __construct(
        private readonly CapabilityInstallerContract $capabilityInstaller,
        private readonly FieldVisibilityInstallerContract $fieldVisibilityInstaller,
        private readonly ExtensionInstallerContract $extensionInstaller,
    ) {
    }

    public function instalar(array $componentKeys): void
    {
        $catalogo = config('componentes', []);

        foreach ($componentKeys as $key) {
            if (! isset($catalogo[$key])) {
                continue;
            }

            DB::table('componentes_instalados')->updateOrInsert(
                ['componente_key' => $key],
                ['instalado_en' => now(), 'updated_at' => now(), 'created_at' => now()]
            );
        }

        /** @var Collection<int, Componente> $instalados */
        $instalados = DB::table('componentes_instalados')
            ->pluck('componente_key')
            ->map(fn (string $key) => $catalogo[$key] ?? null)
            ->filter();

        $this->capabilityInstaller->aplicar(
            $instalados->flatMap(fn (Componente $c) => $c->capabilities)->unique()->values()->all()
        );

        $this->capabilityInstaller->deshabilitar(
            $instalados->flatMap(fn (Componente $c) => $c->capabilitiesDisabled)->unique()->values()->all()
        );

        $presetKeys = $instalados->flatMap(fn (Componente $c) => $c->fieldVisibilitySeed)->unique();
        $fieldVisibilitySeeds = $presetKeys
            ->flatMap(fn (string $presetKey) => config('field_visibility_presets.' . $presetKey, []))
            ->all();
        $this->fieldVisibilityInstaller->aplicar($fieldVisibilitySeeds);

        $this->aplicarTiposDocumento($instalados->flatMap(fn (Componente $c) => $c->tiposDocumentoSeed)->all());
        $this->aplicarConfiguracionInicial($instalados->flatMap(fn (Componente $c) => $c->configuracionInicial)->all());

        foreach ($instalados as $componente) {
            if ($componente->extension !== null) {
                $this->extensionInstaller->aplicar($componente->key, $componente->extension);
            }
        }
    }

    /**
     * Shape esperado por entrada: tipo_nombre, tipo_modulo_key,
     * plantilla_nombre, plantilla_codigo, contenido. Idempotente por
     * nombre/código. Sin consumidor real todavía (ver clase docblock).
     */
    private function aplicarTiposDocumento(array $seeds): void
    {
        foreach ($seeds as $seed) {
            $tipo = InformeTipo::firstOrCreate(
                ['name' => $seed['tipo_nombre']],
                ['modulo_key' => $seed['tipo_modulo_key'] ?? 'historia_clinica', 'activo' => true]
            );

            $plantilla = PlantillaDocumento::firstOrCreate(
                ['codigo' => $seed['plantilla_codigo']],
                ['tipo_documento_id' => $tipo->id, 'nombre' => $seed['plantilla_nombre'], 'activo' => true]
            );

            if ($plantilla->versiones()->doesntExist()) {
                PlantillaDocumentoVersion::create([
                    'plantilla_documento_id' => $plantilla->id,
                    'version' => 1,
                    'contenido' => $seed['contenido'],
                    'vigente_desde' => now(),
                ]);
            }
        }
    }

    /**
     * Solo completa campos NULL/vacíos de ConfiguracionSistema — nunca pisa
     * lo que el admin ya cargó. Sin consumidor real todavía (ver clase
     * docblock).
     */
    private function aplicarConfiguracionInicial(array $config): void
    {
        if (empty($config)) {
            return;
        }

        $actual = DB::table('configuracion_sistema')->first();

        if (! $actual) {
            return;
        }

        $update = [];
        foreach ($config as $campo => $valor) {
            if (empty($actual->$campo)) {
                $update[$campo] = $valor;
            }
        }

        if ($update) {
            DB::table('configuracion_sistema')->update($update);
        }
    }
}
