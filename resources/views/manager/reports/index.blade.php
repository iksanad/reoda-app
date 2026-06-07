@extends('layouts.app')
@section('title', 'Laporan Pembayaran - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Laporan Pembayaran</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Laporan</li>
    </ol></nav>
</div>

<form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="mb-1.5 block text-xs font-medium text-gray-700">Tahun</label>
        <select name="year" class="rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
            @foreach($years as $y)<option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-gray-700">Bulan (Opsional)</label>
        <select name="month" class="rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
            <option value="">Semua Bulan</option>
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i=>$nm)
            <option value="{{ $i+1 }}" {{ $month==$i+1?'selected':'' }}>{{ $nm }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-gray-700">Properti (Opsional)</label>
        <select name="property_id" class="rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition min-w-[160px]">
            <option value="">Semua Properti</option>
            @foreach(\App\Models\Property::where('manager_id', auth()->id())->orderBy('name')->get() as $p)
            <option value="{{ $p->id }}" {{ request('property_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="rounded-lg bg-reoda px-5 py-2.5 text-sm font-semibold text-white hover:bg-reoda-dark transition">Filter</button>
    <a href="{{ route('manager.reports.export', request()->only('year','month','property_id')) }}"
        class="rounded-lg border border-reoda px-5 py-2.5 text-sm font-semibold text-reoda hover:bg-reoda-lightest flex items-center gap-2 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export XLSX
    </a>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
    <div class="rounded-xl border border-stroke bg-white shadow-sm p-6 flex items-center gap-5">
        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-reoda/10 shrink-0">
            <svg class="w-7 h-7 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Pendapatan</p>
            <p class="text-2xl font-extrabold text-reoda-dark">Rp {{ number_format($totalRevenue,0,',','.') }}</p>
            <p class="text-xs text-gray-400">{{ $month ? \Carbon\Carbon::create()->month($month)->translatedFormat('F') : 'Seluruh bulan' }} {{ $year }}</p>
        </div>
    </div>
    <div class="rounded-xl border border-stroke bg-white shadow-sm p-6 flex items-center gap-5">
        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-success-100 shrink-0">
            <svg class="w-7 h-7 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Transaksi Dikonfirmasi</p>
            <p class="text-2xl font-extrabold text-reoda-dark">{{ $totalPaid }}</p>
            <p class="text-xs text-gray-400">Pembayaran terverifikasi</p>
        </div>
    </div>
</div>

{{-- Bar Chart (Chart.js) --}}
<div class="rounded-xl border border-stroke bg-white shadow-sm p-6 mb-6">
    <h4 class="font-bold text-black mb-4">Grafik Pendapatan {{ $year }}</h4>
    <div style="height: 220px; position: relative;">
        <canvas id="reportChart"></canvas>
    </div>
</div>

<div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
    <div class="border-b border-stroke px-6 py-4 flex items-center justify-between">
        <h4 class="font-bold text-black">Daftar Pembayaran</h4>
        <span class="text-sm text-gray-500">{{ $payments->total() }} transaksi</span>
    </div>
    @if($payments->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 border-b border-stroke">
                <th class="py-3 px-6 text-left font-semibold text-gray-600">Penyewa</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-600">Unit</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-600">Jenis</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-600">Nominal</th>
                <th class="py-3 px-4 text-left font-semibold text-gray-600">Tanggal</th>
            </tr></thead>
            <tbody class="divide-y divide-stroke">
                @foreach($payments as $p)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-6 font-medium">{{ $p->invoice->leaseContract->tenant->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $p->invoice->leaseContract->unit->unit_code ?? '-' }} <span class="text-xs text-gray-400">{{ $p->invoice->leaseContract->unit->property->name ?? '' }}</span></td>
                    <td class="py-3 px-4 capitalize">{{ $p->invoice->type }}</td>
                    <td class="py-3 px-4 font-bold text-reoda">Rp {{ number_format($p->amount,0,',','.') }}</td>
                    <td class="py-3 px-4 text-gray-500">{{ $p->paid_at?->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border-t border-stroke p-4">{{ $payments->links() }}</div>
    @else
    <div class="py-12 text-center text-sm text-gray-400">Tidak ada data pembayaran untuk periode ini.</div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const months = @json(array_values($monthlyData));
    const labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const activeMonth = {{ $month ?: 0 }};
    const colors = months.map((_, i) => (activeMonth > 0 && (i+1) === activeMonth) ? '#0e9f6e' : 'rgba(14,165,125,0.25)');
    const borders = months.map((_, i) => (activeMonth > 0 && (i+1) === activeMonth) ? '#0e9f6e' : 'rgba(14,165,125,0.6)');

    const ctx = document.getElementById('reportChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: months,
                    backgroundColor: colors,
                    borderColor: borders,
                    borderWidth: 1.5,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            callback: v => v >= 1000000 ? 'Rp '+(v/1000000).toFixed(1)+'jt' : 'Rp '+v.toLocaleString('id-ID'),
                            font: { size: 10 }
                        }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
@endpush
