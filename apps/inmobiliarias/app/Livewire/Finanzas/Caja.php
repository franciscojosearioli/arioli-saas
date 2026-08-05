<?php

namespace App\Livewire\Finanzas;

use App\Models\ArqueoCaja;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Caja')]
class Caja extends Component
{
    use WithPagination;

    public bool $modalAbierto = false;

    public string $fecha = '';

    public ?string $monto_contado = null;

    public ?string $notas = null;

    public function mount(): void
    {
        $this->authorize('viewAny', ArqueoCaja::class);
    }

    protected function rules(): array
    {
        return [
            'fecha' => ['required', 'date', Rule::unique('arqueos_caja', 'fecha')],
            'monto_contado' => ['required', 'numeric', 'min:0'],
            'notas' => ['nullable', 'string'],
        ];
    }

    public function nuevo(): void
    {
        $this->authorize('create', ArqueoCaja::class);

        $this->fecha = now()->toDateString();
        $this->monto_contado = null;
        $this->notas = null;
        $this->resetErrorBag();
        $this->modalAbierto = true;
    }

    public function guardar(): void
    {
        $this->authorize('create', ArqueoCaja::class);

        $datos = $this->validate();

        ArqueoCaja::create([
            ...$datos,
            'monto_esperado' => ArqueoCaja::calcularEsperado($datos['fecha']),
            'cerrado_por_id' => auth()->id(),
        ]);

        $this->modalAbierto = false;
        $this->resetPage();
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
    }

    public function render()
    {
        $arqueos = ArqueoCaja::query()
            ->with('cerradoPor')
            ->orderByDesc('fecha')
            ->paginate(15);

        return view('livewire.finanzas.caja', compact('arqueos'));
    }
}
