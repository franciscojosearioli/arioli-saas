<?php

namespace App\Livewire\Crm;

use App\Models\Cliente;
use App\Models\Lead;
use App\Models\Propiedad;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Leads')]
class Leads extends Component
{
    use WithPagination;

    public string $filtroEstado = '';

    public bool $modalAbierto = false;

    public ?int $editandoId = null;

    public ?int $agente_id = null;

    public ?int $cliente_id = null;

    public ?int $propiedad_id = null;

    public string $nombre = '';

    public ?string $email = null;

    public ?string $telefono = null;

    public string $origen = 'formulario';

    public string $estado = 'nuevo';

    public ?string $notas = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Lead::class);
    }

    protected function rules(): array
    {
        return [
            'agente_id' => ['nullable', 'exists:users,id'],
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'propiedad_id' => ['nullable', 'exists:propiedades,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'origen' => ['required', Rule::in(['storefront', 'formulario', 'whatsapp', 'referido', 'otro'])],
            'estado' => ['required', Rule::in(['nuevo', 'contactado', 'calificado', 'convertido', 'perdido'])],
            'notas' => ['nullable', 'string'],
        ];
    }

    public function nuevo(): void
    {
        $this->authorize('create', Lead::class);

        $this->resetForm();
        // §07: un agente que carga un lead lo carga para sí mismo — si
        // pudiera dejarlo sin asignar o asignarlo a otro, quedaría fuera
        // de su propia cartera (LeadPolicy::esDueno) apenas lo guarda.
        if (auth()->user()->hasRole('agente')) {
            $this->agente_id = auth()->id();
        }
        $this->modalAbierto = true;
    }

    public function editar(int $id): void
    {
        $lead = Lead::findOrFail($id);
        $this->authorize('update', $lead);

        $this->editandoId = $lead->id;
        $this->agente_id = $lead->agente_id;
        $this->cliente_id = $lead->cliente_id;
        $this->propiedad_id = $lead->propiedad_id;
        $this->nombre = $lead->nombre;
        $this->email = $lead->email;
        $this->telefono = $lead->telefono;
        $this->origen = $lead->origen;
        $this->estado = $lead->estado;
        $this->notas = $lead->notas;
        $this->modalAbierto = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if ($this->editandoId) {
            $lead = Lead::findOrFail($this->editandoId);
            $this->authorize('update', $lead);
            $lead->update($datos);
        } else {
            $this->authorize('create', Lead::class);
            Lead::create($datos);
        }

        $this->modalAbierto = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        $lead = Lead::findOrFail($id);
        $this->authorize('delete', $lead);

        $lead->delete();
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editandoId', 'agente_id', 'cliente_id', 'propiedad_id', 'nombre',
            'email', 'telefono', 'notas',
        ]);
        $this->origen = 'formulario';
        $this->estado = 'nuevo';
        $this->resetErrorBag();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $leads = Lead::query()
            ->with(['agente', 'cliente', 'propiedad'])
            ->when($user->hasRole('agente'), fn ($q) => $q->where('agente_id', $user->id))
            ->when($this->filtroEstado !== '', fn ($q) => $q->where('estado', $this->filtroEstado))
            ->orderByDesc('created_at')
            ->paginate(15);

        $agentes = User::role('agente')->orderBy('name')->get();
        $clientes = Cliente::orderBy('nombre')->get();
        $propiedades = Propiedad::orderBy('titulo')->get();

        return view('livewire.crm.leads', compact('leads', 'agentes', 'clientes', 'propiedades'));
    }
}
