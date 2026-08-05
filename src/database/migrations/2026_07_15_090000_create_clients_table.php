<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('commercial_status')->default('prospecto'); // prospecto|presupuesto_enviado|activo|suspendido|ex_cliente
            $table->string('priority')->default('media'); // alta|media|baja
            $table->string('next_action')->nullable();
            $table->date('next_action_due_at')->nullable();
            $table->timestamps();

            $table->index(['commercial_status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
