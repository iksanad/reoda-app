@extends('layouts.app')
@section('title', 'Dashboard Pengelola - REODA')

@section('content')

{{-- Greeting --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl font-extrabold text-reoda-dark">Selamat datang, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-sm text-gray-500 mt-1">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <a href="{{ route('manager.properties.create') }}"
        class="inline-flex items-center gap-2 rounded-xl bg-reoda px-5 py-2.5 text-sm font-bold text-white hover:bg-reoda-dark transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Properti
    </a>
</div>

{{-- Pending Contract Alert --}}
@if($pendingContracts > 0)
<div class="mb-5 flex items-center gap-4 rounded-xl border-l-4 border-yellow-400 bg-yellow-50 px-5 py-4 shadow-sm">
    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-200 shrink-0">
        <span class="text-lg">⏳</span>
    </div>
    <div class="flex-1">
        <p class="font-bold text-yellow-800">{{ $pendingContracts }} Pengajuan Kontrak Menunggu Persetujuan Anda</p>
        <p class="text-sm text-yellow-600">Penyewa sudah mengajukan, segera review dan setujui.</p>
    </div>
    <a href="{{ route('manager.contracts.index', ['status' => 'awaiting_approval']) }}"
        class="rounded-lg bg-yellow-400 hover:bg-yellow-500 px-4 py-2 text-sm font-bold text-white transition whitespace-nowrap">
        Lihat Sekarang →
    </a>
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-7">
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-reoda-lighter shrink-0">
            <svg class="w-6 h-6 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Total Unit</p>
            <h4 class="text-3xl font-extrabold text-black">{{ $totalUnits }}</h4>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 shrink-0">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Unit Disewa</p>
            <h4 class="text-3xl font-extrabold text-green-600">{{ $rentedUnits }}</h4>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Pendapatan</p>
            <h4 class="text-xl font-extrabold text-blue-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
            <p class="text-xs text-gray-400">Bulan ini</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $totalUnits > 0 ? 'bg-reoda-lighter' : 'bg-gray-100' }} shrink-0">
            <svg class="w-6 h-6 {{ $totalUnits > 0 ? 'text-reoda' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Tingkat Hunian</p>
            <h4 class="text-3xl font-extrabold {{ $totalUnits > 0 ? 'text-reoda' : 'text-gray-400' }}">
                {{ $totalUnits > 0 ? round(($rentedUnits / $totalUnits) * 100) : 0 }}%
            </h4>
        </div>
    </div>
</div>

{{-- Chart + Property Table --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
    {{-- Monthly Revenue Chart --}}
    <div class="lg:col-span-3 rounded-2xl bg-white p-6 shadow-sm border border-stroke">
        <h3 class="font-bold text-black mb-4">Grafik Pendapatan {{ now()->year }}</h3>
        <div style="height: 240px; position: relative;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Occupancy Donut --}}
    <div class="lg:col-span-2 rounded-2xl bg-white p-6 shadow-sm border border-stroke">
        <h3 class="font-bold text-black mb-4">Tingkat Hunian</h3>
        @if($totalUnits > 0)
        <div style="height: 200px; position: relative;">
            <canvas id="occupancyChart"></canvas>
        </div>
        <div class="flex justify-center gap-6 mt-3">
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-reoda inline-block"></span><span class="text-xs text-gray-500">Disewa ({{ $rentedUnits }})</span></div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-gray-200 inline-block"></span><span class="text-xs text-gray-500">Tersedia ({{ $totalUnits - $rentedUnits }})</span></div>
        </div>
        @else
        <div class="flex h-full items-center justify-center text-gray-400 text-sm pt-8">Belum ada unit</div>
        @endif
    </div>
</div>

{{-- Property Table --}}
<div class="rounded-2xl bg-white shadow-sm border border-stroke overflow-hidden">
    <div class="px-6 py-4 border-b border-stroke flex items-center justify-between">
        <h3 class="font-bold text-black">Lokasi Hunian</h3>
        <a href="{{ route('manager.properties.index') }}" class="text-sm text-reoda hover:underline font-medium">Lihat semua →</a>
    </div>
    @if($properties->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-50 text-left border-b border-stroke text-xs uppercase text-gray-400 tracking-wide">
                    <th class="py-3 px-6 font-semibold">Properti</th>
                    <th class="py-3 px-6 font-semibold text-center">Total Unit</th>
                    <th class="py-3 px-6 font-semibold text-center">Disewa</th>
                    <th class="py-3 px-6 font-semibold text-center">Hunian %</th>
                    <th class="py-3 px-6 font-semibold text-right">Pendapatan Bulan Ini</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stroke">
                @foreach($properties as $property)
                @php $occ = $property->units_count > 0 ? round(($property->rented_units_count / $property->units_count) * 100) : 0; @endphp
                <tr class="hover:bg-gray-50/60">
                    <td class="py-4 px-6">
                        <a href="{{ route('manager.properties.show', $property) }}" class="font-bold text-reoda-dark text-sm hover:text-reoda">{{ $property->name }}</a>
                        <p class="text-xs text-gray-400">{{ $property->city }}, {{ $property->province }}</p>
                    </td>
                    <td class="py-4 px-6 text-center font-bold text-sm text-gray-700">{{ $property->units_count }}</td>
                    <td class="py-4 px-6 text-center font-bold text-sm text-green-600">{{ $property->rented_units_count }}</td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                <div class="bg-reoda h-1.5 rounded-full" style="width: {{ $occ }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-600">{{ $occ }}%</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right font-bold text-sm text-reoda-dark">Rp {{ number_format($property->revenue, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-16 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        <p class="text-gray-500 font-medium mb-4">Belum ada properti terdaftar.</p>
        <a href="{{ route('manager.properties.create') }}" class="inline-flex rounded-xl bg-reoda py-2.5 px-6 font-bold text-white hover:bg-reoda-dark text-sm transition">
            Tambah Properti Pertama
        </a>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const revenueData = @json($monthlyRevenue);
    const labels     = @json($monthLabels);

    // Bar chart — monthly revenue
    const ctx1 = document.getElementById('revenueChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: revenueData,
                    backgroundColor: 'rgba(14,165,125,0.15)',
                    borderColor: '#0e9f6e',
                    borderWidth: 2,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(14,165,125,0.35)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'jt' : v.toLocaleString('id-ID')),
                            font: { size: 10 },
                        },
                        grid: { color: '#f3f4f6' }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // Donut chart — occupancy
    const ctx2 = document.getElementById('occupancyChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Disewa', 'Tersedia'],
                datasets: [{
                    data: [{{ $rentedUnits }}, {{ $totalUnits - $rentedUnits }}],
                    backgroundColor: ['#0e9f6e', '#e5e7eb'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.label + ': ' + ctx.raw + ' unit'
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
