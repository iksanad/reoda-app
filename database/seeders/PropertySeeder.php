<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    public function run()
    {
        $wifi    = Facility::where('name', 'WiFi')->first();
        $ac      = Facility::where('name', 'AC')->first();
        $kmDlm   = Facility::where('name', 'Kamar Mandi Dalam')->first();
        $parkir  = Facility::where('name', 'Parkir')->first();
        $laundry = Facility::where('name', 'Laundry')->first();
        $kolam   = Facility::where('name', 'Kolam Renang')->first();
        $gym     = Facility::where('name', 'Gym')->first();
        $dapur   = Facility::where('name', 'Dapur Bersama')->first();

        // ─── Manager 1 (existing) ─────────────────────────────
        $m1 = User::where('email', 'pengelola@reoda.com')->first();

        $p1 = Property::updateOrCreate(['property_code' => 'REODA-001'], [
            'manager_id'  => $m1->id,
            'name'        => 'Kos Melati Jember',
            'type'        => 'kos',
            'description' => 'Kos eksklusif pusat kota Jember, dekat kampus UNEJ, fasilitas lengkap.',
            'address'     => 'Jl. Kalimantan No. 37',
            'district'    => 'Sumbersari',
            'city'        => 'Jember',
            'province'    => 'Jawa Timur',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p1, array_filter([$wifi?->id, $ac?->id, $kmDlm?->id, $parkir?->id]));
        $this->createUnits($p1->id, [
            ['MLT-A01', 'Kamar A1', 'standard', 700000, 14, 'available'],
            ['MLT-A02', 'Kamar A2', 'standard', 700000, 14, 'occupied'],
            ['MLT-B01', 'Kamar B1', 'deluxe',   1100000, 20, 'available'],
        ]);

        // ─── Manager 2 ────────────────────────────────────────
        $m2 = User::updateOrCreate(['email' => 'pengelola2@reoda.com'], [
            'name'                => 'Pengelola 2',
            'password'            => Hash::make('password'),
            'role'                => 'manager',
            'phone'               => '082233445566',
            'referral_code'       => strtoupper(Str::random(6)),
            'manager_status'      => 'approved',
            'bank_name'           => 'BRI',
            'bank_account_number' => '9876543210',
            'bank_account_name'   => 'Pengelola 2',
        ]);
        $p2 = Property::updateOrCreate(['property_code' => 'REODA-002'], [
            'manager_id'  => $m2->id,
            'name'        => 'Kontrakan Sejahtera Malang',
            'type'        => 'kontrakan',
            'description' => 'Kontrakan nyaman di pusat Malang, cocok untuk keluarga, lingkungan aman.',
            'address'     => 'Jl. Soekarno-Hatta No. 88',
            'district'    => 'Lowokwaru',
            'city'        => 'Malang',
            'province'    => 'Jawa Timur',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p2, array_filter([$wifi?->id, $parkir?->id, $laundry?->id, $dapur?->id]));
        $this->createUnits($p2->id, [
            ['SJH-01', 'Unit 1', 'standard', 1500000, 45, 'available'],
            ['SJH-02', 'Unit 2', 'standard', 1500000, 45, 'available'],
            ['SJH-03', 'Unit 3', 'deluxe',   2000000, 60, 'occupied'],
        ]);

        // ─── Manager 3 ────────────────────────────────────────
        $m3 = User::updateOrCreate(['email' => 'pengelola3@reoda.com'], [
            'name'                => 'Pengelola 3',
            'password'            => Hash::make('password'),
            'role'                => 'manager',
            'phone'               => '085567890123',
            'referral_code'       => strtoupper(Str::random(6)),
            'manager_status'      => 'approved',
            'bank_name'           => 'BNI',
            'bank_account_number' => '1122334455',
            'bank_account_name'   => 'Pengelola 3',
        ]);
        $p3 = Property::updateOrCreate(['property_code' => 'REODA-003'], [
            'manager_id'  => $m3->id,
            'name'        => 'Apartemen Grand Surabaya',
            'type'        => 'apartemen',
            'description' => 'Apartemen modern dengan fasilitas kolam renang dan gym, lokasi strategis Surabaya.',
            'address'     => 'Jl. Ahmad Yani No. 120',
            'district'    => 'Gayungan',
            'city'        => 'Surabaya',
            'province'    => 'Jawa Timur',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p3, array_filter([$wifi?->id, $ac?->id, $kmDlm?->id, $parkir?->id, $kolam?->id, $gym?->id]));
        $this->createUnits($p3->id, [
            ['GRD-S01', 'Studio 1',   'standard', 2500000, 28, 'available'],
            ['GRD-S02', 'Studio 2',   'standard', 2500000, 28, 'available'],
            ['GRD-1BR', '1 Bedroom',  'deluxe',   3500000, 42, 'available'],
            ['GRD-2BR', '2 Bedroom',  'vip',      5000000, 65, 'occupied'],
        ]);

        // ─── Manager 4 ────────────────────────────────────────
        $m4 = User::updateOrCreate(['email' => 'pengelola4@reoda.com'], [
            'name'                => 'Pengelola 4',
            'password'            => Hash::make('password'),
            'role'                => 'manager',
            'phone'               => '081298765432',
            'referral_code'       => strtoupper(Str::random(6)),
            'manager_status'      => 'approved',
            'bank_name'           => 'Mandiri',
            'bank_account_number' => '5544332211',
            'bank_account_name'   => 'Pengelola 4',
        ]);
        $p4 = Property::updateOrCreate(['property_code' => 'REODA-004'], [
            'manager_id'  => $m4->id,
            'name'        => 'Kos Putra Mandiri Yogyakarta',
            'type'        => 'kos',
            'description' => 'Kos putra eksklusif di Yogyakarta, dekat UGM dan UNY, harga terjangkau.',
            'address'     => 'Jl. Colombo No. 45',
            'district'    => 'Depok',
            'city'        => 'Yogyakarta',
            'province'    => 'DI Yogyakarta',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p4, array_filter([$wifi?->id, $parkir?->id, $laundry?->id, $dapur?->id]));
        $this->createUnits($p4->id, [
            ['PMD-01', 'Kamar 1', 'standard', 500000, 12, 'available'],
            ['PMD-02', 'Kamar 2', 'standard', 500000, 12, 'available'],
            ['PMD-03', 'Kamar 3', 'standard', 550000, 14, 'available'],
            ['PMD-04', 'Kamar 4', 'deluxe',   900000, 18, 'occupied'],
        ]);

        // ─── Properti ke-5 (Manager 1) ────────────────────────
        $p5 = Property::updateOrCreate(['property_code' => 'REODA-005'], [
            'manager_id'  => $m1->id,
            'name'        => 'Rumah Kos Bunda Jember',
            'type'        => 'kos',
            'description' => 'Kos putri nyaman dekat RS dr Soebandi, lingkungan tenang.',
            'address'     => 'Jl. Mastrip No. 12',
            'district'    => 'Patrang',
            'city'        => 'Jember',
            'province'    => 'Jawa Timur',
            'status'      => 'active',
        ]);
        $this->syncFacilities($p5, array_filter([$wifi?->id, $kmDlm?->id, $parkir?->id]));
        $this->createUnits($p5->id, [
            ['BND-01', 'Kamar 1', 'standard', 600000, 12, 'available'],
            ['BND-02', 'Kamar 2', 'standard', 600000, 12, 'available'],
        ]);
    }

    private function syncFacilities(Property $property, array $ids): void
    {
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
