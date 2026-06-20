<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add billing config columns to properties
        Schema::table('properties', function (Blueprint $table) {
            $table->enum('electricity_config', ['all_in', 'token', 'postpaid'])->default('all_in')->after('maps_url');
        });
        
        Schema::table('properties', function (Blueprint $table) {
            $table->enum('water_config', ['all_in', 'pdam', 'pump'])->default('all_in')->after('electricity_config');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('ipl_amount', 10, 2)->default(0)->after('yearly_discount_percent');
        });

        // 2. Convert 'rumah' to 'kontrakan' before altering the enum
        DB::statement("UPDATE properties SET type = 'kontrakan' WHERE type = 'rumah'");

        // 3. Alter properties.type enum to remove 'rumah'
        DB::statement("ALTER TABLE properties MODIFY COLUMN type ENUM('kos','kontrakan','apartemen') NOT NULL DEFAULT 'kos'");

        // 4. Alter invoices.type enum to add 'ipl' and 'deposit'
        DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('rent','electricity','water','ipl','deposit') NOT NULL");

        // 5. Add platform_fee and gateway_fee to payments
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('platform_fee', 10, 2)->default(0)->after('amount');
        });
        
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('gateway_fee', 10, 2)->default(0)->after('platform_fee');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['electricity_config', 'water_config']);
        });

        DB::statement("ALTER TABLE properties MODIFY COLUMN type ENUM('kos','kontrakan','apartemen','rumah') NOT NULL DEFAULT 'kos'");
        DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('rent','electricity','water') NOT NULL");

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['platform_fee', 'gateway_fee']);
        });
    }
};
