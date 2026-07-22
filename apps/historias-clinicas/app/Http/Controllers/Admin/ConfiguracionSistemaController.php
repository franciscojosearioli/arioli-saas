<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ConfiguracionSistemaController extends Controller
{
    public function edit()
    {
        $config = ConfiguracionSistema::instancia();
        return view('admin.configuracion.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre_sistema'     => 'required|string|max:255',
            'logo'               => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon'            => 'nullable|image|mimes:png,ico,jpg,jpeg|max:512',
            'nombre_institucion' => 'nullable|string|max:255',
            'tipo_institucion'   => 'nullable|string|max:100',
            'descripcion'        => 'nullable|string|max:1000',
            'direccion'          => 'nullable|string|max:255',
            'localidad'          => 'nullable|string|max:100',
            'provincia'          => 'nullable|string|max:100',
            'pais'               => 'nullable|string|max:100',
            'telefono'           => 'nullable|string|max:50',
            'email_contacto'     => 'nullable|email|max:255',
            'website'            => 'nullable|url|max:255',
            'cuit'               => 'nullable|string|max:20',
            'pie_pdf'            => 'nullable|string|max:500',
        ]);

        $config = ConfiguracionSistema::instancia();
        $data   = $request->except(['_token', '_method', 'logo', 'favicon']);

        // Logo
        if ($request->hasFile('logo')) {
            if ($config->logo) {
                Storage::disk('public')->delete($config->logo);
            }
            $data['logo'] = $request->file('logo')->store('sistema', 'public');
        }

        // Favicon
        if ($request->hasFile('favicon')) {
            if ($config->favicon) {
                Storage::disk('public')->delete($config->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('sistema', 'public');
        }

        $config->update($data);

        Cache::forget('sistema_config');

        notify()->success('Configuración guardada correctamente.', 'Sistema actualizado');
        return redirect()->route('admin.configuracion.edit');
    }
}
