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

    public function approve(Request $request, $id)
    {
        $request->validate([
            'proof_of_transfer' => 'required|image|max:5120',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Status penarikan tidak valid.');
        }

        $path = $request->file('proof_of_transfer')->store('withdrawals', 'public');

        $withdrawal->update([
            'status' => 'approved',
            'proof_of_transfer' => $path,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $withdrawal->user_id,
            'type' => 'withdrawal_approved',
            'title' => 'Penarikan Dana Berhasil',
            'message' => 'Dana sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' telah ditransfer ke rekening Anda.',
        ]);

        return back()->with('success', 'Penarikan dana disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Status penarikan tidak valid.');
        }

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Refund balance
            $withdrawal->user->increment('balance', $withdrawal->amount);

            WalletTransaction::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'credit',
                'amount' => $withdrawal->amount,
                'description' => 'Pengembalian dana (Penarikan ditolak)',
            ]);

            Notification::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'withdrawal_rejected',
                'title' => 'Penarikan Dana Ditolak',
                'message' => 'Penarikan Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' ditolak. Alasan: ' . $request->rejection_reason,
            ]);

            DB::commit();
            return back()->with('success', 'Penarikan dana ditolak dan saldo dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}
