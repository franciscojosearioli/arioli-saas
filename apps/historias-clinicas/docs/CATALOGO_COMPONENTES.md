# Catálogo de Componentes — Sistema de Salud como plataforma modular

Documento de **análisis y ordenamiento conceptual**, no de implementación. No hay código, migraciones ni tablas nuevas en este documento — es exactamente el mismo tipo de trabajo que `docs/ARQUITECTURA_MODULAR.md` hizo antes de escribir la primera línea de `PlatformRegistry`, aplicado ahora a la pregunta de negocio, no a la técnica: **qué es el producto**, no cómo se construye.

Motivo del documento (palabras de Francisco): después de las Etapas 4-6, el producto dejó de ser "un sistema de historias clínicas con componentes opcionales" y pasó a ser una **plataforma modular de Sistema de Salud** — con Odontología, Salud Mental y (hasta hace poco) Medicina Laboral como primera evidencia real de esa forma. Se usa Igaleno como *benchmark* de qué módulos suele tener una plataforma de este tipo — no como especificación a copiar.

## 1. Cómo se armó este documento

Relevamiento real del código, no memoria de la sesión: se listaron los 22 controllers de `Panel`/`Admin`, los 6 controllers de nivel superior, los 28 modelos de `app/Models/`, el módulo real de `app/Modules/Odontologia/`, las rutas de `routes/web.php` agrupadas por prefijo, y se hizo `grep` explícito buscando rastros de Portal del Paciente, WhatsApp, Telemedicina, Facturación y Sucursales antes de asumir que no existen. Cada afirmación de "esto ya existe" o "esto no existe" de este documento tiene una línea de código, un modelo o un `grep` vacío detrás — no es una lista de memoria.

## 2. Criterio para decidir Núcleo vs. Componente

Antes de clasificar cada pieza hace falta la regla, no solo el resultado — para que la próxima capacidad que aparezca (año 3, año 5) se pueda clasificar sin volver a discutir el criterio desde cero:

**Es Núcleo si:**
- Todo perfil comercial lo necesita, sin excepción — no hay ninguna combinación de Componentes que tenga sentido sin esto.
- Es una responsabilidad transversal de la plataforma (seguridad, trazabilidad, identidad), no un dominio clínico propio.
- Otros Componentes dependen de su existencia para funcionar (Odontología no tiene sentido sin Paciente ni sin Historia Clínica).

**Es Componente si:**
- Un perfil lo necesita y otro no — la señal más fuerte es que ya existe al menos un perfil real que lo tiene apagado.
- Agrega dominio propio (tablas, modelos, reglas) que el Núcleo no necesita conocer.
- Se puede instalar/desinstalar sin romper la coherencia de los perfiles que no lo usan (mismo contrato que `ComponenteInstaller` ya exige hoy).

**Zona gris real, no evitada**: hay capacidades que HOY están implementadas como "activas por default, algún perfil las apaga" (Medicación, Consentimientos) en vez de "apagadas por default, algún perfil las prende" (Odontología, Salud Mental). Técnicamente usan el mismo mecanismo (`capability_states` + `CapabilityInstaller`), pero la **polaridad** es distinta — y esa polaridad es exactamente la señal de si algo es "Núcleo con excepción" o "Componente opcional". Se usa como criterio de desempate en la sección 4.

## 3. Matriz completa

Convención de la columna **Estado real**: `✅ completo` (existe y cubre el caso de uso descrito), `🟡 parcial` (existe algo, pero no lo que un Igaleno-like esperaría), `⬜ roadmap` (no existe ni como stub — confirmado por grep, no asumido).

### 3.1 Núcleo (siempre presente)

| Capacidad | Estado real | Evidencia | Nota |
|---|---|---|---|
| **Pacientes** | ✅ completo | `Paciente` + 9 modelos satélite (`PacienteConyugue`, `PacienteHijo`, `PacienteHermano`, `PacienteFichaAdmision`, `PacienteDatosAdicionales`, `PacienteEducacion`, `PacienteLaboral`, `PacientePadresTutor`, `PacienteProblematica`, `PacienteReingreso`, `PacienteHistorialTratamientos`), `Panel/PacienteController`, wizard de alta/edición ya adaptado por perfil (Etapa 6.6.4) | Modelo más maduro de la plataforma — es el que más iteraciones tuvo esta sesión |
| **Agenda** | 🟡 parcial | `Agenda` (modelo + controller), capability `agenda` siempre activa | Turnos individuales por profesional — **sin** multiagenda/vista semanal consolidada, sin gestión de consultorios como recurso propio, sin confirmaciones automáticas. Es Núcleo real, pero angosto frente a lo que Igaleno-style espera |
| **Historia Clínica (Informes)** | ✅ completo | `Informe`/`InformeTipo` + motor de plantillas versionado (`PlantillaDocumento`/`PlantillaDocumentoVersion`, Etapa "Informes — primera infraestructura genuinamente nueva") | El subsistema más sofisticado del Núcleo — versionado real, UI conectada, validación cruzada |
| **Profesionales** | 🟡 parcial | `User` + `Especialidad` (belongsToMany), sin modelo propio "Profesional" separado de `User` | Un profesional es un `User` con especialidades asociadas — no tiene horarios propios, matrícula estructurada (solo `firma_matricula` de texto libre), ni disponibilidad configurable por consultorio |
| **Institución** | 🟡 parcial | `ConfiguracionSistema` — singleton por tenant (`instancia()`, `firstOrCreate([])`) | Branding + datos de contacto reales. **Confirmado sin sucursales**: el modelo asume una sola ubicación física por tenant — coherente con la arquitectura multi-tenant (una institución = un tenant), pero es una limitación real si un cliente tiene 2 consultorios físicos |
| **Usuarios / Roles / Permisos** | ✅ completo | `User`/`Role`/`Permission`, `AuthGates` middleware, Gate-por-`capability_key` ya validado en 3 sesiones distintas | Maduro, es la base de todo el sistema de capabilities |
| **Consentimientos** | ✅ completo (con matiz de polaridad, ver 4.1) | `Consentimiento`/`TipoConsentimiento`, `FirmaPublicaController` (firma remota por link con token, sin login del paciente) | Tiene su propio mecanismo de firma pública — ver 4.3 sobre si esto es el germen de "Firma Digital" como Componente futuro |
| **Recetas** | ✅ completo | `Receta`, `Panel/RecetaController`, ligada a `Informe` | Confirmado en Ajustes post-6.5: aplica incluso a Odontología (a diferencia de Medicación) |
| **Archivos** | ⬜ roadmap real (no lo que el nombre sugiere) | `Panel/DocumentoController@show` — lee `informe->document_files` (columna JSON en `Informe`) | **No es un sistema de archivos genérico.** No hay tabla `documentos`, no hay adjuntos a nivel Paciente, no hay versionado ni control de acceso propio — es un JSON colgado de un Informe puntual. Esto es una brecha real si se lo compara con "Archivos" como Igaleno lo vende |
| **Auditoría** | ✅ completo, y **mal clasificado en el borrador de Francisco** (ver 4.2) | `AuditLog` (polimórfico: `subject_id`/`subject_type`) + trait `Auditable`, aplicado hoy a `Paciente`, `User`, `Role`, `Permission`, `Agenda`, `Informe`, `InformeTipo`, `Especialidad`, `UserAlert` | Mecanismo genérico y transversal — no es algo que un perfil "active", es infraestructura de cumplimiento normativo que todo dato clínico necesita |

### 3.2 Componentes de especialidad

| Componente | Estado real | Evidencia |
|---|---|---|
| **Odontología** | 🟡 parcial (mecanismo completo, dominio clínico mínimo) | `app/Modules/Odontologia/` — `Odontograma`/`PiezaDental`, `OdontologiaExtension` (provisiona permisos), gráfico visual click-to-edit (Etapa 6.6.3). Pendiente: el rediseño de modelo de dominio que se está por acordar (superficies, tratamientos, dentición temporal, historial) — ver `ARQUITECTURA_MODULAR.md` |
| **Salud Mental** | 🟡 parcial (sin tablas propias) | `config/platform/componentes.php` — `capabilities: []`, solo dispara `fieldVisibilitySeed: ['ficha_psicosocial_extendida']`. No tiene dominio propio (sesiones, escalas, diagnósticos, objetivos terapéuticos — todo lo que Francisco listó en su borrador) — hoy es 100% reutilización de las sub-fichas de Paciente que ya existían |
| **Medicina Laboral** | ⬜ retirado por completo | Eliminado en Etapa 6.6.1 (código, tablas, config) — decisión explícita de Francisco ("no voy a proveer eso") | Se lista acá solo para que la matriz quede completa frente al borrador — no es candidato a reconstruir salvo pedido explícito nuevo |
| **Pediatría** (percentiles, vacunas, crecimiento) | ⬜ roadmap | Sin rastro en el código | — |
| **Cardiología, Oftalmología, Nutrición, Kinesiología, Dermatología, etc.** | ⬜ roadmap | Sin rastro | El mecanismo (`ComponenteInstaller`, `ComponenteExtension`, `fieldVisibilitySeed`) ya está probado con 2 casos reales (Odontología, Salud Mental) — agregar el 3° componente de especialidad es repetir un patrón conocido, no diseñar uno nuevo |

### 3.3 Componentes administrativos

| Componente | Estado real | Evidencia |
|---|---|---|
| **Facturación / Caja / Contabilidad / Liquidaciones** | ⬜ roadmap total | `grep` vacío en toda la app | Importante no confundir con la app **central** (`/opt/arioli-saas/src`) — esa factura la *suscripción SaaS* del cliente a Arioli (Order/Plan/License), no la facturación del cliente a *sus propios* pacientes/obras sociales. Son dos negocios distintos que comparten la palabra "facturación" |

### 3.4 Componentes de comunicación

| Componente | Estado real | Evidencia |
|---|---|---|
| **Portal del Paciente** | ⬜ roadmap, con una semilla ya sembrada | `grep` vacío para "portal.*paciente" en código, PERO `informes_tipos.visible_portal` (columna real, del motor de plantillas) ya existe sin consumidor — documentado desde la etapa que la creó como "sin Portal todavía" | Cuando se construya, ya hay una política (`visible_portal`) esperando quién la lea |
| **Recordatorios (WhatsApp/Email/SMS)** | ⬜ roadmap | `grep` vacío | Notificaciones hoy = `UserAlert`, 100% interno (staff-a-staff), sin canal de salida ni destinatario paciente — ver 4.4 |
| **Mensajería interna (Qa Topics)** | ✅ completo, pero **no es lo que el nombre de Igaleno sugiere** | `QaTopic`/`QaMessage`, `Panel/MessengerController` — mensajería *staff-a-staff* (`creator_id`/`receiver_id`, ambos `User`) | Existe y funciona, pero es una herramienta de colaboración interna del equipo, no un canal de comunicación con el paciente — no debería contarse como "Componente de comunicación" en el sentido del borrador |
| **Telemedicina** | ⬜ roadmap | `grep` vacío | — |
| **Firma Digital** | ⬜ roadmap como Componente genérico, pero con un caso real ya resuelto adentro de Consentimientos (ver 4.3) | `FirmaPublicaController` + `Consentimiento.token`/`tokenValido()` | Firma remota por link, sin login, con expiración — funciona, pero está cableada específicamente a `Consentimiento`, no es reutilizable hoy para firmar, por ejemplo, un presupuesto o un informe |

### 3.5 Componentes de negocio

| Componente | Estado real | Evidencia |
|---|---|---|
| **Reportes / Estadísticas** | ⬜ roadmap real | El "dashboard" existente (`Admin\HomeController`, editado esta sesión) es una pantalla fija con 5 KPIs hardcodeados, no un motor de reportes configurable | — |
| **Dashboard** | 🟡 parcial, ver arriba | — | — |
| **Auditoría** | Ver 3.1 — reclasificada como Núcleo | — | — |

## 4. Hallazgos: dónde el borrador de Francisco y el código real no coinciden

### 4.1 Medicación y Consentimientos tienen la polaridad invertida frente a Odontología/Salud Mental

Mismo mecanismo técnico (`capability_states`, `CapabilityInstaller`), pero:
- **Odontología, Salud Mental** → apagados por default, un Componente los *prende* (`capabilities: [...]` en `Componente`).
- **Medicación, Consentimientos** → prendidos por default para todo tenant nuevo (`CapabilityStatesSeeder`), y un Componente los *apaga* explícitamente (`capabilitiesDisabled: [...]`, agregado primero para Odontología+medicación en el ajuste post-6.5, después para Odontología+consentimientos en 6.6.2).

Esto no es un detalle técnico — es la diferencia entre "Componente opcional que un perfil elige agregar" y **"capacidad de Núcleo con una excepción documentada por perfil"**. Medicación y Consentimientos deberían pensarse como Núcleo (todo perfil parte con ellos) con una lista de excepciones por Componente, no como Componentes que "no se activaron". La distinción importa para el catálogo: un cliente nuevo no debería tener que "elegir" Consentimientos en un selector de Componentes — lo tiene por default, salvo que su perfil lo excluya explícitamente.

### 4.2 Auditoría no es un Componente de negocio vendible — es Núcleo transversal

El borrador la lista junto a Reportes/Dashboard/Estadísticas. El código real (`Auditable` trait ya aplicado a 9 modelos, incluyendo `Paciente`) sugiere otra cosa: trazabilidad de cambios sobre datos clínicos no es una feature que un cliente "compra aparte" — en la mayoría de los marcos regulatorios de salud es una expectativa mínima, igual que control de acceso por rol. Reportes/Estadísticas sí son negociables como Componente (agregan valor analítico opcional); Auditoría no compite en la misma categoría.

### 4.3 Firma Digital y Portal del Paciente ya tienen semillas reales, no son roadmap "desde cero"

- `FirmaPublicaController`/`Consentimiento.token` resuelve exactamente el problema de "firma remota sin fricción" — el día que se generalice a otros documentos (Recetas, presupuestos de Odontología), el patrón (token firmado, expiración, sin cuenta de paciente) ya está probado en producción, no hay que diseñarlo de nuevo.
- `informes_tipos.visible_portal` es una política que ya existe y ya se aplica a cada tipo de documento — el Portal del Paciente, cuando se construya, entra a un sistema que ya sabe distinguir qué debería mostrarse ahí y qué no.

Esto es evidencia a favor del criterio de "no generalizar antes de la segunda necesidad real" que ya rigió `ExtensionContribution`/`CapabilityInstaller` — la sesión ya dejó, sin buscarlo, la base de dos Componentes de comunicación futuros.

### 4.4 Mensajería interna no debe confundirse con Notificaciones/Recordatorios al paciente

Son necesidades completamente distintas (colaboración de equipo vs. canal de comunicación con terceros) que el borrador podría fusionar por compartir la palabra "mensajería". `QaTopic` no es ni el punto de partida técnico de un futuro componente de WhatsApp/Email/SMS — son problemas de dominio distintos (uno es interno y sincrónico dentro de la app, el otro es externo y depende de proveedores de mensajería de terceros).

### 4.5 "Archivos" en el borrador no corresponde a lo que existe con ese nombre

Vale la pena que quede explícito para no asumir cobertura que no existe: hoy "Archivos" es una columna JSON colgada de `Informe`, no una capacidad de Paciente. Si el catálogo lo promete como capacidad de Núcleo (como lo lista Francisco, bajo Historia Clínica), hace falta construirlo — no es un caso de "ya existe pero está mal ubicado", es directamente roadmap disfrazado de feature existente.

## 5. Perfiles comerciales como combinación de Componentes

Estado real hoy (`config/platform/perfiles.php`, post Etapa 6.6.1):

| Perfil | Componentes que instala | Excepciones de Núcleo |
|---|---|---|
| `clinica_general` | Ninguno (`componentes: []`) | Ninguna — tiene el Núcleo completo, sin excepciones |
| `odontologia` | `odontologia` | `medicacion` y `consentimientos` apagados |
| `salud_mental` | `salud_mental` | Ninguna — usa el Núcleo completo, el Componente solo cambia qué secciones de la ficha de Paciente son visibles |

Esto ya es, en los hechos, exactamente el modelo que Francisco describe como "el próximo salto": un Perfil no es más que **Núcleo + lista de Componentes + lista de excepciones de Núcleo**. No hace falta inventar el mecanismo — `ComponenteInstaller::instalar($perfil->componentes)` + `capabilitiesDisabled` ya lo implementan. Lo que falta es que el *catálogo* de Componentes disponibles crezca (Pediatría, Cardiología, Portal, Facturación...), no el mecanismo que los combina.

**Personalización por cliente** (mencionada por Francisco: "el cliente incluso podría personalizar esa combinación"): técnicamente ya es posible hoy — `ComponenteInstaller::instalar()` acepta cualquier array de keys de Componente, no solo los que trae un `Perfil` predefinido. Lo que no existe es una **UI** para que un cliente (o el staff en su nombre) arme esa combinación fuera de los 3 perfiles predefinidos — eso sí es roadmap real, de UI, no de arquitectura.

## 6. Roadmap explícito (sin comprometer diseño ni fechas)

Ordenado por lo que ya tiene semilla real (más barato) a lo que es 100% nuevo (más caro):

1. **Odontología — dominio clínico completo.** Ya en curso de acuerdo (ver `ARQUITECTURA_MODULAR.md`, análisis de modelo pendiente de confirmación final).
2. **Firma Digital como Componente genérico**, generalizando `FirmaPublicaController` más allá de Consentimientos.
3. **Portal del Paciente**, apoyado en `visible_portal` ya existente.
4. **Archivos como capacidad real de Núcleo** (hoy es un JSON de `Informe`, no alcanza para "Archivos" como lo vende el borrador).
5. **Agenda — multiagenda/vista semanal/consultorios como recurso.** Hoy es turnos individuales, angosto frente al estándar del mercado.
6. **Nuevos Componentes de especialidad** (Pediatría, Cardiología, etc.) — mecanismo ya probado 2 veces, patrón conocido, sin trabajo de arquitectura nuevo.
7. **Reportes/Estadísticas como Componente configurable**, reemplazando el dashboard hardcodeado actual.
8. **Recordatorios (WhatsApp/Email/SMS) y Telemedicina** — dependen de integraciones con terceros, ningún trabajo previo hecho.
9. **Facturación/Caja/Contabilidad** — el más grande y el más alejado de lo que existe hoy; ningún componente de este dominio tiene siquiera una tabla parecida en el código actual.
10. **UI de personalización de Componentes por cliente**, una vez que el catálogo (puntos 1-6) tenga más de 3 Componentes reales — con 3 no se justifica la complejidad de una UI genérica todavía (mismo criterio de "esperar a la segunda/tercera ocurrencia real" que ya se aplicó en toda la arquitectura).

No se proponen fechas ni se compromete orden de implementación — esto es un mapa, no un plan de sprints.

## 7. Propuesta: catálogo único como fuente de verdad

Pedido de Francisco tras revisar este documento: el catálogo no debe quedar solo como documentación — debe ser la única definición de cada Componente, consumida por igual por la arquitectura (`ComponenteInstaller`), el portal de demos, el provisioning de clientes reales y el checkout/producto en Arioli.dev. Sigue siendo **propuesta, no implementación** — nada de lo que sigue está construido todavía.

### 7.1 Duplicación real confirmada (no supuesta)

Se buscó explícitamente, en los dos repos, cada lugar que hoy conoce la lista de perfiles/componentes:

| Dónde | Qué sabe | Repo |
|---|---|---|
| `config/platform/perfiles.php` | La definición completa por perfil (nombre, descripción, componentes, nombreSistema, caracteristicas) | historias-clinicas — **fuente más completa hoy** |
| `config/platform/componentes.php` | Qué instala cada Componente (capabilities, extension, navegación...) | historias-clinicas |
| `CheckoutController::PERFILES_SISTEMA_SALUD` | Copia parcial (solo `key => label`) del mismo listado de `perfiles.php` | app central — **duplicación real, cross-repo** |
| `TenantsCrear::ESCENARIOS_DEMO` | Qué perfiles tienen seeder de datos demo | historias-clinicas — **duplicación real, mismo repo** |

La duplicación cross-repo ya mordió en esta misma sesión: al retirar Medicina Laboral (Etapa 6.6.1) hubo que acordarse de tocar `perfiles.php` **y** `CheckoutController.php` a mano, en dos repos separados, dejando un comentario pidiendo sincronización manual. Eso es exactamente el síntoma que se quiere eliminar.

### 7.2 Restricción real que cualquier propuesta tiene que respetar

historias-clinicas y la app central son repos separados, sin paquete compartido, con deploys independientes — y así van a seguir (no hay pedido de unificarlos). Eso descarta, sin necesidad de explorarlas a fondo:

- **Paquete Composer compartido versionado entre los dos repos** — agrega infraestructura de publicación que hoy nadie mantiene, para un catálogo que cambia con la frecuencia de un deploy, no de un request.
- **Que la central app lea directo la DB o el filesystem de historias-clinicas** — rompe el aislamiento multi-tenant que Gate G-01 cuida deliberadamente desde Etapa 6.

Lo que **sí** existe y ya es el canal formal entre las dos apps: los endpoints `/internal/*` (`provision`, `demo/seed`, `demo/reset`), protegidos por `ValidateApiKey` con el bearer token compartido. La propuesta usa exactamente ese mismo mecanismo, no uno nuevo.

### 7.3 Propuesta central: historias-clinicas sigue siendo la fuente; se expone por API interna de solo lectura

Nuevo endpoint `GET /internal/catalogo`, mismo mecanismo que ya existe: devuelve `config('perfiles')` + `config('componentes')` serializado a JSON, protegido por `ValidateApiKey`.

La app central deja de tener `PERFILES_SISTEMA_SALUD` hardcodeado — lo reemplaza por una llamada cacheada a ese endpoint. Con esto, el checkout, la ficha de producto y cualquier otro consumidor futuro del lado central leen el mismo catálogo que ya gobierna qué instala `ComponenteInstaller` — no una copia.

**Comportamiento ante falla, decidido de antemano, no como ocurrencia tardía**: si historias-clinicas está caída o el endpoint falla, el checkout de historias-clinicas no debería tumbarse por completo (y mucho menos afectar a loteos/tallerpro, que no dependen de este catálogo). Propuesta: caché con TTL largo del lado central + servir el último valor cacheado como fallback si el fetch falla, en vez de error visible. El catálogo cambia con la cadencia de un deploy, no con la de un usuario navegando — no hace falta pegarle a historias-clinicas en cada carga de checkout.

### 7.4 Qué le falta declarar a `Componente` para ser la única fuente (aditivo, no reemplaza nada)

Hoy `Componente` describe únicamente "qué le hace esto a un tenant al instalarse" — nada sobre "cómo se vende". Campos nuevos propuestos, todos con default que preserva el comportamiento actual:

- **`categoria`**: `especialidad` | `administrativo` | `comunicacion` | `negocio` | `null`. Nullable a propósito — Componentes que son "Núcleo con excepción" (Medicación, Consentimientos, ver sección 4.1) no tienen categoría comercial, no se venden aparte.
- **`dependencias`**: array de keys de otros Componentes que este necesita. Hallazgo real de este análisis: **hoy `ComponenteInstaller` no valida dependencias entre Componentes** — no existe el concepto todavía. Se propone declarar el campo sin activar ninguna validación (no hay ningún caso real hoy de un Componente que dependa de otro más allá del Núcleo) — se aplica el mismo criterio que ya rigió toda la arquitectura: el dato se declara cuando aparece la necesidad real, el enforcement se construye cuando aparece el segundo caso que lo necesite.
- **`disponibleEnDemo`** / **`disponibleEnCheckout`**: booleanos, hoy implícitos (si está en `perfiles.php`, aparece en ambos lados por igual). Separarlos permite que, por ejemplo, un futuro Componente administrativo (Facturación) exista y se pueda contratar sin tener una demo pública standalone.

### 7.5 `Perfil` no se fusiona con `Componente` — resuelven preguntas distintas

Un Perfil sigue siendo el bundle comercial curado (Odontología, Salud Mental, Clínica General...) = Núcleo + lista de Componentes + excepciones de Núcleo + copy propio (`nombreSistema`, `caracteristicas`). No se fusiona con `Componente` porque la distinción ya demostró ser útil en este mismo documento: "Medicación es Núcleo con excepción, no un Componente" (sección 4.1) solo es una afirmación con sentido si Perfil y Componente son cosas distintas.

Lo que sí se propone: **plegar `TenantsCrear::ESCENARIOS_DEMO` dentro de `Perfil`** como un campo `demoSeeder` — hoy vive en un tercer archivo separado, exactamente el tipo de duplicación interna que se quiere eliminar. Un Perfil pasaría a declarar en un solo lugar todo lo que hoy está repartido en dos.

### 7.6 Camino de migración — aditivo, en pasos reversibles, ninguno depende del siguiente

1. Agregar los campos nuevos a `Componente`/`Perfil` con defaults que preservan el comportamiento exacto de hoy — cero cambio de comportamiento el día que se agregan.
2. Construir `GET /internal/catalogo`, probado de forma aislada (curl con bearer token) antes de que nada dependa de él.
3. Migrar `CheckoutController` para consumir el endpoint en vez de `PERFILES_SISTEMA_SALUD`, con el fallback de caché ya decidido en 7.3.
4. (Más adelante, no ahora) usar el mismo catálogo para alimentar el "qué incluye" de la página de producto en Arioli.dev, hoy prosa hardcodeada en `landing/product.blade.php`.

Se puede parar en cualquier paso sin dejar nada roto — ninguno asume que el siguiente se va a hacer.

### 7.7 Decisiones confirmadas por Francisco

- **Autenticación del endpoint nuevo**: se reutiliza el mismo bearer token que ya protege `/internal/provision` — un token de solo lectura separado se evalúa recién si aparece un consumidor real con permisos distintos. Separar ahora sería una abstracción anticipada sin necesidad confirmada, mismo criterio que ya rigió toda la arquitectura.
- **`demoSeeder` se pliega dentro de `Perfil` ahora**, no se difiere — elimina la tercera lista (`TenantsCrear::ESCENARIOS_DEMO`) en el mismo movimiento, en vez de dejarla pendiente para "cuando se toque Perfil por otro motivo".

### 7.8 Corrección al modelo mental: el Catálogo es la fuente, el endpoint es un consumidor más

Ajuste de Francisco sobre cómo se venía planteando el punto 7.3: el endpoint `/internal/catalogo` **no es** la fuente de verdad — es uno más de los consumidores del Catálogo de Componentes que ya vive en `config/platform/` dentro de historias-clinicas, exactamente igual que `ComponenteInstaller`, el portal de demos o `TenantsCrear`. El endpoint no construye nada — serializa a JSON el mismo catálogo que la aplicación ya usa internamente. Este documento (sección 7) queda corregido con ese entendimiento; no cambia ninguna decisión técnica de 7.1-7.6, corrige el diagrama mental de por qué existe el endpoint.

```
Catálogo de Componentes (config/platform/, historias-clinicas)
        │
        ├── Perfiles
        ├── Demos (portal público + demoSeeder plegado desde TenantsCrear)
        ├── Provisioning (tenants:crear, ProvisionClienteService)
        ├── UI interna (paneles, navegación)
        ├── Endpoint /internal/catalogo (serializa, no construye)
        └── Futuras integraciones (checkout central, ficha de producto)
```

### 7.9 Validación final — barrido completo antes de implementar, sin asumir nada limpio

Pedido explícito: encontrar cualquier dato de Componentes/Perfiles/Demos que viva hoy fuera del catálogo único y pueda convertirse en una tercera fuente sin que nadie lo note. Resultado del barrido, con evidencia:

**Confirmado sucio — ya ocurrió, no es hipotético.** `resources/views/landing/product.blade.php` (app central) tiene prosa de marketing hardcodeada (tagline, `description`, 6 `features`, 4 `steps`, 5 `faqs`) que nombra "Medicina Laboral" explícitamente en 5 lugares distintos — pese a que Medicina Laboral se retiró por completo del catálogo en la Etapa 6.6.1. **La página de producto en producción hoy promociona una especialidad que ya no existe.** Es exactamente el síntoma que se busca eliminar, ya materializado sin que nadie lo notara — la evidencia más fuerte a favor de que el catálogo único no puede ser "documentación que alguien debería consultar", tiene que ser la fuente que alimenta esta página directamente.

**Confirmado limpio** (revisado archivo por archivo, no asumido): `Plan` de la app central (nombres de frecuencia de facturación — "Sistema de Salud — Mensual/Trimestral/...", sin ninguna mención de especialidad); `checkout.blade.php` (renderiza `$perfilKey => $perfilLabel` recibido del controller, ninguna descripción propia hardcodeada); y del lado de historias-clinicas, **todos** los consumidores reales de perfil/componente (`ProvisionDemoService`, `ProvisionClienteService`, `Internal\ProvisionController`, `DemoCrear`, `DemoPublicoController`, `TenantsCrear`, `ComponenteInstaller`, `NavigationInstaller`) leen `config('perfiles')`/`config('componentes')` dinámicamente — ninguno mantiene su propia copia.

**Hallazgo adyacente, no es duplicación de catálogo pero es limpieza pendiente**: `App\Console\Commands\ProvisionHistoriasDirectly` y `UpdateHistoriasDirectly` (app central) son el mecanismo viejo pre-Gate-G02 (`provision:historias-clinicas-direct`, recibe la contraseña del admin como argumento de texto plano, clona tablas a mano) — confirmado que **nada los llama** hoy (`Admin\TenantController::store()` ya despacha `ProvisionHistoriasInstance`, el job nuevo). No conocen el concepto de Perfil en absoluto (por eso se reemplazaron), así que no son una tercera fuente de catálogo — pero son código muerto con manejo de contraseñas en texto plano todavía presente en el repo. Fuera del alcance de este documento (no es catálogo), se anota para no perderlo de vista.

**Conclusión de la validación**: una sola duplicación real de catálogo existe hoy (`CheckoutController::PERFILES_SISTEMA_SALUD`, ya cubierta por 7.1-7.6) más una consecuencia directa de esa duplicación ya materializada (`product.blade.php`, desactualizado). No apareció ninguna tercera fuente adicional en el barrido.

**Resuelto — parche manual aplicado, deliberadamente separado de la causa.** Decisión de Francisco: no dejar información comercial incorrecta publicada mientras se construye la solución estructural — "eso es un bug de producto, no un detalle de implementación". Se corrigieron a mano las 5 menciones de Medicina Laboral en `product.blade.php` (`description`, dos `features`, un `step`, un `faq`), verificado con diff línea por línea que no se tocó nada más, deployado y confirmado en vivo (`curl` contra `arioli.dev/productos/historias-clinicas` sin ninguna mención de Medicina Laboral, con Odontología/Salud Mental/Clínica General intactos). Este parche es exactamente el tipo de arreglo manual que el catálogo único existe para eliminar — cuando el catálogo esté implementado (7.6, paso 4), este contenido pasa a generarse desde ahí y el parche deja de tener sentido de mantenerse a mano.

**Nota aparte, fuera del pedido de esta corrección**: durante la validación (7.9) también quedó registrado que la FAQ "¿Funciona para múltiples consultorios o sedes?" de esta misma página responde que sí — pero `ConfiguracionSistema` es un singleton por tenant (confirmado en la sección 3.1 de este documento), sin soporte real de multi-sede hoy. Es la misma clase de problema (contenido comercial que no refleja el producto real) pero no fue parte de lo pedido en esta corrección — queda anotado, no corregido, a la espera de que Francisco decida si también se ajusta ahora o se deja para más adelante.

## 8. Etapa 7.1 — Reorganización interna del catálogo (cerrada)

Implementado, sin tocar ningún consumidor externo (endpoint, checkout, Arioli.dev) — exactamente el alcance pedido. Detalle completo abajo; resumen: `Componente` gana los campos comerciales que le faltaban, `Perfil` pliega la tercera lista que quedaba suelta, y los 8 consumidores reales (`ComponenteInstaller`, `NavigationInstaller`, `TenantsCrear`, `ProvisionDemoService`, `Internal\ProvisionController`, `DemoPublicoController`, `DemoCrear`, y transitivamente `CapabilityInstaller`) siguen funcionando sin que se les haya tocado una sola línea, salvo `TenantsCrear` (cambio mínimo y necesario, ver abajo).

### 8.1 Auditoría — qué vivía dónde, antes de mover nada

| Dato | Vivía antes | Vive ahora |
|---|---|---|
| `nombre` (del Componente, técnico) | `Componente.nombre` | Igual — sin cambios |
| `nombre` (del Perfil, comercial) | `Perfil.nombre` | Igual — es información distinta de la anterior, no un duplicado (ej. componente `odontologia`.nombre = "Odontología"; perfil `odontologia`.nombre = "Consultorio Odontológico") |
| `descripción` (Componente y Perfil) | Cada uno la suya | Igual — mismo criterio que `nombre` |
| `categoría` | No existía en ningún lado | **Nuevo**: `Componente.categoria` |
| `core` (¿es Núcleo?) | No existía como campo — se infería (si no está en `componentes.php`, es Núcleo) | **Nuevo**: `Componente.core`, con la limitación honesta anotada abajo (8.4) |
| `demo` / `contratable` | No existían — implícito (todo lo que está en `perfiles.php` aparece en ambos lados por igual) | **Nuevo**: `Componente.demo` / `Componente.contratable` |
| `dependencias` | No existía — `ComponenteInstaller` nunca las valida | **Nuevo**: `Componente.dependencias`, declarativo, sin enforcement (mismo criterio que toda la arquitectura: se activa cuando aparece el primer caso real) |
| `capabilities` / `capabilitiesDisabled` | `Componente` | Igual |
| `fieldVisibilitySeed` / `tiposDocumentoSeed` / `configuracionInicial` | `Componente` | Igual |
| `extension` / `navegacionSeed` | `Componente` | Igual |
| `componentes` (qué instala un Perfil) | `Perfil.componentes` | Igual |
| `nombreSistema` / `caracteristicas` | `Perfil` | Igual — son configuración propia del Perfil, no descripciones de Componentes |
| `demoSeeder` (seeder de datos demo) | `TenantsCrear::ESCENARIOS_DEMO` — **tercera lista, separada de `perfiles.php`** | **Movido**: `Perfil.demoSeeder` |
| "qué perfiles usan este Componente" | No existía en ningún lado, se podía inferir a mano recorriendo `perfiles.php` | **Nuevo, pero derivado, no declarado**: `CatalogoComponentes::perfilesQueUsan()` — ver 8.3 sobre por qué no es un campo |

### 8.2 Diseño definitivo aplicado

`Componente` (`app/Platform/DTO/Componente.php`) — todos los campos nuevos opcionales con default que preserva el comportamiento de cada Componente ya declarado:

```php
new Componente(
    key: 'odontologia',
    nombre: 'Odontología',
    descripcion: '...',
    categoria: 'especialidad',
    core: false,
    demo: true,
    contratable: true,
    dependencias: [],
    capabilities: ['odontologia'],
    // ...resto de campos técnicos, sin cambios...
)
```

`Perfil` gana un solo campo nuevo, `demoSeeder` (nullable, `?class-string`) — ya cargado para `odontologia` y `salud_mental` con las clases que antes vivían en `TenantsCrear::ESCENARIOS_DEMO`; `clinica_general` queda en `null` (nunca tuvo seeder demo).

### 8.3 Por qué "qué perfiles lo utilizan" no es un campo de `Componente`

Desviación deliberada del ejemplo original de Francisco, justificada: declararlo como campo (`Componente.perfiles = [...]`) habría creado exactamente el tipo de duplicación bidireccional que esta etapa existe para eliminar — `Perfil.componentes` ya dice "qué instalo"; si `Componente` *también* dijera "quién me usa", las dos listas habría que mantenerlas sincronizadas a mano en direcciones opuestas cada vez que un Perfil cambia. Se resolvió como consulta derivada: `App\Platform\CatalogoComponentes::perfilesQueUsan(string $key): array`, clase nueva, mínima, un solo método estático, sin estado — recorre `config('perfiles')` y filtra. Un único lugar de autoría (`Perfil.componentes`), la vista inversa se calcula, nunca se declara dos veces. `Componente`/`Perfil` se mantienen puramente descriptivos, sin conocer `config()` — la nueva clase es la única que sí lo hace, y existe porque el dato se pidió explícitamente, no por anticipación.

### 8.4 Limitación honesta sobre `core`

El campo `Componente.core` solo tiene sentido para lo que **ya tiene una fila** en `componentes.php` — hoy ninguno de los 2 Componentes reales (`odontologia`, `salud_mental`) es `core=true`, ambos son claramente opcionales. El caso real de "Núcleo con excepción" identificado en la sección 4.1 (Medicación, Consentimientos) **no tiene fila propia en este catálogo** — son capabilities del Núcleo que un Componente apaga vía `capabilitiesDisabled`, no Componentes en sí mismos. El campo `core` no resuelve ese caso; se declaró honestamente sin forzar una solución que hubiera significado convertir Medicación/Consentimientos en Componentes falsos solo para que encajaran en el campo — eso sí habría sido un cambio funcional/de instalación, fuera del alcance explícito de esta etapa ("ningún cambio funcional visible").

### 8.5 Único cambio de código real: `TenantsCrear`

`ESCENARIOS_DEMO` (el `const` array) se eliminó por completo; la línea que lo consumía pasa de `self::ESCENARIOS_DEMO[$perfilKey]` a `$perfil->demoSeeder` (la variable `$perfil` ya estaba en scope en ese punto del método, cargada desde `config('perfiles.'.$perfilKey)`). Es el único archivo de código (no-config) que cambió — necesario para eliminar la tercera lista, tal como Francisco pidió explícitamente ("sí plegaría `ESCENARIOS_DEMO` dentro de Perfil... cuantas menos fuentes, mejor").

### 8.6 Compatibilidad — verificada, no asumida

- **Los 4 archivos de config/DTO** (`Componente.php`, `Perfil.php`, `componentes.php`, `perfiles.php`) más `CatalogoComponentes.php` (nuevo) lintean limpio (`php -l`) y se confirmó por Tinker que `config('componentes')`/`config('perfiles')` cargan con todos los campos nuevos resueltos correctamente para los 3 perfiles y 2 componentes reales.
- **`CatalogoComponentes::perfilesQueUsan()`** probado con un caso usado (`odontologia` → `['odontologia']`), un caso usado (`salud_mental` → `['salud_mental']`) y un caso inexistente (`→ []`).
- **`TenantsCrear` con el nuevo campo `demoSeeder`**, probado end-to-end contra un tenant real descartable (no un mock): `tenants:crear catalogo_test_verificacion --perfil=odontologia --con-datos-demo` corrió las migraciones, aplicó el Componente `odontologia` vía `ComponenteInstaller` (verificado, no solo asumido — el log mostró "Perfil 'odontologia' aplicado: odontologia"), sembró el escenario demo vía el seeder correcto ("Escenario demo de 'odontologia' sembrado"), y una lectura fresca de la base confirmó **5 pacientes y 6 odontogramas** — el mismo número exacto que `OdontologiaDemoSeeder` siempre produjo (María González con 2 + 4 pacientes con 1 cada uno). Tenant de prueba borrado al terminar (DB + fila de `tenants`), verificado antes de continuar.
- **Portal público de demos** (`DemoPublicoController` + vistas), probado en vivo contra producción tras el deploy: los 3 perfiles (`Clínica / Consultorio Médico`, `Consultorio Odontológico`, `Centro de Salud Mental`) siguen apareciendo correctamente en `https://clinica.arioli.dev/demo`.
- **`route:list`** corrido después del deploy — cero errores, ningún registro de ruta rompió por los cambios (habría fallado ruidosamente si algún Service Provider o controller cargado en boot hubiera tenido un problema).
- **No verificados por separado, por bajo riesgo estructural** (misma razón: son chequeos de existencia — `if (! config('perfiles.'.$key))` — indiferentes a qué campos tiene el objeto): `Internal\ProvisionController`, `ProvisionDemoService`, `DemoCrear`. Cubiertos indirectamente por el mismo mecanismo ya probado en `TenantsCrear`.

### 8.7 ¿Apareció alguna duplicación nueva durante la implementación?

No. El único punto de riesgo real era "qué perfiles lo utilizan" (8.3), resuelto como derivado en vez de declarado — la implementación no introdujo ninguna lista paralela nueva. El catálogo (`componentes.php` + `perfiles.php`) sigue siendo el único lugar donde se escribe esta información; todo lo demás la lee.

### 8.8 Qué queda igual, explícitamente

- Ningún endpoint nuevo (`/internal/catalogo` sigue sin existir — Etapa 7.2).
- Cero cambios en la app central.
- Cero cambios funcionales visibles para un usuario o un cliente — mismos 3 perfiles, mismas demos, mismo comportamiento de provisioning.
- `Componente`/`Perfil` siguen siendo clases puramente descriptivas — no se les agregó lógica, solo datos.

## 9. Etapa 7.2 — Endpoint `/internal/catalogo` (adaptador puro)

Mismo criterio que 7.1: el endpoint no reconstruye nada, solo serializa lo que el catálogo ya dice.

### 9.1 Implementado

- **`App\Platform\CatalogoSerializer`** (`app/Platform/CatalogoSerializer.php`) — único lugar que convierte `Componente`/`Perfil` a array serializable. Dos métodos por entidad (`componente()`, `perfil()`) más `catalogoCompleto()` que arma la respuesta final. No lee `config()` fuera de `catalogoCompleto()`, no aplica ninguna regla de negocio — mapea campo a campo, más el único valor derivado (`perfiles` de cada Componente, vía `CatalogoComponentes::perfilesQueUsan()`, la misma clase de la Etapa 7.1 — no se reimplementó la lógica).
- **`Internal\CatalogoController@index`** — no arma ningún array; su única línea de lógica es `response()->json($this->serializer->catalogoCompleto())`.
- **`GET /internal/catalogo`**, agregado a `routes/internal.php`, dentro del mismo grupo que ya aplica `ValidateApiKey` (`api.key`) a todo `/internal/*` — no hizo falta tocar middleware ni agregar un token nuevo, confirmando la decisión ya tomada en 7.7 de reusar el mismo mecanismo.

**Qué se excluye deliberadamente de la serialización** (documentado en el docblock de `CatalogoSerializer`, no es un olvido): de `Componente` — `capabilities`, `capabilitiesDisabled`, `fieldVisibilitySeed`, `tiposDocumentoSeed`, `configuracionInicial`, `extension`, `navegacionSeed` — mecánica de instalación interna, no información comercial, y `extension` ni siquiera es serializable (es una instancia de `ComponenteExtension`, no un dato). De `Perfil` — `demoSeeder`, un detalle de implementación (qué clase de seeder correr) sin sentido para un consumidor externo.

### 9.2 Validado en producción, no solo local

```
GET /internal/catalogo sin token       → 401
GET /internal/catalogo con token malo  → 401
GET /internal/catalogo con token real  → 200, JSON completo
```

Respuesta real verificada (los 2 Componentes, los 3 Perfiles, con `perfiles` derivado correcto en cada Componente — `odontologia` → `["odontologia"]`, `salud_mental` → `["salud_mental"]`).

### 9.3 Validación 1 pedida: ¿alcanza para reemplazar `CheckoutController::PERFILES_SISTEMA_SALUD` por completo?

`PERFILES_SISTEMA_SALUD` se usa hoy en `CheckoutController` para exactamente dos cosas: (a) el mapa `key => label` que arma el selector de perfil en `checkout.blade.php`, y (b) la lista de keys válidas en la regla de validación `'in:...'`. La respuesta de `/internal/catalogo` trae `perfiles[].key` y `perfiles[].nombre` — alcanza para reconstruir el mapa exacto (`collect($catalogo['perfiles'])->pluck('nombre', 'key')`) y la lista de keys válidas (`collect($catalogo['perfiles'])->pluck('key')`). **Sí alcanza, sin necesidad de ningún campo adicional.** De hecho sobra información hoy no usada por el checkout (`descripcion`, `nombre_sistema`, `caracteristicas`) — disponible para si en el futuro el selector quiere mostrar más que un radio button con una etiqueta, sin que eso requiera tocar el endpoint de nuevo.

### 9.4 Validación 2 pedida: ¿quedó algún dato comercial fuera del catálogo único?

Mismo resultado que el barrido de la Etapa 7 (sección 7.9) — no apareció ningún caso nuevo al construir el endpoint. Único matiz para dejar explícito, no un hallazgo nuevo sino una aclaración de límite: `landing/product.blade.php` (ya corregido a mano en 7.9) sigue teniendo **prosa de marketing en texto libre** (tagline, features, steps, faqs del producto "historias-clinicas" como un todo) que **menciona** nombres de perfiles dentro del texto — pero no es una estructura `key => dato` paralela al catálogo, es contenido editorial. Migrar esa prosa para que se genere a partir de `caracteristicas` por perfil es exactamente el "paso 4, más adelante" ya anotado en la sección 7.6 — deliberadamente fuera del alcance de esta migración (que es sobre `CheckoutController`, no sobre la ficha de producto). No es una duplicación de catálogo sin resolver; es contenido de otro tipo, con su propio paso ya identificado y no descartado.

**Las dos condiciones pedidas antes de migrar `CheckoutController` están confirmadas.** A la espera de que Francisco confirme antes de tocar la app central.
