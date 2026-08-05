<?php

namespace App\Platform\DTO;

use App\Platform\Contracts\ComponenteExtension;

/**
 * Puramente descriptivo — no sabe cómo se instala, no toca la base, no
 * ejecuta SQL. Toda la inteligencia vive en ComponenteInstaller y sus
 * sub-instaladores. Ver docs/ARQUITECTURA_MODULAR.md, Etapa 3.
 *
 * Etapa 7.1 (ver docs/CATALOGO_COMPONENTES.md): gana los campos que hacen
 * de esta clase el catálogo comercial además del técnico — antes solo
 * describía "qué le hace esto a un tenant al instalarse", ahora también
 * "cómo se vende". Todos los campos nuevos son opcionales con default que
 * preserva el comportamiento de cada Componente ya declarado: nada cambió
 * de comportamiento por agregarlos.
 */
final class Componente
{
    /**
     * @param ?string $categoria 'especialidad'|'administrativo'|'comunicacion'|'negocio'|null.
     *        Nullable a propósito: no todo lo que existe en la plataforma se vende como
     *        Componente aparte (ver docs/CATALOGO_COMPONENTES.md sección 4.1-4.2).
     * @param bool $core si es una capacidad que todo perfil necesita — hoy ningún Componente
     *        real declarado acá es core=true (Odontología y Salud Mental son ambos opcionales).
     *        El caso real de "Núcleo con excepción" (Medicación, Consentimientos) no tiene fila
     *        propia en este catálogo — son capabilities del Núcleo que un Componente apaga vía
     *        $capabilitiesDisabled, no Componentes en sí. Ver docs/CATALOGO_COMPONENTES.md 7.
     * @param bool $demo si puede ofrecerse en el portal público de demos (/demo).
     * @param bool $contratable si puede contratarse (checkout, alta de cliente real).
     * @param string[] $dependencias keys de otros Componentes que este necesita. Declarativo
     *        solamente — ComponenteInstaller no valida dependencias todavía (no hay ningún caso
     *        real hoy que las necesite); se activa el enforcement cuando aparezca ese caso.
     * @param string[] $capabilities
     * @param string[] $capabilitiesDisabled capabilities encendidas por defecto (ej. 'recetas') que
     *        este Componente apaga explícitamente al instalarse — Etapa post-6.5, primer caso real:
     *        Odontología no necesita Recetas. Mismo respeto por source='manual' que $capabilities.
     * @param string[] $fieldVisibilitySeed claves de preset en config/platform/field_visibility_presets.php
     * @param array $tiposDocumentoSeed
     * @param array $configuracionInicial
     * @param NavigationItem[] $navegacionSeed ítems de menú que aporta este Componente cuando está instalado
     */
    public function __construct(
        public readonly string $key,
        public readonly string $nombre,
        public readonly string $descripcion = '',
        public readonly ?string $categoria = null,
        public readonly bool $core = false,
        public readonly bool $demo = true,
        public readonly bool $contratable = true,
        public readonly array $dependencias = [],
        public readonly array $capabilities = [],
        public readonly array $capabilitiesDisabled = [],
        public readonly array $fieldVisibilitySeed = [],
        public readonly array $tiposDocumentoSeed = [],
        public readonly array $configuracionInicial = [],
        public readonly ?ComponenteExtension $extension = null,
        public readonly array $navegacionSeed = [],
    ) {
    }
}
