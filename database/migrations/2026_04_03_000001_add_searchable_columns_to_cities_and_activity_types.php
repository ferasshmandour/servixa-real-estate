<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cities: add virtual generated columns + indexes for fast search
        Schema::table('cities', function (Blueprint $table) {
            $table->string('name_ar', 255)->virtualAs("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar'))")->nullable()->after('name');
            $table->string('name_en', 255)->virtualAs("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en'))")->nullable()->after('name_ar');
            $table->index('name_ar');
            $table->index('name_en');
        });

        // Activity types: add virtual generated columns + indexes for fast search
        Schema::table('activity_types', function (Blueprint $table) {
            $table->string('name_ar', 255)->virtualAs("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar'))")->nullable()->after('name');
            $table->string('name_en', 255)->virtualAs("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en'))")->nullable()->after('name_ar');
            $table->index('name_ar');
            $table->index('name_en');
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex(['name_ar']);
            $table->dropIndex(['name_en']);
            $table->dropColumn(['name_ar', 'name_en']);
        });

        Schema::table('activity_types', function (Blueprint $table) {
            $table->dropIndex(['name_ar']);
            $table->dropIndex(['name_en']);
            $table->dropColumn(['name_ar', 'name_en']);
        });
    }
};
