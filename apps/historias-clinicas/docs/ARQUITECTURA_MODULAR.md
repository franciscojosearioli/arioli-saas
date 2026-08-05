# Arquitectura de plataforma modular — Documento de diseño (v8)

Estado: **diseño cerrado, Fase 0 implementada y deployada en producción** (`historias_default`, `historias_demo`). A partir de acá los ajustes vienen de la implementación real, no de una nueva ronda de diseño en el papel.

**Documento hermano**: `docs/CATALOGO_COMPONENTES.md` — ordenamiento conceptual de producto (qué es Núcleo, qué es Componente, qué es roadmap, cómo se arman los perfiles comerciales). Este documento (`ARQUITECTURA_MODULAR.md`) es el registro técnico de *cómo* se construyó cada pieza; el catálogo es el mapa de negocio de *qué* pertenece a la plataforma. Ninguno reemplaza al otro.

## Fase 0 — hallazgos de la revisión de código (post-implementación)

Con código real corriendo en producción aparecieron 2 correcciones concretas al diseño de v7, exactamente el tipo de brecha entre "lo escrito" y "lo implementado" que conviene cazar temprano:

1. **`ModuleDefinition::replaces()` y `optionalDependencies()` se sacaron de la interfaz.** Estaban declarados desde v3 pero `PlatformRegistry::register()` nunca los leyó — ningún módulo real los necesitó. Se sacan ahora; se reintroducen el día que un módulo real necesite esa semántica (el caso que los motivó originalmente, "Portal v2 reemplaza Portal clásico", no está planeado todavía).
2. **Registro de módulos movido a `config/platform.php`** en vez de hardcodeado en `PlatformServiceProvider::register()`. Agregar un módulo nuevo es agregar una línea al array de config, no editar el provider.

Deliberadamente **no** se tocó (evaluado y descartado):

- **Índice sobre `capability_states.enabled`**: no se agrega. `capability_key` ya es `unique` (índice automático, cubre el lookup real). `enabled` es una columna booleana de baja cardinalidad sobre una tabla que nunca va a ser grande (acotada por cantidad de capabilities, no por datos de tenant) — un índice ahí no aporta nada que MySQL no resuelva igual de rápido con un scan completo de una tabla de este tamaño.
- **Dividir `PlatformRegistry`**: hoy tiene 3 responsabilidades (registro de módulos, resolución de capabilities, navegación) en ~110 líneas. No es un God Object todavía — el umbral real para dividirlo (backlog desde v3: `NavigationRegistry`/`WidgetRegistry`/etc.) es cuando se agreguen Publishers/Widgets/Extensions y pase a 5-6 responsabilidades, no antes.
- **Tests automatizados para `tenants:migrate`**: el proyecto no tiene aislamiento de DB para tests (`phpunit.xml` tiene las líneas de sqlite comentadas — `APP_ENV=testing` hoy cae sobre el mismo `DB_DATABASE` real de `.env`). Decisión de Francisco: seguir con verificación manual vía tinker por ahora, como el resto del proyecto — se retoma infraestructura de testing más adelante, no bloquea avanzar con más módulos.

## Segundo módulo: Agenda

`AgendaModule` registrado (capability `agenda`, permisos `agenda_access/create/show/edit/delete`, navegación → `panel.agenda.index`), mismo patrón que Especialidades: apunta al código existente (`app/Models/Agenda.php`, `Panel/AgendaController.php`) sin mover nada. Verificado end-to-end en `historias_demo`: ON/OFF de la capability controla el Gate correctamente, navegación resuelve ordenada por `orden` (Agenda antes que Especialidades).

`eventosEmitidos()`/`eventosEscuchados()` devuelven `[]` a propósito — `TurnoCancelado`/`TurnoConfirmado` son el candidato natural cuando el mecanismo de Domain Events se construya, pero eso requiere decidir desde dónde se disparan (`AgendaController@cambiarEstado`) y no existe todavía el catálogo en `PlatformRegistry`. No se construye por adelantado.

Duplicación Admin/Panel de Agenda (ya señalada en el análisis original, Fase 2 del roadmap): sigue pendiente, no bloquea que el módulo funcione hoy.

## Tercer módulo: Consentimientos — primer caso sin permiso propio

Consentimientos reveló una variación real que Especialidades/Agenda no tenían: el controller (`Panel/ConsentimientoController`) no usa un permiso propio, autoriza contra `paciente_edit`/`paciente_show` prestados. El mecanismo de `AuthGates` (capability atada a un `permission.capability_key`) no alcanza ahí porque no hay ninguna fila de `permissions` dueña de la feature.

Se resolvió con una pieza nueva, no anticipada en el diseño de v8: middleware de ruta **`EnsureCapabilityEnabled`** (alias `capability`, registrado en `Kernel.php`), aplicado directamente sobre las rutas de consentimientos en `routes/web.php` (`->middleware('capability:consentimientos')`) — gatea por capability de forma independiente de cualquier permiso. `ConsentimientosModule::permisos()` devuelve `[]` honestamente (no hay ninguno que declarar) y `contributions()` devuelve `[]` (no hay ítem de menú propio, se accede desde la ficha del paciente).

Verificado end-to-end vía HTTP real (no solo tinker): con la capability ON, `GET /firma-consentimiento/{token}` de un consentimiento de prueba devolvió `200 Firma de Consentimiento`; con la capability OFF, `404`; restaurada a ON, `200` de nuevo. Datos de prueba (Consentimiento + TipoConsentimiento descartables) borrados al final.

No son "dos mecanismos alternativos" — son **dos puntos de enforcement distintos**, cada uno respondiendo una pregunta distinta del modelo de 3 niveles (sección 4.5):

| Nivel | Pregunta que responde | Responsable |
|---|---|---|
| Acceso a una pantalla/ruta (¿existe la funcionalidad en este tenant?) | Capability | `EnsureCapabilityEnabled` (middleware de ruta) |
| Autorización del usuario (¿puede usarla?) | Permission | `Gate` / `AuthGates` |

En Especialidades/Agenda/Medicación ambos puntos coinciden en un solo lugar porque el permiso propio ya trae el `capability_key` colgado (el Gate resuelve las dos preguntas a la vez). En Consentimientos se separan porque no hay permiso propio del que colgar nada — el middleware de ruta responde sola la pregunta de "¿existe?", y `paciente_edit`/`paciente_show` siguen respondiendo "¿puede?" sin cambios.

**Backlog anotado, no implementado ahora** (evolución posible sugerida por Francisco): un método declarativo en `ModuleDefinition`, algo como `requiredCapabilities(): array` mapeando patrones de ruta (`'consentimientos.*' => 'consentimientos'`) para que el middleware se aplique automáticamente en vez de escribirlo a mano en `routes/web.php` por cada ruta. Tiene sentido cuando haya suficientes módulos con este patrón (3-4 casos reales) como para justificar la capa de indirección — con 1 solo caso (Consentimientos) escribirlo a mano es más simple y más legible que construir el mecanismo genérico.

## Checklist por módulo

| Verificación | Especialidades | Agenda | Consentimientos | Medicación |
|---|---|---|---|---|
| `ModuleDefinition` registrado | ✅ | ✅ | ✅ | ✅ |
| Capability creada | ✅ `especialidades` | ✅ `agenda` | ✅ `consentimientos` | ✅ `medicacion` |
| Permisos asociados | ✅ (4 propios) | ✅ (5 propios) | N/A — sin permiso propio, gating por middleware de ruta | ✅ (6 propios) |
| Navegación visible/oculta | ✅ | ✅ | N/A — sin ítem de menú, acceso contextual | ✅ |
| Gates funcionando | ✅ | ✅ | N/A — usa `EnsureCapabilityEnabled`, no Gate | ✅ |
| Módulo ON/OFF probado | ✅ (tinker + Gate) | ✅ (tinker + Gate) | ✅ (HTTP real: 200→404→200) | ✅ (tinker + Gate) |
| Seeder ejecutado (ambos tenants) | ✅ | ✅ | ✅ | ✅ |
| Sin regresiones | ✅ (permisos Core sin `capability_key` sin cambios) | ✅ | ✅ (datos de prueba limpiados) | ✅ |

## Tabla de madurez por módulo

| Módulo | Gate | Middleware Capability | Navegación | Seed | Documentos | Eventos |
|---|---|---|---|---|---|---|
| Especialidades | ✅ | — | ✅ | ✅ | — | — |
| Agenda | ✅ | — | ✅ | ✅ | — | 🔜 (`TurnoCancelado`, sin mecanismo de catálogo todavía) |
| Consentimientos | — | ✅ | — | ✅ | ✅ (PDF, firma) | — |
| Medicación | ✅ | — | ✅ | ✅ | — | — |
| Informes | ✅ | — | ✅ | ✅ | ✅ (motor de plantillas versionado) | — |

No es gestión de proyecto — es la señal rápida de si un módulo nuevo reutiliza infraestructura existente o necesita una pieza nueva (como pasó con Consentimientos → `EnsureCapabilityEnabled`).

## Quinto módulo: Informes — primera infraestructura de producto genuinamente nueva

A diferencia de los 4 módulos anteriores (que solo conectaban código ya existente a la plataforma sin agregar comportamiento), Informes requería construir algo que hoy no existe en absoluto: hasta este punto `informes.redaccion` es texto libre sin ningún concepto de plantilla ni variables.

Se construyó el motor completo de la sección 1.3-1.6 del diseño (v5-v8): `informes_tipos` ganó las columnas de política (`modulo_key`, `categoria`, `firma_requerida`, `roles_firmantes`, `visible_portal`, `permiso_codigo`, `activo`, `orden`, con backfill real de los 5 tipos existentes — nombres reales verificados en el servidor, no supuestos: "Informe Psicológico", "Informe Psiquiátrico", "Informe de Operador Terapéutico", "Informe Judicial", "Informe Clínico General"); tablas nuevas `plantillas_documento` (el concepto) y `plantilla_documento_versiones` (el contenido versionado); `informes.plantilla_documento_version_id` nullable.

**Decisión de alcance explícita**: el rename de clase `Informe`→`Documento` / `InformeTipo`→`TipoDocumento` (mencionado en el diseño como "a nivel de modelo, no de tabla física") **no se hizo** en este incremento — se extendieron los modelos existentes (`InformeTipo`, `Informe`) en vez de crear clases paralelas o renombrarlas, para no mezclar "construir el motor" con "renombrar todas las referencias a estas clases en el resto del código". `PlantillaDocumento` y `PlantillaDocumentoVersion` sí son modelos nuevos genuinos (no había nada que renombrar).

**Validado end-to-end con datos reales y descartables** (creados y borrados en la misma sesión, `historias_demo`):
1. `PlantillaDocumento::versionVigente()` resuelve la versión 1 recién creada.
2. `PlantillaDocumentoVersion::renderizar()` sustituye `{{paciente_nombre}}`/`{{diagnostico}}` correctamente.
3. Un `Informe` se creó pinneado a la versión 1 (`plantilla_documento_version_id`).
4. Se creó una versión 2 de la misma plantilla — `versionVigente()` pasó a resolver la versión 2.
5. **El informe viejo siguió apuntando a la versión 1**, sin cambios — la prueba central de que editar una plantilla no altera documentos históricos ya emitidos.
6. Las políticas (`firma_requerida`) se confirmaron distintas entre el tipo de prueba (`false`) y un tipo real existente (`true`) — quedan como datos consultables por tipo, listas para que un consumidor las use.

**Explícitamente NO conectado en este momento** (siguiente incremento, no mezclado con este para no combinar "el motor funciona" con "cambiar el flujo en vivo"): `Panel/InformeController` sigue guardando `redaccion` como texto libre, exactamente como antes — ningún profesional ve todavía un selector de plantilla al crear un informe. Conectar la UI real es el paso que falta antes de que las políticas (`firma_requerida`, `visible_portal`) tengan efecto real sobre el comportamiento del sistema, no solo sobre datos consultables.

## UI de Informes conectada al motor — subsistema cerrado

Se conectó `Panel/InformeController` (create) al motor construido en el paso anterior. Flujo real implementado y validado:

1. El profesional elige `tipo_id` → AJAX `GET informe/tipos/{tipo}/plantillas` (`plantillasPorTipo()`) devuelve las plantillas activas de ese tipo. Si el tipo no tiene ninguna (los 5 tipos reales de producción no tienen ninguna todavía), el selector de plantilla queda oculto y el formulario se comporta **exactamente igual que antes** — cero cambio de comportamiento hasta que un admin cargue una plantilla real.
2. Si elige una plantilla → AJAX `GET informe/plantillas/{plantilla}/preview` (`plantillaPreview()`) resuelve `versionVigente()`, renderiza con las variables ya completadas en el formulario (paciente, diagnóstico, fecha, profesional) y precarga el editor — el profesional puede seguir editando el resultado antes de guardar, no es un bloqueo.
3. Al guardar, `store()`/`update()` validan (`resolverPlantillaVersionId()`, defensa en profundidad) que la versión enviada realmente pertenezca al tipo seleccionado, y persisten `informes.plantilla_documento_version_id`.

**Validado end-to-end con datos reales y descartables** (no solo a nivel de modelo esta vez — a través de los métodos reales del controller, incluyendo `store()` completo vía un `StoreInformeRequest` fabricado):
- `plantillasPorTipo()` devuelve la plantilla real creada para el tipo de prueba.
- `plantillaPreview()` renderiza con el nombre real del paciente (`López, María José`), diagnóstico y fecha formateada en español — sustitución `{{variable}}` correcta.
- `store()` completo creó un Informe real con `plantilla_documento_version_id` persistido correctamente.
- **Prueba de la validación cruzada**: se envió la misma versión de plantilla con un `tipo_id` distinto al de su plantilla dueña → el sistema guardó `NULL` en vez de aceptar la versión ajena (protección contra formulario manipulado).

**Alcance explícitamente NO incluido en este incremento**:
- `Admin/InformeController` (el duplicado del lado admin) no recibió este cambio — sigue como estaba, consistente con tratar la duplicación Admin/Panel como deuda de Fase 2 ya señalada, no algo a resolver dos veces de paso.
- `edit.blade.php`/`update()` de Informes no recibió el selector de plantilla en la vista (sí recibió el guardado defensivo en el controller, por si se envía el campo desde otro lugar) — es la vista de creación la que se conectó primero por ser el punto de entrada principal del flujo.
- `firma_requerida` sigue siendo dato pasivo, consultable pero no exigido: el flujo de "guardar sin firmar / firmar ahora" no cambió — forzar la firma según la política del tipo es una decisión de negocio real (podría bloquear a profesionales que hoy redactan y firman después) que no se tomó sin confirmación explícita.
- `visible_portal` sigue sin consumidor — no hay Portal todavía (Fase 4).

Con esto, el subsistema de documentos queda cerrado en el sentido que pedía Francisco: motor + UI real + validación cruzada, listo para que Historia Clínica lo consuma sin tener que volver a tocarlo.

## Historia Clínica — Etapa 1: field_visibility sobre la ficha existente

Siguiendo el orden pedido (Etapa 1: llevar la ficha actual al mecanismo, sin agregar funcionalidades ni tocar Componentes todavía — eso es Etapa 2/3), se construyó:

- Tabla `field_visibility` (sección 1.7 del diseño, sin cambios respecto a lo ya especificado).
- Modelo `FieldVisibility` + `FieldVisibilityResolver` (`app/Platform/FieldVisibilityResolver.php`) — misma forma que `PlatformRegistry::isCapabilityEnabled()`: sin fila = `visible=true` (zero-risk, nada se oculta hasta que alguien lo decida explícitamente), cacheado por request.
- Componente Blade `<x-field-if entidad="..." campo="...">` (`resources/views/components/field-if.blade.php`), auto-descubierto por convención de Laravel, sin registro adicional.
- Las **6 secciones reales** de la ficha de Paciente que hoy existen en `panel/pacientes/show.blade.php` (pestaña "Historia") quedaron envueltas: `problematica`, `datos_adicionales`, `familia`, `educacion`, `laboral`, `historial_tratamientos`. Ninguna se movió, renombró ni cambió de contenido — solo se les agregó la condición de visibilidad alrededor.

**Validado con el render real de la página completa** (no un fragmento aislado — `Panel/PacienteController@show` real, paciente real con datos de familia, layout completo): con `visible=true` la sección "Grupo familiar" aparece; con `visible=false` desaparece mientras el resto de la ficha (ej. "Situación laboral") sigue intacto; restaurado a `true`, reaparece.

**Alcance explícitamente NO incluido en esta etapa** (a propósito, por instrucción de Francisco — Etapa 2 y 3 son pasos separados):
- No se movieron estas 6 secciones a un catálogo de "presets" todavía (Etapa 2).
- Ningún `Componente` (`salud_mental` u otro) quedó conectado a `fieldVisibilitySeed` todavía (Etapa 3) — hoy el `visible=true` de las 6 filas es manual/seed fijo, no derivado de qué Componentes están instalados.
- No se tocó `ExtensionContribution` ni `extensionPoints()` — el propio Francisco pidió explícitamente no entrar ahí todavía.
- Solo se envolvió `panel/pacientes/show.blade.php`. El duplicado admin y los bloques de "Padres/Tutor" y "Admisión y egresos" (que viven en la pestaña "Resumen", no en las 6 secciones de "Historia") quedan fuera de esta pasada — no fueron mencionados explícitamente como parte del alcance de Etapa 1 y agregarlos ahora sería anticipar sin necesidad confirmada.

## Historia Clínica — Etapa 2: presets → field_visibility (`FieldVisibilityInstaller`)

Servicio nuevo, deliberadamente **separado** de `ComponenteInstaller` (instalar un Componente y aplicar visibilidad son responsabilidades distintas, aunque compartan el mismo algoritmo no-destructivo ya probado con `capability_states`):

```
app/Platform/DTO/FieldVisibilitySeed.php
app/Platform/Contracts/Services/FieldVisibilityInstallerContract.php
app/Platform/Services/FieldVisibilityInstaller.php
config/platform/field_visibility_presets.php   -- 2 presets: ficha_psicosocial_extendida, ficha_clinica_basica
```

`FieldVisibilityInstaller::aplicar(array $seeds)`: por cada `FieldVisibilitySeed`, si la fila existe y tiene `origen='manual'` no la toca; si no, hace `updateOrCreate` con `origen='preset'`. Mismo patrón exacto que `ComponenteInstaller` sobre `capability_states`.

**Nota técnica real encontrada al implementar** (no estaba en el diseño previo): los subdirectorios bajo `config/` **no se auto-cargan** en Laravel — solo escanea `config/*.php` sin recursión. `config/platform/field_visibility_presets.php` habría quedado invisible para `config()` sin registrarlo explícitamente vía `mergeConfigFrom()` en `PlatformServiceProvider`. Esto también aplica retroactivamente a los archivos `config/platform/tipos_institucion.php` y `config/platform/componentes.php` que el diseño de v5-v6 especificó pero todavía no se implementaron — cuando se construyan (Etapa 3+), van a necesitar el mismo `mergeConfigFrom()`.

**Validado con el render real de la página** (mismo método que Etapa 1, sin las presets todavía disparadas por ningún Componente — se invocó `aplicar()` directamente):
1. Aplicar `ficha_clinica_basica` (las 6 en `false`) → "Grupo familiar" y "Situación laboral" desaparecen de la página real.
2. Aplicar `ficha_psicosocial_extendida` (las 6 en `true`) → reaparecen.
3. Marcar `familia` como `origen='manual', visible=true` a mano, y volver a aplicar `ficha_clinica_basica` (que pide `false`) → `familia` sigue visible y su `origen` sigue `manual` — el preset no la pisó.
4. Estado restaurado al final: las 6 secciones `visible=true, origen=preset` en `historias_demo` (estado idéntico al que dejó Etapa 1). `historias_default` no se tocó en esta prueba.

**Explícitamente NO incluido en esta etapa** (Etapa 3, próximo paso): ningún `Componente` invoca `FieldVisibilityInstaller` todavía — los presets se aplicaron manualmente para probar el mecanismo, no automáticamente al instalar "Salud Mental". Tampoco se tocó `ExtensionContribution`/`extensionPoints()` — sigue sin existir un consumidor real (Odontología) que lo justifique.

## Historia Clínica — Etapa 3: Componente real (`salud_mental`) + pipeline completo de `ComponenteInstaller`

Se completó el pipeline end-to-end pedido: `ComponenteInstaller::instalar(['salud_mental'])` deja el sistema configurado solo, sin tocar nada más.

**`Componente` (DTO, `app/Platform/DTO/Componente.php`)** — deliberadamente aburrido: solo `key`, `nombre`, `descripcion`, `capabilities`, `fieldVisibilitySeed` (claves de preset, no objetos), `tiposDocumentoSeed`, `configuracionInicial`. No sabe cómo se instala, no toca la base, no ejecuta SQL — coincide exactamente con lo pedido.

`config/platform/componentes.php` define **`salud_mental`** con `capabilities: []` (honesto: hoy no existe ninguna capability nueva que deba encenderse — historia_clínica/agenda/consentimientos ya están siempre activas) y `fieldVisibilitySeed: ['ficha_psicosocial_extendida']` — es lo único que realmente aporta, y es exactamente el preset validado en Etapa 2.

**Tabla `componentes_instalados`** (sección 1.8 del diseño original, implementada recién ahora): registra qué Componentes están instalados por tenant.

**`ComponenteInstaller`** (orquestador, `app/Platform/Services/ComponenteInstaller.php`) — inyecta `CapabilityInstallerContract` y `FieldVisibilityInstallerContract`, resuelve la unión de capabilities/fieldVisibilitySeed de TODOS los Componentes instalados (nuevos + previos, vía `componentes_instalados`), resuelve cada clave de preset contra `config('field_visibility_presets.*')`, y delega. `aplicarTiposDocumento()`/`aplicarConfiguracionInicial()` quedan como **métodos privados**, no clases separadas — a propósito: ningún Componente real los usa todavía (decisión explícita de Francisco: no dividir hasta que un segundo consumidor real lo pida, mismo criterio que ya ganó sus clases propias `FieldVisibilityInstaller` y `CapabilityInstaller`).

**`CapabilityInstaller`** (nuevo, separado) — mismo algoritmo no-destructivo preset/manual que ya tenía `capability_states`, extraído a clase propia porque esta es la **segunda ocurrencia real** (la primera fue el bootstrap manual vía `CapabilityStatesSeeder`, que se deja como está — no se retira sin necesidad).

**Institucionalizado** el hallazgo de Etapa 2: `PlatformServiceProvider::registrarConfiguracionDePlataforma()` itera `config/platform/*.php` con `glob()` y aplica `mergeConfigFrom()` a cada archivo automáticamente — ningún archivo nuevo bajo `config/platform/` va a volver a quedar invisible por config() sin que nadie se acuerde de registrarlo.

**Validado end-to-end en `historias_demo`, con datos reales y descartables**:
1. Se simuló el estado "antes de instalar" (las 6 secciones en `false`) → confirmado en la página real.
2. `ComponenteInstaller::instalar(['salud_mental'])` → las 6 secciones pasaron a `true` **solas**, sin ninguna otra llamada — confirmado en la página real (`Panel/PacienteController@show` completo).
3. Llamada repetida (idempotencia) → sin error, `componentes_instalados` sigue con una sola fila.
4. **`tiposDocumentoSeed`/`configuracionInicial`** (los dos caminos que `salud_mental` no ejercita) se probaron con un `Componente` sintético descartable inyectado en runtime (no en el catálogo real): creó `InformeTipo`+`PlantillaDocumento`+`PlantillaDocumentoVersion` correctamente, y `configuracionInicial` confirmó **ambas direcciones** — no pisó `nombre_institucion` (ya tenía datos reales) y sí completó `pie_pdf` (estaba vacío). Todo el material sintético se borró al final; `pie_pdf` volvió a `NULL`.
5. `historias_default` recibió el mismo `instalar(['salud_mental'])` para quedar consistente con `historias_demo` (ambos tenants con el mismo Componente instalado).

**Explícitamente NO incluido todavía**: `ExtensionContribution`/`extensionPoints()` — sigue sin existir un Componente (Odontología) que realmente lo necesite. Ese es el próximo paso, ahora sí con el pipeline de instalación completo y probado de punta a punta detrás.

## Etapa 4: primera `ComponenteExtension` real (Odontología)

**Aclaración de nombres, importante**: esto **no es** el `ExtensionContribution` de v6-v7 (inyectar un payload tipado en un extension point que otro módulo declaró, ej. una pestaña en la ficha de paciente). Ese mecanismo sigue sin construirse — sigue sin existir un caso real que lo necesite. Lo de acá es un problema distinto: darle a un Componente un hook de instalación propio para comportamiento que los seeds declarativos no cubren. Se nombró **`ComponenteExtension`** a propósito para no colisionar con el otro concepto en el documento.

```php
interface ComponenteExtension {
    public function version(): string;   // bump de version() = reinstalación
    public function instalar(): void;    // debe ser idempotente
}
```

Tabla `componente_extensiones` (`componente_key`, `extension_key`, `version`, `instalado_en`) — registra qué extensión está instalada y en qué versión, por tenant. `ExtensionInstaller` compara la versión registrada contra `$extension->version()`; si difieren (o no hay registro), corre `instalar()` y actualiza el registro. `ComponenteInstaller` lo llama al final de su pipeline, para cada Componente instalado que declare `->extension`.

**`OdontologiaExtension`** (`app/Modules/Odontologia/OdontologiaExtension.php`) es la primera y deliberadamente mínima: no crea tablas propias (piezas dentarias/odontograma quedan para cuando Odontología se construya de verdad como funcionalidad — acá el objetivo era probar el mecanismo, no resolver Odontología). Hace una sola cosa real que ningún seed cubre bien: **provisiona permisos en tiempo de instalación** (`odontologia_access/create/edit`, con `capability_key='odontologia'`, asignados al rol Admin) en vez de forzarlos vía una migración que los cree en todos los tenants — Odontología es genuinamente opcional, a diferencia de los 5 módulos "siempre activos" cuyos permisos sí tiene sentido crear vía migración porque todos los tenants los usan hoy.

`config/platform/componentes.php`: `'odontologia' => new Componente(key: 'odontologia', nombre: 'Odontología', capabilities: ['odontologia'], extension: new OdontologiaExtension())`.

**Validado end-to-end en `historias_demo` (los 3 puntos de la Etapa 4.5)**:
1. **Instalación limpia**: `ComponenteInstaller::instalar(['odontologia'])` creó los 3 permisos (0 antes → 3 después), los asignó a Admin (3 de 3), habilitó la capability `odontologia`, y registró `componente_extensiones` (versión `1.0.0`).
2. **Gate ON/OFF real**: con la capability habilitada, `odontologia_access` resuelve `true`; deshabilitada, `false` — mismo mecanismo exacto que Especialidades/Agenda/Medicación, sin ninguna pieza nueva en `AuthGates`.
3. **Reinstalación**: llamar `instalar(['odontologia'])` de nuevo (misma versión) no duplicó los 3 permisos.
4. **Actualización futura**: con una `ComponenteExtension` sintética de versión `2.0.0`, `ExtensionInstaller` detectó la diferencia contra el registro (`1.0.0`) y volvió a correr `instalar()` — confirmado que el mecanismo de versión funciona, y se restauró el registro a `1.0.0` (el real) al final.

**`historias_default` no recibió Odontología** — a diferencia de `salud_mental` (que se instaló en ambos tenants por ser el perfil real de la institución), Odontología fue puramente una prueba de mecanismo; instalarla ahí no tendría sentido de producto.

**Lo que sigue quedando fuera, a propósito**: tablas propias de Odontología (piezas dentarias, tratamientos), su UI (odontograma visual), y el `ExtensionContribution` original (inyectar una pestaña en la ficha de Historia Clínica) — ninguno tiene todavía un caso real que lo justifique. El objetivo de esta etapa era demostrar que la plataforma crece sin tocar el núcleo, no resolver Odontología como funcionalidad.

## Changelog v6 → v7

Se refuerza el tipado en el único lugar donde el diseño todavía perdía seguridad: la extensión de módulos.

1. **`extensionPoints()` deja de devolver strings sueltos.** Cada módulo declara sus propios extension points como un `enum` de PHP 8.1 que implementa `ExtensionPointContract` (marker interface) — `historia_clinica.paciente.tab` como typo ahora es un error de tipo/autocompletado del IDE, no un bug silencioso que se descubre en producción.
2. **`ExtensionContribution::$payload` deja de ser `mixed`.** Se define `ExtensionPayload` (marker interface) con una clase concreta por cada extension point (`PatientTabExtension`, `PatientWidgetExtension`, `DocumentTypeExtension`, ...). Cada caso del enum de extension points sabe qué clase de payload espera (`payloadType(): string`), y `PlatformRegistry::register()` valida `$payload instanceof $extensionPoint->payloadType()` — mismatch = excepción en el deploy, mismo patrón que la validación de `conflicts()`.
3. **Aclaración explícita, sin cambio de código**: se documenta la diferencia entre **Componente** (una instalación — decisión del cliente/admin), **Capability** (una capacidad técnica — lo que efectivamente prende/apaga código) y **Permission** (una autorización de usuario — quién puede usarla). Son tres niveles distintos y se venían usando de forma correcta pero implícita.

Backlog de arquitectura (anotado, **no incorporado ahora**, respetando lo que Francisco marcó como no urgente):

- Renombrar `ModuleDefinition` → algo que cubra tanto "módulo clásico" como "componente que solo extiende otro" (`PlatformComponent`/`PlatformModule`). Tiene mérito, pero renombrar la interfaz central otra vez agrega churn — se pospone hasta que haya una razón más fuerte que la simetría de nombres.
- Dividir `PlatformRegistry` en registries internos más chicos (`NavigationRegistry`, `WidgetRegistry`, `ExtensionRegistry`, `PermissionRegistry`) coordinados por `PlatformRegistry`, cuando el registry actual empiece a ser difícil de mantener. No es para Fase 0.
- Concepto de `uninstall()`/desinstalación de un Componente. No se diseña ni se implementa todavía (qué pasa con los datos, las capabilities y los permisos al desinstalar es una pregunta real pero no urgente) — se deja anotado que el concepto va a existir, para no cerrar la interfaz de un modo que lo haga imposible después.

## Changelog v5 → v6

Dos cambios, uno de nombre y uno de capacidad real:

1. **`AreaFuncional` → `Componente`**. Mismo concepto (multi-select, aditivo, dispara capabilities vía `ComponenteInstaller`), nombre más genérico y correcto: cubre tanto módulos completos (Laboratorio) como extensiones de otro módulo (Odontología) como componentes futuros sin relación con un área médica (IA, Telemedicina, Firma Digital, Integración FHIR). `areas_funcionales.php` → `componentes.php`, tabla `areas_funcionales_seleccionadas` → `componentes_instalados`, servicio `AreaFuncionalApplier::aplicar()` → `ComponenteInstaller::instalar()`.

2. **Un Componente puede extender un módulo existente, no solo instalar uno nuevo.** Se agrega `extensionPoints(): array` a `ModuleDefinition` (el módulo destino declara dónde puede ser extendido) y un tipo de contribución nuevo, `ExtensionContribution` (el componente que extiende apunta a un extension point declarado). Esto **no es un sistema paralelo**: es exactamente la extensibilidad que `contributions()` ya preveía desde v3 ("agregar `ImportContribution` a futuro sin romper el contrato"), aplicada a un caso de uso real.

Distinción importante entre los ejemplos de Francisco, para no sobre-resolver:

- **Odontología, Medicina Laboral, Pediatría, Obstetricia** → datos que hoy no existen en el schema (odontograma, curvas de crecimiento, controles prenatales). Cada uno es un Componente con **tablas y modelos propios** (mismo patrón que ya usan `PacienteHijo`/`PacienteHermano`/etc. hoy) que se engancha a Historia Clínica vía `ExtensionContribution` sobre el extension point `paciente.tabs`. Historia Clínica nunca importa código de Odontología ni al revés.
- **Salud Mental** → sus datos **ya existen** en el core (las 11 sub-fichas de Paciente ya construidas). No necesita `ExtensionContribution` — es exactamente el caso ya resuelto por `field_visibility` (Fase 3): hoy esas secciones están siempre visibles; el componente "Salud Mental" simplemente las vuelve condicionales. Meterla en el mecanismo de extensión sería resolver dos veces el mismo problema con dos mecanismos distintos.

Todo lo demás de v5 (Tipo de Institución descriptivo, capability_states con `source`, servicios de plataforma, TipoDocumento/PlantillaDocumento versionado, tabla `tenants`, contribuciones tipadas) queda sin cambios.

---

## 1. Modelo de datos

Todas las tablas nuevas viven dentro de cada DB de tenant, salvo `tenants` (sección 6.1).

### 1.1 `capability_states`

```
id              bigint PK
capability_key  varchar(150) unique
enabled         boolean default false
source          varchar(20) default 'preset'   -- 'preset' | 'manual'
settings        json nullable
enabled_at      timestamp nullable
enabled_by      bigint FK users nullable
timestamps
```

### 1.2 `permissions` — columna nueva

```sql
ALTER TABLE permissions ADD capability_key varchar(150) NULL;
```

### 1.3 `informes_tipos` (modelo `TipoDocumento`) — capa de política

```sql
ALTER TABLE informes_tipos ADD modulo_key       varchar(100) NOT NULL DEFAULT 'historia_clinica';
ALTER TABLE informes_tipos ADD categoria        varchar(100) NULL;
ALTER TABLE informes_tipos ADD firma_requerida  boolean DEFAULT true;
ALTER TABLE informes_tipos ADD roles_firmantes  json NULL;
ALTER TABLE informes_tipos ADD visible_portal   boolean DEFAULT false;
ALTER TABLE informes_tipos ADD permiso_codigo   varchar(150) NULL;
ALTER TABLE informes_tipos ADD activo           boolean DEFAULT true;
ALTER TABLE informes_tipos ADD orden            int NULL;
```

### 1.4 `plantillas_documento` / 1.5 `plantilla_documento_versiones` / 1.6 `informes` — sin cambios respecto a v5

(Ver v5: concepto de plantilla sin contenido propio, contenido versionado, `informes.plantilla_documento_version_id` nullable.)

### 1.7 `field_visibility` (Fase 3, sin cambios)

```
id          bigint PK
entidad     varchar(100)
campo       varchar(100)
tipo        enum('campo','seccion','tab')
visible     boolean default true
requerido   boolean nullable
origen      varchar(20) default 'preset'
timestamps
unique(entidad, campo)
```

### 1.8 `componentes_instalados` (renombrada en v6, antes `areas_funcionales_seleccionadas`)

```
id                bigint PK
componente_key     varchar(100) unique   -- 'salud_mental', 'laboratorio', 'odontologia', ...
instalado_en       timestamp
instalado_por      bigint FK users nullable
timestamps
```

### 1.9 `pacientes` — seam de `Sujeto` (Fase 6, sin cambios)

```sql
ALTER TABLE pacientes ADD tipo_sujeto     varchar(50) DEFAULT 'persona';
ALTER TABLE pacientes ADD atributos_extra json NULL;
```

### 1.10 `configuracion_sistema.tipo_institucion` (columna existente, sin cambios respecto a v5)

Descriptivo, seteado directamente por el admin en el paso 1 del wizard, sin efecto en capabilities.

### 1.11 Tablas propias de Componentes-extensión (nuevo en v6, ejemplo)

Cada Componente que extiende Historia Clínica con datos nuevos trae sus propias tablas — no se tocan `pacientes` ni `informes`:

```
piezas_dentarias            (Odontología)   -- paciente_id, numero_pieza, estado, observaciones
tratamientos_odontologicos  (Odontología)   -- paciente_id, pieza_id, tipo_tratamiento, fecha
aptitudes_laborales         (Medicina Laboral) -- paciente_id, tipo_examen (preocupacional/periodico), resultado, fecha, vencimiento
mediciones_crecimiento      (Pediatría)     -- paciente_id, fecha, peso, talla, percentil
controles_prenatales        (Obstetricia)   -- paciente_id, fecha, semana_gestacional, observaciones
```

Todas `belongsTo(Paciente)`, mismo patrón que las sub-fichas satélite ya existentes hoy. Viven en `app/Modules/<Componente>/` desde el día en que se crean (código nuevo, no hay nada que mover — a diferencia de los 5 módulos legacy, ver sección 8).

---

## 2. Contratos PHP

```php
final class ModuleManifest
{
    public function __construct(
        public readonly string $key,
        public readonly string $nombre,
        public readonly string $descripcion = '',
        public readonly string $icon = '',
        public readonly string $color = '',
        public readonly string $category = '',
        public readonly string $version = '1.0.0',
        public readonly string $minimumPlatformVersion = '1.0.0',
        public readonly string $author = 'Arioli',
        public readonly int $priority = 100,
        public readonly bool $beta = false,
        public readonly bool $hidden = false,
    ) {}
}

interface ModuleDefinition
{
    public function manifest(): ModuleManifest;
    public function dependencias(): array;
    public function conflicts(): array;
    public function replaces(): array;
    public function optionalDependencies(): array;
    public function capabilities(): array;
    public function permisos(): array;
    public function contributions(): iterable;         // iterable<Contribution>
    public function extensionPoints(): array;           // nuevo en v6 — ['paciente.tabs', 'paciente.widgets_ficha', 'documento.tipos']
    public function eventosEmitidos(): array;
    public function eventosEscuchados(): array;
    public function migrationsPath(): ?string;
}
```

`extensionPoints()` lo declara el módulo **destino** (ej. `HistoriaClinicaModule`) — son los lugares donde acepta que otros le agreguen contenido. Un módulo sin nada que ofrecer devuelve `[]`.

### Contribuciones tipadas

```php
interface Contribution {}   // marker

final class NavigationContribution implements Contribution
{
    public function __construct(public readonly NavigationItem $item) {}
}

final class WidgetContribution implements Contribution
{
    public function __construct(public readonly string $widgetClass) {}
}

final class PublisherContribution implements Contribution
{
    public function __construct(public readonly string $publisherClass) {}
}

interface ExtensionPointContract {}   // marker — cada módulo destino define su propio enum que lo implementa

interface ExtensionPayload {}         // marker — cada extension point define su propia clase de payload concreta

final class ExtensionContribution implements Contribution
{
    public function __construct(
        public readonly string $moduloDestino,             // 'historia_clinica' — key de lookup en el Registry
        public readonly ExtensionPointContract $extensionPoint, // caso de enum del módulo destino, no string
        public readonly ExtensionPayload $payload,             // tipado según el extension point, no mixed
    ) {}
}
```

Ejemplo de un módulo destino declarando sus extension points como enum (PHP 8.1, ya disponible en el proyecto):

```php
enum HistoriaClinicaExtensionPoint: string implements ExtensionPointContract
{
    case PacienteTabs        = 'historia_clinica.paciente.tabs';
    case PacienteWidgetsFicha = 'historia_clinica.paciente.widgets_ficha';
    case DocumentoTipos       = 'historia_clinica.documento.tipos';

    public function payloadType(): string
    {
        return match ($this) {
            self::PacienteTabs         => PatientTabExtension::class,
            self::PacienteWidgetsFicha => PatientWidgetExtension::class,
            self::DocumentoTipos       => DocumentTypeExtension::class,
        };
    }
}

final class PatientTabExtension implements ExtensionPayload
{
    public function __construct(public readonly NavigationItem $tab) {}
}

final class PatientWidgetExtension implements ExtensionPayload
{
    public function __construct(public readonly string $widgetClass) {}
}

final class DocumentTypeExtension implements ExtensionPayload
{
    public function __construct(public readonly array $tipoDocumentoSeed) {}
}
```

```php
foreach ($module->contributions() as $contribution) {
    match (true) {
        $contribution instanceof NavigationContribution => $this->navigation[] = $contribution->item,
        $contribution instanceof WidgetContribution     => $this->widgetClasses[] = $contribution->widgetClass,
        $contribution instanceof PublisherContribution  => $this->publisherClasses[] = $contribution->publisherClass,
        $contribution instanceof ExtensionContribution  => $this->registrarExtension($contribution), // valida moduloDestino+extensionPoint, ver sección 5
        default => null,
    };
}
```

```php
final class NavigationItem
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly ?string $route = null,
        public readonly ?string $icon = null,
        public readonly ?string $capabilityRequerida = null,
        public readonly ?string $permisoRequerido = null,
        public readonly int $orden = 100,
        public readonly ?string $seccion = null,
        public readonly ?\Closure $badge = null,
        public readonly array $children = [],
    ) {}
}

interface Publisher
{
    public function key(): string;
    public function capabilityRequerida(): string;
    public function publishFor(Sujeto $sujeto): array;
}

final class PortalAction
{
    public function __construct(
        public readonly string $label,
        public readonly string $url,
        public readonly ?string $icon = null,
    ) {}
}

final class PortalItem
{
    public function __construct(
        public readonly string $tipo,
        public readonly string $titulo,
        public readonly \DateTimeInterface $fecha,
        public readonly string $moduloOrigen,
        public readonly int $prioridad = 100,
        public readonly ?string $categoria = null,
        public readonly ?string $icon = null,
        public readonly ?string $preview = null,
        public readonly array $acciones = [],
    ) {}
}

interface DashboardWidget
{
    public function key(): string;
    public function titulo(): string;
    public function capabilityRequerida(): string;
    public function permisoRequerido(): ?string;
    public function prioridad(): int;
    public function data(): WidgetData;
}

final class WidgetData
{
    public function __construct(
        public readonly string $view,
        public readonly array $payload,
    ) {}
}

interface Sujeto
{
    public function nombreParaMostrar(): string;
    public function tipoSujeto(): string;
    public function atributosExtra(): array;
}
```

### Domain Events

Sin cambios — convención sobre el sistema nativo de eventos de Laravel.

---

## 3. Servicios de plataforma (`app/Platform/Services/`)

```
app/Platform/Contracts/Services/
  StorageServiceContract.php
  PdfServiceContract.php
  QrServiceContract.php
  NotificationServiceContract.php
  AuditServiceContract.php
  SignatureServiceContract.php
  ComponenteInstallerContract.php     -- renombrado en v6 (antes AreaFuncionalApplierContract)

app/Platform/Services/
  StorageService.php
  PdfService.php
  QrService.php
  NotificationService.php
  AuditService.php
  SignatureService.php
  ComponenteInstaller.php             -- renombrado en v6
```

### `ComponenteInstaller` — algoritmo (mismo de v4/v5, renombrado)

```php
interface ComponenteInstallerContract
{
    public function instalar(array $componentKeys): void;
}
```

```
instalar(['salud_mental', 'laboratorio']):

1. Por cada componentKey nuevo (sin fila en componentes_instalados), crear la fila.
2. Unión de ->capabilities() de TODOS los componentes instalados (nuevos + existentes).
3. Por capability: sin fila -> crear enabled=true, source='preset'.
                    source='preset' -> asegurar enabled=true (idempotente).
                    source='manual' -> NO TOCAR.
4. TipoDocumento/PlantillaDocumento seed: crear solo si no existe el mismo `codigo`.
5. field_visibility seed: merge OR, solo si origen='preset' o no existe.
6. configuracion_inicial seed: solo campos NULL/vacíos hoy.
```

---

## 4. Catálogos: Tipo de Institución vs. Componente

### 4.1 `TipoInstitucion` — descriptivo, sin capabilities (sin cambios respecto a v5)

```php
final class TipoInstitucion
{
    public function __construct(
        public readonly string $key,
        public readonly string $nombre,
        public readonly string $icon = '',
        public readonly string $color = '',
        public readonly array $componentesSugeridos = [], // antes areasSugeridas — solo pre-tilda checkboxes
    ) {}
}
```

### 4.2 `Componente` (renombrado en v6, antes `AreaFuncional`)

```php
final class Componente
{
    public function __construct(
        public readonly string $key,
        public readonly string $nombre,
        public readonly string $descripcion,
        public readonly string $icon,
        public readonly array $capabilities,
        public readonly array $tiposDocumentoSeed = [],
        public readonly array $fieldVisibilitySeed = [],
        public readonly array $configuracionInicial = [],
    ) {}
}
```

`config/platform/componentes.php` (reemplaza `areas_funcionales.php`):

```php
return [
    'historia_clinica'    => new Componente('historia_clinica', 'Historia Clínica', capabilities: ['historia_clinica'], ...),
    'agenda'              => new Componente('agenda', 'Agenda', capabilities: ['agenda'], ...),
    'salud_mental'        => new Componente('salud_mental', 'Salud Mental / Adicciones', capabilities: ['ficha_psicosocial_extendida', 'consentimientos'], fieldVisibilitySeed: [/* muestra las sub-fichas ya existentes de Paciente */], ...),
    'laboratorio'         => new Componente('laboratorio', 'Laboratorio', capabilities: ['laboratorio', 'laboratorio.ordenes', 'laboratorio.muestras', 'laboratorio.resultados', 'laboratorio.validacion', 'portal.resultados'], ...),
    'imagenes'            => new Componente('imagenes', 'Diagnóstico por Imágenes', capabilities: ['imagenes', 'imagenes.estudios', 'imagenes.informes', 'portal.imagenes'], ...),
    'odontologia'         => new Componente('odontologia', 'Odontología', capabilities: ['odontologia'], ...),          // registra ExtensionContribution hacia historia_clinica.paciente.tabs
    'medicina_laboral'    => new Componente('medicina_laboral', 'Medicina Laboral', capabilities: ['medicina_laboral'], ...), // idem
    'pediatria'           => new Componente('pediatria', 'Pediatría', capabilities: ['pediatria'], ...),                       // idem
    'obstetricia'         => new Componente('obstetricia', 'Obstetricia', capabilities: ['obstetricia'], ...),                 // idem
    'kinesiologia'        => new Componente('kinesiologia', 'Kinesiología', capabilities: ['kinesiologia'], ...),
    'farmacia'            => new Componente('farmacia', 'Farmacia', capabilities: ['farmacia'], ...),
    'internacion'         => new Componente('internacion', 'Internación', capabilities: ['internacion'], ...),
    'guardia'             => new Componente('guardia', 'Guardia', capabilities: ['guardia'], ...),
    'vacunatorio'         => new Componente('vacunatorio', 'Vacunatorio', capabilities: ['vacunatorio'], ...),
    'portal_paciente'     => new Componente('portal_paciente', 'Portal del Paciente', capabilities: ['portal'], ...),
    'portal_profesional'  => new Componente('portal_profesional', 'Portal para Profesionales', capabilities: ['portal_profesional'], ...),
    'facturacion'         => new Componente('facturacion', 'Facturación', capabilities: ['facturacion'], ...),
    'inventario'          => new Componente('inventario', 'Inventario', capabilities: ['inventario'], ...),
];
```

El catálogo `componentes.php` no distingue "instala módulo nuevo" de "extiende uno existente" — esa diferencia vive en el código del `ModuleDefinition` correspondiente (si contribuye `NavigationContribution` de nivel superior, o `ExtensionContribution` hacia otro módulo, o ambas cosas), no en el catálogo de configuración. Desde el wizard, ambos casos se ven exactamente igual: un checkbox más.

### 4.3 Ejemplo concreto: Odontología extiende Historia Clínica

```php
// HistoriaClinicaModule
public function extensionPoints(): array
{
    return HistoriaClinicaExtensionPoint::cases();   // enum, no strings sueltos
}
```

```php
// OdontologiaModule — tiene sus propias tablas (sección 1.11), y además extiende Historia Clínica
public function contributions(): iterable
{
    yield new ExtensionContribution(
        moduloDestino: 'historia_clinica',
        extensionPoint: HistoriaClinicaExtensionPoint::PacienteTabs,
        payload: new PatientTabExtension(
            tab: new NavigationItem(
                key: 'odontologia',
                label: 'Odontograma',
                route: 'odontologia.paciente.show',
                capabilityRequerida: 'odontologia',
            ),
        ),
    );
}
```

Si `OdontologiaModule` intentara mandar cualquier otro tipo de payload a `PacienteTabs` (por ejemplo, por error, un `PatientWidgetExtension`), `PlatformRegistry::register()` lo rechaza en el momento del registro — no llega a producción un extension point con el shape equivocado.

La vista de ficha de paciente en Historia Clínica nunca importa nada de Odontología — solo llama `$registry->extensionesPara('historia_clinica', 'paciente.tabs')` y renderiza lo que haya (cero, una o varias pestañas de distintos componentes instalados).

### 4.4 Wizard de alta de tenant — sin cambios respecto a v5

Paso 1 (Tipo de institución, descriptivo) + Paso 2 (Componentes, checkboxes múltiples → `ComponenteInstaller::instalar()`).

### 4.5 Tres niveles distintos: Componente, Capability, Permission — nuevo en v7

Aparecen tres palabras parecidas que representan cosas conceptualmente distintas. Dejarlo explícito acá evita que en seis meses alguien las use indistintamente:

| Nivel | Qué es | Quién lo decide | Ejemplo |
|---|---|---|---|
| **Componente** | Una **instalación** — una pieza de la plataforma que el tenant eligió tener | El admin del tenant, vía wizard o Configuración → Módulos | "Este tenant tiene instalado Odontología" |
| **Capability** | Una **capacidad técnica** — lo que efectivamente prende o apaga código (rutas, menú, Gates) | El sistema, derivado de qué Componentes están instalados (`capability_states`) | "La capability `odontologia` está `enabled=true`" |
| **Permission** | Una **autorización de usuario** — quién, dentro de un tenant con la capability habilitada, puede usarla | El admin, al asignar roles | "El rol Odontólogo tiene el permiso `odontologia_access`" |

Un Componente instalado habilita N capabilities; una capability habilitada es prerequisito (no reemplazo) de que un permiso ligado a ella tenga efecto — por eso la validación en `AuthGates` (sección 5) es un **AND**: `capability habilitada && usuario tiene el permiso`. Ninguno de los tres reemplaza a los otros dos.

---

## 5. `PlatformRegistry`

```php
final class PlatformRegistry
{
    public function register(ModuleDefinition $module): void {}      // valida conflicts() Y extensionPoints() al registrar
    public function all(): array {}
    public function get(string $key): ?ModuleDefinition {}
    public function isCapabilityEnabled(string $capabilityKey): bool {}
    public function permisosHabilitados(): array {}
    public function navigationParaUsuario(User $user): array {}
    public function publishersHabilitados(): array {}
    public function widgetsHabilitadosPara(User $user): array {}
    public function eventCatalog(): array {}
    public function extensionesPara(string $modulo, string $extensionPoint): array {}   // nuevo en v6
}
```

**Validación al registrar** (extiende la ya existente de `conflicts()`): si un módulo contribuye una `ExtensionContribution` cuyo `moduloDestino` no existe, cuyo `extensionPoint` no está declarado en `extensionPoints()` del destino, **o cuyo `payload` no es una instancia del tipo que ese extension point espera** (`$extensionPoint->payloadType()`), `register()` lanza excepción — falla en el deploy, no en un tab que simplemente nunca aparece y nadie sabe por qué.

Integración con `AuthGates` — sin cambios:

```php
foreach ($permissions as $permission) {
    $habilitado = $permission->capability_key === null
        || $registry->isCapabilityEnabled($permission->capability_key);

    Gate::define($permission->title, fn($user) =>
        $habilitado && $user->hasPermission($permission->title)
    );
}
```

---

## 6. Multi-tenancy: tabla `tenants` — sin cambios respecto a v5

Tabla `tenants`, comando `tenants:migrate`, migración de arranque leyendo `information_schema` una única vez. Ver v5 para el detalle completo (sin cambios en v6).

---

## 7. Plan de migraciones — orden concreto

**Prerequisito**: `tenants` + `tenants:migrate` antes que cualquier migración de plataforma.

1. `create_tenants_table.php` + seeder de bootstrap.
2. `add_capability_key_to_permissions_table.php`.
3. `create_capability_states_table.php` (con `source`) + seeder, `enabled=true, source='preset'` para lo existente.
4. `add_platform_fields_to_informes_tipos_table.php`.
5. `create_plantillas_documento_table.php`.
6. `create_plantilla_documento_versiones_table.php`.
7. `add_plantilla_documento_version_id_to_informes_table.php`.
8. `create_componentes_instalados_table.php` — poblada con el/los componentes que describan cada tenant existente (a confirmar).
9. (opcional, baja prioridad) backfill de `configuracion_sistema.tipo_institucion`.

Las tablas propias de componentes-extensión (`piezas_dentarias`, `mediciones_crecimiento`, etc., sección 1.11) se crean recién en Fase 6, cuando se construya cada componente — no forman parte de este plan de migraciones de plataforma.

Todas corridas vía `tenants:migrate`.

---

## 8. Layout de directorios

**Fase 0 (los 5 módulos legacy): cero movimiento de archivos.** Los manifiestos apuntan a `app/Models`/`app/Http/Controllers` donde ya viven.

**Componentes nuevos (Fase 6 en adelante): nacen directamente en `app/Modules/<Componente>/`**, porque no hay código legacy que mover — no aplica la restricción de Fase 0, que era específicamente sobre no perturbar código ya funcionando.

```
app/
  Platform/
    PlatformRegistry.php
    Contracts/
      ModuleDefinition.php  Contribution.php  Publisher.php  DashboardWidget.php  Sujeto.php
      Services/
        StorageServiceContract.php  PdfServiceContract.php  QrServiceContract.php
        NotificationServiceContract.php  AuditServiceContract.php  SignatureServiceContract.php
        ComponenteInstallerContract.php
    DTO/
      ModuleManifest.php  NavigationItem.php  PortalItem.php  PortalAction.php  WidgetData.php
      NavigationContribution.php  WidgetContribution.php  PublisherContribution.php  ExtensionContribution.php
      TipoInstitucion.php  Componente.php  TipoDocumentoSeed.php  FieldVisibilitySeed.php
    Services/
      StorageService.php  PdfService.php  QrService.php
      NotificationService.php  AuditService.php  SignatureService.php
      ComponenteInstaller.php
    Events/
      PacienteCreado.php  PacienteActualizado.php
    Models/
      Tenant.php
    Modules/
      HistoriaClinicaModule.php   -- declara extensionPoints(): ['paciente.tabs', 'paciente.widgets_ficha', 'documento.tipos']
      AgendaModule.php
      ConsentimientosModule.php
      MedicacionModule.php
      EspecialidadesModule.php
  Modules/
    Odontologia/           -- Fase 6, nace acá directamente
      OdontologiaModule.php  Models/  Http/  migrations/
    MedicinaLaboral/
    Pediatria/
    Obstetricia/
    Laboratorio/
    Imagenes/

config/platform/
  tipos_institucion.php
  componentes.php
```

---

## 9. Checklist antes de escribir código

- [x] Mecanismo de migración multi-tenant → tabla `tenants` + `tenants:migrate`.
- [x] Modelo de composición funcional → Componentes combinables, unión aditiva, `ComponenteInstaller` idempotente.
- [x] Separación Tipo de Institución (descriptivo) vs. Componente (funcional).
- [x] Componentes que extienden un módulo existente vs. componentes que instalan uno nuevo → `extensionPoints()` + `ExtensionContribution`, validado al registrar.
- [ ] Confirmar convención de deploy para esta app.
- [ ] Decidir si los primeros `ModuleDefinition` se escriben todos juntos en Fase 0, o se registra 1 solo como prueba de concepto.
- [ ] Decidir qué Servicio de Plataforma se extrae primero (recomendado: `PdfService`).
- [ ] Definir qué tipo de institución + componentes le corresponden a `historias_default`/`historias_demo` al migrar — a confirmar con Francisco antes de correr esa migración en producción.

---

# Checkpoint arquitectónico — post Etapa 4

Este documento fue narrativo hasta acá (una entrada por Fase/Etapa, en orden cronológico). Esta sección es la primera que consolida, no agrega — es la pausa pedida después de Etapa 4 antes de seguir sumando módulos.

## Contrato de un Componente (referencia consolidada)

Un **Componente** (`app/Platform/DTO/Componente.php`) es puramente declarativo — no sabe cómo se instala, no toca la base, no ejecuta SQL:

```php
new Componente(
    key: string,                          // clave única en config/platform/componentes.php
    nombre: string,
    descripcion: string = '',
    capabilities: string[] = [],          // capability_key a habilitar (unión aditiva entre componentes)
    fieldVisibilitySeed: string[] = [],   // claves de preset en config/platform/field_visibility_presets.php
    tiposDocumentoSeed: array = [],       // shape: tipo_nombre, tipo_modulo_key, plantilla_nombre, plantilla_codigo, contenido
    configuracionInicial: array = [],     // ['campo_de_configuracion_sistema' => valor]
    extension: ?ComponenteExtension = null,
)
```

**Ciclo de vida**, todo orquestado por `ComponenteInstaller::instalar(string[] $componentKeys)`:

1. **Instalar** — registra en `componentes_instalados`, resuelve la unión de `capabilities`/`fieldVisibilitySeed` de TODOS los componentes instalados (nuevos + previos), delega a `CapabilityInstaller`/`FieldVisibilityInstaller`/`aplicarTiposDocumento()`/`aplicarConfiguracionInicial()`, y por último a `ExtensionInstaller` si el componente declara `->extension`.
2. **Reinstalar** — llamar `instalar()` de nuevo con la misma lista es siempre seguro: cada sub-instalador es idempotente (preset/manual no-destructivo para capabilities y field_visibility; `firstOrCreate` para tipos/plantillas; solo-si-vacío para configuración; comparación de versión para extensiones).
3. **Actualizar** — un Componente no tiene versión propia todavía (solo su `ComponenteExtension`, si tiene una). Bump de `ComponenteExtension::version()` → `ExtensionInstaller` vuelve a correr `instalar()` de esa extensión la próxima vez que `ComponenteInstaller::instalar()` se llame con ese componente en la lista de instalados.
4. **Desinstalar** — **no existe todavía**. Sigue siendo backlog (anotado desde v6): qué pasa con los datos, capabilities y permisos al desinstalar es una pregunta real pero sin caso de uso todavía. `componentes_instalados` nunca borra una fila hoy.

**Protección `preset`/`manual`**: en `capability_states` y `field_visibility`, cualquier fila que un admin tocó a mano (`origen`/`source = 'manual'`) queda protegida — ningún `instalar()`/reinstalación futura la pisa. Es la regla que atraviesa todo el pipeline.

## Límite entre Platform y Componentes (estado real, no aspiracional)

**Platform (Core, siempre presente)**: `users`, `roles`, `permissions` (tabla base), `PlatformRegistry`, `AuthGates`, `capability_states` (el mecanismo, no las capabilities en sí), `tenants` + `tenants:migrate`, `ConfiguracionSistema`, `ComponenteInstaller` y sus sub-instaladores. Es infraestructura — no tiene capabilities propias, no se "instala".

**Módulos siempre activos (`ModuleDefinition`, vía `PlatformRegistry`)**: Especialidades, Agenda, Consentimientos, Medicación, Informes. Registrados en `config('platform.modules')`, aportan navegación (`NavigationContribution`) además de capability+permisos. Sus capabilities se habilitaron manualmente vía `CapabilityStatesSeeder` (bootstrap de Fase 0), **no** pasan por `ComponenteInstaller`.

**Componentes opcionales (`Componente`, vía `ComponenteInstaller`)**: Salud Mental, Odontología. Registrados en `config('componentes')`, con capabilities/fieldVisibility/tiposDocumento/configuracionInicial/extension orquestados y trackeados en `componentes_instalados`.

**Tensión real — dirección conceptual decidida, implementación diferida**: `ModuleDefinition` y `Componente` son hoy **dos registros paralelos** que ambos terminan escribiendo en `capability_states`, por caminos de código distintos (`CapabilityStatesSeeder` manual vs. `CapabilityInstaller` orquestado). No es un bug — ambos caminos son no-destructivos y conviven sin pisarse.

La dirección conceptual (Francisco, post-Etapa 4): **Platform = capacidades estructurales del sistema** (usuarios, roles, historias clínicas core, pacientes, instituciones); **Componentes = capacidades funcionales instalables** (odontología, salud mental, laboratorio, internación). `capability_states` seguiría siendo una tabla común, pero con un origen explícito:

```
source_type   -- 'platform' | 'component'
source_key    -- 'usuarios' | 'odontologia' | ...
```

**No se implementa ahora** — queda anotado como la dirección a tomar, no como trabajo pendiente de esta sesión. Se revisita cuando escalar a 10+ Componentes haga que mantener dos registros paralelos cueste más que unificarlos.

## Pendientes reales encontrados en este checkpoint (no arquitectura, estado del proyecto)

- **Nada de lo construido en esta sesión (Fase 0 a Etapa 4) está en control de versiones.** `apps/historias-clinicas` figura como `# Sub-apps (not versioned in this repo yet — deployed via rsync)` en el `.gitignore` del monorepo — decisión previa e intencional, no un descuido de esta sesión. "Etiquetar una versión interna" requiere primero decidir si este es el momento de empezar a trackearlo.
- **Sigue sin existir aislamiento de DB para tests** (decisión ya tomada después de Fase 0: seguir con verificación manual por ahora). Se revisitó después de este checkpoint y se confirmó la misma decisión: posponer, seguir con verificación manual.

## Contrato de `Componente` — congelado hasta validar extensiones

El contrato actual queda **congelado** a propósito, antes de entrar a `ExtensionContribution`/Odontología con tablas propias:

```
Componente
{
    key
    nombre
    capabilities
    fieldVisibilitySeed
    tiposDocumentoSeed
    configuracionInicial
    extension   -- ?ComponenteExtension, agregado en Etapa 4
}
```

No se agrega `routes()`, `controllers()`, `models()`, `migrations()` todavía — haría exactamente lo que toda la sesión evitó: infraestructura sin un caso real que la obligue. La pregunta guía para cuando se retome (Etapa 4.1, sin implementar Odontología todavía) es **"¿qué necesita obligatoriamente una extensión para existir dentro de la plataforma?"** — y la respuesta tiene que salir de intentar construir Odontología de verdad, no al revés.

## Punto de retorno

`git tag historias-clinicas-checkpoint-etapa4` (commit `d8b2e2e`) — Fase 0, motor de documentos, field_visibility, `ComponenteInstaller`, Salud Mental, config loader genérico, primera `ComponenteExtension` (Odontología mínima). Respaldo adicional del estado de `historias_demo` en ese punto: ver `docs/snapshots/`.

**Antes de seguir con Odontología (comportamiento real, migraciones por componente, versionado, rollback) conviene retomar con la cabeza fresca, no en la misma sesión.**

---

# Etapa 4.1 — Primera necesidad real de Odontología: navegación

Retomando con la regla acordada (**no diseñar `ExtensionContribution` primero — dejar que una necesidad real de Odontología obligue el contrato**): se probaron los 3 casos reales contra las herramientas ya existentes (`Componente` + `ComponenteInstaller`).

- **Caso 1 (ficha odontológica: antecedentes, hábitos)** → encaja en `fieldVisibilitySeed`/`configuracionInicial`. No se implementó todavía (no hay campos reales en Paciente para ocultar/mostrar — implementarlo ahora sería fabricar un preset sin contraparte real en la UI).
- **Caso 2 (documentos odontológicos: ficha, consentimiento, presupuesto)** → encaja en `tiposDocumentoSeed`, ya soportado desde Etapa 3. No se implementó todavía por la misma razón.
- **Caso 3 (Odontograma: piezas, superficies, estados, historial)** → **sí reveló una necesidad real no cubierta**: tablas propias (van por migración Laravel normal, no por `ComponenteExtension` — distinción ya resuelta en Etapa 4) y, más importante, **un ítem de menú** — y ningún `Componente` podía aportar navegación todavía, solo `ModuleDefinition` podía (`NavigationContribution`, vía `PlatformRegistry`). Exactamente la tensión Platform/Componentes ya anotada en el checkpoint, llegando antes de lo esperado.

## Hallazgo real durante la implementación: el menú documentado en Fase 0 nunca estuvo conectado

Al ir a conectar el nuevo ítem de navegación, se descubrió que **`resources/views/partials/menu.blade.php` — el archivo que Fase 0 tomó como "el menú real" para diseñar `PlatformRegistry::navigationParaUsuario()`, con sus `@can('agenda_access')`, etc. — no lo incluye nada en la aplicación.** Es código huérfano: `grep -rn "partials.menu" resources/views/` no devuelve ningún resultado. La investigación de Fase 0 confió en que un archivo con el patrón correcto (condicional por permiso) era el menú en producción, sin verificar que estuviera efectivamente incluido — la lección concreta: verificar que una vista se renderiza de verdad (buscar quién la incluye), no solo que su contenido parece correcto.

**El menú real** vive inline dentro de `resources/views/layouts/app.blade.php` (`<nav class="sidebar-nav">`, línea ~597). Ahí apareció un segundo hallazgo, más importante: **los 5 ítems de los módulos "siempre activos" (Pacientes, Agenda, Informes, Prescripciones, Recetas) no tienen ningún gating por capability ni por permiso en el menú real** — se muestran siempre. La autorización real sigue funcionando (cada controller valida `Gate::denies(...)`), así que no es un problema de seguridad, pero sí una inconsistencia de UX real y preexistente: un usuario sin `agenda_access`, o un tenant con la capability `agenda` apagada, vería igual el ítem "Agenda" en el menú y recibiría un 403 al entrar. **No se corrige en esta pasada** — es una corrección más grande (tocar los 5 ítems existentes) fuera del alcance de "conectar la navegación de Odontología", queda anotada como deuda real encontrada.

## `navegacionSeed` + `NavigationInstaller`

`Componente` ganó un campo más (contrato ya "congelado" reabierto por una necesidad concreta, no especulativa):

```php
navegacionSeed: NavigationItem[]   // reutiliza el DTO NavigationItem que ya existía para ModuleDefinition
```

`NavigationInstaller` (`app/Platform/Services/NavigationInstaller.php`) — nota honesta en su propio docblock: **no persiste nada**. A diferencia de `CapabilityInstaller`/`FieldVisibilityInstaller`, la navegación de un Componente se resuelve en el momento de pedirla (mismo criterio que ya usaba `PlatformRegistry` para `ModuleDefinition` — ninguna de las dos guarda "ítems de menú" en una tabla). Se llama "Installer" por consistencia de nombres con el resto del pipeline, no porque escriba estado; por eso **no** se invoca desde `ComponenteInstaller::instalar()` — no hay nada que instalar, solo resolver en cada request. `resolverPara(User $user)` filtra por capability + permiso, igual que `PlatformRegistry::navigationParaUsuario()`.

## Odontología: página mínima real (no el odontograma)

Para que el ítem de menú no fuera un link roto, se creó lo mínimo indispensable: `Panel/OdontologiaController@index` (gateado por `Gate::denies('odontologia_access')`, permiso que ya existía desde Etapa 4), ruta `panel.odontologia.index` con `->middleware('capability:odontologia')` (mismo middleware que ya protege Consentimientos), y una vista placeholder ("módulo en construcción"). El Componente `odontologia` declara `navegacionSeed` apuntando ahí.

**Validado end-to-end sobre el archivo real** (no el huérfano): con la capability `odontologia` habilitada, el ítem "Odontología" aparece en el menú de una página real (`Panel/InformeController@index`, que incluye el layout completo); deshabilitada, desaparece; restaurada, reaparece. La página placeholder renderiza su contenido real.

**Sigue sin construirse**: las tablas del odontograma (piezas, superficies, estados, historial) y su UI — Caso 3 solo estaba pendiente de resolver el problema de navegación, no de resolver Odontología como funcionalidad. El contrato de `ComponenteExtension` (`version()`/`instalar()`) sigue sin necesitar cambios — la necesidad de esta etapa fue de navegación, no de instalación, y por eso se resolvió con un mecanismo nuevo y chico (`navegacionSeed`/`NavigationInstaller`) en vez de forzarla dentro de `ComponenteExtension`.

---

# Etapa 4.2 — Consolidación (solo documentación, sin cambios de código)

## Los installers de plataforma tienen dos naturalezas distintas

Hasta 4.1 el pipeline se leía como una sola familia homogénea. No lo es — hay dos grupos con una diferencia real:

```
Componente
  |
  ├── Instalación persistente (produce estado guardado en DB)
  |      ├── CapabilityInstaller       → capability_states
  |      ├── FieldVisibilityInstaller  → field_visibility
  |      ├── aplicarTiposDocumento()   → informes_tipos / plantillas_documento
  |      └── aplicarConfiguracionInicial() → configuracion_sistema
  |
  └── Resolución runtime (produce una vista derivada, no persiste nada)
         └── NavigationInstaller       → NavigationItem[] resueltos en cada request
```

**Los installers de plataforma pueden ser persistentes o resolutivos. `NavigationInstaller` pertenece al segundo grupo: transforma declaraciones de Componentes en elementos de navegación disponibles, sin guardar ese resultado en ninguna tabla** — exactamente el mismo criterio que ya regía `PlatformRegistry::navigationParaUsuario()` para `ModuleDefinition`, ahora explícito como categoría. Si en el futuro aparece un tercer caso resolutivo (ej. un `DashboardInstaller` que resuelve qué widgets mostrar), esta distinción ya está nombrada y no hay que redescubrirla.

## Los dos problemas del hallazgo de navegación, separados a propósito

- **Problema A (resuelto en 4.1)**: Componentes opcionales no podían aportar navegación. Solución: `navegacionSeed` + `NavigationInstaller`.
- **Problema B (pendiente, anotado como futura etapa "Normalización de navegación base")**: los 5 módulos "siempre activos" tienen su navegación desacoplada de sus capacidades — el menú real (`layouts/app.blade.php`) los muestra siempre, sin `@can`/capability check, mientras el controller sí exige `Gate::denies('agenda_access')` etc. No es una vulnerabilidad (la autorización real sigue en el controller), es una inconsistencia de UX. **No se mezcla con Odontología** — es un problema distinto (normalizar navegación de módulos ya existentes), con su propio momento.

## Nomenclatura — anotado, no decidido

`navegacionSeed` fue la primera evidencia de que un Componente no solo instala datos: también puede aportar **puntos de integración**. Con un solo caso (navegación) no hay necesidad real de generalizar el nombre. Si en el futuro aparecen `permissionSeed`, `dashboardSeed`, `notificationSeed` como necesidades reales (no antes), ahí sí valdría la pena unificar bajo un concepto tipo "Contribution Seeds". **No se implementa ahora.**

## Próximo paso real: Etapa 4.3 — primer dominio odontológico

La infraestructura de Componente ya cubrió, con necesidad real detrás de cada pieza: configuración, visibilidad, documentos, navegación, extensión (permisos). La pregunta que sigue abierta desde Etapa 4 — **"¿qué necesita obligatoriamente una extensión para existir dentro de la plataforma?"** — todavía no tiene respuesta, y no se responde en el papel: la responde el primer modelo odontológico real.

Etapa 4.3 (siguiente, no en esta sesión): una sola entidad, por ejemplo **Pieza dental** — definir la migración, la relación con `Paciente`, los permisos que necesita, y recién ahí observar si el `ComponenteExtension` actual alcanza o si aparece una necesidad genuina de extenderlo.

---

# Futuro (después de estabilizar Odontología): Demo Provisioning / Temporary Tenants

Necesidad real señalada por Francisco, todavía **sin construir** — se documenta ahora porque cambia el concepto de "Componente instalado" (deja de ser solo sobre una institución existente, pasa a ser también sobre un tenant temporal), pero se resuelve después de Odontología, no en paralelo.

## El problema

Hoy `demo.clinica.arioli.dev` entra directo al login. La necesidad real: que sea un **selector público de demos** antes del login, que provisione un tenant temporal (24hs) para el sistema elegido.

## Flujo esperado

```
demo.clinica.arioli.dev
        ↓
Pantalla pública de selección (Clínica / Odontología / Salud Mental / ...)
        ↓
Usuario elige un sistema
        ↓
Se provisiona un tenant temporal (pantalla de carga: "Preparando tu demo...")
        ↓
"Tu demo está lista — disponible las próximas 24 horas" + botón "Ingresar al demo"
        ↓
Login del tenant temporal generado
```

Pantalla de carga, pasos sugeridos: "✓ Creando entorno / ✓ Instalando componente / ✓ Aplicando configuración inicial / ✓ Preparando datos de ejemplo".

## `DemoInstance` — modelo de datos conceptual con ciclo de vida explícito (no implementado)

Actualizado post 6.1/6.2: ya existen `tenants` (`tenant_key`, `database`) y `Perfil` (`perfil_key`) de verdad — `DemoInstance` se apoya en ambos en vez de inventar sus propias claves. El objetivo de 6.3, tal como lo dejó Francisco, **no es "un cron que borra bases"** — es el ciclo de vida completo, con estado explícito, reintentable ante fallos, y trazable:

```
demo_instances
  id
  tenant_key       -- FK lógica a tenants.tenant_key
  perfil_key       -- FK lógica a config/platform/perfiles.php
  status           -- pendiente | provisionando | activa | expirada | eliminando | eliminada | error
  created_at
  expires_at       -- created_at + 24h
  activada_at
  eliminada_at
```

```
pendiente
    ↓
provisionando   -- tenants:crear corriendo (6.1, ya construido)
    ↓
activa          -- lista, usuario puede entrar
    ↓
expirada        -- pasó expires_at, todavía no se borró
    ↓
eliminando      -- job de limpieza tomó el registro
    ↓
eliminada       -- DB borrada, tenants + demo_instances actualizados
```

`error` es un estado transversal (puede llegar desde `provisionando` o `eliminando`) — mismo criterio de no auto-destruir usado en `tenants:crear`: un fallo deja el registro en `error` para que un humano decida, nunca reintenta solo ni borra a ciegas. Esto es lo que da **trazabilidad y reintentos** — y una base sin rediseño para mejoras futuras (extender duración, recordatorios antes de expirar) que Francisco ya anticipa como posibles, sin necesitar tocar el modelo cuando aparezcan.

Expiración: un job programado (o el `scheduler` que ya corre en el stack, ver `agenda:recordatorios` en `Kernel::schedule()`) mueve `activa`→`expirada`→`eliminando`→`eliminada`, usando el usuario de MySQL acotado del Gate G-01 — nunca el usuario global.

## Por qué esto valida (no contradice) las decisiones ya tomadas

- **El mismo `ComponenteInstaller`** (`CapabilityInstaller` + `FieldVisibilityInstaller` + `DocumentInstaller`/`aplicarTiposDocumento` + `ConfigurationInstaller` + `NavigationInstaller`) sería el responsable de preparar la demo — la única diferencia es que el Componente se instala sobre un **tenant temporal**, no sobre una institución existente. No hace falta un pipeline nuevo, hace falta decidir cuándo/cómo se crea ese tenant temporal (probablemente reutilizando `IdentifyTenant`/la tabla `tenants` ya existente, con un `status` adicional tipo `'demo'`).
- **`DemoCatalog`** (qué Componentes se ofrecen como demo público) es explícitamente **distinto** de `config/platform/componentes.php` (qué Componentes existen en la plataforma) — un Componente puede existir sin estar publicado como demo. Evita que el selector público lea directamente el catálogo interno.
- **Refuerza, en retrospectiva, la decisión de no convertir Odontología en `ModuleDefinition`**: si fuera un módulo "siempre activo", instalarlo temporalmente para una demo de 24hs no tendría un camino natural. Como `Componente` opcional, "instalar temporalmente" es la misma operación que "instalar", solo que sobre un tenant que se borra solo.

**No se mezcla con `ExtensionContribution`/Odontología dominio** — son capas relacionadas pero distintas: Odontología pregunta "¿qué necesita una extensión para existir dentro de una institución?"; Demo Provisioning pregunta "¿cómo provisionamos una institución temporal con uno o varios Componentes?". Se aborda como su propia etapa, después de estabilizar Odontología.

---

# Etapa 4.3 — Primera entidad odontológica real: el experimento que responde la pregunta pendiente

Objetivo del experimento (tal como lo planteó Francisco): instalar Odontología en una demo limpia y que aparezca `Paciente → ficha odontológica → odontograma vacío`. Se construyó lo mínimo — no el odontograma visual, no tratamientos, no presupuestos.

## Modelo de dominio

- **`Odontograma`** (`app/Modules/Odontologia/Models/Odontograma.php`) — `belongsTo Paciente`, `belongsTo User` (profesional), `hasMany PiezaDental`. Tablas normales (`odontogramas`, `piezas_dentales`), migradas vía `tenants:migrate` como cualquier otra — no pasan por `ComponenteExtension`, coherente con la distinción ya resuelta en Etapa 4 ("una migración es evolución de esquema, no capacidad instalable").
- **`PiezaDental`** — `belongsTo Odontograma`. Notación FDI adulta (32 piezas, 11-48).
- **Decisión de diseño (Opción B del planteo de Francisco)**: la pieza dental pertenece al **odontograma** (una fotografía del estado en una fecha), no al paciente directamente — porque el estado cambia (`sana` → `cariada` → `obturada` → `extraída`), y ese histórico vive en el odontograma que la registró, no en un dato estático del paciente. Cada odontograma nuevo crea sus 32 piezas en estado `sana` por defecto (una fotografía completa, no un diff incremental — la más simple de las dos formas de modelarlo, y suficiente para esta primera versión).

## El límite que se cuidó

`app/Models/Paciente.php` (Core) **no se tocó** — no tiene ningún método `odontogramas()`. La dependencia va en un solo sentido: `Odontograma` conoce `Paciente` (`use App\Models\Paciente`), Paciente no sabe que Odontología existe. Para navegar de paciente a sus odontogramas, el controller de Odontología consulta directo (`Odontograma::where('paciente_id', ...)`), no a través de una relación en el modelo Core.

## La fricción real encontrada — y por qué no justifica `ExtensionContribution` todavía

El dominio en sí (modelos, tablas, controller, vistas) se construyó **sin ninguna fricción** — cero necesidad de un mecanismo de extensión. La única fricción real apareció al intentar cumplir el objetivo completo: que se llegue a la ficha odontológica **desde la página del paciente**, no como una sección desconectada.

Para lograrlo hubo que **editar `resources/views/panel/pacientes/show.blade.php`** (vista de Historia Clínica, Core) y agregar un botón condicional:

```blade
@if(app(\App\Platform\PlatformRegistry::class)->isCapabilityEnabled('odontologia'))
@can('odontologia_access')
<a href="{{ route('panel.odontologia.paciente', $Paciente->id) }}">Odontología</a>
@endcan
@endif
```

Esto **es** acoplamiento — Historia Clínica ahora tiene una línea de código que menciona la capability `odontologia` por nombre. Es exactamente el problema que `extensionPoints()`/`ExtensionContribution` (v6-v7, todavía sin construir) fue diseñado para eliminar: que un módulo destino declare un punto de extensión y el que aporta contenido no obligue al destino a conocerlo por nombre.

**Conclusión, siguiendo la misma regla de toda la sesión (generalizar en la segunda repetición real, no en la primera)**: esta fricción es real pero **chica** — 7 líneas, un único punto, no requiere que Historia Clínica conozca nada del dominio interno de Odontología (modelos, tablas), solo una capability y un nombre de ruta. **No justifica construir `extensionPoints()` todavía.** Se deja anotado el candidato concreto: si un segundo Componente (ej. Pediatría, Medicina Laboral) necesita el mismo tipo de inyección en la misma página, ahí sí esas 2 repeticiones justifican generalizar este bloque en un extension point real (`historia_clinica.paciente.acciones` o similar) — no antes.

## Validado end-to-end con datos reales y descartables

Paciente real de `historias_demo`: `crear()` generó un odontograma con las 32 piezas en `sana` (confirmado, no 31 ni 33); las vistas de lista y detalle renderizan contenido real; el botón "Odontología" aparece en la ficha real del paciente con la capability habilitada, desaparece deshabilitada, reaparece restaurada. Datos de prueba borrados al final (odontograma + 32 piezas, cascada).

**La pregunta abierta desde Etapa 4** ("¿qué necesita obligatoriamente una extensión para existir dentro de la plataforma?") **queda respondida por este experimento, al menos para el primer caso real**: nada más allá de lo que `Componente` + `ComponenteExtension` + `navegacionSeed` ya dan. El único punto de fricción (un link condicional en una vista ajena) es aceptable como está — no se generaliza hasta que se repita.

---

# ADR: Extensiones de dominio v1

**Estado**: aceptado.

Un Componente puede agregar, sin ningún mecanismo genérico de extensión:

- modelos propios
- migraciones propias (código normal, corren en todos los tenants vía `tenants:migrate`, independiente de si el Componente está instalado)
- permisos propios (vía `ComponenteExtension::instalar()`)
- navegación propia (vía `navegacionSeed` + `NavigationInstaller`)
- rutas, controllers y vistas propias (código normal)

**No hace falta, y no se construye todavía**: `extensionPoints()`, `ExtensionContribution` (inyección tipada en un punto que otro módulo declara), `ContributionResolver`, `ExtensionPointRegistry`. Las integraciones puntuales con pantallas del Core se resuelven con un `if` explícito sobre la capability, dentro de la vista del Core (fricción aceptada mientras sea un solo caso — ver Etapa 4.3). Se revisita solo si un segundo Componente necesita el mismo tipo de inyección en el mismo lugar.

## Etapa 4.4 — ¿Es `ComponenteInstaller` suficiente como único orquestador? Sí, confirmado

En vez de construir un `OdontologiaInstaller` dedicado (que hubiera duplicado exactamente lo que `ComponenteInstaller` ya hace — la misma clase de infraestructura especulativa que este ADR acaba de descartar), se corrió la prueba definitiva: reset a estado genuinamente limpio en `historias_demo` (0 permisos `odontologia_*`, sin fila en `capability_states`, sin fila en `componentes_instalados`/`componente_extensiones`) y **un único llamado**:

```php
app(ComponenteInstallerContract::class)->instalar(['odontologia']);
```

Resultado, con datos reales, sin ningún paso manual adicional:

| Antes de instalar | Después de instalar (1 llamado) |
|---|---|
| 0 permisos `odontologia_*` | 3 permisos creados y asignados a Admin |
| capability `odontologia` inexistente | `enabled = true` |
| sin link en la ficha del paciente | link "Odontología" visible |
| — | página de ficha odontológica accesible |

`ComponenteInstaller` ya es el único orquestador necesario — no se construye `OdontologiaInstaller` ni ningún installer por-componente. El escenario completo que se quería probar (`Tenant limpio → Instalar Odontología → Paciente existente → Aparece ficha odontológica`) quedó confirmado end-to-end.

---

# Etapa 4.5 — ¿Se puede apagar un Componente sin dejar basura? Sí, con lo que ya existe

Antes de diseñar un `desinstalar()`, se corrió el experimento con el único mecanismo ya construido: apagar `capability_states.odontologia` (`enabled=false, source='manual'`) — la misma protección preset/manual que ya existía desde Etapa 2. Checklist real, con datos reales (`historias_demo`):

| Verificación | Resultado |
|---|---|
| Desaparece navegación | ✅ `NavigationInstaller::resolverPara()` devuelve 0 ítems |
| Permisos quedan inutilizados (sin removerlos) | ✅ `Gate::allows('odontologia_access')` → `false`, aunque `permission_role` sigue intacto — el Gate exige capability **y** permiso, apagar la capability alcanza |
| Capability deshabilitada | ✅ `enabled=false` |
| El dato ya generado (un odontograma + 32 piezas creado *antes* de apagar) sobrevive | ✅ Sigue en la base, íntegro — apagar es un cambio de estado, no un borrado |
| Pacientes / Historia Clínica siguen funcionando | ✅ La ficha del paciente renderiza completa (otras secciones intactas), el link de Odontología simplemente no aparece |
| La desactivación es estable (no se reactiva sola) | ✅ Se volvió a llamar `ComponenteInstaller::instalar(['salud_mental'])` (instalar OTRO componente) después de apagar Odontología — `odontologia` siguió `enabled=false, source=manual`, la protección ya construida en Etapa 2 sostiene la decisión |

**Conclusión**: no hizo falta construir nada. "Apagar" un Componente ya es una operación completa y segura con lo existente — es exactamente la distinción que señaló Francisco: la instalación es **código desplegado** (modelos, migraciones, rutas — siempre presentes) + **estado de activación** (`capability_states` — lo único que realmente cambia). Un `desinstalar()` que borre datos sería una operación **distinta y mucho más peligrosa** (pérdida irreversible de información clínica) — deliberadamente no se construye sin una necesidad real y explícita que lo pida; hoy "apagar" ya cubre el caso real (dejar de usar el Componente, sin perder lo ya cargado). Estado real de `historias_demo` restaurado al final (`odontologia` de nuevo `enabled=true, source=preset`, datos de prueba borrados).

---

# ADR: Ciclo de vida de componentes v1

**Estado**: aceptado.

Un Componente tiene **dos dimensiones independientes**:

**1. Presencia técnica** — el código existe en la aplicación (`app/Modules/Odontologia/`, sus migraciones, rutas, vistas). No depende del tenant — está desplegado o no está, para todos los tenants por igual.

**2. Activación funcional** — el tenant decide si usa esa capacidad (`capability_states.odontologia.enabled`, con `source = 'preset' | 'manual'`). Esto sí es por tenant, y es lo único que cambia al "instalar" o "desactivar".

**Regla**: desactivar una capacidad **nunca** elimina información histórica. Los datos que un Componente generó (odontogramas, piezas dentales, lo que sea) sobreviven a la desactivación — quedan inertes, no borrados. Una eliminación real de datos, si alguna vez hace falta, es una operación **distinta, explícita y deliberadamente separada** de "desactivar" — no se construye hasta que exista una necesidad concreta que la pida.

Corolario práctico de estas dos ADR juntas (Extensiones de dominio v1 + Ciclo de vida v1): la plataforma no es un sistema de plugins genérico — es un **sistema de capacidades activables dentro de un producto modular**. No hay instalación/desinstalación de código en runtime, no hay un registro de "qué versión de qué extensión corre en qué tenant" más allá de lo que `componente_extensiones`/`capability_states` ya trackean. Es una diferencia de alcance deliberada, no una limitación.

---

## Etapa 4 — cerrada

Con esto, la serie de experimentos que arrancó preguntando "¿qué necesita una extensión para existir dentro de la plataforma?" queda respondida con evidencia real, no con diseño especulativo:

| Pregunta | Resultado |
|---|---|
| ¿Puede existir una extensión aislada (sin que el Core la conozca)? | ✅ |
| ¿Puede agregar dominio propio (modelos, tablas, relaciones)? | ✅ |
| ¿Puede agregar permisos propios? | ✅ |
| ¿Puede agregar navegación propia? | ✅ |
| ¿Puede instalarse con un único llamado, sin pasos manuales? | ✅ |
| ¿Puede desactivarse sin dejar el sistema roto? | ✅ |
| ¿Pierde datos al desactivarse? | ❌ (correctamente, no) |

Ninguna de las abstracciones que parecían necesarias al principio de la etapa (`ExtensionContribution` genérico, `ExtensionRegistry`, un `Lifecycle Manager`, un installer por módulo) se terminó construyendo — cada vez que una capa parecía necesaria, el experimento con Odontología real demostró que sobraba. Lo que sí se construyó (`navegacionSeed`/`NavigationInstaller`, el ADR de extensiones de dominio, el ADR de ciclo de vida) nació de necesidad comprobada, no de anticipación.

**Próximo paso, cuando se retome**: Etapa 5 — un **segundo** Componente real con dominio propio (candidatos: Medicina Laboral, Pediatría, Laboratorio), no para agregar funcionalidad por agregarla, sino para responder la pregunta que un solo caso no puede responder — **qué de lo construido para Odontología era específico de Odontología, y qué es realmente un patrón reutilizable**. Recién ahí, si algo se repite entre los dos Componentes, esa repetición (no la imaginación) justifica generalizar.

## Cierre de Etapa 4

No se descubrió un sistema de plugins. Se descubrió un modelo de:

```
Aplicación
  +-- Componentes disponibles (presencia técnica, código desplegado)
  +-- Capacidades activables por tenant (capability_states)
```

Tres reglas quedaron congeladas: (1) un Componente no necesita un framework de extensiones mientras no haya repetición real; (2) código desplegado y activación por tenant son dos cosas distintas — eso es lo que permite vender módulos, activar por cliente, hacer demos, apagar capacidades y mantener históricos, todo con el mismo mecanismo; (3) el Core habilita capacidades, no conoce módulos futuros — Odontología domina sus propios datos, Historia Clínica nunca tuvo que saber que existía.

Encaje comercial (para cuando se retome el lado SaaS): `Plan Clínica Base + Módulo Odontología + Módulo Laboral + Módulo Pediatría` ya es un concepto natural sobre esta arquitectura, sin que el Core se vuelva un monstruo — cada módulo nuevo es un Componente más en el catálogo, no una modificación al núcleo.

**Frase de cierre**: la modularidad no se logró haciendo el sistema más abstracto; se logró haciendo que cada capacidad tenga dueño y que la activación sea explícita.

## Etapa 5 — Segundo Componente real: Medicina Laboral

Mismo patrón exacto que Odontología, entidad mínima: `EvaluacionLaboral` (`app/Modules/MedicinaLaboral/Models/EvaluacionLaboral.php`) — `tipo` (preocupacional/periódico/egreso), `fecha`, `estado` (apto/no_apto/apto_con_restricciones), `observaciones`. `belongsTo Paciente`/`User`; `Paciente.php` otra vez sin tocar. `MedicinaLaboralExtension implements ComponenteExtension` provisiona `medicina_laboral_access/create/edit` — código deliberadamente calcado de `OdontologiaExtension` para poder comparar de verdad.

**Validado con el mismo rigor**: reset a estado limpio (0 permisos, sin capability, sin `componentes_instalados`) → un único `ComponenteInstaller::instalar(['medicina_laboral'])` → 3 permisos creados y asignados, capability habilitada, link visible en la ficha real del paciente. Creación real vía formulario (tipo/fecha/estado/observaciones), lista renderiza el registro real, capability ON/OFF/restaurado controla el link. Todo con datos descartables, limpiado al final.

**Bug real encontrado y corregido en el camino**: `EvaluacionLaboral` sin `$table` explícito generaba `evaluacion_laborals` (pluralización naive de Eloquent, en inglés) en vez de `evaluaciones_laborales` — a diferencia de `Odontograma`, cuyo plural naive coincidía por casualidad con el real. Lección práctica, no arquitectónica: todo modelo con nombre en español necesita `$table` explícito, no confiar en la convención de Eloquent.

## Tabla comparativa — con datos reales, no proyectados

| Capacidad | Odontología | Medicina Laboral | ¿Generalizable? |
|---|---|---|---|
| Permisos (vía `ComponenteExtension`) | ✅ | ✅ | Ya generalizado — mismo contrato, mismo código calcado |
| Navegación (`navegacionSeed`) | ✅ | ✅ | Ya generalizado — mismo mecanismo desde Etapa 4.1 |
| Datos propios (modelo + tabla, sin tocar Paciente) | ✅ | ✅ | Ya generalizado — es la regla del ADR "Extensiones de dominio v1" |
| Instalación single-call | ✅ | ✅ | Ya generalizado — `ComponenteInstaller` sin cambios |
| Historial / no se borra al desactivar | ✅ | ✅ (mismo mecanismo, no re-probado con datos esta vez) | Ya generalizado — es el mecanismo de `capability_states`, no algo por-componente |
| **Fricción de navegación en `panel/pacientes/show.blade.php`** | ✅ (7 líneas) | ✅ (7 líneas, calcadas) | **Se repitió exacto — ver nota abajo** |
| Formulario de creación | Sin formulario (snapshot fijo de 32 piezas) | **Formulario real** (tipo/fecha/estado/observaciones) | No generalizable todavía — son formas de dominio genuinamente distintas, un formulario dinámico sería anticipar sin un tercer caso |
| Documentos (`tiposDocumentoSeed`) | No usado | No usado | Sigue sin datos para decidir nada |

## La decisión que queda pendiente: ¿la repetición del link ya cruza el umbral?

La única fila realmente nueva es la de fricción de navegación — **se repitió exactamente igual**, línea por línea, en el mismo archivo (`panel/pacientes/show.blade.php`). Es la primera vez que algo se repite dos veces en esta serie. La regla que se venía usando ("se generaliza en la segunda repetición real, no antes") apuntaría a que **este es el momento** de considerar `extensionPoints()`/`ExtensionContribution` (v6-v7) para este punto puntual — pero no se construyó todavía: queda como decisión explícita para la próxima ronda, no tomada unilateralmente acá.

## Próximo paso técnico en cola (diseñado, no implementado): contribución a la ficha del paciente

Decisión de Francisco: la abstracción, si se construye, debe ser del tamaño exacto del problema — **no** `extensionPoints()`/`ExtensionContribution` genérico (implicaría un sistema general sin evidencia para eso: sigue habiendo un único punto de extensión). El nombre y el contrato deben ser específicos de la ficha del paciente. Diseño ya acordado, listo para implementar cuando se retome:

```php
interface PatientProfileContribution
{
    public function actions(Paciente $paciente): array; // [['label' => ..., 'route' => ..., 'permission' => ...], ...]
}
```

`panel/pacientes/show.blade.php` pasaría de dos bloques `@if/@can` calcados (Odontología, Medicina Laboral) a un único `@foreach` sobre las acciones resueltas — el mismo patrón que `NavigationInstaller` ya usa para el menú, aplicado ahora al punto de fricción real encontrado en Etapa 5. Alcance acotado del experimento cuando se implemente: un único punto de contribución para la ficha del paciente, migrar Odontología y Medicina Laboral a usarlo, verificar que `show.blade.php` ya no menciona ningún Componente por nombre, **no tocar ninguna otra pantalla**. Si aparece un segundo tipo de contribución (menú lateral, dashboard, ficha de otra entidad), ahí sí — no antes — se generaliza en una interfaz más amplia.

**No se implementó en esta sesión** — quedó reordenado detrás de una necesidad de producto más urgente (ver siguiente sección).

## Etapa 5.1 — Perfil de institución (producto, no arquitectura)

Hallazgo real de Francisco, más importante que la abstracción técnica pendiente: **el demo mostraba todo a la vez** (Salud Mental + Odontología + Medicina Laboral instalados juntos en `historias_demo`, acumulados de las pruebas de esta sesión) — nada que un cliente real reconociera como su propio sistema. Un odontólogo no debería ver "Medicina Laboral"; un centro de medicina laboral no debería ver "Odontología".

**`Perfil`** (`app/Platform/DTO/Perfil.php`) — mismo espíritu que `Componente`: puramente descriptivo, no sabe cómo se aplica. `key`, `nombre`, `descripcion`, `componentes: string[]` (claves de `Componente`). `config/platform/perfiles.php` — catálogo, recogido automáticamente por el `mergeConfigFrom` genérico de `PlatformServiceProvider` (nada que registrar a mano): `clinica_general` (sin componentes opcionales), `odontologia`, `medicina_laboral`, `salud_mental`.

**No se construyó ningún instalador nuevo** — "aplicar" un perfil es literalmente `ComponenteInstaller::instalar($perfil->componentes)`, sin ninguna clase intermedia. Nota honesta sobre el alcance real: `Perfil` es tan aditivo como `Componente` — perfecto para un **tenant nuevo** (aditivo = exclusivo cuando no hay nada previo), pero aplicar un perfil sobre un tenant que ya tiene otros Componentes activos **no los desinstala solo** — eso sigue siendo el paso explícito de desactivación por `capability_states` (Etapa 4.5), deliberado, no automático.

**Corrección aplicada a `historias_demo`** (con confirmación de Francisco, porque cambiaba lo que ve cualquiera que entre al demo real hoy): se desactivaron `odontologia` y `medicina_laboral` (`enabled=false, source='manual'` — protegido contra reactivarse solo en una futura instalación de otro Componente, mismo mecanismo de Etapa 4.5), dejando activo solo lo que corresponde al perfil `salud_mental` ya decidido para ese tenant desde el inicio de la sesión. Los datos de prueba de Odontología/Medicina Laboral generados durante Etapa 5 ya habían sido borrados; ninguna información real se perdió.

**Relación con Demo Provisioning (sección futura ya documentada)**: `Perfil` es la pieza que le faltaba a ese diseño — "elegir un perfil" en el selector público de demos es exactamente `ComponenteInstaller::instalar($perfil->componentes)` sobre un tenant temporal recién creado (aditivo = exclusivo, porque el tenant nace vacío). El catálogo de perfiles ya existe y está listo; el resto de Demo Provisioning (tenant temporal, 24hs, selector público) sigue siendo trabajo futuro, sin construirse todavía.

---

# Etapa 6 — Perfiles de Implementación y Demos Especializadas

Objetivo completo (Francisco): selector de perfil al crear un tenant (real o demo), demo temporal por perfil con datos específicos, expiración automática, y wizard tanto para demos como para altas de clientes reales. Alcance grande — se divide igual que Etapa 4, empezando por la pieza que todo lo demás necesita.

## Hallazgo de seguridad real, antes de construir nada

`saas_user` (el usuario de MySQL que usa esta app) tiene privilegios `GRANT ALL ... ON *.* ... WITH GRANT OPTION` — **compartido por toda la infraestructura del servidor** (loteos, tallerpro, arioli-saas, historias-clinicas usan el mismo usuario). Es decir: `CREATE DATABASE`/`DROP DATABASE` funcionan, pero un bug de targeting en un futuro job de borrado automático (Etapa 6.3, expiración de demos) no quedaría acotado a `historias_*` — podría alcanzar cualquier base del servidor. **Recomendación, todavía no aplicada**: crear un usuario de MySQL dedicado y acotado (`GRANT ... ON `historias\_%`.* TO ...`) antes de construir el job de expiración automática (6.3). No bloquea 6.1 (creación manual, un comando a la vez, bajo control humano) — sí debería resolverse antes de automatizar el borrado.

## Etapa 6.1 — Provisionar un tenant nuevo por código

Comando `tenants:crear {key} {--perfil=}` (`app/Console/Commands/TenantsCrear.php`). Secuencia: `CREATE DATABASE historias_{key}` → registrar en `tenants` (`status='en_migracion'`) → cambiar la conexión a la DB nueva → `migrate --force` (todo el historial de migraciones, no solo las de plataforma) → `db:seed` (la cadena base: `PermissionsTableSeeder`, `RolesTableSeeder`, `PermissionRoleTableSeeder`, `SecretariaRoleSeeder`, `UsersTableSeeder`, `RoleUserTableSeeder` — sin esto el tenant nace sin ningún usuario con el que loguearse) → `CapabilityStatesSeeder` (habilita los 5 módulos "siempre activos") → si se pasó `--perfil`, `ComponenteInstaller::instalar($perfil->componentes)` → `status='activo'`.

Si algo falla a mitad de camino, **no se borra la base automáticamente** — el tenant queda con `status='error'` para que un humano decida (mismo criterio de no auto-destruir de toda la sesión).

**Validado con un tenant real y descartable** (`historias_prueba_etapa6`, perfil `odontologia`, borrado al final):

| Verificación | Resultado |
|---|---|
| Migraciones (todo el historial, no solo plataforma) | ✅ corrieron todas |
| Usuarios sembrados (incluye admin) | ✅ 3 usuarios, `admin@admin.com` existe |
| Roles / permisos base | ✅ 3 roles, 68 permisos |
| Capability core (`historia_clinica`) habilitada | ✅ |
| Perfil `odontologia` aplicado (`componentes_instalados`, capability `odontologia`) | ✅ |
| `tenants` (maestra) refleja el nuevo tenant | ✅ `status=activo, last_migration_status=ok` |
| Rechaza clave duplicada | ✅ |

**Todavía no construido** (próximos pasos de Etapa 6, en orden): 6.2 datos de demo específicos por perfil (seeders); 6.3 expiración automática 24hs (requiere el usuario de MySQL acotado mencionado arriba); 6.4 selector público de demo; 6.5 wizard de alta para clientes reales (perfil + opción "Personalizado" con selección manual de Componentes). Cada una es una pieza independiente — no se construyen todas de una.

## Etapa 6.2 — Escenarios Demo (no "seeds", historias clínicas chicas y coherentes)

Decisión de Francisco, importante: no se trata de poblar la base — se trata de que en 5 minutos un especialista sienta que está viendo su propio consultorio. Un `OdontologiaDemoSeeder`/`MedicinaLaboralDemoSeeder`/`SaludMentalDemoSeeder` (`database/seeders/`) por perfil, cada uno chico y con una narrativa real, no datos aleatorios:

- **Odontología**: 2 profesionales con firma digital, 5 pacientes. María González tiene **dos** odontogramas — uno de hace 3 meses con la pieza 26 `cariada` ("se detecta caries"), otro de hoy con la misma pieza `obturada` ("restauración realizada, próximo control en 6 meses") — el histórico real que el modelo (Odontograma como fotografía fechada, no el paciente) fue diseñado para soportar desde Etapa 4.3. Los otros 4 pacientes tienen variaciones reales (ausente, corona, cariada) — nunca 32 piezas "sana" para todos.
- **Medicina Laboral**: reutiliza `PacienteLaboral` (Core, ya existente) para el dato de empresa — **no hizo falta ninguna entidad nueva** para "Metalúrgica Delta"/"Logística del Sur SA". Juan Pérez con 2 evaluaciones (preocupacional apto → periódico apto con restricciones, con motivo real: exposición a ruido).
- **Salud Mental**: Laura Fernández, usando exclusivamente las sub-fichas de Paciente que ya existen en el Core (`PacienteFichaAdmision`, `PacienteProblematica`, `PacienteHistorialTratamientos`) — sin inventar sesiones/escalas clínicas, porque esas entidades todavía no existen (sería infraestructura nueva sin necesidad comprobada, fuera del alcance de esta etapa).

**Conectado a `tenants:crear` vía `--con-datos-demo`** (mapeo simple perfil→seeder dentro del comando, no una propiedad nueva en `Perfil` — un único consumidor no amerita más).

**3 bugs reales encontrados y corregidos al probar con un tenant real** (`pacientes` tiene más columnas obligatorias sin default de lo que parecía a simple vista: `fecha_nac`, `edad`, y luego `estado_civil`/`obra_social`/`n_afiliado`/`provincia`/`localidad`/`calle`/`calle_numero`) — se resolvió con un método `datosBase()` por seeder que aporta valores neutros para lo que no hace a la narrativa, y overrides explícitos para lo que sí importa.

**Validado con un tenant real completo, creado y borrado**: `tenants:crear demo_odonto_test --perfil=odontologia --con-datos-demo` → 5 pacientes, 2 profesionales con firma, la narrativa de María González verificada exactamente (pieza 26: `cariada` en la fecha de hace 3 meses → `obturada` hoy, con las observaciones correctas), 8 piezas no-`sana` distribuidas en el tenant (no una base plana). `capability_states` del tenant nuevo mostró **solo** `odontologia` como extra — ninguna mezcla con Medicina Laboral ni Salud Mental, resolviendo directamente el problema original ("el demo me está mostrando todo").

## Gate G-01 — Automatización de ciclo de vida (formal, bloqueante)

**Ningún proceso automático que cree o elimine tenants podrá implementarse utilizando un usuario MySQL con privilegios globales (`GRANT ALL ON *.*`). Antes de Etapa 6.3 deberá existir un usuario dedicado con permisos limitados exclusivamente al patrón `historias_%` (o el esquema que se defina), de modo que un error en la automatización no pueda afectar bases de datos ajenas a Historias Clínicas.**

No es un pendiente — es un requisito de seguridad que bloquea el inicio de 6.3. `tenants:crear` (6.1) queda exceptuado porque es manual, un tenant a la vez, bajo control humano directo — el riesgo real aparece recién con un job desatendido de borrado automático.

### Gate G-01 — RESUELTO

**Validación experimental previa (no asumida)**, con un usuario descartable (`g01_test_user`) y un grant candidato — `CREATE, ALTER, DROP, INDEX, REFERENCES, SELECT, INSERT, UPDATE, DELETE ON \`historias_%\`.*` —, ambas preguntas confirmadas con evidencia real y sin sorpresas:

1. **`CREATE DATABASE`/`DROP DATABASE historias_xxx`** funcionan con exactamente ese grant — no hace falta ningún privilegio administrativo de servidor (`CREATE`/`DROP` alcanzan también a nivel de base de datos cuando el patrón del grant cubre el nombre).
2. **El flujo completo** (`CREATE DATABASE → migrate → seed → Perfil → datos demo`) corrió de punta a punta con `tenants:crear g01_full_test --perfil=odontologia --con-datos-demo` usando *solo* ese usuario — cero privilegios adicionales necesarios.

**Implementación real** (usuario descartable borrado, reemplazado por el definitivo):

- Usuario de producción `historias_tenant_admin`@`%`, con exactamente el grant validado arriba. Sin `SUPER`, `FILE`, `PROCESS`, `SHUTDOWN`, `RELOAD`, `CREATE USER`, `GRANT OPTION`, replicación, administración de roles ni de variables de servidor — ninguno resultó necesario.
- Nueva conexión Eloquent `mysql_tenant_admin` en `config/database.php`, con credenciales propias (`TENANT_ADMIN_DB_USERNAME`/`TENANT_ADMIN_DB_PASSWORD` en `.env`) — **nunca** comparte la conexión `mysql` (la de `saas_user`, con privilegios de toda la infraestructura del servidor).
- **Separación conceptual aplicada dentro de `TenantsCrear`** (dos métodos, no dos clases — un único consumidor no amerita más): `crearBaseDeDatos()` (Database Provisioning — la base física) vs. `provisionarTenant()` (Tenant Provisioning — migraciones, seeders, Perfil, datos demo). Hoy usan el mismo usuario MySQL; la separación es de responsabilidad en el código, no de credenciales — se revisará si conviene separarlas también a nivel de usuario si `DemoInstance` (Etapa 6.3) lo exige.
- Detalle de implementación no trivial: ni el modelo `Tenant` ni los installers de Platform (`ComponenteInstaller`, `CapabilityInstaller`, `FieldVisibilityInstaller`...) fijan una conexión explícita — todos dependen de la conexión *default* de Eloquent. `TenantsCrear` reapunta temporalmente `database.default` a `mysql_tenant_admin` durante toda su ejecución (restaurado al finalizar), replicando sobre la nueva conexión el mismo mecanismo que la versión anterior aplicaba sobre `mysql`.
- **Revalidado end-to-end en producción** con el usuario real (no el descartable): `tenants:crear g01_real_test --perfil=salud_mental --con-datos-demo` corrió sin overrides de entorno (a diferencia de la prueba experimental, que forzaba `DB_USERNAME`/`DB_PASSWORD` — acá la conexión ya usa `historias_tenant_admin` por configuración). Confirmado: 68 migraciones, seeders base, componente `salud_mental` instalado, dato demo de Laura Fernández presente. Limpieza posterior: base dropeada con el propio `historias_tenant_admin` (dentro de su grant) y fila de `tenants` borrada.

Con esto resuelto, Etapa 6.3 (ciclo de vida de `DemoInstance`) queda desbloqueada.

## Etapa 6.3.1 — Ciclo de vida manual de `DemoInstance` (validado, sin automatizar)

Antes de escribir cualquier scheduler, se construyó y probó el ciclo completo con acciones **manuales** (comandos ejecutados a mano por un humano, ninguno programado): `demo_instances` (migración en `database/migrations/platform/`, igual que `tenants` — no corre vía `tenants:migrate`), modelo `DemoInstance`, y tres comandos artisan: `demo:crear {perfil} {--horas=24}`, `demo:expirar {id}`, `demo:limpiar {id}`.

**División de responsabilidad frente a Gate G-01**: `demo:crear` reutiliza `tenants:crear` (6.1) tal cual — no duplica lógica de provisión. `demo:expirar` solo escribe un flag en `demo_instances` (DB maestra, conexión normal) — no toca ninguna base de tenant, por eso no necesita `mysql_tenant_admin`. `demo:limpiar` es el único paso destructivo (`DROP DATABASE` + borrar el registro de `tenants`) y es el único que usa la conexión acotada del Gate G-01 — nunca `saas_user`.

**Validado end-to-end en producción, con datos reales y descartables**:

1. `demo:crear odontologia --horas=24` → `DemoInstance #1` pasó `pendiente → provisionando → activa` en una sola corrida, tenant real `demo_odontologia_houjpu` con 5 pacientes y el componente `odontologia` instalado (mismo resultado que 6.1/6.2, ahora orquestado).
2. `demo:expirar 1` → `activa → expirada`. Reintentarlo (`demo:expirar 1` de nuevo) fue rechazado con exit code 1 porque el estado ya no era `activa` — el guard-rail de transición funciona.
3. `demo:limpiar 1` → `expirada → eliminando → eliminada`, usando `historias_tenant_admin`. Confirmado: la base `historias_demo_odontologia_houjpu` desapareció de `SHOW DATABASES`, el registro de `tenants` se borró, `demo_instances` quedó con `eliminada_at` seteado.

**Camino de falla probado deliberadamente** (la pregunta explícita de Francisco: "qué pasa si falla a mitad de camino"): se creó una segunda `DemoInstance`, se la expiró, y se borró a mano su fila de `tenants` para simular una inconsistencia real (ej. una corrida previa que falló entre borrar la base y borrar el registro). Al correr `demo:limpiar 2`, el comando detectó que no había `Tenant` asociado, **no tocó la base física** (siguió existiendo, verificado con `SHOW DATABASES`), y dejó `DemoInstance` en `status = 'error'` con `error_message` explicando la inconsistencia — exactamente el criterio de `tenants:crear`: ante cualquier duda, un humano decide, nada se reintenta ni se borra a ciegas. Mismo guard adicional (no ejercitado en esta prueba pero presente en el código): si `tenant->database` no matchea `historias_%`, `demo:limpiar` aborta sin ejecutar el `DROP`.

Los 4 registros y 2 bases físicas de esta validación se borraron al terminar — no queda ningún artefacto de prueba en producción.

**Etapa 6.3.2 (automatización) queda para después**, y consiste únicamente en programar lo que ya existe y ya se probó: un scheduler que corra `demo:expirar` sobre las `activa` con `expires_at` vencido y `demo:limpiar` sobre las `expirada`, más logging/alertas sobre las que terminen en `error`. No hace falta nueva infraestructura — los tres comandos ya son la pieza reutilizable.

## Etapa 6.3.2 — Automatización: el scheduler orquesta, no reimplementa

Antes de programar nada se validó el modelo operativo real (no se asumió): **historias-clinicas corre como una única aplicación/codebase/contenedor** (`saas_historias`), con los tenants aislados únicamente por base de datos y por contexto de request (`IdentifyTenant` reapunta la conexión `mysql` según el subdominio) — no hay un contenedor ni proceso por tenant, y los demos no son una aplicación aparte: son tenants temporales, indistinguibles a nivel de código/datos de un cliente real, con la única diferencia de tener una fila en `demo_instances`. Confirma que un scheduler es una propiedad de **la aplicación**, no de cada tenant — uno solo alcanza para todos.

**Hallazgo real antes de implementar**: no existía ningún disparador de `schedule:run` para `historias_app` — ni contenedor scheduler propio, ni cron de host. El único `scheduler` (`saas_scheduler`) que corre en `docker-compose.prod.yml` apunta a `./src`, la app central de licencias (`saas_central`), completamente distinta. Consecuencia directa: `agenda:recordatorios` (`dailyAt('08:00')`, preexistente) nunca se ejecutó sola en producción. Esto confirma que el problema a resolver era el **scheduler general de la aplicación**, no algo específico de demos — `demo:expirar-vencidas`/`demo:limpiar-vencidas` simplemente se suman al mismo scheduler que ya hacía falta.

**Implementación**: nuevo servicio `historias_scheduler` en `docker-compose.prod.yml` (mismo patrón que `scheduler`/`saas_scheduler` de la app raíz — `sh -c "while true; do php artisan schedule:run --verbose --no-interaction; sleep 60; done"`), mismo build/entorno que `historias_app`, container aparte. `Kernel::schedule()` ahora registra:

```php
$schedule->command('agenda:recordatorios')->dailyAt('08:00');
$schedule->command('demo:expirar-vencidas')->hourly();
$schedule->command('demo:limpiar-vencidas')->hourly();
```

Ningún comando tiene lógica nueva — el scheduler solo dispara `demo:expirar-vencidas`/`demo:limpiar-vencidas` (6.3.2, que a su vez delegan a `demo:expirar`/`demo:limpiar`, 6.3.1, sin reimplementarlos). Período de gracia entre `expirada` y limpieza física: **6 horas**, usando el nuevo campo `expirada_at` (no `updated_at` — ese puede cambiar por otros motivos mientras el registro sigue expirado, y hubiera corrido el riesgo silencioso de reiniciar el conteo). Auditoría: canal de log dedicado `demo-lifecycle` (`storage/logs/demo-lifecycle-*.log`, driver `daily`, 30 días) — una línea por acción del scheduler, sin tabla nueva.

**Bug real encontrado y corregido al desplegar** (no al diseñar): `docker-entrypoint.sh` tenía `exec php-fpm` hardcodeado al final, ignorando por completo cualquier `command:` pasado por `docker-compose` — el primer intento de levantar `historias_scheduler` terminó corriendo *otra instancia de PHP-FPM* en vez del loop del scheduler. Peor: como ambos contenedores comparten el mismo volumen (`./apps/historias-clinicas:/var/www/html`), el entrypoint también re-ejecutaba `config:cache`/`route:cache`/`view:cache`/`storage:link` en cada arranque — el `historias_scheduler` hubiera estado reescribiendo innecesariamente (y en potencial carrera) los archivos de caché de los que depende `historias_app` en vivo. Corregido: el bloque de build de assets + cache warming + storage:link ahora solo corre `if [ "$1" = "php-fpm" ]` (o sea, solo en el contenedor que sirve HTTP), y el script siempre termina con `exec "$@"` en vez de `exec php-fpm` — mismo comportamiento para `historias_app` (su `command` implícito, heredado de la imagen base, sigue siendo `php-fpm`), comportamiento correcto y sin carrera para `historias_scheduler`.

**Validado en producción con evidencia real, no solo revisión de código**:
- Con el schedule temporalmente en `everyMinute()` (revertido a `hourly()` apenas confirmado), se creó una demo con `--horas=0` y se observó, sin ninguna invocación manual, que `demo:expirar-vencidas` la detectó y expiró sola — confirmado en `demo_instances` (`status`, `expirada_at`) y en el log dedicado.
- Confirmado que `historias_app` (el contenedor que sirve tráfico real) no se reinició ni se vio afectado en ningún momento de este trabajo — se validó `docker compose ps` (mismo uptime) y una request real a `https://clinica.arioli.dev/` (200 OK) después de cada cambio de infraestructura.
- `docker-compose.prod.yml` en el servidor quedó actualizado con el nuevo servicio; en el repo se decidió **no** poblar ese archivo (está trackeado pero vacío — el contenido real vivía solo en el servidor, decisión previa a esta sesión) para no mezclar en un mismo commit configuración de otras apps del VPS (loteos, tallerpro) que no corresponde versionar como efecto colateral de este trabajo.

Con esto, Etapa 6.3 (`DemoInstance`) queda completa: ciclo manual validado (6.3.1) + automatización que orquesta sin reimplementar (6.3.2).

## Etapa 6.4 — Autoservicio público de demos

Objetivo: que cualquier visitante elija un perfil, deje nombre/email, y en segundos tenga una demo real lista para entrar — reusando el ciclo de vida ya construido (6.1-6.3.2), no uno nuevo.

### El diseño de fondo: `tenant_key` (interno) vs. `slug` (público)

Antes de tocar el flujo público se corrigió el problema real que el `_` en `demo_odontologia_a8f29c` solo evidenció: `tenant_key` cumplía dos roles a la vez (nombre de base de datos interno + identificador de ruteo por subdominio), y funcionaba nada más porque ningún `tenant_key` real había usado guión bajo todavía.

```
tenants
-------
tenant_key   -- interno, sufijo de historias_{tenant_key}, nunca visible, inmutable
slug         -- NUEVO: público, DNS-safe ([a-z0-9-]), único, regenerable sin tocar tenant_key/database
```

- **Derivación**: el slug de una demo se deriva de su `tenant_key` reemplazando `_` por `-` (`demo_odontologia_a8f29c` → `demo-odontologia-a8f29c`) — decisión explícita de no generar dos aleatorios independientes sin un caso real que lo justifique.
- **`tenants:crear`** ahora acepta `--slug=` (opcional; por defecto deriva del `key`) y valida formato y unicidad, igual que ya hacía con `tenant_key`. Backfill trivial de las 2 filas reales existentes (`demo`, `default`): ambas ya eran slugs válidos, sin colisión.
- **`IdentifyTenant` cambia de mecanismo, no solo de regex**: antes resolvía por *adivinanza* (¿existe una base con este nombre en `information_schema`?), sin mirar `tenants` para nada más que la página de error. Ahora hace `Tenant::where('slug', $slug)->where('status', 'activo')->first()` — una consulta explícita contra el registro real, que de paso cierra un gap: un tenant a medio provisionar (`en_migracion`/`error`) antes igual "resolvía" porque solo se chequeaba existencia de base; ahora no.
- **`tenant_key`, no `slug`, sigue siendo la base del namespacing interno** (cookie de sesión, prefix de caché, ruta de storage) — deliberado: el slug puede regenerarse el día de mañana, y si el storage path dependiera de él, una regeneración dejaría archivos huérfanos. Como hoy `slug == tenant_key` para los 2 tenants reales existentes, este cambio no movió ningún dato.
- **Hallazgo adicional, corregido de una vez** (mismo espíritu de "arreglar el problema de fondo"): el dominio central sin subdominio (`clinica.arioli.dev`, el propio `APP_URL`) tenía 3 partes igual que exige el chequeo viejo (`< 3` partes → no resuelve), así que **se resolvía a sí mismo como si "clinica" fuera un tenant** — verificado en vivo antes del fix (mostraba `tenant-not-found` con "clinica" en el título). Se subió el mínimo a 4 partes (`{slug}.clinica.arioli.dev`), liberando el dominio central para servir rutas normales — la app central ahora muestra el login real en vez de una página de "subdominio disponible" fantasma.

### El caso de uso se extrae a un servicio (segunda repetición real)

`ProvisionDemoService` (`App\Platform\Services`, con su contrato en `App\Platform\Contracts\Services`) absorbe lo que antes vivía solo dentro de `demo:crear`: generar `tenant_key`+`slug`, crear el registro `DemoInstance`, invocar `tenants:crear`, y activar o marcar error. `demo:crear` (CLI) quedó como una cáscara fina que delega al servicio. Se extrajo recién ahora porque apareció el segundo consumidor real (CLI + HTTP) — no antes.

### El flujo público

- `GET /demo` — perfil cards, leyendo directo de `config/platform/perfiles.php` (no se construyó un `DemoCatalog` separado — no hay hoy un perfil que deba existir pero no ofrecerse como demo).
- `GET /demo/{perfil}` — formulario (nombre, email).
- `POST /demo/{perfil}` — valida, llama a `ProvisionDemoServiceContract::provisionar()` **síncrono** (sin colas — no existe worker en esta app y el ciclo tarda unos segundos, agregar infraestructura para evitar esa espera sería anticiparse sin necesidad real), redirige a `listo`.
- `GET /demo/listo/{slug}` — URL, usuario (`admin@admin.com`) y contraseña (`password`) — las mismas que siembra `UsersTableSeeder` en cualquier tenant nuevo. Sin auto-login: cero superficie de ataque nueva, cero tokens, reusa 100% el login real ya existente.
- Las 4 rutas están protegidas con `throttle` y solo son alcanzables sin contexto de tenant resuelto (`DemoPublicoController::abortSiHayTenant` — 404 si `tenant_id` ya está seteado en el request).

**Bug real encontrado probando el flujo, no diseñando**: el middleware `throttle:X,Y` de Laravel, sin un tercer parámetro `prefix`, firma su contador **solo por dominio+IP** — ignora la ruta. Las 4 rutas (`index`, `solicitar`, `crear`, `listo`) compartían el mismo contador, así que las llamadas de prueba a las rutas GET ya habían consumido el límite de 3 intentos del POST antes de llegar a probarlo, devolviendo 429 en el primer submit real. Corregido agregando un `prefix` distinto por ruta (`throttle:30,1,demo-index`, etc.) — cada una con su propio contador.

**Validado en producción, de punta a punta, con datos reales y descartables**: `GET /demo` → `GET /demo/odontologia` → `POST /demo/odontologia` (nombre real, email real) → redirect a `/demo/listo/demo-odontologia-pqinad` con URL y credenciales correctas → `https://demo-odontologia-pqinad.clinica.arioli.dev/login` resolvió al tenant nuevo (antes: `tenant-not-found`) → confirmado en DB: `tenants.slug` y `demo_instances.solicitante_nombre/email` con los valores correctos. Limpieza con el ciclo ya existente (`demo:expirar` + `demo:limpiar`) — verificado que el subdominio vuelve a mostrar "disponible" después de borrar. Regresión: `demo.clinica.arioli.dev` (tenant real preexistente) sigue resolviendo igual que antes. Guard probado: `demo.clinica.arioli.dev/demo` devuelve 404 (no se puede disparar el flujo público desde dentro de un tenant).

**Límite de la validación automatizada**: el login real está protegido con Cloudflare Turnstile (obligatorio, preexistente, nada que ver con esta etapa) — no se puede completar un login por `curl`. Se verificó todo lo demás (URL correcta, credenciales correctas, página de login real renderizada); el paso de autenticación en sí requiere un navegador real, exactamente como para cualquier tenant existente.

**Aviso de Cloudflare, no bug**: la pantalla `listo` muestra el email en texto plano en el HTML fuente, pero Cloudflare (Email Address Obfuscation) lo reescribe automáticamente como un enlace ofuscado — un navegador real lo decodifica solo vía su JS inyectado. Confirmado decodificando el valor a mano: es el email correcto.

## Corte — Etapa 6 pausa acá

Con 6.1 (provisión por código) y 6.2 (Escenarios Demo coherentes) validados de punta a punta, el resto de Etapa 6 cambia de naturaleza: deja de ser arquitectura/producto y pasa a ser infraestructura y operaciones (credenciales, jobs automáticos, tolerancia a fallos). Se cierra la sesión acá a propósito, no por agotamiento del trabajo.

**Próxima sesión, en orden**: resolver Gate G-01 (usuario MySQL acotado) → 6.3 (expiración automática 24hs) → 6.4 (selector público de demo) → 6.5 (wizard de alta para clientes reales, perfil + "Personalizado").

**Confirmado antes de arrancar 6.3**: el Gate G-01 no es negociable — ningún proceso automático de creación/borrado de tenants se implementa hasta tener el usuario MySQL acotado a `historias_%` resuelto y probado. El objetivo de 6.3 no es "escribir el job de borrado" — es el ciclo de vida completo de `DemoInstance` (ver sección de arriba), con la misma metodología de siempre: resolver el primer caso real, observar la fricción, generalizar recién si aparece una segunda repetición. No se introduce infraestructura nueva si la existente (`tenants:crear`, `ComponenteInstaller`, `Perfil`) alcanza.

## Etapa 6 — cierre real

Con Gate G-01, 6.3.1, 6.3.2 y 6.4 completos y validados en producción, la nota de "corte" de arriba queda como registro histórico de una pausa intermedia — Etapa 6 está completa de punta a punta: `tenants:crear` (6.1) → Escenarios Demo (6.2) → usuario MySQL acotado (Gate G-01) → ciclo de vida manual de `DemoInstance` (6.3.1) → scheduler de la aplicación (6.3.2) → autoservicio público, `tenant_key`/`slug`, `IdentifyTenant` por consulta real (6.4). El flujo completo — elegir perfil, solicitar, provisionar, obtener URL, entrar con datos de ejemplo — quedó probado con datos reales y descartables, reutilizando la misma arquitectura multi-tenant que sirve a clientes reales. La separación `tenant_key`/`slug` y el `IdentifyTenant` basado en consulta a `tenants` (no en adivinanza) quedan consolidados como parte permanente del modelo de la plataforma, no como algo específico de demos.

## Gate G-02 — Credenciales iniciales (formal, bloqueante)

**Ningún tenant destinado a un cliente real podrá provisionarse con credenciales conocidas o compartidas. La creación segura del administrador inicial pasa a ser un requisito previo del flujo comercial.**

Motivo: `UsersTableSeeder` siembra hoy `admin@admin.com` / `password` en **cualquier** tenant nuevo — aceptable (hasta conveniente) para una demo descartable de 24-30hs, pero es una credencial pública y conocida si se usa para provisionar un cliente real. Etapa 6.5 no puede reutilizar `tenants:crear`/`ProvisionDemoService` tal cual para clientes reales sin resolver esto primero.

No es un pendiente — es un requisito de seguridad que bloquea el inicio de 6.5, con el mismo criterio que el Gate G-01: no se implementa el onboarding de clientes reales hasta tener un mecanismo de alta de administrador inicial que no dependa de una contraseña compartida conocida de antemano.

## Etapa 6.5 — Onboarding de cliente real (Gate G-02 resuelto, lado historias-clinicas)

### El hallazgo que definió el alcance

Antes de diseñar nada se investigó qué existía ya. Resultado: hay una **app central completa** (`/opt/arioli-saas/src`, compartida por loteos/tallerpro/historias-clinicas) con CRM de clientes, licencias, planes, portal de autoservicio del cliente, y un patrón de provisioning ya usado por otro producto (`Api\ProvisioningController::provisionLoteos`). Para historias-clinicas específicamente, ya existía un job (`ProvisionHistoriasInstance`) y un comando (`provision:historias-clinicas-direct`) conectados desde `Admin\TenantController::store()` — pero con dos problemas de fondo, encontrados leyendo el código real, no imaginados:

1. **Violaba exactamente lo que Gate G-02 prohíbe**: el formulario de alta le pedía la contraseña **al staff**, y esa contraseña se enviaba al cliente por email en texto plano.
2. **No usaba nada de la plataforma modular construida en 6.1-6.4**: clonaba tablas a mano (`CREATE TABLE ... LIKE historias_default.*`), sin `tenants:crear`, sin `ComponenteInstaller`, sin `Perfil`.

De paso se confirmó que ya existe un patrón correcto para esto en el mismo sistema, solo que aplicado a otra cosa: `HostingCredentialController` — link firmado, temporal, un solo uso, el cliente define su propia contraseña, nunca se transmite.

**Decisión de alcance** (explícita, no asumida): resolver el lado de historias-clinicas — un endpoint interno nuevo que reemplace al mecanismo viejo, usando el motor real (`tenants:crear`), más la pantalla de reclamo de credenciales — **sin tocar `/opt/arioli-saas/src`**, que es infraestructura compartida por 3 productos y queda fuera del alcance de esta sesión. Queda documentado como el próximo gap a cerrar del lado central.

### El diseño

**La frontera**: historias-clinicas no sabe nada de pagos, planes, CRM ni clientes — solo recibe una orden de provisión. `POST /internal/provision` (mismo mecanismo que ya usan `internal.demo.seed/reset`: `routes/internal.php`, protegido por `ValidateApiKey`/bearer token compartido). Payload: `tenant_key`, `perfil`, `admin_nombre`, `admin_email` — **nunca una contraseña**.

**Un único mecanismo de provisión**: `ProvisionClienteService` reutiliza `tenants:crear` tal cual (Database + Tenant Provisioning ya validados en Gate G-01/6.1) — no existe un segundo camino para crear la base de un tenant. `tenants:crear` se generalizó levemente para esto: su regex de `key` ahora acepta guión medio además de guión bajo (`^[a-z0-9_-]+$`), porque el identificador de un cliente real no necesita ofuscarse como el de una demo — con eso, `slug` termina siendo idéntico a `tenant_key` sin ninguna transformación.

**El hallazgo más importante del análisis, no algo imaginado de antemano**: `UsersTableSeeder` siempre siembra `admin@admin.com`/`user@user.com`/`secretaria@sistema.com` con la misma contraseña conocida, y `RoleUserTableSeeder` asigna roles **por ID**, no por email. Eso permitió una solución quirúrgica: `tenants:asegurar-administrador` (nuevo comando, usa `mysql_tenant_admin` — Gate G-01, porque es ciclo de vida de tenant) transforma al usuario id=1 en el administrador real (nombre/email reales desde el arranque, no un renombrado posterior — contraseña aleatoria e inutilizable hasta el reclamo) y **elimina físicamente** (`forceDelete`, no `delete` — `User` usa `SoftDeletes`, un delete normal solo marca `deleted_at` y deja el hash de password sentado en la tabla, exactamente la cuenta "fantasma" que se decidió evitar) a los usuarios id=2 y 3. Demos: los 3 usuarios se mantienen tal cual, sin cambios — es exactamente lo que hace falta mostrar.

**Reclamo de credenciales**: `URL::temporarySignedRoute()` (nada de un sistema de tokens propio) — con un detalle no trivial: el link debe apuntar al subdominio del tenant recién creado (`{slug}.clinica.arioli.dev`), no al dominio donde corre el servicio que lo genera, así que se usa `URL::forceRootUrl()` solo para esa firma. La pantalla de reclamo vive en el propio subdominio del tenant (a diferencia de `/demo`, que vive en el dominio central) — `OnboardingController` exige lo contrario que `DemoPublicoController`: hace falta que SÍ haya un tenant resuelto, comparado explícitamente contra el `slug` de la ruta. Un solo campo nuevo (`tenants.credencial_claimed_at`) evita reusar el link — mismo criterio que `HostingAccount::credential_claimed_at` en la app central, sin tabla de tokens.

**Auto-login, a diferencia de `/demo`**: acá el cliente acaba de elegir su propia contraseña — pedírsela de nuevo en un login no suma seguridad, solo fricción. `CompleteTenantProvisioning` (caso de uso dedicado, no lógica en el controller) valida el reclamo, actualiza la contraseña, marca el link usado, y devuelve el `User` para que el controller haga `Auth::login()` y redirija directo al panel.

**Dos casos de uso, no uno forzado**: `ProvisionDemoService` (mostrar el producto) y `ProvisionClienteService` (entregar un sistema operativo) comparten el motor (`tenants:crear`) pero son clases separadas a propósito — el negocio detrás de cada uno es distinto (una demo expira y se borra sola; un cliente real no).

### Validado en producción con un tenant real descartable

`POST /internal/provision` (con y sin bearer token — 401 correcto sin token) → tenant creado, administrador con nombre/email reales desde el inicio, usuarios 2/3 **verificados ausentes** de la tabla (no solo `deleted_at`), rol de Admin intacto (asignación por ID, no se rompió). Link firmado cross-subdominio generado y verificado válido. Reclamo de contraseña → auto-login → aterrizó autenticado en `/admin/dashboard`. Reuso del mismo link → bloqueado ("ya utilizado"). Acceso con slug de otro tenant vía subdominio equivocado → bloqueado. Tenant y base de prueba borrados al terminar.

**Pendiente, documentado, no resuelto en esta sesión**: el lado central (`Admin\TenantController::store`, el Job, `HistoriasClinicasWelcomeMail` con password en texto plano) sigue como está — es el próximo paso para que este endpoint nuevo quede realmente conectado al flujo comercial.

## Aclaración post-6.4 + dos bugs reales encontrados (preexistentes, no de Etapa 6)

**`demo.clinica.arioli.dev` no es el portal público de demos — nunca lo fue.** Es el subdominio del tenant `demo` preexistente (`tenant_key=slug='demo'`, el "demo oficial de producto" creado por el mecanismo central, documentado en el cierre de Etapa 6). El portal público real de la Etapa 6.4 es `https://clinica.arioli.dev/demo` (ruta, dominio central) — deliberadamente NO un subdominio, porque `IdentifyTenant` trata cualquier hostname de 4+ partes como intento de resolución de tenant. Esto ya se había probado como "regresión OK" durante la validación de 6.4; no es una contradicción del diseño, es el mismo comportamiento reportado en su momento.

Investigando el reporte del usuario sobre el título mal codificado ("Historias ClÃ­nicas") aparecieron dos bugs reales, independientes de Etapa 6 y del portal de demos:

**1. Fuga de caché entre tenants (`AppServiceProvider`)** — `Cache::remember('sistema_config', 600, ...)` usaba una clave literal, no aislada por tenant. `IdentifyTenant` reasigna `config(['cache.prefix' => ...])` por request, pero si algo anterior en el pipeline de middleware (ej. `PreventRequestsDuringMaintenance`) ya resolvió el store de Redis con el prefix genérico de la app, ese cambio de config posterior no lo afecta — Laravel no recrea el store. Confirmado en vivo: `historias_default` mostraba el nombre configurado de `historias_demo` (y viceversa, según qué tenant escribió la caché compartida último). **Fix**: la clave de caché ahora incluye el tenant explícitamente (`'sistema_config_' . $tenantKey`), sin depender de que el prefix dinámico se propague correctamente.

**2. Bytes doblemente codificados en `configuracion_sistema.nombre_sistema`** — específico de `historias_default` (el tenant más viejo, de antes de esta sesión); confirmado con tenants nuevos que el pipeline actual (`mysql_tenant_admin`, Gate G-01) ya no produce esta corrupción — no era un bug activo, era un dato histórico mal guardado. Corregido con `mb_convert_encoding($valor, 'ISO-8859-1', 'UTF-8')` (revierte exactamente una doble codificación UTF-8→Latin1→UTF-8), verificado antes de aplicar.

**Un tercer hallazgo, operativo, no un bug de código**: durante el diagnóstico, `optimize:clear` no fue suficiente para que el fix de `AppServiceProvider` tomara efecto — hizo falta un `view:clear` explícito para invalidar el caché de vistas Blade compiladas. Ninguno de los pasos de diagnóstico previos (dato en DB, caché Redis, conexión de tenant) mostraba nada mal — la vista compilada vieja seguía sirviéndose. Anotado como algo a tener en cuenta: si un cambio de código no parece tomar efecto pese a que todo lo demás verifica correcto, `view:clear` explícito es el siguiente paso, no asumir que `optimize:clear` ya lo cubrió.

## Ajustes post-6.5 — el Perfil debe adaptar el sistema de verdad, no solo instalar un Componente

Reporte real sobre un tenant demo de Odontología (`demo-odontologia-ieqi62`): mostraba secciones de ficha psicosocial (familia, antecedentes, educación, laboral) que no le corresponden, el menú "Odontología" decía "módulo en construcción" pese a que el odontograma real ya existe desde Etapa 4.3, aparecía el módulo de seguimiento de medicación (que no corresponde a un consultorio odontológico), y el login no mostraba credenciales de demo ni el nombre del sistema reflejaba el perfil. Cuatro causas reales, cada una verificada antes de tocar código:

**1. `fieldVisibilitySeed` nunca se declaró para Odontología ni Medicina Laboral.** Los presets (`ficha_psicosocial_extendida` / `ficha_clinica_basica`) existían desde Etapa 2, pero en `config/platform/componentes.php` solo Salud Mental los disparaba — sin un Componente que lo pida, ninguna sección de paciente se oculta, así que todo tenant no-Salud-Mental mostraba el formulario completo por defecto. Fix: Odontología ahora declara `fieldVisibilitySeed: ['ficha_clinica_basica']`. Medicina Laboral necesitaba un preset propio (`ficha_ocupacional`, nuevo) porque `ficha_clinica_basica` también oculta 'laboral' — justo el dato que a Medicina Laboral le importa.

**2. El texto de Odontología era un stub de Etapa 4.1, nunca actualizado tras Etapa 4.3.** Medicina Laboral, que siguió el mismo patrón (`index()` sin dominio propio, funcionalidad real solo per-paciente), sí tenía el texto correcto desde el principio — fue puntualmente Odontología la que quedó con "todavía no están implementados" mucho después de que el odontograma real ya funcionara. Corregido para decir lo mismo que Medicina Laboral: buscar al paciente y entrar desde su ficha.

**3. "Prescripciones" (seguimiento longitudinal de medicación) confundido inicialmente con "Recetas" (documento de prescripción).** Antes de tocar nada se confirmó funcionalmente cada controller, a pedido explícito de Francisco: `RecetaController` genera el documento de receta ligado a un `Informe` (protegido por `informe_access`/`informe_edit`) — un odontólogo sí emite recetas (antibióticos, analgésicos), así que queda intacto. `MedicacionController` es seguimiento longitudinal de dosis/horario por paciente — no aplica a un consultorio odontológico. Esto reveló un gap real en la plataforma: ningún Componente podía **apagar** una capability encendida por defecto, solo prenderlas. Se agregó `Componente::$capabilitiesDisabled` + `CapabilityInstaller::deshabilitar()` (mismo algoritmo no-destructivo que respeta `source='manual'`), enganchado en `ComponenteInstaller::instalar()`. Odontología ahora declara `capabilitiesDisabled: ['medicacion']`. El backend ya quedó protegido solo con esto — `AuthGates` ya ata `medicacion_access` a `capability_key='medicacion'` — hizo falta además ocultar el link "Prescripciones" del menú (antes se mostraba sin chequear ninguna capability).

**4. El login no reconocía los demos de autoservicio.** Ya existía un mecanismo (`login.blade.php` + `config/demo.php`) para mostrar credenciales en el login "modo demo", pero estaba hardcodeado al demo oficial viejo (`subdomain === 'demo'`, credenciales `admin@demo.com`/`demo1234`) — no sabía nada de `DemoInstance` (Etapa 6.1-6.4). Generalizado: ahora también detecta si existe una `DemoInstance` para el tenant resuelto (consultado vía `mysql_tenant_admin`, que al inicio de cualquier request normal sigue apuntando a la DB maestra) y muestra las credenciales reales (`admin@admin.com`/`password`, las que siembra `UsersTableSeeder`). De paso se encontró y cerró un gap de seguridad relacionado: `DemoProtection` (bloquea ediciones destructivas en modo demo) usaba el mismo chequeo viejo — un demo de autoservicio no tenía **ninguna** protección de escritura hasta este fix.

**Nomenclatura por Perfil, no genérica**: se agregó `Perfil::$nombreSistema` (ej. "Sistema de Salud Odontología", "Sistema de Salud Mental", "Sistema de Salud Laboral", "Sistema de Salud Médico" para `clinica_general`) — `TenantsCrear::provisionarTenant()` lo aplica explícitamente sobre `configuracion_sistema.nombre_sistema` justo después de instalar el Perfil. Deliberadamente no se usó el mecanismo existente `aplicarConfiguracionInicial()` (que solo llena campos vacíos) porque `nombre_sistema` tiene un `DEFAULT` no vacío a nivel de columna — ese mecanismo nunca hubiera disparado.

**Validado con el tenant real reportado (corregido retroactivamente) y con tenants nuevos de los 3 perfiles** (Odontología, Salud Mental, Medicina Laboral) creados de punta a punta sin intervención manual: título correcto, credenciales de demo visibles, capabilities y field_visibility correctos por perfil, sin regresión en Salud Mental (los 6 campos siguen visibles) ni en Medicina Laboral (solo 'laboral' visible, `medicacion` sigue activo — no se tocó, nadie lo pidió). Tenants de prueba limpiados al terminar.

## Etapa 6.6 — Rebrand comercial: "Historias Clínicas" → "Sistema de Salud"

**Motivo, en palabras de Francisco**: con la arquitectura de Perfiles/Componentes ya construida (Etapas 4-6), el producto dejó de ser solo "historias clínicas" — Odontología, Medicina Laboral y Salud Mental son sistemas de gestión con dominios propios que comparten el mismo motor. El nombre comercial tenía que reflejar eso. Pedido explícito de alcance, con relevamiento previo obligatorio antes de tocar código: (1) renombrar el producto en arioli.dev sin perder funcionalidad, (2) eliminar `demo.clinica.arioli.dev` como punto de entrada — solo `clinica.arioli.dev/demo` debe existir, (3) selector de tipo de sistema antes de crear una demo, (4) el checkout de contratación debe capturar el perfil elegido y alimentar el provisioning real, (5) consistencia de branding en todo el resto de las pantallas. Decisión explícita sobre el mecanismo viejo de demo (`Admin\DemoController`, `ProvisionDemoInstance`, `ResetDemoInstance` para este producto): **retirado por completo**, no solo ocultado.

**Alcance real, distinto de las etapas anteriores**: por primera vez esta etapa toca `/opt/arioli-saas/src` (la app central, compartida por loteos/tallerpro/historias-clinicas) además de este repo. La app central **no tiene control de versiones** — cada archivo se respaldó a `.bak` antes de editar (`/opt/arioli-saas/backups/rebrand-20260722/`), y todo el trabajo en ambas apps se deployó por ssh/tar (sin git commit, mismo criterio que ya regía para este repo).

**1. Branding — 18 ubicaciones + 1 columna con DEFAULT.** "Historias Clínicas"/"Sistema HC" reemplazado por "Sistema de Salud" en vistas, emails, wizard, layouts y `lang/es/panel.php`; `ConfiguracionSistema::instancia()` cambia su fallback default y una migración nueva (`2026_07_27_000001_change_nombre_sistema_default`, deliberadamente no editando la migración original) actualiza el `DEFAULT` de columna. Deliberadamente **no tocado**: el label "Historias Clínicas" del KPI del dashboard admin — ahí es el término clínico genérico del dominio (el tipo de documento), no branding del producto, y confundir ambos sentidos habría sido el mismo error que ya se había señalado con "Prescripciones" vs. "Recetas" en el ajuste post-6.5.

**2. `Perfil` gana superficie pública.** `Perfil::$caracteristicas` (array de bullets de feature) se agrega al DTO — hasta ahora `Perfil` solo describía cómo instalar un tenant (`componentes`, `nombreSistema`), nunca cómo explicárselo a un usuario eligiendo perfil. `config/platform/perfiles.php` gana `nombre` comercial ("Consultorio Odontológico", "Clínica / Consultorio Médico", "Centro de Salud Mental", "Medicina Laboral") y `caracteristicas` por perfil — mismo patrón que ya usa `Componente`/`ModuleManifest`: DTO deliberadamente aburrido, sin lógica, solo datos que un consumidor (portal de demos, checkout) puede leer.

**3. Portal de demos rediseñado (`demo-publico/{index,solicitar,listo}.blade.php`), no solo re-textualizado.** Punto 2 del pedido ("misma calidad de diseño que arioli.dev/checkout, landing comercial, no pantalla técnica") no se resolvía con find-and-replace — el portal existente desde Etapa 6.4 funcionaba pero visualmente no pertenecía a la misma familia que el resto del embudo comercial. Rediseño completo con la identidad visual de arioli.dev (tema oscuro, tokens `--bg/--card/--accent/...`, DM Sans/DM Mono, tarjetas de perfil mostrando `caracteristicas`) — mismo layout de 2 columnas que el checkout. Validado end-to-end con un tenant real descartable (`demo_odontologia_wcbayc`) creado y limpiado en la misma sesión.

**4. El checkout central ahora captura perfil y ya no inventa una contraseña de producto.** Este fue el hallazgo más importante de la etapa, no algo previsto de antemano: `CheckoutController`/`ProvisionHistoriasInstance` nunca conectaron con `POST /internal/provision` (Etapa 6.5) — seguían usando el mecanismo viejo (clonado de tablas a mano, contraseña generada por el staff y reenviada en texto plano). Esto era exactamente el "próximo paso del lado central" que había quedado documentado, sin resolver, al cierre de 6.5. Se resolvió ahí:

- `orders.perfil` (columna nueva, migración `2026_07_23_000001_add_perfil_to_orders_table`, agregada también a `Order::$fillable` — sin esto el campo se hubiera descartado silenciosamente por mass-assignment).
- `CheckoutController` valida `perfil` (`in:clinica_general,odontologia,medicina_laboral,salud_mental`) solo cuando el producto es historias-clinicas — el resto de los productos no ve el campo.
- `checkout.blade.php` gana un selector de perfil condicional (`@if($perfiles)`), mismo tema oscuro.
- `ProvisionHistoriasInstance` **reescrito por completo**: en vez de `Artisan::call('provision:...-direct', [...contraseña...])` + `Mail::send(HistoriasClinicasWelcomeMail)`, ahora hace `Http::post()` contra `/internal/provision` (mismo endpoint, mismo bearer token compartido que ya usa `internal.demo.seed/reset`) pasando `perfil`, sin contraseña — historias-clinicas manda su propio email de bienvenida con el link de reclamo firmado (Etapa 6.5), la app central deja de mandar dos emails con dos flujos de credenciales distintos para el mismo alta.
- Validado con `dispatchSync()` en tinker (Turnstile bloquea el formulario real de checkout vía curl, y MercadoPago está en modo producción con dinero real — no se completó ningún pago real ni se probó el formulario HTTP en vivo): tenant real creado, un único administrador con nombre/email reales, perfil correcto instalado, sin cuentas fantasma.

**Gap conocido, no resuelto en esta etapa, señalado a Francisco**: `Admin\TenantController::store()` (alta de tenant desde el panel de staff, no desde el checkout de cliente) sigue pidiendo una contraseña tipeada por el staff y no tiene selector de perfil — el job ahora ignora esa contraseña (ya no la usa) y cae a `clinica_general` por defecto si no se pasa perfil por esa vía. No es la misma violación de Gate G-02 que tenía el checkout (ese, el que factura, ya está resuelto) pero es una inconsistencia real que queda pendiente de decisión.

**5. Retiro completo del mecanismo viejo de demo, no solo ocultamiento de links.** `ProvisionDemoInstance`/`ResetDemoInstance` (comandos artisan centrales) dejan de aceptar `historias-clinicas` como producto válido — rechazan con mensaje explicativo en vez de intentar nada. `Admin\DemoController` (panel de staff): la fila de historias-clinicas ahora muestra un link directo a `/demo` en vez de un botón "Provisionar demo" que solo iba a fallar (la primera versión del fix dejaba ese botón roto — corregido antes de deployar, ver nota de la sección siguiente). `$demoUrl` en `routes/landing.php` e `index.blade.php` (CTAs de la landing/homepage) queda condicional por producto: historias-clinicas apunta a `/demo`, loteos/tallerpro conservan el patrón de subdominio sin cambios.

**Limpieza de datos, irreversible, ejecutada solo tras confirmar el modelo de datos exacto**: antes de borrar nada se confirmó por tinker que las `License` de demo son una fila por producto (`tenant_id='demo'` compartido, diferenciadas por `plan_id`→`product_id`) sobre un único `Tenant::find('demo')` central compartido por los 3 productos — borrar la license de historias-clinicas no toca la de loteos/tallerpro ni el `Tenant` central. Con eso confirmado: backup de `historias_demo` (`mysqldump --single-transaction --no-tablespaces` — los flags por defecto fallan contra el usuario acotado de Gate G-01 por falta de `PROCESS`/`LOCK TABLES`, confirmación positiva de que la restricción funciona como debe, no un bug), `DROP DATABASE historias_demo`, borrado de la fila `tenants` de historias-clinicas para `demo`, borrado de la `License` id=1 (historias-clinicas). Confirmado sin tocar el `Tenant` central ni ningún `Domain` de `demo.clinica.*` (no existía ninguno).

**Validado en producción tras el retiro**: `demo:provision historias-clinicas` y `demo:reset historias-clinicas` rechazan con "Producto inválido" (exit 1) sin tocar nada. `Admin\DemoController@index` renderiza sin error para los 3 productos (confirmado invocando el controller real vía tinker, no solo lectura de código) — loteos/tallerpro siguen mostrando su demo activa sin cambios, historias-clinicas muestra el link al portal nuevo. `demo.clinica.arioli.dev` (HTTP 200) ya no sirve ningún demo — cae en la pantalla genérica de "subdominio disponible" (`tenant-not-found.blade.php`) porque el tenant fue borrado, comportamiento correcto y distinto de un demo funcionando. Los CTAs de demo en `/productos/historias-clinicas` y en la homepage apuntan a `https://clinica.arioli.dev/demo` (confirmado con curl contra las páginas en vivo); loteos/tallerpro conservan `demo.<producto>.arioli.dev` sin cambios.

**Corregido en Etapa 6.6.1** (ver abajo): la pantalla de "subdominio disponible" para `demo.clinica.arioli.dev` descripta en el párrafo anterior resultó ser, en sí misma, la próxima fuente de confusión — mostraba "demo" como un nombre contratable en vez de tratarlo como reservado. No era una regresión de este cierre de Fase 4 sino el siguiente síntoma del mismo problema de fondo (nunca hubo una noción formal de "subdominio reservado" en la plataforma).

**Nota operativa real, para la próxima vez que se edite un archivo de la app central con `sed` por línea**: el primer intento de arreglar `Admin\DemoController.php` con la técnica de insert-then-delete (`sed -i 'Nr archivo'` seguido de `sed -i 'N,Md'`) en un solo comando encadenado rompió el archivo — el `insert` corre primero y desplaza los números de línea, así que el `delete` referenciado contra los números originales borra las líneas equivocadas del archivo ya modificado. Se detectó antes de hacer deploy (lectura del archivo resultante mostraba líneas de la ternaria vieja y nueva mezcladas) y se corrigió trayendo el archivo completo localmente para editarlo con reemplazo de texto exacto en vez de aritmética de números de línea. La técnica de insert-then-delete sigue siendo válida para un único comando `sed` a la vez contra el archivo real (como se usó en el resto de la sesión) — el error fue encadenar `insert` y `delete` en el mismo `ssh` sin volver a leer los números de línea entre medio.

**Explícitamente fuera de alcance de esta etapa** (documentado, no una omisión): `Admin\TenantController::store()` sin selector de perfil (arriba); `HistoriasClinicasWelcomeMail`/su vista quedan re-brandeados pero efectivamente sin uso real en el camino de checkout (el job ya no los dispara — siguen referenciados solo por el camino de `Admin\TenantController::store`); ningún test automatizado nuevo (mismo criterio que el resto del proyecto — verificación manual vía tinker/HTTP real, documentada acá).

## Etapa 6.6.1 — Coherencia post-rebrand: subdominios reservados, portal sin login fantasma

Reporte de Francisco tras usar el flujo completo ya rebrandeado, con 5 puntos concretos. Cuatro se resolvieron esta etapa; uno (BOM/caracteres invisibles) quedó investigado sin reproducir, ver más abajo.

**1-5 comparten una misma causa raíz, encontrada al investigar antes de tocar código**: la plataforma nunca tuvo una noción formal de "subdominio reservado". `IdentifyTenant` trataba cualquier primer-label de un host de 4+ partes que no resolviera a un tenant real como "subdominio disponible para contratar" — sin distinguir entre un nombre de cliente real que nadie tomó todavía y una palabra de infraestructura (`demo`, `admin`, `test`) que nunca debería poder tomarse. Consecuencia directa, confirmada en vivo antes de corregir: `demo.clinica.arioli.dev` (el viejo punto de entrada, con el tenant ya borrado en el cierre de Etapa 6) devolvía HTTP 200 con el mensaje "El subdominio demo está disponible... Podés contratar este espacio" — exactamente el "demo general" que Francisco pidió eliminar, solo que reaparecido con otra forma.

**Regla de plataforma nueva, formalizada** (Punto 5 del pedido): `config/platform/reserved_slugs.php` (nuevo, mergeado automáticamente por el `glob()` de `PlatformServiceProvider` ya existente desde Etapa 2 — sin registro manual) es el punto único de verdad, del lado de historias-clinicas, para qué palabras nunca pueden ser `tenant_key` ni `slug` de un tenant: `demo, admin, api, app, www, mail, ftp, cliente, test, ejemplo, staging, panel, soporte, support`. Deliberadamente **no** incluye `default` — es el `tenant_key`/`slug` real de la instancia de producción existente, no una palabra de infraestructura; incluirla habría sido tratar un tenant legítimo como si fuera un error.

Tres puntos de enforcement, todos leyendo la misma config (no una validación duplicada con su propia lista cada vez):
1. **`TenantsCrear`** — valida `key` y `slug` contra la lista antes de crear la base física. Cubre alta manual por CLI y, transitivamente, `ProvisionDemoService`/`ProvisionClienteService` (ambos delegan en `Artisan::call('tenants:crear', ...)`, no hay un segundo camino de creación de tenant).
2. **`Internal\ProvisionController`** (Etapa 6.5, el endpoint que la app central llama desde el checkout) — valida `tenant_key` **antes** de llamar al servicio, devolviendo un 422 explícito ("es un subdominio reservado") en vez de dejar que `tenants:crear` fallara más abajo y burbujeara como una `RuntimeException` sin capturar (500 opaco). Defensa en profundidad real, no cosmética: sin este chequeo, un `customer_company` que produjera exactamente un slug reservado en el checkout central (ver punto siguiente) igual habría llegado hasta acá.
3. **`IdentifyTenant`** — cuando el slug resuelto no tiene tenant Y está en la lista de reservados, ya no muestra "disponible para contratar". Caso especial para `demo` específicamente (el que tenía tráfico heredado real): en vez de una pantalla, `redirect(302)` directo a `{dominio-raíz}/demo` — el visitante que todavía tiene el link viejo cae exactamente en el portal correcto, sin fricción y sin que exista más una landing "demo" distinta. Para el resto de los reservados (`admin`, `test`, ...) se agregó un estado `$reservado` a `tenant-not-found.blade.php`: mismo layout, pero "Subdominio reservado" en vez de "Subdominio disponible", sin botón "Contratar este subdominio" ni el resto del copy de venta — la pantalla ya no ofrece en venta algo que nunca estuvo a la venta.

**Lado central, mismo criterio pero código separado** (Punto 5, mirror explícito porque las dos apps no comparten código): `CheckoutController::process()` en `/opt/arioli-saas/src` ya tenía una lista de reservados propia desde antes de esta sesión (`demo, admin, api, cliente, www, mail, ftp, app`) — protegía el `customer_company` → `Str::slug()` → `tenant_key` de cualquier producto, no solo historias-clinicas. Se amplió para incluir `test`/`ejemplo`/`staging`/`panel`/`soporte`/`support`, con un comentario explícito de que debe mantenerse sincronizada a mano con `reserved_slugs.php` — no hay forma de compartir código entre las dos apps sin construir infraestructura nueva que nadie pidió, así que la sincronización manual documentada es la solución real, no una deuda.

**Punto 1 (eliminar `demo.clinica.arioli.dev` como entrada) queda resuelto por el redirect de `IdentifyTenant` de arriba** — no por borrar DNS ni por una regla de nginx, sino por el mismo mecanismo que ya resuelve cualquier otro subdominio. Validado con curl en vivo: `http://demo.clinica.arioli.dev` → `302 Location: http://clinica.arioli.dev/demo` → sigue a `200` en el portal real.

**Punto 2 — login fantasma en el portal público**: `demo-publico/index.blade.php` tenía un footer `¿Ya tenés una cuenta? Iniciá sesión` apuntando a `/login` en el dominio central (`clinica.arioli.dev`, 3 partes — `IdentifyTenant` nunca lo resuelve como tenant, así que ese `/login` no pertenecía a ningún tenant específico, era un login "flotante" sin dueño claro). Investigado antes de tocar nada, como pidió Francisco explícitamente ("no quiero esconderlo con CSS, quiero corregir el flujo"): no hay ningún mecanismo real detrás — es un link estático heredado de una iteración anterior del portal, sin controller ni ruta propia que lo justifique. `listo.blade.php` (la pantalla que sí importa) ya hacía lo correcto desde Etapa 6.4: linkea directo a `{slug}.clinica.arioli.dev`, el subdominio real de la demo recién creada, que ahí sí tiene su propio `/login`. Fix: se eliminó el footer completo (no solo el link — también la regla CSS `.foot-note`, que quedaba sin uso) de `index.blade.php`. `solicitar.blade.php` no tenía nada equivalente.

**Punto 4 — validación del flujo completo**, hecha antes de tocar código y re-confirmada después con un tenant descartable real (`demo_odontologia_36l3vc`, creado y limpiado con `demo:crear`/`demo:expirar`/`demo:limpiar`, no a mano): `arioli.dev/productos/historias-clinicas` → CTA a `clinica.arioli.dev/demo` (confirmado con curl) → selector de perfil sin login fantasma → `demo:crear`/`ProvisionDemoService` (protegido contra reservados) → `listo.blade.php` con el link real a `{slug}.clinica.arioli.dev` → `/login` de ese subdominio específico, con nombre de sistema correcto (`Sistema de Salud Odontología`) y sin ningún resto del "demo global" viejo. Regresión verificada explícitamente: un subdominio no reservado (`consultoriorandomxyz123`) sigue mostrando la pantalla de "disponible para contratar" sin cambios — el fix no afectó el caso real de venta, solo el caso de palabras reservadas.

**Punto 3 — caracteres invisibles / BOM UTF-8, investigado sin reproducir**: búsqueda exhaustiva sin resultado. Se descartó, en orden: bytes de BOM (`EF BB BF`) en cualquier archivo `.php`/`.blade.php`/`.php` de config de ambos repos (`grep -rlP` recursivo, sin vendor, sin resultados en ninguno de los dos); el mismo patrón en el contenido de `configuracion_sistema` de los 3 tenants reales (incluyendo `nombre_sistema`, el campo que ya había tenido un bug de mojibake real en Etapa 6 — descartado explícitamente, no solo asumido limpio); la cadena literal `&#xFEFF;` en el código fuente de ambos repos; los primeros y — en las páginas públicas alcanzables — los bytes completos de la respuesta HTTP de `/login` (dos tenants reales), `/demo`, `/productos/historias-clinicas`, homepage, `/checkout/9`, `/contacto`, `/partner`; y el render server-side vía Tinker (`view()->render()`) de las vistas no alcanzables por curl anónimo (`setup.wizard`, emails, `onboarding.*`, las 3 vistas de `demo-publico`, `tenant-not-found`). Ninguna de estas fuentes mostró el artefacto. **No se aplicó ningún fix a ciegas** — hace falta que Francisco indique la URL exacta y si aparece en "Ver código fuente" (bytes crudos) o en la página ya renderizada (posible artefacto del navegador/extensión, no necesariamente del servidor) para poder reproducirlo antes de tocar algo.

## Etapa 6.6.2 — Odontología probada en uso real: capabilities que no llegaban a toda la UI, y Medicina Laboral retirada

Reporte de Francisco probando `demo-odontologia-vz6t6e` con datos reales (capturas del dashboard y de la ficha de "González, María"): el dashboard mostraba KPIs y gráficos de "Prescripciones" pese a que Odontología ya tiene `medicacion` deshabilitado desde el ajuste post-6.5; la ficha de paciente mostraba las pestañas "Prescripción" y "Consentimientos", ninguna de las dos aplicable al perfil; y el odontograma seguía siendo una tabla de texto, pedido explícito de que fuera "un gráfico real". Un cuarto pedido, de alcance de producto: retirar Medicina Laboral por completo — "no voy a proveer eso, no tiene utilidad".

**Causa raíz de los primeros dos síntomas, igual en los dos casos**: `capability_states`/`Gate` protegen el *backend* (rutas, acciones) correctamente desde el ajuste post-6.5, pero dos superficies de UI nunca se conectaron a esa protección:

1. **`Admin\HomeController::index()`** consultaba `Medicacion` incondicionalmente y el dashboard (`admin/dashboard/home.blade.php`) renderizaba el KPI "Prescripciones", el donut "Prescripciones por horario", la fila de actividad, los dos accesos rápidos y la tabla "Prescripciones programadas hoy" sin ningún chequeo — a diferencia del link de menú "Prescripciones" (`admin-layout.blade.php`/`layouts/app.blade.php`), que sí estaba envuelto en `isCapabilityEnabled('medicacion')` desde el ajuste anterior. Fix: `$capabilityMedicacion = app(PlatformRegistry::class)->isCapabilityEnabled('medicacion')` calculado una vez en el controller (evita la query a `Medicacion` cuando está apagada, no solo oculta el resultado) y usado para envolver cada bloque en la vista; el canvas de Chart.js pasa a construirse solo si el elemento existe en el DOM (`if (medCanvas) {...}`), en vez de asumir que `#chartMedicaciones` siempre está presente.
2. **`panel/pacientes/show.blade.php`** (la ficha de paciente): las pestañas "Prescripción" y "Consentimientos" (botón + panel de contenido + el botón de descarga PDF de Prescripción dentro de la pestaña PDFs) no tenían ningún `@if` de capability — a diferencia de los botones de acción rápida arriba de la ficha ("Nueva cita", "Nuevo informe", "Prescripción"), que sí usan `@can(...)` correctamente. "Recetas" (RecetaController, documento ligado a un Informe) se dejó **intacta a propósito** — sigue siendo la decisión ya tomada en el ajuste post-6.5 (un odontólogo emite recetas; "Prescripciones" es seguimiento de medicación, otra cosa).

**Decisión de producto nueva, no un bug**: Francisco pidió explícitamente que "Consentimientos" tampoco aplique a Odontología. A diferencia de `medicacion` (ya tenía su capability y su `capabilitiesDisabled` desde el ajuste anterior), `consentimientos` nunca había sido apagada por ningún Componente — es uno de los módulos "siempre activos" del diseño original (junto con Especialidades/Agenda/Historia Clínica). Se agregó `'consentimientos'` al `capabilitiesDisabled` de Odontología en `config/platform/componentes.php`, con el mismo comentario explicando el motivo que ya tenía `medicacion`. Aplicado retroactivamente con `ComponenteInstaller::instalar(['odontologia'])` sobre los 2 tenants Odontología reales existentes (confirmado antes/después: `consentimientos` pasó de `true` a `false` en ambos).

**Verificación, con la misma limitación de siempre para capabilities dinámicas**: renderizar `layouts.app` completo vía Tinker sigue fallando por algo no relacionado a este cambio (`$alerts`/navegación necesitan contexto HTTP real, ya documentado en sesiones anteriores) — se verificó en su lugar (a) que `PlatformRegistry::isCapabilityEnabled()` devuelve `false` para `medicacion` y `consentimientos` en el tenant real de la captura, y (b) que ambos archivos Blade compilan a PHP válido (`Blade::compileString()` + `php -l` sobre el resultado) sin errores de sintaxis en los nuevos `@if`. Un hallazgo de tooling nuevo en el camino: los Gates de este proyecto **no se definen en un ServiceProvider sino en middleware** (`App\Http\Middleware\AuthGates`, corre por request, itera `Role::with('permissions')` y hace `Gate::define()` dinámicamente por título de permiso según `capability_key`) — cualquier prueba futura por Tinker que dependa de `Gate::allows()`/`@can` tiene que instanciar y correr `(new AuthGates())->handle(request(), fn($r) => $r)` a mano primero, o todo Gate resuelve `false` por definición ausente, no por lógica de negocio.

**Medicina Laboral retirada por completo** (a diferencia del retiro de `demo.clinica.arioli.dev` en Etapa 6.6.1, que fue "sacar del catálogo, dejar el código" — acá Francisco pidió explícitamente borrar todo): `app/Modules/MedicinaLaboral/` (Extension + modelo `EvaluacionLaboral`), `Panel/MedicinaLaboralController`, `resources/views/panel/medicina-laboral/`, `database/seeders/MedicinaLaboralDemoSeeder.php` y la migración `create_evaluaciones_laborales_table` eliminados del repo; las 3 rutas `medicina-laboral*` sacadas de `routes/web.php`; la entrada `medicina_laboral` sacada de `config/platform/perfiles.php`, `componentes.php` (y su `use MedicinaLaboralExtension`) y `field_visibility_presets.php` (el preset `ficha_ocupacional`, que solo este Componente usaba); el botón de acción rápida "Medicina Laboral" sacado de la ficha de paciente; `TenantsCrear::ESCENARIOS_DEMO` sin la entrada de su seeder. Del lado central, `CheckoutController::PERFILES_SISTEMA_SALUD` perdió la opción — el selector de perfil del checkout ya no la ofrece para ningún cliente nuevo.

**Limpieza de datos real, no solo de código**: ningún tenant tenía el Componente `medicina_laboral` instalado (`componentes_instalados` vacío para esa key en los 3 tenants reales — confirmado antes de borrar nada), pero la migración de `evaluaciones_laborales` **sí se había ejecutado** en los 3 (una tabla la crea `tenants:migrate` para todos los tenants sin importar qué Componente tengan instalado — instalar un Componente y correr sus migraciones de esquema son cosas distintas). Se hizo `DROP TABLE evaluaciones_laborales` + limpieza de la fila correspondiente en `migrations` en los 3 tenants, en vez de dejar una tabla vacía y huérfana sin ningún código que la referencie.

**Incidente real durante las pruebas, corregido en la misma sesión — vale la pena dejarlo anotado**: al probar `actualizarPieza()` (ver Etapa 6.6.3 más abajo) contra el tenant real de la captura (`demo-odontologia-vz6t6e`, no un tenant descartable), un bug en el script de prueba (`(int) $stringable` sobre un objeto `Illuminate\Support\Stringable` sin convertir a string primero — PHP devuelve `1` con un warning en vez de fallar, en lugar del ID real capturado de la redirección) hizo que la limpieza del test (`forceDelete()`) borrara por error un odontograma **real, sembrado por `OdontologiaDemoSeeder`** (el de María González de "hace 3 meses", con la pieza 26 en `cariada` — la mitad "antes" de la narrativa deliberada del seeder, "caries detectada → resuelta", ver el seeder mismo para el texto exacto). Detectado inmediatamente al notar que el estado leído "antes" de la prueba no correspondía a una pieza recién creada. Reconstruido con los mismos valores exactos que el seeder original (mismo paciente, misma profesional, misma fecha relativa, misma pieza y observaciones) — el ID de fila cambió (era imposible de recuperar tras `forceDelete()`, no hay soft-delete de por medio) pero el contenido y la narrativa del demo quedaron idénticos. Dos odontogramas de prueba adicionales (creados por reintentos del mismo test, antes de aislar la causa) también identificados y borrados por patrón exacto (fecha de hoy, paciente 1, las 32 piezas todavía en `sana`, sin observaciones) para no dejarlos como basura. La prueba final, repetida con el ID capturado directo del modelo (`Odontograma::max('id')` en vez de parsear la URL de redirección) en vez de por texto, no tocó ningún dato real — limpieza exacta de un solo registro, verificado contando el total de odontogramas antes/después.

## Etapa 6.6.3 — Odontograma real: de tabla a gráfico clickeable

**Estado previo, confirmado leyendo el código antes de construir nada**: `Odontograma`/`PiezaDental` (Etapa 4.3) ya modelaban el dominio completo (32 piezas por odontograma, notación FDI, 7 estados posibles vía `PiezaDental::estadosLabels()`) pero la única vista (`panel/odontologia/show.blade.php`) era una tabla HTML de dos columnas, y **no existía ningún camino para editar una pieza después de creada** — `crear()` las siembra todas en `sana` una vez y `show()` solo lee. Confirmado con Francisco antes de construir: quería gráfico real **y** edición click-to-edit en la misma pasada, no dos etapas separadas.

**Ruta y controller nuevos**: `PATCH odontologia/pieza/{pieza}` → `OdontologiaController::actualizarPieza()`, protegida por el mismo Gate `odontologia_edit` que ya provisiona `OdontologiaExtension` desde Etapa 4 (no hizo falta ningún permiso nuevo). Valida `estado` contra `array_keys(PiezaDental::estadosLabels())` (rechaza cualquier valor fuera del catálogo — confirmado con una prueba real: `estado=no_existe` devuelve 422 con el mensaje de validación de Laravel, no un guardado silencioso de basura) y `observaciones` opcional hasta 500 caracteres. Devuelve JSON (`success`, `numero`, `estado`, `estado_label`, `observaciones`) en vez de un redirect — el punto es que el gráfico se actualice sin recargar la página.

**El gráfico**: `show.blade.php` reescrita por completo. 32 "dientes" (`<button>`, no `<td>`) dispuestos en dos filas de 16 con un divisor central — el orden visual estándar de un odontograma clínico (vista del profesional): arcada superior `18‑17‑...‑11 | 21‑22‑...‑28`, arcada inferior `48‑47‑...‑41 | 31‑32‑...‑38` — deliberadamente distinto del orden de iteración de `numerosFdiAdulto()` (que va cuadrante por cuadrante, 11→18, 21→28...), reordenado en PHP con dos arrays literales en vez de tocar el modelo (el modelo no tiene por qué saber nada de cómo se dibuja). Cada diente es un rectángulo con esquinas redondeadas, coloreado por CSS según `estado` (blanco=sana, rojo=cariada, azul=obturada, rayado=ausente, gris atenuado=extraída, dorado=corona, violeta=implante) — sin librería de gráficos ni SVG anatómico: un chart funcional de estados, no una ilustración realista, que es lo que un odontograma clínico necesita comunicar.

**Click-to-edit**: click en un diente (solo si `Gate::allows('odontologia_edit')` — sin permiso, los botones quedan `data-readonly` y sin listener, con un aviso de "vista de solo lectura" debajo del gráfico) abre un popover posicionado junto al diente clickeado con un `<select>` de los 7 estados y un `<textarea>` de observaciones. "Guardar" dispara un `fetch()` PATCH con el mismo patrón de CSRF que ya usa `markAllNotifsRead()` en `layouts/app.blade.php` (meta tag `csrf-token`, no un formulario) — al responder OK, actualiza `className`/`dataset`/`title` del botón in-place, sin recargar la página. Error de red o validación se muestra en el propio popover, no un `alert()`.

**Validado end-to-end contra un tenant real** (no uno descartable — con la precaución aprendida del incidente de arriba: ID capturado del modelo, limpieza de un solo registro identificado por su propio ID, conteo total antes/después para confirmar cero efectos secundarios): odontograma creado, pieza 14 pasó de `sana` a `obturada` con observaciones, confirmado con una lectura fresca de la base (no el objeto en memoria) después de pasar por el controller real — no un mock. Validación de estado inválido confirmada rechazando con 422. `route:list` confirma las 5 rutas de `odontologia.*` registradas sin colisión con las rutas retiradas de `medicina-laboral.*`.

**Explícitamente no incluido en este incremento**: sin historial de cambios por pieza (cada `actualizarPieza()` sobreescribe, no versiona — igual que como se venía tratando `observaciones` a nivel de Odontograma completo); sin confirmación/deshacer antes de guardar un cambio de estado; el ícono/forma del diente es genérico, no distingue molares de incisivos visualmente (la única diferencia entre piezas es el número FDI y el color de estado). Ninguno fue pedido — quedan anotados como posibles próximos pasos, no como deuda urgente.

## Corrección real al Punto 3 de Etapa 6.6.1 — el BOM sí existía, la búsqueda anterior tenía un falso negativo

Francisco volvió a reportarlo con evidencia nueva: DevTools mostrando, como primer nodo de texto dentro de `<body>`, la cadena literal `&#xFEFF;` en `/admin/dashboard` — y como consecuencia visible del mismo problema, `<head>` aparecía vacío en el DOM con `<meta>`/`<title>`/`<link>` reparentados como hijos de `<body>` (comportamiento de recuperación de errores estándar de cualquier parser HTML5 cuando aparece texto no-whitespace en el punto donde se espera abrir `<head>`).

**La búsqueda de la Etapa 6.6.1 dio un falso negativo, no es que el BOM no existiera.** El comando usado entonces, `grep -rlP '\xEF\xBB\xBF' ...`, no matcheaba ni siquiera en el caso más mínimo posible (`head -c3 archivo | grep -qP '\xef\xbb\xbf'` contra un archivo de exactamente esos 3 bytes, confirmados con `xxd`) — problema del binario de `grep -P` en este contenedor, no de los bytes buscados. La lección real: para bytes no imprimibles, no confiar en `grep -P` con escapes hex sin validarlo primero contra un caso conocido-positivo; se resolvió con un script PHP (`fopen`/`fread` de los primeros 3 bytes de cada archivo, comparados con la constante `"\xEF\xBB\xBF"`) corrido dentro del propio contenedor — mismo lenguaje que ya se usaba para todo lo demás en la sesión, sin depender de las particularidades del `grep` del sistema.

**Alcance real, mucho más grande que un archivo**: **30 archivos** con BOM al inicio, no uno. Los dos que explican el síntoma visible (`layouts/app.blade.php` y `components/admin-layout.blade.php`, los layouts raíz de los que casi toda pantalla autenticada hereda — Etapa 6.6.1 nunca pudo confirmar esta hipótesis porque el render de `/admin/dashboard` vía Tinker fallaba por el problema no relacionado de `$alerts`/navegación, documentado en 6.6.2) más otros 28 — `setup/wizard`, varias vistas `admin/*`, `panel/home`, `panel/pacientes/*` (index/create/edit), `panel/medicacion/*` (las 4), `panel/informes/*` (las 4), `panel/agenda/index`, `panel/profile`, dos plantillas de `messenger`, un componente de `vendor/notify` y `welcome.blade.php`. Todos con el patrón idéntico: BOM como primer byte del archivo, antes de la primera directiva Blade — consistente con haber sido guardados alguna vez con un editor que agrega BOM por default a UTF-8, en algún punto anterior a esta sesión (no hay forma de saber cuándo exactamente, sin control de versiones en este repo tampoco hasta hace poco).

**Fix**: script PHP que recorre `app/`, `resources/`, `routes/`, `config/` (mismos directorios ya cubiertos por el escaneo), y para cada `.php`/`.blade.php` cuyo contenido empieza exactamente con los 3 bytes del BOM, reescribe el archivo con `substr($contenido, 3)` — no toca nada más del contenido. Corrido primero en modo solo-lectura (listar) para confirmar el conteo exacto antes de escribir nada, con un backup completo de `app/resources/routes/config` a `/opt/arioli-saas/backups/bom-fix-20260723/` antes de aplicar. Los 30 archivos re-verificados con `php -l` después del fix (todos limpios) y con el mismo escáner de solo-lectura corrido de nuevo (0 archivos con BOM). Se re-escaneó también la app central con el mismo script — 0 archivos, confirmado limpio de verdad esta vez (la Etapa 6.6.1 también había dado ese resultado por el `grep` roto, pero ahí sí resultó ser correcto).

## Etapa 6.6.4 — field_visibility llega al alta y edición de paciente, no solo a la ficha

Reporte de Francisco, con capturas de un odontograma real de referencia (software dental profesional) y del panel: pidió, entre otras cosas, que el alta y edición de paciente respeten el perfil igual que la ficha, y que "Admisión" (tipo internación/alta, hoy visible siempre) también se adapte — Odontología no la necesita.

**Causa raíz, mismo patrón que ya apareció dos veces en esta sesión (dashboard, tabs de la ficha)**: `field_visibility`/`<x-field-if>` (Etapa 1-3) solo se conectó a `panel/pacientes/show.blade.php` — decisión de alcance **explícita** en su momento ("Solo se envolvió panel/pacientes/show.blade.php", ver Etapa 1 más arriba), no un descuido, pero significaba que el wizard de alta (`create.blade.php`, 6 pasos) y edición (`edit.blade.php`, misma estructura con otro sistema de clases CSS) mostraban Familia/Antecedentes/Educación y laboral siempre, para cualquier perfil.

**Extensión, mismo mecanismo, sin tocar el resolver**: se agregó `$fv = app(FieldVisibilityResolver::class)` a ambos formularios y se envolvió cada paso del wizard (el `<li>` de navegación **y** el `<div class="tab-pane">` correspondiente) detrás de la misma condición que ya usa `<x-field-if>` para las 6 secciones de Etapa 1 — más `<x-field-if>` real donde el paso mezcla más de un campo (ver abajo). Pasos que dependen de un solo campo (`familia`) se ocultan si ese campo está apagado; pasos que agrupan varios (`5. Antecedentes` = `problematica` + `datos_adicionales` + `historial_tratamientos`; `6. Educación y laboral` = `educacion` + `laboral`) se ocultan solo si **todos** los campos que agrupan están apagados (`||` entre `isVisible()`), y adentro cada sub-sección lleva su propio `<x-field-if>` — así un preset futuro que active solo uno de los dos no deja el paso vacío.

**Riesgo real detectado y resuelto antes de tocarlo a ciegas**: el botón final "Guardar paciente"/"Guardar cambios" vive dentro del **último** `tab-pane` (Educación y laboral) — si ese paso se oculta completo (como pasa para Odontología, con `educacion` y `laboral` ambos apagados), ese botón desaparecería con él. Se confirmó, leyendo el archivo antes de decidir, que **ya existe** un botón de submit persistente fuera de `.tab-content` (visible sin importar qué paso esté activo) en los dos formularios — no hubo que mover nada, solo confirmar que la salvaguarda ya estaba.

**El wizard tiene navegación "Siguiente"/"Anterior" con `data-target` fijo — se rompía al ocultar pasos.** Los botones apuntaban a IDs de paso hardcodeados (`data-target="tab-familia"`); si ese paso no se renderiza, `$('#pacienteTabs a[href="#tab-familia"]')` no encuentra nada y jQuery no tira error — el click simplemente no hace nada, wizard trabado en silencio. Reescrito en los dos archivos: la función recorre `$('#pacienteTabs .nav-link')` (los que **sí** están en el DOM, en orden) y avanza/retrocede un índice relativo al paso activo, en vez de confiar en el `data-target` de cada botón — funciona sin importar cuántos pasos estén ocultos ni cuáles.

**Campo nuevo: `admision`.** No existía en el catálogo de Etapa 1 — el bloque "Admisión y egresos" de `show.blade.php` había quedado **explícitamente fuera** del alcance de esa etapa ("los bloques de 'Padres/Tutor' y 'Admisión y egresos'... quedan fuera de esta pasada"). Ahora sí se conecta: agregado a `field_visibility_presets.php` (`ficha_clinica_basica`: `false` — un consultorio odontológico no maneja internación/alta; `ficha_psicosocial_extendida`: `true` — sí aplica a un centro de tratamiento de Salud Mental) y envuelto con `<x-field-if entidad="paciente" campo="admision">` en las tres vistas (`show`, `create`, `edit`). `clinica_general` no instala ningún Componente, así que nunca corre ningún preset — por la regla zero-risk del resolver (sin fila = visible), sigue mostrando Admisión por defecto, correcto para una clínica general que podría manejar internación. `Padres/Tutor` se dejó exactamente como está — Francisco no lo pidió esta vez, y agregarlo por cuenta propia hubiera sido inventar alcance no confirmado (además vive intercalado dentro de la card "Domicilio y contacto", no es una sección propia — separarlo es un cambio de estructura, no solo un wrap).

**Aplicado retroactivamente** a los 2 tenants Odontología reales con el mismo `ComponenteInstaller::instalar(['odontologia'])` ya usado para `medicacion`/`consentimientos` — confirmado `admision` pasando de sin-fila a `false` en ambos. **Validado sin adivinar**: los 3 archivos (`show`, `create`, `edit`) se compilaron con `Blade::compileString()` + `php -l` contra el estado real de `field_visibility` del tenant de la captura (los 7 campos en `false`) — compilan limpio. Regresión confirmada en `historias_default` (sin Componente instalado): los mismos 7 campos siguen en `true`, ningún tenant sin perfil pierde secciones que nunca pidió ocultar.

**Explícitamente pendiente, por decisión de Francisco sobre el orden** (no un olvido): el rediseño completo del odontograma (modelo de datos con partes por pieza — raíz/corona/caras —, dentición primaria además de la permanente, panel de notas real) y el mini-calendario del dashboard con detalle por día/hora quedan para después de esta etapa — priorizado explícitamente así ("formularios primero").

## Corrección real a Etapa 6.6.4 — el fix retroactivo de `admision` tenía alcance incompleto

Francisco pidió una auditoría funcional completa del producto real (no revisión de código): crear una demo de cada perfil por el flujo público real y recorrer la interfaz, con el criterio explícito de que "terminado" significa comportamiento verificado, no código que compila. Se hizo exactamente eso — 3 demos nuevas creadas por el formulario público real (`curl` contra `/demo/{perfil}` con CSRF/sesión real, dos de las tres sin ningún atajo; la tercera vía el mismo servicio que usa el controller, por rate-limit del formulario), más auditoría de los tenants Odontología preexistentes.

**Resultado: `demo_odontologia_bmco49` (el mismo tenant de una captura que Francisco había compartido antes) seguía mostrando "Admisión y egresos" en la ficha de paciente y el paso "Admisión" en el wizard de alta — exactamente el comportamiento viejo.** Causa raíz confirmada, no supuesta: el párrafo anterior de esta misma sección dice *"Aplicado retroactivamente a los 2 tenants Odontología reales"* — esa lista de 2 (`ieqi62`, `vz6t6e`) estaba **hardcodeada**, basada en cuáles tenants existían cuando se escribió ese fix. `bmco49` ya existía en ese momento (creado antes, durante pruebas de una etapa anterior) pero no estaba en la lista — quedó afuera del alcance del fix sin que nadie lo notara, porque nada en el proceso de esa etapa preguntó "¿cuáles son TODOS los tenants con Odontología instalada ahora mismo?", solo asumió que ya se conocía la lista completa.

**Fix real**: mismo mecanismo (`ComponenteInstaller::instalar(['odontologia'])`), pero corrida contra una consulta dinámica — `Tenant::where('status','activo')` filtrado por `componentes_instalados` conteniendo `'odontologia'` — no una lista escrita a mano. Encontró y corrigió 4 tenants reales (`ieqi62`, `vz6t6e`, `bmco49`, `izou1e`), no los 2 que se creía que existían. Verificado con el mismo render real end-to-end (`PacienteController@show`/`@create` a través del controller real, no un mock) contra `bmco49` específicamente: "Admisión y egresos" y el paso "Admisión" del wizard pasaron de visibles a ocultos, igual que en el resto de los tenants Odontología.

**Lección de metodología, para no repetir el error**: cualquier fix retroactivo futuro sobre tenants existentes debe consultar dinámicamente qué tenants están en el estado relevante (vía `componentes_instalados`, `capability_states`, etc.), nunca asumir una lista fija de "los tenants que existen hoy" — esa lista cambia con cada demo nueva que se crea, y un fix con alcance fijo queda desactualizado en cuanto aparece un tenant nuevo entre que se escribió el fix y que se corrió.

**Segundo hallazgo de la misma auditoría, en la app central**: `resources/views/landing/product.blade.php` tenía un link de footer con el texto viejo `"Clínica — Historias"` — el mismo bug que ya se había corregido en `index/contacto/partner/servicio.blade.php` en la Etapa 6, pero `product.blade.php` no estaba en el alcance de ese fix y quedó con el texto viejo. Corregido y verificado en vivo (`curl` contra la página real, cero coincidencias del texto viejo).

**Tercer hallazgo, no corregido, dejado para que Francisco decida**: `Product.description` en la base de datos de la app central (no un archivo de código) sigue diciendo *"Sistema de gestión de historias clínicas médicas"* — nunca se actualizó cuando `Product.name` pasó a "Sistema de Salud". Es contenido editorial, no un bug de código — se anota, no se reescribe unilateralmente.

**Todo lo demás de la auditoría — confirmado correcto contra datos reales, no solo por lectura de código**: los 3 perfiles (incluidos los 2 tenants Odontología más viejos, `ieqi62` y `vz6t6e`, provisionados antes de varios de estos fixes) muestran nombre de sistema correcto en login y dashboard, KPIs/navegación/tabs de paciente/pasos del wizard correctamente adaptados por perfil, el odontograma renderiza como gráfico real (no la tabla vieja), `demo.clinica.arioli.dev` sigue redirigiendo a `/demo`, y el checkout central sigue funcionando (todavía con `PERFILES_SISTEMA_SALUD` hardcodeado — Etapa 7.3, no iniciada, no es un bug).

## Etapa 6.6.5 — Odontograma: modelo de dominio completo, no un parche sobre v1

Continuación directa del análisis de dominio (ver más arriba, "antes de escribir migraciones") — con el modelo ya acordado (catálogo estático de piezas, `Odontograma` 1×paciente, `PiezaOdontologica`+`SuperficieOdontologica`, `HistorialOdontologico`, `TratamientoOdontologico`, dentición temporal bajo demanda, datos existentes descartados) y las dos decisiones confirmadas por Francisco, se implementó completo: no quedó como diseño en el papel.

**Motivo inmediato**: además de continuar el trabajo ya acordado, Francisco reportó tres problemas concretos probando la ficha de Odontología — el botón "Prescripción" (ya estaba correctamente oculto, confirmado; no hizo falta tocar nada ahí), el tab "PDFs" (nunca había estado gateado — se agregó), y que "Historia completa" no mostraba nada del odontograma.

**Catálogo estático** (`config/platform/piezas_dentales_catalogo.php`): 52 piezas (32 permanentes + 20 temporales), cada una con nombre anatómico real, tipo de diente, cuadrante y qué superficies le aplican (incisivos/caninos usan "incisal", premolares/molares usan "oclusal" — nunca ambas). **Bug real encontrado y corregido antes de que llegara a producción**: `PlatformServiceProvider` registra `config/platform/*.php` con `mergeConfigFrom()`, que internamente hace `array_merge($nuevo, $existente)` — `array_merge` **renumera cualquier clave puramente entera del array de nivel superior**, incluso mezclando contra un array vacío (confirmado con una prueba directa: `array_merge([11=>[...],12=>[...]], [])` devuelve `[0=>[...],1=>[...]]`, no `[11=>...,12=>...]`). Un catálogo indexado por número FDI (11, 12, 21...) se corrompía en cuanto Laravel lo cargaba — los números de pieza dejaban de corresponder a los datos reales. Fix: anidar el catálogo bajo una clave string (`return ['piezas' => $catalogo]`) — `array_merge` es superficial (no recursivo), así que nunca toca las claves enteras del array interno. Verificado con los 11 números de prueba (permanentes de los 4 cuadrantes + temporales de los 4 cuadrantes) antes de seguir.

**Modelo de datos nuevo** (migraciones `2026_07_23_1500{01-05}`): `piezas_odontologicas` y `superficies_odontologicas` reemplazan a `piezas_dentales` (retirada, no editada); `historial_odontologico` y `tratamientos_odontologicos` son enteramente nuevas. **Segundo bug real, encontrado corriendo la migración de verdad, no en revisión**: el nombre autogenerado del índice único de `superficies_odontologicas` (`superficies_odontologicas_pieza_odontologica_id_superficie_unique`) excede el límite de 64 caracteres de MySQL — error 1059. Peor todavía: `CREATE TABLE` y `ALTER TABLE ... ADD UNIQUE` son DDL con auto-commit en MySQL, así que la tabla quedó creada (sin el índice) en los 7 tenants donde corrió, pese a que Laravel no registró la migración como exitosa (la migración entera lanzó excepción). Se limpiaron a mano las tablas parciales en los 7 tenants antes de reintentar con un nombre de índice explícito y corto. Confirmado con una segunda corrida de `tenants:migrate` sin ningún error.

**`OdontogramaPiezaSeeder`** (`app/Modules/Odontologia/OdontogramaPiezaSeeder.php`) — el loop de "crear pieza + sus superficies desde el catálogo" hacía falta en dos lugares reales (`OdontologiaController` al crear un odontograma o agregar dentición temporal, y `OdontologiaDemoSeeder`) — segunda ocurrencia real, se extrajo en vez de duplicar, mismo criterio que ya rigió `CapabilityInstaller`/`FieldVisibilityInstaller`.

**`OdontologiaController` reescrito**: `porPaciente()` ya no lista múltiples odontogramas — encuentra o crea el único vivo del paciente y va directo a `show()`. Nuevos endpoints: `actualizarSuperficie()` (reemplaza a `actualizarPieza()` v1, ahora por cara, registra `HistorialOdontologico` solo si el estado realmente cambió), `actualizarPiezaGeneral()` (condiciones de toda la pieza — ausente/extraída), `agregarDenticionTemporal()` (bajo demanda, confirmado con Francisco — no automático para todo paciente), `crearTratamiento()`/`completarTratamiento()`.

**Vista rediseñada** (`resources/views/panel/odontologia/show.blade.php`) — reescrita completa, no un ajuste sobre la v1. Cada pieza es un círculo con un `conic-gradient` dividido en franjas por superficie, coloreada según su estado — lectura "de un vistazo" de qué pieza tiene algo, sin abrir nada. Click en una pieza → panel de detalle con sus 5 superficies y su estado/observaciones, cada una editable. Panel lateral "Notas de todas las piezas" — todo lo que tiene observaciones (superficie, pieza entera o tratamiento), ordenado por fecha, exactamente lo que Francisco pidió ("dice todo lo que se hizo en cada pieza"). Sección de Tratamientos con alta rápida (tipo, superficie, material, fecha) y marcar-realizado. Dentición temporal: oculta por defecto, un botón "+ Agregar dentición temporal" la siembra bajo demanda.

**Tercer bug real, encontrado probando el render, no asumido correcto**: `@json()` de Blade con una expresión PHP multilínea (un `->map(function($p) {...})` con `return [...]` adentro) tira `ParseError: Unclosed '[' does not match ')'` — el parser de directivas de Blade no maneja bien expresiones complejas multilínea dentro de `@json(...)`. Fix: precalcular el array en el bloque `@php` de arriba (`$piezasDataJs = ...`) y pasar una variable simple a `@json($piezasDataJs)` en el script. Confirmado el render completo después (104KB, sin error).

**Se reescribió `OdontologiaDemoSeeder`** sobre el modelo nuevo — la narrativa de María González ("caries detectada → resuelta") ya no se puede representar con dos `Odontograma` separados (imposible ahora, cardinalidad 1×1): se representa con el estado ACTUAL de la superficie (`obturada`) más dos filas de `HistorialOdontologico` con fecha retroactiva (`cariada` hace 3 meses → `obturada` hoy) — literalmente el caso de uso para el que se construyó `HistorialOdontologico`. Confirmado con una lectura real de la base: la superficie oclusal de la pieza 26 de María está en `obturada`, con el historial completo debajo.

**Datos existentes descartados y re-sembrados** (decisión ya confirmada por Francisco): los 4 tenants Odontología reales (`ieqi62`, `vz6t6e`, `bmco49`, `izou1e`) se limpiaron (pacientes/odontogramas del Escenario Demo v1 borrados) y se re-sembraron contra el modelo nuevo — 5 odontogramas, 160 piezas, 800 superficies por tenant, confirmado.

**Dos ajustes en la ficha de paciente**, pedido explícito de Francisco:
1. El tab "PDFs" nunca había tenido ningún gate — se agregó `@unless(isCapabilityEnabled('odontologia'))` alrededor del botón de navegación y del panel de contenido completo.
2. "Historia completa" ahora muestra una sección "Odontograma" (solo si el paciente tiene uno y el tenant tiene Odontología instalada) con la tabla de piezas con novedad (estado distinto de sana, o con nota) y un link directo al odontograma completo.

**Validado end-to-end, con datos reales creados y borrados con precisión** (aprendiendo del incidente de la Etapa 6.6.2 — ID capturado directo del modelo, nunca de texto): tenant descartable nuevo, paciente real, `porPaciente()` → 32 piezas permanentes creadas; `actualizarSuperficie()` cambia `sana`→`cariada` confirmado con lectura fresca de la base, genera 1 fila de historial; `agregarDenticionTemporal()` lleva las piezas de 32 a 52; `crearTratamiento()` crea un tratamiento `pendiente`; `completarTratamiento()` lo pasa a `realizado` con `fecha_realizada` seteada; el render final con todo combinado (dentición temporal + tratamiento + material) renderiza sin error. Tenant de prueba borrado al terminar, verificado.

**Explícitamente no logrado, dicho con honestidad**: el diseño visual de la referencia que mandó Francisco (software dental profesional, con íconos de diente realistas y un selector "individual/múltiple") no se replicó píxel a píxel — no hay herramienta de diseño en este flujo de trabajo. Se construyó una versión **estructuralmente equivalente**: círculos por pieza con color por superficie (conic-gradient en vez de dientes ilustrados), panel de notas real al costado, panel de detalle al hacer click — mismo lenguaje visual y misma funcionalidad, con un estilo propio en vez de una copia exacta de la captura.
