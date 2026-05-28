<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@reoda.com'],
            [
                'name' => 'Superadmin REODA',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'phone' => '081111111111',
            ]
        );

        // 2. Manager (Pengelola)
        $manager = User::updateOrCreate(
            ['email' => 'pengelola@reoda.com'],
            [
                'name' => 'Pengelola 1',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '082222222222',
                'referral_code' => strtoupper(Str::random(6)),
                'manager_status' => 'approved',
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'Pengelola 1',
            ]
        );

        // 3. Tenants (Penyewa) — 5 akun demo
        $tenants = [
            ['email' => 'penyewa@reoda.com',   'name' => 'Penyewa 1',           'phone' => '083333333333'],
            ['email' => 'penyewa2@reoda.com',  'name' => 'Budi Santoso',        'phone' => '085511223344'],
            ['email' => 'penyewa3@reoda.com',  'name' => 'Siti Rahayu',         'phone' => '087722334455'],
            ['email' => 'penyewa4@reoda.com',  'name' => 'Ahmad Fauzi',         'phone' => '081234567890'],
            ['email' => 'penyewa5@reoda.com',  'name' => 'Dewi Kusuma',         'phone' => '089987654321'],
            ['email' => 'penyewa6@reoda.com',  'name' => 'Rizki Pratama',       'phone' => '082111222333'],
            ['email' => 'penyewa7@reoda.com',  'name' => 'Nadia Fitriani',      'phone' => '081355667788'],
        ];

        foreach ($tenants as $tenant) {
            User::updateOrCreate(
                ['email' => $tenant['email']],
                [
                    'name'     => $tenant['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'tenant',
                    'phone'    => $tenant['phone'],
                ]
            );
        }

        // 4. Manager pending (untuk demo approval superadmin)
        User::updateOrCreate(
            ['email' => 'pendingmanager@reoda.com'],
            [
                'name'           => 'Calon Pengelola',
                'password'       => Hash::make('password'),
                'role'           => 'manager',
                'phone'          => '081199887766',
                'manager_status' => 'pending',
            ]
        );
    }
}
