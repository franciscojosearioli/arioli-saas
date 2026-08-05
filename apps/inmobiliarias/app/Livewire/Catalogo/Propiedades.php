<?php

namespace App\Livewire\Catalogo;

use App\Models\Cliente;
use App\Models\Desarrollo;
use App\Models\Propiedad;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Propiedades')]
class Propiedades extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroTipo = '';

    public string $filtroEstado = '';

    public bool $modalAbierto = false;

    public ?int $editandoId = null;

    public ?int $desarrollo_id = null;

    public ?int $propietario_id = null;

    public string $tipo = 'casa';

    public string $titulo = '';

    public ?string $descripcion = null;

    public string $estado = 'disponible';

    public ?string $precio = null;

    public string $moneda = 'ARS';

    public ?string $superficie_cubierta = null;

    public ?string $superficie_total = null;

    public ?int $ambientes = null;

    public ?int $dormitorios = null;

    public ?int $banos = null;

    public ?int $cocheras = null;

    public ?string $manzana = null;

    public ?string $numero_lote = null;

    public ?string $direccion = null;

    public ?string $provincia = null;

    public ?string $ciudad = null;

    public ?string $barrio = null;

    public string $servicios = '';

    public string $caracteristicas_destacadas = '';

    public string $ubicacion_wkt = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Propiedad::class);
    }

    protected function rules(): array
    {
        return [
            'desarrollo_id' => ['nullable', 'exists:desarrollos,id'],
            'propietario_id' => ['nullable', 'exists:clientes,id'],
            'tipo' => ['required', Rule::in([
                'loteo', 'casa', 'departamento', 'local', 'oficina', 'galpon', 'campo', 'cochera',
            ])],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['disponible', 'reservado', 'vendido', 'alquilado'])],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'moneda' => ['required', Rule::in(['ARS', 'USD'])],
            'superficie_cubierta' => ['nullable', 'numeric', 'min:0'],
            'superficie_total' => ['nullable', 'numeric', 'min:0'],
            'ambientes' => ['nullable', 'integer', 'min:0', 'max:255'],
            'dormitorios' => ['nullable', 'integer', 'min:0', 'max:255'],
            'banos' => ['nullable', 'integer', 'min:0', 'max:255'],
            'cocheras' => ['nullable', 'integer', 'min:0', 'max:255'],
            'manzana' => [
                'nullable', 'string', 'max:50',
                Rule::unique('propiedades')->ignore($this->editandoId)->where(fn ($q) => $q
                    ->where('desarrollo_id', $this->desarrollo_id)
                    ->where('numero_lote', $this->numero_lote)),
            ],
            'numero_lote' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'ubicacion_wkt' => ['nullable', 'string', 'regex:/^(POINT\([-\d.\s]+\)|POLYGON\(\([-\d.,\s]+\)\))$/i'],
        ];
    }

    protected function messages(): array
    {
        return [
            'manzana.unique' => 'Ya existe un lote con ese número en esa manzana, dentro de este desarrollo.',
        ];
    }

    public function nueva(): void
    {
        $this->authorize('create', Propiedad::class);

        $this->resetForm();
        $this->modalAbierto = true;
    }

    public function editar(int $id): void
    {
        $propiedad = Propiedad::findOrFail($id);
        $this->authorize('update', $propiedad);

        $this->editandoId = $propiedad->id;
        $this->desarrollo_id = $propiedad->desarrollo_id;
        $this->propietario_id = $propiedad->propietario_id;
        $this->tipo = $propiedad->tipo;
        $this->titulo = $propiedad->titulo;
        $this->descripcion = $propiedad->descripcion;
        $this->estado = $propiedad->estado;
        $this->precio = $propiedad->precio;
        $this->moneda = $propiedad->moneda;
        $this->superficie_cubierta = $propiedad->superficie_cubierta;
        $this->superficie_total = $propiedad->superficie_total;
        $this->ambientes = $propiedad->ambientes;
        $this->dormitorios = $propiedad->dormitorios;
        $this->banos = $propiedad->banos;
        $this->cocheras = $propiedad->cocheras;
        $this->manzana = $propiedad->manzana;
        $this->numero_lote = $propiedad->numero_lote;
        $this->direccion = $propiedad->direccion;
        $this->provincia = $propiedad->provincia;
        $this->ciudad = $propiedad->ciudad;
        $this->barrio = $propiedad->barrio;
        $this->servicios = implode(', ', $propiedad->servicios ?? []);
        $this->caracteristicas_destacadas = implode(', ', $propiedad->caracteristicas_destacadas ?? []);
        $this->ubicacion_wkt = $propiedad->ubicacionComoWkt() ?? '';
        $this->modalAbierto = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();
        $datos['servicios'] = $this->tags($this->servicios);
        $datos['caracteristicas_destacadas'] = $this->tags($this->caracteristicas_destacadas);
        $wkt = $datos['ubicacion_wkt'];
        unset($datos['ubicacion_wkt']);

        if ($this->editandoId) {
            $propiedad = Propiedad::findOrFail($this->editandoId);
            $this->authorize('update', $propiedad);
            $propiedad->update($datos);
        } else {
            $this->authorize('create', Propiedad::class);
            $propiedad = Propiedad::create($datos);
        }

        // guardarUbicacion() escribe vía query builder crudo — nunca
        // dispara el evento `updated` de Eloquent, así que
        // PropiedadObserver no se entera solo del cambio de geometría.
        $propiedad->guardarUbicacion($wkt ?: null);
        $propiedad->dispararSincronizacion();

        $this->modalAbierto = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        $propiedad = Propiedad::findOrFail($id);
        $this->authorize('delete', $propiedad);

        $propiedad->delete();
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetForm();
    }

    private function tags(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn (string $v) => trim($v))
            ->filter()
            ->values()
            ->all();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editandoId', 'desarrollo_id', 'propietario_id', 'titulo', 'descripcion', 'precio',
            'superficie_cubierta', 'superficie_total', 'ambientes', 'dormitorios', 'banos',
            'cocheras', 'manzana', 'numero_lote', 'direccion', 'provincia', 'ciudad', 'barrio',
            'servicios', 'caracteristicas_destacadas', 'ubicacion_wkt',
        ]);
        $this->tipo = 'casa';
        $this->estado = 'disponible';
        $this->moneda = 'ARS';
        $this->resetErrorBag();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
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
        $propiedades = Propiedad::query()
            ->with(['desarrollo', 'propietario'])
            ->when($this->search !== '', fn ($q) => $q->where('titulo', 'like', "%{$this->search}%"))
            ->when($this->filtroTipo !== '', fn ($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroEstado !== '', fn ($q) => $q->where('estado', $this->filtroEstado))
            ->orderByDesc('created_at')
            ->paginate(15);

        $desarrollos = Desarrollo::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre')->get();

        return view('livewire.catalogo.propiedades', compact('propiedades', 'desarrollos', 'clientes'));
    }
}
