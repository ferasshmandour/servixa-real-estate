<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('initiator_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->timestamps();

            $table->unique(['service_id', 'initiator_id', 'receiver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
