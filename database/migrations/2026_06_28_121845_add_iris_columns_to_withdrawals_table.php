<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('iris_reference_no')->nullable()->after('rejection_reason');
            $table->decimal('admin_fee', 15, 2)->default(5000)->after('amount');
            $table->decimal('amount_transferred', 15, 2)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['iris_reference_no', 'admin_fee', 'amount_transferred']);
        });
    }
};
