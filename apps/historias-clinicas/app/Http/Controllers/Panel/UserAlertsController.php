<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserAlertRequest;
use App\Http\Requests\StoreUserAlertRequest;
use App\Models\User;
use App\Models\UserAlert;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAlertsController extends Controller
{
    public function index()
    {
        $userAlerts = auth()->user()->userUserAlerts()
            ->withPivot('read')
            ->orderByDesc('user_alerts.created_at')
            ->get();

        return view('panel.notificaciones.index', compact('userAlerts'));
    }

    public function readAll(Request $request)
    {
        $alerts = auth()->user()->userUserAlerts()->wherePivot('read', false)->get();
        foreach ($alerts as $alert) {
            auth()->user()->userUserAlerts()->updateExistingPivot($alert->id, ['read' => true]);
        }
        return back()->with('success', 'Notificaciones marcadas como leídas.');
    }

    public function readOne(Request $request, $id)
    {
        auth()->user()->userUserAlerts()->updateExistingPivot($id, ['read' => true]);
        return back();
    }

    public function create()
    {
        abort_if(Gate::denies('user_alert_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id');

        return view('panel.userAlerts.create', compact('users'));
    }

    public function store(StoreUserAlertRequest $request)
    {
        $userAlert = UserAlert::create($request->all());
        $userAlert->users()->sync($request->input('users', []));

        return redirect()->route('panel.user-alerts.index');
    }

    public function show(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userAlert->load('users');

        return view('panel.userAlerts.show', compact('userAlert'));
    }

    public function destroy(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userAlert->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserAlertRequest $request)
    {
        $userAlerts = UserAlert::find(request('ids'));

        foreach ($userAlerts as $userAlert) {
            $userAlert->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function read(Request $request)
    {
        $alerts = \Auth::user()->userUserAlerts()->where('read', false)->get();
        foreach ($alerts as $alert) {
            $pivot       = $alert->pivot;
            $pivot->read = true;
            $pivot->save();
        }
    }
}