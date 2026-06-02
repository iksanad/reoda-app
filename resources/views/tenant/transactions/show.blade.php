@extends('layouts.app')

@section('title', 'Detail Tagihan - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Detail Tagihan</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.transactions.index') }}">Transaksi /</a></li>
        <li class="font-medium text-reoda">Detail</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-5 rounded-md border-l-4 border-error-500 bg-error-50 px-5 py-3 text-sm font-medium text-error-700">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="mb-5 rounded-md border-l-4 border-error-400 bg-error-50 px-5 py-3 text-sm text-error-700">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
    $unit = $invoice->leaseContract->unit;
    $prop = $unit->property;
    $manager = $prop->manager;
    $sc = match($invoice->status) {
        'unpaid'  => ['label'=>'Belum Dibayar','class'=>'bg-error-50 text-error-700 border-error-200'],
        'pending' => ['label'=>'Menunggu Konfirmasi Pengelola','class'=>'bg-warning-50 text-warning-700 border-warning-200'],
        'paid'    => ['label'=>'Lunas','class'=>'bg-success-50 text-success-700 border-success-200'],
        default   => ['label'=>ucfirst($invoice->status),'class'=>'bg-gray-50 text-gray-700 border-gray-200'],
    };
    $latestPayment = $invoice->payments->first();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">

        {{-- Status Banner --}}
        <div class="flex items-center gap-3 rounded-xl border px-5 py-3.5 {{ $sc['class'] }}">
            <p class="font-bold text-sm">Status: {{ $sc['label'] }}</p>
        </div>

        {{-- Invoice Detail --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Detail Tagihan</h4></div>
            <div class="p-6 grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-xs text-gray-400 mb-0.5">No. Invoice</p><p class="font-mono font-semibold">{{ $invoice->invoice_number }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Jenis</p><p class="font-semibold capitalize">{{ $invoice->type === 'rent' ? 'Sewa Hunian' : ucfirst($invoice->type) }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Periode</p><p class="font-semibold">{{ $invoice->billing_month }}/{{ $invoice->billing_year }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Jatuh Tempo</p><p class="font-semibold {{ $invoice->due_date && $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-error-600' : '' }}">{{ $invoice->due_date?->format('d M Y') }}</p></div>
                @if(isset($discountAmount) && $discountAmount > 0)
                <div class="col-span-2 pt-2 border-t border-stroke flex justify-between items-end">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Voucher Diskon Referral</p>
                        <p class="text-lg font-bold text-success-500">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endif
                <div class="col-span-2 pt-2 border-t border-stroke flex justify-between items-end">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Total Tagihan (termasuk biaya admin)</p>
                        <p class="text-2xl font-extrabold text-reoda">Rp {{ number_format($invoice->amount + 14000 - ($discountAmount ?? 0), 0, ',', '.') }}</p>
                    </div>
                    @if(isset($snapToken) && $invoice->status === 'unpaid')
                        <button id="pay-button" class="rounded-lg bg-reoda px-6 py-2.5 font-bold text-white hover:bg-reoda-dark transition shadow-md flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Bayar Otomatis
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Latest Payment Proof if exists --}}
        @if($latestPayment && $latestPayment->proof_of_payment)
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4 flex items-center justify-between">
                <h4 class="font-bold text-black">Bukti Pembayaran Terakhir</h4>
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                    {{ $latestPayment->status === 'verified' ? 'bg-success-50 text-success-700' : ($latestPayment->status === 'rejected' ? 'bg-error-50 text-error-700' : 'bg-warning-50 text-warning-700') }}">
                    {{ ucfirst($latestPayment->status) }}
                </span>
            </div>
            <div class="p-4">
                <img src="{{ asset('storage/'.$latestPayment->proof_of_payment) }}" alt="Bukti" class="max-h-64 w-full object-contain rounded-lg bg-gray-50 border border-stroke">
            </div>
            @if($latestPayment->status === 'rejected')
            <div class="border-t border-stroke px-6 py-3 bg-error-50 text-sm text-error-700">
                <span class="font-semibold">Alasan Penolakan:</span> {{ $latestPayment->rejection_reason }}
            </div>
            @endif
        </div>
        @endif

        {{-- Upload Payment Form --}}
        @if($invoice->status === 'unpaid' || $invoice->status === 'rejected' || ($latestPayment && $latestPayment->status === 'rejected'))
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Upload Bukti Pembayaran</h4></div>
            <form action="{{ route('tenant.transactions.pay', $invoice) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Metode Pembayaran <span class="text-error-500">*</span></label>
                        <select name="payment_method" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition" x-data x-model="method">
                            <option value="">Pilih Metode</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Bayar Tunai</option>
                        </select>
                    </div>
                </div>

                <div x-data="{ method: '' }">
                    <select name="payment_method" required class="hidden" x-ref="sel"></select>
                    {{-- Bank info fields for transfer --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Bank Pengirim</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="Contoh: BCA, BRI, BNI..." class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                        </div>
                        <div class="flex-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">No. Rekening Pengirim</label>
                            <input type="text" name="bank_account" value="{{ old('bank_account') }}" placeholder="No. Rek / Nama akun" class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Foto Bukti Transfer <span class="text-error-500">*</span></label>
                    <div class="flex items-center justify-center w-full">
                        <label for="proof_of_payment" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-reoda-lighter rounded-xl cursor-pointer bg-reoda-lightest/50 hover:bg-reoda-lightest transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 text-reoda mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="text-sm font-medium text-gray-600">Klik untuk upload atau drag & drop</p>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — Maks 5MB</p>
                            </div>
                            <input id="proof_of_payment" name="proof_of_payment" type="file" accept="image/*" class="hidden" required>
                        </label>
                    </div>
                </div>

                {{-- Rekening Tujuan Transfer --}}
                <div class="rounded-lg bg-reoda-lightest border border-reoda-lighter p-4 text-sm">
                    <p class="font-bold text-reoda-dark mb-1">Rekening Tujuan Transfer</p>
                    <p class="text-gray-600">Bank: <span class="font-semibold text-black">{{ $manager->bank_name ?? 'Hubungi pengelola' }}</span></p>
                    <p class="text-gray-600">No. Rek: <span class="font-semibold text-black">{{ $manager->bank_account_number ?? '-' }}</span></p>
                    <p class="text-gray-600">Atas Nama: <span class="font-semibold text-black">{{ $manager->bank_account_name ?? $manager->name }}</span></p>
                    <p class="font-bold text-reoda mt-2">Jumlah: Rp {{ number_format($invoice->amount,0,',','.') }}</p>
                </div>

                <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-lg bg-reoda py-3 font-bold text-white hover:bg-reoda-dark transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    Kirim Bukti Pembayaran
                </button>
            </form>
        </div>
        @endif

    </div>

    {{-- Right: Unit Info + Manager --}}
    <div class="space-y-5">
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Info Unit</h4>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Properti</span><span class="font-semibold text-right">{{ $prop->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Unit</span><span class="font-semibold">{{ $unit->unit_code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tipe</span><span class="font-semibold">{{ $unit->type }}</span></div>
                <div class="flex justify-between border-t border-stroke pt-2 mt-1">
                    <span class="text-gray-500">Harga Sewa</span>
                    <span class="font-bold text-reoda">Rp {{ number_format($unit->rent_price,0,',','.') }}/bln</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Pengelola</h4>
            <div class="flex items-center gap-3 mb-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($manager->name) }}&background=4C74AF&color=fff&size=48" class="h-12 w-12 rounded-full">
                <div><p class="font-bold text-black">{{ $manager->name }}</p><p class="text-xs text-gray-400">{{ $manager->email }}</p></div>
            </div>
            @if($manager->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $manager->phone) }}" target="_blank" class="flex items-center justify-center gap-2 rounded-lg bg-green-500 py-2 text-sm font-medium text-white hover:bg-green-600 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat WhatsApp
            </a>
            @endif
        </div>
    </div>
</div>

@if(isset($snapToken) && $invoice->status === 'unpaid')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button').onclick = function(){
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                alert("Pembayaran berhasil!");
                window.location.reload();
            },
            onPending: function(result){
                alert("Menunggu pembayaran Anda!");
            },
            onError: function(result){
                alert("Pembayaran gagal!");
            },
            onClose: function(){
                console.log('Customer closed the popup without finishing the payment');
            }
        });
    };
</script>
@endif
@endsection
