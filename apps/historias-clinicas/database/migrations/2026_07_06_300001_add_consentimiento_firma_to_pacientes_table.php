<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->boolean('consentimiento_firmado')->default(false)->after('status');
            $table->timestamp('consentimiento_firmado_at')->nullable()->after('consentimiento_firmado');
            $table->string('consentimiento_firma_imagen')->nullable()->after('consentimiento_firmado_at');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['consentimiento_firmado', 'consentimiento_firmado_at', 'consentimiento_firma_imagen']);
        });
    }
};
