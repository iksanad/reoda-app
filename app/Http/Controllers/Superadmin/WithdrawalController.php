<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Notification;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with('user')->latest()->paginate(15);
        return view('superadmin.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Mark withdrawal as SUCCESS — transfer has been done manually by admin.
     * Deduct balance_hold and deduct balance accordingly.
     */
    public function approve(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if (!in_array($withdrawal->status, ['PENDING', 'PROCESSING'])) {
            return back()->with('error', 'Status penarikan tidak valid untuk dikonfirmasi.');
        }

        DB::beginTransaction();
        try {
            $user = User::lockForUpdate()->findOrFail($withdrawal->user_id);

            // Deduct from balance (actual money gone) and balance_hold
            $user->decrement('balance', $withdrawal->amount);
            $user->decrement('balance_hold', $withdrawal->amount);

            $balanceAfter = $user->balance - $user->balance_hold;

            $withdrawal->update([
                'status'       => 'SUCCESS',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Record final ledger entry
            WalletTransaction::create([
                'user_id'       => $withdrawal->user_id,
                'type'          => 'WITHDRAW',
                'amount'        => $withdrawal->amount,
                'balance_after' => $user->fresh()->balance,
                'reference_id'  => 'WD-' . $withdrawal->id,
                'description'   => 'Transfer berhasil — ' . $withdrawal->bank_name . ' (' . $withdrawal->bank_account . ')',
            ]);

            Notification::create([
                'user_id' => $withdrawal->user_id,
                'type'    => 'withdrawal_approved',
                'title'   => 'Penarikan Dana Berhasil ✅',
                'message' => 'Dana sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' telah berhasil ditransfer ke rekening ' . $withdrawal->bank_name . ' (' . $withdrawal->bank_account . ').',
            ]);

            DB::commit();
            return back()->with('success', 'Transfer dikonfirmasi. Saldo pengelola telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Reject withdrawal — return balance_hold back to available balance.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);

        if (!in_array($withdrawal->status, ['PENDING', 'PROCESSING'])) {
            return back()->with('error', 'Status penarikan tidak valid untuk ditolak.');
        }

        DB::beginTransaction();
        try {
            $user = User::lockForUpdate()->findOrFail($withdrawal->user_id);

            // Return balance_hold back to available
            $user->decrement('balance_hold', $withdrawal->amount);

            $withdrawal->update([
                'status'           => 'REJECTED',
                'rejection_reason' => $request->rejection_reason,
                'processed_by'     => auth()->id(),
                'processed_at'     => now(),
            ]);

            WalletTransaction::create([
                'user_id'       => $withdrawal->user_id,
                'type'          => 'WITHDRAW_REVERSAL',
                'amount'        => $withdrawal->amount,
                'balance_after' => $user->balance - $user->balance_hold,
                'reference_id'  => 'WD-' . $withdrawal->id,
                'description'   => 'Penarikan dibatalkan/ditolak. Saldo dikembalikan. Alasan: ' . $request->rejection_reason,
            ]);

            Notification::create([
                'user_id' => $withdrawal->user_id,
                'type'    => 'withdrawal_rejected',
                'title'   => 'Penarikan Dana Ditolak',
                'message' => 'Penarikan Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' ditolak dan saldo dikembalikan ke akun Anda. Alasan: ' . $request->rejection_reason,
            ]);

            DB::commit();
            return back()->with('success', 'Penarikan dana ditolak dan saldo dikembalikan ke pengelola.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}
