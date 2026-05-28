<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class GlobalSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('superadmin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'                 => 'required|string|max:100',
            'site_tagline'              => 'nullable|string|max:200',
            'contact_email'             => 'nullable|email|max:100',
            'contact_phone'             => 'nullable|string|max:20',
            'default_late_fee_percent'  => 'nullable|numeric|min:0|max:100',
            'max_late_fee_percent'      => 'nullable|numeric|min:0|max:100',
            'default_grace_period_days' => 'nullable|integer|min:0|max:30',
            'max_grace_period_days'     => 'nullable|integer|min:0|max:30',
            'smtp_host'                 => 'nullable|string|max:100',
            'smtp_port'                 => 'nullable|integer',
            'smtp_email'                => 'nullable|email|max:100',
            'smtp_password'             => 'nullable|string|max:100',
        ]);

        $keys = [
            'site_name', 'site_tagline', 'contact_email', 'contact_phone', 
            'default_late_fee_percent', 'max_late_fee_percent', 
            'default_grace_period_days', 'max_grace_period_days',
            'smtp_host', 'smtp_port', 'smtp_email', 'smtp_password'
        ];

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key, '')]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan global berhasil disimpan.');
    }
}
