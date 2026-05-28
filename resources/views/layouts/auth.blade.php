<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Auth - REODA')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('template/logo/Reoda-4C74AF.png') }}" type="image/x-icon">
</head>
<body class="font-sans antialiased text-gray-900 bg-reoda-lightest">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-4xl rounded-2xl bg-white shadow-xl flex overflow-hidden">
            <!-- Branding Panel -->
            <div class="hidden md:flex md:w-1/2 bg-reoda items-center justify-center p-8 flex-col text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-reoda-dark to-reoda opacity-90"></div>
                <div class="relative z-10 text-center">
                    <img src="{{ asset('template/logo/Reoda-003648.png') }}" alt="REODA Logo" class="h-24 mx-auto mb-6 bg-white p-2 rounded-xl">
                    <h2 class="text-3xl font-bold mb-4">REODA Platform</h2>
                    <p class="text-reoda-lightest mb-8">Solusi manajemen sewa hunian terpadu. Kelola kos, kontrakan, dan apartemen Anda dengan lebih mudah.</p>
                </div>
                <!-- Decorative Circle -->
                <div class="absolute -bottom-16 -left-16 w-64 h-64 rounded-full bg-reoda-light opacity-20"></div>
                <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full bg-reoda-lighter opacity-10"></div>
            </div>

            <!-- Form Panel -->
            <div class="w-full md:w-1/2 p-8 md:p-12">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
