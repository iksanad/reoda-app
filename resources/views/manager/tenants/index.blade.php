@extends('layouts.app')

@section('title', 'Data Penyewa - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Data Penyewa</h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li class="font-medium text-reoda">Penyewa</li>
        </ol>
    </nav>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lighter shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Total Penyewa</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['total'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Penyewa Aktif</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Kontrak Aktif</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['active'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Kontrak</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Akan Berakhir</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['expiring'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Kontrak</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Sudah Berakhir</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['expired'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Kontrak</p>
        </div>
    </div>
</div>

<!-- Filter / Search Bar -->
<div class="rounded-sm border border-stroke bg-white shadow-default mb-6">
    <form method="GET" action="{{ route('manager.tenants.index') }}" class="p-5 flex flex-col md:flex-row gap-4 items-end">
        <div class="w-full md:flex-1">
            <label class="mb-2 block text-sm font-medium text-gray-700">Cari Penyewa</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, email, atau telepon..." class="w-full rounded border-[1.5px] border-stroke bg-transparent py-2.5 pl-10 pr-5 text-sm font-medium outline-none transition focus:border-reoda" />
            </div>
        </div>
        <div class="w-full md:w-48">
            <label class="mb-2 block text-sm font-medium text-gray-700">Status Kontrak</label>
            <select name="status" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-reoda">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Berakhir</option>
                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-reoda px-5 py-2.5 text-sm font-medium text-white hover:bg-reoda-dark transition">Cari</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('manager.tenants.index') }}" class="rounded border border-stroke bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Reset</a>
                <a href="{{ route('manager.tenants.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center gap-3">
        <h3 class="text-xl font-extrabold text-reoda-dark">Daftar Penyewa</h3>
        <span class="text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ $tenants->total() }} penyewa</span>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('manager.tenants.export') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export XLSX
        </a>
    </div>
</div>

<div class="rounded-2xl bg-white shadow-sm p-4 sm:p-6 overflow-hidden">
    @if($tenants->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] border-separate" style="border-spacing: 0 8px;">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-l-full">Penyewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Unit & Lokasi</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Mulai Sewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Berakhir Sewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Status</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-r-full">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $tenant)
                @php
                    $latestContract = $tenant->leaseContracts->first();
                @endphp
                <tr class="even:bg-[#e6f4f1] odd:bg-white">
                    <td class="px-6 py-4 rounded-l-full">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full overflow-hidden shrink-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($tenant->name) }}&background=4C74AF&color=fff&size=40" alt="{{ $tenant->name }}">
                            </div>
                            <div>
                                <p class="font-bold text-reoda-dark text-sm">{{ $tenant->name }}</p>
                                <p class="text-xs text-gray-500">{{ $tenant->phone ?? $tenant->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($latestContract)
                            <p class="font-bold text-reoda-dark text-sm">{{ $latestContract->unit->property->name }}</p>
                            <p class="text-xs text-gray-500">Unit {{ $latestContract->unit->unit_code }}</p>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($latestContract)
                            <p class="font-bold text-reoda-dark text-sm">{{ \Carbon\Carbon::parse($latestContract->start_date)->format('d M Y') }}</p>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($latestContract)
                            <p class="font-bold text-reoda-dark text-sm">{{ \Carbon\Carbon::parse($latestContract->end_date)->format('d M Y') }}</p>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($latestContract)
                            @php
                                $statusColor = match($latestContract->status) {
                                    'active' => 'bg-[#b0ebb0] text-[#1a5e1a]',
                                    'expired' => 'bg-[#f9c5c5] text-[#7a1c1c]',
                                    'terminated' => 'bg-[#f9c5c5] text-[#7a1c1c]',
                                    default => 'bg-[#fce599] text-[#7a5c00]'
                                };
                                $statusText = match($latestContract->status) {
                                    'active' => 'Aktif',
                                    'expired' => 'Berakhir',
                                    'terminated' => 'Dibatalkan',
                                    default => 'Pending'
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusColor }}">
                                {{ $statusText }}
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 rounded-r-full text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('manager.tenants.show', $tenant) }}" title="Lihat Detail"
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
        {{ $tenants->links() }}
    </div>
    @else
    <div class="py-12 text-center border-t border-gray-100">
        <svg class="mx-auto h-14 w-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        <p class="text-gray-500 mb-1 font-medium">Tidak ada data penyewa</p>
        <p class="text-sm text-gray-400">Penyewa akan muncul di sini setelah mereka mendaftar dan menyewa unit Anda.</p>
    </div>
    @endif
</div>
@endsection
