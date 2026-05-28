@extends('layouts.app')

@section('title', $tenant->name . ' - Detail Penyewa')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Detail Penyewa</h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.tenants.index') }}">Penyewa /</a></li>
            <li class="font-medium text-reoda">{{ $tenant->name }}</li>
        </ol>
    </nav>
</div>

<!-- Profile Card -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 flex flex-col items-center text-center">
        <div class="h-24 w-24 rounded-full overflow-hidden mb-4 ring-4 ring-reoda-lightest">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($tenant->name) }}&background=4C74AF&color=fff&size=96" alt="{{ $tenant->name }}" class="w-full h-full object-cover">
        </div>
        <h3 class="text-2xl font-extrabold text-reoda-dark mb-1">{{ $tenant->name }}</h3>
        <p class="text-sm font-medium text-gray-500 mb-4">{{ $tenant->email }}</p>
        @if($tenant->phone)
            <a href="tel:{{ $tenant->phone }}" class="inline-flex items-center gap-2 text-sm font-bold text-reoda hover:text-reoda-dark transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                {{ $tenant->phone }}
            </a>
        @endif
        <div class="mt-6 w-full border-t border-gray-100 pt-5 text-left space-y-3">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Bergabung: <span class="font-bold text-reoda-dark ml-1">{{ $tenant->created_at->format('d M Y') }}</span></p>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Kontrak: <span class="font-bold text-reoda-dark ml-1">{{ $contracts->count() }}</span></p>
        </div>
    </div>

    <!-- Active Contract Summary -->
    <div class="lg:col-span-2">
        @php $activeContract = $contracts->where('status', 'active')->first(); @endphp
        @if($activeContract)
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 h-full">
            <div class="flex items-center justify-between mb-6">
                <h4 class="font-extrabold text-reoda-dark text-xl">Kontrak Aktif Saat Ini</h4>
                <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold bg-[#b0ebb0] text-[#1a5e1a]">Aktif</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-xl bg-[#e6f4f1] p-4">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Properti</p>
                    <p class="font-extrabold text-reoda-dark">{{ $activeContract->unit->property->name }}</p>
                </div>
                <div class="rounded-xl bg-[#e6f4f1] p-4">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Unit / Kamar</p>
                    <p class="font-extrabold text-reoda-dark">{{ $activeContract->unit->unit_code }} - {{ $activeContract->unit->name }}</p>
                </div>
                <div class="rounded-xl bg-[#e6f4f1] p-4">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Mulai Sewa</p>
                    <p class="font-extrabold text-reoda-dark">{{ \Carbon\Carbon::parse($activeContract->start_date)->format('d M Y') }}</p>
                </div>
                <div class="rounded-xl bg-[#e6f4f1] p-4">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Akhir Sewa</p>
                    <p class="font-extrabold text-reoda-dark">{{ \Carbon\Carbon::parse($activeContract->end_date)->format('d M Y') }}</p>
                </div>
                <div class="rounded-xl bg-[#e6f4f1] p-4">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Harga Sewa</p>
                    <p class="font-extrabold text-reoda-dark">Rp {{ number_format($activeContract->rent_amount, 0, ',', '.') }}<span class="text-[10px] text-gray-500 font-medium">/bln</span></p>
                </div>
                <div class="rounded-xl bg-[#e6f4f1] p-4">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Deposit</p>
                    <p class="font-extrabold text-reoda-dark">Rp {{ number_format($activeContract->deposit_amount ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-8 text-center flex flex-col items-center justify-center h-full">
            <svg class="h-14 w-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="text-gray-500 font-medium">Tidak ada kontrak aktif saat ini.</p>
        </div>
        @endif
    </div>
</div>

<!-- Riwayat Kontrak -->
<div class="mb-4 mt-8 flex items-center justify-between">
    <h3 class="text-xl font-extrabold text-reoda-dark">Riwayat Kontrak</h3>
</div>
<div class="rounded-2xl bg-white shadow-sm p-4 sm:p-6 overflow-hidden border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] border-separate" style="border-spacing: 0 8px;">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-reoda-lightest text-left text-sm font-bold text-reoda-dark rounded-l-full">Unit</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-left text-sm font-bold text-reoda-dark mx-1">Periode</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-left text-sm font-bold text-reoda-dark mx-1">Harga Sewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-r-full">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts as $contract)
                @php
                    $statusColor = match($contract->status) {
                        'active' => 'bg-[#b0ebb0] text-[#1a5e1a]',
                        'expired' => 'bg-[#fce599] text-[#7a5c00]',
                        'terminated' => 'bg-[#f9c5c5] text-[#7a1c1c]',
                        default => 'bg-gray-200 text-gray-700'
                    };
                    $statusLabel = match($contract->status) {
                        'active' => 'Aktif', 'expired' => 'Berakhir', 'terminated' => 'Dibatalkan', default => ucfirst($contract->status)
                    };
                @endphp
                <tr class="even:bg-[#e6f4f1] odd:bg-white">
                    <td class="px-6 py-4 rounded-l-full">
                        <p class="font-extrabold text-reoda-dark text-sm">{{ $contract->unit->unit_code }}</p>
                        <p class="text-xs font-medium text-gray-500">{{ $contract->unit->property->name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-reoda-dark">{{ \Carbon\Carbon::parse($contract->start_date)->format('d M Y') }}</p>
                        <p class="text-xs font-medium text-gray-500">s/d {{ \Carbon\Carbon::parse($contract->end_date)->format('d M Y') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-reoda-dark">Rp {{ number_format($contract->rent_amount, 0, ',', '.') }}<span class="text-[10px] text-gray-500 font-medium">/bln</span></p>
                    </td>
                    <td class="px-6 py-4 rounded-r-full text-center">
                        <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold {{ $statusColor }}">{{ $statusLabel }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
