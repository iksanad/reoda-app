@extends('layouts.app')

@section('title', 'Aset Saya - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">
        Informasi Aset Sewa
    </h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium text-reoda" href="#">Dashboard</a></li>
        </ol>
    </nav>
</div>

@if(!$activeContract)

{{-- Awaiting Approval Notice --}}
@if($awaitingContract)
<div class="rounded-xl border border-yellow-200 bg-yellow-50 p-5 mb-5 flex items-start gap-4">
    <svg class="w-8 h-8 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <h4 class="font-bold text-yellow-800">Menunggu Persetujuan Pengelola</h4>
        <p class="text-sm text-yellow-700 mt-1">
            Pengajuan kontrak Anda untuk unit <strong>{{ $awaitingContract->unit->name }}</strong> di
            <strong>{{ $awaitingContract->unit->property->name }}</strong> sedang ditinjau.
            Anda akan mendapat notifikasi setelah disetujui.
        </p>
    </div>
</div>
@endif

<div class="rounded-2xl border border-stroke bg-white shadow-sm overflow-hidden">
    {{-- Hero Empty State --}}
    <div class="bg-linear-to-br from-reoda to-reoda-dark px-8 py-10 text-white text-center">
        <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 mb-5">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <h3 class="text-2xl font-extrabold mb-2">Selamat Datang di REODA!</h3>
        <p class="text-white/80 text-sm max-w-md mx-auto">Anda belum memiliki kontrak sewa aktif. Mulai dengan scan kode QR hunian atau cari di Explore Market.</p>
    </div>

    {{-- CTA Buttons --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-stroke">
        {{-- Scan QR Code --}}
        <div class="p-8 text-center hover:bg-gray-50 transition cursor-pointer group" onclick="openQrScanner()">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-reoda/10 group-hover:bg-reoda/20 transition mb-4">
                <svg class="w-8 h-8 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 6h4V4m0 4h4m-4 0v4m12-12h-4v4h4V4z"/>
                </svg>
            </div>
            <h4 class="font-bold text-black text-base mb-1">📷 Scan Kode QR Hunian</h4>
            <p class="text-sm text-gray-500">Scan barcode/QR Code yang ada di lokasi hunian Anda untuk langsung melihat detail dan mengajukan kontrak.</p>
        </div>
        {{-- Explore Market --}}
        <a href="{{ route('tenant.explore.index') }}" class="p-8 text-center hover:bg-gray-50 transition group block">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-green-100 group-hover:bg-green-200 transition mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h4 class="font-bold text-black text-base mb-1">🔍 Cari di Explore Market</h4>
            <p class="text-sm text-gray-500">Jelajahi seluruh properti yang tersedia, bandingkan harga, fasilitas, dan lokasi untuk temukan hunian terbaik.</p>
        </a>
    </div>
</div>

{{-- QR Scanner Modal --}}
<div id="qr-scanner-modal" class="fixed inset-0 z-50 bg-black/60 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm mx-4">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-black">Masukkan Kode Hunian</h4>
            <button onclick="closeQrScanner()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
        </div>
        <p class="text-sm text-gray-500 mb-4">Masukkan kode properti secara manual, atau gunakan tombol kamera untuk scan QR Code langsung dari layar Anda.</p>

        {{-- Camera Scanner Area --}}
        <div id="qr-reader" class="hidden mb-4 rounded-xl overflow-hidden border border-stroke"></div>
        <div id="qr-scan-result" class="hidden mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700 font-medium"></div>

        {{-- Manual Input --}}
        <input type="text" id="property-code-input" placeholder="Contoh: REODA-001"
            class="w-full rounded-lg border border-stroke py-3 px-4 text-sm font-mono uppercase outline-none focus:border-reoda transition mb-3">

        <div class="flex gap-2">
            {{-- Scan Camera Button --}}
            <button id="btn-scan-camera" onclick="toggleCameraScanner()"
                class="flex items-center gap-2 rounded-lg border border-reoda text-reoda px-4 py-3 text-sm font-semibold hover:bg-reoda hover:text-white transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                Kamera
            </button>
            {{-- Go Button --}}
            <button onclick="goToProperty()"
                class="flex-1 rounded-lg bg-reoda py-3 font-semibold text-white hover:bg-reoda-dark transition">
                Lihat Hunian
            </button>
        </div>
    </div>
</div>
@else

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- DASHBOARD PENYEWA — KONTRAK AKTIF (Redesigned based on mockup) --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}

@php
    $property = $activeContract->unit->property;
    $manager  = $property->manager;
    $unit     = $activeContract->unit;
@endphp

{{-- Section 1: Header Profil --}}
<div class="rounded-2xl bg-white border border-stroke shadow-sm p-6 sm:p-8 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex-1">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-reoda-dark leading-tight">{{ auth()->user()->name }}</h1>
            <p class="text-base sm:text-lg font-semibold text-gray-500 mt-1">{{ $unit->name }} — {{ $property->name }}</p>
            <a href="{{ route('tenant.profile.index') }}" class="inline-flex items-center gap-2 mt-4 rounded-full border-2 border-reoda text-reoda px-5 py-2 text-sm font-bold hover:bg-reoda hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Profil
            </a>
        </div>
        <div class="shrink-0">
            <div class="h-20 w-20 sm:h-24 sm:w-24 rounded-full bg-reoda/10 border-4 border-reoda/20 overflow-hidden flex items-center justify-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4C74AF&color=fff&size=96&bold=true" alt="Avatar" class="h-full w-full object-cover rounded-full">
            </div>
        </div>
    </div>
</div>

{{-- Section 2: 3 Kartu Statistik --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">

    {{-- Card: Sisa Waktu Sewa --}}
    <div class="rounded-2xl bg-white border border-stroke shadow-sm p-6 text-center">
        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Sisa Waktu Sewa</p>
        @if($remainingDays !== null)
            <h2 class="text-5xl font-black text-reoda-dark leading-none">{{ $remainingDays }}</h2>
            <p class="text-base font-semibold text-gray-400 mt-2">Hari Lagi</p>
            @if($remainingDays <= 30 && $remainingDays > 0)
                <p class="text-xs text-orange-500 font-medium mt-2">⚠️ Segera perpanjang sewa Anda</p>
            @elseif($remainingDays == 0)
                <p class="text-xs text-red-500 font-medium mt-2">🚨 Kontrak berakhir hari ini!</p>
            @endif
        @else
            <h2 class="text-4xl font-black text-reoda-dark leading-none">∞</h2>
            <p class="text-base font-semibold text-gray-400 mt-2">Tanpa Batas</p>
            <p class="text-xs text-green-500 font-medium mt-2">Kontrak bulanan otomatis diperpanjang</p>
        @endif
    </div>

    {{-- Card: Pembayaran Lunas --}}
    <div class="rounded-2xl bg-white border border-stroke shadow-sm p-6 text-center">
        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Pembayaran Lunas</p>
        @if($lastPaidInvoice)
            @php
                $paidMonthName = \Carbon\Carbon::create()->month($lastPaidInvoice->billing_month)->translatedFormat('F');
            @endphp
            <h2 class="text-2xl sm:text-3xl font-black text-reoda-dark leading-tight">{{ $paidMonthName }} {{ $lastPaidInvoice->billing_year }}</h2>
        @else
            <h2 class="text-2xl font-black text-gray-300 leading-tight">Belum Ada</h2>
        @endif
        <div class="mt-4">
            @if($pendingInvoice && $pendingInvoice->status === 'unpaid')
                <a href="{{ route('tenant.transactions.show', $pendingInvoice) }}" class="inline-flex items-center gap-2 rounded-full bg-reoda text-white px-5 py-2 text-sm font-bold hover:bg-reoda-dark transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Bayar Tagihan
                </a>
            @elseif($activeContract->end_date && $remainingDays !== null && $remainingDays <= 30)
                <a href="{{ route('tenant.services.index') }}" class="inline-flex items-center gap-2 rounded-full bg-reoda text-white px-5 py-2 text-sm font-bold hover:bg-reoda-dark transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Perpanjang Sewa
                </a>
            @else
                <span class="inline-flex items-center gap-2 rounded-full bg-green-100 text-green-700 px-5 py-2 text-sm font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Lunas
                </span>
            @endif
        </div>
    </div>

    {{-- Card: Tagihan Utilitas --}}
    <div class="rounded-2xl bg-white border border-stroke shadow-sm p-6">
        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4 text-center">Tagihan Utilitas</p>
        <div class="flex flex-col gap-3">
            {{-- Listrik --}}
            <div class="flex items-center gap-3 rounded-xl bg-amber-50 border border-amber-100 p-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-400 text-white text-lg">⚡</div>
                <div class="flex-1 min-w-0">
                    @if($electricityInvoice)
                        <p class="font-bold text-gray-800 text-sm">Rp {{ number_format($electricityInvoice->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::create()->month($electricityInvoice->billing_month)->translatedFormat('F') }} {{ $electricityInvoice->billing_year }}</p>
                    @else
                        <p class="font-bold text-gray-400 text-sm">Tidak Ada</p>
                        <p class="text-xs text-gray-400">Sudah termasuk sewa</p>
                    @endif
                </div>
            </div>
            {{-- Air --}}
            <div class="flex items-center gap-3 rounded-xl bg-blue-50 border border-blue-100 p-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-400 text-white text-lg">💧</div>
                <div class="flex-1 min-w-0">
                    @if($waterInvoice)
                        <p class="font-bold text-gray-800 text-sm">Rp {{ number_format($waterInvoice->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::create()->month($waterInvoice->billing_month)->translatedFormat('F') }} {{ $waterInvoice->billing_year }}</p>
                    @else
                        <p class="font-bold text-gray-400 text-sm">Tidak Ada</p>
                        <p class="text-xs text-gray-400">Sudah termasuk sewa</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Section 3: Informasi Hunian (Tabbed) --}}
<div class="rounded-2xl bg-white border border-stroke shadow-sm overflow-hidden" x-data="{ activeTab: 'lokasi' }">
    <div class="border-b border-stroke">
        <div class="flex">
            <button @click="activeTab = 'lokasi'"
                :class="activeTab === 'lokasi' ? 'border-b-2 border-reoda text-reoda bg-reoda/5' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-4 text-sm sm:text-base font-bold text-center transition">
                📍 Lokasi
            </button>
            <button @click="activeTab = 'sewa'"
                :class="activeTab === 'sewa' ? 'border-b-2 border-reoda text-reoda bg-reoda/5' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-4 text-sm sm:text-base font-bold text-center transition">
                📋 Tipe Sewa
            </button>
            <button @click="activeTab = 'pengelola'"
                :class="activeTab === 'pengelola' ? 'border-b-2 border-reoda text-reoda bg-reoda/5' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-4 px-4 text-sm sm:text-base font-bold text-center transition">
                👤 Pengelola
            </button>
        </div>
    </div>

    <div class="p-6">
        <h3 class="text-lg font-bold text-reoda-dark mb-4">Informasi Hunian</h3>

        {{-- Tab: Lokasi --}}
        <div x-show="activeTab === 'lokasi'" x-transition>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-semibold text-gray-500 w-1/3">Alamat</td>
                            <td class="py-3 font-medium text-gray-800">{{ $property->address ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 bg-reoda/[0.03]">
                            <td class="py-3 pr-4 font-semibold text-gray-500">RT/RW</td>
                            <td class="py-3 font-medium text-gray-800">{{ $property->rt_rw ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-semibold text-gray-500">Kelurahan</td>
                            <td class="py-3 font-medium text-gray-800">{{ $property->village ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 bg-reoda/[0.03]">
                            <td class="py-3 pr-4 font-semibold text-gray-500">Kecamatan</td>
                            <td class="py-3 font-medium text-gray-800">{{ $property->district ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-semibold text-gray-500">Kota / Kabupaten</td>
                            <td class="py-3 font-medium text-gray-800">{{ $property->city ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 pr-4 font-semibold text-gray-500">Provinsi</td>
                            <td class="py-3 font-medium text-gray-800">{{ $property->province ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if($property->maps_url)
                <a href="{{ $property->maps_url }}" target="_blank" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-reoda hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Buka di Google Maps
                </a>
            @endif
        </div>

        {{-- Tab: Tipe Sewa --}}
        <div x-show="activeTab === 'sewa'" x-transition x-cloak>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-semibold text-gray-500 w-1/3">Jenis Kontrak</td>
                            <td class="py-3 font-medium text-gray-800 capitalize">{{ $activeContract->rental_type ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 bg-reoda/[0.03]">
                            <td class="py-3 pr-4 font-semibold text-gray-500">Harga Sewa / Bulan</td>
                            <td class="py-3 font-bold text-reoda-dark">Rp {{ number_format($activeContract->rent_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-semibold text-gray-500">Deposit / Jaminan</td>
                            <td class="py-3 font-medium text-gray-800">Rp {{ number_format($activeContract->deposit_amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 bg-reoda/[0.03]">
                            <td class="py-3 pr-4 font-semibold text-gray-500">Mulai Kontrak</td>
                            <td class="py-3 font-medium text-gray-800">{{ $activeContract->start_date->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-semibold text-gray-500">Akhir Kontrak</td>
                            <td class="py-3 font-medium text-gray-800">{{ $activeContract->end_date ? $activeContract->end_date->translatedFormat('d F Y') : 'Tanpa Batas (Bulanan)' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 pr-4 font-semibold text-gray-500">Nomor Kontrak</td>
                            <td class="py-3 font-mono font-medium text-gray-600 text-xs">{{ $activeContract->contract_number }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <a href="{{ route('tenant.contract.show', $activeContract) }}" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-reoda hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Lihat Detail Kontrak
            </a>
        </div>

        {{-- Tab: Pengelola --}}
        <div x-show="activeTab === 'pengelola'" x-transition x-cloak>
            <div class="flex items-center gap-4 mb-5">
                <div class="h-14 w-14 rounded-full overflow-hidden shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($manager->name ?? 'P') }}&background=4C74AF&color=fff&size=56" alt="Pengelola" class="h-full w-full object-cover">
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-lg">{{ $manager->name ?? 'Pengelola' }}</h4>
                    <p class="text-sm text-gray-500">Pengelola {{ $property->name }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-semibold text-gray-500 w-1/3">Email</td>
                            <td class="py-3 font-medium text-gray-800">{{ $manager->email ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 bg-reoda/[0.03]">
                            <td class="py-3 pr-4 font-semibold text-gray-500">Telepon</td>
                            <td class="py-3 font-medium text-gray-800">{{ $manager->phone ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 mt-5">
                @if(isset($manager->phone) && $manager->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $manager->phone) }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-green-500 text-green-600 px-5 py-2.5 text-sm font-bold hover:bg-green-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat via WhatsApp
                </a>
                @endif
                <a href="{{ route('tenant.services.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 text-gray-700 px-5 py-2.5 text-sm font-bold hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Lapor Kendala / Layanan
                </a>
            </div>
        </div>
    </div>
</div>

@endif
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
var qrScanner = null;
var cameraActive = false;

function openQrScanner() {
    var modal = document.getElementById('qr-scanner-modal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
}
function closeQrScanner() {
    stopCamera();
    var modal = document.getElementById('qr-scanner-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    document.getElementById('property-code-input').value = '';
    document.getElementById('qr-scan-result').classList.add('hidden');
}
function stopCamera() {
    if (qrScanner && cameraActive) {
        qrScanner.stop().catch(function() {});
        cameraActive = false;
    }
    document.getElementById('qr-reader').classList.add('hidden');
    var btn = document.getElementById('btn-scan-camera');
    if (btn) btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg> Kamera';
}
function toggleCameraScanner() {
    if (cameraActive) { stopCamera(); return; }

    var readerEl = document.getElementById('qr-reader');
    readerEl.classList.remove('hidden');
    readerEl.innerHTML = '';

    var btn = document.getElementById('btn-scan-camera');
    if (btn) btn.innerHTML = '⏹ Stop Kamera';

    qrScanner = new Html5Qrcode('qr-reader');
    qrScanner.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        function(decodedText) {
            // Berhasil scan - ambil kode dari URL atau teks langsung
            var code = decodedText;
            var match = decodedText.match(/\/property\/([A-Z0-9\-]+)/i);
            if (match) code = match[1];

            document.getElementById('property-code-input').value = code.toUpperCase();
            var resultEl = document.getElementById('qr-scan-result');
            resultEl.textContent = '✓ Kode terdeteksi: ' + code.toUpperCase();
            resultEl.classList.remove('hidden');
            stopCamera();
        },
        function() {} // Ignore per-frame errors
    ).then(function() {
        cameraActive = true;
    }).catch(function(err) {
        stopCamera();
        readerEl.classList.add('hidden');
        alert('Tidak dapat mengakses kamera: ' + err + '\n\nPastikan Anda memberikan izin akses kamera di browser.');
    });
}
function goToProperty() {
    var code = document.getElementById('property-code-input').value.trim().toUpperCase();
    if (!code) { alert('Masukkan kode hunian terlebih dahulu.'); return; }
    window.location.href = '/property/' + code;
}
</script>
@endpush
