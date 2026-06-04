<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('terms_accepted_at')->nullable()->comment('Kapan pengelola menyetujui T&C Reoda');
            $table->decimal('balance_hold', 15, 2)->default(0)->comment('Saldo yang sedang dalam proses withdrawal');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['terms_accepted_at', 'balance_hold']);
        });
    }
};
