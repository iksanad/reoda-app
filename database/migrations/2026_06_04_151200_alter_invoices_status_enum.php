<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'failed' and 'pending' to invoices status enum
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid','pending','pending_verification','paid','overdue','failed') DEFAULT 'unpaid'"
        );
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid','pending_verification','paid','overdue') DEFAULT 'unpaid'"
        );
    }
};
