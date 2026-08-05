<?php

namespace App\Livewire\Catalogo;

use App\Models\Constructora;
use App\Models\Desarrollo;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Desarrollos')]
class Desarrollos extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $modalAbierto = false;

    public ?int $editandoId = null;

    public ?int $constructora_id = null;

    public string $nombre = '';

    public string $tipo = 'loteo';

    public ?string $descripcion = null;

    public ?string $provincia = null;

    public ?string $ciudad = null;

    public ?string $barrio = null;

    public ?string $plano_maestro = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Desarrollo::class);
    }

    protected function rules(): array
    {
        return [
            'constructora_id' => ['nullable', 'exists:constructoras,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(['loteo', 'barrio_cerrado', 'edificio', 'emprendimiento'])],
            'descripcion' => ['nullable', 'string'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'plano_maestro' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function nuevo(): void
    {
        $this->authorize('create', Desarrollo::class);

        $this->resetForm();
        $this->modalAbierto = true;
    }

    public function editar(int $id): void
    {
        $desarrollo = Desarrollo::findOrFail($id);
        $this->authorize('update', $desarrollo);

        $this->editandoId = $desarrollo->id;
        $this->constructora_id = $desarrollo->constructora_id;
        $this->nombre = $desarrollo->nombre;
        $this->tipo = $desarrollo->tipo;
        $this->descripcion = $desarrollo->descripcion;
        $this->provincia = $desarrollo->provincia;
        $this->ciudad = $desarrollo->ciudad;
        $this->barrio = $desarrollo->barrio;
        $this->plano_maestro = $desarrollo->plano_maestro;
        $this->modalAbierto = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if ($this->editandoId) {
            $desarrollo = Desarrollo::findOrFail($this->editandoId);
            $this->authorize('update', $desarrollo);
            $desarrollo->update($datos);
        } else {
            $this->authorize('create', Desarrollo::class);
            Desarrollo::create($datos);
        }

        $this->modalAbierto = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        $desarrollo = Desarrollo::findOrFail($id);
        $this->authorize('delete', $desarrollo);

        $desarrollo->delete();
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editandoId', 'constructora_id', 'nombre', 'tipo', 'descripcion',
            'provincia', 'ciudad', 'barrio', 'plano_maestro',
        ]);
        $this->tipo = 'loteo';
        $this->resetErrorBag();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $desarrollos = Desarrollo::query()
            ->with('constructora')
            ->withCount('propiedades')
            ->when($this->search !== '', fn ($q) => $q->where('nombre', 'like', "%{$this->search}%"))
            ->orderBy('nombre')
            ->paginate(15);

        $constructoras = Constructora::orderBy('nombre')->get();

        return view('livewire.catalogo.desarrollos', compact('desarrollos', 'constructoras'));
    }
}
