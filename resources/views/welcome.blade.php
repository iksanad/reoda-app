@extends('layouts.guest')

@section('title', 'REODA - Kelola & Cari Hunian Impian Anda')

@section('content')
<div class="relative bg-[#C9F0FD]">
    @php
        $heroBg = asset('template/welcome-home.png');
    @endphp
    <style>
        .hero-banner-bg {
            background-color: #eef5fe;
            background-image: none;
            /* No background image below desktop - hidden on mobile */
        }
        @media (min-width: 1024px) {
            .hero-banner-bg {
                background-image: url('{{ $heroBg }}');
                background-position: right bottom;
                background-repeat: no-repeat;
                background-size: 850px;
            }
        }
        @media (min-width: 1280px) {
            .hero-banner-bg {
                background-size: 950px;
            }
        }
    </style>
    <div class="relative overflow-hidden min-h-[500px] lg:min-h-[540px] flex items-start hero-banner-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-12 lg:pt-16 lg:pb-16 w-full z-10 relative">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                
                {{-- Left Content Column --}}
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-reoda-lightest text-reoda-dark text-sm font-semibold mb-4">
                        <span class="flex h-2 w-2 rounded-full bg-reoda"></span>
                        Sistem Sewa Hunian Masa Kini
                    </div>
                    
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl lg:text-4xl xl:text-5xl">
                        <span class="block">Solusi Cerdas untuk</span>
                        <span class="block mt-1">Kelola Hunian,</span>
                        <span class="block text-reoda mt-1">Semua Jadi Mudah</span>
                    </h1>
                    
                    <p class="mt-3 text-base text-gray-500 sm:text-lg lg:text-sm xl:text-base leading-relaxed">
                        Kelola properti, penyewa, pembayaran, hingga kontrak sewa dalam satu platform terintegrasi. Efisien, aman, dan terpecaya.
                    </p>
                    
                    <!-- Search Box (Mamikos-style) -->
                    <form action="{{ route('explore.public') }}" method="GET" class="mt-8 w-full">
                        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-3 sm:p-4 border border-white/50">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <!-- Lokasi / Search -->
                                <div class="flex-1 flex items-center gap-3 bg-white/60 border border-gray-100 rounded-xl px-4 py-3">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" name="search" placeholder="Cari nama, kota, atau alamat..."
                                        value="{{ request('search') }}"
                                        class="bg-transparent w-full text-sm text-gray-800 placeholder-gray-500 outline-none font-medium">
                                </div>
                                <!-- Tipe -->
                                <div class="flex items-center gap-3 bg-white/60 border border-gray-100 rounded-xl px-4 py-3 min-w-[140px]">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <select name="type" class="bg-transparent text-sm text-gray-700 outline-none font-medium w-full">
                                        <option value="">Semua Tipe</option>
                                        <option value="kos">Kos-kosan</option>
                                        <option value="kontrakan">Kontrakan</option>
                                        <option value="apartemen">Apartemen</option>
                                    </select>
                                </div>
                                <!-- Button -->
                                <button type="submit" class="bg-reoda hover:bg-reoda-dark text-white font-bold px-6 py-3 rounded-xl transition text-sm flex items-center justify-center whitespace-nowrap shadow-sm">
                                    Cari
                                </button>
                            </div>

                            <!-- Quick tags -->
                            <div class="mt-3 flex flex-wrap gap-2 px-1">
                                <span class="text-[11px] text-gray-500 font-bold pt-0.5 uppercase tracking-wide">Populer:</span>
                                @foreach(['Bandung','Yogyakarta','Surabaya','Semarang','Depok','Bekasi'] as $city)
                                <a href="{{ route('explore.public', ['search' => $city]) }}"
                                    class="text-xs bg-white/50 hover:bg-reoda-lightest hover:text-reoda border border-gray-100 text-gray-600 rounded-full px-3 py-1 font-medium transition">
                                    {{ $city }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
        
    </div>

    {{-- ===== STATS BAR ===== --}}
    @php
        $totalProps  = \App\Models\Property::where('status','active')->count();
        $totalCities = \App\Models\Property::where('status','active')->distinct()->count('city');
        $totalAvail  = \App\Models\Unit::where('status','available')->count();
        $totalMgr    = \App\Models\User::where('role','manager')->where('manager_status','approved')->count();
    @endphp
    <div class="bg-white border-y border-gray-100 relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <p class="text-3xl font-extrabold text-reoda">{{ number_format($totalProps) }}+</p>
                    <p class="text-sm text-gray-500 mt-1">Properti Aktif</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-reoda">{{ $totalAvail }}+</p>
                    <p class="text-sm text-gray-500 mt-1">Unit Tersedia</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-reoda">{{ $totalCities }}+</p>
                    <p class="text-sm text-gray-500 mt-1">Kota di Indonesia</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-reoda">{{ $totalMgr }}+</p>
                    <p class="text-sm text-gray-500 mt-1">Pengelola Terverifikasi</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== CATEGORY SHORTCUTS ===== --}}
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-6">Cari Berdasarkan Tipe</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @php
                $categories = [
                    ['type'=>'kos',       'icon'=>'🏠', 'label'=>'Kos-kosan',  'color'=>'from-reoda to-blue-600'],
                    ['type'=>'kontrakan', 'icon'=>'🏡', 'label'=>'Kontrakan',  'color'=>'from-emerald-400 to-green-500'],
                    ['type'=>'apartemen', 'icon'=>'🏢', 'label'=>'Apartemen',  'color'=>'from-purple-500 to-indigo-600'],
                ];
            @endphp
            @foreach($categories as $cat)
            <a href="{{ route('explore.public', ['type' => $cat['type']]) }}"
                class="group relative overflow-hidden rounded-2xl bg-linear-to-br {{ $cat['color'] }} p-6 text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="text-4xl mb-3">{{ $cat['icon'] }}</div>
                <p class="font-bold text-lg">{{ $cat['label'] }}</p>
                @php $cnt = \App\Models\Property::where('status','active')->where('type',$cat['type'])->count(); @endphp
                <p class="text-white/80 text-sm mt-1">{{ $cnt }} properti</p>
                <div class="absolute bottom-0 right-0 text-8xl opacity-10 -mb-4 -mr-4 group-hover:scale-110 transition-transform">{{ $cat['icon'] }}</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== FEATURED LISTINGS ===== --}}
@php
    $featured = \App\Models\Property::where('status','active')
        ->whereHas('manager', fn($q) => $q->where('manager_status','approved'))
        ->withCount(['units as available_units_count' => fn($q) => $q->where('status','available')])
        ->with([
            'units' => fn($q) => $q->where('status','available')->orderBy('rent_price')->limit(1),
            'media'
        ])
        ->having('available_units_count', '>', 0)
        ->inRandomOrder()
        ->limit(6)
        ->get();
@endphp

@if($featured->count() > 0)
<section class="bg-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-extrabold text-gray-900">Hunian Pilihan</h2>
            <a href="{{ route('explore.public') }}" class="text-sm font-semibold text-reoda hover:underline">Lihat semua →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featured as $property)
            @php
                $minPrice = $property->units->min('rent_price');
                $typeColors = [
                    'kos'       => ['bg-blue-100 text-reoda-dark',       'Kos-kosan'],
                    'kontrakan' => ['bg-emerald-100 text-emerald-700',   'Kontrakan'],
                    'apartemen' => ['bg-purple-100 text-purple-700',     'Apartemen'],
                ];
                $badge = $typeColors[$property->type] ?? ['bg-gray-100 text-gray-600', ucfirst($property->type)];
            @endphp
            <a href="{{ url('/property/' . $property->property_code) }}"
                class="property-card group block rounded-2xl border border-gray-100 bg-white overflow-hidden shadow-sm cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                {{-- Cover Image --}}
                <div class="relative h-48 overflow-hidden bg-linear-to-br from-gray-100 to-gray-200">
                    @php
                        $images = [];
                        if (isset($property->media) && $property->media->count() > 0) {
                            $images = $property->media->map->url->toArray();
                        } elseif ($property->cover_image_url) {
                            $images[] = $property->cover_image_url;
                        }
                    @endphp
                    @if(empty($images))
                    <div class="w-full h-full flex items-center justify-center text-6xl bg-linear-to-br from-emerald-50 to-teal-100 transition-transform duration-500 group-hover:scale-105">
                        @switch($property->type)
                            @case('kos') 🏠 @break
                            @case('kontrakan') 🏡 @break
                            @case('apartemen') 🏢 @break
                            @default 🏘️
                        @endswitch
                    </div>
                    @else
                    <x-carousel :images="$images" :alt="$property->name" heightClass="h-48" />
                    @endif
                    <span class="absolute top-3 left-3 text-xs font-bold px-3 py-1 rounded-full {{ $badge[0] }}">
                        {{ $badge[1] }}
                    </span>
                    <span class="absolute top-3 right-3 text-xs font-semibold bg-black/60 text-white px-2.5 py-1 rounded-full">
                        {{ $property->available_units_count }} unit tersedia
                    </span>
                </div>

                {{-- Content --}}
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 text-base leading-snug group-hover:text-reoda transition line-clamp-1">
                        {{ $property->name }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-1 line-clamp-1">
                        <svg class="w-3.5 h-3.5 text-reoda shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $property->district ? $property->district . ', ' : '' }}{{ $property->city }}
                    </p>
                    @if($minPrice)
                    <div class="mt-3 flex items-baseline gap-1">
                        <span class="text-xs text-gray-400">mulai</span>
                        <span class="text-lg font-extrabold text-reoda">Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                        <span class="text-xs text-gray-400">/bln</span>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== HOW IT WORKS ===== --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-extrabold text-gray-900">Cara Kerja REODA</h2>
            <p class="text-gray-500 mt-2 text-sm">Proses sewa hunian yang mudah dan transparan</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['icon'=>'🔍','step'=>'1','title'=>'Cari Hunian','desc'=>'Temukan hunian sesuai lokasi, tipe, dan anggaran. Filter berdasarkan fasilitas yang Anda inginkan.'],
                ['icon'=>'📋','step'=>'2','title'=>'Ajukan Kontrak','desc'=>'Daftar, login sebagai penyewa, dan ajukan kontrak langsung ke pengelola. Proses cepat dan digital.'],
                ['icon'=>'💳','step'=>'3','title'=>'Bayar & Huni','desc'=>'Setelah disetujui, lakukan pembayaran sewa bulanan via platform. Aman dan tercatat otomatis.'],
            ] as $step)
            <div class="text-center bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <div class="text-5xl mb-4">{{ $step['icon'] }}</div>
                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-reoda text-white text-xs font-bold mb-3">{{ $step['step'] }}</div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $step['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== CTA SECTION ===== --}}
<section class="bg-reoda py-16 border-t border-reoda-dark/20">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-extrabold text-white mb-4">Punya Hunian untuk Disewakan?</h2>
        <p class="text-white/80 mb-8 text-base">Daftarkan properti Anda di REODA dan kelola sewa dengan mudah — pembayaran, kontrak, dan laporan semua dalam satu platform.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register', ['role' => 'manager']) }}"
                class="inline-flex items-center justify-center gap-2 bg-white text-reoda-dark font-bold px-8 py-3.5 rounded-xl hover:bg-gray-50 transition shadow-lg text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Daftarkan Properti Saya
            </a>
            <a href="{{ route('explore.public') }}"
                class="inline-flex items-center justify-center gap-2 bg-white/20 border border-white/30 text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-white/30 transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari Hunian Dulu
            </a>
        </div>
    </div>
</section>

@endsection
