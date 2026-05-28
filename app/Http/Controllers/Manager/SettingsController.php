<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        return view('manager.settings.index');
    }

    public function update(Request $request)
    {
        // Placeholder for future logic
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
