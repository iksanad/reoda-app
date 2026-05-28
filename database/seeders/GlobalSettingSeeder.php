<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'REODA'],
            ['key' => 'site_tagline', 'value' => 'Solusi Hunian Terpercaya'],
            ['key' => 'contact_email', 'value' => 'support@reoda.com'],
            ['key' => 'contact_phone', 'value' => '021-12345678'],
            ['key' => 'default_late_fee_percent', 'value' => '5'],
            ['key' => 'max_late_fee_percent', 'value' => '10'],
            ['key' => 'default_grace_period_days', 'value' => '3'],
            ['key' => 'max_grace_period_days', 'value' => '7'],
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com'],
            ['key' => 'smtp_port', 'value' => '587'],
            ['key' => 'smtp_email', 'value' => ''],
            ['key' => 'smtp_password', 'value' => ''],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
