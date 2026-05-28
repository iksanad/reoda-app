@extends('layouts.app')
@section('title', 'Buat Kontrak Sewa - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Buat Kontrak Sewa</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.contracts.index') }}">Kontrak /</a></li>
        <li class="font-medium text-reoda">Buat</li>
    </ol></nav>
</div>

<div class="rounded-xl border border-stroke bg-white shadow-sm">
    <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Form Data Kontrak</h4></div>
    <form action="{{ route('manager.contracts.store') }}" method="POST" class="p-6 space-y-5">
        @csrf
        @if($errors->any())
        <div class="rounded-md bg-error-50 border border-error-200 p-4 text-sm text-error-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Penyewa & Unit --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div x-data="qrScanner()">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">ID / Kode Penyewa <span class="text-error-500">*</span></label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" x-model="tenantCode" name="tenant_code" required placeholder="Contoh: T-123456" class="w-full rounded-lg border border-stroke py-3 px-4 pl-10 text-sm outline-none focus:border-reoda transition uppercase">
                        <span class="absolute left-3.5 top-3.5 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </span>
                    </div>
                    <button type="button" @click="startScanner" class="inline-flex items-center justify-center rounded-lg bg-gray-800 px-4 py-3 text-sm font-medium text-white hover:bg-gray-700 transition">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Scan
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1.5">Ketik ID penyewa atau scan dari layar HP mereka.</p>

                <!-- Scanner Modal -->
                <div x-show="showScanner" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div @click.outside="stopScanner" class="w-full max-w-md rounded-2xl bg-white shadow-xl overflow-hidden">
                        <div class="flex items-center justify-between border-b border-stroke px-6 py-4">
                            <h3 class="font-bold text-black">Scan QR Code Penyewa</h3>
                            <button type="button" @click="stopScanner" class="text-gray-400 hover:text-black transition">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <div id="qr-reader" class="w-full overflow-hidden rounded-lg border border-stroke bg-gray-50"></div>
                            <p class="text-xs text-center text-gray-500 mt-4">Arahkan kamera ke QR Code milik penyewa.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div x-data="{ propId: '' }">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Properti <span class="text-error-500">*</span></label>
                <select x-model="propId" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition mb-3">
                    <option value="">Pilih Properti Dulu</option>
                    @foreach($properties as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Unit <span class="text-error-500">*</span></label>
                <select name="unit_id" required class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    <option value="">Pilih Unit</option>
                    @foreach($properties as $p)
                        @foreach($p->units->where('status','available') as $u)
                        <option value="{{ $u->id }}" x-show="propId == '{{ $p->id }}'" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->unit_code }} — {{ $u->name }} (Rp {{ number_format($u->rent_price,0,',','.') }}/bln)
                        </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Periode & Tipe --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Mulai <span class="text-error-500">*</span></label>
                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Akhir <span class="text-error-500">*</span></label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Sewa <span class="text-error-500">*</span></label>
                <select name="rental_type" required class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    <option value="monthly" {{ old('rental_type')=='monthly'?'selected':'' }}>Bulanan</option>
                    <option value="yearly" {{ old('rental_type')=='yearly'?'selected':'' }}>Tahunan</option>
                </select>
            </div>
        </div>

        {{-- Harga & Deposit --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Harga Sewa / Bulan (Rp) <span class="text-error-500">*</span></label>
                <input type="number" name="rent_amount" value="{{ old('rent_amount') }}" min="0" required class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Deposit / Uang Jaminan (Rp)</label>
                <input type="number" name="deposit_amount" value="{{ old('deposit_amount', 0) }}" min="0" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
        </div>

        {{-- Catatan --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
            <textarea name="notes" rows="3" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">Buat Kontrak</button>
            <a href="{{ route('manager.contracts.index') }}" class="rounded-lg border border-stroke px-6 py-3 font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('qrScanner', () => ({
            showScanner: false,
            tenantCode: '{{ old("tenant_code") }}',
            html5QrcodeScanner: null,
            
            startScanner() {
                this.showScanner = true;
                
                // Wait for modal to render
                setTimeout(() => {
                    this.html5QrcodeScanner = new Html5QrcodeScanner(
                        "qr-reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
                        
                    this.html5QrcodeScanner.render((decodedText, decodedResult) => {
                        this.tenantCode = decodedText;
                        this.stopScanner();
                    }, (error) => {
                        // ignore scan errors, it throws them continuously when no QR is found
                    });
                }, 100);
            },
            
            stopScanner() {
                if (this.html5QrcodeScanner) {
                    this.html5QrcodeScanner.clear().catch(error => {
                        console.error("Failed to clear html5QrcodeScanner. ", error);
                    });
                    this.html5QrcodeScanner = null;
                }
                this.showScanner = false;
            }
        }));
    });
</script>
@endpush
