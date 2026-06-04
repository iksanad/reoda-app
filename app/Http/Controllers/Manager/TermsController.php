<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function show()
    {
        return view('manager.terms');
    }

    public function accept(Request $request)
    {
        $request->user()->update(['terms_accepted_at' => now()]);
        return redirect()->route('manager.dashboard')->with('success', 'Selamat datang! Anda telah menyetujui Ketentuan Penggunaan REODA.');
    }
}
