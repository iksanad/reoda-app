<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('type', [
                'payment_due',        // Pengingat jatuh tempo
                'payment_received',   // Pembayaran diterima (ke pengelola)
                'payment_approved',   // Pembayaran disetujui (ke penyewa)
                'payment_rejected',   // Pembayaran ditolak (ke penyewa)
                'contract_expiring',  // Kontrak akan berakhir
                'contract_renewed',   // Kontrak diperpanjang
                'contract_terminated',// Kontrak diakhiri
                'facility_request',   // Permintaan fasilitas
                'emergency_report',   // Laporan darurat
                'manager_approved',   // Pengelola disetujui superadmin
                'general'             // Notifikasi umum
            ])->default('general');
            $table->string('link')->nullable()->comment('URL terkait notifikasi');
            $table->morphs('notifiable');     // polymorphic: bisa ke payment, contract, dll
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });

        // Global settings untuk Superadmin
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general')->comment('Grup setting: general, payment, notification, etc');
            $table->string('label')->nullable()->comment('Label tampilan di UI');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notifications');
    }
};
