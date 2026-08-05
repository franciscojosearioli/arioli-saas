<?php

namespace App\Livewire\Catalogo;

use App\Models\Constructora;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Constructoras')]
class Constructoras extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $modalAbierto = false;

    public ?int $editandoId = null;

    public string $nombre = '';

    public ?string $descripcion = null;

    public ?string $logo = null;

    public ?string $email = null;

    public ?string $telefono = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Constructora::class);
    }

    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function nueva(): void
    {
        $this->authorize('create', Constructora::class);

        $this->resetForm();
        $this->modalAbierto = true;
    }

    public function editar(int $id): void
    {
        $constructora = Constructora::findOrFail($id);
        $this->authorize('update', $constructora);

        $this->editandoId = $constructora->id;
        $this->nombre = $constructora->nombre;
        $this->descripcion = $constructora->descripcion;
        $this->logo = $constructora->logo;
        $this->email = $constructora->email;
        $this->telefono = $constructora->telefono;
        $this->modalAbierto = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if ($this->editandoId) {
            $constructora = Constructora::findOrFail($this->editandoId);
            $this->authorize('update', $constructora);
            $constructora->update($datos);
        } else {
            $this->authorize('create', Constructora::class);
            Constructora::create($datos);
        }

        $this->modalAbierto = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        $constructora = Constructora::findOrFail($id);
        $this->authorize('delete', $constructora);

        $constructora->delete();
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editandoId', 'nombre', 'descripcion', 'logo', 'email', 'telefono']);
        $this->resetErrorBag();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $constructoras = Constructora::query()
            ->withCount('desarrollos')
            ->when($this->search !== '', fn ($q) => $q->where('nombre', 'like', "%{$this->search}%"))
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.catalogo.constructoras', compact('constructoras'));
    }
}
