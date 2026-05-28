@extends('layouts.app')

@section('title', 'Detail Unit ' . $unit->unit_code . ' - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Detail Unit</h2>
    <nav>
        <ol class="flex items-center gap-2 text-sm">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.properties.index') }}">Properti /</a></li>
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.properties.show', $unit->property) }}">{{ $unit->property->name }} /</a></li>
            <li class="font-medium text-reoda">{{ $unit->unit_code }}</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

@php
    $statusConfig = match($unit->status) {
        'available'   => ['label' => 'Tersedia', 'class' => 'bg-success-50 text-success-700 border-success-200'],
        'rented'      => ['label' => 'Disewa', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'maintenance' => ['label' => 'Perbaikan', 'class' => 'bg-warning-50 text-warning-700 border-warning-200'],
        default       => ['label' => ucfirst($unit->status), 'class' => 'bg-gray-50 text-gray-700 border-gray-200'],
    };
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Info Unit --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Status Banner --}}
        <div class="flex items-center justify-between rounded-xl border px-5 py-3.5 {{ $statusConfig['class'] }}">
            <p class="font-bold text-base">Status Unit: {{ $statusConfig['label'] }}</p>
            @if($unit->status === 'rented' && $unit->activeContract)
                @php $daysLeft = now()->diffInDays($unit->activeContract->end_date, false); @endphp
                <p class="text-sm">Kontrak berakhir {{ $unit->activeContract->end_date->format('d M Y') }} 
                    <span class="font-semibold">({{ $daysLeft > 0 ? $daysLeft.' hari lagi' : 'Sudah jatuh tempo' }})</span>
                </p>
            @endif
        </div>

        {{-- Info Unit --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4 flex items-center justify-between">
                <h4 class="font-bold text-black">Informasi Unit</h4>
                <div class="flex gap-2">
                    <a href="{{ route('manager.units.edit', $unit) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-stroke px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Unit
                    </a>
                </div>
            </div>
            <div class="p-6 grid grid-cols-2 gap-5 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Kode Unit</p>
                    <p class="text-xl font-extrabold text-reoda-dark">{{ $unit->unit_code }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Nama / Kategori</p>
                    <p class="font-semibold text-black">{{ $unit->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Tipe / Ukuran</p>
                    <p class="font-semibold text-black">{{ $unit->type }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Harga Sewa / Bulan</p>
                    <p class="text-lg font-extrabold text-reoda">Rp {{ number_format($unit->rent_price, 0, ',', '.') }}</p>
                </div>
                @if($unit->area_sqm)
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Luas</p>
                    <p class="font-semibold text-black">{{ $unit->area_sqm }} m²</p>
                </div>
                @endif
                @if($unit->floor)
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Lantai</p>
                    <p class="font-semibold text-black">{{ $unit->floor }}</p>
                </div>
                @endif
                @if($unit->description)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-0.5">Deskripsi / Fasilitas</p>
                    <p class="text-gray-700 leading-relaxed">{{ $unit->description }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Kontrak --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4">
                <h4 class="font-bold text-black">Riwayat Kontrak ({{ $unit->leaseContracts->count() }})</h4>
            </div>
            @if($unit->leaseContracts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-stroke">
                            <th class="py-3 px-6 text-left font-semibold text-gray-600">No. Kontrak</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Penyewa</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Periode</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Harga</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Status</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke">
                        @foreach($unit->leaseContracts as $contract)
                        @php
                            $cs = match($contract->status) {
                                'active'     => ['label' => 'Aktif',    'class' => 'bg-success-50 text-success-700'],
                                'expired'    => ['label' => 'Berakhir', 'class' => 'bg-gray-100 text-gray-600'],
                                'terminated' => ['label' => 'Diakhiri', 'class' => 'bg-error-50 text-error-700'],
                                default      => ['label' => ucfirst($contract->status), 'class' => 'bg-gray-100 text-gray-600'],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-mono text-xs text-gray-600">{{ $contract->contract_number }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($contract->tenant->name) }}&background=4C74AF&color=fff&size=28" class="h-7 w-7 rounded-full shrink-0">
                                    <div>
                                        <p class="font-semibold text-black text-xs">{{ $contract->tenant->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $contract->tenant->phone ?? $contract->tenant->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <p class="text-xs text-gray-600">{{ $contract->start_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400">s/d {{ $contract->end_date->format('d M Y') }}</p>
                            </td>
                            <td class="py-3 px-4 font-semibold text-reoda">Rp {{ number_format($contract->rent_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4"><span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $cs['class'] }}">{{ $cs['label'] }}</span></td>
                            <td class="py-3 px-4"><a href="{{ route('manager.contracts.show', $contract) }}" class="text-xs font-medium text-reoda hover:text-reoda-dark transition">Detail →</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-10 text-center text-sm text-gray-400">Belum ada riwayat kontrak untuk unit ini.</div>
            @endif
        </div>

    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-5">

        {{-- Info Properti --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Properti</h4>
            <div class="flex items-start gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-reoda/10 shrink-0">
                    <svg class="w-5 h-5 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="font-bold text-black">{{ $unit->property->name }}</p>
                    <p class="text-xs text-gray-500">{{ $unit->property->address }}, {{ $unit->property->city }}</p>
                </div>
            </div>
            <a href="{{ route('manager.properties.show', $unit->property) }}" class="text-xs text-reoda font-medium hover:underline">Lihat semua unit →</a>
        </div>

        {{-- Penyewa Aktif --}}
        @if($unit->activeContract)
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Penyewa Aktif</h4>
            <div class="flex items-center gap-3 mb-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($unit->activeContract->tenant->name) }}&background=4C74AF&color=fff&size=48" class="h-12 w-12 rounded-full">
                <div>
                    <p class="font-bold text-black">{{ $unit->activeContract->tenant->name }}</p>
                    <p class="text-xs text-gray-400">{{ $unit->activeContract->tenant->email }}</p>
                </div>
            </div>
            @if($unit->activeContract->tenant->phone)
            <p class="text-sm text-gray-600 mb-3">📞 {{ $unit->activeContract->tenant->phone }}</p>
            @endif
            <div class="space-y-1.5 text-sm mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-500">No. Kontrak</span>
                    <span class="font-mono text-xs font-semibold">{{ $unit->activeContract->contract_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Mulai</span>
                    <span class="font-semibold">{{ $unit->activeContract->start_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Berakhir</span>
                    <span class="font-semibold">{{ $unit->activeContract->end_date->format('d M Y') }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('manager.tenants.show', $unit->activeContract->tenant) }}"
                   class="flex-1 text-center text-xs font-medium text-reoda border border-reoda rounded-lg py-2 hover:bg-reoda-lightest transition">
                    Profil Penyewa
                </a>
                <a href="{{ route('manager.contracts.show', $unit->activeContract) }}"
                   class="flex-1 text-center text-xs font-medium text-white bg-reoda rounded-lg py-2 hover:bg-reoda-dark transition">
                    Lihat Kontrak
                </a>
            </div>
        </div>
        @else
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6 text-center">
            <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <p class="text-sm text-gray-400 mb-3">Unit ini sedang kosong</p>
            <a href="{{ route('manager.contracts.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-reoda px-4 py-2 text-xs font-semibold text-white hover:bg-reoda-dark transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Kontrak
            </a>
        </div>
        @endif

        {{-- Quick Stats --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Statistik Unit</h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Total Kontrak</span>
                    <span class="font-bold text-reoda-dark">{{ $unit->leaseContracts->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Kontrak Aktif</span>
                    <span class="font-bold text-success-600">{{ $unit->leaseContracts->where('status','active')->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Harga Sewa</span>
                    <span class="font-bold text-reoda">Rp {{ number_format($unit->rent_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
