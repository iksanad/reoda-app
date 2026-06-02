<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use App\Models\LeaseContract;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────
        // Semua kontrak didefinisikan secara eksplisit agar tanggal,
        // penyewa, dan unit saling konsisten dan terlihat realistis.
        // ─────────────────────────────────────────────────────────────

        $contracts = [
            // ── Kos Melati Jember (REODA-001) ─────────────────────
            // Penyewa Utama #1: Ardyana Azza.26 — aktif 9 bulan lalu
            [
                'tenant_email'  => 'ardyana.azza26@smp.belajar.id',
                'unit_code'     => 'MLT-A01',
                'start'         => now()->subMonths(9)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],
            // Penyewa Utama #2: Iksanad — aktif 6 bulan lalu
            [
                'tenant_email'  => 'iksanad10rpl2@gmail.com',
                'unit_code'     => 'MLT-B01',
                'start'         => now()->subMonths(6)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],
            // Dummy penyewa: Nadia — aktif 5 bulan lalu (ada invoice overdue nanti)
            [
                'tenant_email'  => 'penyewa7@reoda.com',
                'unit_code'     => 'MLT-A02',
                'start'         => now()->subMonths(5)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],

            // ── Rumah Kos Bunda Jember (REODA-002) ────────────────
            // Dummy penyewa: Hendra — aktif 4 bulan lalu
            [
                'tenant_email'  => 'penyewa2@reoda.com',
                'unit_code'     => 'BND-01',
                'start'         => now()->subMonths(4)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],

            // ── Kontrakan Sejahtera Malang (REODA-003) ─────────────
            // Dummy penyewa: Mega — aktif, kontrak hampir habis (1 bulan lagi)
            [
                'tenant_email'  => 'penyewa3@reoda.com',
                'unit_code'     => 'SJH-01',
                'start'         => now()->subMonths(11)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => 'Kontrak akan berakhir bulan depan.',
            ],
            // Dummy penyewa: Tono — kontrak sudah expired (selesai 2 bulan lalu)
            [
                'tenant_email'  => 'penyewa6@reoda.com',
                'unit_code'     => 'SJH-02',
                'start'         => now()->subMonths(14)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'expired',
                'note'          => 'Kontrak telah berakhir, unit kembali tersedia.',
            ],

            // ── Apartemen Grand Surabaya (REODA-004) ──────────────
            // Dummy penyewa: Nadia — aktif 3 bulan lalu
            [
                'tenant_email'  => 'penyewa@reoda.com',
                'unit_code'     => 'GRD-S01',
                'start'         => now()->subMonths(3)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],
            // Dummy penyewa: Hendra — aktif 7 bulan lalu
            [
                'tenant_email'  => 'penyewa2@reoda.com',
                'unit_code'     => 'GRD-1BR',
                'start'         => now()->subMonths(7)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],

            // ── Kos Putra Mandiri Yogyakarta (REODA-005) ──────────
            // Dummy penyewa: Nadia — aktif 8 bulan lalu
            [
                'tenant_email'  => 'penyewa@reoda.com',
                'unit_code'     => 'PMD-01',
                'start'         => now()->subMonths(8)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],
            // Dummy penyewa: Putri — aktif 2 bulan lalu
            [
                'tenant_email'  => 'penyewa7@reoda.com',
                'unit_code'     => 'PMD-04',
                'start'         => now()->subMonths(2)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],

            // ── Kos Permata Bandung (REODA-006) ───────────────────
            // Dummy: Hendra — aktif 10 bulan lalu
            [
                'tenant_email'  => 'penyewa2@reoda.com',
                'unit_code'     => 'PMT-01',
                'start'         => now()->subMonths(10)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],
            // Dummy: Mega — aktif 5 bulan lalu
            [
                'tenant_email'  => 'penyewa3@reoda.com',
                'unit_code'     => 'PMT-02',
                'start'         => now()->subMonths(5)->startOfMonth(),
                'duration_mo'   => 12,
                'rental_type'   => 'monthly',
                'status'        => 'active',
                'note'          => null,
            ],
        ];

        foreach ($contracts as $def) {
            $tenant = User::where('email', $def['tenant_email'])->first();
            $unit   = Unit::where('unit_code', $def['unit_code'])->with('property')->first();

            if (!$tenant || !$unit) {
                $this->command->warn("Skip: tenant {$def['tenant_email']} atau unit {$def['unit_code']} tidak ditemukan.");
                continue;
            }

            $manager   = $unit->property->manager ?? null;
            if (!$manager) continue;

            $startDate = Carbon::parse($def['start']);
            $endDate   = $startDate->copy()->addMonths($def['duration_mo'])->subDay();

            LeaseContract::updateOrCreate(
                ['tenant_id' => $tenant->id, 'unit_id' => $unit->id],
                [
                    'contract_number' => 'REODA-CTR-' . strtoupper($unit->unit_code) . '-' . $startDate->format('Ym'),
                    'manager_id'      => $manager->id,
                    'start_date'      => $startDate,
                    'end_date'        => $endDate,
                    'rental_type'     => $def['rental_type'],
                    'rent_amount'     => $unit->rent_price,
                    'deposit_amount'  => $unit->rent_price,
                    'status'          => $def['status'],
                    'notes'           => $def['note'],
                ]
            );
        }

        $this->command->info('ContractSeeder: ' . LeaseContract::count() . ' kontrak dibuat.');
    }
}
