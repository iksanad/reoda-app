@extends('layouts.guest')

@section('title', 'REODA - Solusi Sewa Hunian Anda')

@section('content')
<div class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>

            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Manajemen hunian</span>
                        <span class="block text-reoda">lebih mudah & efisien</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        REODA adalah platform terpadu untuk mencari, menyewa, dan mengelola properti seperti kos, kontrakan, dan apartemen. Tersedia untuk penyewa dan pengelola.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start gap-3 flex-wrap">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-reoda hover:bg-reoda-dark md:py-4 md:text-lg transition shadow-sm">
                            Mulai Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-reoda-dark bg-reoda-lightest hover:bg-reoda-lighter md:py-4 md:text-lg transition">
                            Masuk Akun
                        </a>
                        <a href="{{ route('compare.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3 border-2 border-reoda text-base font-medium rounded-md text-reoda hover:bg-reoda/5 md:py-4 md:text-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Bandingkan Hunian
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-gray-100">
        <div class="h-56 w-full sm:h-72 md:h-96 lg:w-full lg:h-full bg-gradient-to-tr from-reoda-dark to-reoda-light flex items-center justify-center">
             <img src="{{ asset('template/logo/Reoda-003648.png') }}" alt="REODA" class="h-32 opacity-20 filter grayscale">
        </div>
    </div>
</div>
@endsection
