@extends('layouts.app')
@section('title', 'Edit Properti - REODA')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 300px; width: 100%; border-radius: 12px; z-index: 1; }
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Edit Properti</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.properties.index') }}">Properti /</a></li>
        <li class="font-medium text-reoda">Edit</li>
    </ol></nav>
</div>

<form action="{{ route('manager.properties.update', $property) }}" method="POST" x-data="propertyForm()">
    @csrf
    @method('PUT')

    @if($errors->any())
    <div class="mb-5 rounded-lg bg-error-50 border border-error-200 p-4 text-sm text-error-700">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Bagian 1: Informasi Dasar --}}
    <div class="rounded-xl border border-stroke bg-white shadow-sm mb-5">
        <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Informasi Dasar</h4></div>
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Properti <span class="text-error-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $property->name) }}" required
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Hunian <span class="text-error-500">*</span></label>
                    <select name="type" x-model="propertyType" required
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                        <option value="kos" {{ old('type',$property->type)=='kos' ? 'selected' : '' }}>Kos-kosan</option>
                        <option value="kontrakan" {{ old('type',$property->type)=='kontrakan' ? 'selected' : '' }}>Kontrakan</option>
                        <option value="apartemen" {{ old('type',$property->type)=='apartemen' ? 'selected' : '' }}>Apartemen</option>
                        <option value="rumah" {{ old('type',$property->type)=='rumah' ? 'selected' : '' }}>Rumah</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                        <option value="active" {{ old('status',$property->status)=='active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status',$property->status)=='inactive' ? 'selected' : '' }}>Non-aktif</option>
                    </select>
                </div>
                <div x-show="propertyType === 'kontrakan' || propertyType === 'apartemen' || propertyType === 'rumah'" style="display:none;">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Diskon Jika Bayar Tahunan (%)</label>
                    <input type="number" name="yearly_discount_percent" value="{{ old('yearly_discount_percent', $property->yearly_discount_percent ?? 0) }}"
                        min="0" max="50" step="0.5"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">{{ old('description', $property->description) }}</textarea>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Ketentuan / Peraturan Hunian
                    <span class="text-xs text-gray-400 font-normal ml-1">— ditampilkan ke calon penyewa</span>
                </label>
                <textarea name="property_terms" rows="5"
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">{{ old('property_terms', $property->property_terms) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Bagian 2: Alamat & Lokasi --}}
    <div class="rounded-xl border border-stroke bg-white shadow-sm mb-5">
        <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Alamat & Lokasi</h4></div>
        <div class="p-6 space-y-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Alamat Lengkap <span class="text-error-500">*</span></label>
                <textarea name="address" rows="2" required
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">{{ old('address', $property->address) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Provinsi <span class="text-error-500">*</span></label>
                    <select id="select-province" x-model="selectedProvince" required
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                        <option value="">Pilih Provinsi</option>
                    </select>
                    <input type="hidden" name="province" x-bind:value="selectedProvinceName">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kota / Kabupaten <span class="text-error-500">*</span></label>
                    <select id="select-city" x-model="selectedCity" required :disabled="!selectedProvince"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition disabled:opacity-50">
                        <option value="">Pilih Kota/Kabupaten</option>
                    </select>
                    <input type="hidden" name="city" x-bind:value="selectedCityName">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kecamatan</label>
                    <input type="text" name="district" value="{{ old('district', $property->district) }}"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kelurahan / Desa</label>
                    <input type="text" name="village" value="{{ old('village', $property->village) }}"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $property->postal_code) }}"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
            </div>

            {{-- Peta --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Lokasi di Peta</label>
                    <div class="flex gap-2">
                        <input type="text" id="search-address" placeholder="Cari lokasi..."
                            class="rounded-lg border border-stroke py-2 px-3 text-sm outline-none focus:border-reoda transition w-56">
                        <button type="button" onclick="searchOnMap()"
                            class="rounded-lg bg-reoda px-4 py-2 text-sm font-medium text-white hover:bg-reoda-dark transition">Cari</button>
                    </div>
                </div>
                <div id="map" class="border border-stroke"></div>
                <input type="hidden" name="latitude" id="lat-input" value="{{ old('latitude', $property->latitude) }}">
                <input type="hidden" name="longitude" id="lng-input" value="{{ old('longitude', $property->longitude) }}">
                <input type="hidden" name="maps_url" id="maps-url-input" value="{{ old('maps_url', $property->maps_url) }}">
                <p id="map-coords" class="text-xs text-gray-400 mt-2">
                    @if($property->latitude)
                        📍 Lat: {{ $property->latitude }}, Lng: {{ $property->longitude }}
                    @else
                        Belum ada titik yang dipilih.
                    @endif
                </p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">RT/RW</label>
                <input type="text" name="rt_rw" value="{{ old('rt_rw', $property->rt_rw) }}"
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex gap-3">
        <button type="submit" class="rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">
            Simpan Perubahan
        </button>
        <a href="{{ route('manager.properties.show', $property) }}"
            class="rounded-lg border border-stroke px-6 py-3 font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, marker;
const initLat = {{ $property->latitude ?? -6.2 }};
const initLng = {{ $property->longitude ?? 106.8 }};
const hasCoords = {{ $property->latitude ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', () => {
    map = L.map('map').setView([initLat, initLng], hasCoords ? 16 : 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19
    }).addTo(map);

    if (hasCoords) {
        marker = L.marker([initLat, initLng]).addTo(map);
    }

    map.on('click', (e) => setMarker(e.latlng.lat, e.latlng.lng));
});

function setMarker(lat, lng) {
    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);
    map.setView([lat, lng], 16);
    document.getElementById('lat-input').value = lat.toFixed(7);
    document.getElementById('lng-input').value = lng.toFixed(7);
    document.getElementById('maps-url-input').value = `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}&zoom=16`;
    document.getElementById('map-coords').textContent = `📍 Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
}

function searchOnMap() {
    const q = document.getElementById('search-address').value.trim();
    if (!q) return;
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1&countrycodes=id`)
        .then(r => r.json())
        .then(data => {
            if (data.length > 0) setMarker(parseFloat(data[0].lat), parseFloat(data[0].lon));
            else alert('Lokasi tidak ditemukan.');
        });
}

document.addEventListener('alpine:init', () => {
    Alpine.data('propertyForm', () => ({
        propertyType: '{{ old("type", $property->type) }}',
        selectedProvince: '',
        selectedProvinceName: '{{ old("province", $property->province) }}',
        selectedCity: '',
        selectedCityName: '{{ old("city", $property->city) }}',
        provinces: [],
        cities: [],

        init() {
            fetch('/data/provinces.json')
                .then(r => r.json())
                .then(data => {
                    this.provinces = data;
                    const selProv = document.getElementById('select-province');
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        if (p.name === this.selectedProvinceName) opt.selected = true;
                        selProv.appendChild(opt);
                    });
                    if (this.selectedProvinceName) {
                        const found = data.find(p => p.name === this.selectedProvinceName);
                        if (found) { this.selectedProvince = found.id; this.loadCities(found.id); }
                    }
                });

            this.$watch('selectedProvince', (id) => {
                const found = this.provinces.find(p => p.id === id);
                this.selectedProvinceName = found ? found.name : '';
                this.selectedCity = '';
                this.selectedCityName = '';
                if (id) this.loadCities(id);
            });

            this.$watch('selectedCity', (id) => {
                const found = this.cities.find(c => c.id === id);
                this.selectedCityName = found ? found.name : '';
            });
        },

        loadCities(provinceId) {
            fetch('/data/cities.json')
                .then(r => r.json())
                .then(data => {
                    this.cities = data.filter(c => String(c.province_id) === String(provinceId));
                    const sel = document.getElementById('select-city');
                    sel.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                    this.cities.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name;
                        if (c.name === '{{ old("city", $property->city) }}') opt.selected = true;
                        sel.appendChild(opt);
                    });
                });
        }
    }));
});
</script>
@endpush
