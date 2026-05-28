<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use App\Models\LeaseContract;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContractSeeder extends Seeder
{
    public function run()
    {
        // Ambil unit yang statusnya occupied
        $occupiedUnits = Unit::where('status', 'occupied')->with('property')->get();

        // Ambil tenant yang tersedia
        $tenants = User::where('role', 'tenant')->get();

        if ($tenants->isEmpty() || $occupiedUnits->isEmpty()) {
            $this->command->warn('Tidak ada tenant atau unit occupied. Skip ContractSeeder.');
            return;
        }

        $tenantPool = $tenants->shuffle();
        $index = 0;

        foreach ($occupiedUnits as $unit) {
            $tenant = $tenantPool[$index % $tenantPool->count()];
            $manager = $unit->property->manager ?? User::where('role', 'manager')->first();

            // Skip jika manager tidak ada
            if (!$manager) continue;

            $startDate = now()->subMonths(rand(3, 12))->startOfMonth();
            $endDate   = $startDate->copy()->addYear();

            $contract = LeaseContract::updateOrCreate(
                ['tenant_id' => $tenant->id, 'unit_id' => $unit->id],
                [
                    'contract_number'  => 'REODA-CTR-' . strtoupper(Str::random(8)),
                    'manager_id'       => $manager->id,
                    'start_date'       => $startDate,
                    'end_date'         => $endDate,
                    'rental_type'      => 'monthly',
                    'rent_amount'      => $unit->rent_price,
                    'deposit_amount'   => $unit->rent_price,
                    'status'           => 'active',
                ]
            );

            $index++;
        }

        $this->command->info('ContractSeeder: ' . LeaseContract::count() . ' kontrak dibuat.');
    }
}
