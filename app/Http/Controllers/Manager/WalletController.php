<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\Notification;
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
            'amount'       => 'required|numeric|min:10000',
            'bank_name'    => 'required|string',
            'bank_account' => 'required|string',
            'account_name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Lock the user row to prevent race conditions
            $freshUser = User::lockForUpdate()->findOrFail($user->id);

            $availableBalance = $freshUser->balance - $freshUser->balance_hold;

            if ($request->amount > $availableBalance) {
                DB::rollBack();
                return back()->with('error', 'Saldo yang tersedia tidak mencukupi. Saldo tersedia: Rp ' . number_format($availableBalance, 0, ',', '.'));
            }

            // Move amount from balance to balance_hold
            $freshUser->increment('balance_hold', $request->amount);

            $balanceAfter = $freshUser->balance - $freshUser->balance_hold;

            // Create withdrawal record with PENDING status
            $withdrawal = Withdrawal::create([
                'user_id'      => $freshUser->id,
                'amount'       => $request->amount,
                'bank_name'    => $request->bank_name,
                'bank_account' => $request->bank_account,
                'account_name' => $request->account_name,
                'status'       => 'PENDING',
            ]);

            // Record in ledger
            WalletTransaction::create([
                'user_id'      => $freshUser->id,
                'type'         => 'WITHDRAW',
                'amount'       => $request->amount,
                'balance_after'=> $balanceAfter,
                'reference_id' => 'WD-' . $withdrawal->id,
                'description'  => 'Permintaan penarikan ke ' . $request->bank_name . ' (' . $request->bank_account . ')',
            ]);

            DB::commit();
            return back()->with('success', 'Permintaan penarikan dana sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil diajukan. Sedang diproses oleh tim REODA.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses penarikan dana. Silakan coba lagi.');
        }
    }
}
