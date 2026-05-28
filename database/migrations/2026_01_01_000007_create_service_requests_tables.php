<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Permintaan penambahan fasilitas dari penyewa ke pengelola
        Schema::create('facility_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('manager_id')->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected', 'done'])->default('pending');
            $table->text('manager_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        // Laporan darurat / emergency report dari penyewa
        Schema::create('emergency_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('manager_id')->constrained('users');
            $table->enum('category', ['electricity', 'water', 'structural', 'security', 'other'])->default('other');
            $table->string('title');
            $table->text('description');
            $table->string('photo')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('manager_response')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // Permintaan perpanjangan/pembatalan kontrak
        Schema::create('contract_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_contract_id')->constrained('lease_contracts')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users');
            $table->foreignId('manager_id')->constrained('users');
            $table->enum('type', ['renewal', 'termination'])->comment('Jenis permintaan');
            $table->date('requested_date')->nullable()->comment('Tanggal perpanjangan yang diinginkan');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('manager_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_requests');
        Schema::dropIfExists('emergency_reports');
        Schema::dropIfExists('facility_requests');
    }
};
