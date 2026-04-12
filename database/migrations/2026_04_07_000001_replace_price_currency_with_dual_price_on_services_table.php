<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price', 'currency']);
            $table->decimal('price_syp', 12, 2)->nullable()->after('type');
            $table->decimal('price_usd', 10, 2)->nullable()->after('price_syp');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price_syp', 'price_usd']);
            $table->decimal('price', 12, 2)->after('type');
            $table->enum('currency', ['USD', 'SYP'])->after('price');
        });
    }
};
