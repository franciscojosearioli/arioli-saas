<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('logo_path')->nullable()->after('slug');
            $table->string('cover_image')->nullable()->after('logo_path');
            $table->string('category')->nullable()->after('cover_image');
            $table->text('short_description')->nullable()->after('category');
            $table->text('challenge')->nullable()->after('short_description');
            $table->text('solution')->nullable()->after('challenge');
            $table->text('results')->nullable()->after('solution');
            $table->text('testimonial_quote')->nullable()->after('results');
            $table->string('testimonial_author')->nullable()->after('testimonial_quote');
            $table->string('testimonial_position')->nullable()->after('testimonial_author');
            $table->boolean('show_on_landing')->default(false)->after('testimonial_position');
            $table->unsignedInteger('display_order')->default(0)->after('show_on_landing');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'logo_path', 'cover_image', 'category', 'short_description',
                'challenge', 'solution', 'results',
                'testimonial_quote', 'testimonial_author', 'testimonial_position',
                'show_on_landing', 'display_order',
            ]);
        });
    }
};
