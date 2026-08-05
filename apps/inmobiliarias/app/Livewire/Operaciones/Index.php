<?php

namespace App\Livewire\Operaciones;

use App\Models\Operacion;
use App\Models\Propiedad;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Operaciones')]
class Index extends Component
{
    use WithPagination;

    public string $filtroTipo = '';

    public string $filtroEstado = '';

    public bool $modalAbierto = false;

    public ?int $propiedad_id = null;

    public ?int $agente_id = null;

    public string $tipo = 'venta';

    public string $fecha_inicio = '';

    public ?string $monto = null;

    public string $moneda = 'ARS';

    public function mount(): void
    {
        $this->authorize('viewAny', Operacion::class);
        $this->fecha_inicio = now()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'propiedad_id' => ['required', 'exists:propiedades,id'],
            'agente_id' => ['nullable', 'exists:users,id'],
            'tipo' => ['required', Rule::in(['venta', 'alquiler', 'reserva'])],
            'fecha_inicio' => ['required', 'date'],
            'monto' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['required', Rule::in(['ARS', 'USD'])],
        ];
    }

    public function nueva(): void
    {
        $this->authorize('create', Operacion::class);

        $this->resetForm();
        $this->modalAbierto = true;
    }

    public function guardar(): void
    {
        $this->authorize('create', Operacion::class);

        $datos = $this->validate();

        // §07: si un agente abre su propia operación, queda asignada a
        // él — sin esto, la Policy la deja invisible para su creador
        // apenas se guarda (mismo caso resuelto en la API).
        if (! $datos['agente_id'] && auth()->user()->hasRole('agente')) {
            $datos['agente_id'] = auth()->id();
        }

        $operacion = Operacion::create($datos);

        $this->modalAbierto = false;
        $this->resetForm();

        $this->redirectRoute('panel.operaciones.show', $operacion, navigate: true);
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['propiedad_id', 'agente_id', 'monto']);
        $this->tipo = 'venta';
        $this->moneda = 'ARS';
        $this->fecha_inicio = now()->toDateString();
        $this->resetErrorBag();
    }

    public function updatingFiltroTipo(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $operaciones = Operacion::query()
            ->with(['propiedad', 'agente'])
            ->when($user->hasRole('agente'), fn ($q) => $q->where('agente_id', $user->id))
            ->when($this->filtroTipo !== '', fn ($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroEstado !== '', fn ($q) => $q->where('estado', $this->filtroEstado))
            ->orderByDesc('fecha_inicio')
            ->paginate(15);

        $propiedades = Propiedad::whereIn('estado', ['disponible', 'reservado'])->orderBy('titulo')->get();
        $agentes = User::role('agente')->orderBy('name')->get();

        return view('livewire.operaciones.index', compact('operaciones', 'propiedades', 'agentes'));
    }
}
