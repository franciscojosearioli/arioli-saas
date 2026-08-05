<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\ClientJob;
use App\Models\TimeEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TimeEntryController extends Controller
{
    private const TRACKABLE_TYPES = [
        'charge' => Charge::class,
        'job'    => ClientJob::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'trackable_type' => 'required|in:charge,job',
            'trackable_id'   => 'required|integer',
            'description'    => 'nullable|string|max:255',
            'worked_on'      => 'required|date',
            'hours'          => 'required|numeric|min:0.01',
            'rate_per_hour'  => 'required|numeric|min:0',
        ]);

        $modelClass = self::TRACKABLE_TYPES[$validated['trackable_type']];
        $trackable = $modelClass::findOrFail($validated['trackable_id']);

        $trackable->timeEntries()->create([
            'description'   => $validated['description'] ?? null,
            'worked_on'     => $validated['worked_on'],
            'hours'         => $validated['hours'],
            'rate_per_hour' => $validated['rate_per_hour'],
            'subtotal'      => round($validated['hours'] * $validated['rate_per_hour'], 2),
        ]);

        $this->recalculateAmount($trackable);

        return back()->with('success', 'Entrada de horas agregada.');
    }

    public function destroy(TimeEntry $timeEntry): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $trackable = $timeEntry->trackable;
        $timeEntry->delete();
        $this->recalculateAmount($trackable);

        return back()->with('success', 'Entrada de horas eliminada.');
    }

    /**
     * Si quedan entradas, el monto pasa a ser la suma. Si se borró la última
     * entrada, no se toca el monto (queda en su último valor conocido y el
     * campo vuelve a ser editable a mano) — nunca lo pisamos a 0.
     */
    private function recalculateAmount(Charge|ClientJob $trackable): void
    {
        $count = $trackable->timeEntries()->count();

        if ($count > 0) {
            $trackable->update(['amount' => $trackable->timeEntries()->sum('subtotal')]);
        }
    }
}
