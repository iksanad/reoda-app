<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | REODA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe4e4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a202c;
        }
        .container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
        }
        .logo-wrap { margin-bottom: 2rem; }
        .logo-wrap img { height: 48px; filter: grayscale(0.3); }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            color: #e53e3e;
            opacity: 0.12;
            letter-spacing: -4px;
            margin-bottom: -2rem;
            user-select: none;
        }
        .icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 96px;
            height: 96px;
            background: #fed7d7;
            border-radius: 50%;
            margin-bottom: 1.5rem;
        }
        .icon-wrap svg { width: 48px; height: 48px; color: #e53e3e; }
        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #742a2a;
            margin-bottom: 0.75rem;
        }
        p {
            font-size: 1rem;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn-group { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #4C74AF;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 700;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: #003648; }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #fff;
            color: #e53e3e;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            border: 2px solid #e53e3e;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-secondary:hover { background: #e53e3e; color: #fff; }
        .footer-note {
            margin-top: 3rem;
            font-size: 0.8rem;
            color: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-wrap">
            <img src="{{ asset('template/logo/Reoda-4C74AF.png') }}" alt="REODA">
        </div>

        <div class="error-code">500</div>

        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h1>Terjadi Kesalahan Server</h1>
        <p>Maaf, terjadi kesalahan internal pada server kami. Tim teknis REODA sudah diberitahu dan sedang menangani masalah ini. Silakan coba beberapa saat lagi.</p>

        <div class="btn-group">
            <button onclick="window.location.reload()" class="btn-secondary">
                🔄 Muat Ulang
            </button>
            @auth
            <a href="{{ auth()->user()->isManager() ? route('manager.dashboard') : (auth()->user()->isTenant() ? route('tenant.dashboard') : route('superadmin.dashboard')) }}" class="btn-primary">
                🏠 Ke Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="btn-primary">
                🏠 Ke Halaman Utama
            </a>
            @endauth
        </div>

        <p class="footer-note">Error 500 &mdash; &copy; {{ date('Y') }} REODA</p>
    </div>
</body>
</html>
