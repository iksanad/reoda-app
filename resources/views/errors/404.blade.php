<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | REODA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #f4f8ff 0%, #e8f0fe 100%);
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
        .logo-wrap img { height: 48px; }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            color: #4C74AF;
            opacity: 0.15;
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
            background: #dbe8ff;
            border-radius: 50%;
            margin-bottom: 1.5rem;
        }
        .icon-wrap svg { width: 48px; height: 48px; color: #4C74AF; }
        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #003648;
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
            color: #4C74AF;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            border: 2px solid #4C74AF;
            transition: all 0.2s;
        }
        .btn-secondary:hover { background: #4C74AF; color: #fff; }
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

        <div class="error-code">404</div>

        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1>Halaman Tidak Ditemukan</h1>
        <p>Maaf, halaman yang Anda cari tidak ada atau mungkin sudah dipindahkan. Pastikan URL yang Anda masukkan sudah benar.</p>

        <div class="btn-group">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn-secondary">
                ← Kembali
            </a>
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

        <p class="footer-note">Error 404 &mdash; &copy; {{ date('Y') }} REODA</p>
    </div>
</body>
</html>
