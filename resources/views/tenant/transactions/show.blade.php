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
@if(session('info'))
<div class="mb-5 rounded-md border-l-4 border-blue-400 bg-blue-50 px-5 py-3 text-sm font-medium text-blue-700">{{ session('info') }}</div>
@endif

@php
    $unit    = $invoice->leaseContract->unit;
    $prop    = $unit->property;
    $manager = $prop->manager;

    $typeLabels = [
        'rent'        => 'Sewa Hunian',
        'electricity' => 'Tagihan Listrik',
        'water'       => 'Tagihan Air',
        'ipl'         => 'IPL / Maintenance Fee',
        'deposit'     => 'Deposit / Uang Jaminan',
    ];

    $sc = match($invoice->status) {
        'unpaid'  => ['label'=>'Belum Dibayar',               'class'=>'bg-error-50 text-error-700 border-error-200'],
        'pending' => ['label'=>'Menunggu Konfirmasi Pembayaran','class'=>'bg-warning-50 text-warning-700 border-warning-200'],
        'paid'    => ['label'=>'Lunas',                        'class'=>'bg-success-50 text-success-700 border-success-200'],
        'overdue' => ['label'=>'Jatuh Tempo',                  'class'=>'bg-error-50 text-error-800 border-error-300'],
        default   => ['label'=>ucfirst($invoice->status),      'class'=>'bg-gray-50 text-gray-700 border-gray-200'],
    };

    $latestPayment = $invoice->payments->first();
    $totalBayar = $invoice->amount + ($platformFee ?? 0) + ($gatewayFee ?? 0) - ($discountAmount ?? 0);
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
                <div><p class="text-xs text-gray-400 mb-0.5">Jenis</p><p class="font-semibold">{{ $typeLabels[$invoice->type] ?? ucfirst($invoice->type) }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Periode</p><p class="font-semibold">{{ $invoice->billing_month }}/{{ $invoice->billing_year }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Jatuh Tempo</p><p class="font-semibold {{ $invoice->due_date && $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-error-600' : '' }}">{{ $invoice->due_date?->format('d M Y') }}</p></div>

                @if($invoice->meter_start && $invoice->meter_end)
                <div class="col-span-2 grid grid-cols-3 gap-3 pt-2 border-t border-stroke">
                    <div><p class="text-xs text-gray-400 mb-0.5">Meter Awal</p><p class="font-semibold">{{ number_format($invoice->meter_start, 0) }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-0.5">Meter Akhir</p><p class="font-semibold">{{ number_format($invoice->meter_end, 0) }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-0.5">Pemakaian</p><p class="font-semibold text-reoda">{{ number_format($invoice->meter_end - $invoice->meter_start, 0) }} unit</p></div>
                </div>
                @endif

                @if($invoice->notes)
                <div class="col-span-2 pt-2 border-t border-stroke">
                    <p class="text-xs text-gray-400 mb-0.5">Catatan</p>
                    <p class="font-medium text-gray-600">{{ $invoice->notes }}</p>
                </div>
                @endif

                {{-- Total + Pay Button --}}
                <div class="col-span-2 pt-3 border-t border-stroke">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Rincian Pembayaran</p>
                            <div class="space-y-0.5 text-sm">
                                <div class="flex justify-between gap-8"><span class="text-gray-500">Tagihan pokok</span><span class="font-medium">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span></div>
                                @if(isset($platformFee) && $platformFee > 0)
                                <div class="flex justify-between gap-8"><span class="text-gray-500">Biaya admin REODA</span><span class="font-medium">Rp {{ number_format($platformFee, 0, ',', '.') }}</span></div>
                                @endif
                                @if(isset($gatewayFee) && $gatewayFee > 0)
                                <div class="flex justify-between gap-8"><span class="text-gray-500">Biaya payment gateway</span><span class="font-medium">Rp {{ number_format($gatewayFee, 0, ',', '.') }}</span></div>
                                @endif
                                @if(isset($discountAmount) && $discountAmount > 0)
                                <div class="flex justify-between gap-8"><span class="text-success-600">Diskon Referral</span><span class="font-medium text-success-600">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span></div>
                                @endif
                                <div class="flex justify-between gap-8 pt-1 border-t mt-1"><span class="font-bold text-black">Total</span><span class="font-extrabold text-xl text-reoda">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span></div>
                            </div>
                        </div>
                        @if(isset($snapToken) && in_array($invoice->status, ['unpaid', 'pending']))
                        <button id="pay-button" onclick="openSnapEmbed()" class="shrink-0 rounded-xl bg-reoda px-8 py-3 font-bold text-white hover:bg-reoda-dark transition shadow-md flex items-center gap-2 text-base">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Bayar Sekarang
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending state info --}}
        @if($invoice->status === 'pending')
        <div class="rounded-xl border border-warning-200 bg-warning-50 px-6 py-4 text-sm text-warning-700">
            <p class="font-bold mb-1">⏳ Menunggu Konfirmasi Pembayaran</p>
            <p>Pembayaran Anda sedang diproses. Sistem akan otomatis mengkonfirmasi setelah pembayaran berhasil diverifikasi oleh Midtrans.</p>
        </div>
        @endif

        {{-- Paid state --}}
        @if($invoice->status === 'paid')
        <div class="rounded-xl border border-success-200 bg-success-50 px-6 py-4 text-sm text-success-700">
            <p class="font-bold mb-1">✅ Pembayaran Berhasil!</p>
            <p>Tagihan ini telah lunas. Terima kasih atas pembayaran Anda.</p>
        </div>
        @endif

    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-5">
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black mb-4">Info Unit</h4>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Properti</span><span class="font-semibold text-right">{{ $prop->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Unit</span><span class="font-semibold">{{ $unit->unit_code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tipe</span><span class="font-semibold capitalize">{{ $unit->type }}</span></div>
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

@if(isset($snapToken) && in_array($invoice->status, ['unpaid', 'pending']))
{{-- Embedded Midtrans Snap --}}
<div id="snap-embed-container" class="mt-5 hidden">
    <div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stroke flex items-center justify-between">
            <h4 class="font-bold text-black">Pilih Metode Pembayaran</h4>
            <button onclick="closeSnapEmbed()" class="text-sm text-gray-400 hover:text-gray-600">✕ Tutup</button>
        </div>
        <div id="snap-container" class="p-4"></div>
    </div>
</div>

<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    function openSnapEmbed() {
        const container = document.getElementById('snap-embed-container');
        const btn = document.getElementById('pay-button');
        container.classList.remove('hidden');
        if (btn) btn.classList.add('hidden');
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });

        snap.embed('{{ $snapToken }}', {
            embedId: 'snap-container',
            onSuccess: function(result) {
                console.log('Payment success', result);
                document.getElementById('snap-container').innerHTML = '<div class="p-8 text-center"><div class="text-success-500 mb-4"><svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><h3 class="text-xl font-bold mb-2">Pembayaran Berhasil!</h3><p class="text-gray-500">Mengarahkan kembali ke daftar tagihan...</p></div>';
                setTimeout(() => window.location.href = "{{ route('tenant.transactions.index') }}", 2000);
            },
            onPending: function(result) {
                console.log('Payment pending', result);
                document.getElementById('snap-container').innerHTML = '<div class="p-8 text-center"><div class="text-yellow-500 mb-4"><svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><h3 class="text-xl font-bold mb-2">Menunggu Pembayaran</h3><p class="text-gray-500">Mengarahkan kembali ke daftar tagihan...</p></div>';
                setTimeout(() => window.location.href = "{{ route('tenant.transactions.index') }}", 2000);
            },
            onError: function(result) {
                console.log('Payment error', result);
                closeSnapEmbed();
                alert('Pembayaran gagal. Silakan coba lagi.');
            },
            onClose: function() {
                closeSnapEmbed();
            }
        });
    }

    function closeSnapEmbed() {
        document.getElementById('snap-embed-container').classList.add('hidden');
        const btn = document.getElementById('pay-button');
        if (btn) btn.classList.remove('hidden');
        // Reload to get fresh snap token
        window.location.reload();
    }
</script>
@endif
@endsection
