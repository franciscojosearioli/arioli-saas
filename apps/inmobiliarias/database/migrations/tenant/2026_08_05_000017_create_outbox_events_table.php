<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §09: outbox pattern — se inserta en la MISMA transacción que el cambio
// de negocio (ver App\Observers\PropiedadObserver), así nunca hay un
// cambio confirmado sin su evento correspondiente. El worker (§09,
// SincronizarPublicaciones) hace polling de esto y llama al
// ChannelAdapter correspondiente por cada PublicacionCanal activo.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbox_events', function (Blueprint $table) {
            $table->id();
            $table->string('aggregate_type');
            $table->unsignedBigInteger('aggregate_id');
            $table->string('evento');
            $table->json('payload')->nullable();

            $table->timestamp('procesado_en')->nullable();
            $table->unsignedTinyInteger('intentos')->default(0);
            $table->text('ultimo_error')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['aggregate_type', 'aggregate_id']);
            $table->index('procesado_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_events');
    }
};
