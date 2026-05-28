<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran — REODA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f6fb; color: #1a202c; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(76,116,175,0.10); }
        .header { padding: 36px 40px 28px; text-align: center; }
        .header.approved { background: linear-gradient(135deg, #4C74AF 0%, #003648 100%); }
        .header.rejected { background: linear-gradient(135deg, #e53e3e 0%, #742a2a 100%); }
        .header img { height: 48px; margin-bottom: 12px; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 700; }
        .header p  { color: rgba(255,255,255,0.8); font-size: 13px; margin-top: 4px; }
        .badge-ok  { display: inline-block; background: #48bb78; color: #fff; font-size: 12px; font-weight: 700; padding: 4px 14px; border-radius: 99px; margin-top: 12px; }
        .badge-err { display: inline-block; background: #fc8181; color: #742a2a; font-size: 12px; font-weight: 700; padding: 4px 14px; border-radius: 99px; margin-top: 12px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #003648; margin-bottom: 8px; }
        .text { font-size: 14px; color: #4a5568; line-height: 1.7; margin-bottom: 20px; }
        .card { background: #f4f6fb; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; }
        .card-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .card-row:last-child { margin-bottom: 0; border-top: 1px dashed #c7d7ee; padding-top: 10px; }
        .card-label { font-size: 13px; color: #718096; }
        .card-value { font-size: 13px; font-weight: 600; color: #1a202c; }
        .card-value.amount { font-size: 18px; font-weight: 800; color: #4C74AF; }
        .notes-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #7a5000; line-height: 1.6; }
        .btn { display: block; width: fit-content; margin: 0 auto 20px; background: #4C74AF; color: #fff; font-size: 14px; font-weight: 700; padding: 12px 32px; border-radius: 10px; text-decoration: none; }
        .footer { background: #f4f6fb; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #a0aec0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header {{ $action === 'approved' ? 'approved' : 'rejected' }}">
        <img src="{{ asset('template/logo/Reoda-White.png') }}" alt="REODA">
        @if($action === 'approved')
            <h1>Pembayaran Dikonfirmasi! 🎉</h1>
            <p>Pengelola telah memverifikasi pembayaran Anda</p>
            <span class="badge-ok">✅ Lunas</span>
        @else
            <h1>Pembayaran Ditolak</h1>
            <p>Ada masalah dengan bukti pembayaran Anda</p>
            <span class="badge-err">❌ Ditolak</span>
        @endif
    </div>

    <div class="body">
        @php
            $invoice = $payment->invoice;
            $tenant  = $invoice->tenant ?? $payment->tenant;
        @endphp

        <p class="greeting">Halo, {{ $tenant->name ?? 'Penyewa' }}!</p>

        @if($action === 'approved')
        <p class="text">
            Kabar baik! Pembayaran tagihan Anda telah <strong>dikonfirmasi dan diverifikasi</strong> oleh pengelola. 
            Terima kasih telah melakukan pembayaran tepat waktu.
        </p>
        @else
        <p class="text">
            Kami mohon maaf, pembayaran Anda untuk tagihan berikut <strong>tidak dapat dikonfirmasi</strong> oleh pengelola. 
            Silakan upload ulang bukti pembayaran yang valid atau hubungi pengelola untuk informasi lebih lanjut.
        </p>
        @endif

        <div class="card">
            <div class="card-row">
                <span class="card-label">No. Invoice</span>
                <span class="card-value">{{ $invoice->invoice_number ?? "INV-{$invoice->id}" }}</span>
            </div>
            <div class="card-row">
                <span class="card-label">Jenis Tagihan</span>
                <span class="card-value">{{ ucfirst($invoice->type ?? 'rent') }}</span>
            </div>
            <div class="card-row">
                <span class="card-label">Nominal Dibayar</span>
                <span class="card-value amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($notes)
        <div class="notes-box">
            <strong>Catatan dari Pengelola:</strong><br>
            {{ $notes }}
        </div>
        @endif

        <a href="{{ url('/tenant/transactions') }}" class="btn">
            📄 Lihat Riwayat Transaksi
        </a>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem REODA. Jangan balas email ini.</p>
        <p style="margin-top:4px;">&copy; {{ date('Y') }} REODA — Solusi Hunian Terpercaya</p>
    </div>
</div>
</body>
</html>
