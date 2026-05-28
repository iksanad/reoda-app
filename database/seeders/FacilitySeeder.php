<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitySeeder extends Seeder
{
    public function run()
    {
        $data = [
            'WiFi', 'AC', 'Kamar Mandi Dalam', 'Parkir', 'Laundry',
            'Kolam Renang', 'Gym', 'Dapur Bersama',
        ];

        foreach ($data as $f) {
            Facility::firstOrCreate(['name' => $f]);
        }
    }
}
