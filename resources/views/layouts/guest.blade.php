<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'REODA - Sistem Sewa Hunian')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" href="{{ asset('template/logo/Reoda-4C74AF.png') }}" type="image/x-icon">
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-900 bg-white">

    {{-- Navbar --}}
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                {{-- Logo --}}
                <div class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
                        <img class="h-8 w-auto" src="{{ asset('template/logo/Reoda-4C74AF.png') }}" alt="REODA">
                    </a>
                    {{-- Nav links --}}
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('explore.public') }}" class="text-sm font-semibold text-gray-600 hover:text-reoda transition {{ request()->routeIs('explore.public') ? 'text-reoda' : '' }}">
                            🔍 Cari Hunian
                        </a>
                        <a href="{{ route('compare.index') }}" class="text-sm font-semibold text-gray-600 hover:text-reoda transition">
                            ⚖️ Bandingkan
                        </a>
                    </div>
                </div>

                {{-- Auth --}}
                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isManager())
                            <a href="{{ route('manager.dashboard') }}" class="text-sm font-semibold text-reoda hover:text-reoda-dark">Dashboard</a>
                        @elseif(auth()->user()->isTenant())
                            <a href="{{ route('tenant.dashboard') }}" class="text-sm font-semibold text-reoda hover:text-reoda-dark">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-reoda transition px-4 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-reoda hover:bg-reoda-dark text-white px-5 py-2 text-sm font-semibold transition shadow-sm">
                            Daftar Gratis
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <div class="md:col-span-2">
                    <img class="h-8 w-auto mb-3 brightness-0 invert" src="{{ asset('template/logo/Reoda-4C74AF.png') }}" alt="REODA">
                    <p class="text-sm text-gray-400 leading-relaxed max-w-xs">
                        Platform digital untuk mencari, menyewa, dan mengelola hunian di seluruh Indonesia. Mudah, aman, dan terpercaya.
                    </p>
                </div>
                <div>
                    <h5 class="text-sm font-bold mb-4 text-white">Penyewa</h5>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('explore.public') }}" class="hover:text-white transition">Cari Hunian</a></li>
                        <li><a href="{{ route('compare.index') }}" class="hover:text-white transition">Bandingkan Properti</a></li>
                        <li><a href="{{ route('register', ['role'=>'tenant']) }}" class="hover:text-white transition">Daftar Sebagai Penyewa</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-sm font-bold mb-4 text-white">Pengelola</h5>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('register', ['role'=>'manager']) }}" class="hover:text-white transition">Daftarkan Properti</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk ke Dasbor</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} REODA. All rights reserved. — Platform Sewa Hunian Indonesia
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
