<?php

namespace App\Services;

use App\Models\Informe;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Support\Collection;

class NotificacionService
{
    // Role IDs
    const ROLE_ADMIN       = 1;
    const ROLE_PROFESIONAL = 2;
    const ROLE_SECRETARIA  = 3;

    /**
     * Create a UserAlert and dispatch it to the given user IDs.
     */
    public static function dispatch(string $texto, ?string $link, array|Collection $userIds): void
    {
        if (empty($userIds) || (is_countable($userIds) && count($userIds) === 0)) {
            return;
        }

        $alert = UserAlert::create([
            'alert_text' => $texto,
            'alert_link' => $link,
        ]);

        $ids = collect($userIds)->unique()->values()->toArray();
        $pivotData = array_fill_keys($ids, ['read' => false]);
        $alert->users()->sync($pivotData);
    }

    /**
     * All admin + secretaria users.
     */
    public static function adminYSecretaria(): Collection
    {
        return User::whereHas('roles', fn($q) => $q->whereIn('id', [self::ROLE_ADMIN, self::ROLE_SECRETARIA]))
            ->pluck('id');
    }

    /**
     * Professionals who have at least one informe for this patient.
     */
    public static function profesionalesDelPaciente(int $pacienteId): Collection
    {
        return Informe::where('paciente_id', $pacienteId)
            ->whereNotNull('profesional_id')
            ->distinct()
            ->pluck('profesional_id');
    }

    /**
     * Combined audience for patient-related events:
     * admin + secretaria + professionals linked to that patient,
     * optionally excluding the actor who triggered the event.
     */
    public static function audienciaEvento(int $pacienteId, ?int $excluirUserId = null): Collection
    {
        $ids = self::adminYSecretaria()
            ->merge(self::profesionalesDelPaciente($pacienteId))
            ->unique();

        if ($excluirUserId) {
            $ids = $ids->filter(fn($id) => $id !== $excluirUserId)->values();
        }

        return $ids;
    }

    // ── Convenience methods for each event type ───────────────────────────

    public static function consentimientoFirmadoPaciente(
        string $pacienteNombre, int $pacienteId, int $consentimientoId, ?int $excluirUserId = null
    ): void {
        static::dispatch(
            "El paciente {$pacienteNombre} firmó un consentimiento informado.",
            route('panel.consentimiento.pdf', $consentimientoId),
            static::audienciaEvento($pacienteId, $excluirUserId)
        );
    }

    public static function consentimientoFirmadoProfesional(
        string $profesionalNombre, string $pacienteNombre, int $pacienteId, int $consentimientoId, ?int $excluirUserId = null
    ): void {
        static::dispatch(
            "{$profesionalNombre} firmó el consentimiento de {$pacienteNombre} como profesional.",
            route('panel.consentimiento.pdf', $consentimientoId),
            static::audienciaEvento($pacienteId, $excluirUserId)
        );
    }

    public static function informeCreado(
        string $profesionalNombre, string $pacienteNombre, string $tipoInforme, int $informeId, ?int $excluirUserId = null
    ): void {
        static::dispatch(
            "{$profesionalNombre} cargó un nuevo informe de {$tipoInforme} para {$pacienteNombre}.",
            route('panel.informe.show', $informeId),
            static::adminYSecretaria()->when($excluirUserId, fn($c) => $c->filter(fn($id) => $id !== $excluirUserId)->values())
        );
    }

    public static function informeFirmado(
        string $profesionalNombre, string $pacienteNombre, string $tipoInforme, int $informeId, int $pacienteId, ?int $excluirUserId = null
    ): void {
        static::dispatch(
            "{$profesionalNombre} firmó digitalmente un informe de {$tipoInforme} para {$pacienteNombre}.",
            route('panel.informe.show', $informeId),
            static::audienciaEvento($pacienteId, $excluirUserId)
        );
    }

    public static function recetaSubida(
        string $profesionalNombre, string $pacienteNombre, int $informeId, int $pacienteId, int $cantidad, ?int $excluirUserId = null
    ): void {
        $txt = $cantidad === 1
            ? "{$profesionalNombre} adjuntó una receta médica para {$pacienteNombre}."
            : "{$profesionalNombre} adjuntó {$cantidad} recetas médicas para {$pacienteNombre}.";

        static::dispatch(
            $txt,
            route('panel.informe.show', $informeId),
            static::adminYSecretaria()->when($excluirUserId, fn($c) => $c->filter(fn($id) => $id !== $excluirUserId)->values())
        );
    }

    public static function citaCreada(
        string $actor, string $pacienteNombre, string $fecha, int $agendaId, int $profesionalId
    ): void {
        // Notify the assigned professional (if different from actor)
        $profesional = User::find($profesionalId);
        if (!$profesional) return;

        // Also notify admin/secretaria
        $ids = static::adminYSecretaria()->push($profesionalId)->unique();

        static::dispatch(
            "Nueva cita agendada para {$pacienteNombre} el {$fecha}.",
            route('panel.agenda.show', $agendaId),
            $ids
        );
    }
}
