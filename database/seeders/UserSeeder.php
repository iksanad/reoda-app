<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ═══════════════════════════════════════════════════
        //  SUPERADMIN
        // ═══════════════════════════════════════════════════
        User::updateOrCreate(
            ['email' => 'superadmin@reoda.com'],
            [
                'name'       => 'Superadmin REODA',
                'password'   => Hash::make('password'),
                'role'       => 'superadmin',
                'phone'      => '081111111111',
                'user_code'  => 'SA-001',
                'notif_email'    => true,
                'notif_due_date' => true,
            ]
        );

        // ═══════════════════════════════════════════════════
        //  PENGELOLA — AKUN UTAMA (data lengkap & kaya)
        // ═══════════════════════════════════════════════════

        // Akun utama #1 — Ardyana Azza
        User::updateOrCreate(
            ['email' => 'ardyanaazza@gmail.com'],
            [
                'name'                => 'Ardyana Azza',
                'password'            => Hash::make('password'),
                'role'                => 'manager',
                'phone'               => '081234567890',
                'user_code'           => 'MGR-001',
                'id_number'           => '3509876543210001',
                'date_of_birth'       => '2000-03-15',
                'address'             => 'Jl. Kalimantan No. 37, Sumbersari, Jember',
                'referral_code'       => 'ARDYANA',
                'manager_status'      => 'approved',
                'bank_name'           => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name'   => 'Ardyana Azza',
                'notif_email'         => true,
                'notif_due_date'      => true,
            ]
        );

        // Akun utama #2 — Iksanarya
        User::updateOrCreate(
            ['email' => 'iksanarya123@gmail.com'],
            [
                'name'                => 'Iksanarya',
                'password'            => Hash::make('password'),
                'role'                => 'manager',
                'phone'               => '082233445566',
                'user_code'           => 'MGR-002',
                'id_number'           => '3571234567890002',
                'date_of_birth'       => '2001-07-10',
                'address'             => 'Jl. Mastrip No. 12, Patrang, Jember',
                'referral_code'       => 'IKSANAR',
                'manager_status'      => 'approved',
                'bank_name'           => 'BRI',
                'bank_account_number' => '9876543210',
                'bank_account_name'   => 'Iksanarya',
                'notif_email'         => true,
                'notif_due_date'      => true,
            ]
        );

        // ═══════════════════════════════════════════════════
        //  PENGELOLA — AKUN DUMMY (variasi berbagai kondisi)
        // ═══════════════════════════════════════════════════

        // Dummy #1 — Approved, punya properti
        User::updateOrCreate(
            ['email' => 'pengelola@reoda.com'],
            [
                'name'                => 'Budi Hartono',
                'password'            => Hash::make('password'),
                'role'                => 'manager',
                'phone'               => '085511223344',
                'user_code'           => 'MGR-003',
                'referral_code'       => 'BUDIHRT',
                'manager_status'      => 'approved',
                'bank_name'           => 'Mandiri',
                'bank_account_number' => '1122334455',
                'bank_account_name'   => 'Budi Hartono',
                'notif_email'         => true,
                'notif_due_date'      => true,
            ]
        );

        // Dummy #2 — Approved, punya properti
        User::updateOrCreate(
            ['email' => 'pengelola2@reoda.com'],
            [
                'name'                => 'Siti Rahayu',
                'password'            => Hash::make('password'),
                'role'                => 'manager',
                'phone'               => '087722334455',
                'user_code'           => 'MGR-004',
                'referral_code'       => 'SITIRHY',
                'manager_status'      => 'approved',
                'bank_name'           => 'BNI',
                'bank_account_number' => '5544332211',
                'bank_account_name'   => 'Siti Rahayu',
                'notif_email'         => false,
                'notif_due_date'      => true,
            ]
        );

        // Dummy #3 — Approved, baru saja bergabung, belum ada penyewa
        User::updateOrCreate(
            ['email' => 'pengelola3@reoda.com'],
            [
                'name'                => 'Ahmad Fauzi',
                'password'            => Hash::make('password'),
                'role'                => 'manager',
                'phone'               => '081298765432',
                'user_code'           => 'MGR-005',
                'referral_code'       => 'AHMADFZ',
                'manager_status'      => 'approved',
                'bank_name'           => 'BSI',
                'bank_account_number' => '7788991122',
                'bank_account_name'   => 'Ahmad Fauzi',
                'notif_email'         => true,
                'notif_due_date'      => false,
            ]
        );

        // Dummy #4 — Approved, propertinya inactive/kosong semua
        User::updateOrCreate(
            ['email' => 'pengelola4@reoda.com'],
            [
                'name'                => 'Dewi Kusuma',
                'password'            => Hash::make('password'),
                'role'                => 'manager',
                'phone'               => '082111222333',
                'user_code'           => 'MGR-006',
                'referral_code'       => 'DEWIKUS',
                'manager_status'      => 'approved',
                'bank_name'           => 'BCA',
                'bank_account_number' => '3344556677',
                'bank_account_name'   => 'Dewi Kusuma',
                'notif_email'         => true,
                'notif_due_date'      => true,
            ]
        );

        // Dummy #5 — Status pending (untuk testing approval superadmin)
        User::updateOrCreate(
            ['email' => 'pendingmanager@reoda.com'],
            [
                'name'           => 'Rizki Pratama',
                'password'       => Hash::make('password'),
                'role'           => 'manager',
                'phone'          => '081355667788',
                'user_code'      => 'MGR-007',
                'manager_status' => 'pending',
                'notif_email'    => true,
                'notif_due_date' => true,
            ]
        );

        // ═══════════════════════════════════════════════════
        //  PENYEWA — AKUN UTAMA (data lengkap)
        // ═══════════════════════════════════════════════════

        // Akun utama #1 — Ardyana Azza (sebagai penyewa)
        User::updateOrCreate(
            ['email' => 'ardyana.azza26@smp.belajar.id'],
            [
                'name'           => 'Ardyana Azza',
                'password'       => Hash::make('password'),
                'role'           => 'tenant',
                'phone'          => '089987654321',
                'user_code'      => 'TNT-001',
                'id_number'      => '3509876543210010',
                'date_of_birth'  => '2000-03-15',
                'address'        => 'Jl. Kalimantan No. 37, Sumbersari, Jember',
                'notif_email'    => true,
                'notif_due_date' => true,
            ]
        );

        // Akun utama #2 — Iksanad (sebagai penyewa)
        User::updateOrCreate(
            ['email' => 'iksanad10rpl2@gmail.com'],
            [
                'name'           => 'Iksanad',
                'password'       => Hash::make('password'),
                'role'           => 'tenant',
                'phone'          => '083344556677',
                'user_code'      => 'TNT-002',
                'id_number'      => '3571234567890020',
                'date_of_birth'  => '2001-07-10',
                'address'        => 'Jl. Mastrip No. 12, Patrang, Jember',
                'notif_email'    => true,
                'notif_due_date' => true,
            ]
        );

        // ═══════════════════════════════════════════════════
        //  PENYEWA — AKUN DUMMY (variasi berbagai kondisi)
        // ═══════════════════════════════════════════════════
        $dummyTenants = [
            // penyewa aktif, punya kontrak, data lengkap
            ['email' => 'penyewa@reoda.com',  'name' => 'Nadia Fitriani',  'phone' => '085667788990', 'code' => 'TNT-003', 'id_num' => '3578901234560001', 'dob' => '1999-04-20', 'address' => 'Jl. Colombo No. 45, Depok, Yogyakarta'],
            // penyewa aktif, punya kontrak, data lengkap
            ['email' => 'penyewa2@reoda.com', 'name' => 'Hendra Gunawan', 'phone' => '082199887766', 'code' => 'TNT-004', 'id_num' => '3209876543210002', 'dob' => '1998-11-05', 'address' => 'Jl. Ahmad Yani No. 120, Gayungan, Surabaya'],
            // penyewa aktif, kontrak hampir habis bulan ini
            ['email' => 'penyewa3@reoda.com', 'name' => 'Mega Wulandari',  'phone' => '087712345678', 'code' => 'TNT-005', 'id_num' => '3302345678900003', 'dob' => '2000-08-17', 'address' => 'Jl. Soekarno-Hatta No. 88, Lowokwaru, Malang'],
            // penyewa baru, belum punya kontrak (baru daftar)
            ['email' => 'penyewa4@reoda.com', 'name' => 'Fajar Nugroho',   'phone' => '083311223344', 'code' => 'TNT-006', 'id_num' => null, 'dob' => null, 'address' => null],
            // penyewa baru, belum punya kontrak (baru daftar)
            ['email' => 'penyewa5@reoda.com', 'name' => 'Lina Setiawati',  'phone' => '081400112233', 'code' => 'TNT-007', 'id_num' => null, 'dob' => null, 'address' => null],
            // penyewa lama, kontrak sudah berakhir (expired)
            ['email' => 'penyewa6@reoda.com', 'name' => 'Tono Widodo',     'phone' => '089922334455', 'code' => 'TNT-008', 'id_num' => '3309988776650006', 'dob' => '1995-02-28', 'address' => 'Jl. Colombo No. 10, Depok, Yogyakarta'],
            // penyewa aktif, ada invoice overdue
            ['email' => 'penyewa7@reoda.com', 'name' => 'Putri Anggraini', 'phone' => '082277889900', 'code' => 'TNT-009', 'id_num' => '3504321098760007', 'dob' => '2002-06-14', 'address' => 'Jl. Kalimantan No. 5, Sumbersari, Jember'],
        ];

        foreach ($dummyTenants as $t) {
            User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name'           => $t['name'],
                    'password'       => Hash::make('password'),
                    'role'           => 'tenant',
                    'phone'          => $t['phone'],
                    'user_code'      => $t['code'],
                    'id_number'      => $t['id_num'],
                    'date_of_birth'  => $t['dob'],
                    'address'        => $t['address'],
                    'notif_email'    => true,
                    'notif_due_date' => true,
                ]
            );
        }
    }
}
