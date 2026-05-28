<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        return view('tenant.settings.index');
    }

    public function update(Request $request)
    {
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
