@extends('layouts.app')

@php $role = auth()->user()->role; @endphp

@section('title', 'Explore Market - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-black">Explore Market</h2>
        <p class="text-sm text-gray-500 mt-1">Temukan hunian yang sesuai dengan kebutuhan Anda.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('compare.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-reoda px-4 py-2 text-sm font-semibold text-reoda hover:bg-reoda/5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Bandingkan Hunian
        </a>
        <nav><ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route($role . '.dashboard') }}">Dashboard /</a></li>
            <li class="font-medium text-reoda">Explore</li>
        </ol></nav>
    </div>
</div>

<!-- Filter Bar -->
<div class="rounded-sm border border-stroke bg-white shadow-default mb-6">
    <form method="GET" action="{{ route($role . '.explore.index') }}" class="p-5 flex flex-wrap gap-4 items-end">
        <!-- Search -->
        <div class="flex-1 min-w-[180px]">
            <label class="mb-2 block text-sm font-medium text-gray-700">Cari Lokasi</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, alamat, kota..." class="w-full rounded border-[1.5px] border-stroke bg-transparent py-2.5 pl-9 pr-4 text-sm font-medium outline-none transition focus:border-reoda" />
            </div>
        </div>
        <!-- City -->
        <div class="w-full sm:w-44">
            <label class="mb-2 block text-sm font-medium text-gray-700">Kota</label>
            <select name="city" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-reoda">
                <option value="">Semua Kota</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <!-- Type -->
        <div class="w-full sm:w-44">
            <label class="mb-2 block text-sm font-medium text-gray-700">Tipe Hunian</label>
            <select name="type" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-reoda">
                <option value="">Semua Tipe</option>
                <option value="kos" {{ request('type') == 'kos' ? 'selected' : '' }}>Kos-kosan</option>
                <option value="kontrakan" {{ request('type') == 'kontrakan' ? 'selected' : '' }}>Kontrakan</option>
                <option value="apartemen" {{ request('type') == 'apartemen' ? 'selected' : '' }}>Apartemen</option>
            </select>
        </div>
        <!-- Available only -->
        <div class="w-full sm:w-44">
            <label class="mb-2 block text-sm font-medium text-gray-700">Ketersediaan</label>
            <select name="available" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-reoda">
                <option value="1" {{ request('available', '1') == '1' ? 'selected' : '' }}>Ada Kamar Tersedia</option>
                <option value="0" {{ request('available') == '0' ? 'selected' : '' }}>Semua (Termasuk Penuh)</option>
            </select>
        </div>
        <!-- Sort -->
        <div class="w-full sm:w-44">
            <label class="mb-2 block text-sm font-medium text-gray-700">Urutkan</label>
            <select name="sort" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-reoda">
                <option value="latest" {{ request('sort','latest') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="rounded bg-reoda px-5 py-2.5 text-sm font-medium text-white hover:bg-reoda-dark transition">Filter</button>
            @if(request()->hasAny(['search','city','type','available','sort']))
                <a href="{{ route($role . '.explore.index') }}" class="rounded border border-stroke bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Results -->
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Menampilkan <span class="font-medium text-black">{{ $properties->firstItem() ?? 0 }}–{{ $properties->lastItem() ?? 0 }}</span> dari <span class="font-medium text-black">{{ $properties->total() }}</span> properti</p>
</div>

@if($properties->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($properties as $property)
    @php
        $minPrice = $property->units->where('status','available')->min('rent_price');
    @endphp
    <div class="rounded-lg border border-stroke bg-white shadow-sm overflow-hidden flex flex-col hover:shadow-md transition group">
        <!-- Cover Image -->
        <div class="relative h-48 overflow-hidden bg-gray-100">
            @php
                $images = [];
                if (isset($property->media) && $property->media->count() > 0) {
                    $images = $property->media->map->url->toArray();
                } elseif ($property->cover_image_url) {
                    $images[] = $property->cover_image_url;
                }
            @endphp
            @if(empty($images))
            <img src="https://placehold.co/600x400/4C74AF/ffffff?text={{ urlencode($property->name) }}" alt="{{ $property->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
            <x-carousel :images="$images" :alt="$property->name" heightClass="h-48" />
            @endif
            <!-- Badge type -->
            <div class="absolute top-3 left-3 z-10 rounded-md bg-reoda-dark/80 backdrop-blur-sm px-2.5 py-1 text-xs font-bold text-white uppercase shadow-sm">
                {{ $property->type }}
            </div>
            <!-- Available rooms badge -->
            <div class="absolute top-3 right-3 z-10 rounded-md {{ $property->available_units_count > 0 ? 'bg-success' : 'bg-gray-500' }} px-2.5 py-1 text-xs font-bold text-white shadow-sm">
                {{ $property->available_units_count > 0 ? $property->available_units_count . ' Tersedia' : 'Penuh' }}
            </div>
        </div>

        <div class="p-5 flex flex-col flex-grow">
            <h3 class="font-bold text-black text-lg mb-1">
                <a href="{{ route($role . '.explore.show', $property) }}" class="hover:text-reoda transition">{{ $property->name }}</a>
            </h3>
            <p class="text-sm text-gray-500 flex items-start gap-1 mb-3">
                <svg class="w-4 h-4 mt-0.5 shrink-0 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $property->district }}, {{ $property->city }}
            </p>

            <!-- Stats row -->
            <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    {{ $property->units_count }} unit total
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ $property->manager->name ?? 'Pengelola' }}
                </span>
            </div>

            <div class="mt-auto pt-4 border-t border-stroke flex items-center justify-between gap-2">
                <div>
                    <p class="text-xs text-gray-400">Mulai dari</p>
                    <p class="text-lg font-bold text-reoda">
                        {{ $minPrice ? 'Rp '.number_format($minPrice,0,',','.') : '-' }}
                        <span class="text-xs font-normal text-gray-500">/bln</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('compare.index', ['prop1' => $property->id]) }}" title="Bandingkan" class="rounded-md border border-stroke p-2 text-gray-400 hover:text-reoda hover:border-reoda transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </a>
                    <a href="{{ route($role . '.explore.show', $property) }}" class="rounded-md bg-reoda px-4 py-2 text-sm font-medium text-white hover:bg-reoda-dark transition">
                        {{ $role === 'manager' ? 'Analisis Kompetitor' : 'Lihat Detail' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $properties->links() }}
</div>

@else
<div class="rounded-sm border border-stroke bg-white py-16 text-center shadow-default">
    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    <h4 class="text-lg font-medium text-gray-900 mb-1">Tidak ada properti yang cocok</h4>
    <p class="text-gray-500 text-sm mb-4">Coba ubah filter pencarian atau kata kunci Anda.</p>
    <a href="{{ route($role . '.explore.index') }}" class="inline-flex rounded-md bg-reoda py-2 px-6 font-medium text-white hover:bg-reoda-dark text-sm">Lihat Semua Properti</a>
</div>
@endif
@endsection
