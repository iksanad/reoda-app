<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lease_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number', 30)->unique()->comment('Nomor kontrak unik, misal: REODA-CTR-2025-001');
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained('users');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('rental_type', ['monthly', 'quarterly', 'semi_annual', 'annual'])->default('monthly');
            $table->decimal('rent_amount', 12, 2)->comment('Harga sewa sesuai kontrak');
            $table->decimal('deposit_amount', 12, 2)->default(0)->comment('Uang deposit');
            $table->enum('status', ['active', 'expired', 'terminated', 'pending'])->default('pending');
            $table->string('contract_document')->nullable()->comment('Path file PDF kontrak');
            $table->text('notes')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->text('termination_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lease_contracts');
    }
};
