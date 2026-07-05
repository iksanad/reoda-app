<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Mail\PaymentApprovedMail;
use App\Models\Payment;
use App\Exports\PaymentExport;
use App\Services\NotificationService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::select('payments.*')
            ->join('users as tenants', 'payments.tenant_id', '=', 'tenants.id')
            ->whereHas('invoice.leaseContract.unit.property', function ($q) {
                $q->where('manager_id', Auth::id());
            })
            ->with([
                'invoice.leaseContract.unit.property',
                'invoice.leaseContract.tenant',
                'tenant',
            ])
            ->orderByRaw("CASE WHEN payments.status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('payments.paid_at', 'desc')
            ->orderBy('tenants.name', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(15)->appends(request()->query());

        $managerQuery = fn($q) => $q->whereHas('invoice.leaseContract.unit.property', fn($p) => $p->where('manager_id', Auth::id()));
        $counts = [
            'all'      => Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', Auth::id()))->count(),
            'pending'  => Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', Auth::id()))->where('status','pending')->count(),
            'verified' => Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', Auth::id()))->where('status','verified')->count(),
            'rejected' => Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', Auth::id()))->where('status','rejected')->count(),
        ];

        $stats = [
            'total_revenue' => Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', Auth::id()))->where('status','verified')->sum('amount'),
            'verified'      => $counts['verified'],
            'pending'       => $counts['pending'],
            'total'         => $counts['all'],
        ];

        return view('manager.payments.index', compact('payments', 'counts', 'stats'));
    }

    public function show(Payment $payment)
    {
        // Ensure payment belongs to this manager's property
        if (!$payment->invoice->leaseContract->unit->property->manager_id === Auth::id()) {
            abort(403);
        }

        $payment->load([
            'invoice.leaseContract.unit.property',
            'invoice.leaseContract.tenant',
            'tenant',
        ]);

        return view('manager.payments.show', compact('payment'));
    }

    public function approve(Payment $payment)
    {
        $payment->update([
            'status'      => 'approved',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        // Mark related invoice as paid
        $invoice = $payment->invoice;
        $invoice->update(['status' => 'paid']);

        // Kredit saldo pengelola — pembayaran manual tidak ada platform_fee,
        // pengelola menerima penuh sesuai nominal tagihan
        $manager = Auth::user();
        $creditAmount = $invoice->amount;
        $balanceBefore = $manager->balance ?? 0;
        $manager->increment('balance', $creditAmount);

        \App\Models\WalletTransaction::create([
            'user_id'       => $manager->id,
            'type'          => 'CREDIT',
            'amount'        => $creditAmount,
            'balance_after' => $balanceBefore + $creditAmount,
            'reference_id'  => $payment->payment_code ?? 'PAY-' . $payment->id,
            'description'   => 'Pembayaran manual diverifikasi: ' . ($invoice->invoice_number ?? 'INV-' . $invoice->id),
        ]);

        \App\Models\Notification::create([
            'user_id'         => $manager->id,
            'type'            => 'wallet_credit',
            'title'           => 'Saldo Masuk: Rp ' . number_format($creditAmount, 0, ',', '.'),
            'message'         => 'Pembayaran manual dari penyewa untuk invoice ' . ($invoice->invoice_number ?? '') . ' telah Anda verifikasi. Saldo ditambahkan ke dompet Anda.',
            'notifiable_type' => \App\Models\Payment::class,
            'notifiable_id'   => $payment->id,
        ]);

        // Notify tenant via NotificationService (logs email to Superadmin Email Logs)
        $tenant = $invoice->leaseContract->tenant ?? $payment->tenant;
        if ($tenant) {
            app(NotificationService::class)->send(
                $tenant,
                'Pembayaran Dikonfirmasi ✅',
                'Pembayaran Anda untuk invoice ' . ($invoice->invoice_number ?? '') . ' telah dikonfirmasi oleh pengelola.',
                'payment_approved',
                route('tenant.transactions.index'),
                $payment
            );
        }

        return redirect()->route('manager.payments.show', $payment)
            ->with('success', 'Pembayaran berhasil dikonfirmasi. Email notifikasi telah dikirim ke penyewa.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $payment->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Revert invoice back to unpaid
        $payment->invoice->update(['status' => 'unpaid']);

        // Notify tenant via NotificationService (logs email to Superadmin Email Logs)
        $tenant = $payment->invoice->leaseContract->tenant ?? $payment->tenant;
        if ($tenant) {
            app(NotificationService::class)->send(
                $tenant,
                'Pembayaran Ditolak ❌',
                'Pembayaran Anda untuk invoice ' . ($payment->invoice->invoice_number ?? '') . ' ditolak. Alasan: ' . $request->rejection_reason,
                'payment_rejected',
                route('tenant.transactions.index'),
                $payment
            );
        }

        return redirect()->route('manager.payments.show', $payment)
            ->with('success', 'Pembayaran ditolak. Email notifikasi telah dikirim ke penyewa.');
    }

    public function export(Request $request)
    {
        $query = Payment::select('payments.*')
            ->join('users as tenants', 'payments.tenant_id', '=', 'tenants.id')
            ->whereHas('invoice.leaseContract.unit.property', function ($q) {
                $q->where('manager_id', Auth::id());
            })
            ->with([
                'invoice.leaseContract.unit.property',
                'invoice.leaseContract.tenant',
                'tenant',
            ])
            ->orderByRaw("CASE WHEN payments.status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('payments.paid_at', 'desc')
            ->orderBy('tenants.name', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payments = $query->get();

        return Excel::download(new PaymentExport($payments), 'data_pembayaran.xlsx');
    }
}
