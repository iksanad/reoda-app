<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'payment_due',
            'payment_received',
            'payment_approved',
            'payment_rejected',
            'contract_expiring',
            'contract_renewed',
            'contract_terminated',
            'facility_request',
            'emergency_report',
            'manager_approved',
            'general',
            'contract_requested',
            'contract_approved',
            'contract_rejected',
            'payment_manager_received',
            'withdrawal_approved',
            'withdrawal_rejected'
        ) DEFAULT 'general'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'payment_due',
            'payment_received',
            'payment_approved',
            'payment_rejected',
            'contract_expiring',
            'contract_renewed',
            'contract_terminated',
            'facility_request',
            'emergency_report',
            'manager_approved',
            'general',
            'contract_requested',
            'contract_approved',
            'contract_rejected',
            'payment_manager_received'
        ) DEFAULT 'general'");
    }
};
