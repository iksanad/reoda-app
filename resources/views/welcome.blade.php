@extends('layouts.guest')

@section('title', 'REODA - Kelola & Cari Hunian Impian Anda')

@section('content')
<div class="relative bg-white">
    <!-- Hero Section Container with background image -->
    <div class="relative overflow-hidden min-h-[500px] lg:min-h-[560px] flex items-center bg-[#C9F0FD]"
         style="background-image: url('{{ asset('template/welcome-home.png') }}'); background-position: right bottom; background-repeat: no-repeat; background-size: contain;">
        
        <!-- Semi-transparent overlay on the left for text readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#C9F0FD] via-[#C9F0FD]/90 to-[#C9F0FD]/20 lg:to-transparent pointer-events-none"></div>
        
        <!-- Hero Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12 lg:pt-10 lg:pb-14 w-full z-10 relative">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                
                <!-- Left Content Column -->
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-reoda-lightest text-reoda-dark text-sm font-semibold mb-4">
                        <span class="flex h-2 w-2 rounded-full bg-reoda"></span>
                        Sistem Sewa Hunian Masa Kini
                    </div>
                    
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl lg:text-4xl xl:text-5xl">
                        <span class="block">Kelola & Sewa Hunian</span>
                        <span class="block text-reoda mt-1">Lebih Mudah Bersama REODA</span>
                    </h1>
                    
                    <p class="mt-3 text-base text-gray-500 sm:text-lg lg:text-sm xl:text-base leading-relaxed">
                        Platform pintar terintegrasi untuk memudahkan pengelola hunian memantau properti, sekaligus membantu penyewa menemukan hunian impian mereka secara transparan dan aman.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="mt-6 flex flex-col sm:flex-row items-center sm:items-start lg:items-start gap-3 sm:justify-center lg:justify-start">
                        <a href="{{ route('register', ['role' => 'manager']) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-reoda hover:bg-reoda-dark transition shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Gabung Pengelola
                        </a>
                        <a href="{{ route('register', ['role' => 'tenant']) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-reoda-dark bg-reoda-lightest hover:bg-reoda-lighter transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari Hunian
                        </a>
                        <a href="{{ route('compare.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-gray-600 border border-gray-200 hover:border-reoda/30 hover:text-reoda transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Bandingkan
                        </a>
                    </div>
                </div>
                
                <!-- Mobile/Tablet Image (shown below text on small screens) -->
                <div class="mt-10 sm:mt-12 lg:hidden flex justify-center">
                    <img 
                        src="{{ asset('template/welcome-home.png') }}" 
                        alt="Welcome to REODA" 
                        class="w-full max-w-md object-contain"
                    />
                </div>
                
            </div>
        </div>
        
    </div>

    <!-- Info / Feature Limitation Banner (Positioned below the Hero Section) -->
    <div class="bg-gray-50 border-t border-gray-100 py-12 relative z-20">
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
