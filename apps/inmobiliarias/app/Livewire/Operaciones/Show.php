<?php

namespace App\Livewire\Operaciones;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Operacion;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Operacion $operacion;

    public bool $modalParteAbierto = false;

    public ?int $parte_cliente_id = null;

    public string $parte_rol = 'comprador';

    public bool $modalPlanAbierto = false;

    public int $cantidad_cuotas = 1;

    public string $fecha_primer_vencimiento = '';

    public ?string $monto_por_cuota = null;

    public bool $modalContratoAbierto = false;

    public string $contrato_fecha_inicio = '';

    public ?string $contrato_fecha_fin = null;

    public function mount(Operacion $operacion): void
    {
        $this->authorize('view', $operacion);

        $this->operacion = $operacion;
        $this->fecha_primer_vencimiento = now()->toDateString();
        $this->contrato_fecha_inicio = now()->toDateString();
    }

    public function title(): string
    {
        return 'Operación — '.$this->operacion->propiedad->titulo;
    }

    public function abrirModalParte(): void
    {
        $this->authorize('update', $this->operacion);
        $this->modalParteAbierto = true;
    }

    public function asignarParte(): void
    {
        $this->authorize('update', $this->operacion);

        $datos = $this->validate([
            'parte_cliente_id' => ['required', 'exists:clientes,id'],
            'parte_rol' => ['required', Rule::in(['comprador', 'vendedor', 'locador', 'locatario', 'garante'])],
        ]);

        $this->operacion->asignarParte(Cliente::findOrFail($datos['parte_cliente_id']), $datos['parte_rol']);

        $this->modalParteAbierto = false;
        $this->reset(['parte_cliente_id']);
        $this->operacion->load('partes');
    }

    public function abrirModalPlan(): void
    {
        $this->authorize('update', $this->operacion);
        $this->modalPlanAbierto = true;
    }

    public function generarPlan(): void
    {
        $this->authorize('update', $this->operacion);

        $datos = $this->validate([
            'cantidad_cuotas' => ['required', 'integer', 'min:1', 'max:360'],
            'fecha_primer_vencimiento' => ['required', 'date'],
            'monto_por_cuota' => ['required', 'numeric', 'min:0.01'],
        ]);

        $this->operacion->generarPlanDeCuotas(
            $datos['cantidad_cuotas'],
            $datos['fecha_primer_vencimiento'],
            (string) $datos['monto_por_cuota'],
        );

        $this->modalPlanAbierto = false;
        $this->reset(['monto_por_cuota']);
        $this->operacion->load('cuotas.pagos');
    }

    public function abrirModalContrato(): void
    {
        $this->authorize('update', $this->operacion);
        $this->modalContratoAbierto = true;
    }

    public function crearContrato(): void
    {
        $this->authorize('update', $this->operacion);

        $datos = $this->validate([
            'contrato_fecha_inicio' => ['required', 'date'],
            'contrato_fecha_fin' => ['nullable', 'date', 'after:contrato_fecha_inicio'],
        ]);

        Contrato::create([
            'operacion_id' => $this->operacion->id,
            'fecha_inicio' => $datos['contrato_fecha_inicio'],
            'fecha_fin' => $datos['contrato_fecha_fin'],
        ]);

        $this->modalContratoAbierto = false;
        $this->operacion->load('contratos');
    }

    public function renovarContrato(Contrato $contrato): void
    {
        $this->authorize('update', $this->operacion);

        $contrato->renovar([
            'fecha_inicio' => $contrato->fecha_fin ?? now()->toDateString(),
            'fecha_fin' => $contrato->fecha_fin?->addYear()->toDateString(),
            'estado' => 'firmado',
        ]);

        $this->operacion->load('contratos');
    }

    public function cerrar(): void
    {
        $this->authorize('update', $this->operacion);

        $this->operacion->cerrar();
        $this->operacion->load(['propiedad', 'comision']);
    }

    public function cancelar(): void
    {
        $this->authorize('update', $this->operacion);

        $this->operacion->cancelar();
    }

    public function render()
    {
        $this->operacion->loadMissing(['propiedad', 'agente', 'partes', 'cuotas.pagos', 'contratos', 'comision']);
        $clientes = Cliente::orderBy('nombre')->get();

        return view('livewire.operaciones.show', [
            'clientes' => $clientes,
            'contratoActivo' => $this->operacion->contratos->whereNotIn('estado', ['renovado'])->last(),
        ]);
    }
}
