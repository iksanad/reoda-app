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
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE lease_contracts MODIFY COLUMN end_date DATE NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE lease_contracts MODIFY COLUMN end_date DATE NOT NULL');
    }
};
