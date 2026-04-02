<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('subcategory_id')->nullable()->constrained('categories');
            $table->json('title');       // translatable
            $table->json('description'); // translatable
            $table->integer('available_quantity')->default(1);
            $table->string('main_image');
            $table->enum('type', ['sale', 'rent']);
            $table->decimal('price', 12, 2);
            $table->enum('currency', ['USD', 'SYP']);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('category_id');
            $table->index('business_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
