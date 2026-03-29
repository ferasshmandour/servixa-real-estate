<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_dynamic_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dynamic_field_id')->constrained()->cascadeOnDelete();
            $table->text('value');
            $table->timestamps();

            $table->unique(['service_id', 'dynamic_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_dynamic_values');
    }
};
