@extends('layouts.app')

@section('title', $property->name . ' - Detail Properti')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">
        Detail Properti
    </h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.properties.index') }}">Properti /</a></li>
            <li class="font-medium text-reoda">Detail</li>
        </ol>
    </nav>
</div>

<!-- Header Detail -->
<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm mb-6">
    <div class="relative z-20 h-35 md:h-65 bg-gray-200">
        <img src="{{ $property->cover_image_url }}" alt="Cover" class="h-full w-full object-cover object-center" />
    </div>
    <div class="px-6 pb-6 lg:pb-8 xl:pb-11.5">
        <div class="mt-6 flex flex-col sm:flex-row sm:items-end sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h3 class="text-3xl font-extrabold text-reoda-dark">{{ $property->name }}</h3>
                <p class="text-gray-500 flex items-center gap-1.5 mt-2 font-medium">
                    <svg class="w-4 h-4 shrink-0 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    {{ $property->address }}, {{ $property->village }}, {{ $property->district }}, {{ $property->city }}, {{ $property->province }} {{ $property->postal_code }}
                </p>
                <div class="mt-4 flex gap-2">
                    <span class="rounded-full bg-gray-100 py-1 px-3 text-xs font-bold text-gray-700 uppercase tracking-wide">{{ ucfirst($property->type) }}</span>
                    <span class="rounded-full {{ $property->status === 'active' ? 'bg-[#b0ebb0] text-[#1a5e1a]' : 'bg-[#f9c5c5] text-[#7a1c1c]' }} py-1 px-3 text-xs font-bold">{{ $property->status === 'active' ? 'Aktif' : 'Non-aktif' }}</span>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('manager.properties.edit', $property) }}" class="inline-flex rounded-lg border border-gray-200 bg-white py-2.5 px-6 font-semibold text-gray-700 hover:bg-gray-50 transition">Edit Info</a>
                <a href="{{ route('manager.properties.units.create', $property) }}" class="inline-flex rounded-lg bg-reoda py-2.5 px-6 font-semibold text-white hover:bg-reoda-dark transition shadow-sm">Tambah Unit</a>
            </div>
        </div>
        @if($property->description)
        <div class="mt-8 border-t border-gray-100 pt-6">
            <h4 class="font-bold text-reoda-dark mb-2 text-lg">Deskripsi</h4>
            <p class="text-gray-600 leading-relaxed">{{ $property->description }}</p>
        </div>
        @endif

        {{-- QR Code + Public Link --}}
        <div class="mt-6 border-t border-gray-100 pt-6 flex flex-col sm:flex-row gap-5 items-start">
            <div class="text-center">
                <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">QR Code Hunian</p>
                <div class="inline-block bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate(url('/property/' . $property->property_code)) !!}
                </div>
                <p class="text-xs font-mono text-gray-400 mt-1">{{ $property->property_code }}</p>
            </div>
            <div class="flex-1 space-y-3">
                @if($property->yearly_discount_percent > 0)
                <div class="inline-flex rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-bold">
                    Diskon {{ $property->yearly_discount_percent }}% untuk pembayaran tahunan
                </div>
                @endif
                <div class="flex flex-wrap gap-2 mt-2">
                    <a href="{{ url('/property/' . $property->property_code) }}" target="_blank"
                        class="inline-flex items-center gap-2 rounded-lg border border-reoda text-reoda px-4 py-2 text-sm font-medium hover:bg-reoda hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Lihat Halaman Publik
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Property Terms --}}
@if($property->property_terms)
<div class="rounded-2xl bg-white shadow-sm p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-extrabold text-reoda-dark mb-4">Ketentuan & Peraturan Hunian</h3>
    <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
        <p class="text-sm text-amber-800 whitespace-pre-line leading-relaxed">{{ $property->property_terms }}</p>
    </div>
</div>
@endif

{{-- Galeri Foto Properti --}}
<div class="mb-6">
    @include('components.media-gallery', [
        'mediaItems'   => $property->media,
        'uploadRoute'  => 'manager.properties.media.store',
        'uploadParams' => ['property' => $property->id],
        'maxImages'    => 5,
        'title'        => 'Galeri Foto Properti',
    ])
</div>

{{-- Peta --}}
@if($property->latitude && $property->longitude)
<div class="rounded-2xl bg-white shadow-sm p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-extrabold text-reoda-dark mb-4">Lokasi di Peta</h3>
    <div id="detail-map" style="height:250px;border-radius:12px;" class="border border-stroke"></div>
    @if($property->maps_url)
    <a href="{{ $property->maps_url }}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-sm text-reoda hover:underline">
        🗺️ Lihat di OpenStreetMap
    </a>
    @endif
</div>
@endif

<!-- Daftar Unit Kamar -->
<div class="mb-4 mt-2 flex items-center justify-between">
    <h3 class="text-xl font-extrabold text-reoda-dark">Daftar Unit / Kamar</h3>
</div>
<div class="rounded-2xl bg-white shadow-sm p-4 sm:p-6 overflow-hidden border border-gray-200">
    @if($property->units->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] border-separate" style="border-spacing: 0 8px;">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-reoda-lightest text-left text-sm font-bold text-reoda-dark rounded-l-full">Kode Unit</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-left text-sm font-bold text-reoda-dark mx-1">Tipe</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-left text-sm font-bold text-reoda-dark mx-1">Harga Sewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Status</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-r-full">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($property->units as $unit)
                <tr class="even:bg-[#e6f4f1] odd:bg-white">
                    <td class="px-6 py-4 rounded-l-full">
                        <p class="font-extrabold text-reoda-dark text-sm">{{ $unit->unit_code }}</p>
                        <p class="text-xs text-gray-500 font-medium">{{ $unit->name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-reoda-dark text-sm">{{ $unit->type }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-reoda-dark text-sm">Rp {{ number_format($unit->rent_price ?? $unit->rent_amount, 0, ',', '.') }}<span class="text-[10px] text-gray-500 font-medium">/bln</span></p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold 
                            {{ $unit->status === 'occupied' ? 'bg-[#b0ebb0] text-[#1a5e1a]' : ($unit->status === 'available' ? 'bg-[#cce3fc] text-[#1c4d82]' : 'bg-gray-200 text-gray-700') }}">
                            {{ $unit->status === 'occupied' ? 'Disewa' : ($unit->status === 'available' ? 'Tersedia' : ucfirst($unit->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 rounded-r-full text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('manager.units.show', $unit) }}" title="Detail"
                               class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-reoda-lightest text-reoda-dark hover:bg-reoda-lighter transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <a href="{{ route('manager.units.edit', $unit) }}" title="Edit"
                               class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-12 text-center">
        <svg class="mx-auto h-14 w-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        <p class="text-gray-500 mb-4 font-medium">Belum ada unit/kamar di lokasi ini.</p>
        <a href="{{ route('manager.properties.units.create', $property) }}" class="inline-flex rounded-lg bg-reoda py-2.5 px-6 font-semibold text-white hover:bg-reoda-dark transition">Tambah Unit Pertama</a>
    </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

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
        .bindPopup('<strong>{{ addslashes($property->name) }}</strong>')
        .openPopup();
});
</script>
@endif
@endpush
