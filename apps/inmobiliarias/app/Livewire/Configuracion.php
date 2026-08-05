<?php

namespace App\Livewire;

use App\Models\Configuracion as ConfiguracionModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Configuración')]
class Configuracion extends Component
{
    public ?string $comision_porcentaje = null;

    public function mount(): void
    {
        $configuracion = ConfiguracionModel::actual();
        $this->authorize('view', $configuracion);

        $this->comision_porcentaje = $configuracion->comision_porcentaje;
    }

    protected function rules(): array
    {
        return [
            'comision_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function guardar(): void
    {
        $configuracion = ConfiguracionModel::actual();
        $this->authorize('update', $configuracion);

        $datos = $this->validate();
        $configuracion->update($datos);

        $this->dispatch('configuracion-guardada');
    }

    public function render()
    {
        return view('livewire.configuracion');
    }
}
