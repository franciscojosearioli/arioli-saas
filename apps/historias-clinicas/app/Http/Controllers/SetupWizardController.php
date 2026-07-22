<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SetupWizardController extends Controller
{
    public function show()
    {
        $config = ConfiguracionSistema::instancia();

        if ($config->setup_completed) {
            return redirect()->route('panel.home');
        }

        return view('setup.wizard', compact('config'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'nombre_sistema'     => 'required|string|max:255',
            'nombre_institucion' => 'required|string|max:255',
            'logo'               => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon'            => 'nullable|image|mimes:png,ico,jpg,jpeg|max:512',
            'tipo_institucion'   => 'nullable|string|max:100',
            'descripcion'        => 'nullable|string|max:1000',
            'direccion'          => 'nullable|string|max:255',
            'localidad'          => 'nullable|string|max:100',
            'provincia'          => 'nullable|string|max:100',
            'telefono'           => 'nullable|string|max:50',
            'cuit'               => 'nullable|string|max:20',
            'email_contacto'     => 'nullable|email|max:255',
            'website'            => 'nullable|url|max:255',
            'pie_pdf'            => 'nullable|string|max:500',
        ]);

        $config = ConfiguracionSistema::instancia();
        $data   = $request->except(['_token', 'logo', 'favicon']);

        if ($request->hasFile('logo')) {
            if ($config->logo) {
                Storage::disk('public')->delete($config->logo);
            }
            $data['logo'] = $request->file('logo')->store('sistema', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($config->favicon) {
                Storage::disk('public')->delete($config->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('sistema', 'public');
        }

        $data['setup_completed'] = true;
        $config->update($data);

        return redirect()->route('panel.home')
            ->with('success', '¡Sistema configurado correctamente!');
    }
}
