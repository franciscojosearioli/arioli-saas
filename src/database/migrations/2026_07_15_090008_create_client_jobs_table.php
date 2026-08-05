<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nombre de tabla "client_jobs" (no "jobs"): la tabla "jobs" ya existe y es la cola
     * de trabajos en background de Laravel (QUEUE_CONNECTION=database) — un concepto
     * totalmente distinto al "trabajo puntual" facturable a un cliente.
     */
    public function up(): void
    {
        Schema::create('client_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('hours', 8, 2)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('presupuestado'); // presupuestado|aprobado|en_progreso|completado|facturado|cancelado
            $table->date('requested_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_jobs');
    }
};
