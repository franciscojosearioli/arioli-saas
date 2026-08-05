<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('type'); // sitio_web|tienda_online|blog|sistema_a_medida|landing|intranet|otro
            $table->foreignId('domain_id')->nullable()->constrained('client_domains')->nullOnDelete();
            $table->foreignId('hosting_id')->nullable()->constrained('hostings')->nullOnDelete();
            $table->string('production_url')->nullable();
            $table->string('staging_url')->nullable();
            $table->string('status')->default('active'); // active|inactive|archived
            $table->string('priority')->default('media'); // alta|media|baja
            $table->date('started_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->date('archived_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
