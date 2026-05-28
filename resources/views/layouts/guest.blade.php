<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'REODA - Sistem Sewa Hunian')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" href="{{ asset('template/logo/Reoda-4C74AF.png') }}" type="image/x-icon">
</head>
<body class="font-sans antialiased text-gray-900 bg-white">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <img class="h-8 w-auto" src="{{ asset('template/logo/Reoda-4C74AF.png') }}" alt="REODA">
                    </div>
                </div>
                <div class="flex items-center">
                    @auth
                        @if(auth()->user()->isManager())
                            <a href="{{ route('manager.dashboard') }}" class="text-reoda hover:text-reoda-dark font-medium mr-4">Dashboard</a>
                        @elseif(auth()->user()->isTenant())
                            <a href="{{ route('tenant.dashboard') }}" class="text-reoda hover:text-reoda-dark font-medium mr-4">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-reoda font-medium mr-4">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-reoda hover:bg-reoda-dark text-white px-4 py-2 rounded-md text-sm font-medium transition">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} REODA. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
