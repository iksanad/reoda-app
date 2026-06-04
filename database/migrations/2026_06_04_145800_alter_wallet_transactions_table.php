<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify type enum to more descriptive types
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM('credit','debit','SALE','WITHDRAW','WITHDRAW_REVERSAL','REFUND','ADJUSTMENT') NOT NULL"
        );

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->decimal('balance_after', 15, 2)->nullable()->comment('Snapshot saldo setelah transaksi (untuk audit)');
        });
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM('credit','debit') NOT NULL"
        );

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn('balance_after');
        });
    }
};
