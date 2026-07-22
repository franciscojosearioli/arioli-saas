<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function index()
    {
        return view('panel.profile');
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $user->update($request->validated());

        return redirect()->route('panel.profile.index')->with('message', __('global.update_profile_success'));
    }

    public function destroy()
    {
        $user = auth()->user();

        $user->update([
            'email' => time() . '_' . $user->email,
        ]);

        $user->delete();

        return redirect()->route('login')->with('message', __('global.delete_account_success'));
    }

    public function password(UpdatePasswordRequest $request)
    {
        auth()->user()->update($request->validated());

        return redirect()->route('panel.profile.index')->with('message', __('global.change_password_success'));
    }

    public function toggleTwoFactor(Request $request)
    {
        $user = auth()->user();

        if ($user->two_factor) {
            $message = __('global.two_factor.disabled');
        } else {
            $message = __('global.two_factor.enabled');
        }

        $user->two_factor = ! $user->two_factor;

        $user->save();

        return redirect()->route('panel.profile.index')->with('message', $message);
    }

    public function updateFirma(Request $request)
    {
        $request->validate([
            'firma_nombre'             => 'required|string|max:100',
            'firma_dni'                => 'nullable|string|max:20',
            'firma_matricula'          => 'nullable|string|max:50',
            'firma_especialidad_texto' => 'nullable|string|max:100',
            'firma_canvas_data'        => 'nullable|string',
            'firma_imagen_file'        => 'nullable|image|max:3072',
        ]);

        $user = auth()->user();

        $data = $request->only([
            'firma_nombre', 'firma_dni', 'firma_matricula', 'firma_especialidad_texto',
        ]);

        if ($request->filled('firma_canvas_data')) {
            $raw     = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('firma_canvas_data'));
            $decoded = base64_decode(str_replace(' ', '+', $raw));

            if ($user->firma_imagen) {
                Storage::disk('public')->delete($user->firma_imagen);
            }
            $path = 'firmas/' . $user->id . '/firma.png';
            Storage::disk('public')->put($path, $decoded);
            $data['firma_imagen'] = $path;

        } elseif ($request->hasFile('firma_imagen_file')) {
            if ($user->firma_imagen) {
                Storage::disk('public')->delete($user->firma_imagen);
            }
            $ext  = $request->file('firma_imagen_file')->getClientOriginalExtension();
            $path = $request->file('firma_imagen_file')->storeAs(
                'firmas/' . $user->id, 'firma.' . $ext, 'public'
            );
            $data['firma_imagen'] = $path;
        }

        $user->update($data);

        return redirect()->route('panel.profile.index')->with('message', 'Firma digital actualizada correctamente.');
    }

    public function deleteFirma()
    {
        $user = auth()->user();

        if ($user->firma_imagen) {
            Storage::disk('public')->delete($user->firma_imagen);
        }

        $user->update([
            'firma_imagen'             => null,
            'firma_nombre'             => null,
            'firma_dni'                => null,
            'firma_matricula'          => null,
            'firma_especialidad_texto' => null,
        ]);

        return redirect()->route('panel.profile.index')->with('message', 'Firma digital eliminada.');
    }
}