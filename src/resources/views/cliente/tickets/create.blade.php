<x-cliente-layout title="Crear Ticket de Soporte">

    <div class="page-header">
        <div>
            <h1 class="page-title">Crear Ticket de Soporte</h1>
            <p class="page-subtitle">¿Tenés un problema o consulta? Te ayudamos a resolverlo</p>
        </div>
        <a href="{{ route('cliente.tickets.index') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Mis Tickets
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div>
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul style="margin: 8px 0 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body" style="padding: 32px;">
            <form method="POST" action="{{ route('cliente.tickets.store') }}">
                @csrf

                <div style="margin-bottom: 28px;">
                    <label for="title" class="form-label">¿Cuál es tu problema o consulta? *</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title') }}"
                           placeholder="Por ejemplo: No puedo acceder al sistema, Error al guardar datos, etc."
                           class="form-input" required maxlength="255">
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                        Escribí un título claro y descriptivo para tu problema o consulta.
                    </div>
                </div>

                <div style="margin-bottom: 28px;">
                    <label for="description" class="form-label">Descripción detallada *</label>
                    <textarea name="description" id="description" rows="8"
                              class="form-input"
                              placeholder="Contanos en detalle qué está pasando. Por ejemplo:&#10;&#10;- ¿Qué estabas intentando hacer?&#10;- ¿Qué error aparece (si hay alguno)?&#10;- ¿Cuándo empezó a pasar esto?&#10;- ¿Hay alguna captura de pantalla que puedas compartir?"
                              required>{{ old('description') }}</textarea>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                        Cuanto más detalle nos brindes, más rápido podremos ayudarte. No olvides mencionar si hay mensajes de error específicos.
                    </div>
                </div>

                @if(! empty($relatedOptions))
                    <div style="margin-bottom: 28px;">
                        <label for="related" class="form-label">Servicio relacionado (opcional)</label>
                        <select name="related" id="related" class="form-select">
                            <option value="">— No es sobre un servicio en particular —</option>
                            @foreach($relatedOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('related', $preselected ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                            Si tu consulta es sobre un hosting, dominio o servicio puntual, elegilo acá para que soporte lo identifique más rápido.
                        </div>
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
                    <div>
                        <label for="priority" class="form-label">¿Qué tan urgente es? *</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="baja" {{ old('priority', 'media') === 'baja' ? 'selected' : '' }}>
                                🟢 Baja - No es urgente, puede esperar
                            </option>
                            <option value="media" {{ old('priority', 'media') === 'media' ? 'selected' : '' }}>
                                🟡 Media - Es importante pero no bloquea mi trabajo
                            </option>
                            <option value="alta" {{ old('priority') === 'alta' ? 'selected' : '' }}>
                                🟠 Alta - Necesito resolverlo pronto
                            </option>
                            <option value="critica" {{ old('priority') === 'critica' ? 'selected' : '' }}>
                                🔴 Crítica - Urgente, no puedo trabajar
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="category" class="form-label">¿Qué tipo de problema es? *</label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="tecnico" {{ old('category', 'tecnico') === 'tecnico' ? 'selected' : '' }}>
                                🔧 Problema Técnico
                            </option>
                            <option value="configuracion" {{ old('category') === 'configuracion' ? 'selected' : '' }}>
                                ⚙️ Configuración o Setup
                            </option>
                            <option value="facturacion" {{ old('category') === 'facturacion' ? 'selected' : '' }}>
                                💰 Facturación o Pagos
                            </option>
                            <option value="otro" {{ old('category') === 'otro' ? 'selected' : '' }}>
                                📝 Consulta General
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Información útil -->
                <div style="background: var(--body-bg); border-left: 4px solid var(--accent); padding: 20px; border-radius: 0 8px 8px 0; margin-bottom: 32px;">
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">
                        💡 Para que te podamos ayudar mejor:
                    </h4>
                    <ul style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; margin: 0; padding-left: 20px;">
                        <li>Incluí capturas de pantalla si hay mensajes de error</li>
                        <li>Menciona en qué navegador o dispositivo estás teniendo el problema</li>
                        <li>Si es un problema recurrente, contanos cuándo empezó</li>
                        <li>Describí los pasos que seguiste antes de que apareciera el problema</li>
                    </ul>
                </div>

                <div style="display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--card-border);">
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Enviar Ticket
                    </button>
                    <a href="{{ route('cliente.tickets.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>

                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--card-border); font-size: 12px; color: var(--text-muted);">
                    <strong>🕒 Tiempo de respuesta:</strong> Nuestro equipo de soporte revisará tu ticket y te responderá en un plazo máximo de 24 horas durante días hábiles.
                </div>
            </form>
        </div>
    </div>

</x-cliente-layout>