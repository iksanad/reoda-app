@extends('layouts.guest')

@section('title', 'REODA - Kelola & Cari Hunian Impian Anda')

@section('content')
<div class="relative overflow-hidden bg-white min-h-[calc(100vh-4rem)] flex flex-col justify-between">
    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-20 lg:py-24 w-full my-auto z-10 relative">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
            
            <!-- Left Content Column -->
            <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-reoda-lightest text-reoda-dark text-sm font-semibold mb-6">
                    <span class="flex h-2 w-2 rounded-full bg-reoda"></span>
                    Sistem Sewa Hunian Masa Kini
                </div>
                
                <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
                    <span class="block">Kelola & Sewa Hunian</span>
                    <span class="block text-reoda mt-2">Lebih Mudah Bersama REODA</span>
                </h1>
                
                <p class="mt-4 text-base text-gray-500 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl leading-relaxed">
                    Platform pintar terintegrasi untuk memudahkan pengelola hunian memantau properti, sekaligus membantu penyewa menemukan hunian impian mereka secara transparan dan aman.
                </p>
                
                <!-- CTA Cards Grid -->
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Card 1: Pengelola -->
                    <div class="group relative bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-reoda/30 transition duration-300">
                        <div class="h-12 w-12 rounded-xl bg-reoda-lightest flex items-center justify-center text-reoda mb-4 group-hover:scale-105 transition-transform duration-300">
                            <!-- Icon Manager / Building -->
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Pengelola Hunian</h3>
                        <p class="text-xs text-gray-500 mb-4">Kelola kos-kosan, kontrakan, unit apartemen, kelola kontrak penyewa & tagihan secara otomatis.</p>
                        <a href="{{ route('register', ['role' => 'manager']) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-reoda hover:text-reoda-dark">
                            Mulai Mengelola
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    
                    <!-- Card 2: Penyewa -->
                    <div class="group relative bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-reoda/30 transition duration-300">
                        <div class="h-12 w-12 rounded-xl bg-reoda-lightest flex items-center justify-center text-reoda mb-4 group-hover:scale-105 transition-transform duration-300">
                            <!-- Icon Tenant / Search -->
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Pencari Hunian</h3>
                        <p class="text-xs text-gray-500 mb-4">Temukan kos & kontrakan sesuai budget Anda. Bandingkan fasilitas hunian dengan mudah.</p>
                        <a href="{{ route('register', ['role' => 'tenant']) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-reoda hover:text-reoda-dark">
                            Cari Hunian
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    
                </div>
                
                <!-- Secondary CTAs -->
                <div class="mt-6 flex items-center justify-center lg:justify-start gap-4">
                    <a href="{{ route('compare.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-reoda transition duration-200">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-reoda" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        ⚡ Bandingkan Fasilitas Hunian
                    </a>
                </div>
            </div>
            
            <!-- Mobile/Tablet Image (Hidden on Desktop) -->
            <div class="mt-12 sm:mt-16 lg:hidden flex justify-center">
                <img 
                    src="{{ asset('template/welcome-home.png') }}" 
                    alt="Welcome to REODA" 
                    class="w-full max-w-md object-contain"
                />
            </div>
            
        </div>
    </div>
    
    <!-- Desktop Image (Absolute on Desktop, Hidden on Mobile/Tablet) -->
    <div class="hidden lg:flex lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 items-end justify-end overflow-hidden pointer-events-none h-full">
        <img 
            src="{{ asset('template/welcome-home.png') }}" 
            alt="Welcome to REODA" 
            class="w-auto h-[92%] max-h-[850px] object-contain object-right-bottom transform translate-x-12 translate-y-6 scale-110"
        />
    </div>

    <!-- Info / Feature Limitation Banner -->
    <div class="bg-gray-50 border-t border-gray-100 py-12 z-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Ingin Menyewa Hunian?</h2>
            <p class="text-sm text-gray-500 max-w-xl mx-auto mb-6">
                Untuk melakukan pengajuan sewa, mengunggah dokumen, dan membayar sewa, Anda diharuskan mendaftar dan masuk sebagai **Penyewa** terlebih dahulu demi keamanan data Anda.
            </p>
            <div class="inline-flex items-center gap-3">
                <a href="{{ route('register', ['role' => 'tenant']) }}" class="bg-reoda hover:bg-reoda-dark text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
                    Buat Akun Penyewa
                </a>
                <span class="text-gray-400">atau</span>
                <a href="{{ route('login') }}" class="text-reoda hover:text-reoda-dark text-sm font-semibold transition">
                    Masuk ke Akun Anda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
