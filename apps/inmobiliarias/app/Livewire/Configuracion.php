<?php

namespace App\Livewire;

use App\Models\Configuracion as ConfiguracionModel;
use App\Models\CuentaConectada;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Configuración')]
class Configuracion extends Component
{
    public ?string $nombre_comercial = null;

    public ?string $descripcion = null;

    public ?string $logo_url = null;

    public ?string $comision_porcentaje = null;

    public ?string $sitio_web_url = null;

    public ?string $sitio_web_api_key = null;

    public function mount(): void
    {
        $configuracion = ConfiguracionModel::actual();
        $this->authorize('view', $configuracion);

        $this->nombre_comercial = $configuracion->nombre_comercial;
        $this->descripcion = $configuracion->descripcion;
        $this->logo_url = $configuracion->logo_url;
        $this->comision_porcentaje = $configuracion->comision_porcentaje;
        $this->sitio_web_url = $configuracion->sitio_web_url;
        $this->sitio_web_api_key = $configuracion->sitio_web_api_key;
    }

    protected function rules(): array
    {
        return [
            'nombre_comercial' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'url', 'max:255'],
            'comision_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sitio_web_url' => ['nullable', 'url', 'max:255'],
            'sitio_web_api_key' => ['nullable', 'string', 'max:255'],
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
        return view('livewire.configuracion', [
            'cuentasConectadas' => CuentaConectada::all()->keyBy('canal'),
        ]);
    }
}
