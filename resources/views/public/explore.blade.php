@extends('layouts.guest')

@section('title', 'Explore Hunian - REODA')

@section('content')

{{-- Page Header --}}
<div class="bg-linear-to-r from-reoda to-teal-500 text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold">🔍 Explore Hunian</h1>
        <p class="text-white/80 mt-1 text-sm">Temukan kos, kontrakan, dan apartemen terbaik di seluruh Indonesia</p>

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('explore.public') }}" class="mt-5 flex flex-col sm:flex-row gap-3">
            <div class="flex-1 flex items-center gap-3 bg-white rounded-xl px-4 py-3 shadow-sm">
                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nama hunian, kota, atau alamat..."
                    class="w-full text-sm text-gray-800 placeholder-gray-400 outline-none font-medium">
            </div>
            <button type="submit" class="bg-white text-reoda-dark font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition text-sm shadow-sm">
                Cari
            </button>
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- ===== SIDEBAR FILTER ===== --}}
        <aside class="lg:w-64 shrink-0">
            <form method="GET" action="{{ route('explore.public') }}" id="filter-form">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-20 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900">Filter</h3>
                        @if(request()->hasAny(['type','city','province','min_price','max_price','search']))
                        <a href="{{ route('explore.public') }}" class="text-xs text-red-500 hover:underline">Reset</a>
                        @endif
                    </div>

                    {{-- Preserve search --}}
                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    {{-- Tipe Hunian --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Tipe Hunian</label>
                        <div class="space-y-2">
                            @foreach(['kos'=>'🏠 Kos-kosan','kontrakan'=>'🏡 Kontrakan','apartemen'=>'🏢 Apartemen'] as $val=>$lbl)
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="radio" name="type" value="{{ $val }}" class="accent-reoda"
                                    {{ request('type')===$val ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                                <span class="text-sm text-gray-700 group-hover:text-reoda transition">{{ $lbl }}</span>
                            </label>
                            @endforeach
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="radio" name="type" value="" class="accent-reoda" {{ !request('type') ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-sm text-gray-400 group-hover:text-gray-600 transition">Semua Tipe</span>
                            </label>
                        </div>
                    </div>

                    {{-- Provinsi --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Provinsi</label>
                        <select name="province" id="explore-province"
                            class="w-full rounded-lg border border-stroke py-2.5 px-3 text-sm outline-none focus:border-reoda">
                            <option value="">Semua Provinsi</option>
                            @foreach($provinces as $p)
                            <option value="{{ $p }}" {{ request('province')===$p ? 'selected':'' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Kota / Kabupaten</label>
                        <select name="city" id="explore-city"
                            class="w-full rounded-lg border border-stroke py-2.5 px-3 text-sm outline-none focus:border-reoda">
                            <option value="">Semua Kota</option>
                        </select>
                    </div>

                    {{-- Harga --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Harga / Bulan</label>
                        <div class="space-y-2">
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-gray-400">Rp</span>
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                    placeholder="Min" step="100000" min="0"
                                    class="w-full rounded-lg border border-stroke py-2.5 pl-9 pr-3 text-sm outline-none focus:border-reoda">
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-gray-400">Rp</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                    placeholder="Maks" step="100000" min="0"
                                    class="w-full rounded-lg border border-stroke py-2.5 pl-9 pr-3 text-sm outline-none focus:border-reoda">
                            </div>
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Urutkan</label>
                        <select name="sort" class="w-full rounded-lg border border-stroke py-2.5 px-3 text-sm outline-none focus:border-reoda" onchange="this.form.submit()">
                            <option value="latest"    {{ request('sort','latest')==='latest'    ? 'selected':'' }}>Terbaru</option>
                            <option value="price_asc" {{ request('sort')==='price_asc'          ? 'selected':'' }}>Harga Terendah</option>
                            <option value="price_desc"{{ request('sort')==='price_desc'         ? 'selected':'' }}>Harga Tertinggi</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-reoda text-white py-2.5 rounded-xl font-semibold text-sm hover:bg-reoda-dark transition">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </aside>

        {{-- ===== LISTING GRID ===== --}}
        <div class="flex-1 min-w-0">

            {{-- Result count + active filters --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div>
                    <p class="font-bold text-gray-900">
                        <span class="text-reoda text-xl">{{ $properties->total() }}</span> hunian ditemukan
                        @if(request('search')) <span class="text-sm text-gray-400 font-normal">untuk "{{ request('search') }}"</span> @endif
                    </p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @if(request('type'))
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-reoda-lightest text-reoda text-xs font-semibold px-3 py-1">
                            {{ ucfirst(request('type')) }}
                            <a href="{{ route('explore.public', array_merge(request()->except('type','page'))) }}" class="hover:text-red-500">✕</a>
                        </span>
                        @endif
                        @if(request('city'))
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1">
                            {{ request('city') }}
                            <a href="{{ route('explore.public', array_merge(request()->except('city','page'))) }}" class="hover:text-red-500">✕</a>
                        </span>
                        @endif
                        @if(request('province'))
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold px-3 py-1">
                            {{ request('province') }}
                            <a href="{{ route('explore.public', array_merge(request()->except('province','page'))) }}" class="hover:text-red-500">✕</a>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cards --}}
            @if($properties->isEmpty())
            <div class="py-20 text-center">
                <div class="text-7xl mb-4">🏚️</div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada hunian yang ditemukan</h3>
                <p class="text-gray-400 text-sm mb-6">Coba ubah filter atau kata kunci pencarian Anda.</p>
                <a href="{{ route('explore.public') }}" class="inline-flex rounded-xl bg-reoda px-6 py-3 font-semibold text-white text-sm hover:bg-reoda-dark transition">
                    Lihat Semua Hunian
                </a>
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($properties as $property)
                @php
                    $minPrice = $property->units->min('rent_price');
                    $badge = match($property->type) {
                        'kos'       => ['bg-emerald-100 text-emerald-700', '🏠 Kos'],
                        'kontrakan' => ['bg-blue-100 text-blue-700',       '🏡 Kontrakan'],
                        'apartemen' => ['bg-purple-100 text-purple-700',   '🏢 Apartemen'],
                        default     => ['bg-gray-100 text-gray-600',       ucfirst($property->type)],
                    };
                @endphp
                <a href="{{ url('/property/' . $property->property_code) }}"
                    class="property-card group block rounded-2xl border border-gray-100 bg-white overflow-hidden shadow-sm">

                    {{-- Image --}}
                    <div class="relative h-44 overflow-hidden bg-linear-to-br from-gray-100 to-gray-200">
                        @php
                            $images = [];
                            if (isset($property->media) && $property->media->count() > 0) {
                                $images = $property->media->map->url->toArray();
                            } elseif ($property->cover_image_url) {
                                $images[] = $property->cover_image_url;
                            }
                        @endphp
                        @if(empty($images))
                        <div class="w-full h-full flex items-center justify-center text-5xl bg-linear-to-br from-emerald-50 to-teal-100">
                            @switch($property->type)
                                @case('kos') 🏠 @break
                                @case('kontrakan') 🏡 @break
                                @case('apartemen') 🏢 @break
                                @default 🏘️
                            @endswitch
                        </div>
                        @else
                        <x-carousel :images="$images" :alt="$property->name" heightClass="h-44" />
                        @endif
                        <span class="absolute top-3 left-3 text-xs font-bold px-3 py-1 rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                        @if($property->available_units_count > 0)
                        <span class="absolute top-3 right-3 text-xs font-semibold bg-reoda text-white px-2.5 py-1 rounded-full">
                            {{ $property->available_units_count }} tersedia
                        </span>
                        @else
                        <span class="absolute top-3 right-3 text-xs font-semibold bg-gray-600 text-white px-2.5 py-1 rounded-full">
                            Penuh
                        </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 text-sm leading-snug group-hover:text-reoda transition line-clamp-1">
                            {{ $property->name }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3 text-reoda shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $property->district ? $property->district . ', ' : '' }}{{ $property->city }}
                        </p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                @if($minPrice)
                                <p class="text-xs text-gray-400">mulai</p>
                                <p class="text-base font-extrabold text-reoda">Rp {{ number_format($minPrice, 0, ',', '.') }}<span class="text-xs text-gray-400 font-normal">/bln</span></p>
                                @else
                                <p class="text-sm text-gray-400 italic">Harga belum tersedia</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs text-reoda bg-reoda-lightest rounded-full px-3 py-1 font-semibold group-hover:bg-reoda group-hover:text-white transition">
                                Lihat →
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($properties->hasPages())
            <div class="mt-8">{{ $properties->links() }}</div>
            @endif
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .property-card:hover { transform: translateY(-3px); box-shadow: 0 16px 32px rgba(0,0,0,0.1); transition: all 0.3s ease; }
    .property-card { transition: all 0.3s ease; }
    .property-img { transition: transform 0.4s ease; }
    .property-card:hover .property-img { transform: scale(1.05); }
</style>
@endpush

@push('scripts')
{{-- Data PHP ditempatkan di script type JSON agar tidak di-lint sebagai JS --}}
<script type="application/json" id="explore-cities-data">{!! json_encode($citiesByProvince) !!}</script>
<script type="application/json" id="explore-all-cities">{!! json_encode($allCities) !!}</script>

<script>
(function() {
    var citiesByProv = JSON.parse(document.getElementById('explore-cities-data').textContent);
    var allCities    = JSON.parse(document.getElementById('explore-all-cities').textContent);
    var activeCity   = "{{ request('city', '') }}";

    var selectProv = document.getElementById('explore-province');
    var selectCity = document.getElementById('explore-city');

    if (!selectProv || !selectCity) return;

    function renderCities(prov, selectedCity) {
        var cities = (prov && citiesByProv[prov]) ? citiesByProv[prov] : allCities;
        selectCity.innerHTML = '<option value="">Semua Kota</option>';
        cities.forEach(function(c) {
            var opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c;
            if (c === selectedCity) opt.selected = true;
            selectCity.appendChild(opt);
        });
    }

    // Init on page load
    renderCities(selectProv.value, activeCity);

    // On province change - update cities
    selectProv.addEventListener('change', function() {
        renderCities(this.value, '');
    });
})();
</script>
@endpush

@endsection
