@extends('layouts.app')

@section('title', 'Detail Pembayaran - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Detail Pembayaran</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.payments.index') }}">Pembayaran /</a></li>
        <li class="font-medium text-reoda">Detail</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

@php
    $tenant = $payment->tenant ?? $payment->invoice->leaseContract->tenant;
    $unit   = $payment->invoice->leaseContract->unit;
    $prop   = $unit->property;
    $sc = match($payment->status) {
        'pending'  => ['label'=>'Menunggu Konfirmasi','class'=>'bg-warning-50 text-warning-700 border-warning-200'],
        'approved' => ['label'=>'Dikonfirmasi','class'=>'bg-success-50 text-success-700 border-success-200'],
        'rejected' => ['label'=>'Ditolak','class'=>'bg-error-50 text-error-700 border-error-200'],
        default    => ['label'=>ucfirst($payment->status),'class'=>'bg-gray-50 text-gray-700 border-gray-200'],
    };
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">

        {{-- Status Banner --}}
        <div class="flex items-center gap-3 rounded-2xl border px-6 py-5 {{ str_replace('border-', 'border-', $sc['class']) }}">
            <p class="font-extrabold text-lg">Status: {{ $sc['label'] }}</p>
            @if($payment->status === 'rejected' && $payment->rejection_reason)
            <p class="text-sm font-bold ml-2">— {{ $payment->rejection_reason }}</p>
            @elseif($payment->status === 'verified')
            <p class="text-sm font-bold ml-2">Dikonfirmasi {{ $payment->verified_at?->format('d M Y H:i') }}</p>
            @endif
        </div>

        {{-- Payment Details --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5 bg-gray-50/50"><h4 class="font-extrabold text-reoda-dark text-lg">Informasi Pembayaran</h4></div>
            <div class="p-6 grid grid-cols-2 gap-5 text-sm">
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Kode Pembayaran</p><p class="font-extrabold text-reoda-dark font-mono text-base">{{ $payment->payment_code }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">No. Invoice</p><p class="font-extrabold text-reoda-dark font-mono text-base">{{ $payment->invoice->invoice_number }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nominal</p><p class="text-xl font-extrabold text-reoda">Rp {{ number_format($payment->amount,0,',','.') }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Jenis Tagihan</p><p class="font-extrabold text-reoda-dark capitalize text-base">{{ $payment->invoice->type }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Metode</p><p class="font-extrabold text-reoda-dark capitalize text-base">{{ $payment->payment_method }}</p></div>
                <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Waktu Upload</p><p class="font-extrabold text-reoda-dark text-base">{{ $payment->paid_at?->format('d M Y, H:i') }}</p></div>
                @if($payment->bank_name)
                <div class="col-span-2 mt-2 pt-4 border-t border-gray-100 grid grid-cols-2 gap-5">
                    <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Bank Pengirim</p><p class="font-extrabold text-reoda-dark text-base">{{ $payment->bank_name }}</p></div>
                    <div><p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">No. Rekening</p><p class="font-extrabold text-reoda-dark text-base">{{ $payment->bank_account }}</p></div>
                </div>
                @endif
            </div>
        </div>

        {{-- Proof Image --}}
        @if($payment->proof_of_payment)
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5 bg-gray-50/50 flex items-center justify-between">
                <h4 class="font-extrabold text-reoda-dark text-lg">Bukti Transfer</h4>
                <a href="{{ asset('storage/'.$payment->proof_of_payment) }}" target="_blank" class="text-sm text-reoda font-bold hover:underline">Buka Full &rarr;</a>
            </div>
            <div class="p-6">
                <img src="{{ asset('storage/'.$payment->proof_of_payment) }}" alt="Bukti Bayar" class="max-h-96 w-full object-contain rounded-xl bg-gray-50 border border-gray-200 shadow-sm">
            </div>
        </div>
        @endif

        {{-- Approve / Reject Actions --}}
        @if($payment->status === 'pending')
            @if(strtolower($payment->payment_method) === 'midtrans')
            {{-- Midtrans: Auto-verified via webhook --}}
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <h4 class="font-bold text-blue-800">Pembayaran via Midtrans</h4>
                        <p class="text-sm text-blue-700 mt-1">Pembayaran ini diproses melalui Midtrans dan akan <strong>dikonfirmasi secara otomatis</strong> setelah penyewa menyelesaikan pembayaran. Anda tidak perlu melakukan tindakan apapun.</p>
                    </div>
                </div>
            </div>
            @else
            {{-- Manual Transfer: Need manager confirmation --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-4" x-data="{ rejectOpen: false }">
                <h4 class="font-extrabold text-reoda-dark mb-2 text-lg">Konfirmasi Pembayaran</h4>
                <form action="{{ route('manager.payments.approve', $payment) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Konfirmasi pembayaran ini?')"
                        class="w-full flex items-center justify-center gap-2 rounded-lg bg-success-600 py-3 font-bold text-white hover:bg-success-700 transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Konfirmasi Pembayaran
                    </button>
                </form>
                <button @click="rejectOpen = !rejectOpen" type="button"
                    class="w-full flex items-center justify-center gap-2 rounded-lg border border-error-300 py-3 font-bold text-error-600 hover:bg-error-50 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Tolak Pembayaran
                </button>
                <div x-show="rejectOpen" x-transition style="display:none">
                    <form action="{{ route('manager.payments.reject', $payment) }}" method="POST" class="space-y-4 pt-4 border-t border-gray-100 mt-4">
                        @csrf
                        <textarea name="rejection_reason" rows="3" required placeholder="Alasan penolakan..." class="w-full rounded-lg border border-gray-300 py-3 px-4 text-sm font-medium outline-none focus:border-error-500 focus:ring-1 focus:ring-error-500 transition"></textarea>
                        <button type="submit" class="w-full rounded-lg bg-error-600 py-3 font-bold text-white hover:bg-error-700 transition shadow-sm">Kirim Penolakan</button>
                    </form>
                </div>
            </div>
            @endif
        @endif
    </div>

    {{-- Right side --}}
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
            <h4 class="font-extrabold text-reoda-dark mb-5 text-lg">Data Penyewa</h4>
            <div class="flex items-center gap-4 mb-5">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($tenant->name) }}&background=4C74AF&color=fff&size=48" class="h-14 w-14 rounded-full ring-2 ring-reoda-lightest">
                <div><p class="font-extrabold text-reoda-dark text-lg leading-tight">{{ $tenant->name }}</p><p class="text-xs font-medium text-gray-500 mt-0.5">{{ $tenant->email }}</p></div>
            </div>
            <a href="{{ route('manager.tenants.show', $tenant) }}" class="inline-flex items-center justify-center w-full rounded-lg bg-gray-100 py-2.5 px-4 text-sm font-bold text-reoda-dark hover:bg-gray-200 transition">Lihat Profil Penyewa</a>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
            <h4 class="font-extrabold text-reoda-dark mb-5 text-lg">Info Unit</h4>
            <div class="space-y-4 text-sm">
                <div><span class="text-xs font-bold text-gray-500 uppercase block mb-1">Properti</span><span class="font-extrabold text-reoda-dark text-base">{{ $prop->name }}</span></div>
                <div><span class="text-xs font-bold text-gray-500 uppercase block mb-1">Unit</span><span class="font-extrabold text-reoda-dark text-base">{{ $unit->unit_code }}</span></div>
                <div><span class="text-xs font-bold text-gray-500 uppercase block mb-1">Periode</span><span class="font-extrabold text-reoda-dark text-base">{{ $payment->invoice->billing_month }}/{{ $payment->invoice->billing_year }}</span></div>
                <div class="border-t border-gray-100 pt-4 mt-2">
                    <span class="text-xs font-bold text-gray-500 uppercase block mb-1">Total Tagihan</span>
                    <span class="font-extrabold text-reoda text-xl">Rp {{ number_format($payment->invoice->amount,0,',','.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
