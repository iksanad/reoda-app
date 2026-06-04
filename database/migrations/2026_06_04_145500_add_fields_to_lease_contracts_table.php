<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            // Add awaiting_approval to status enum
            // TiDB/MySQL: We recreate the enum
            $table->integer('contract_duration')->nullable()->comment('Durasi kontrak dalam bulan (untuk kontrakan/apartemen)');
            $table->enum('payment_cycle', ['monthly', 'yearly'])->default('monthly')->comment('Siklus pembayaran');
            $table->integer('tolerance_days')->default(7)->comment('Batas hari toleransi telat bayar (untuk kos)');
            $table->timestamp('tenant_sign_at')->nullable()->comment('Waktu penyewa setujui kontrak');
            $table->timestamp('manager_approved_at')->nullable()->comment('Waktu pengelola setujui kontrak');
        });

        // Modify status enum to include awaiting_approval
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE lease_contracts MODIFY COLUMN status ENUM('active','expired','terminated','pending','awaiting_approval') DEFAULT 'awaiting_approval'"
        );
    }

    public function down(): void
    {
        Schema::table('lease_contracts', function (Blueprint $table) {
            $table->dropColumn(['contract_duration', 'payment_cycle', 'tolerance_days', 'tenant_sign_at', 'manager_approved_at']);
        });

        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE lease_contracts MODIFY COLUMN status ENUM('active','expired','terminated','pending') DEFAULT 'pending'"
        );
    }
};
