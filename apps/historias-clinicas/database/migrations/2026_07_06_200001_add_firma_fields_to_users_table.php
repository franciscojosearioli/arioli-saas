<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirmaFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firma_imagen')->nullable()->after('two_factor_expires_at');
            $table->string('firma_nombre')->nullable()->after('firma_imagen');
            $table->string('firma_dni', 20)->nullable()->after('firma_nombre');
            $table->string('firma_matricula', 50)->nullable()->after('firma_dni');
            $table->string('firma_especialidad_texto', 100)->nullable()->after('firma_matricula');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'firma_imagen', 'firma_nombre', 'firma_dni',
                'firma_matricula', 'firma_especialidad_texto',
            ]);
        });
    }
}
