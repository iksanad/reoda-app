<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = WalletTransaction::where('user_id', $user->id)->latest()->paginate(10);
        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->paginate(10);

        return view('manager.wallet.index', compact('user', 'transactions', 'withdrawals'));
    }

    public function withdraw(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:10000|max:' . $user->balance,
            'bank_name' => 'required|string',
            'bank_account' => 'required|string',
            'account_name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Deduct balance
            $user->decrement('balance', $request->amount);

            // Record transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $request->amount,
                'description' => 'Penarikan Dana ke ' . $request->bank_name . ' - ' . $request->bank_account,
            ]);

            // Create withdrawal request
            Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'account_name' => $request->account_name,
                'status' => 'pending',
            ]);

            DB::commit();
            return back()->with('success', 'Permintaan penarikan dana berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses penarikan dana.');
        }
    }
}
