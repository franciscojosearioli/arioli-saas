<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->foreignId('plantilla_documento_version_id')
                ->nullable()
                ->after('tipo_id')
                ->constrained('plantilla_documento_versiones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plantilla_documento_version_id');
        });
    }
};
