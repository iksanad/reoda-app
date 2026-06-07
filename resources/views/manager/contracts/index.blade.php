@extends('layouts.app')
@section('title', 'Kontrak Sewa - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Kontrak Sewa</h2>
    <div class="flex items-center gap-3">
        <nav><ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li class="font-medium text-reoda">Kontrak</li>
        </ol></nav>
    </div>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

@php
    $expiring = \App\Models\LeaseContract::where('manager_id', auth()->id())
        ->where('status', 'active')
        ->where('end_date', '<=', now()->addDays(30))
        ->count();
@endphp

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 shrink-0">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total</p><h4 class="text-2xl font-extrabold text-black">{{ $counts['all'] }}</h4></div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4 {{ $counts['awaiting_approval'] > 0 ? 'border-2 border-yellow-300' : '' }}">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 shrink-0">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><p class="text-xs font-bold text-yellow-600 uppercase tracking-wide">Menunggu</p><h4 class="text-2xl font-extrabold text-yellow-700">{{ $counts['awaiting_approval'] }}</h4></div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 shrink-0">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><p class="text-xs font-bold text-green-600 uppercase tracking-wide">Aktif</p><h4 class="text-2xl font-extrabold text-green-700">{{ $counts['active'] }}</h4></div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 shrink-0">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Berakhir</p><h4 class="text-2xl font-extrabold text-gray-700">{{ $counts['expired'] }}</h4></div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 shrink-0">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><p class="text-xs font-bold text-red-500 uppercase tracking-wide">Diakhiri</p><h4 class="text-2xl font-extrabold text-red-600">{{ $counts['terminated'] }}</h4></div>
    </div>
</div>

{{-- Status Tabs --}}
<div class="mb-5 flex flex-wrap gap-2">
    @php
        $tabs = [
            'all'               => 'Semua',
            'awaiting_approval' => '⏳ Menunggu Persetujuan',
            'active'            => 'Aktif',
            'expired'           => 'Berakhir',
            'terminated'        => 'Diakhiri',
        ];
        $cur  = request('status', 'all');
        $tabColors = [
            'all'               => 'bg-gray-700',
            'awaiting_approval' => 'bg-yellow-500',
            'active'            => 'bg-green-600',
            'expired'           => 'bg-gray-500',
            'terminated'        => 'bg-red-500',
        ];
    @endphp
    @foreach($tabs as $key => $label)
    <a href="{{ route('manager.contracts.index', array_merge(request()->except('page'), ['status' => $key==='all' ? null : $key])) }}"
       class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold transition
           {{ ($cur===$key || ($cur==='all' && $key==='all')) ? $tabColors[$key].' text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
        {{ $label }}<span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $counts[$key] }}</span>
    </a>
    @endforeach
</div>

{{-- Search --}}
<form method="GET" class="mb-5 flex gap-3">
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <div class="relative flex-1 max-w-sm">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama penyewa..." class="w-full rounded-lg border border-stroke bg-white py-2.5 pl-9 pr-4 text-sm outline-none focus:border-reoda transition">
    </div>
    <button type="submit" class="rounded-lg bg-reoda px-4 py-2.5 text-sm font-medium text-white hover:bg-reoda-dark transition">Cari</button>
</form>

<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h3 class="text-xl font-extrabold text-reoda-dark">Daftar Kontrak Sewa</h3>
    <div class="flex items-center gap-2">
        <a href="{{ route('manager.contracts.export') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export XLSX
        </a>
        <a href="{{ route('manager.contracts.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-reoda px-4 py-2 text-sm font-semibold text-white hover:bg-reoda-dark transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Kontrak
        </a>
    </div>
</div>

<div class="rounded-2xl bg-white shadow-sm p-4 sm:p-6 overflow-hidden">
    @if($contracts->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full min-w-[800px] border-separate" style="border-spacing: 0 8px;">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-l-full">No. Kontrak</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Penyewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Unit & Lokasi</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Periode</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Sewa/Bln</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Status</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-r-full">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts as $contract)
                <tr class="even:bg-[#e6f4f1] odd:bg-white">
                    <td class="px-6 py-4 rounded-l-full text-center">
                        <p class="font-mono text-sm font-bold text-reoda-dark">{{ $contract->contract_number }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($contract->tenant->name) }}&background=4C74AF&color=fff&size=28" class="h-8 w-8 rounded-full shrink-0">
                            <div>
                                <p class="text-sm font-bold text-reoda-dark">{{ $contract->tenant->name }}</p>
                                <p class="text-xs text-gray-500">{{ $contract->tenant->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="text-sm font-bold text-reoda-dark">Unit {{ $contract->unit->unit_code }}</p>
                        <p class="text-xs text-gray-500">{{ $contract->unit->property->name }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="text-sm font-bold text-reoda-dark">{{ $contract->start_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">s/d {{ $contract->end_date->format('d M Y') }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="text-sm font-bold text-reoda-dark">Rp {{ number_format($contract->rent_amount,0,',','.') }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusColor = match($contract->status) {
                                'active' => 'bg-[#b0ebb0] text-[#1a5e1a]',
                                'expired' => 'bg-[#fce599] text-[#7a5c00]',
                                'terminated' => 'bg-[#f9c5c5] text-[#7a1c1c]',
                                default => 'bg-gray-200 text-gray-700'
                            };
                            $statusText = match($contract->status) {
                                'active' => 'Aktif',
                                'expired' => 'Berakhir',
                                'terminated' => 'Diakhiri',
                                default => ucfirst($contract->status)
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusColor }}">{{ $statusText }}</span>
                    </td>
                    <td class="px-6 py-4 rounded-r-full text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('manager.contracts.show', $contract) }}" title="Lihat Detail"
                               class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-reoda-lightest text-reoda-dark hover:bg-reoda-lighter transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border-t border-gray-100 p-4">
        {{ $contracts->links() }}
    </div>
    @else
    <div class="py-12 text-center border-t border-gray-100">
        <svg class="mx-auto h-14 w-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <p class="text-gray-500 mb-1 font-medium">Tidak ada data kontrak</p>
        <p class="text-sm text-gray-400">Belum ada kontrak sewa. <a href="{{ route('manager.contracts.create') }}" class="text-reoda font-medium hover:underline">Buat sekarang</a></p>
    </div>
    @endif
</div>
@endsection
