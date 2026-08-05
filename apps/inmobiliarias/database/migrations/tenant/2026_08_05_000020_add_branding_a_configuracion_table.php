<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §08: "perfil de inmobiliaria — una página por tenant, con su branding
// ... alimentada por Configuración". Nullable: sin esto cargado, el
// perfil público simplemente no tiene branding propio todavía — no se
// inventa un nombre comercial ni un logo.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('nombre_comercial')->nullable()->after('id');
            $table->text('descripcion')->nullable()->after('nombre_comercial');
            $table->string('logo_url')->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn(['nombre_comercial', 'descripcion', 'logo_url']);
        });
    }
};
