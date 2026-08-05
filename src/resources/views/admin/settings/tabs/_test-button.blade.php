{{-- Botón genérico "Probar conexión" — reutilizado por las pestañas que tienen driver. --}}
<div style="margin-top:20px; padding-top:20px; border-top:1px solid #f3f4f6;">
    <button type="button" class="btn btn-secondary" onclick="testConnection('{{ $group }}', this)">
        Probar conexión
    </button>
    <span id="test-result-{{ $group }}" style="margin-left:10px; font-size:13px;"></span>
</div>

<script>
function testConnection(group, btn) {
    const resultEl = document.getElementById('test-result-' + group);
    btn.disabled = true;
    resultEl.textContent = 'Probando...';
    resultEl.style.color = 'var(--text-muted)';

    fetch(`{{ url('configuracion') }}/${group}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
        .then(r => r.json())
        .then(data => {
            resultEl.textContent = data.message;
            resultEl.style.color = data.success ? '#059669' : '#dc2626';
        })
        .catch(() => {
            resultEl.textContent = 'Error inesperado al probar la conexión.';
            resultEl.style.color = '#dc2626';
        })
        .finally(() => { btn.disabled = false; });
}
</script>
