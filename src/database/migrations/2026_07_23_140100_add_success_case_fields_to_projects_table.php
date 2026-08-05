<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('public_name')->nullable()->after('name');
            $table->text('commercial_description')->nullable()->after('public_name');
            $table->text('problem_solved')->nullable()->after('commercial_description');
            $table->json('key_features')->nullable()->after('problem_solved');
            $table->unsignedInteger('display_order')->default(0)->after('key_features');
            $table->boolean('is_featured')->default(false)->after('display_order');
            $table->boolean('show_publicly')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'public_name', 'commercial_description', 'problem_solved', 'key_features',
                'display_order', 'is_featured', 'show_publicly',
            ]);
        });
    }
};
