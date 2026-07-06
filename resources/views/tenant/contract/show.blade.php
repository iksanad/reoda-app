@extends('layouts.app')

@section('title', 'Detail Kontrak Saya - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Detail Kontrak Sewa</h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.dashboard') }}">Dashboard /</a></li>
            <li class="font-medium text-reoda">Kontrak</li>
        </ol>
    </nav>
</div>
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Left: Detail Information -->
    <div class="xl:col-span-2 space-y-6">
        <div class="rounded-sm border border-stroke bg-white shadow-default p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-stroke">
                <div>
                    <h3 class="font-bold text-black text-xl">{{ $contract->unit->property->name }} - Unit {{ $contract->unit->unit_code }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Nomor Kontrak: {{ $contract->contract_number }}</p>
                </div>
                <div>
                    @if($contract->status == 'active')
                        <span class="inline-flex rounded-full bg-success-50 px-3 py-1 text-sm font-medium text-success-700 border border-success-200">Aktif</span>
                    @elseif($contract->status == 'pending')
                        <span class="inline-flex rounded-full bg-warning-50 px-3 py-1 text-sm font-medium text-warning-700 border border-warning-200">Menunggu Persetujuan</span>
                    @elseif($contract->status == 'expired')
                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 border border-gray-300">Berakhir</span>
                    @else
                        <span class="inline-flex rounded-full bg-error-50 px-3 py-1 text-sm font-medium text-error-700 border border-error-200">Dibatalkan</span>
                    @endif
                </div>
            </div>

            @if($contract->status == 'pending')
            <div class="mb-6 rounded-xl border border-warning-200 bg-warning-50 p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-warning-100 text-warning-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-base font-semibold text-warning-800">Menunggu Persetujuan Anda</h4>
                        <p class="mt-1 text-sm text-warning-700">
                            Pengelola telah membuat kontrak ini. Harap tinjau detail periode dan biaya sewa. Jika sudah sesuai, Anda dapat menyetujui kontrak ini untuk mengaktifkannya.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <form action="{{ route('tenant.contract.approve', $contract->id) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui kontrak ini?')" class="inline-flex items-center gap-2 rounded-lg bg-reoda px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-reoda-dark">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Setujui Kontrak
                                </button>
                            </form>
                            <form action="{{ route('tenant.contract.reject', $contract->id) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak kontrak ini?')" class="inline-flex items-center gap-2 rounded-lg border border-error-200 bg-white px-5 py-2.5 text-sm font-semibold text-error-600 shadow-sm transition-all hover:bg-error-50 hover:text-error-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak Kontrak
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Info Waktu -->
                <div>
                    <h4 class="font-semibold text-black mb-3">Periode Sewa</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b border-stroke pb-2">
                            <span class="text-sm text-gray-500">Tanggal Mulai</span>
                            <span class="text-sm font-medium text-black">{{ $contract->start_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-stroke pb-2">
                            <span class="text-sm text-gray-500">Tanggal Berakhir</span>
                            <span class="text-sm font-medium text-black">{{ $contract->end_date ? $contract->end_date->format('d M Y') : 'Tanpa Batas' }}</span>
                        </div>
                        <div class="flex justify-between pb-2">
                            <span class="text-sm text-gray-500">Jenis Pembayaran</span>
                            <span class="text-sm font-medium text-black capitalize">{{ str_replace('_', ' ', $contract->rental_type) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Info Biaya -->
                <div>
                    <h4 class="font-semibold text-black mb-3">Rincian Biaya</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b border-stroke pb-2">
                            <span class="text-sm text-gray-500">Harga Sewa</span>
                            <span class="text-sm font-medium text-black">Rp {{ number_format($contract->rent_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-stroke pb-2">
                            <span class="text-sm text-gray-500">Uang Deposit</span>
                            <span class="text-sm font-medium text-black">Rp {{ number_format($contract->deposit_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($contract->notes)
            <div class="mt-6 pt-6 border-t border-stroke">
                <h4 class="font-semibold text-black mb-2">Catatan Kontrak</h4>
                <div class="rounded-md bg-gray-50 p-4 text-sm text-gray-700">
                    {!! nl2br(e($contract->notes)) !!}
                </div>
            </div>
            @endif
        </div>

        @if($contract->unit->facilities->count() > 0)
        <div class="rounded-sm border border-stroke bg-white shadow-default p-6">
            <h4 class="font-bold text-black text-lg mb-4">Fasilitas Unit</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($contract->unit->facilities as $facility)
                <span class="inline-flex rounded-full border border-stroke bg-gray-50 px-3 py-1 text-sm text-gray-600">
                    {{ $facility->name }}
                </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Right: Actions & Contacts -->
    <div class="space-y-6">
        <!-- Dokumen Kontrak -->
        <div class="rounded-sm border border-stroke bg-white shadow-default p-6">
            <h4 class="font-bold text-black text-lg mb-4">Dokumen Kontrak</h4>
            @if($contract->contract_document)
            <a href="{{ asset('storage/' . $contract->contract_document) }}" target="_blank" class="flex w-full items-center justify-center gap-2 rounded-md bg-reoda py-2.5 text-sm font-medium text-white hover:bg-reoda-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Unduh PDF Kontrak
            </a>
            @else
            <div class="rounded-lg bg-gray-50 border border-stroke p-4 text-sm text-gray-500 text-center">
                Dokumen kontrak belum diunggah oleh pengelola.
            </div>
            @endif
        </div>

        <!-- Pengelola -->
        <div class="rounded-sm border border-stroke bg-white shadow-default p-6">
            <h4 class="font-bold text-black text-lg mb-4">Pengelola Properti</h4>
            <div class="flex items-center gap-3 mb-5">
                <div class="h-12 w-12 rounded-full overflow-hidden ring-2 ring-reoda/20">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($contract->unit->property->manager->name ?? 'P') }}&background=4C74AF&color=fff&size=48" alt="Pengelola">
                </div>
                <div>
                    <p class="font-semibold text-black">{{ $contract->unit->property->manager->name ?? 'Pengelola' }}</p>
                    <p class="text-xs text-gray-500">Pemilik / Pengurus</p>
                </div>
            </div>
            @if(isset($contract->unit->property->manager->phone) && $contract->unit->property->manager->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contract->unit->property->manager->phone) }}" target="_blank" class="flex w-full items-center justify-center gap-2 rounded-md bg-green-500 py-2.5 text-sm font-medium text-white hover:bg-green-600 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat via WhatsApp
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
