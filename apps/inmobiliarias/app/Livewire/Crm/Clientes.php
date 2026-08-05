<?php

namespace App\Livewire\Crm;

use App\Models\Cliente;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Clientes')]
class Clientes extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $modalAbierto = false;

    public ?int $editandoId = null;

    public string $tipo_persona = 'fisica';

    public string $nombre = '';

    public ?string $documento = null;

    public ?string $email = null;

    public ?string $telefono = null;

    public ?string $direccion = null;

    public ?string $provincia = null;

    public ?string $ciudad = null;

    public ?string $notas = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Cliente::class);
    }

    protected function rules(): array
    {
        return [
            'tipo_persona' => ['required', Rule::in(['fisica', 'juridica'])],
            'nombre' => ['required', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'notas' => ['nullable', 'string'],
        ];
    }

    public function nuevo(): void
    {
        $this->authorize('create', Cliente::class);

        $this->resetForm();
        $this->modalAbierto = true;
    }

    public function editar(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $this->authorize('update', $cliente);

        $this->editandoId = $cliente->id;
        $this->tipo_persona = $cliente->tipo_persona;
        $this->nombre = $cliente->nombre;
        $this->documento = $cliente->documento;
        $this->email = $cliente->email;
        $this->telefono = $cliente->telefono;
        $this->direccion = $cliente->direccion;
        $this->provincia = $cliente->provincia;
        $this->ciudad = $cliente->ciudad;
        $this->notas = $cliente->notas;
        $this->modalAbierto = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if ($this->editandoId) {
            $cliente = Cliente::findOrFail($this->editandoId);
            $this->authorize('update', $cliente);
            $cliente->update($datos);
        } else {
            $this->authorize('create', Cliente::class);
            Cliente::create($datos);
        }

        $this->modalAbierto = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $this->authorize('delete', $cliente);

        $cliente->delete();
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editandoId', 'nombre', 'documento', 'email', 'telefono',
            'direccion', 'provincia', 'ciudad', 'notas',
        ]);
        $this->tipo_persona = 'fisica';
        $this->resetErrorBag();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->withCount('propiedades')
            ->when($this->search !== '', fn ($q) => $q->where('nombre', 'like', "%{$this->search}%"))
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.crm.clientes', compact('clientes'));
    }
}
