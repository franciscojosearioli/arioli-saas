<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirmaToInformesTable extends Migration
{
    public function up()
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->boolean('firmado')->default(false)->after('redaccion');
            $table->unsignedBigInteger('firmado_por')->nullable()->after('firmado');
            $table->timestamp('firmado_at')->nullable()->after('firmado_por');

            $table->foreign('firmado_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->dropForeign(['firmado_por']);
            $table->dropColumn(['firmado', 'firmado_por', 'firmado_at']);
        });
    }
}
