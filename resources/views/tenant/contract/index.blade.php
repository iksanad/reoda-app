@extends('layouts.app')
@section('title', 'Kontrak Sewa Saya - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Kontrak Sewa Saya</h2>
    <div class="flex items-center gap-3">
        <nav><ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.dashboard') }}">Dashboard /</a></li>
            <li class="font-medium text-reoda">Kontrak</li>
        </ol></nav>
    </div>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="mb-5 rounded-md border-l-4 border-error-500 bg-error-50 px-5 py-3 text-sm font-medium text-error-700">{{ session('error') }}</div>
@endif

<div class="mb-4">
    <h3 class="text-xl font-extrabold text-reoda-dark">Daftar Kontrak Sewa</h3>
</div>

<div class="rounded-2xl bg-white shadow-sm p-4 sm:p-6 overflow-hidden">
    @if($contracts->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] border-separate" style="border-spacing: 0 8px;">
            <thead>
                <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <th class="px-5 py-3 border-b border-stroke">No. Kontrak</th>
                    <th class="px-5 py-3 border-b border-stroke">Properti & Unit</th>
                    <th class="px-5 py-3 border-b border-stroke">Periode</th>
                    <th class="px-5 py-3 border-b border-stroke">Status</th>
                    <th class="px-5 py-3 border-b border-stroke text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts as $idx => $contract)
                <tr class="group {{ $idx % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-reoda-lightest transition duration-200">
                    <td class="px-5 py-4 border-y border-l border-stroke group-hover:border-reoda/30 rounded-l-2xl">
                        <span class="font-extrabold text-black block">{{ $contract->contract_number }}</span>
                        <span class="text-xs text-gray-500">Mulai: {{ $contract->start_date->format('d M Y') }}</span>
                    </td>
                    <td class="px-5 py-4 border-y border-stroke group-hover:border-reoda/30">
                        <span class="font-extrabold text-black block">{{ $contract->unit->property->name }}</span>
                        <span class="text-sm text-gray-500">Unit: {{ $contract->unit->unit_code }}</span>
                    </td>
                    <td class="px-5 py-4 border-y border-stroke group-hover:border-reoda/30">
                        <span class="font-bold text-gray-700">{{ $contract->start_date->format('d M Y') }}</span>
                        <span class="text-gray-400 mx-1">-</span>
                        <span class="font-bold text-gray-700">{{ $contract->end_date->format('d M Y') }}</span>
                    </td>
                    <td class="px-5 py-4 border-y border-stroke group-hover:border-reoda/30">
                        @if($contract->status == 'active')
                            <span class="inline-block rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-bold text-success-700">Aktif</span>
                        @elseif($contract->status == 'pending')
                            <span class="inline-block rounded-full bg-warning-100 px-2.5 py-0.5 text-xs font-bold text-warning-700">Menunggu</span>
                        @elseif($contract->status == 'expired')
                            <span class="inline-block rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-bold text-gray-700">Berakhir</span>
                        @else
                            <span class="inline-block rounded-full bg-error-100 px-2.5 py-0.5 text-xs font-bold text-error-700">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-y border-r border-stroke group-hover:border-reoda/30 rounded-r-2xl text-right">
                        <a href="{{ route('tenant.contract.show', $contract->id) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-reoda hover:text-white transition">
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-12 text-center text-gray-500">
        <svg class="mx-auto w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <p class="text-base font-medium">Belum ada kontrak sewa</p>
    </div>
    @endif
</div>
@endsection
