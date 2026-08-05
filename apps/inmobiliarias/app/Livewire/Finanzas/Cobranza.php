<?php

namespace App\Livewire\Finanzas;

use App\Models\Cuota;
use App\Models\Pago;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Cobranza')]
class Cobranza extends Component
{
    use WithPagination;

    public string $filtroEstado = 'pendiente';

    public bool $modalAbierto = false;

    public ?int $cuota_id = null;

    public ?string $cuota_referencia = null;

    public ?string $monto = null;

    public string $fecha = '';

    public string $medio_pago = 'efectivo';

    public function mount(): void
    {
        // Cuota::viewAny es amplio (también lo usa el agente para ver sus
        // propias cuotas en el detalle de una Operación) — esta pantalla
        // en cambio lista TODAS las cuotas del tenant sin filtrar por
        // dueño, así que acá el corte es por rol financiero, no por Policy.
        abort_unless(
            auth()->user()->hasRole('admin') || auth()->user()->hasRole('administrativo') || auth()->user()->hasRole('solo-lectura'),
            403
        );

        $this->fecha = now()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'cuota_id' => ['required', 'exists:cuotas,id'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'fecha' => ['required', 'date'],
            'medio_pago' => ['required', Rule::in(['efectivo', 'transferencia', 'cheque', 'tarjeta', 'otro'])],
        ];
    }

    public function registrarPago(int $cuotaId): void
    {
        $this->authorize('create', Pago::class);
        $cuota = Cuota::findOrFail($cuotaId);

        $this->cuota_id = $cuota->id;
        $this->cuota_referencia = $cuota->operacion->propiedad->titulo.' — cuota #'.$cuota->numero;
        $this->monto = bcsub((string) $cuota->monto, $cuota->montoPagado(), 2);
        $this->fecha = now()->toDateString();
        $this->medio_pago = 'efectivo';
        $this->modalAbierto = true;
    }

    public function guardar(): void
    {
        $this->authorize('create', Pago::class);

        $datos = $this->validate();
        $cuota = Cuota::findOrFail($datos['cuota_id']);

        $cuota->registrarPago([
            'monto' => $datos['monto'],
            'fecha' => $datos['fecha'],
            'medio_pago' => $datos['medio_pago'],
            'registrado_por_id' => auth()->id(),
        ]);

        $this->modalAbierto = false;
        $this->reset(['cuota_id', 'cuota_referencia', 'monto']);
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $cuotas = Cuota::query()
            ->with(['operacion.propiedad', 'operacion.agente'])
            ->when($this->filtroEstado !== '', fn ($q) => $q->where('estado', $this->filtroEstado))
            ->orderBy('fecha_vencimiento')
            ->paginate(15);

        return view('livewire.finanzas.cobranza', compact('cuotas'));
    }
}
