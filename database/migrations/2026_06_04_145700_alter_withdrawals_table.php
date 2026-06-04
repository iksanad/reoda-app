<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify status enum to new marketplace statuses
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE withdrawals MODIFY COLUMN status ENUM('PENDING','PROCESSING','SUCCESS','FAILED','REJECTED','CANCELLED') DEFAULT 'PENDING'"
        );

        // Remove proof_of_transfer column (no longer needed)
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn('proof_of_transfer');
        });
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE withdrawals MODIFY COLUMN status ENUM('pending','approved','rejected') DEFAULT 'pending'"
        );

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('proof_of_transfer')->nullable();
        });
    }
};
