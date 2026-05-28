<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('manager.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'nullable|string|max:20',
            'bank_name'          => 'nullable|string|max:100',
            'bank_account_number'=> 'nullable|string|max:50',
            'bank_account_name'  => 'nullable|string|max:100',
            'current_password'   => 'nullable|string',
            'new_password'       => 'nullable|string|min:8|confirmed',
        ]);

        // Verify current password if changing password
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name                = $validated['name'];
        $user->phone               = $validated['phone'] ?? null;
        $user->bank_name           = $validated['bank_name'] ?? null;
        $user->bank_account_number = $validated['bank_account_number'] ?? null;
        $user->bank_account_name   = $validated['bank_account_name'] ?? null;
        $user->save();

        return redirect()->route('manager.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
