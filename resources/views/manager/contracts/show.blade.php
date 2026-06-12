@extends('layouts.app')
@section('title', 'Detail Kontrak - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Detail Kontrak Sewa</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.contracts.index') }}">Kontrak /</a></li>
        <li class="font-medium text-reoda">Detail</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

@php
    $sc = match($contract->status) {
        'active'     => ['label'=>'Aktif','class'=>'bg-success-50 text-success-700 border-success-200'],
        'expired'    => ['label'=>'Berakhir','class'=>'bg-gray-50 text-gray-700 border-gray-200'],
        'terminated' => ['label'=>'Diakhiri','class'=>'bg-error-50 text-error-700 border-error-200'],
        default      => ['label'=>ucfirst($contract->status),'class'=>'bg-gray-50 text-gray-700 border-gray-200'],
    };
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">

        {{-- Status --}}
        <div class="flex items-center justify-between rounded-2xl border px-6 py-5 {{ str_replace('border-', 'border-', $sc['class']) }}">
            <p class="font-extrabold text-lg">Status: {{ $sc['label'] }}</p>
            @if($contract->status === 'active')
            @php $daysLeft = now()->diffInDays($contract->end_date, false); @endphp
            <p class="text-sm font-bold">{{ $daysLeft > 0 ? $daysLeft.' hari lagi' : 'Sudah jatuh tempo' }}</p>
            @endif
        </div>

        {{-- Contract Details --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5 bg-gray-50/50"><h4 class="font-extrabold text-reoda-dark text-lg">Informasi Kontrak</h4></div>
            <div class="p-6 grid grid-cols-2 gap-5 text-sm">
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">No. Kontrak</p><p class="font-extrabold text-reoda-dark font-mono text-base">{{ $contract->contract_number }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Jenis Sewa</p><p class="font-extrabold text-reoda-dark capitalize text-base">{{ $contract->rental_type === 'monthly' ? 'Bulanan' : 'Tahunan' }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Mulai Sewa</p><p class="font-extrabold text-reoda-dark text-base">{{ $contract->start_date->format('d M Y') }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Akhir Sewa</p><p class="font-extrabold text-reoda-dark text-base">{{ $contract->end_date->format('d M Y') }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Harga Sewa / Bln</p><p class="text-xl font-extrabold text-reoda">Rp {{ number_format($contract->rent_amount,0,',','.') }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Deposit</p><p class="font-extrabold text-reoda-dark text-base">Rp {{ number_format($contract->deposit_amount,0,',','.') }}</p></div>
                @if($contract->notes)
                <div class="col-span-2 mt-2 pt-4 border-t border-gray-100"><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Catatan</p><p class="text-gray-700 font-medium leading-relaxed">{{ $contract->notes }}</p></div>
                @endif
            </div>
        </div>

        {{-- Invoice History --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5 bg-gray-50/50 flex justify-between items-center">
                <h4 class="font-extrabold text-reoda-dark text-lg">Riwayat Invoice</h4>
                <span class="inline-flex items-center justify-center bg-gray-200 text-gray-700 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $contract->invoices->count() }}</span>
            </div>
            @if($contract->invoices->count() > 0)
            <div class="overflow-x-auto p-4">
                <table class="w-full text-sm border-separate" style="border-spacing: 0 4px;">
                    <thead><tr>
                        <th class="py-3 px-6 bg-reoda-lightest text-left font-bold text-reoda-dark rounded-l-full">No. Invoice</th>
                        <th class="py-3 px-4 bg-reoda-lightest text-left font-bold text-reoda-dark mx-1">Jenis</th>
                        <th class="py-3 px-4 bg-reoda-lightest text-left font-bold text-reoda-dark mx-1">Periode</th>
                        <th class="py-3 px-4 bg-reoda-lightest text-left font-bold text-reoda-dark mx-1">Nominal</th>
                        <th class="py-3 px-4 bg-reoda-lightest text-left font-bold text-reoda-dark rounded-r-full">Status</th>
                    </tr></thead>
                    <tbody>
                        @foreach($contract->invoices as $inv)
                        @php
                            $is = match($inv->status) {
                                'paid'    => 'bg-[#b0ebb0] text-[#1a5e1a]',
                                'pending' => 'bg-[#fce599] text-[#7a5c00]',
                                'unpaid'  => 'bg-[#f9c5c5] text-[#7a1c1c]',
                                default   => 'bg-gray-200 text-gray-700',
                            };
                        @endphp
                        <tr class="even:bg-[#e6f4f1] odd:bg-white transition hover:bg-gray-50">
                            <td class="py-3 px-6 font-mono text-xs font-bold text-reoda-dark rounded-l-full">{{ $inv->invoice_number }}</td>
                            <td class="py-3 px-4 font-bold text-gray-600 capitalize">{{ $inv->type }}</td>
                            <td class="py-3 px-4 font-bold text-gray-600">{{ $inv->billing_month }}/{{ $inv->billing_year }}</td>
                            <td class="py-3 px-4 font-extrabold text-reoda-dark">Rp {{ number_format($inv->amount,0,',','.') }}</td>
                            <td class="py-3 px-4 rounded-r-full"><span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $is }}">{{ ucfirst($inv->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-12 text-center flex flex-col items-center">
                <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-sm font-medium text-gray-500">Belum ada invoice untuk kontrak ini.</span>
            </div>
            @endif
        </div>

        {{-- Approval Actions --}}
        @if($contract->status === 'awaiting_approval')
        <div class="rounded-2xl border border-reoda/30 bg-reoda-lightest/30 shadow-sm p-6" x-data="{ openReject: false }">
            <h4 class="font-extrabold text-reoda-dark mb-2 text-lg">Tinjauan Pengajuan Kontrak</h4>
            <p class="text-sm text-gray-600 mb-5 font-medium">Penyewa ini telah mengajukan penyewaan. Silakan tinjau profil dan riwayat sebelum mengambil keputusan.</p>
            
            <div class="flex flex-wrap items-center gap-3" x-show="!openReject">
                <form action="{{ route('manager.contracts.approve-request', $contract) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Setujui pengajuan kontrak ini? Status unit akan dikunci untuk penyewa ini.')" class="inline-flex items-center gap-2 rounded-xl bg-success-600 px-6 py-2.5 font-bold text-white hover:bg-success-700 transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Setujui Pengajuan
                    </button>
                </form>
                
                <button type="button" @click="openReject = true" class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-300 px-6 py-2.5 font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Tolak
                </button>
            </div>

            {{-- Reject Form --}}
            <div x-show="openReject" x-transition style="display:none" class="mt-4 p-4 bg-white rounded-xl border border-gray-200">
                <form action="{{ route('manager.contracts.reject-request', $contract) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Penolakan</label>
                        <textarea name="rejection_reason" rows="3" required placeholder="Tuliskan alasan penolakan (akan dikirimkan ke penyewa)..." class="w-full rounded-lg border border-gray-300 py-3 px-4 text-sm font-medium outline-none focus:border-reoda focus:ring-1 focus:ring-reoda transition"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" onclick="return confirm('Anda yakin menolak pengajuan ini?')" class="rounded-lg bg-error-600 px-6 py-2 font-bold text-white hover:bg-error-700 transition shadow-sm">Konfirmasi Tolak</button>
                        <button type="button" @click="openReject = false" class="rounded-lg bg-gray-100 px-6 py-2 font-bold text-gray-600 hover:bg-gray-200 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Invoice History + Create Invoice --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm" x-data="{ openInvoice: false }">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h4 class="font-extrabold text-reoda-dark text-lg">Riwayat Invoice / Tagihan</h4>
                @if($contract->status === 'active')
                <button @click="openInvoice = true" type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-reoda px-4 py-2 text-sm font-bold text-white hover:bg-reoda-dark transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Tagihan
                </button>
                @endif
            </div>

            {{-- Invoice Table --}}
            @php
                $invoices = $contract->invoices()->orderBy('created_at','desc')->get();
                $invLabels = ['rent'=>'Sewa','electricity'=>'Listrik','water'=>'Air','ipl'=>'IPL','deposit'=>'Deposit'];
            @endphp
            @if($invoices->isEmpty())
            <div class="px-6 py-8 text-center text-sm text-gray-400">Belum ada tagihan untuk kontrak ini.</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">No. Invoice</th>
                            <th class="px-4 py-3 text-left">Jenis</th>
                            <th class="px-4 py-3 text-left">Periode</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($invoices as $inv)
                        @php
                            $isc = match($inv->status) {
                                'paid'    => 'bg-success-50 text-success-700',
                                'unpaid'  => 'bg-error-50 text-error-700',
                                'pending','pending_verification' => 'bg-warning-50 text-warning-700',
                                'overdue' => 'bg-error-100 text-error-800',
                                default   => 'bg-gray-50 text-gray-600',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3 font-medium">{{ $invLabels[$inv->type] ?? ucfirst($inv->type) }}</td>
                            <td class="px-4 py-3">{{ $inv->billing_month }}/{{ $inv->billing_year }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 {{ $inv->due_date && $inv->due_date->isPast() && $inv->status !== 'paid' ? 'text-error-600 font-semibold' : '' }}">{{ $inv->due_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3"><span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $isc }}">{{ ucfirst($inv->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Create Invoice Modal --}}
            <div x-show="openInvoice" x-transition style="display:none"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 p-4 sm:p-6 backdrop-blur-sm" @click.self="openInvoice = false">
                <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-y-auto max-h-[90vh]" x-data="{ invType: 'rent', iplDefault: {{ $contract->unit->property->ipl_amount ?? 0 }} }">
                    <div class="sticky top-0 z-10 border-b border-gray-100 bg-white/90 px-6 py-5 backdrop-blur-md flex items-center justify-between">
                        <div>
                            <h5 class="font-extrabold text-reoda-dark text-xl">Buat Tagihan Baru</h5>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">Unit {{ $contract->unit->unit_code }} - {{ $contract->tenant->name }}</p>
                        </div>
                        <button @click="openInvoice = false" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('manager.contracts.invoices.store', $contract) }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Tagihan <span class="text-error-500">*</span></label>
                            <select name="type" x-model="invType" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                                <option value="rent">Sewa Hunian</option>
                                <option value="electricity">Listrik</option>
                                <option value="water">Air</option>
                                @if($contract->unit->property->type === 'apartemen')
                                <option value="ipl">IPL / Maintenance Fee</option>
                                @endif
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">Bulan <span class="text-error-500">*</span></label>
                                <select name="billing_month" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                                    @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">Tahun <span class="text-error-500">*</span></label>
                                <input type="number" name="billing_year" value="{{ now()->year }}" min="2020" max="2100" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                            </div>
                        </div>

                        {{-- Meter fields — only for electricity/water --}}
                        <div x-show="invType === 'electricity' || invType === 'water'" style="display:none" class="grid grid-cols-3 gap-3 p-3 bg-blue-50 rounded-xl border border-blue-200">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Meter Awal</label>
                                <input type="number" name="meter_start" step="0.01" min="0" placeholder="0" class="w-full rounded-lg border border-stroke py-2 px-3 text-sm outline-none focus:border-reoda transition">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Meter Akhir</label>
                                <input type="number" name="meter_end" step="0.01" min="0" placeholder="0" class="w-full rounded-lg border border-stroke py-2 px-3 text-sm outline-none focus:border-reoda transition">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Tarif/Unit (Rp)</label>
                                <input type="number" name="price_per_unit" step="1" min="0" placeholder="0" class="w-full rounded-lg border border-stroke py-2 px-3 text-sm outline-none focus:border-reoda transition">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Total Tagihan (Rp) <span class="text-error-500">*</span></label>
                            <input type="number" name="amount" min="1000" step="1" placeholder="Contoh: 500000" required
                                :value="invType === 'ipl' && iplDefault > 0 ? iplDefault : ''"
                                class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                            <p x-show="invType === 'ipl' && iplDefault > 0" style="display:none" class="mt-1 text-xs text-blue-600">
                                Default IPL properti ini: Rp {{ number_format($contract->unit->property->ipl_amount ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Jatuh Tempo <span class="text-error-500">*</span></label>
                            <input type="date" name="due_date" value="{{ now()->addDays(7)->format('Y-m-d') }}" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                            <textarea name="notes" rows="2" placeholder="Keterangan tambahan..." class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition"></textarea>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="w-full rounded-xl bg-reoda py-3.5 font-extrabold text-white hover:bg-reoda-dark transition shadow-md hover:shadow-lg flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Terbitkan Tagihan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Terminate --}}

        @if($contract->status === 'active')
        <div class="rounded-2xl border border-error-200 bg-white shadow-sm p-6" x-data="{ open: false }">
            <h4 class="font-extrabold text-error-600 mb-3 text-lg">Akhiri Kontrak</h4>
            <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-lg border border-error-300 bg-error-50 px-5 py-2.5 text-sm font-bold text-error-600 hover:bg-error-100 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Akhiri Kontrak Ini
            </button>
            <div x-show="open" x-transition style="display:none" class="mt-5 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <form action="{{ route('manager.contracts.terminate', $contract) }}" method="POST" class="space-y-4">
                    @csrf
                    <textarea name="termination_reason" rows="3" required placeholder="Alasan pengakhiran kontrak..." class="w-full rounded-lg border border-gray-300 py-3 px-4 text-sm font-medium outline-none focus:border-error-500 focus:ring-1 focus:ring-error-500 transition"></textarea>
                    <button type="submit" onclick="return confirm('Anda yakin ingin mengakhiri kontrak ini?')" class="rounded-lg bg-error-600 px-6 py-2.5 font-bold text-white hover:bg-error-700 transition shadow-sm">Konfirmasi Pengakhiran</button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Right sidebar --}}
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
            <h4 class="font-extrabold text-reoda-dark mb-5 text-lg">Penyewa</h4>
            <div class="flex items-center gap-4 mb-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($contract->tenant->name) }}&background=4C74AF&color=fff&size=48" class="h-14 w-14 rounded-full ring-2 ring-reoda-lightest">
                <div><p class="font-extrabold text-reoda-dark text-lg leading-tight">{{ $contract->tenant->name }}</p><p class="text-xs font-medium text-gray-500 mt-0.5">{{ $contract->tenant->email }}</p></div>
            </div>
            @if($contract->tenant->phone)<p class="text-sm font-bold text-gray-600 mb-4 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> {{ $contract->tenant->phone }}</p>@endif
            <a href="{{ route('manager.tenants.show', $contract->tenant) }}" class="inline-flex items-center justify-center w-full rounded-lg bg-gray-100 py-2.5 px-4 text-sm font-bold text-reoda-dark hover:bg-gray-200 transition">Lihat Profil Penyewa</a>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
            <h4 class="font-extrabold text-reoda-dark mb-5 text-lg">Unit & Properti</h4>
            <div class="space-y-4 text-sm">
                <div><span class="text-xs font-bold text-gray-500 uppercase block mb-1">Properti</span><span class="font-extrabold text-reoda-dark text-base">{{ $contract->unit->property->name }}</span></div>
                <div><span class="text-xs font-bold text-gray-500 uppercase block mb-1">Kode Unit</span><span class="font-extrabold text-reoda-dark text-base">{{ $contract->unit->unit_code }}</span></div>
                <div><span class="text-xs font-bold text-gray-500 uppercase block mb-1">Tipe</span><span class="font-extrabold text-reoda-dark text-base">{{ $contract->unit->type }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
