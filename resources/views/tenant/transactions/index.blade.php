@extends('layouts.app')

@section('title', 'Transaksi & Tagihan - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Transaksi & Tagihan</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Transaksi</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

@if(session('info'))
<div class="mb-5 rounded-md border-l-4 border-blue-400 bg-blue-50 px-5 py-3 text-sm font-medium text-blue-700">{{ session('info') }}</div>
@endif

{{-- PLN / PDAM Static Info Card --}}
@php
    $property = $activeContract?->unit?->property;
    $plnId     = $activeContract?->unit?->pln_customer_id;
    $pdamId    = $activeContract?->unit?->pdam_customer_id;
    $elecConf  = $property?->electricity_config;
    $waterConf = $property?->water_config;
    $showPln   = $activeContract && $elecConf === 'token' && $plnId;
    $showPdam  = $activeContract && $waterConf === 'pdam' && $pdamId;
@endphp

@if($showPln || $showPdam)
<div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
    @if($showPln)
    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-5 flex flex-col gap-3">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-700 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="font-bold text-yellow-800 text-sm">⚡ Listrik Token / Prabayar</p>
                <p class="text-xs text-yellow-600">Isi token sendiri — gunakan ID Pelanggan PLN berikut:</p>
            </div>
        </div>
        <div class="flex items-center gap-2 bg-white rounded-lg border border-yellow-200 px-4 py-2.5">
            <span id="pln-id" class="font-mono font-bold text-gray-800 tracking-wider flex-1">{{ $plnId }}</span>
            <button onclick="copyToClipboard('pln-id', this)" class="text-xs font-medium text-yellow-700 hover:text-yellow-900 border border-yellow-300 rounded px-2 py-1 transition">Copy</button>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="https://shopee.co.id/search?keyword=token+listrik+pln" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3 py-2 text-xs font-bold text-white hover:bg-orange-600 transition">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm3.536-11.414l-4.95 4.95-2.12-2.121L7.05 12.83l3.536 3.536 6.364-6.364-1.414-1.416z"/></svg>
                Beli di Shopee
            </a>
            <a href="https://www.tokopedia.com/search?st=product&q=token+listrik+pln" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-2 text-xs font-bold text-white hover:bg-green-700 transition">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm3.536-11.414l-4.95 4.95-2.12-2.121L7.05 12.83l3.536 3.536 6.364-6.364-1.414-1.416z"/></svg>
                Beli di Tokopedia
            </a>
        </div>
    </div>
    @endif

    @if($showPdam)
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5 flex flex-col gap-3">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <div>
                <p class="font-bold text-blue-800 text-sm">💧 Air PDAM (Bayar Sendiri)</p>
                <p class="text-xs text-blue-600">Tagihan air diurus langsung ke PDAM — gunakan ID Pelanggan berikut:</p>
            </div>
        </div>
        <div class="flex items-center gap-2 bg-white rounded-lg border border-blue-200 px-4 py-2.5">
            <span id="pdam-id" class="font-mono font-bold text-gray-800 tracking-wider flex-1">{{ $pdamId }}</span>
            <button onclick="copyToClipboard('pdam-id', this)" class="text-xs font-medium text-blue-700 hover:text-blue-900 border border-blue-300 rounded px-2 py-1 transition">Copy</button>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="https://shopee.co.id/search?keyword=tagihan+pdam" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3 py-2 text-xs font-bold text-white hover:bg-orange-600 transition">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm3.536-11.414l-4.95 4.95-2.12-2.121L7.05 12.83l3.536 3.536 6.364-6.364-1.414-1.416z"/></svg>
                Bayar di Shopee
            </a>
            <a href="https://www.tokopedia.com/search?st=product&q=tagihan+air+pdam" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-2 text-xs font-bold text-white hover:bg-green-700 transition">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm3.536-11.414l-4.95 4.95-2.12-2.121L7.05 12.83l3.536 3.536 6.364-6.364-1.414-1.416z"/></svg>
                Bayar di Tokopedia
            </a>
        </div>
    </div>
    @endif
</div>
@endif

{{-- Filter Bar --}}
<form method="GET" action="{{ route('tenant.transactions.index') }}" id="filter-form"
      class="mb-5 rounded-xl border border-stroke bg-white shadow-sm p-4 flex flex-wrap gap-3 items-end">
    <div class="flex flex-col gap-1">
        <label class="text-xs font-medium text-gray-500">Jenis Tagihan</label>
        <select name="type" id="filter-type" onchange="document.getElementById('filter-form').submit()"
                class="rounded-lg border border-stroke bg-transparent py-2 px-3 text-sm text-gray-700 outline-none focus:border-reoda">
            <option value="">Semua Jenis</option>
            <option value="rent" {{ request('type') === 'rent' ? 'selected' : '' }}>Sewa Hunian</option>
            <option value="electricity" {{ request('type') === 'electricity' ? 'selected' : '' }}>Listrik</option>
            <option value="water" {{ request('type') === 'water' ? 'selected' : '' }}>Air</option>
            <option value="ipl" {{ request('type') === 'ipl' ? 'selected' : '' }}>IPL / Maintenance</option>
            <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
        </select>
    </div>
    <div class="flex flex-col gap-1">
        <label class="text-xs font-medium text-gray-500">Tahun</label>
        <select name="year" id="filter-year" onchange="document.getElementById('filter-form').submit()"
                class="rounded-lg border border-stroke bg-transparent py-2 px-3 text-sm text-gray-700 outline-none focus:border-reoda">
            <option value="">Semua Tahun</option>
            @foreach($availableYears as $y)
            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex flex-col gap-1">
        <label class="text-xs font-medium text-gray-500">Bulan</label>
        <select name="month" id="filter-month" onchange="document.getElementById('filter-form').submit()"
                class="rounded-lg border border-stroke bg-transparent py-2 px-3 text-sm text-gray-700 outline-none focus:border-reoda">
            <option value="">Semua Bulan</option>
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}
            </option>
            @endforeach
        </select>
    </div>
    @if(request()->hasAny(['type','year','month','status']))
    <a href="{{ route('tenant.transactions.index') }}"
       class="rounded-lg border border-stroke px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
        Reset Filter
    </a>
    @endif
</form>

{{-- Status Tabs --}}
<div class="mb-5 flex flex-wrap gap-2">
    @php
        $tabs = ['all'=>'Semua','unpaid'=>'Belum Dibayar','pending'=>'Menunggu','paid'=>'Lunas'];
        $cur  = request('status','all');
        $tabColors = ['all'=>'bg-gray-700','unpaid'=>'bg-red-500','pending'=>'bg-warning-500','paid'=>'bg-success-600'];
    @endphp
    @foreach($tabs as $key => $label)
    <a href="{{ route('tenant.transactions.index', array_merge(request()->except(['page','status']), ['status' => $key==='all' ? null : $key])) }}"
       class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold transition
           {{ ($cur === $key || ($cur==='all' && $key==='all')) ? $tabColors[$key].' text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
        {{ $label }}<span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $counts[$key] }}</span>
    </a>
    @endforeach
</div>

{{-- Invoice List --}}
@if($invoices->count() > 0)
<div class="space-y-4">
    @foreach($invoices as $invoice)
    @php
        $typeLabels = [
            'rent' => 'Sewa Hunian', 'electricity' => 'Listrik',
            'water' => 'Air', 'ipl' => 'IPL / Maintenance', 'deposit' => 'Deposit',
        ];
        $sc = match($invoice->status) {
            'unpaid'  => ['label'=>'Belum Dibayar','class'=>'bg-red-50 text-red-700'],
            'pending' => ['label'=>'Menunggu Konfirmasi','class'=>'bg-warning-50 text-warning-700'],
            'paid'    => ['label'=>'Lunas','class'=>'bg-success-50 text-success-700'],
            default   => ['label'=>ucfirst($invoice->status),'class'=>'bg-gray-100 text-gray-600'],
        };
    @endphp
    <div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-5 gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-reoda/10 shrink-0">
                    @if($invoice->type === 'rent')
                    <svg class="w-6 h-6 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    @elseif($invoice->type === 'electricity')
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    @elseif($invoice->type === 'water')
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    @else
                    <svg class="w-6 h-6 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <p class="font-bold text-black">{{ $invoice->invoice_number }}</p>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $sc['class'] }}">{{ $sc['label'] }}</span>
                    </div>
                    <p class="text-sm text-gray-500">
                        {{ $typeLabels[$invoice->type] ?? ucfirst($invoice->type) }}
                        — Periode {{ \Carbon\Carbon::create(null, $invoice->billing_month)->translatedFormat('F') }} {{ $invoice->billing_year }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $invoice->leaseContract->unit->unit_code ?? '-' }}
                        · {{ $invoice->leaseContract->unit->property->name ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">Jatuh tempo: {{ $invoice->due_date?->format('d M Y') }}</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <p class="text-xl font-extrabold text-reoda">Rp {{ number_format($invoice->amount,0,',','.') }}</p>
                <a href="{{ route('tenant.transactions.show', $invoice) }}"
                   class="mt-2 inline-flex items-center gap-1 rounded-lg {{ $invoice->status === 'unpaid' ? 'bg-reoda text-white hover:bg-reoda-dark' : 'border border-stroke text-gray-700 hover:bg-gray-50' }} px-4 py-1.5 text-sm font-semibold transition">
                    {{ $invoice->status === 'unpaid' ? 'Bayar Sekarang' : 'Lihat Detail' }}
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-5">{{ $invoices->links() }}</div>
@else
<div class="rounded-xl border border-stroke bg-white py-16 text-center shadow-sm">
    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    <h4 class="text-lg font-medium text-gray-900 mb-1">Tidak ada tagihan ditemukan</h4>
    <p class="text-sm text-gray-500">Coba ubah atau reset filter pencarian.</p>
</div>
@endif

@push('scripts')
<script>
function copyToClipboard(elementId, btn) {
    const text = document.getElementById(elementId).textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const original = btn.textContent;
        btn.textContent = 'Tersalin!';
        btn.classList.add('text-green-600', 'border-green-400');
        setTimeout(() => {
            btn.textContent = original;
            btn.classList.remove('text-green-600', 'border-green-400');
        }, 2000);
    });
}
</script>
@endpush

@endsection
