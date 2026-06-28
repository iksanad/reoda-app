<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('payment_due', 'payment_received', 'payment_approved', 'payment_rejected', 'contract_expiring', 'contract_renewed', 'contract_terminated', 'facility_request', 'emergency_report', 'manager_approved', 'general', 'contract_requested') DEFAULT 'general'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications_type', function (Blueprint $table) {
            //
        });
    }
};
