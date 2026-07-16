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
        DB::statement('ALTER TABLE notifications MODIFY COLUMN notifiable_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE notifications MODIFY COLUMN notifiable_type VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert not fully supported safely if there are NULLs
    }
};
