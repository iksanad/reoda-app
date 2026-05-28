<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran Pengelola — REODA</title>
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
        .info-list { list-style: none; margin-bottom: 24px; }
        .info-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: #4a5568; margin-bottom: 10px; }
        .info-list li::before { content: "✓"; color: #4C74AF; font-weight: 700; flex-shrink: 0; }
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
            <h1>Selamat! Anda Diterima 🎉</h1>
            <p>Pendaftaran sebagai Pengelola REODA berhasil disetujui</p>
            <span class="badge-ok">✅ Disetujui</span>
        @elseif($action === 'revoked')
            <h1>Hak Akses Dicabut</h1>
            <p>Akses Anda sebagai Pengelola REODA telah dibekukan</p>
            <span class="badge-err" style="background:#f6ad55;color:#fff;">⚠️ Dicabut</span>
        @else
            <h1>Pendaftaran Tidak Disetujui</h1>
            <p>Maaf, pendaftaran Pengelola Anda belum bisa kami terima</p>
            <span class="badge-err">❌ Ditolak</span>
        @endif
    </div>

    <div class="body">
        <p class="greeting">Halo, {{ $manager->name }}!</p>

        @if($action === 'approved')
        <p class="text">
            Selamat! Tim REODA telah menyetujui pendaftaran Anda sebagai <strong>Pengelola (Manager)</strong>. 
            Anda sekarang dapat masuk ke panel pengelola dan mulai mendaftarkan properti Anda.
        </p>
        <ul class="info-list">
            <li>Tambahkan properti dan unit hunian Anda</li>
            <li>Kelola penyewa dan kontrak sewa dengan mudah</li>
            <li>Pantau pembayaran dan laporan keuangan</li>
            <li>Akses semua fitur pengelola REODA</li>
        </ul>
        <a href="{{ url('/manager/dashboard') }}" class="btn">🏠 Masuk ke Panel Pengelola</a>
        @elseif($action === 'revoked')
        <p class="text">
            Mohon maaf, hak akses Anda sebagai Pengelola di REODA telah <strong>dicabut/dibekukan</strong> oleh sistem kami.
            Anda tidak lagi dapat menambahkan properti baru.
        </p>
        @if($notes)
        <div class="notes-box">
            <strong>Alasan Pencabutan Akses:</strong><br>
            {{ $notes }}
        </div>
        @endif
        <p class="text">
            Meskipun demikian, Anda masih memiliki akses terbatas ke dashboard untuk menyelesaikan transaksi atau melayani penyewa (tenant) pada kontrak yang masih berjalan saat ini.
        </p>
        @else
        <p class="text">
            Terima kasih telah mendaftar sebagai Pengelola di REODA. Namun kami mohon maaf, 
            pendaftaran Anda saat ini <strong>belum dapat kami setujui</strong>.
        </p>
        @if($notes)
        <div class="notes-box">
            <strong>Alasan Penolakan:</strong><br>
            {{ $notes }}
        </div>
        @endif
        <p class="text">
            Jika Anda merasa ini adalah kesalahan atau ingin mengajukan pendaftaran ulang, 
            silakan hubungi tim REODA melalui email support kami.
        </p>
        @endif
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem REODA. Jangan balas email ini.</p>
        <p style="margin-top:4px;">&copy; {{ date('Y') }} REODA — Solusi Hunian Terpercaya</p>
    </div>
</div>
</body>
</html>
