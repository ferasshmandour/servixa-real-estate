<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop service_images table (replaced by media table)
        Schema::dropIfExists('service_images');

        // Drop main_image column from services (replaced by media collection)
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('main_image');
        });

        // Drop business_account_files table (replaced by media table)
        Schema::dropIfExists('business_account_files');

        // Drop image column from sliders (replaced by media collection)
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('main_image')->nullable();
        });

        Schema::create('service_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->timestamps();
        });

        Schema::create('business_account_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_account_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->enum('file_type', ['document', 'image']);
            $table->timestamps();
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->string('image')->nullable();
        });
    }
};
