@extends('layouts.app')

@section('title', 'Aset Saya - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">
        Informasi Aset Sewa
    </h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium text-reoda" href="#">Dashboard</a></li>
        </ol>
    </nav>
</div>

@if(!$activeContract)

{{-- Awaiting Approval Notice --}}
@if($awaitingContract)
<div class="rounded-xl border border-yellow-200 bg-yellow-50 p-5 mb-5 flex items-start gap-4">
    <svg class="w-8 h-8 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <h4 class="font-bold text-yellow-800">Menunggu Persetujuan Pengelola</h4>
        <p class="text-sm text-yellow-700 mt-1">
            Pengajuan kontrak Anda untuk unit <strong>{{ $awaitingContract->unit->name }}</strong> di
            <strong>{{ $awaitingContract->unit->property->name }}</strong> sedang ditinjau.
            Anda akan mendapat notifikasi setelah disetujui.
        </p>
    </div>
</div>
@endif

<div class="rounded-2xl border border-stroke bg-white shadow-sm overflow-hidden">
    {{-- Hero Empty State --}}
    <div class="bg-linear-to-br from-reoda to-reoda-dark px-8 py-10 text-white text-center">
        <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 mb-5">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <h3 class="text-2xl font-extrabold mb-2">Selamat Datang di REODA!</h3>
        <p class="text-white/80 text-sm max-w-md mx-auto">Anda belum memiliki kontrak sewa aktif. Mulai dengan scan kode QR hunian atau cari di Explore Market.</p>
    </div>

    {{-- CTA Buttons --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-stroke">
        {{-- Scan QR Code --}}
        <div class="p-8 text-center hover:bg-gray-50 transition cursor-pointer group" onclick="openQrScanner()">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-reoda/10 group-hover:bg-reoda/20 transition mb-4">
                <svg class="w-8 h-8 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 6h4V4m0 4h4m-4 0v4m12-12h-4v4h4V4z"/>
                </svg>
            </div>
            <h4 class="font-bold text-black text-base mb-1">📷 Scan Kode QR Hunian</h4>
            <p class="text-sm text-gray-500">Scan barcode/QR Code yang ada di lokasi hunian Anda untuk langsung melihat detail dan mengajukan kontrak.</p>
        </div>
        {{-- Explore Market --}}
        <a href="{{ route('tenant.explore.index') }}" class="p-8 text-center hover:bg-gray-50 transition group block">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-green-100 group-hover:bg-green-200 transition mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h4 class="font-bold text-black text-base mb-1">🔍 Cari di Explore Market</h4>
            <p class="text-sm text-gray-500">Jelajahi seluruh properti yang tersedia, bandingkan harga, fasilitas, dan lokasi untuk temukan hunian terbaik.</p>
        </a>
    </div>
</div>

{{-- QR Scanner Modal --}}
<div id="qr-scanner-modal" class="fixed inset-0 z-50 bg-black/60 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm mx-4">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-black">Masukkan Kode Hunian</h4>
            <button onclick="closeQrScanner()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <p class="text-sm text-gray-500 mb-4">Masukkan kode properti yang tertera di bawah QR Code, atau scan QR Code menggunakan kamera perangkat Anda.</p>
        <input type="text" id="property-code-input" placeholder="Contoh: REODA-001"
            class="w-full rounded-lg border border-stroke py-3 px-4 text-sm font-mono uppercase outline-none focus:border-reoda transition mb-3">
        <button onclick="goToProperty()"
            class="w-full rounded-lg bg-reoda py-3 font-semibold text-white hover:bg-reoda-dark transition">
            Lihat Hunian
        </button>
    </div>
</div>
@else

<!-- Info Banner -->
<div class="mb-6 flex w-full items-center justify-between rounded-lg bg-reoda-lightest p-4 sm:p-6 shadow-sm border border-reoda-lighter">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-reoda">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-black">Unit {{ $activeContract->unit->unit_code }} - {{ $activeContract->unit->property->name }}</h3>
            <p class="text-sm font-medium text-gray-500">Masa sewa aktif hingga {{ $activeContract->end_date->format('d F Y') }}</p>
        </div>
    </div>
    <div class="hidden sm:block">
        <a href="{{ route('tenant.contract.show') }}" class="rounded-md bg-reoda px-6 py-2 font-medium text-white hover:bg-reoda-dark transition">
            Lihat Kontrak
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-3 2xl:gap-7.5">
    <!-- QR Code Card -->
    <div class="rounded-sm border border-stroke bg-white shadow-default">
        <div class="border-b border-stroke py-4 px-6.5">
            <h3 class="font-bold text-black text-lg">
                ID Penyewa Saya
            </h3>
        </div>
        <div class="p-6.5 text-center">
            <p class="text-sm text-gray-500 mb-4">Tunjukkan kode ini kepada pengelola saat pembuatan kontrak.</p>
            <div class="inline-block p-4 bg-white border border-gray-200 rounded-xl shadow-sm mb-4">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate(auth()->user()->user_code ?? 'NO-CODE') !!}
            </div>
            <h4 class="text-xl font-black text-reoda tracking-widest">{{ auth()->user()->user_code }}</h4>
        </div>
    </div>

    <!-- Tagihan Card -->
    <div class="rounded-sm border border-stroke bg-white shadow-default">
        <div class="border-b border-stroke py-4 px-6.5">
            <h3 class="font-bold text-black text-lg">
                Tagihan Bulan Ini
            </h3>
        </div>
        <div class="flex flex-col gap-5 p-6.5">
            @if($pendingInvoice)
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-black capitalize">{{ str_replace('_', ' ', $pendingInvoice->type) }} ({{ date('F', mktime(0, 0, 0, $pendingInvoice->billing_month, 1)) }})</h4>
                        <p class="text-sm text-gray-500">Jatuh tempo: {{ $pendingInvoice->due_date->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-black">Rp {{ number_format($pendingInvoice->amount, 0, ',', '.') }}</p>
                        @if($pendingInvoice->status === 'unpaid')
                            <span class="inline-block rounded bg-danger px-2.5 py-0.5 text-xs font-medium text-white">Belum Dibayar</span>
                        @else
                            <span class="inline-block rounded bg-warning px-2.5 py-0.5 text-xs font-medium text-white">Menunggu Konfirmasi</span>
                        @endif
                    </div>
                </div>

                @if($pendingInvoice->status === 'unpaid')
                <div class="mt-4">
                    <a href="{{ route('tenant.transactions.show', $pendingInvoice) }}" class="flex w-full justify-center rounded bg-reoda p-3 font-medium text-white hover:bg-reoda-dark transition">
                        Bayar Sekarang (Rp {{ number_format($pendingInvoice->amount, 0, ',', '.') }})
                    </a>
                </div>
                @else
                <div class="mt-4">
                    <a href="{{ route('tenant.transactions.show', $pendingInvoice) }}" class="flex w-full justify-center rounded border border-reoda p-3 font-medium text-reoda hover:bg-reoda/10 transition">
                        Lihat Status Pembayaran
                    </a>
                </div>
                @endif
            @else
                <div class="py-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-success-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-black font-semibold">Semua Tagihan Lunas</p>
                    <p class="text-sm text-gray-500 mt-1">Anda tidak memiliki tagihan tertunggak saat ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Pengelola Card -->
    <div class="rounded-sm border border-stroke bg-white shadow-default">
        <div class="border-b border-stroke py-4 px-6.5">
            <h3 class="font-bold text-black text-lg">
                Kontak Pengelola
            </h3>
        </div>
        <div class="p-6.5">
            <div class="mb-4 flex items-center gap-4">
                <div class="h-16 w-16 overflow-hidden rounded-full">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($activeContract->unit->property->manager->name ?? 'P') }}&background=4C74AF&color=fff&size=64" alt="Pengelola">
                </div>
                <div>
                    <h4 class="font-semibold text-black text-lg">{{ $activeContract->unit->property->manager->name ?? 'Pengelola' }}</h4>
                    <p class="text-sm text-gray-500">Pengelola {{ $activeContract->unit->property->name }}</p>
                </div>
            </div>
            <div class="mt-6 flex flex-col gap-3">
                @if(isset($activeContract->unit->property->manager->phone) && $activeContract->unit->property->manager->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activeContract->unit->property->manager->phone) }}" target="_blank" class="flex items-center justify-center gap-2 rounded border border-reoda px-4 py-2 font-medium text-reoda hover:bg-reoda hover:text-white transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    Chat via WhatsApp
                </a>
                @endif
                <a href="{{ route('tenant.services.index') }}" class="flex items-center justify-center gap-2 rounded bg-gray-100 px-4 py-2 font-medium text-gray-700 hover:bg-gray-200 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Lapor Kendala / Layanan
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openQrScanner() {
    const modal = document.getElementById('qr-scanner-modal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
}
function closeQrScanner() {
    const modal = document.getElementById('qr-scanner-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
}
function goToProperty() {
    const code = document.getElementById('property-code-input').value.trim().toUpperCase();
    if (!code) { alert('Masukkan kode hunian terlebih dahulu.'); return; }
    window.location.href = '/property/' + code;
}
</script>
@endpush
