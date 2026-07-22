# Snapshot de estado — Checkpoint Etapa 4

Fecha: 2026-07-22
Tag git: `historias-clinicas-checkpoint-etapa4` (commit `d8b2e2e`)
Motivo: respaldo del estado real antes de entrar a Odontología con comportamiento propio (migraciones por componente, versionado, rollback) — punto de retorno documentado, no solo el código.

Acompaña a `2026-07-22_etapa4_historias_demo_schema.sql` (dump de esquema, sin datos, de `historias_demo`).

## `tenants` (vive en `historias_default`)

| tenant_key | database | status | last_migration_status |
|---|---|---|---|
| demo | historias_demo | activo | ok |
| default | historias_default | activo | ok |

## `capability_states` (historias_demo)

| capability_key | enabled | source |
|---|---|---|
| agenda | true | preset |
| consentimientos | true | preset |
| especialidades | true | preset |
| historia_clinica | true | preset |
| medicacion | true | preset |
| odontologia | true | preset |

## `componentes_instalados` (historias_demo)

| componente_key | instalado_en |
|---|---|
| salud_mental | 2026-07-22 12:49:07 |
| odontologia | 2026-07-22 13:48:43 |

`historias_default` tiene solo `salud_mental` instalado (Odontología fue prueba de mecanismo, no se instaló ahí a propósito — ver documento principal).

## `componente_extensiones` (historias_demo)

| componente_key | extension_key | version |
|---|---|---|
| odontologia | App\Modules\Odontologia\OdontologiaExtension | 1.0.0 |

## `field_visibility` (historias_demo) — entidad `paciente`

| campo | visible | origen |
|---|---|---|
| datos_adicionales | true | preset |
| educacion | true | preset |
| familia | true | preset |
| historial_tratamientos | true | preset |
| laboral | true | preset |
| problematica | true | preset |

Todas en `preset` — ningún admin tocó manualmente ninguna en este punto (si en el futuro alguna aparece en `origen=manual`, significa que se editó a mano después de este snapshot).

## Módulos y Componentes registrados en código en este punto

- `ModuleDefinition` (`config/platform.php`): EspecialidadesModule, AgendaModule, ConsentimientosModule, MedicacionModule, InformesModule.
- `Componente` (`config/platform/componentes.php`): salud_mental, odontologia.
