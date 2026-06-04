@extends('layouts.app')
@section('title', $property->name . ' - REODA')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #detail-map { height: 250px; border-radius: 12px; }
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-black">{{ $property->name }}</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ $property->address }}, {{ $property->city }}, {{ $property->province }}</p>
    </div>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="/">Beranda /</a></li>
        <li class="font-medium text-reoda">Detail Properti</li>
    </ol></nav>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Kolom Kiri: Info Properti --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Foto & Badge --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
            <div class="relative h-56 bg-gray-100">
                <img src="{{ $property->cover_image_url }}" alt="{{ $property->name }}"
                    class="w-full h-full object-cover">
                <span class="absolute top-4 left-4 inline-flex rounded-full px-3 py-1 text-xs font-bold text-white
                    @if($property->type === 'kos') bg-blue-500
                    @elseif($property->type === 'kontrakan') bg-green-500
                    @elseif($property->type === 'apartemen') bg-purple-500
                    @else bg-gray-500 @endif">
                    {{ ucfirst($property->type) }}
                </span>
            </div>
        </div>

        {{-- Deskripsi --}}
        @if($property->description)
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-3">Tentang Hunian Ini</h4>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $property->description }}</p>
        </div>
        @endif

        {{-- Unit/Kamar Tersedia --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4 flex items-center justify-between">
                <h4 class="font-bold text-black">Kamar / Unit Tersedia</h4>
                <span class="text-sm text-gray-500">{{ $property->units->count() }} unit tersedia</span>
            </div>
            @if($property->units->count() > 0)
            <div class="divide-y divide-stroke">
                @foreach($property->units as $unit)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-black text-sm">{{ $unit->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Kode: {{ $unit->unit_code }}
                            @if($unit->area_sqm) · {{ $unit->area_sqm }} m² @endif
                            @if($unit->floor) · Lantai {{ $unit->floor }} @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-reoda">Rp {{ number_format($unit->rent_price, 0, ',', '.') }}<span class="text-xs font-normal text-gray-400">/bln</span></p>
                        @if($property->yearly_discount_percent > 0 && in_array($property->type, ['kontrakan','apartemen','rumah']))
                        <p class="text-xs text-green-600 mt-0.5">Hemat {{ $property->yearly_discount_percent }}% jika bayar tahunan</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-sm text-gray-400">Tidak ada unit yang tersedia saat ini.</div>
            @endif
        </div>

        {{-- Fasilitas --}}
        @if($property->facilities->count() > 0)
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Fasilitas</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($property->facilities as $facility)
                <span class="inline-flex items-center gap-1.5 rounded-full border border-reoda/30 bg-reoda/5 px-3 py-1.5 text-xs font-medium text-reoda">
                    {{ $facility->name }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Ketentuan Hunian --}}
        @if($property->property_terms)
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Ketentuan & Peraturan Hunian</h4>
            <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
                <p class="text-sm text-amber-800 whitespace-pre-line leading-relaxed">{{ $property->property_terms }}</p>
            </div>
        </div>
        @endif

        {{-- Peta --}}
        @if($property->latitude && $property->longitude)
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Lokasi di Peta</h4>
            <div id="detail-map" class="border border-stroke"></div>
            @if($property->maps_url)
            <a href="{{ $property->maps_url }}" target="_blank"
                class="mt-3 inline-flex items-center gap-2 text-sm text-reoda hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Lihat di OpenStreetMap
            </a>
            @endif
        </div>
        @endif
    </div>

    {{-- Kolom Kanan: CTA --}}
    <div class="space-y-5">
        {{-- Info Card --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6 sticky top-24">
            <div class="text-center mb-5 pb-5 border-b border-stroke">
                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Mulai dari</p>
                @if($property->units->count() > 0)
                <p class="text-3xl font-extrabold text-reoda-dark">Rp {{ number_format($property->units->min('rent_price'), 0, ',', '.') }}</p>
                <p class="text-sm text-gray-400">/bulan</p>
                @else
                <p class="text-gray-400 text-sm">Tidak ada unit tersedia</p>
                @endif
            </div>

            <div class="space-y-3 mb-5 text-sm">
                <div class="flex items-center gap-3 text-gray-600">
                    <svg class="w-5 h-5 text-reoda shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span>{{ $property->district ?? $property->city }}, {{ $property->province }}</span>
                </div>
                <div class="flex items-center gap-3 text-gray-600">
                    <svg class="w-5 h-5 text-reoda shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>{{ $property->units->count() }} unit tersedia</span>
                </div>
                <div class="flex items-center gap-3 text-gray-600">
                    <svg class="w-5 h-5 text-reoda shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Pengelola: {{ $property->manager->name ?? '-' }}</span>
                </div>
            </div>

            @auth
                @if(auth()->user()->role === 'tenant')
                <a href="{{ route('tenant.contract.request', $property->property_code) }}"
                    class="flex w-full justify-center rounded-xl bg-reoda py-3 font-bold text-white hover:bg-reoda-dark transition">
                    Ajukan Kontrak Sewa
                </a>
                @endif
            @else
            <a href="{{ route('login') }}?redirect={{ url()->current() }}"
                class="flex w-full justify-center rounded-xl bg-reoda py-3 font-bold text-white hover:bg-reoda-dark transition">
                Login untuk Mengajukan Sewa
            </a>
            <p class="text-center text-xs text-gray-400 mt-2">Belum punya akun? <a href="{{ route('register') }}" class="text-reoda hover:underline">Daftar sekarang</a></p>
            @endauth

            {{-- QR Code Properti --}}
            <div class="mt-5 pt-5 border-t border-stroke text-center">
                <p class="text-xs text-gray-400 mb-3">Scan untuk bagikan ke teman</p>
                <div class="inline-block bg-white border border-gray-200 rounded-lg p-2">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate(url('/property/' . $property->property_code)) !!}
                </div>
                <p class="text-xs font-mono text-gray-500 mt-2">{{ $property->property_code }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($property->latitude && $property->longitude)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const map = L.map('detail-map').setView([{{ $property->latitude }}, {{ $property->longitude }}], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19
    }).addTo(map);
    L.marker([{{ $property->latitude }}, {{ $property->longitude }}])
        .addTo(map)
        .bindPopup('<strong>{{ addslashes($property->name) }}</strong><br>{{ addslashes($property->address) }}')
        .openPopup();
});
</script>
@endif
@endpush
