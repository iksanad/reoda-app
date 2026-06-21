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

        // Get the active contract to detect PLN/PDAM config
        $activeContract = \App\Models\LeaseContract::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with('unit.property')
            ->latest()
            ->first();

        $query = Invoice::where('tenant_id', $user->id)
            ->with(['leaseContract.unit.property', 'payments' => fn($q) => $q->latest()->first()])
            // Priority: unpaid → pending → paid, then newest first
            ->orderByRaw("FIELD(status, 'unpaid', 'pending', 'paid', 'expired')")
            ->orderBy('due_date', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('year')) {
            $query->where('billing_year', $request->year);
        }

        if ($request->filled('month')) {
            $query->where('billing_month', $request->month);
        }

        $invoices = $query->paginate(12)->appends(request()->query());

        $counts = [
            'all'     => Invoice::where('tenant_id', $user->id)->count(),
            'unpaid'  => Invoice::where('tenant_id', $user->id)->where('status', 'unpaid')->count(),
            'pending' => Invoice::where('tenant_id', $user->id)->where('status', 'pending')->count(),
            'paid'    => Invoice::where('tenant_id', $user->id)->where('status', 'paid')->count(),
        ];

        // Get available years for filter dropdown
        $availableYears = Invoice::where('tenant_id', $user->id)
            ->selectRaw('DISTINCT billing_year')
            ->orderBy('billing_year', 'desc')
            ->pluck('billing_year');

        return view('tenant.transactions.index', compact('invoices', 'counts', 'availableYears', 'activeContract'));
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->tenant_id !== Auth::id()) {
            abort(403);
        }

        $invoice->load(['leaseContract.unit.property.manager', 'payments' => fn($q) => $q->latest()]);

        $snapToken      = null;
        $discountAmount = 0;
        $platformFee    = 0;
        $gatewayFee     = 0;

        if ($invoice->status === 'unpaid') {
            // Calculate tiered platform fee from global settings
            $amount = (float) $invoice->amount;
            $tier1Max    = (float) (\App\Models\Setting::getValue('fee_tier_1_max', 1000000));
            $tier1Amount = (float) (\App\Models\Setting::getValue('fee_tier_1_amount', 5000));
            $tier2Max    = (float) (\App\Models\Setting::getValue('fee_tier_2_max', 3000000));
            $tier2Amount = (float) (\App\Models\Setting::getValue('fee_tier_2_amount', 10000));
            $tier3Amount = (float) (\App\Models\Setting::getValue('fee_tier_3_amount', 15000));
            $gatewayFee  = (float) (\App\Models\Setting::getValue('gateway_fee_fixed', 4000));

            if ($amount <= $tier1Max) {
                $platformFee = $tier1Amount;
            } elseif ($amount <= $tier2Max) {
                $platformFee = $tier2Amount;
            } else {
                $platformFee = $tier3Amount;
            }

            $totalAmount = $amount + $platformFee + $gatewayFee;

            // Apply referral discount if available
            if (Auth::user()->discount_quota > 0) {
                $discountAmount = 50000;
                if ($totalAmount - $discountAmount < 10000) {
                    $discountAmount = $totalAmount - 10000;
                }
                if ($discountAmount > 0) {
                    $totalAmount -= $discountAmount;
                }
            }

            $typeLabels = [
                'rent'        => 'Sewa Hunian',
                'electricity' => 'Tagihan Listrik',
                'water'       => 'Tagihan Air',
                'ipl'         => 'IPL / Maintenance Fee',
                'deposit'     => 'Deposit / Uang Jaminan',
            ];
            $itemName = ($typeLabels[$invoice->type] ?? ucfirst($invoice->type))
                . ': ' . ($invoice->leaseContract->unit->property->name ?? '');

            // Setup Midtrans
            Config::$serverKey    = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            // Reuse existing pending payment's order_id to prevent duplicates on page refresh
            $existingPayment = Payment::where('invoice_id', $invoice->id)
                ->where('status', 'pending')
                ->whereNotNull('midtrans_order_id')
                ->latest()
                ->first();

            if ($existingPayment) {
                $orderId = $existingPayment->midtrans_order_id;
            } else {
                $orderId = $invoice->invoice_number . '-' . time();
            }

            $itemDetails = [
                ['id' => 'ITEM-' . $invoice->id, 'price' => (int)$amount,       'quantity' => 1, 'name' => $itemName],
                ['id' => 'FEE-PLATFORM',           'price' => (int)$platformFee,  'quantity' => 1, 'name' => 'Biaya Admin REODA'],
                ['id' => 'FEE-PG',                 'price' => (int)$gatewayFee,   'quantity' => 1, 'name' => 'Biaya Payment Gateway'],
            ];
            if ($discountAmount > 0) {
                $itemDetails[] = ['id' => 'DISC-REF', 'price' => -(int)$discountAmount, 'quantity' => 1, 'name' => 'Voucher Diskon Referral'];
            }

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => (int)$totalAmount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email'      => Auth::user()->email,
                    'phone'      => Auth::user()->phone ?? '',
                ],
                'item_details' => $itemDetails,
                'callbacks'    => [
                    'finish' => route('tenant.transactions.index'),
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);

                // Pre-create payment record so webhook can find it by order_id
                // Do NOT change invoice status here — let the webhook handle it
                if (!$existingPayment) {
                    Payment::create([
                        'payment_code'       => 'PAY-' . strtoupper(Str::random(10)),
                        'invoice_id'         => $invoice->id,
                        'tenant_id'          => Auth::id(),
                        'manager_id'         => $invoice->manager_id,
                        'amount'             => $invoice->amount,
                        'platform_fee'       => $platformFee,
                        'gateway_fee'        => $gatewayFee,
                        'payment_method'     => 'midtrans',
                        'midtrans_order_id'  => $orderId,
                        'status'             => 'pending',
                        // NOTE: invoice status stays 'unpaid' until webhook confirms
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Midtrans snap token error: ' . $e->getMessage());
            }
        }

        return view('tenant.transactions.show', compact('invoice', 'snapToken', 'discountAmount', 'platformFee', 'gatewayFee'));
    }

    public function pay(Request $request, Invoice $invoice)
    {
        // This endpoint is now handled by Midtrans Snap directly in show()
        // and confirmed automatically via webhook. Redirect back.
        return redirect()->route('tenant.transactions.show', $invoice)
            ->with('info', 'Silakan gunakan tombol Bayar Sekarang untuk melanjutkan pembayaran.');
    }
}
