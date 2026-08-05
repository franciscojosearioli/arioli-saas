<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('summary')->nullable()->after('title');
            $table->text('introduction')->nullable()->after('summary');
            $table->text('project_scope')->nullable()->after('introduction');
            $table->text('benefits')->nullable()->after('project_scope');
            $table->text('exclusions')->nullable()->after('benefits');
            $table->text('warranty')->nullable()->after('exclusions');
            $table->text('observations')->nullable()->after('payment_terms');
            $table->text('final_message')->nullable()->after('observations');
        });

        // Preserva el contenido real ya cargado (propuesta id=5 en producción, con datos
        // reales de un cliente) antes de eliminar `content` — se copia como texto plano a
        // `introduction` para que se redistribuya manualmente en las secciones nuevas.
        DB::table('quotes')->whereNotNull('content')->where('content', '!=', '')
            ->get(['id', 'content'])
            ->each(fn ($row) => DB::table('quotes')->where('id', $row->id)->update([
                'introduction' => trim(strip_tags($row->content)),
            ]));

        // `payment_terms` cambia de HTML (editor Quill) a texto plano — limpia lo ya guardado.
        DB::table('quotes')->whereNotNull('payment_terms')->where('payment_terms', '!=', '')
            ->get(['id', 'payment_terms'])
            ->each(fn ($row) => DB::table('quotes')->where('id', $row->id)->update([
                'payment_terms' => trim(strip_tags($row->payment_terms)),
            ]));

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('title');
            $table->dropColumn([
                'summary', 'introduction', 'project_scope', 'benefits',
                'exclusions', 'warranty', 'observations', 'final_message',
            ]);
        });
    }
};
