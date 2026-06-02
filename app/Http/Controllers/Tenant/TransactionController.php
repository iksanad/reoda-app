<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Mail\PaymentReceivedMail;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\LeaseContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Invoice::where('tenant_id', $user->id)
            ->with(['leaseContract.unit.property', 'payments' => fn($q) => $q->latest()->first()])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $invoices = $query->paginate(12)->appends(request()->query());

        $counts = [
            'all'     => Invoice::where('tenant_id', $user->id)->count(),
            'unpaid'  => Invoice::where('tenant_id', $user->id)->where('status', 'unpaid')->count(),
            'pending' => Invoice::where('tenant_id', $user->id)->where('status', 'pending')->count(),
            'paid'    => Invoice::where('tenant_id', $user->id)->where('status', 'paid')->count(),
        ];

        return view('tenant.transactions.index', compact('invoices', 'counts'));
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->tenant_id !== Auth::id()) {
            abort(403);
        }

        $invoice->load(['leaseContract.unit.property.manager', 'payments' => fn($q) => $q->latest()]);

        $snapToken = null;
        $discountAmount = 0;
        if ($invoice->status === 'unpaid') {
            // Setup Midtrans Config
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Admin fee & Gateway fee (Platform fee = 10k, Gateway = ~4k)
            $platformFee = 10000;
            $gatewayFee = 4000;
            $totalAmount = $invoice->amount + $platformFee + $gatewayFee;

            $params = [
                'transaction_details' => [
                    'order_id' => $invoice->invoice_number . '-' . time(),
                    'gross_amount' => $totalAmount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone,
                ],
                'item_details' => [
                    [
                        'id' => 'RENT-' . $invoice->id,
                        'price' => $invoice->amount,
                        'quantity' => 1,
                        'name' => 'Sewa: ' . $invoice->leaseContract->unit->property->name,
                    ],
                    [
                        'id' => 'FEE-PLATFORM',
                        'price' => $platformFee,
                        'quantity' => 1,
                        'name' => 'Biaya Admin Reoda',
                    ],
                    [
                        'id' => 'FEE-PG',
                        'price' => $gatewayFee,
                        'quantity' => 1,
                        'name' => 'Biaya Payment Gateway',
                    ]
                ]
            ];

            // Apply Discount if available
            if (Auth::user()->discount_quota > 0) {
                $discountAmount = 50000;
                // Ensure total amount doesn't go below minimum (Midtrans min is usually 10k or 1k, we set 10k)
                if ($totalAmount - $discountAmount < 10000) {
                    $discountAmount = $totalAmount - 10000;
                }
                
                if ($discountAmount > 0) {
                    $totalAmount -= $discountAmount;
                    $params['transaction_details']['gross_amount'] = $totalAmount;
                    $params['item_details'][] = [
                        'id' => 'DISC-REF',
                        'price' => -$discountAmount,
                        'quantity' => 1,
                        'name' => 'Voucher Diskon Referral',
                    ];
                }
            }

            try {
                $snapToken = Snap::getSnapToken($params);
            } catch (\Exception $e) {
                // Ignore if fails
            }
        }

        return view('tenant.transactions.show', compact('invoice', 'snapToken', 'discountAmount'));
    }

    public function pay(Request $request, Invoice $invoice)
    {
        if ($invoice->tenant_id !== Auth::id()) {
            abort(403);
        }

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice ini sudah lunas.');
        }

        $request->validate([
            'payment_method'  => 'required|in:transfer,cash',
            'bank_name'       => 'required_if:payment_method,transfer|nullable|string|max:100',
            'bank_account'    => 'required_if:payment_method,transfer|nullable|string|max:100',
            'proof_of_payment'=> 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'proof_of_payment.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_of_payment.image'    => 'File harus berupa gambar.',
            'proof_of_payment.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        // Store proof image
        $path = $request->file('proof_of_payment')->store('payments/proofs', 'public');

        $payment = Payment::create([
            'payment_code'     => 'PAY-' . strtoupper(Str::random(10)),
            'invoice_id'       => $invoice->id,
            'tenant_id'        => Auth::id(),
            'manager_id'       => $invoice->manager_id,
            'amount'           => $invoice->amount,
            'payment_method'   => $request->payment_method,
            'bank_name'        => $request->bank_name,
            'bank_account'     => $request->bank_account,
            'proof_of_payment' => $path,
            'status'           => 'pending',
            'paid_at'          => now(),
        ]);

        // Update invoice status to pending (waiting manager approval)
        $invoice->update(['status' => 'pending']);

        // Kirim email ke pengelola
        try {
            $payment->load('invoice.leaseContract.unit.property.manager');
            $manager = $invoice->leaseContract->unit->property->manager ?? null;
            if ($manager && $manager->email) {
                Mail::to($manager->email)->send(new PaymentReceivedMail($payment));
            }
        } catch (\Exception $e) {
            // Email gagal, tapi jangan block proses
        }

        return redirect()->route('tenant.transactions.show', $invoice)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Pengelola akan segera mengkonfirmasi.');
    }
}
