@extends('layouts.app')

@php $role = auth()->user()->role; @endphp

@section('title', $property->name . ' - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Detail Properti</h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route($role . '.dashboard') }}">Dashboard /</a></li>
            <li><a class="font-medium hover:text-reoda" href="{{ route($role . '.explore.index') }}">Explore /</a></li>
            <li class="font-medium text-reoda">{{ $property->name }}</li>
        </ol>
    </nav>
</div>

<!-- Hero Section -->
<div class="rounded-sm border border-stroke bg-white shadow-default mb-6 overflow-hidden">
    <div class="h-56 md:h-80 bg-gray-100 relative">
        <img src="{{ $property->cover_image_url ?? 'https://placehold.co/1200x400/4C74AF/ffffff?text='.urlencode($property->name) }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-reoda-dark/60 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-6">
            <span class="mb-2 inline-block rounded bg-reoda px-3 py-1 text-xs font-bold text-white uppercase">{{ $property->type }}</span>
            <h2 class="text-2xl md:text-3xl font-bold text-white">{{ $property->name }}</h2>
            <p class="text-sm text-white/80 flex items-center gap-1.5 mt-1">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $property->address }}, {{ $property->village }}, {{ $property->district }}, {{ $property->city }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Description + Units -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Description -->
        @if($property->description)
        <div class="rounded-sm border border-stroke bg-white shadow-default p-6">
            <h4 class="font-bold text-black text-lg mb-3">Tentang Properti Ini</h4>
            <p class="text-gray-600 leading-relaxed">{{ $property->description }}</p>
        </div>
        @endif

        <!-- Available Units -->
        <div class="rounded-sm border border-stroke bg-white shadow-default">
            <div class="py-4 px-6 border-b border-stroke flex items-center justify-between">
                <h4 class="font-bold text-black text-lg">Kamar / Unit Tersedia</h4>
                <span class="text-sm text-success font-medium">{{ $availableUnits->count() }} tersedia</span>
            </div>

            @if($availableUnits->count() > 0)
            <div class="divide-y divide-stroke">
                @foreach($availableUnits as $unit)
                <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-gray-50 transition">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-black text-base">{{ $unit->unit_code }}</span>
                            <span class="rounded bg-success-50 text-success-600 text-xs font-medium px-2 py-0.5">Tersedia</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $unit->name }}</p>
                        <div class="flex flex-wrap gap-3 mt-2">
                            @if($unit->type)
                            <span class="flex items-center gap-1 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                {{ $unit->type }}
                            </span>
                            @endif
                            @if($unit->area_sqm)
                            <span class="flex items-center gap-1 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                {{ $unit->area_sqm }} m²
                            </span>
                            @endif
                            @if($unit->floor)
                            <span class="flex items-center gap-1 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                Lantai {{ $unit->floor }}
                            </span>
                            @endif
                        </div>
                        @if($unit->description)
                        <p class="text-xs text-gray-500 mt-1">{{ $unit->description }}</p>
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-lg font-bold text-reoda">Rp {{ number_format($unit->rent_price, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mb-2">/bulan</p>
                        @if($role === 'tenant')
                        <button onclick="alert('Fitur pengajuan sewa akan segera hadir!')" class="rounded bg-reoda px-3 py-1.5 text-xs font-medium text-white hover:bg-reoda-dark transition w-full">
                            Ajukan Sewa
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center text-gray-500">Tidak ada unit yang tersedia saat ini.</div>
            @endif
        </div>

        <!-- Rented Units -->
        @if($rentedUnits->count() > 0)
        <div class="rounded-sm border border-stroke bg-white shadow-default">
            <div class="py-4 px-6 border-b border-stroke">
                <h4 class="font-bold text-black text-lg">Unit Sedang Ditempati ({{ $rentedUnits->count() }})</h4>
            </div>
            <div class="divide-y divide-stroke">
                @foreach($rentedUnits as $unit)
                <div class="p-5 flex items-center justify-between opacity-60">
                    <div>
                        <span class="font-semibold text-sm text-black">{{ $unit->unit_code }}</span>
                        <span class="ml-2 rounded bg-gray-100 text-gray-500 text-xs font-medium px-2 py-0.5">Disewa</span>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $unit->name }}</p>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Rp {{ number_format($unit->rent_price, 0, ',', '.') }}/bln</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Right: Contact Card -->
    <div class="space-y-6">
        @if($property->manager_id === auth()->id())
        <!-- Kelola Properti Card (For Owner) -->
        <div class="rounded-sm border border-stroke bg-white shadow-default p-6 sticky top-6">
            <h4 class="font-bold text-black text-lg mb-4">Properti Anda</h4>
            <div class="rounded-lg bg-reoda-lightest border border-reoda-lighter p-4 text-sm text-gray-600 text-center mb-3">
                <svg class="w-8 h-8 text-reoda mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Ini adalah properti milik Anda yang sedang tampil di Explore Market.
            </div>
            <a href="{{ route('manager.properties.show', $property) }}" class="flex w-full items-center justify-center gap-2 rounded-md bg-reoda py-2.5 text-sm font-medium text-white hover:bg-reoda-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Edit & Kelola Properti
            </a>
        </div>
        @else
        <!-- Pengelola Card (For Tenants/Other Managers) -->
        <div class="rounded-sm border border-stroke bg-white shadow-default p-6 sticky top-6">
            <h4 class="font-bold text-black text-lg mb-4">Hubungi Pengelola</h4>
            <div class="flex items-center gap-3 mb-5">
                <div class="h-12 w-12 rounded-full overflow-hidden ring-2 ring-reoda/20">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($property->manager->name ?? 'P') }}&background=4C74AF&color=fff&size=48" alt="Pengelola">
                </div>
                <div>
                    <p class="font-semibold text-black">{{ $property->manager->name ?? 'Pengelola' }}</p>
                    <p class="text-xs text-gray-500">Pemilik Properti</p>
                </div>
            </div>
            @if($property->manager->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $property->manager->phone) }}" target="_blank" class="flex w-full items-center justify-center gap-2 rounded-md bg-green-500 py-2.5 text-sm font-medium text-white hover:bg-green-600 transition mb-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat via WhatsApp
            </a>
            @endif
            <div class="rounded-lg bg-reoda-lightest border border-reoda-lighter p-4 text-sm text-gray-600 text-center">
                <svg class="w-8 h-8 text-reoda mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Fitur pengajuan sewa online akan hadir segera!
            </div>
        </div>
        @endif

        <!-- Quick Stats -->
        <div class="rounded-sm border border-stroke bg-white shadow-default p-6">
            <h4 class="font-bold text-black mb-4">Info Singkat</h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Unit</span>
                    <span class="font-medium text-black">{{ $property->units->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tersedia</span>
                    <span class="font-medium text-success">{{ $availableUnits->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Ditempati</span>
                    <span class="font-medium text-gray-700">{{ $rentedUnits->count() }}</span>
                </div>
                @if($availableUnits->count() > 0)
                <div class="flex justify-between border-t border-stroke pt-3">
                    <span class="text-gray-500">Harga Mulai</span>
                    <span class="font-bold text-reoda">Rp {{ number_format($availableUnits->min('rent_price'), 0, ',', '.') }}/bln</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
