<?php

namespace App\Livewire\Finanzas;

use App\Models\Comision;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Comisiones')]
class Comisiones extends Component
{
    use WithPagination;

    public string $filtroEstado = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Comision::class);
    }

    public function liquidar(int $comisionId): void
    {
        $comision = Comision::findOrFail($comisionId);
        $this->authorize('update', $comision);

        $comision->liquidar();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $comisiones = Comision::query()
            ->with(['agente', 'operacion.propiedad'])
            ->when($user->hasRole('agente'), fn ($q) => $q->where('agente_id', $user->id))
            ->when($this->filtroEstado !== '', fn ($q) => $q->where('estado', $this->filtroEstado))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.finanzas.comisiones', compact('comisiones'));
    }
}
