<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Facility;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // ── Ambil fasilitas ───────────────────────────────
        $wifi    = Facility::where('name', 'WiFi')->first();
        $ac      = Facility::where('name', 'AC')->first();
        $kmDlm   = Facility::where('name', 'Kamar Mandi Dalam')->first();
        $parkir  = Facility::where('name', 'Parkir')->first();
        $laundry = Facility::where('name', 'Laundry')->first();
        $kolam   = Facility::where('name', 'Kolam Renang')->first();
        $gym     = Facility::where('name', 'Gym')->first();
        $dapur   = Facility::where('name', 'Dapur Bersama')->first();

        // ═══════════════════════════════════════════════════════
        //  PENGELOLA UTAMA #1 — Ardyana Azza  (2 properti)
        // ═══════════════════════════════════════════════════════
        $mArdyana = User::where('email', 'ardyanaazza@gmail.com')->first();

        // P-001: Kos Melati Jember — kos eksklusif, beberapa unit occupied
        $p1 = Property::updateOrCreate(['property_code' => 'REODA-001'], [
            'manager_id'  => $mArdyana->id,
            'name'        => 'Kos Melati Jember',
            'type'        => 'kos',
            'description' => 'Kos eksklusif pusat kota Jember, dekat kampus UNEJ. Lingkungan aman, nyaman, dan fasilitas lengkap.',
            'address'     => 'Jl. Kalimantan No. 37',
            'district'    => 'Sumbersari',
            'city'        => 'Jember',
            'province'    => 'Jawa Timur',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p1, [$wifi, $ac, $kmDlm, $parkir]);
        $this->createUnits($p1->id, [
            // [kode, nama, tipe, harga/bln, luas_m2, status]
            ['MLT-A01', 'Kamar A1', 'standard', 750000,  14, 'occupied'],
            ['MLT-A02', 'Kamar A2', 'standard', 750000,  14, 'occupied'],
            ['MLT-A03', 'Kamar A3', 'standard', 750000,  14, 'available'],
            ['MLT-B01', 'Kamar B1', 'deluxe',  1150000,  20, 'occupied'],
            ['MLT-B02', 'Kamar B2', 'deluxe',  1150000,  20, 'available'],
        ]);

        // P-002: Rumah Kos Bunda Jember — kos putri, beberapa kosong
        $p2 = Property::updateOrCreate(['property_code' => 'REODA-002'], [
            'manager_id'  => $mArdyana->id,
            'name'        => 'Rumah Kos Bunda Jember',
            'type'        => 'kos',
            'description' => 'Kos putri nyaman dekat RS dr. Soebandi Jember. Lingkungan tenang dan keamanan 24 jam.',
            'address'     => 'Jl. Mastrip No. 12',
            'district'    => 'Patrang',
            'city'        => 'Jember',
            'province'    => 'Jawa Timur',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p2, [$wifi, $kmDlm, $parkir]);
        $this->createUnits($p2->id, [
            ['BND-01', 'Kamar 1', 'standard', 650000, 12, 'occupied'],
            ['BND-02', 'Kamar 2', 'standard', 650000, 12, 'available'],
            ['BND-03', 'Kamar 3', 'standard', 650000, 12, 'available'],
        ]);

        // ═══════════════════════════════════════════════════════
        //  PENGELOLA UTAMA #2 — Iksanarya  (2 properti)
        // ═══════════════════════════════════════════════════════
        $mIksanar = User::where('email', 'iksanarya123@gmail.com')->first();

        // P-003: Kontrakan Sejahtera Malang
        $p3 = Property::updateOrCreate(['property_code' => 'REODA-003'], [
            'manager_id'  => $mIksanar->id,
            'name'        => 'Kontrakan Sejahtera Malang',
            'type'        => 'kontrakan',
            'description' => 'Kontrakan nyaman di pusat Malang, cocok untuk keluarga kecil. Lingkungan aman dan strategis.',
            'address'     => 'Jl. Soekarno-Hatta No. 88',
            'district'    => 'Lowokwaru',
            'city'        => 'Malang',
            'province'    => 'Jawa Timur',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p3, [$wifi, $parkir, $laundry, $dapur]);
        $this->createUnits($p3->id, [
            ['SJH-01', 'Unit A', 'standard', 1600000, 45, 'occupied'],
            ['SJH-02', 'Unit B', 'standard', 1600000, 45, 'occupied'],
            ['SJH-03', 'Unit C', 'deluxe',   2100000, 60, 'available'],
        ]);

        // P-004: Apartemen Grand Surabaya
        $p4 = Property::updateOrCreate(['property_code' => 'REODA-004'], [
            'manager_id'  => $mIksanar->id,
            'name'        => 'Apartemen Grand Surabaya',
            'type'        => 'apartemen',
            'description' => 'Apartemen modern dengan fasilitas kolam renang dan gym. Lokasi strategis di jantung kota Surabaya.',
            'address'     => 'Jl. Ahmad Yani No. 120',
            'district'    => 'Gayungan',
            'city'        => 'Surabaya',
            'province'    => 'Jawa Timur',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p4, [$wifi, $ac, $kmDlm, $parkir, $kolam, $gym]);
        $this->createUnits($p4->id, [
            ['GRD-S01', 'Studio 1',  'standard', 2600000, 28, 'occupied'],
            ['GRD-S02', 'Studio 2',  'standard', 2600000, 28, 'available'],
            ['GRD-1BR', '1 Bedroom', 'deluxe',   3700000, 42, 'occupied'],
            ['GRD-2BR', '2 Bedroom', 'vip',      5200000, 65, 'available'],
        ]);

        // ═══════════════════════════════════════════════════════
        //  PENGELOLA DUMMY #1 — Budi Hartono (approved, ada penyewa)
        // ═══════════════════════════════════════════════════════
        $mBudi = User::where('email', 'pengelola@reoda.com')->first();

        $p5 = Property::updateOrCreate(['property_code' => 'REODA-005'], [
            'manager_id'  => $mBudi->id,
            'name'        => 'Kos Putra Mandiri Yogyakarta',
            'type'        => 'kos',
            'description' => 'Kos putra eksklusif di Yogyakarta, dekat UGM dan UNY. Harga terjangkau dengan fasilitas memadai.',
            'address'     => 'Jl. Colombo No. 45',
            'district'    => 'Depok',
            'city'        => 'Yogyakarta',
            'province'    => 'DI Yogyakarta',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p5, [$wifi, $parkir, $laundry, $dapur]);
        $this->createUnits($p5->id, [
            ['PMD-01', 'Kamar 1', 'standard', 550000, 12, 'occupied'],
            ['PMD-02', 'Kamar 2', 'standard', 550000, 12, 'available'],
            ['PMD-03', 'Kamar 3', 'standard', 550000, 12, 'available'],
            ['PMD-04', 'Kamar 4', 'deluxe',   950000, 18, 'occupied'],
        ]);

        // ═══════════════════════════════════════════════════════
        //  PENGELOLA DUMMY #2 — Siti Rahayu (approved, ada penyewa)
        // ═══════════════════════════════════════════════════════
        $mSiti = User::where('email', 'pengelola2@reoda.com')->first();

        $p6 = Property::updateOrCreate(['property_code' => 'REODA-006'], [
            'manager_id'  => $mSiti->id,
            'name'        => 'Kos Permata Bandung',
            'type'        => 'kos',
            'description' => 'Kos mewah di Bandung, dekat ITB dan Dago. Suasana sejuk khas kota Bandung.',
            'address'     => 'Jl. Dago No. 55',
            'district'    => 'Coblong',
            'city'        => 'Bandung',
            'province'    => 'Jawa Barat',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p6, [$wifi, $ac, $kmDlm, $parkir, $laundry]);
        $this->createUnits($p6->id, [
            ['PMT-01', 'Kamar A', 'standard', 900000,  14, 'occupied'],
            ['PMT-02', 'Kamar B', 'standard', 900000,  14, 'occupied'],
            ['PMT-03', 'Kamar C', 'deluxe',  1400000,  20, 'available'],
        ]);

        // ═══════════════════════════════════════════════════════
        //  PENGELOLA DUMMY #3 — Ahmad Fauzi (baru bergabung, belum ada penyewa)
        // ═══════════════════════════════════════════════════════
        $mAhmad = User::where('email', 'pengelola3@reoda.com')->first();

        $p7 = Property::updateOrCreate(['property_code' => 'REODA-007'], [
            'manager_id'  => $mAhmad->id,
            'name'        => 'Kos Baru Semarang',
            'type'        => 'kos',
            'description' => 'Kos baru di Semarang. Masih dalam tahap pemasaran unit. Harga perdana spesial.',
            'address'     => 'Jl. Pemuda No. 20',
            'district'    => 'Semarang Tengah',
            'city'        => 'Semarang',
            'province'    => 'Jawa Tengah',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p7, [$wifi, $parkir]);
        $this->createUnits($p7->id, [
            ['KBS-01', 'Kamar 1', 'standard', 600000, 12, 'available'],
            ['KBS-02', 'Kamar 2', 'standard', 600000, 12, 'available'],
        ]);

        // ═══════════════════════════════════════════════════════
        //  PENGELOLA DUMMY #4 — Dewi Kusuma (properti inactive)
        // ═══════════════════════════════════════════════════════
        $mDewi = User::where('email', 'pengelola4@reoda.com')->first();

        $p8 = Property::updateOrCreate(['property_code' => 'REODA-008'], [
            'manager_id'  => $mDewi->id,
            'name'        => 'Kontrakan Lama Bekasi',
            'type'        => 'kontrakan',
            'description' => 'Kontrakan lama, sedang dalam proses renovasi.',
            'address'     => 'Jl. Raya Bekasi No. 77',
            'district'    => 'Bekasi Timur',
            'city'        => 'Bekasi',
            'province'    => 'Jawa Barat',
            'status'      => 'inactive',
        ]);
        $this->syncFacilities($p8, [$parkir]);
        $this->createUnits($p8->id, [
            ['KLB-01', 'Unit 1', 'standard', 1200000, 36, 'available'],
            ['KLB-02', 'Unit 2', 'standard', 1200000, 36, 'available'],
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    private function syncFacilities(Property $property, array $facilities): void
    {
        $ids = collect($facilities)->filter()->pluck('id')->toArray();
        if (!empty($ids)) {
            $property->facilities()->sync($ids);
        }
    }

    private function createUnits(int $propertyId, array $units): void
    {
        foreach ($units as [$code, $name, $type, $price, $area, $status]) {
            Unit::updateOrCreate(
                ['unit_code' => $code],
                [
                    'property_id' => $propertyId,
                    'name'        => $name,
                    'type'        => $type,   // standard | deluxe | vip
                    'rent_price'  => $price,
                    'area_sqm'    => $area,
                    'status'      => $status, // available | occupied
                ]
            );
        }
    }
}
