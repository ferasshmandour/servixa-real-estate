<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Which business account the initiator is acting as ("wallet" concept).
            // NULL = the initiator is chatting as a plain user.
            // The receiver is always the service owner, so its business account is
            // derivable as service.business_account_id (no column needed).
            $table->foreignId('initiator_business_account_id')
                  ->nullable()
                  ->after('initiator_id')
                  ->constrained('business_accounts')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('initiator_business_account_id');
        });
    }
};
