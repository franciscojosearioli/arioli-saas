<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos que devuelve AFIP al autorizar un comprobante vía WSFEv1
 * (FECAESolicitar) — cbte_tipo/doc_tipo son los códigos que exige AFIP
 * (11 = Factura C, 80 = CUIT, 99 = Consumidor Final, etc.), cae/cae_vencimiento
 * son el número de autorización real y su vencimiento.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedSmallInteger('cbte_tipo')->nullable()->after('type');
            $table->unsignedSmallInteger('doc_tipo')->nullable()->after('customer_cuit');
            $table->string('doc_nro')->nullable()->after('doc_tipo');
            $table->string('cae')->nullable()->after('number');
            $table->date('cae_vencimiento')->nullable()->after('cae');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['cbte_tipo', 'doc_tipo', 'doc_nro', 'cae', 'cae_vencimiento']);
        });
    }
};
