<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('public_domain', 63)->after('slug')->default('');
        });

        // Populate from existing slugs — safe defaults until seeder runs
        \Illuminate\Support\Facades\DB::table('products')->get()->each(function ($row) {
            $publicDomain = match($row->slug) {
                'tallerpro'          => 'servis',
                'historias-clinicas' => 'clinica',
                default              => $row->slug,
            };
            \Illuminate\Support\Facades\DB::table('products')
                ->where('id', $row->id)
                ->update(['public_domain' => $publicDomain]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unique('public_domain');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['public_domain']);
            $table->dropColumn('public_domain');
        });
    }
};
