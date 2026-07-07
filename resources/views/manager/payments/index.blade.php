@extends('layouts.app')

@section('title', 'Manajemen Pembayaran - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Pembayaran</h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li class="font-medium text-reoda">Pembayaran</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="mb-5 flex w-full border-l-4 border-success-500 bg-success-50 px-5 py-3 shadow-sm rounded-md">
    <p class="text-sm font-medium text-success-700">{{ session('success') }}</p>
</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lighter shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Pendapatan</p>
            <h4 class="text-xl font-extrabold text-reoda-dark mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Total Keseluruhan</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Dikonfirmasi</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['verified'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Transaksi</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Menunggu</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['pending'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Transaksi</p>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-5 shadow-sm flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-7 h-7 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-reoda-dark uppercase tracking-wide">Semua</p>
            <h4 class="text-3xl font-extrabold text-reoda-dark mt-1">{{ $stats['total'] }}</h4>
            <p class="text-[10px] font-medium text-gray-500">Transaksi</p>
        </div>
    </div>
</div>

{{-- Status filter tabs --}}
<div class="mb-5 flex flex-wrap gap-2">
    @php
        $statuses = ['all' => 'Semua', 'pending' => 'Menunggu', 'verified' => 'Dikonfirmasi', 'rejected' => 'Ditolak'];
        $currentStatus = request('status', 'all');
        $tabColors = ['all' => 'bg-gray-700', 'pending' => 'bg-warning-500', 'verified' => 'bg-success-600', 'rejected' => 'bg-error-600'];
    @endphp
    @foreach($statuses as $key => $label)
    <a href="{{ route('manager.payments.index', array_merge(request()->except('page'), ['status' => $key === 'all' ? null : $key])) }}"
       class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold transition
           {{ ($currentStatus === $key || ($currentStatus === 'all' && $key === 'all')) ? $tabColors[$key].' text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
        {{ $label }}
        <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $counts[$key] ?? 0 }}</span>
    </a>
    @endforeach
</div>

{{-- Search --}}
<div class="mb-5">
    <form method="GET" action="{{ route('manager.payments.index') }}" class="flex gap-3">
        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
        <div class="relative flex-1 max-w-sm">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama penyewa..." class="w-full rounded-lg border border-stroke bg-white py-2.5 pl-9 pr-4 text-sm outline-none focus:border-reoda transition" />
        </div>
        <button type="submit" class="rounded-lg bg-reoda px-4 py-2.5 text-sm font-medium text-white hover:bg-reoda-dark transition">Cari</button>
    </form>
</div>

{{-- Payments Table --}}
<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h3 class="text-xl font-extrabold text-reoda-dark">Daftar Pembayaran</h3>
    <div class="flex items-center gap-2">
        <a href="{{ route('manager.payments.export') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export XLSX
        </a>
    </div>
</div>

<div class="rounded-2xl bg-white shadow-sm p-4 sm:p-6 overflow-hidden">
    @if($payments->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full min-w-[800px] border-separate" style="border-spacing: 0 8px;">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-l-full">Penyewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Invoice</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Nominal</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Metode</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Tanggal</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Status</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-r-full">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                @php
                    $tenant = $payment->tenant ?? $payment->invoice->leaseContract->tenant;
                @endphp
                <tr class="even:bg-[#e6f4f1] odd:bg-white">
                    <td class="px-6 py-4 rounded-l-full">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($tenant->name) }}&background=4C74AF&color=fff&size=32" class="h-8 w-8 rounded-full shrink-0" alt="">
                            <div>
                                <p class="font-bold text-reoda-dark text-sm">{{ $tenant->name }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->invoice->leaseContract->unit->property->name ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="font-bold text-reoda-dark text-sm font-mono">{{ $payment->invoice->invoice_number ?? '-' }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ $payment->invoice->type ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="font-bold text-reoda-dark text-sm">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="font-bold text-reoda-dark text-sm">{{ $payment->payment_method ?? 'Manual' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="font-bold text-reoda-dark text-sm">{{ $payment->paid_at ? $payment->paid_at->format('d M Y') : '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->paid_at ? $payment->paid_at->format('H:i') : '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusColor = match($payment->status) {
                                'pending' => 'bg-[#fce599] text-[#7a5c00]',
                                'verified' => 'bg-[#b0ebb0] text-[#1a5e1a]',
                                'rejected' => 'bg-[#f9c5c5] text-[#7a1c1c]',
                                default => 'bg-gray-200 text-gray-700'
                            };
                            $statusText = match($payment->status) {
                                'pending' => 'Menunggu',
                                'verified' => 'Dikonfirmasi',
                                'rejected' => 'Ditolak',
                                default => ucfirst($payment->status)
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusColor }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-6 py-4 rounded-r-full text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('manager.payments.show', $payment) }}" title="Lihat Detail"
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
        {{ $payments->links() }}
    </div>
    @else
    <div class="py-12 text-center border-t border-gray-100">
        <svg class="mx-auto h-14 w-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        <p class="text-gray-500 mb-1 font-medium">Tidak ada data pembayaran</p>
        <p class="text-sm text-gray-400">Pembayaran akan muncul saat penyewa melakukan transaksi.</p>
    </div>
    @endif
</div>
@endsection
