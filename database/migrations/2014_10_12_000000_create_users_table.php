<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['superadmin', 'manager', 'tenant'])->default('tenant');
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->string('id_number', 20)->nullable()->comment('NIK / KTP');
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            // Manager specific fields
            $table->string('referral_code', 10)->nullable()->unique()->comment('Kode unik pengelola untuk penyewa');
            $table->enum('manager_status', ['pending', 'approved', 'rejected'])->nullable()->comment('Status approval pengelola oleh superadmin');
            $table->text('manager_notes')->nullable()->comment('Catatan dari superadmin');
            // Tenant specific fields - bank account for payment
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            // Notification preferences
            $table->boolean('notif_email')->default(true);
            $table->boolean('notif_due_date')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
