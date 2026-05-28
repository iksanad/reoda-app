@extends('layouts.app')

@section('title', 'Lokasi & Properti - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Lokasi & Properti</h2>
    <div class="flex items-center gap-3">
        <nav>
            <ol class="flex items-center gap-2 text-sm">
                <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
                <li class="font-medium text-reoda">Lokasi & Properti</li>
            </ol>
        </nav>
    </div>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lighter shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Total Lokasi</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['total_properties'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Lokasi Aktif</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Total Unit Hunian</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['total_units'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Unit Terdaftar</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Unit Disewa</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['rented_units'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Unit</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Perbaikan</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['maintenance_units'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Unit</p>
        </div>
    </div>
</div>

{{-- Search --}}
<div class="flex flex-col sm:flex-row gap-3 mb-5 items-start sm:items-center justify-between">
    <form method="GET" action="{{ route('manager.properties.index') }}" class="flex gap-2 flex-1 max-w-sm">
        <div class="relative flex-1">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari lokasi..."
                   class="w-full rounded-lg border border-stroke bg-white py-2.5 pl-9 pr-4 text-sm outline-none focus:border-reoda transition">
        </div>
        <button type="submit" class="rounded-lg bg-reoda px-4 py-2.5 text-sm font-medium text-white hover:bg-reoda-dark transition">Cari</button>
        @if(request('search'))
        <a href="{{ route('manager.properties.index') }}" class="rounded-lg border border-stroke bg-white px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Table Header & Actions --}}
<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h3 class="text-xl font-extrabold text-reoda-dark">Daftar Lokasi & Properti</h3>
    <div class="flex items-center gap-2">
        <a href="{{ route('manager.properties.export') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export XLSX
        </a>
        <a href="{{ route('manager.properties.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-reoda px-4 py-2 text-sm font-semibold text-white hover:bg-reoda-dark transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Lokasi
        </a>
    </div>
</div>
<div class="rounded-2xl bg-white shadow-sm p-4 sm:p-6 overflow-hidden">
    @if($properties->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] border-separate" style="border-spacing: 0 8px;">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-l-full">Lokasi</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Alamat</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Total Unit</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Disewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Status</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-r-full">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($properties as $property)
                <tbody x-data="{ expanded: false }">
                    <tr class="{{ $loop->even ? 'bg-[#e6f4f1]' : 'bg-white' }}">
                        <td class="px-6 py-4 rounded-l-full">
                            <p class="font-extrabold text-reoda-dark text-sm">{{ $property->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ $property->type }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-reoda-dark text-sm">{{ $property->address }}</p>
                            <p class="text-xs text-gray-500">{{ $property->city }}, {{ $property->province }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <p class="font-bold text-reoda-dark text-sm">{{ $property->units_count }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <p class="font-bold text-reoda-dark text-sm">{{ $property->rented_units_count }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold
                                {{ $property->status === 'active' ? 'bg-[#b0ebb0] text-[#1a5e1a]' : 'bg-[#f9c5c5] text-[#7a1c1c]' }}">
                                {{ $property->status === 'active' ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 rounded-r-full">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="expanded = !expanded" title="Lihat Kamar"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <a href="{{ route('manager.properties.show', $property) }}" title="Detail"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-reoda-lightest text-reoda-dark hover:bg-reoda-lighter transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('manager.properties.edit', $property) }}" title="Edit"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form method="POST" action="{{ route('manager.properties.destroy', $property) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus properti ini beserta semua kamar di dalamnya? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    
                    {{-- Expanded Row untuk Kamar --}}
                    <tr x-show="expanded" x-cloak class="bg-gray-50/50" x-transition>
                        <td colspan="6" class="px-6 py-4 rounded-xl border border-gray-100">
                            @if($property->units->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                                    @foreach($property->units as $unit)
                                        <div class="bg-white border border-gray-200 rounded-xl p-4 flex justify-between items-center shadow-sm">
                                            <div>
                                                <p class="font-extrabold text-reoda-dark text-sm">Unit {{ $unit->unit_code }}</p>
                                                <p class="text-xs text-gray-500 font-medium">Rp {{ number_format($unit->rent_amount,0,',','.') }} / bln</p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold
                                                    {{ $unit->status === 'occupied' ? 'bg-[#b0ebb0] text-[#1a5e1a]' : 
                                                       ($unit->status === 'available' ? 'bg-[#cce3fc] text-[#1c4d82]' : 'bg-gray-200 text-gray-700') }}">
                                                    {{ $unit->status === 'occupied' ? 'Disewa' : ($unit->status === 'available' ? 'Tersedia' : ucfirst($unit->status)) }}
                                                </span>
                                                <a href="{{ route('manager.units.edit', $unit) }}" class="text-gray-400 hover:text-reoda transition" title="Edit Unit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-500 mb-2">Belum ada unit terdaftar di properti ini.</p>
                                    <a href="{{ route('manager.units.create', ['property_id' => $property->id]) }}" class="text-xs font-semibold text-reoda hover:underline">Tambah Unit Sekarang</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                </tbody>
                @endforeach
            </table>
    </div>
    <div class="border-t border-gray-100 p-4">
        {{ $properties->links() }}
    </div>
    @else
    <div class="py-16 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        <h4 class="text-lg font-medium text-gray-900 mb-1">
            {{ request('search') ? 'Tidak ada properti yang cocok' : 'Belum ada properti' }}
        </h4>
        <p class="text-gray-500 mb-6 text-sm">
            {{ request('search') ? 'Coba kata kunci lain.' : 'Mulai dengan menambahkan lokasi properti pertama Anda.' }}
        </p>
        @if(!request('search'))
        <a href="{{ route('manager.properties.create') }}" class="inline-flex rounded-md bg-reoda py-2 px-6 font-medium text-white hover:bg-reoda-dark">Tambah Sekarang</a>
        @endif
    </div>
    @endif
</div>
@endsection
