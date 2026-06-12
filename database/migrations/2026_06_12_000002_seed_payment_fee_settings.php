<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $fees = [
            // Tier-based platform fees
            ['key' => 'fee_tier_1_max',    'value' => '1000000', 'group' => 'payment', 'label' => 'Batas atas tier 1 (Rp)'],
            ['key' => 'fee_tier_1_amount', 'value' => '5000',    'group' => 'payment', 'label' => 'Biaya admin tier 1 (≤ Rp 1 juta)'],
            ['key' => 'fee_tier_2_max',    'value' => '3000000', 'group' => 'payment', 'label' => 'Batas atas tier 2 (Rp)'],
            ['key' => 'fee_tier_2_amount', 'value' => '10000',   'group' => 'payment', 'label' => 'Biaya admin tier 2 (≤ Rp 3 juta)'],
            ['key' => 'fee_tier_3_amount', 'value' => '15000',   'group' => 'payment', 'label' => 'Biaya admin tier 3 (> Rp 3 juta)'],
            ['key' => 'gateway_fee_fixed', 'value' => '4000',    'group' => 'payment', 'label' => 'Biaya payment gateway (tetap per transaksi)'],
        ];

        foreach ($fees as $fee) {
            DB::table('settings')->updateOrInsert(
                ['key' => $fee['key']],
                array_merge($fee, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'fee_tier_1_max', 'fee_tier_1_amount',
            'fee_tier_2_max', 'fee_tier_2_amount',
            'fee_tier_3_amount', 'gateway_fee_fixed',
        ])->delete();
    }
};
