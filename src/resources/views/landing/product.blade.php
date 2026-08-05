@php
$content = [
    'loteos' => [
        'tagline'     => 'Gestión integral de loteos y fraccionamientos',
        'description' => 'Organizá tus lotes, compradores, cuotas y cobranzas en un solo sistema. Diseñado para comercializadoras y desarrollistas inmobiliarios.',
        'emoji'       => '🏘️',
        'accent'      => '#60a5fa',
        'accent_bg'   => 'rgba(59,130,246,.15)',
        'features'    => [
            ['path'  => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
              'title' => 'Mapa de lotes interactivo',
              'desc'  => 'Visualizá el estado de cada lote en tiempo real —disponible, reservado o vendido— directamente sobre el mapa geográfico real del loteo.'],
            ['path'  => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
              'title' => 'Carga de lotes por KMZ o dibujo en el mapa',
              'desc'  => 'Subí el archivo KMZ de tu mensura y el sistema ubica cada lote automáticamente, o dibujá el polígono a mano directamente sobre el mapa.'],
            ['path'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
              'title' => 'Gestión de compradores',
              'desc'  => 'Ficha completa de cada comprador: datos personales, contacto y qué lotes tiene asignados.'],
            ['path'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
              'title' => 'Control de cuotas y cobranzas',
              'desc'  => 'Plan de cuotas por comprador, registro de pagos y cobro en lote: marcá varias cuotas como pagadas de una sola vez.'],
            ['path'  => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
              'title' => 'Recordatorios automáticos por email',
              'desc'  => 'El sistema avisa a cada comprador 7, 3 y 1 día antes del vencimiento de su cuota, y el mismo día, sin que tengas que acordarte vos.'],
            ['path'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
              'title' => 'Portal del cliente',
              'desc'  => 'Cada comprador entra con su usuario y ve sus propios lotes en el mapa, sus cuotas pendientes y su historial de pagos, sin poder modificar nada.'],
            ['path'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
              'title' => 'Reportes y estadísticas',
              'desc'  => 'Lotes por estado, cuotas por estado, clientes activos y totales cobrados, exportables en PDF o Excel.'],
        ],
        'steps' => [
            ['num' => '01', 'title' => 'Cargás el loteo',       'desc' => 'Subís el KMZ de la mensura o dibujás los lotes sobre el mapa, con manzana, medidas y precio de cada uno.'],
            ['num' => '02', 'title' => 'Registrás la venta',    'desc' => 'Asignás el lote a un comprador y lo marcás como reservado o vendido, con su plan de cuotas.'],
            ['num' => '03', 'title' => 'Gestionás los cobros',  'desc' => 'Registrás los pagos, cobrás varias cuotas juntas si hace falta, y el sistema recuerda por email antes de cada vencimiento.'],
            ['num' => '04', 'title' => 'Generás reportes',      'desc' => 'Consultás el estado del loteo, las cuotas pendientes y exportás todo a PDF o Excel cuando lo necesites.'],
        ],
        'faqs' => [
            ['q' => '¿Cómo se cargan los lotes con su ubicación real?',
             'a' => 'Podés subir el archivo KMZ de tu mensura (el mismo formato que usan las apps de mapas) y el sistema ubica cada lote automáticamente, o dibujar el polígono a mano sobre el mapa.'],
            ['q' => '¿Los compradores pueden ver sus propios lotes y cuotas?',
             'a' => 'Sí. Cada comprador tiene un usuario propio para entrar a un portal donde ve sus lotes en el mapa, sus cuotas pendientes y su historial de pagos — sin poder modificar datos.'],
            ['q' => '¿Genera recibos de pago?',
             'a' => 'Sí. Cada pago registrado genera un recibo en PDF al instante, con los datos del comprador y el detalle de la cuota cancelada.'],
            ['q' => '¿Cómo se maneja la mora?',
             'a' => 'El sistema identifica las cuotas vencidas según su fecha de vencimiento y avisa por email antes y el día del vencimiento. Podés ver el estado de cada comprador y del loteo completo.'],
            ['q' => '¿Puedo aumentar los precios de varios lotes a la vez?',
             'a' => 'Sí. Podés aplicar un aumento porcentual de precio a todos los lotes de una manzana o de todo el loteo en un solo paso, en vez de editarlos uno por uno.'],
        ],
    ],
    'tallerpro' => [
        'tagline'     => 'Gestión completa para servicio técnico y reparación de equipos',
        'description' => 'Controlá órdenes de trabajo, presupuestos, pagos y mantenimiento preventivo desde un sistema pensado para talleres y servicios técnicos que reparan y mantienen equipos — maquinaria industrial, electrodomésticos, panaderías, fábricas y comercios en general.',
        'emoji'       => '🔧',
        'accent'      => '#fbbf24',
        'accent_bg'   => 'rgba(245,158,11,.15)',
        'features'    => [
            ['path'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
              'title' => 'Órdenes de trabajo con seguimiento completo',
              'desc'  => 'Cada equipo que ingresa genera una orden con número propio. El estado avanza de recibido a diagnóstico, espera de repuestos, en reparación, listo y entregado, con un registro de quién cambió cada estado y cuándo.'],
            ['path'  => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z',
              'title' => 'Fotos de antes, durante y después',
              'desc'  => 'Documentá el estado del equipo con fotos en cada etapa del trabajo, todas adjuntas a la orden de trabajo.'],
            ['path'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
              'title' => 'Historial completo por equipo',
              'desc'  => 'Cada equipo acumula todas sus órdenes de trabajo y su plan de mantenimiento en un mismo historial, disponible al instante.'],
            ['path'  => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
              'title' => 'Presupuestos con aprobación del cliente',
              'desc'  => 'Armá presupuestos por mano de obra, repuestos u otros ítems, con descuentos y total. El cliente los aprueba o rechaza, y los descargás en PDF.'],
            ['path'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
              'title' => 'Registro de pagos',
              'desc'  => 'Registrá señas, pagos parciales o el pago total de cada trabajo, con el método utilizado: efectivo, transferencia, tarjeta u otro.'],
            ['path'  => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
              'title' => 'Mantenimiento preventivo y recordatorios',
              'desc'  => 'Definí una frecuencia de mantenimiento por equipo (3, 6, 12 meses o la que necesites) y el sistema calcula el próximo vencimiento y te avisa cuando se acerca.'],
            ['path'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
              'title' => 'Reportes y estadísticas',
              'desc'  => 'Reportes en PDF por período, por cliente o de facturación, más un panel de estadísticas del taller en tiempo real.'],
        ],
        'steps' => [
            ['num' => '01', 'title' => 'Ingresás el equipo',       'desc' => 'Registrás el cliente, el equipo y el problema reportado. El sistema genera el número de orden de trabajo automáticamente.'],
            ['num' => '02', 'title' => 'Diagnosticás y presupuestás', 'desc' => 'Cargás el diagnóstico, armás el presupuesto con mano de obra y repuestos, y el cliente lo aprueba antes de arrancar.'],
            ['num' => '03', 'title' => 'Seguís el avance',         'desc' => 'El estado de la orden avanza en cada etapa —diagnóstico, espera de repuestos, en reparación, listo— con todo el historial de cambios registrado.'],
            ['num' => '04', 'title' => 'Entregás y cobrás',        'desc' => 'Registrás la entrega y el pago, total o parcial. El equipo queda con su historial completo para la próxima visita.'],
        ],
        'faqs' => [
            ['q' => '¿Para qué tipo de talleres o servicios técnicos sirve?',
             'a' => 'Para cualquier negocio que repare o mantenga equipos: maquinaria industrial, electrodomésticos, panaderías, carnicerías, fábricas y comercios en general. No está limitado a un rubro específico.'],
            ['q' => '¿Lleva control de stock de repuestos?',
             'a' => 'No maneja stock ni existencias. Los repuestos se cargan como ítems dentro del presupuesto de cada orden de trabajo, pero el sistema no controla inventario ni alertas de reposición.'],
            ['q' => '¿Cómo se registran los pagos?',
             'a' => 'Podés registrar señas, pagos parciales o el pago total de cada orden de trabajo, con el método usado. Para facturación electrónica oficial (AFIP) te recomendamos integrarlo con tu sistema de facturación habitual.'],
            ['q' => '¿Cuántos técnicos o usuarios puedo agregar?',
             'a' => 'No hay límite de usuarios. Podés agregar todos los técnicos y empleados, cada uno con su nivel de acceso (administrador, técnico o solo lectura).'],
            ['q' => '¿Funciona desde el celular?',
             'a' => 'Sí. El sistema es responsive y funciona desde cualquier dispositivo con navegador: teléfono, tablet o computadora, sin necesidad de instalar nada.'],
        ],
    ],
    'historias-clinicas' => [
        'tagline'     => 'Historia clínica digital para consultorios y centros de salud',
        'description' => 'Gestioná pacientes, turnos, evoluciones médicas y documentación clínica desde un sistema seguro, ágil y diseñado para profesionales de la salud.',
        'emoji'       => '🏥',
        'accent'      => '#34d399',
        'accent_bg'   => 'rgba(16,185,129,.15)',
        'features'    => [
            ['path'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
              'title' => 'Historia clínica electrónica',
              'desc'  => 'Informes clínicos organizados por paciente, armados a partir de plantillas por especialidad, con diagnóstico y código CIE-10. Exportá la historia clínica completa en un solo PDF.'],
            ['path'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
              'title' => 'Agenda de turnos con recordatorios',
              'desc'  => 'Calendario de citas presenciales o virtuales, con link de videollamada. Recordatorio automático por email 24 horas antes del turno.'],
            ['path'  => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
              'title' => 'Firma digital y consentimiento informado',
              'desc'  => 'El profesional firma los informes clínicos digitalmente, con auditoría de quién y cuándo. El paciente puede firmar el consentimiento informado en el consultorio o a distancia, con un link que recibe por email.'],
            ['path'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
              'title' => 'Gestión de pacientes',
              'desc'  => 'Ficha completa por paciente: datos personales, obra social, antecedentes y estructura familiar, adaptada según el tipo de institución.'],
            ['path'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
              'title' => 'Recetas y estudios adjuntos',
              'desc'  => 'Adjuntá la receta o el estudio como archivo al informe clínico firmado, disponible para consultar o descargar cuando lo necesites.'],
            ['path'  => 'M12 5a2 2 0 100 4 2 2 0 000-4zM19 5a2 2 0 100 4 2 2 0 000-4zM5 5a2 2 0 100 4 2 2 0 000-4zM12 19a2 2 0 100-4 2 2 0 000 4zM19 19a2 2 0 100-4 2 2 0 000 4zM5 19a2 2 0 100-4 2 2 0 000 4z',
              'title' => 'Módulos especializados',
              'desc'  => 'Odontograma digital pieza por pieza para odontología, y evaluaciones de aptitud (preocupacional, periódica, de egreso) para medicina laboral — se activan según el tipo de institución.'],
            ['path'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
              'title' => 'Estadísticas de atención',
              'desc'  => 'Consultas por período, diagnósticos frecuentes y rendimiento general del consultorio o centro de salud.'],
        ],
        'steps' => [
            ['num' => '01', 'title' => 'Registrás al paciente',  'desc' => 'Cargás la ficha completa: datos personales, obra social, antecedentes y estructura familiar según el tipo de institución.'],
            ['num' => '02', 'title' => 'Gestionás los turnos',   'desc' => 'Agendás la cita presencial o virtual. El sistema envía un recordatorio automático por email 24 horas antes.'],
            ['num' => '03', 'title' => 'Abrís la consulta',      'desc' => 'Registrás la evolución y el diagnóstico en un informe con plantilla, y lo firmás digitalmente al terminar.'],
            ['num' => '04', 'title' => 'Adjuntás y firmás',      'desc' => 'Sumás la receta o el estudio como archivo al informe firmado, y gestionás el consentimiento informado presencial o a distancia.'],
        ],
        'faqs' => [
            ['q' => '¿El sistema cumple con la normativa de historia clínica electrónica?',
             'a' => 'El sistema está diseñado para cumplir con los requisitos básicos de registro médico digital. Cada informe queda firmado digitalmente con fecha, hora y profesional. Para requerimientos específicos de cada provincia o especialidad, consultanos.'],
            ['q' => '¿Cuántos médicos o especialidades puede manejar?',
             'a' => 'No hay límite de profesionales. Podés agregar médicos, especialistas y personal de administración, cada uno con su propia agenda y nivel de acceso al sistema.'],
            ['q' => '¿Cómo se manejan las recetas?',
             'a' => 'La receta se adjunta como archivo al informe clínico firmado por el profesional habilitado. No generamos la receta con validez legal electrónica oficial — para eso te recomendamos usar el sistema de recetas electrónicas vigente en tu provincia y adjuntar el resultado acá.'],
            ['q' => '¿El consentimiento informado se puede firmar sin que el paciente venga al consultorio?',
             'a' => 'Sí. Podés enviarle al paciente un link por email para que firme el consentimiento a distancia, con firma digital, sin imprimir ni venir presencialmente. También se puede firmar en el momento, en el consultorio.'],
            ['q' => '¿Tiene módulos para odontología o medicina laboral?',
             'a' => 'Sí. El sistema se adapta según el tipo de institución: odontología suma un odontograma digital por paciente, y medicina laboral suma evaluaciones de aptitud (preocupacional, periódica, de egreso).'],
        ],
    ],
    'turnos' => [
        'tagline'     => 'Reservas online y gestión de turnos para tu negocio',
        'description' => 'Dejá que tus clientes reserven turno solos, las 24 horas, y organizá la fila de espera del local con kiosco, pantalla de llamado y panel de operador. Todo desde un mismo sistema.',
        'emoji'       => '📅',
        'accent'      => '#818cf8',
        'accent_bg'   => 'rgba(129,140,248,.15)',
        'features'    => [
            ['path'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
              'title' => 'Reservas online 24/7',
              'desc'  => 'Tus clientes eligen servicio, profesional y horario disponible desde el celular, sin llamar ni escribir. Reciben confirmación al instante.'],
            ['path'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
              'title' => 'Agenda con disponibilidad automática',
              'desc'  => 'El sistema calcula los horarios libres de cada profesional según sus horarios de trabajo, bloqueos y reservas ya tomadas. Sin superposiciones.'],
            ['path'  => 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2',
              'title' => 'Turnero con kiosco y pantalla de llamado',
              'desc'  => 'Para la atención sin reserva: kiosco táctil donde el cliente saca su número, pantalla en la sala de espera que llama el turno, y panel para que el operador avance la fila.'],
            ['path'  => 'M7 7h.01M7 3h5.586a1 1 0 01.707.293l6.414 6.414a1 1 0 010 1.414l-5.586 5.586a1 1 0 01-1.414 0L6.293 10.293A1 1 0 016 9.586V4a1 1 0 011-1z',
              'title' => 'Categorías de atención',
              'desc'  => 'Clasificá los turnos como quieras — Particular, Obra Social, Urgencia — cada una con su propio color y numeración en el ticket.'],
            ['path'  => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
              'title' => 'Recordatorios automáticos',
              'desc'  => 'El sistema envía un recordatorio por email antes del turno, para reducir ausencias sin que tengas que acordarte de avisar vos.'],
            ['path'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
              'title' => 'Sucursales, servicios y equipo',
              'desc'  => 'Cargá todas tus sucursales, los servicios que ofrecés y quién los brinda — empleados, espacios o equipos — sin límite de cantidad.'],
        ],
        'steps' => [
            ['num' => '01', 'title' => 'Configurás tu agenda',   'desc' => 'Cargás sucursales, servicios y los horarios de trabajo de cada profesional o recurso.'],
            ['num' => '02', 'title' => 'Compartís el link',      'desc' => 'Tus clientes reservan solos desde ese link — sin cuenta, sin llamadas, sin ida y vuelta.'],
            ['num' => '03', 'title' => 'Gestionás la espera',    'desc' => 'Si además atendés gente sin turno, activás el kiosco y la pantalla de llamado para ordenar la fila.'],
            ['num' => '04', 'title' => 'Hacés seguimiento',      'desc' => 'Recordatorios automáticos, historial de turnos y reportes de atención, todo desde el panel.'],
        ],
        'faqs' => [
            ['q' => '¿Tengo que usar el turnero físico (kiosco y pantalla) sí o sí?',
             'a' => 'No. Reservas online y turnero son módulos independientes. Podés usar solo reservas online, solo el turnero, o los dos juntos si tu negocio combina turnos programados con atención espontánea.'],
            ['q' => '¿Cuántas sucursales, servicios o profesionales puedo cargar?',
             'a' => 'No hay límite. Podés cargar todas las sucursales, servicios y profesionales o recursos que necesites.'],
            ['q' => '¿Los clientes necesitan crear una cuenta para reservar?',
             'a' => 'No. Reservan con nombre, email y teléfono en el momento, sin registrarse ni instalar nada. Después pueden ver o cancelar su turno con el link que reciben.'],
            ['q' => '¿Para qué tipo de negocios sirve?',
             'a' => 'Para cualquier negocio que atienda con turnos: peluquerías y centros de estética, consultorios, talleres, oficinas de trámites, gimnasios con clases, y organismos que además manejan fila física en el local.'],
            ['q' => '¿Puedo elegir cómo se llaman las categorías del turnero?',
             'a' => 'Sí. Vos definís las categorías (Particular, Obra Social, VIP, lo que necesites), con su prefijo de numeración y color propio.'],
        ],
    ],
];

$c      = $content[$product->slug] ?? null;
$accent = $c['accent']    ?? '#3b82f6';
$acBg   = $c['accent_bg'] ?? 'rgba(59,130,246,.15)';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} — Arioli.dev</title>
    <meta name="description" content="{{ $c['tagline'] ?? $product->description }} · Sistema SaaS en la nube, precios en pesos, soporte incluido.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --font-sans: 'DM Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;
            --bg:  #080d1a;
            --bg2: #0d1426;
            --card: #111827;
            --card-border: #1e2d45;
            --accent:  {{ $accent }};
            --accent2: #6366f1;
            --accent-bg: {{ $acBg }};
            --text:  #f1f5f9;
            --text2: #94a3b8;
            --text3: #475569;
            --success: #10b981;
            --radius: 16px;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-sans);
            background: var(--bg); color: var(--text);
            font-size: 15px; line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Nav ── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0;
            z-index: 100; padding: 18px 0; transition: all .3s;
        }
        .nav.scrolled {
            background: rgba(8,13,26,.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 12px 0;
        }
        .nav-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 32px;
            display: flex; align-items: center;
            justify-content: space-between; gap: 24px;
        }
        .logo {
            display: flex; align-items: center;
            gap: 10px; text-decoration: none; flex-shrink: 0;
        }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text { font-size: 18px; font-weight: 700; color: var(--text); letter-spacing: -.02em; }
        .logo-text span { color: #3b82f6; }
        .nav-links {
            display: flex; align-items: center;
            gap: 28px; list-style: none; flex: 1;
        }
        .nav-links a { color: var(--text2); text-decoration: none; font-size: 14px; font-weight: 500; transition: color .2s; }
        .nav-links a:hover { color: var(--text); }
        .nav-cta { display: flex; align-items: center; gap: 10px; }
        .btn-nav {
            padding: 9px 18px; border-radius: 9px;
            font-size: 14px; font-weight: 600; font-family: var(--font-sans);
            text-decoration: none; transition: all .2s; cursor: pointer; border: none;
        }
        .btn-outline { background: transparent; color: var(--text2); border: 1.5px solid var(--card-border); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }
        .btn-accent { background: var(--accent); color: #fff; }
        .btn-accent:hover { opacity: .88; transform: translateY(-1px); }
        .nav-hamburger { display: none; background: none; border: none; color: var(--text2); cursor: pointer; padding: 4px; }
        .nav-mobile-menu { display: none; background: rgba(8,13,26,.97); border-top: 1px solid var(--card-border); padding: 20px 32px 24px; }
        .nav-mobile-menu a { display: block; padding: 12px 0; color: var(--text2); text-decoration: none; font-size: 15px; font-weight: 500; border-bottom: 1px solid var(--card-border); transition: color .2s; }
        .nav-mobile-menu a:last-child { border-bottom: none; }
        .nav-mobile-menu a:hover { color: var(--text); }

        /* ── Hero ── */
        .hero {
            min-height: 90vh;
            display: flex; align-items: center;
            padding: 140px 32px 80px;
            position: relative; overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 30% 30%, rgba(59,130,246,.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 70%, {{ $acBg }} 0%, transparent 50%);
        }
        .hero-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .hero-content { position: relative; max-width: 700px; }
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: var(--text3); margin-bottom: 28px;
        }
        .breadcrumb a { color: var(--text3); text-decoration: none; transition: color .2s; }
        .breadcrumb a:hover { color: var(--text2); }
        .breadcrumb svg { color: var(--text3); }
        .hero-icon {
            width: 72px; height: 72px; border-radius: 20px;
            background: var(--accent-bg);
            border: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; margin-bottom: 24px;
        }
        .hero h1 {
            font-size: clamp(32px, 5vw, 56px); font-weight: 800;
            line-height: 1.1; letter-spacing: -.03em;
            color: var(--text); margin-bottom: 14px;
        }
        .hero-tagline {
            font-size: 18px; font-weight: 500;
            color: var(--accent); margin-bottom: 20px;
        }
        .hero p {
            font-size: 17px; color: var(--text2);
            max-width: 560px; line-height: 1.7; margin-bottom: 36px;
        }
        .hero-actions { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .btn-hero-primary {
            padding: 14px 32px; border-radius: 12px;
            font-size: 15px; font-weight: 700;
            background: var(--accent); color: #fff; text-decoration: none;
            box-shadow: 0 8px 32px rgba(0,0,0,.25);
            transition: all .2s; border: none; cursor: pointer;
            font-family: var(--font-sans);
        }
        .btn-hero-primary:hover { opacity: .88; transform: translateY(-2px); }
        .btn-hero-secondary {
            padding: 14px 32px; border-radius: 12px;
            font-size: 15px; font-weight: 600;
            background: transparent; color: var(--text2);
            text-decoration: none; border: 1.5px solid var(--card-border);
            transition: all .2s; font-family: var(--font-sans);
        }
        .btn-hero-secondary:hover { border-color: var(--accent); color: var(--text); }

        /* ── Sections ── */
        .section { padding: 100px 32px; max-width: 1200px; margin: 0 auto; }
        .section-label {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 12px; font-weight: 600; text-transform: uppercase;
            letter-spacing: .1em; color: var(--accent); margin-bottom: 16px;
        }
        .section-title {
            font-size: clamp(26px, 4vw, 40px); font-weight: 800;
            letter-spacing: -.02em; color: var(--text);
            margin-bottom: 16px; line-height: 1.2;
        }
        .section-sub { font-size: 17px; color: var(--text2); max-width: 560px; line-height: 1.7; }
        .alt-bg { background: var(--bg2); padding: 1px 0; }

        /* ── Features grid ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px; margin-top: 56px;
        }
        .feature-card {
            background: var(--card); border: 1px solid var(--card-border);
            border-radius: 14px; padding: 28px; transition: border-color .2s;
        }
        .feature-card:hover { border-color: var(--accent); }
        .feature-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--accent-bg); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .feature-title { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
        .feature-desc { font-size: 14px; color: var(--text2); line-height: 1.65; }

        /* ── Steps ── */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px; margin-top: 56px;
        }
        .step {
            background: var(--card); border: 1px solid var(--card-border);
            border-radius: var(--radius); padding: 32px; text-align: center;
        }
        .step-num {
            display: inline-block; font-family: var(--font-mono);
            font-size: 13px; font-weight: 500; color: var(--accent);
            background: var(--accent-bg); border: 1px solid rgba(255,255,255,.1);
            padding: 4px 12px; border-radius: 8px;
            margin-bottom: 16px; letter-spacing: .05em;
        }
        .step h3 { font-size: 17px; font-weight: 700; color: var(--text); margin-bottom: 10px; }
        .step p { font-size: 14px; color: var(--text2); line-height: 1.65; }

        /* ── Pricing ── */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px; margin-top: 56px;
        }
        .pricing-card {
            background: var(--card); border: 1px solid var(--card-border);
            border-radius: 14px; padding: 28px;
            position: relative; transition: all .2s;
        }
        .pricing-card:hover { transform: translateY(-3px); box-shadow: 0 16px 48px rgba(0,0,0,.3); }
        .pricing-card.best {
            border-color: var(--accent);
            background: linear-gradient(135deg, rgba(59,130,246,.05), var(--card));
        }
        .pricing-best-badge {
            position: absolute; top: -1px; right: 20px;
            background: var(--accent); color: #fff;
            font-size: 11px; font-weight: 700;
            padding: 4px 10px; border-radius: 0 0 8px 8px;
            letter-spacing: .04em;
        }
        .pricing-period { font-size: 13px; font-weight: 600; color: var(--text2); margin-bottom: 8px; }
        .pricing-price { font-size: 32px; font-weight: 800; color: var(--text); letter-spacing: -.02em; line-height: 1; }
        .pricing-detail { font-size: 12px; color: var(--text3); margin-top: 4px; margin-bottom: 20px; }
        .pricing-detail .discount { color: var(--success); }
        .btn-pricing {
            display: block; text-align: center; padding: 11px;
            border-radius: 9px; font-size: 14px; font-weight: 600;
            text-decoration: none; transition: all .2s;
            font-family: var(--font-sans); border: none; cursor: pointer;
        }
        .btn-pricing.primary {
            background: var(--accent); color: #fff;
        }
        .btn-pricing.primary:hover { opacity: .88; }
        .btn-pricing.secondary {
            background: rgba(255,255,255,.06); color: var(--text2);
            border: 1px solid var(--card-border);
        }
        .btn-pricing.secondary:hover { border-color: var(--accent); color: var(--text); }

        /* ── Form ── */
        .form-group { margin-bottom: 18px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--text2); margin-bottom: 7px; }
        .form-input, .form-textarea {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,.05);
            border: 1.5px solid var(--card-border); border-radius: 10px;
            font-size: 14px; font-family: var(--font-sans);
            color: var(--text); outline: none; transition: all .2s;
        }
        select.form-input { cursor: pointer; }
        .form-input option { background: var(--card); color: var(--text); }
        .form-textarea { resize: vertical; min-height: 110px; }
        .form-input:focus, .form-textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
        .form-input::placeholder, .form-textarea::placeholder { color: #475569; }
        .form-error { font-size: 12px; color: #f87171; margin-top: 5px; }
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: var(--font-sans); border: none;
            border-radius: 12px; cursor: pointer;
            box-shadow: 0 6px 24px rgba(59,130,246,.3);
            transition: all .2s;
        }
        .btn-submit:hover { transform: translateY(-1px); }
        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); border-radius: 10px; padding: 14px 16px; margin-bottom: 22px; font-size: 13.5px; color: #34d399; }
        .alert-error { background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.3); border-radius: 10px; padding: 14px 16px; margin-bottom: 22px; font-size: 13.5px; color: #f87171; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }

        /* ── FAQ ── */
        .faq-list { margin-top: 48px; max-width: 800px; }
        .faq-item { border-bottom: 1px solid var(--card-border); }
        .faq-btn {
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            width: 100%; background: none; border: none;
            padding: 22px 0; cursor: pointer; text-align: left;
            font-family: var(--font-sans); transition: color .2s;
        }
        .faq-q-text { font-size: 16px; font-weight: 600; color: var(--text2); transition: color .2s; flex: 1; }
        .faq-btn:hover .faq-q-text { color: var(--text); }
        .faq-icon {
            flex-shrink: 0; width: 28px; height: 28px; border-radius: 8px;
            background: var(--card); border: 1px solid var(--card-border);
            display: flex; align-items: center; justify-content: center;
            color: var(--text3); font-size: 16px; transition: all .2s;
        }
        .faq-item.open .faq-icon { background: var(--accent-bg); border-color: var(--accent); color: var(--accent); }
        .faq-item.open .faq-q-text { color: var(--text); }
        .faq-answer { font-size: 14.5px; color: var(--text2); line-height: 1.75; padding-bottom: 22px; display: none; }
        .faq-item.open .faq-answer { display: block; }

        /* ── CTA ── */
        .cta-wrap { max-width: 1200px; margin: 0 auto; padding: 0 32px 100px; }
        .cta-section {
            background: linear-gradient(135deg, var(--accent-bg), rgba(99,102,241,.08));
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 24px; padding: 80px 48px;
            text-align: center; position: relative; overflow: hidden;
        }
        .cta-title { font-size: clamp(26px, 4vw, 40px); font-weight: 800; color: var(--text); letter-spacing: -.02em; margin-bottom: 14px; line-height: 1.2; }
        .cta-sub { font-size: 17px; color: var(--text2); margin-bottom: 36px; }

        /* ── Footer ── */
        .footer { border-top: 1px solid var(--card-border); padding: 60px 32px 40px; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; }
        .footer-brand p { font-size: 13px; color: var(--text3); margin-top: 12px; line-height: 1.65; max-width: 240px; }
        .footer-col h4 { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text3); margin-bottom: 16px; }
        .footer-col a { display: block; font-size: 14px; color: var(--text3); text-decoration: none; margin-bottom: 10px; transition: color .2s; }
        .footer-col a:hover { color: var(--text2); }
        .footer-bottom { max-width: 1200px; margin: 32px auto 0; padding-top: 24px; border-top: 1px solid var(--card-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .footer-bottom p { font-size: 13px; color: var(--text3); }

        /* ── Responsive ── */
        @media (max-width: 900px) { .footer-inner { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 768px) {
            .nav-links, .nav-cta { display: none; }
            .nav-hamburger { display: block; }
            .hero { padding: 120px 20px 60px; }
            .section { padding: 60px 20px; }
            .cta-section { padding: 48px 24px; }
            .cta-wrap { padding: 0 16px 60px; }
            .footer { padding: 40px 20px 32px; }
            .footer-inner { grid-template-columns: 1fr; gap: 28px; }
            .features-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .pricing-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
        @media (max-width: 480px) { .pricing-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

{{-- Nav --}}
<nav class="nav" id="navbar">
    <div class="nav-inner">
        <a href="{{ route('landing.home') }}" class="logo">
            <div class="logo-icon">
                <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="logo-text">Arioli<span>.dev</span></span>
        </a>

        <ul class="nav-links">
            <li><a href="{{ route('landing.home') }}#productos">Otros sistemas</a></li>
            <li><a href="#caracteristicas">Características</a></li>
            <li><a href="#precios">Precios</a></li>
            <li><a href="#faq">Preguntas frecuentes</a></li>
        </ul>

        <div class="nav-cta">
            <a href="{{ $demoUrl }}" target="_blank" class="btn-nav btn-outline">Demo gratis</a>
            <a href="#precios" class="btn-nav btn-accent">Contratar ahora</a>
        </div>

        <button class="nav-hamburger" id="navHamburger" aria-label="Menú">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <div class="nav-mobile-menu" id="mobileMenu">
        <a href="{{ route('landing.home') }}">← Inicio</a>
        <a href="#caracteristicas" onclick="closeMenu()">Características</a>
        <a href="#precios" onclick="closeMenu()">Precios</a>
        <a href="#faq" onclick="closeMenu()">Preguntas frecuentes</a>
        <a href="{{ $demoUrl }}" target="_blank" style="color:var(--accent); margin-top:4px;">Demo gratis →</a>
    </div>
</nav>

{{-- Hero --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-content">
        <div class="breadcrumb">
            <a href="{{ route('landing.home') }}">Inicio</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('landing.home') }}#productos">Productos</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span>{{ $product->name }}</span>
        </div>

        <div class="hero-icon">{{ $c['emoji'] ?? '💼' }}</div>

        <h1>{{ $product->name }}</h1>
        <div class="hero-tagline">{{ $c['tagline'] ?? '' }}</div>
        <p>{{ $c['description'] ?? $product->description }}</p>

        <div class="hero-actions">
            <a href="{{ $demoUrl }}" target="_blank" class="btn-hero-primary">
                Probar demo gratis →
            </a>
            <a href="#precios" class="btn-hero-secondary">Ver precios</a>
        </div>
    </div>
</section>

{{-- Características --}}
<div class="alt-bg" id="caracteristicas">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Funcionalidades
        </div>
        <h2 class="section-title">Todo lo que necesitás para operar mejor</h2>
        <p class="section-sub">
            Funcionalidades diseñadas específicamente para {{ strtolower($product->name) }}.
            Sin funciones de relleno, solo lo que realmente usás.
        </p>

        <div class="features-grid">
            @foreach($c['features'] ?? [] as $f)
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['path'] }}"/>
                        </svg>
                    </div>
                    <div class="feature-title">{{ $f['title'] }}</div>
                    <div class="feature-desc">{{ $f['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Cómo funciona --}}
<section id="como-funciona">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Flujo de trabajo
        </div>
        <h2 class="section-title">Cómo funciona en la práctica</h2>
        <p class="section-sub">
            Cuatro pasos simples para operar con {{ $product->name }} todos los días.
        </p>

        <div class="steps-grid">
            @foreach($c['steps'] ?? [] as $step)
                <div class="step">
                    <div class="step-num">{{ $step['num'] }}</div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Precios --}}
<div class="alt-bg" id="precios">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Precios claros
        </div>
        <h2 class="section-title">Sin costos ocultos</h2>
        <p class="section-sub">
            Elegí el período que mejor se adapta a tu negocio.
            Más tiempo de contratación, mayor descuento.
        </p>

        <div style="display:flex; align-items:flex-start; gap:12px; max-width:640px; margin-bottom:36px; padding:16px 18px; background:rgba(255,255,255,.03); border:1px solid var(--card-border); border-radius:12px;">
            <svg width="18" height="18" fill="none" stroke="var(--accent)" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size:13.5px; color:var(--text2); line-height:1.55;">
                Los precios de esta sección son de una <strong style="color:var(--text);">licencia</strong>: pagás por mes o período,
                incluye actualizaciones y soporte, y podés dar de baja cuando quieras.
                Si preferís pagar una sola vez y quedarte con el sistema sin pagos mensuales,
                <a href="#compra-completa" style="color:var(--accent); font-weight:600;">mirá la opción de comprarlo completo →</a>
            </p>
        </div>

        <div class="pricing-grid">
            @foreach($plans as $plan)
                @php $isAnnual = $plan->period === 'annual'; @endphp
                <div class="pricing-card {{ $isAnnual ? 'best' : '' }}">
                    @if($isAnnual)
                        <div class="pricing-best-badge">MEJOR PRECIO</div>
                    @endif
                    <div class="pricing-period">{{ $plan->period_label }}</div>
                    <div class="pricing-price">${{ number_format($plan->price, 0, ',', '.') }}</div>
                    <div class="pricing-detail">
                        {{ $plan->period_months }} {{ $plan->period_months === 1 ? 'mes' : 'meses' }} de acceso
                        @if($plan->discount_percent > 0)
                            · <span class="discount">−{{ $plan->discount_percent }}% descuento</span>
                        @endif
                    </div>
                    <a href="{{ route('checkout.show', $plan->id) }}"
                       class="btn-pricing {{ $isAnnual ? 'primary' : 'secondary' }}">
                        Contratar ahora
                    </a>
                </div>
            @endforeach
        </div>

        <div style="margin-top:32px; display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text3);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Sin permanencia mínima
            </div>
            <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text3);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Soporte incluido
            </div>
            <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text3);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Activación inmediata
            </div>
            <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text3);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Precios en pesos argentinos
            </div>
        </div>
    </div>
</div>

{{-- Compra del sistema completo, sin licencia --}}
<div id="compra-completa">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Otra opción
        </div>
        <h2 class="section-title">¿Preferís comprar el sistema completo?</h2>
        <p class="section-sub">
            También ofrecemos {{ $product->name }} como compra única, sin licencia ni suscripción mensual.
        </p>

        <div class="feature-card" style="max-width:720px; margin-top:40px; padding:36px;">
            <ul style="list-style:none; display:flex; flex-direction:column; gap:14px; margin-bottom:28px;">
                <li style="display:flex; align-items:flex-start; gap:10px; font-size:14.5px; color:var(--text2);">
                    <svg width="18" height="18" fill="none" stroke="var(--accent)" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Compra única del sistema completo, sin licencia ni pago mensual.
                </li>
                <li style="display:flex; align-items:flex-start; gap:10px; font-size:14.5px; color:var(--text2);">
                    <svg width="18" height="18" fill="none" stroke="var(--text3)" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    No incluye mantenimiento ni soporte — tienen costo aparte.
                </li>
                <li style="display:flex; align-items:flex-start; gap:10px; font-size:14.5px; color:var(--text2);">
                    <svg width="18" height="18" fill="none" stroke="var(--text3)" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    La instalación, el dominio y el hosting van por separado.
                </li>
            </ul>

            <a href="https://wa.me/5493435433577?text={{ urlencode('Hola! Quiero información sobre ' . $product->name . '.') }}"
               target="_blank" class="btn-hero-primary" style="display:inline-flex; align-items:center; gap:8px; margin-bottom:32px;">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2zm0 18.13c-1.48 0-2.93-.4-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 01-1.26-4.36c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 012.42 5.83c0 4.55-3.7 8.21-8.26 8.21zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.17.24-.64.8-.78.97-.14.17-.29.19-.53.06-.25-.12-1.04-.38-1.99-1.22-.73-.66-1.23-1.46-1.37-1.71-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.16.04-.31-.02-.43-.06-.13-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.43-.14-.01-.31-.01-.48-.01-.17 0-.43.06-.66.31-.23.24-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.57.12.16 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.53.59.19 1.13.16 1.55.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.07-.1-.23-.16-.48-.28z"/></svg>
                Escribinos por WhatsApp
            </a>

            <div style="border-top:1px solid var(--card-border); padding-top:28px;">
                <p style="font-size:13.5px; color:var(--text3); margin-bottom:20px;">
                    O dejanos tus datos y te contactamos nosotros — indicá si te interesa una licencia SaaS o el sistema completo.
                </p>

                @if(session('status'))
                    <div class="alert-success">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('landing.product.inquiry', $product->slug) }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Tu nombre" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="tu@email.com" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Teléfono (opcional)</label>
                            <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+54 9 ...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Me interesa</label>
                            <select name="inquiry_type" class="form-input" required>
                                <option value="licencia" {{ old('inquiry_type') === 'licencia' ? 'selected' : '' }}>Licencia SaaS (planes de la página)</option>
                                <option value="completo" {{ old('inquiry_type') === 'completo' ? 'selected' : '' }}>Sistema completo, sin licencia</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Consulta</label>
                        <textarea name="message" class="form-textarea" placeholder="Contanos qué necesitás" required>{{ old('message') }}</textarea>
                    </div>

                    @if(config('services.turnstile.sitekey'))
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                        <div style="display:flex; justify-content:center; margin-bottom:18px;">
                            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.sitekey') }}" data-theme="dark"></div>
                        </div>
                    @endif

                    <button type="submit" class="btn-submit">Enviar consulta</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- FAQ del producto --}}
<section id="faq">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Preguntas frecuentes
        </div>
        <h2 class="section-title">Dudas sobre {{ $product->name }}</h2>
        <p class="section-sub">Las preguntas más comunes sobre el sistema.</p>

        <div class="faq-list" id="faqList">
            @foreach($c['faqs'] ?? [] as $i => $faq)
                <div class="faq-item" data-index="{{ $i }}">
                    <button class="faq-btn" onclick="toggleFaq({{ $i }})">
                        <span class="faq-q-text">{{ $faq['q'] }}</span>
                        <span class="faq-icon" id="faqIcon{{ $i }}">+</span>
                    </button>
                    <div class="faq-answer" id="faqAnswer{{ $i }}">{{ $faq['a'] }}</div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:40px;">
            <p style="font-size:14px; color:var(--text3);">
                ¿Tenés más preguntas?
                <a href="{{ route('landing.contact') }}" style="color:var(--accent); text-decoration:none; font-weight:600;">
                    Escribinos por el formulario de contacto
                </a>
            </p>
        </div>
    </div>
</section>

{{-- CTA final --}}
<div class="cta-wrap">
    <div class="cta-section">
        <div style="position:relative;">
            <h2 class="cta-title">¿Querés probarlo antes de contratar?</h2>
            <p class="cta-sub">
                La demo es gratuita, sin registro y sin tarjeta de crédito.<br>
                Explorá {{ $product->name }} con datos de ejemplo reales.
            </p>
            <div style="display:flex; align-items:center; justify-content:center; gap:14px; flex-wrap:wrap;">
                <a href="{{ $demoUrl }}" target="_blank" class="btn-hero-primary">Abrir demo gratis →</a>
                <a href="#precios" class="btn-hero-secondary">Ver precios</a>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="{{ route('landing.home') }}" class="logo">
                <div class="logo-icon">
                    <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="logo-text" style="margin-left:10px;">Arioli<span>.dev</span></span>
            </a>
            <p>Sistemas de gestión SaaS para empresas argentinas. En la nube, con soporte real y precios en pesos.</p>
        </div>

        <div class="footer-col">
            <h4>Sistemas</h4>
            <a href="{{ route('landing.product', 'loteos') }}">Loteos</a>
            <a href="{{ route('landing.product', 'tallerpro') }}">Servis — Talleres</a>
            <a href="{{ route('landing.product', 'historias-clinicas') }}">Clínica — Historias</a>
        </div>

        <div class="footer-col">
            <h4>Plataforma</h4>
            <a href="{{ route('landing.home') }}#como-funciona">Cómo funciona</a>
            <a href="{{ route('landing.home') }}#caracteristicas">Características</a>
            <a href="{{ route('landing.home') }}#faq">Preguntas frecuentes</a>
        </div>

        <div class="footer-col">
            <h4>Clientes</h4>
            <a href="https://{{ config('app.cliente_domain') }}">Panel del cliente</a>
            <a href="{{ route('landing.contact') }}">Contacto</a>
            <a href="{{ route('landing.partner') }}">Ser partner</a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} Arioli.dev — Todos los derechos reservados.</p>
        <p>Sistemas de gestión SaaS · Argentina</p>
    </div>
</footer>

<script>
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
});

document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});

document.getElementById('navHamburger').addEventListener('click', () => {
    const menu = document.getElementById('mobileMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});
function closeMenu() { document.getElementById('mobileMenu').style.display = 'none'; }

let openFaq = null;
function toggleFaq(i) {
    const item   = document.querySelector(`.faq-item[data-index="${i}"]`);
    const answer = document.getElementById(`faqAnswer${i}`);
    const icon   = document.getElementById(`faqIcon${i}`);
    if (openFaq !== null && openFaq !== i) {
        document.querySelector(`.faq-item[data-index="${openFaq}"]`).classList.remove('open');
        document.getElementById(`faqAnswer${openFaq}`).style.display = 'none';
        document.getElementById(`faqIcon${openFaq}`).textContent = '+';
    }
    const isOpen = item.classList.contains('open');
    item.classList.toggle('open', !isOpen);
    answer.style.display = isOpen ? 'none' : 'block';
    icon.textContent = isOpen ? '+' : '−';
    openFaq = isOpen ? null : i;
}
</script>

</body>
</html>
