<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Auth - REODA')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('template/logo/Reoda-4C74AF.png') }}" type="image/x-icon">
</head>
<body class="font-sans antialiased text-gray-900 bg-[#EEF2F6]">
    <div class="flex min-h-screen">
        <!-- Left Branding Panel (Hidden on mobile) -->
        <div class="hidden lg:block lg:w-[45%] xl:w-[45%] relative">
            <img src="{{ asset('template/home.png') }}" alt="REODA Background" class="absolute inset-0 w-full h-full object-cover">
        </div>

        <!-- Right Form Panel -->
        <div class="w-full lg:flex-1 flex flex-col justify-center items-center p-6 lg:p-12 relative">
            <div class="w-full max-w-[500px] xl:max-w-[550px] bg-white rounded-2xl shadow-xl p-8 xl:p-12 z-10 relative border border-gray-100">
                @yield('content')
            </div>
            
            <!-- Footer text below the card -->
            <div class="absolute bottom-6 text-center text-[#8C9BAF] text-sm">
                &copy; {{ date('Y') }} Reoda. Copyright
            </div>
        </div>
    </div>
</body>
</html>
