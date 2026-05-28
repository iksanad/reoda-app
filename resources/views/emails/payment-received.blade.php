<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Masuk — REODA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f6fb; color: #1a202c; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(76,116,175,0.10); }
        .header { background: linear-gradient(135deg, #4C74AF 0%, #003648 100%); padding: 36px 40px 28px; text-align: center; }
        .header img { height: 48px; margin-bottom: 12px; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.8); font-size: 13px; margin-top: 4px; }
        .badge { display: inline-block; background: #FFC107; color: #7a5000; font-size: 12px; font-weight: 700; padding: 4px 14px; border-radius: 99px; margin-top: 12px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #003648; margin-bottom: 8px; }
        .text { font-size: 14px; color: #4a5568; line-height: 1.7; margin-bottom: 20px; }
        .card { background: #f4f6fb; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; border-left: 4px solid #4C74AF; }
        .card-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .card-row:last-child { margin-bottom: 0; border-top: 1px dashed #c7d7ee; padding-top: 10px; }
        .card-label { font-size: 13px; color: #718096; }
        .card-value { font-size: 13px; font-weight: 600; color: #1a202c; }
        .card-value.amount { font-size: 18px; font-weight: 800; color: #4C74AF; }
        .btn { display: block; width: fit-content; margin: 0 auto 20px; background: #4C74AF; color: #fff; font-size: 14px; font-weight: 700; padding: 12px 32px; border-radius: 10px; text-decoration: none; }
        .btn:hover { background: #003648; }
        .footer { background: #f4f6fb; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #a0aec0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <img src="{{ asset('template/logo/Reoda-White.png') }}" alt="REODA">
        <h1>Ada Pembayaran Masuk!</h1>
        <p>Penyewa telah mengupload bukti bayar</p>
        <span class="badge">⏳ Menunggu Konfirmasi Anda</span>
    </div>

    <div class="body">
        @php
            $invoice = $payment->invoice;
            $tenant  = $invoice->tenant ?? $payment->tenant;
            $manager = $invoice->manager ?? null;
        @endphp

        <p class="greeting">Halo, Pengelola!</p>
        <p class="text">
            Penyewa <strong>{{ $tenant->name ?? '-' }}</strong> telah mengirimkan bukti pembayaran untuk tagihan berikut. 
            Silakan periksa dan konfirmasi pembayaran tersebut melalui panel pengelola Anda.
        </p>

        <div class="card">
            <div class="card-row">
                <span class="card-label">Nama Penyewa</span>
                <span class="card-value">{{ $tenant->name ?? '-' }}</span>
            </div>
            <div class="card-row">
                <span class="card-label">No. Invoice</span>
                <span class="card-value">{{ $invoice->invoice_number ?? "INV-{$invoice->id}" }}</span>
            </div>
            <div class="card-row">
                <span class="card-label">Jenis Tagihan</span>
                <span class="card-value">{{ ucfirst($invoice->type ?? 'rent') }}</span>
            </div>
            <div class="card-row">
                <span class="card-label">Tanggal Upload</span>
                <span class="card-value">{{ $payment->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
            <div class="card-row">
                <span class="card-label">Total Tagihan</span>
                <span class="card-value amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <a href="{{ url('/manager/payments/' . $payment->id) }}" class="btn">
            🔍 Lihat &amp; Konfirmasi Pembayaran
        </a>

        <p class="text" style="text-align:center; color:#a0aec0; font-size:13px;">
            Jika tombol di atas tidak berfungsi, salin URL ini ke browser Anda:<br>
            <a href="{{ url('/manager/payments/' . $payment->id) }}" style="color:#4C74AF;">{{ url('/manager/payments/' . $payment->id) }}</a>
        </p>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem REODA. Jangan balas email ini.</p>
        <p style="margin-top:4px;">&copy; {{ date('Y') }} REODA — Solusi Hunian Terpercaya</p>
    </div>
</div>
</body>
</html>
