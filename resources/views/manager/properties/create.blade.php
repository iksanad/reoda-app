@extends('layouts.app')
@section('title', 'Tambah Properti - REODA')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 300px; width: 100%; border-radius: 12px; z-index: 1; }
    .leaflet-container { font-family: inherit; }
    .leaflet-container img { max-width: none !important; }
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Tambah Lokasi Properti</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.properties.index') }}">Properti /</a></li>
        <li class="font-medium text-reoda">Tambah</li>
    </ol></nav>
</div>

<form action="{{ route('manager.properties.store') }}" method="POST" enctype="multipart/form-data" x-data="propertyForm()">
    @csrf
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
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Kos Bunga Mawar" required
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Hunian <span class="text-error-500">*</span></label>
                    <select name="type" x-model="propertyType" required
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                        <option value="">Pilih Jenis</option>
                        <option value="kos" {{ old('type')=='kos' ? 'selected' : '' }}>Kos-kosan</option>
                        <option value="kontrakan" {{ old('type')=='kontrakan' ? 'selected' : '' }}>Kontrakan</option>
                        <option value="apartemen" {{ old('type')=='apartemen' ? 'selected' : '' }}>Apartemen</option>
                        <option value="rumah" {{ old('type')=='rumah' ? 'selected' : '' }}>Rumah</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                <textarea name="description" rows="3" placeholder="Tuliskan deskripsi umum mengenai properti ini..."
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">{{ old('description') }}</textarea>
            </div>

            {{-- Diskon Tahunan — Hanya untuk Kontrakan/Apartemen --}}
            <div x-show="propertyType === 'kontrakan' || propertyType === 'apartemen' || propertyType === 'rumah'" style="display:none;">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Diskon Jika Dibayar Tahunan (%)
                    <span class="text-xs text-gray-400 font-normal ml-1">— contoh: 10 berarti 10% lebih murah jika bayar 1 tahun sekaligus</span>
                </label>
                <input type="number" name="yearly_discount_percent" value="{{ old('yearly_discount_percent', 0) }}"
                    min="0" max="50" step="0.5" placeholder="0"
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
            </div>

            {{-- Ketentuan Hunian --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Ketentuan / Peraturan Hunian (Opsional)
                    <span class="text-xs text-gray-400 font-normal ml-1">— akan ditampilkan kepada calon penyewa saat mengajukan kontrak</span>
                </label>
                <textarea name="property_terms" rows="5"
                    placeholder="Contoh:&#10;1. Dilarang membawa tamu menginap.&#10;2. Jam malam pukul 22.00 WIB.&#10;3. Pembayaran paling lambat tanggal 10 setiap bulannya.&#10;4. Dilarang memasak menggunakan kompor gas."
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">{{ old('property_terms') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Bagian 2: Alamat & Lokasi --}}
    <div class="rounded-xl border border-stroke bg-white shadow-sm mb-5">
        <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Alamat & Lokasi</h4></div>
        <div class="p-6 space-y-5">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Alamat Lengkap <span class="text-error-500">*</span></label>
                <textarea name="address" rows="2" required placeholder="Contoh: Jl. Sudirman No. 123, Gang Melati"
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">{{ old('address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Provinsi --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Provinsi <span class="text-error-500">*</span></label>
                    <select id="select-province" x-model="selectedProvince" required
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                        <option value="">Pilih Provinsi</option>
                    </select>
                    <input type="hidden" name="province" x-bind:value="selectedProvinceName">
                </div>
                {{-- Kota/Kabupaten --}}
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
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kecamatan <span class="text-error-500">*</span></label>
                    <select id="select-district" x-model="selectedDistrict" required :disabled="!selectedCity"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition disabled:opacity-50">
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <input type="hidden" name="district" x-bind:value="selectedDistrictName">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kelurahan / Desa <span class="text-error-500">*</span></label>
                    <select id="select-village" x-model="selectedVillage" required :disabled="!selectedDistrict"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition disabled:opacity-50">
                        <option value="">Pilih Kelurahan/Desa</option>
                    </select>
                    <input type="hidden" name="village" x-bind:value="selectedVillageName">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="60241"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
            </div>

            {{-- Peta Interaktif --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Lokasi di Peta</label>
                    <div class="flex gap-2">
                        <input type="text" id="search-address" placeholder="Cari alamat di peta..." 
                            class="rounded-lg border border-stroke py-2 px-3 text-sm outline-none focus:border-reoda transition w-64">
                        <button type="button" onclick="searchOnMap()" 
                            class="rounded-lg bg-reoda px-4 py-2 text-sm font-medium text-white hover:bg-reoda-dark transition">
                            Cari
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mb-3">Klik di peta untuk menentukan titik lokasi properti, atau gunakan kolom pencarian di atas.</p>
                <div id="map" class="border border-stroke"></div>
                <input type="hidden" name="latitude" id="lat-input" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="lng-input" value="{{ old('longitude') }}">
                <input type="hidden" name="maps_url" id="maps-url-input" value="{{ old('maps_url') }}">
                <p id="map-coords" class="text-xs text-gray-400 mt-2">Belum ada titik yang dipilih.</p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">RT/RW (Opsional)</label>
                <input type="text" name="rt_rw" value="{{ old('rt_rw') }}" placeholder="001/002"
                    class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
        </div>
    </div>

    {{-- Bagian 3: Submit --}}
    <div class="flex gap-3">
        <button type="submit" class="rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">
            Simpan Properti
        </button>
        <a href="{{ route('manager.properties.index') }}" class="rounded-lg border border-stroke px-6 py-3 font-medium text-gray-700 hover:bg-gray-50 transition">
            Batal
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ===== LEAFLET MAP =====
let map, marker;
const defaultLat = -6.2, defaultLng = 106.8;

document.addEventListener('DOMContentLoaded', () => {
    map = L.map('map').setView([defaultLat, defaultLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    // Click on map to set marker
    map.on('click', (e) => setMarker(e.latlng.lat, e.latlng.lng));

    // Restore old value
    const oldLat = document.getElementById('lat-input').value;
    const oldLng = document.getElementById('lng-input').value;
    if (oldLat && oldLng) setMarker(parseFloat(oldLat), parseFloat(oldLng));
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
            if (data.length > 0) {
                setMarker(parseFloat(data[0].lat), parseFloat(data[0].lon));
            } else {
                alert('Lokasi tidak ditemukan. Coba dengan nama yang lebih spesifik.');
            }
        });
}

function autoCenterMap(province, city, district, village) {
    if (!map) return;
    let queryParts = [];
    if (village) queryParts.push(village);
    if (district) queryParts.push(district);
    if (city) queryParts.push(city);
    if (province) queryParts.push(province);
    
    if (queryParts.length === 0) return;
    const query = queryParts.join(', ') + ', Indonesia';
    
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=id`)
        .then(r => r.json())
        .then(data => {
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                let zoom = 10;
                if (village) zoom = 15;
                else if (district) zoom = 13;
                else if (city) zoom = 11;

                if (!marker) {
                    setMarker(lat, lng);
                } else {
                    map.setView([lat, lng], zoom);
                }
            }
        }).catch(e => console.error("Geocoding failed", e));
}

// ===== ALPINE.JS DATA (EMSIFA API) =====
document.addEventListener('alpine:init', () => {
    Alpine.data('propertyForm', () => ({
        propertyType: '{{ old("type") }}',
        selectedProvince: '',
        selectedProvinceName: '{{ old("province") }}',
        selectedCity: '',
        selectedCityName: '{{ old("city") }}',
        selectedDistrict: '',
        selectedDistrictName: '{{ old("district") }}',
        selectedVillage: '',
        selectedVillageName: '{{ old("village") }}',
        provinces: [],
        cities: [],
        districts: [],
        villages: [],

        init() {
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
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
                this.selectedDistrict = '';
                this.selectedDistrictName = '';
                this.selectedVillage = '';
                this.selectedVillageName = '';
                if (id) this.loadCities(id);
                if (this.selectedProvinceName) autoCenterMap(this.selectedProvinceName, '', '', '');
            });

            this.$watch('selectedCity', (id) => {
                const found = this.cities.find(c => c.id === id);
                this.selectedCityName = found ? found.name : '';
                this.selectedDistrict = '';
                this.selectedDistrictName = '';
                this.selectedVillage = '';
                this.selectedVillageName = '';
                if (id) this.loadDistricts(id);
                if (this.selectedCityName) autoCenterMap(this.selectedProvinceName, this.selectedCityName, '', '');
            });

            this.$watch('selectedDistrict', (id) => {
                const found = this.districts.find(d => d.id === id);
                this.selectedDistrictName = found ? found.name : '';
                this.selectedVillage = '';
                this.selectedVillageName = '';
                if (id) this.loadVillages(id);
                if (this.selectedDistrictName) autoCenterMap(this.selectedProvinceName, this.selectedCityName, this.selectedDistrictName, '');
            });

            this.$watch('selectedVillage', (id) => {
                const found = this.villages.find(v => v.id === id);
                this.selectedVillageName = found ? found.name : '';
                if (this.selectedVillageName) autoCenterMap(this.selectedProvinceName, this.selectedCityName, this.selectedDistrictName, this.selectedVillageName);
            });
        },

        loadCities(provinceId) {
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                .then(r => r.json())
                .then(data => {
                    this.cities = data;
                    const sel = document.getElementById('select-city');
                    sel.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                    this.cities.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name;
                        if (c.name === '{{ old("city") }}') { opt.selected = true; this.selectedCity = c.id; this.loadDistricts(c.id); }
                        sel.appendChild(opt);
                    });
                });
        },

        loadDistricts(cityId) {
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${cityId}.json`)
                .then(r => r.json())
                .then(data => {
                    this.districts = data;
                    const sel = document.getElementById('select-district');
                    sel.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    this.districts.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.id;
                        opt.textContent = d.name;
                        if (d.name === '{{ old("district") }}') { opt.selected = true; this.selectedDistrict = d.id; this.loadVillages(d.id); }
                        sel.appendChild(opt);
                    });
                });
        },

        loadVillages(districtId) {
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`)
                .then(r => r.json())
                .then(data => {
                    this.villages = data;
                    const sel = document.getElementById('select-village');
                    sel.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                    this.villages.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.id;
                        opt.textContent = v.name;
                        if (v.name === '{{ old("village") }}') { opt.selected = true; this.selectedVillage = v.id; }
                        sel.appendChild(opt);
                    });
                });
        }
    }));
});
</script>
@endpush
