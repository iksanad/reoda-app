<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add fields to units
        Schema::table('units', function (Blueprint $table) {
            $table->string('pln_customer_id')->nullable()->after('type');
            $table->string('pdam_customer_id')->nullable()->after('pln_customer_id');
        });

        // 2. Modify properties enum
        DB::statement("ALTER TABLE properties MODIFY COLUMN water_config ENUM('all_in','pdam','pump','postpaid') NOT NULL DEFAULT 'all_in'");
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['pln_customer_id', 'pdam_customer_id']);
        });

        // This might fail if there's any data with 'postpaid', but for down migration it's acceptable to risk it or just let it fail
        DB::statement("ALTER TABLE properties MODIFY COLUMN water_config ENUM('all_in','pdam','pump') NOT NULL DEFAULT 'all_in'");
    }
};
