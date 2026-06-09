<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembanding Hunian - REODA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
    <style>
        .ts-control { border-radius: 0.5rem; padding: 0.625rem 0.75rem; border-color: #E2E8F0; }
        .ts-control.focus { border-color: #4C74AF; box-shadow: none; }
    </style>
</head>
<body class="bg-gray-50 font-sans">

{{-- Navbar --}}
<nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button onclick="window.history.length > 1 ? history.back() : window.location='/'" class="flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-reoda transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </button>
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('template/logo/Reoda-4C74AF.png') }}" alt="REODA" class="h-8">
            </a>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ url('/') }}" class="text-gray-600 hover:text-reoda transition">Beranda</a>
            @auth
            <a href="{{ auth()->user()->isManager() ? route('manager.dashboard') : route('tenant.dashboard') }}" class="rounded-lg bg-reoda px-4 py-2 text-white font-medium hover:bg-reoda-dark transition">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="rounded-lg bg-reoda px-4 py-2 text-white font-medium hover:bg-reoda-dark transition">Login</a>
            @endauth
        </div>
    </div>
</nav>



<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="text-center mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Pembanding Hunian</h1>
        <p class="text-gray-500">Pilih 2 properti untuk dibandingkan secara berdampingan</p>
    </div>

    {{-- Filter Form --}}
    {{-- Filter Form --}}
    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <form id="filter-form" method="GET" action="{{ route('compare.index') }}">
            @if(request('prop1')) <input type="hidden" name="prop1" value="{{ request('prop1') }}"> @endif
            @if(request('prop2')) <input type="hidden" name="prop2" value="{{ request('prop2') }}"> @endif
            
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-1/4">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Tipe Properti</label>
                    <select name="filter_type" class="w-full rounded-lg border border-stroke text-sm outline-none px-3 py-2.5">
                        <option value="">Semua Tipe</option>
                        <option value="kos" {{ request('filter_type') == 'kos' ? 'selected' : '' }}>Kos</option>
                        <option value="kontrakan" {{ request('filter_type') == 'kontrakan' ? 'selected' : '' }}>Kontrakan</option>
                        <option value="apartemen" {{ request('filter_type') == 'apartemen' ? 'selected' : '' }}>Apartemen</option>
                        <option value="rumah" {{ request('filter_type') == 'rumah' ? 'selected' : '' }}>Rumah</option>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Provinsi</label>
                    <select name="filter_province" id="select-province"
                        class="w-full rounded-lg border border-stroke text-sm outline-none px-3 py-2.5">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $p)
                            <option value="{{ $p }}" {{ request('filter_province') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Kota / Kab</label>
                    <select name="filter_city" id="select-city"
                        class="w-full rounded-lg border border-stroke text-sm outline-none px-3 py-2.5">
                        <option value="">Semua Kota</option>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2.5 font-bold text-white hover:bg-black transition text-sm">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Selector Form --}}
    <form method="GET" action="{{ route('compare.index') }}" class="mb-8">
        @if(request('filter_type')) <input type="hidden" name="filter_type" value="{{ request('filter_type') }}"> @endif
        @if(request('filter_province')) <input type="hidden" name="filter_province" value="{{ request('filter_province') }}"> @endif
        @if(request('filter_city')) <input type="hidden" name="filter_city" value="{{ request('filter_city') }}"> @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            {{-- Selector 1 --}}
            <div class="rounded-xl border-2 {{ $prop1 ? 'border-reoda border-solid' : 'border-dashed border-gray-300' }} bg-white p-4">
                <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Hunian Pertama</label>
                <select name="prop1" class="tom-select w-full rounded-lg border border-stroke text-sm outline-none transition" placeholder="Cari nama atau kota...">
                    <option value="">-- Pilih Properti --</option>
                    @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ ($prop1 && $prop1->id == $p->id) ? 'selected' : '' }}>
                        {{ $p->name }} — {{ $p->city }} ({{ ucfirst($p->type) }})
                    </option>
                    @endforeach
                </select>
            </div>
            {{-- Selector 2 --}}
            <div class="rounded-xl border-2 {{ $prop2 ? 'border-reoda border-solid' : 'border-dashed border-gray-300' }} bg-white p-4">
                <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Hunian Kedua</label>
                <select name="prop2" class="tom-select w-full rounded-lg border border-stroke text-sm outline-none transition" placeholder="Cari nama atau kota...">
                    <option value="">-- Pilih Properti --</option>
                    @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ ($prop2 && $prop2->id == $p->id) ? 'selected' : '' }}>
                        {{ $p->name }} — {{ $p->city }} ({{ ucfirst($p->type) }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="rounded-xl bg-reoda px-10 py-3 font-bold text-white hover:bg-reoda-dark transition text-sm">
                ⚡ Bandingkan Sekarang
            </button>
        </div>
    </form>

    @if($prop1 && $prop2)
    {{-- ===== COMPARISON TABLE ===== --}}
    @php
        $p1MinPrice = $prop1->units->where('status','available')->min('rent_price') ?? $prop1->units->min('rent_price');
        $p2MinPrice = $prop2->units->where('status','available')->min('rent_price') ?? $prop2->units->min('rent_price');
        $p1Avail    = $prop1->units->where('status','available')->count();
        $p2Avail    = $prop2->units->where('status','available')->count();
        $p1Fac      = $prop1->facilities->pluck('name')->toArray();
        $p2Fac      = $prop2->facilities->pluck('name')->toArray();
        $allFac     = collect(array_unique(array_merge($p1Fac, $p2Fac)))->sort()->values();
    @endphp

    {{-- Property Cards Header --}}
    <div class="grid grid-cols-3 gap-0 rounded-t-2xl overflow-hidden border border-stroke">
        <div class="bg-gray-50 p-4 flex items-center justify-center border-r border-stroke">
            <span class="text-xs font-bold uppercase text-gray-400">Aspek</span>
        </div>
        @foreach([$prop1, $prop2] as $p)
        <div class="bg-white p-5 text-center {{ !$loop->last ? 'border-r border-stroke' : '' }}">
            <div class="h-32 rounded-xl overflow-hidden mb-3 bg-gray-100">
                <img src="{{ $p->cover_image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
            </div>
            <h3 class="font-extrabold text-gray-900 text-base">{{ $p->name }}</h3>
            <p class="text-xs text-gray-500">{{ $p->city }}, {{ $p->province }}</p>
            <span class="inline-flex mt-1 rounded-full bg-reoda/10 px-3 py-0.5 text-xs font-semibold text-reoda capitalize">{{ $p->type }}</span>
        </div>
        @endforeach
    </div>

    {{-- Comparison Rows --}}
    @php
        function compareRow($label, $v1, $v2, $higherBetter = true) {
            $same = $v1 === $v2;
            return ['label'=>$label,'v1'=>$v1,'v2'=>$v2,'same'=>$same,'higherBetter'=>$higherBetter];
        }
        $rows = [
            compareRow('Kota / Kecamatan', ($prop1->district ?? '-').', '.$prop1->city, ($prop2->district ?? '-').', '.$prop2->city, false),
            compareRow('Tipe Properti', ucfirst($prop1->type), ucfirst($prop2->type), false),
            compareRow('Harga Mulai (Rp)', 'Rp '.number_format($p1MinPrice,0,',','.'), 'Rp '.number_format($p2MinPrice,0,',','.'), false),
            compareRow('Unit Tersedia', $p1Avail.' unit', $p2Avail.' unit', true),
            compareRow('Total Unit', $prop1->units->count().' unit', $prop2->units->count().' unit', true),
            compareRow('Pengelola', $prop1->manager->name ?? '-', $prop2->manager->name ?? '-', false),
            compareRow('Alamat', $prop1->address, $prop2->address, false),
        ];
    @endphp

    <div class="border-x border-stroke">
        {{-- Section: Info Umum --}}
        <div class="grid grid-cols-3 bg-[#003648] text-white">
            <div class="px-4 py-2.5 font-bold text-xs uppercase col-span-3">📌 Informasi Umum</div>
        </div>
        @foreach($rows as $row)
        <div class="grid grid-cols-3 border-b border-stroke hover:bg-blue-50/30 transition">
            <div class="px-4 py-3 text-xs font-semibold text-gray-500 border-r border-stroke bg-gray-50 flex items-center">{{ $row['label'] }}</div>
            @php
                $isNum1 = is_numeric(str_replace(['Rp ','.',',',' unit'],'',$row['v1']));
                $num1   = (float) str_replace(['Rp ','.',',',' unit'],'',$row['v1']);
                $num2   = (float) str_replace(['Rp ','.',',',' unit'],'',$row['v2']);
                $v1win  = $row['same'] ? false : ($isNum1 ? ($row['higherBetter'] ? $num1 > $num2 : $num1 < $num2) : false);
                $v2win  = $row['same'] ? false : ($isNum1 ? ($row['higherBetter'] ? $num2 > $num1 : $num2 < $num1) : false);
            @endphp
            <div class="px-4 py-3 text-sm border-r border-stroke {{ $v1win ? 'font-bold text-success-700 bg-success-50' : 'text-gray-800' }}">
                {{ $row['v1'] }}
                @if($v1win)<span class="ml-1 text-success-500">✓</span>@endif
            </div>
            <div class="px-4 py-3 text-sm {{ $v2win ? 'font-bold text-success-700 bg-success-50' : 'text-gray-800' }}">
                {{ $row['v2'] }}
                @if($v2win)<span class="ml-1 text-success-500">✓</span>@endif
            </div>
        </div>
        @endforeach

        {{-- Section: Fasilitas --}}
        <div class="grid grid-cols-3 bg-[#003648] text-white">
            <div class="px-4 py-2.5 font-bold text-xs uppercase col-span-3">🏠 Fasilitas Properti</div>
        </div>
        @if($allFac->isEmpty())
        <div class="grid grid-cols-3 border-b border-stroke">
            <div class="px-4 py-3 col-span-3 text-sm text-center text-gray-400">Belum ada data fasilitas</div>
        </div>
        @else
        @foreach($allFac as $fac)
        @php
            $has1 = in_array($fac, $p1Fac);
            $has2 = in_array($fac, $p2Fac);
        @endphp
        <div class="grid grid-cols-3 border-b border-stroke hover:bg-blue-50/30 transition">
            <div class="px-4 py-2.5 text-xs font-semibold text-gray-500 border-r border-stroke bg-gray-50 flex items-center">{{ $fac }}</div>
            <div class="px-4 py-2.5 text-center border-r border-stroke {{ $has1 ? 'text-success-600 bg-success-50 font-bold' : 'text-gray-800' }}">{{ $has1 ? '✓' : '✗' }}</div>
            <div class="px-4 py-2.5 text-center {{ $has2 ? 'text-success-600 bg-success-50 font-bold' : 'text-gray-800' }}">{{ $has2 ? '✓' : '✗' }}</div>
        </div>
        @endforeach
        @endif

        {{-- Section: Unit Tersedia --}}
        <div class="grid grid-cols-3 bg-[#003648] text-white">
            <div class="px-4 py-2.5 font-bold text-xs uppercase col-span-3">🔑 Unit Tersedia</div>
        </div>
        <div class="grid grid-cols-3 border-b border-stroke">
            <div class="px-4 py-3 text-xs font-semibold text-gray-500 border-r border-stroke bg-gray-50"></div>
            @foreach([$prop1, $prop2] as $p)
            <div class="px-4 py-3 border-r border-stroke last:border-r-0">
                @php $avUnits = $p->units->where('status','available'); @endphp
                @if($avUnits->isEmpty())
                    <p class="text-sm text-gray-400 text-center">Tidak ada unit tersedia</p>
                @else
                    <div class="space-y-2">
                    @foreach($avUnits->take(3) as $u)
                    <div class="rounded-lg bg-gray-50 border border-stroke px-3 py-2">
                        <p class="text-xs font-bold text-black">{{ $u->unit_code }}</p>
                        @if($u->area_sqm)<p class="text-xs text-gray-400">{{ $u->area_sqm }} m²</p>@endif
                        <p class="text-xs font-bold text-reoda">Rp {{ number_format($u->rent_price,0,',','.') }}/bln</p>
                    </div>
                    @endforeach
                    @if($avUnits->count() > 3)
                    <p class="text-xs text-gray-400 text-center">+{{ $avUnits->count()-3 }} unit lainnya</p>
                    @endif
                    </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Section: Kontak --}}
        <div class="grid grid-cols-3 bg-[#003648] text-white">
            <div class="px-4 py-2.5 font-bold text-xs uppercase col-span-3">📞 Kontak Pengelola</div>
        </div>
        <div class="grid grid-cols-3 border-b border-stroke">
            <div class="px-4 py-3 text-xs font-semibold text-gray-500 border-r border-stroke bg-gray-50 flex items-center">Telepon / WhatsApp</div>
            @foreach([$prop1, $prop2] as $p)
            <div class="px-4 py-3 text-sm border-r border-stroke last:border-r-0">
                @if($p->manager->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$p->manager->phone) }}" target="_blank" class="text-success-600 hover:underline font-medium">{{ $p->manager->phone }}</a>
                @else
                <span class="text-gray-400">-</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Footer CTAs --}}
    <div class="grid grid-cols-3 gap-0 rounded-b-2xl overflow-hidden border border-t-0 border-stroke">
        <div class="bg-gray-50 p-4 border-r border-stroke"></div>
        @foreach([$prop1, $prop2] as $p)
        <div class="bg-white p-4 text-center border-r border-stroke last:border-r-0">
            @auth
            <a href="{{ route('tenant.explore.show', $p) }}" class="inline-flex w-full justify-center rounded-xl bg-reoda py-2.5 text-sm font-bold text-white hover:bg-reoda-dark transition">Lihat Detail →</a>
            @else
            <a href="{{ route('login') }}" class="inline-flex w-full justify-center rounded-xl bg-reoda py-2.5 text-sm font-bold text-white hover:bg-reoda-dark transition">Login untuk Menyewa</a>
            @endauth
        </div>
        @endforeach
    </div>

    @elseif($prop1 || $prop2)
    {{-- Only 1 selected --}}
    <div class="rounded-xl border border-dashed border-reoda/40 bg-reoda/5 p-10 text-center">
        <p class="text-gray-500 text-sm">Pilih satu hunian lagi untuk memulai perbandingan.</p>
    </div>
    @else
    {{-- Empty state --}}
    <div class="rounded-xl border border-dashed border-gray-200 bg-white p-16 text-center">
        <svg class="w-20 h-20 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <h3 class="font-bold text-gray-700 text-lg mb-2">Belum ada hunian dipilih</h3>
        <p class="text-sm text-gray-400 mb-5">Pilih dua hunian di atas untuk melihat perbandingan lengkap harga, fasilitas, dan lokasi.</p>
        <a href="{{ url('/') }}" class="text-sm text-reoda font-medium hover:underline">← Kembali ke beranda</a>
    </div>
    @endif
</div>

<footer class="mt-16 border-t border-gray-100 bg-white py-6 text-center text-xs text-gray-400">
    © {{ date('Y') }} REODA — Platform Hunian Terpercaya
</footer>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.querySelectorAll('.tom-select').forEach((el) => {
        new TomSelect(el, {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    });
</script>

<script>
(function() {
    const citiesByProv = @json($citiesByProvince);
    const allCities   = @json($allCities);
    const activeCity  = '{{ request('filter_city', '') }}';

    const selectProvince = document.getElementById('select-province');
    const selectCity     = document.getElementById('select-city');

    function renderCities(prov, selectedCity) {
        const cities = (prov && citiesByProv[prov]) ? citiesByProv[prov] : allCities;

        // Reset city dropdown to only the placeholder
        selectCity.innerHTML = '<option value="">Semua Kota</option>';

        cities.forEach(function(c) {
            const opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c;
            if (c === selectedCity) opt.selected = true;
            selectCity.appendChild(opt);
        });
    }

    // Render on page load with current server-side values
    renderCities(selectProvince.value, activeCity);

    // Re-render whenever province changes
    selectProvince.addEventListener('change', function() {
        renderCities(this.value, '');
    });
})();
</script>
</body>
</html>
