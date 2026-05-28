<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Invoices / tagihan yang dibuat oleh pengelola untuk penyewa
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique();
            $table->foreignId('lease_contract_id')->constrained('lease_contracts')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users');
            $table->foreignId('manager_id')->constrained('users');
            $table->enum('type', ['rent', 'electricity', 'water'])->comment('Jenis tagihan: sewa, listrik, air');
            $table->integer('billing_month')->comment('Bulan tagihan 1-12');
            $table->integer('billing_year');
            $table->decimal('amount', 12, 2);
            // For electricity/water
            $table->decimal('meter_start', 10, 2)->nullable()->comment('Meteran awal (kWh/m3)');
            $table->decimal('meter_end', 10, 2)->nullable()->comment('Meteran akhir (kWh/m3)');
            $table->decimal('price_per_unit', 10, 2)->nullable()->comment('Harga per kWh/m3');
            $table->date('due_date')->comment('Tanggal jatuh tempo');
            $table->enum('status', ['unpaid', 'pending_verification', 'paid', 'overdue'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Payments / bukti pembayaran dari penyewa
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code', 30)->unique();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users');
            $table->foreignId('manager_id')->constrained('users');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['transfer', 'cash', 'midtrans'])->default('transfer');
            $table->string('proof_of_payment')->nullable()->comment('Path file bukti bayar');
            $table->string('bank_name')->nullable()->comment('Bank asal transfer');
            $table->string('bank_account')->nullable()->comment('Nomor rekening pengirim');
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('paid_at')->nullable()->comment('Waktu pembayaran dikirim');
            $table->timestamp('verified_at')->nullable()->comment('Waktu diverifikasi pengelola');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
