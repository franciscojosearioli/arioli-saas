<?php

namespace App\Http\Requests\Agenda;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('agenda_edit');
    }

    public function rules(): array
    {
        return [
            'paciente_id'                => 'required|exists:pacientes,id',
            'profesional_tipo'           => 'required|in:registrado,externo',
            'profesional_id'             => 'nullable|required_if:profesional_tipo,registrado|exists:users,id',
            'profesional_externo_nombre' => 'nullable|required_if:profesional_tipo,externo|string|max:255',
            'profesional_externo_email'  => 'nullable|email|max:255',
            'modalidad'                  => 'required|in:presencial,virtual',
            'fecha_hora_inicio'          => 'required|date',
            'fecha_hora_fin'             => 'required|date|after:fecha_hora_inicio',
            'motivo'                     => 'required|string|max:500',
            'estado'                     => 'required|in:pendiente,confirmado,cancelado,realizado',
            'notas'                      => 'nullable|string|max:1000',
            'link_virtual'               => 'nullable|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required'                => 'Debe seleccionar un paciente.',
            'profesional_id.required_if'          => 'Debe seleccionar un profesional registrado.',
            'profesional_externo_nombre.required_if' => 'Debe ingresar el nombre del profesional externo.',
            'fecha_hora_fin.after'                => 'La hora de fin debe ser posterior a la de inicio.',
            'motivo.required'                     => 'El motivo de la cita es obligatorio.',
            'link_virtual.url'                    => 'El enlace virtual debe ser una URL válida.',
        ];
    }
}
