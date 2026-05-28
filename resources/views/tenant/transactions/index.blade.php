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

{{-- Status Tabs --}}
<div class="mb-5 flex flex-wrap gap-2">
    @php
        $tabs = ['all'=>'Semua','unpaid'=>'Belum Dibayar','pending'=>'Menunggu','paid'=>'Lunas'];
        $cur  = request('status','all');
        $tabColors = ['all'=>'bg-gray-700','unpaid'=>'bg-error-600','pending'=>'bg-warning-500','paid'=>'bg-success-600'];
    @endphp
    @foreach($tabs as $key => $label)
    <a href="{{ route('tenant.transactions.index', array_merge(request()->except('page'), ['status' => $key==='all' ? null : $key])) }}"
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
        $sc = match($invoice->status) {
            'unpaid'  => ['label'=>'Belum Dibayar','class'=>'bg-error-50 text-error-700'],
            'pending' => ['label'=>'Menunggu Konfirmasi','class'=>'bg-warning-50 text-warning-700'],
            'paid'    => ['label'=>'Lunas','class'=>'bg-success-50 text-success-700'],
            default   => ['label'=>ucfirst($invoice->status),'class'=>'bg-gray-100 text-gray-600'],
        };
    @endphp
    <div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-5 gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-reoda/10 shrink-0">
                    <svg class="w-6 h-6 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($invoice->type === 'rent')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        @endif
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <p class="font-bold text-black">{{ $invoice->invoice_number }}</p>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $sc['class'] }}">{{ $sc['label'] }}</span>
                    </div>
                    <p class="text-sm text-gray-500 capitalize">
                        {{ $invoice->type === 'rent' ? 'Sewa Hunian' : ucfirst($invoice->type) }}
                        — Periode {{ $invoice->billing_month }}/{{ $invoice->billing_year }}
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
    <h4 class="text-lg font-medium text-gray-900 mb-1">Belum ada tagihan</h4>
    <p class="text-sm text-gray-500">Tagihan akan muncul setelah kontrak sewa aktif.</p>
</div>
@endif
@endsection
