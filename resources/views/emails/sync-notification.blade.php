<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f6fb; color: #1a202c; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(76,116,175,0.10); }
        .header { padding: 32px 40px; text-align: center; background: linear-gradient(135deg, #4C74AF 0%, #003648 100%); }
        .header img { height: 42px; }
        .body { padding: 36px 40px; }
        .title { font-size: 20px; font-weight: 700; color: #003648; margin-bottom: 16px; }
        .greeting { font-size: 15px; font-weight: 600; color: #4a5568; margin-bottom: 12px; }
        .text { font-size: 15px; color: #4a5568; line-height: 1.7; margin-bottom: 24px; white-space: pre-wrap; }
        .btn { display: inline-block; background: #4C74AF; color: #fff; font-size: 14px; font-weight: 700; padding: 12px 32px; border-radius: 10px; text-decoration: none; }
        .btn-container { text-align: center; margin-top: 10px; }
        .footer { background: #f4f6fb; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #a0aec0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <img src="{{ asset('template/logo/Reoda-White.png') }}" alt="REODA">
    </div>

    <div class="body">
        <p class="greeting">Halo, {{ $notification->user->name ?? 'Pengguna' }}!</p>
        
        <h2 class="title">{{ $notification->title }}</h2>
        
        <div class="text">{{ $notification->message }}</div>
        
        @if($notification->link)
        <div class="btn-container">
            <a href="{{ url($notification->link) }}" class="btn">Lihat Detail Notifikasi</a>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem REODA. Mohon tidak membalas email ini.</p>
        <p style="margin-top:6px;">&copy; {{ date('Y') }} REODA — Solusi Hunian Terpercaya</p>
    </div>
</div>
</body>
</html>
