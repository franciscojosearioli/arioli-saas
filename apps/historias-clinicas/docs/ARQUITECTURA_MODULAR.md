# Arquitectura de plataforma modular — Documento de diseño (v8)

Estado: **diseño cerrado, Fase 0 implementada y deployada en producción** (`historias_default`, `historias_demo`). A partir de acá los ajustes vienen de la implementación real, no de una nueva ronda de diseño en el papel.

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

## Modelo de datos conceptual (no implementado)

```
demo_instances
  id
  component_key
  tenant_id
  status        -- pending | provisioning | ready | expired | deleted
  created_at
  expires_at    -- created_at + 24h
  deleted_at
```

Expiración: un job programado (o el `scheduler` que ya corre en el stack, ver `agenda:recordatorios` en `Kernel::schedule()`) revisa `demo_instances` vencidas y elimina el tenant temporal.

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
