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
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50" style="box-shadow: 0 1px 12px 0 rgba(76,116,175,0.08)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-[68px] items-center">

                {{-- Logo + Nav Links --}}
                <div class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0 group">
                        <img class="h-11 w-auto transition group-hover:scale-105" src="{{ asset('template/logo/Reoda-4C74AF.png') }}" alt="REODA">
                        <span class="hidden sm:block text-lg font-extrabold text-[#4C74AF] tracking-tight">REODA</span>
                    </a>

                    {{-- Desktop Nav links --}}
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ url('/') }}" class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition
                            {{ request()->is('/') ? 'text-reoda bg-reoda/8' : 'text-gray-600 hover:text-reoda hover:bg-reoda/5' }}">
                            🏠 Beranda
                        </a>
                        <a href="{{ route('explore.public') }}" class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition
                            {{ request()->routeIs('explore.public') ? 'text-reoda bg-reoda/8' : 'text-gray-600 hover:text-reoda hover:bg-reoda/5' }}">
                            🔍 Cari Hunian
                        </a>
                        <a href="{{ route('compare.index') }}" class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition
                            {{ request()->routeIs('compare.index') ? 'text-reoda bg-reoda/8' : 'text-gray-600 hover:text-reoda hover:bg-reoda/5' }}">
                            ⚖️ Bandingkan
                        </a>
                    </div>
                </div>

                {{-- Auth Buttons --}}
                <div class="flex items-center gap-2">
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-2 rounded-xl bg-reoda px-4 py-2 text-sm font-bold text-white hover:bg-reoda-dark transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Admin Panel
                            </a>
                        @elseif(auth()->user()->isManager())
                            <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-2 rounded-xl bg-reoda px-4 py-2 text-sm font-bold text-white hover:bg-reoda-dark transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </a>
                        @elseif(auth()->user()->isTenant())
                            <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-2 rounded-xl bg-reoda px-4 py-2 text-sm font-bold text-white hover:bg-reoda-dark transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:block text-sm font-semibold text-gray-600 hover:text-reoda transition px-4 py-2 rounded-lg hover:bg-gray-50">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-reoda hover:bg-reoda-dark text-white px-5 py-2.5 text-sm font-bold transition shadow-sm">
                            Daftar Gratis
                        </a>
                    @endauth

                    {{-- Mobile menu button --}}
                    <button id="mobile-menu-btn" class="md:hidden ml-1 p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div id="mobile-menu" class="md:hidden hidden pb-3 border-t border-gray-100 mt-0">
                <div class="pt-3 space-y-1">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->is('/') ? 'text-reoda bg-reoda/8' : 'text-gray-600 hover:text-reoda hover:bg-gray-50' }}">🏠 Beranda</a>
                    <a href="{{ route('explore.public') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('explore.public') ? 'text-reoda bg-reoda/8' : 'text-gray-600 hover:text-reoda hover:bg-gray-50' }}">🔍 Cari Hunian</a>
                    <a href="{{ route('compare.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('compare.index') ? 'text-reoda bg-reoda/8' : 'text-gray-600 hover:text-reoda hover:bg-gray-50' }}">⚖️ Bandingkan</a>
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold text-gray-600 hover:text-reoda hover:bg-gray-50">⚙️ Admin Panel</a>
                        @elseif(auth()->user()->isManager())
                            <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold text-gray-600 hover:text-reoda hover:bg-gray-50">📊 Dashboard</a>
                        @elseif(auth()->user()->isTenant())
                            <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold text-gray-600 hover:text-reoda hover:bg-gray-50">📊 Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold text-gray-600 hover:text-reoda hover:bg-gray-50">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <script>
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
    </script>

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
                <div>
                    <h5 class="text-sm font-bold mb-4 text-white">REODA Team</h5>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Admin Portal</a></li>
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
