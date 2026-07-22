<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_consentimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->longText('contenido_pagina1')->nullable();
            $table->longText('contenido_pagina2')->nullable();
            $table->boolean('requiere_firma_profesional')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('consentimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('tipo_id')->constrained('tipos_consentimiento');
            // Patient signing
            $table->boolean('firmado_paciente')->default(false);
            $table->timestamp('firmado_paciente_at')->nullable();
            $table->string('firma_paciente_img')->nullable();
            // Email token for remote signing
            $table->string('token', 80)->nullable()->unique();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('token_email')->nullable();
            // Professional signing
            $table->boolean('firmado_profesional')->default(false);
            $table->timestamp('firmado_profesional_at')->nullable();
            $table->foreignId('firmado_por_id')->nullable()->constrained('users')->nullOnDelete();
            // Generated PDF
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed default consent type
        DB::table('tipos_consentimiento')->insert([
            'nombre'      => 'Consentimiento Informado para Tratamiento',
            'descripcion' => 'Consentimiento general de tratamiento psicológico/terapéutico.',
            'contenido_pagina1' => '<p>Por medio del presente documento, manifiesto libre y voluntariamente mi consentimiento para recibir tratamiento.</p>

<p><strong>Declaro haber sido informado/a sobre:</strong></p>
<ol>
<li>La naturaleza del tratamiento propuesto, sus objetivos, metodología y duración estimada.</li>
<li>Los posibles beneficios, riesgos y alternativas disponibles.</li>
<li>Mi derecho a retirar este consentimiento en cualquier momento, sin que ello afecte la calidad de la atención que se me brinde.</li>
<li>La confidencialidad de mis datos personales y la información clínica, en conformidad con la normativa vigente.</li>
<li>El reglamento interno de la institución, cuyas normas me comprometo a conocer y respetar durante el período de tratamiento.</li>
</ol>

<p><strong>Compromisos del paciente:</strong></p>
<ol>
<li>Participar activamente en el plan terapéutico indicado por el equipo profesional.</li>
<li>Comunicar cualquier cambio en mi estado de salud o circunstancias que pudieran afectar el tratamiento.</li>
<li>Respetar los horarios, normas de convivencia y disposiciones del equipo tratante.</li>
<li>Abstenerse de conductas que pongan en riesgo la propia seguridad o la de otros miembros de la comunidad terapéutica.</li>
</ol>',
            'contenido_pagina2' => '<p>Yo, el/la abajo firmante, declaro haber recibido, leído y comprendido el <strong>Reglamento Interno</strong> de la institución.</p>

<p>Me comprometo a cumplir con las normas establecidas en dicho reglamento durante todo el período que dure mi tratamiento.</p>

<p>Entiendo que el incumplimiento de dichas normas puede derivar en consecuencias disciplinarias conforme lo establecido en el reglamento, las cuales han sido explicadas por el equipo profesional.</p>

<p>Este acuse de recibo queda incorporado a mi legajo clínico como constancia de mi conocimiento y aceptación voluntaria.</p>',
            'requiere_firma_profesional' => false,
            'activo'      => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('consentimientos');
        Schema::dropIfExists('tipos_consentimiento');
    }
};
