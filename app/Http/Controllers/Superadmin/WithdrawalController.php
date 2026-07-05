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
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Withdrawal::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $withdrawals = $query->paginate(15)->appends($request->query());
        return view('superadmin.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Mark withdrawal as SUCCESS — transfer via Midtrans Iris.
     */
    public function approve(Request $request, $id, \App\Services\MidtransIrisService $irisService)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if (!in_array($withdrawal->status, ['PENDING', 'PROCESSING'])) {
            return back()->with('error', 'Status penarikan tidak valid untuk dikonfirmasi.');
        }

        // Calculate amount to transfer
        $adminFee = 5000;
        $amountTransferred = $withdrawal->amount - $adminFee;

        if ($amountTransferred <= 0) {
            return back()->with('error', 'Nominal penarikan terlalu kecil setelah dipotong biaya admin.');
        }

        // Hit Midtrans Iris API
        $payoutData = [
            'beneficiary_name' => $withdrawal->account_name,
            'beneficiary_account' => $withdrawal->bank_account,
            'beneficiary_bank' => strtolower($withdrawal->bank_name),
            'beneficiary_email' => $withdrawal->user->email,
            'amount' => $amountTransferred,
            'notes' => 'Withdrawal Reoda - ' . $withdrawal->user->name,
        ];

        $irisResponse = $irisService->createPayout($payoutData);

        // Di lingkungan non-production (lokal/sandbox), bypass error Iris agar bisa diuji
        // Di production, error Iris harus ditangani dengan benar (jangan bypass)
        if (isset($irisResponse['error']) && $irisResponse['error'] === true) {
            if (app()->isProduction()) {
                return back()->with('error', 'Gagal mengirim transfer via Midtrans Iris: ' . $irisResponse['message']);
            }
            \Illuminate\Support\Facades\Log::warning('Midtrans Iris Error (Bypassed - Non-Production): ' . $irisResponse['message']);
            $referenceNo = 'MOCK-IRIS-' . uniqid();
        } else {
            $referenceNo = $irisResponse['payouts'][0]['reference_no'] ?? null;
        }

        DB::beginTransaction();
        try {
            $user = User::lockForUpdate()->findOrFail($withdrawal->user_id);

            // Deduct from balance (actual money gone) and balance_hold
            $user->decrement('balance', $withdrawal->amount);
            $user->decrement('balance_hold', $withdrawal->amount);

            $withdrawal->update([
                'status'             => 'SUCCESS',
                'admin_fee'          => $adminFee,
                'amount_transferred' => $amountTransferred,
                'iris_reference_no'  => $referenceNo,
                'processed_by'       => auth()->id(),
                'processed_at'       => now(),
            ]);

            // Record final ledger entry
            WalletTransaction::create([
                'user_id'       => $withdrawal->user_id,
                'type'          => 'WITHDRAW',
                'amount'        => $withdrawal->amount,
                'balance_after' => $user->fresh()->balance,
                'reference_id'  => 'WD-' . $withdrawal->id,
                'description'   => "Transfer berhasil (Iris $referenceNo) — {$withdrawal->bank_name} ({$withdrawal->bank_account})",
            ]);

            Notification::create([
                'user_id'         => $withdrawal->user_id,
                'type'            => 'withdrawal_approved',
                'title'           => 'Penarikan Dana Diproses ✅',
                'message'         => 'Dana sebesar Rp ' . number_format($amountTransferred, 0, ',', '.') . ' (setelah potongan admin Rp ' . number_format($adminFee, 0, ',', '.') . ') sedang ditransfer ke rekening ' . $withdrawal->bank_name . ' Anda via sistem otomatis.',
                'notifiable_type' => Withdrawal::class,
                'notifiable_id'   => $withdrawal->id,
            ]);

            DB::commit();
            return back()->with('success', 'Transfer Midtrans Iris berhasil dikirim. Saldo pengelola telah diperbarui.');
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
                'user_id'         => $withdrawal->user_id,
                'type'            => 'withdrawal_rejected',
                'title'           => 'Penarikan Dana Ditolak',
                'message'         => 'Penarikan Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' ditolak dan saldo dikembalikan ke akun Anda. Alasan: ' . $request->rejection_reason,
                'notifiable_type' => Withdrawal::class,
                'notifiable_id'   => $withdrawal->id,
            ]);

            DB::commit();
            return back()->with('success', 'Penarikan dana ditolak dan saldo dikembalikan ke pengelola.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}
