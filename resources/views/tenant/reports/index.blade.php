@extends('layouts.app')
@section('title', 'Laporan Pembayaran - REODA')
@section('content')

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Laporan Pembayaran</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Laporan</li>
    </ol></nav>
</div>

{{-- Summary Cards + Chart --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    {{-- Cards --}}
    <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
        <h3 class="text-lg font-bold text-black mb-4">Ringkasan Tahun {{ $currentYear }}</h3>
        <div class="grid grid-cols-2 gap-4 mb-5">
            <div class="rounded-lg bg-blue-50 p-4">
                <p class="text-xs font-medium text-blue-600 mb-1">Total Listrik</p>
                <p class="text-xl font-extrabold text-blue-700">Rp {{ number_format($currentYearData['total_elec'],0,',','.') }}</p>
            </div>
            <div class="rounded-lg bg-cyan-50 p-4">
                <p class="text-xs font-medium text-cyan-600 mb-1">Total Air</p>
                <p class="text-xl font-extrabold text-cyan-700">Rp {{ number_format($currentYearData['total_water'],0,',','.') }}</p>
            </div>
        </div>
        <div class="rounded-lg bg-reoda/10 p-4 text-center">
            <p class="text-sm font-semibold text-reoda mb-1">Total Pembayaran</p>
            <p class="text-3xl font-extrabold text-reoda">Rp {{ number_format($currentYearData['grand_total'],0,',','.') }}</p>
        </div>
    </div>

    {{-- Bar Chart --}}
    <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
        <h3 class="text-lg font-bold text-black mb-4">Grafik Bulanan {{ $currentYear }}</h3>
        <div class="flex items-end gap-2 h-40">
            @php
                $maxVal = count($chartTotals) > 0 ? max(max($chartTotals), 1) : 1;
            @endphp
            @foreach($chartMonths as $mi => $monthLabel)
            @php
                $val = $chartTotals[$mi] ?? 0;
                $heightPct = $maxVal > 0 ? max(round(($val / $maxVal) * 100), 2) : 2;
            @endphp
            <div class="flex flex-col items-center gap-1 flex-1">
                <div class="w-full rounded-t-sm bg-blue-300 hover:bg-reoda transition-colors cursor-default"
                     style="height: {{ $heightPct }}%;"
                     title="{{ $monthLabel }}: Rp {{ number_format($val,0,',','.') }}">
                </div>
                <span class="text-[9px] text-gray-400">{{ $monthLabel }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Yearly Accordion Tables --}}
<div class="space-y-3" x-data="{ openYear: {{ $currentYear }} }">
    @forelse($yearlyData as $year => $data)
    <div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
        {{-- Accordion Header --}}
        <button
            @click="openYear = openYear === {{ $year }} ? null : {{ $year }}"
            class="w-full flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
            <h3 class="text-lg font-bold text-black">Tahun {{ $year }}</h3>
            <svg class="w-5 h-5 text-gray-500 transition-transform"
                 :class="openYear === {{ $year }} ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion Body --}}
        <div x-show="openYear === {{ $year }}" x-collapse>
            @if(count($data['monthly']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-blue-50 text-left">
                            <th class="px-5 py-3 font-semibold text-gray-700 w-12">No</th>
                            <th class="px-5 py-3 font-semibold text-gray-700">Bulan</th>
                            <th class="px-5 py-3 font-semibold text-gray-700 text-right">Sewa</th>
                            <th class="px-5 py-3 font-semibold text-gray-700 text-right">Listrik</th>
                            <th class="px-5 py-3 font-semibold text-gray-700 text-right">Air</th>
                            <th class="px-5 py-3 font-semibold text-gray-700 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stroke">
                        @php $no = 1; @endphp
                        @foreach($data['monthly'] as $m => $row)
                        <tr class="{{ $no % 2 === 0 ? 'bg-blue-50/30' : 'bg-white' }} hover:bg-blue-50/50 transition">
                            <td class="px-5 py-3 text-center text-gray-500">{{ $no++ }}</td>
                            <td class="px-5 py-3 font-medium text-black">
                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">{{ $row['rent'] > 0 ? number_format($row['rent'],0,',','.') : '-' }}</td>
                            <td class="px-5 py-3 text-right text-gray-700">{{ $row['electricity'] > 0 ? number_format($row['electricity'],0,',','.') : '-' }}</td>
                            <td class="px-5 py-3 text-right text-gray-700">{{ $row['water'] > 0 ? number_format($row['water'],0,',','.') : '-' }}</td>
                            <td class="px-5 py-3 text-right font-bold text-reoda">{{ number_format($row['total'],0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t-2 border-stroke">
                            <td colspan="2" class="px-5 py-3 font-bold text-black">Total {{ $year }}</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-800">{{ number_format($data['total_rent'],0,',','.') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-800">{{ number_format($data['total_elec'],0,',','.') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-800">{{ number_format($data['total_water'],0,',','.') }}</td>
                            <td class="px-5 py-3 text-right font-extrabold text-reoda text-base">{{ number_format($data['grand_total'],0,',','.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="px-6 py-8 text-center text-sm text-gray-400">Belum ada pembayaran yang tercatat pada tahun {{ $year }}.</div>
            @endif
        </div>
    </div>
    @empty
    <div class="rounded-xl border border-stroke bg-white shadow-sm py-16 text-center">
        <svg class="mx-auto h-14 w-14 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="font-medium text-gray-600 mb-1">Belum Ada Riwayat Pembayaran</p>
        <p class="text-sm text-gray-400">Laporan akan muncul setelah Anda menyelesaikan tagihan pertama.</p>
    </div>
    @endforelse
</div>

@endsection
