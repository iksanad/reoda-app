<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Notification;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function upload(Request $r)
    {
        $r->validate([
            'invoice_id'=>'required',
            'file'=>'required|image'
        ]);

        $invoice = Invoice::findOrFail($r->invoice_id);

        $path = $r->file('file')->store('payments','public');

        $payment = Payment::create([
            'invoice_id'=>$invoice->invoice_id,
            'sender_name'=>auth()->user()->name,
            'amount'=>$invoice->amount,
            'transfer_date'=>now(),
            'proof_image'=>$path,
            'status'=>'pending'
        ]);

        $invoice->update(['status'=>'pending']);

        // notif ke owner
        Notification::create([
            'user_id'=>$invoice->rental->property->owner_id,
            'type'=>'payment_uploaded',
            'title'=>'Pembayaran Baru',
            'message'=>'Ada pembayaran baru menunggu verifikasi',
        ]);

        return response()->json(['msg'=>'uploaded']);
    }

    public function approve($id)
    {
        $payment = Payment::with('invoice.rental')->findOrFail($id);

        $payment->update([
            'status'=>'approved',
            'verified_by'=>auth()->id(),
            'verified_at'=>now()
        ]);

        $payment->invoice->update(['status'=>'paid']);

        // notif ke tenant
        Notification::create([
            'user_id'=>$payment->invoice->rental->tenant_id,
            'type'=>'payment_approved',
            'title'=>'Pembayaran Disetujui',
            'message'=>'Pembayaran Anda telah disetujui'
        ]);

        return response()->json(['msg'=>'approved']);
    }

    public function reject($id)
    {
        $payment = Payment::with('invoice.rental')->findOrFail($id);

        $payment->update(['status'=>'rejected']);

        $payment->invoice->update(['status'=>'unpaid']);

        Notification::create([
            'user_id'=>$payment->invoice->rental->tenant_id,
            'type'=>'payment_rejected',
            'title'=>'Pembayaran Ditolak',
            'message'=>'Silakan upload ulang bukti pembayaran'
        ]);

        return response()->json(['msg'=>'rejected']);
    }
}