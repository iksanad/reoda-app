@component('mail::message')
# Pembayaran Diterima (Otomatis Lunas)

Halo **{{ $payment->invoice->manager->name }}**,

Penyewa Anda, **{{ $payment->invoice->tenant->name }}**, telah berhasil melakukan pembayaran untuk tagihan **{{ $payment->invoice->invoice_number }}**. Pembayaran ini telah diverifikasi otomatis oleh sistem.

**Rincian Pembayaran:**
- **Tagihan:** {{ $payment->invoice->invoice_number }}
- **Kategori:** {{ ucfirst($payment->invoice->type) }}
- **Properti/Unit:** {{ $payment->invoice->leaseContract->unit->property->name ?? '-' }} / {{ $payment->invoice->leaseContract->unit->unit_code ?? '-' }}
- **Total Pembayaran:** Rp {{ number_format($payment->amount, 0, ',', '.') }}
- **Potongan Admin REODA:** Rp {{ number_format($payment->platform_fee, 0, ',', '.') }}
- **Pendapatan Bersih Anda:** Rp {{ number_format($payment->amount - $payment->platform_fee, 0, ',', '.') }}
- **Metode Pembayaran:** {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}
- **Tanggal:** {{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}

Dana bersih telah ditambahkan ke saldo Wallet Anda. Anda bisa mencairkan saldo tersebut kapan saja melalui Dashboard Pengelola.

@component('mail::button', ['url' => route('manager.payments.index')])
Lihat Pembayaran
@endcomponent

Terima kasih telah menggunakan REODA.<br>
Tim {{ config('app.name') }}
@endcomponent
