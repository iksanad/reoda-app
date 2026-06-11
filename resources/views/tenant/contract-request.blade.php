@extends('layouts.app')
@section('title', 'Ajukan Kontrak - ' . $property->name . ' - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-black">Pengajuan Kontrak Sewa</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ $property->name }} · {{ $property->city }}</p>
    </div>
    <nav><ol class="flex items-center gap-2 text-sm">
        <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Ajukan Kontrak</li>
    </ol></nav>
</div>

<form id="contract-form" method="POST" action="{{ route('tenant.contract.request.store', $property->property_code) }}"
    x-data="contractRequestForm()" class="grid grid-cols-1 lg:grid-cols-3 gap-6" onsubmit="return validateForm()" novalidate>
    @csrf

    {{-- Kolom Kiri: Form --}}
    <div class="lg:col-span-2 space-y-5">

        @if(session('error'))
        <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <strong>Gagal:</strong> {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Info Properti --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-5">
            <div class="flex items-center gap-4">
                <img src="{{ $property->cover_image_url }}" alt="{{ $property->name }}"
                    class="h-16 w-24 object-cover rounded-lg shrink-0">
                <div>
                    <h3 class="font-bold text-black">{{ $property->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $property->address }}, {{ $property->city }}</p>
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold mt-1
                        @if($property->type === 'kos') bg-blue-100 text-blue-700
                        @elseif($property->type === 'kontrakan') bg-green-100 text-green-700
                        @else bg-purple-100 text-purple-700 @endif">
                        {{ ucfirst($property->type) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Pilih Unit --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4">
                <h4 class="font-bold text-black">1. Pilih Kamar / Unit</h4>
            </div>
            <div class="p-6">
                @if($property->units->count() > 0)
                <div class="space-y-3">
                    @foreach($property->units as $unit)
                    <label class="flex items-center gap-4 rounded-xl border-2 border-stroke p-4 cursor-pointer hover:border-reoda transition has-[:checked]:border-reoda has-[:checked]:bg-reoda/5">
                        <input type="radio" name="unit_id" value="{{ $unit->id }}"
                            x-on:change="selectUnit({{ $unit->rent_price }})"
                            class="h-4 w-4 text-reoda border-gray-300 focus:ring-reoda"
                            {{ old('unit_id') == $unit->id ? 'checked' : '' }} required>
                        <div class="flex-1">
                            <p class="font-semibold text-black text-sm">{{ $unit->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $unit->unit_code }}
                                @if($unit->area_sqm) · {{ $unit->area_sqm }} m² @endif
                                @if($unit->floor) · Lantai {{ $unit->floor }} @endif
                                @if($unit->description) · {{ $unit->description }} @endif
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold text-reoda">Rp {{ number_format($unit->rent_price, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400">/bulan</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @else
                <p class="text-center text-gray-400 py-8">Tidak ada unit yang tersedia saat ini.</p>
                @endif
            </div>
        </div>

        {{-- Opsi Pembayaran (hanya untuk kontrakan/apartemen/rumah) --}}
        @if(in_array($property->type, ['kontrakan', 'apartemen', 'rumah']))
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4">
                <h4 class="font-bold text-black">2. Durasi & Jenis Pembayaran</h4>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-error-500">*</span></label>
                        <select name="payment_cycle" x-model="paymentCycle" required
                            class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                            <option value="monthly">Bulanan</option>
                            @if($property->yearly_discount_percent > 0)
                            <option value="yearly">Tahunan (Hemat {{ $property->yearly_discount_percent }}%)</option>
                            @else
                            <option value="yearly">Tahunan</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Durasi
                            <span x-text="paymentCycle === 'yearly' ? '(Tahun)' : '(Bulan)'"></span>
                            <span class="text-error-500">*</span>
                        </label>
                        <input type="number" name="contract_duration" x-model.number="duration"
                            min="1" :max="paymentCycle === 'yearly' ? 5 : 24"
                            placeholder="Contoh: 6" required
                            class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                        <p class="text-xs text-gray-400 mt-1">Maks. <span x-text="paymentCycle === 'yearly' ? '5 tahun' : '24 bulan'"></span></p>
                    </div>
                </div>

                {{-- Ringkasan Harga --}}
                <div class="rounded-xl bg-reoda/5 border border-reoda/20 p-4" x-show="selectedPrice > 0">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Ringkasan Harga</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Harga/bulan</span>
                            <span x-text="'Rp ' + formatNum(selectedPrice)"></span>
                        </div>
                        <template x-if="paymentCycle === 'yearly' && {{ $property->yearly_discount_percent }} > 0">
                            <div class="flex justify-between text-green-600">
                                <span>Diskon tahunan ({{ $property->yearly_discount_percent }}%)</span>
                                <span x-text="'- Rp ' + formatNum(selectedPrice * {{ $property->yearly_discount_percent / 100 }})"></span>
                            </div>
                        </template>
                        <div class="flex justify-between text-gray-600">
                            <span>Durasi</span>
                            <span x-text="paymentCycle === 'yearly' ? duration + ' tahun (' + (duration * 12) + ' bulan)' : duration + ' bulan'"></span>
                        </div>
                        <div class="border-t border-reoda/20 pt-2 flex justify-between font-bold text-reoda text-base">
                            <span>Total Estimasi</span>
                            <span x-text="'Rp ' + formatNum(calcTotal())"></span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">* Total dihitung untuk seluruh durasi kontrak. Pembayaran dapat dilakukan <span x-text="paymentCycle === 'yearly' ? 'per tahun' : 'per bulan'"></span>.</p>
                </div>
            </div>
        </div>
        @else
        {{-- Kos: Input Toleransi Telat --}}
        <input type="hidden" name="payment_cycle" value="monthly">
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4">
                <h4 class="font-bold text-black">2. Informasi Pembayaran</h4>
            </div>
            <div class="p-6">
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 mb-4">
                    <p class="text-sm text-blue-800">
                        <strong>Kos-kosan:</strong> Pembayaran dilakukan setiap bulan secara otomatis. Kontrak akan diperpanjang tiap bulan selama pembayaran dilakukan tepat waktu.
                    </p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Batas Toleransi Telat Bayar (hari)</label>
                    <input type="number" name="tolerance_days" value="{{ old('tolerance_days', 7) }}"
                        min="1" max="30" placeholder="7"
                        class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    <p class="text-xs text-gray-400 mt-1">Berapa hari setelah jatuh tempo sebelum kontrak berakhir jika belum dibayar. (Nilai standar: 7 hari)</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Ketentuan Hunian (Selalu Tampil) --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4">
                <h4 class="font-bold text-black">Ketentuan & Peraturan Hunian</h4>
            </div>
            <div class="p-6">
                {{-- Ketentuan Standar Platform --}}
                <div class="mb-4 text-sm text-gray-600 space-y-2">
                    <p class="font-semibold text-gray-800">Syarat dan Ketentuan Standar REODA:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Penyewa wajib mematuhi norma sosial, hukum yang berlaku, serta menjaga kebersihan dan ketertiban hunian.</li>
                        <li>Pembayaran sewa dilakukan sesuai dengan siklus pembayaran yang telah disepakati.</li>
                        <li>Segala bentuk kerusakan fasilitas akibat kelalaian penyewa menjadi tanggung jawab penyewa.</li>
                        <li>Pengelola berhak mengakhiri kontrak secara sepihak apabila penyewa melanggar aturan fatal (seperti narkoba, asusila, atau tindak kriminal).</li>
                    </ul>
                </div>

                {{-- Ketentuan Tambahan dari Pengelola (Jika Ada) --}}
                @if($property->property_terms)
                <div class="mt-5 rounded-lg bg-amber-50 border border-amber-200 p-4">
                    <p class="font-semibold text-amber-900 text-sm mb-2">Peraturan Khusus dari Pengelola ({{ $property->name }}):</p>
                    <p class="text-sm text-amber-800 whitespace-pre-line leading-relaxed">{{ $property->property_terms }}</p>
                </div>
                @endif

                {{-- Checkbox Persetujuan --}}
                <div class="mt-6 flex items-start gap-3 pt-4 border-t border-gray-100">
                    <input type="checkbox" id="agree-terms" required
                        class="mt-1 h-4 w-4 rounded border-gray-300 text-reoda focus:ring-reoda cursor-pointer"
                        onchange="document.getElementById('terms-warning').classList.add('hidden')">
                    <label for="agree-terms" class="text-sm text-gray-700 cursor-pointer">
                        Saya telah membaca, memahami, dan menyetujui seluruh Ketentuan Standar REODA beserta Peraturan Khusus dari pengelola hunian.
                    </label>
                </div>
                <p id="terms-warning" class="hidden mt-2 text-sm text-red-600 font-medium">⚠️ Harap centang persetujuan ketentuan sebelum mengirim pengajuan.</p>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Summary & Submit --}}
    <div>
        <div class="rounded-xl border border-stroke bg-white shadow-sm p-6 sticky top-24">
            <h4 class="font-bold text-black mb-4">Ringkasan Pengajuan</h4>
            <div class="space-y-3 text-sm mb-6">
                <div class="flex justify-between gap-2">
                    <span class="text-gray-500">Properti</span>
                    <span class="font-medium text-right text-gray-800">{{ $property->name }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-gray-500">Pengelola</span>
                    <span class="font-medium text-gray-800">{{ $property->manager->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-gray-500">Jenis</span>
                    <span class="font-medium text-gray-800">{{ ucfirst($property->type) }}</span>
                </div>
                <div class="border-t border-stroke pt-3 flex justify-between gap-2">
                    <span class="text-gray-500">Status setelah kirim</span>
                    <span class="inline-flex rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-0.5">Menunggu Approval</span>
                </div>
            </div>

            <div class="rounded-lg bg-blue-50 border border-blue-200 p-3 mb-5">
                <p class="text-xs text-blue-700">
                    Setelah dikirim, pengajuan akan ditinjau oleh pengelola. Anda akan mendapat notifikasi setelah disetujui atau ditolak.
                </p>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-reoda py-3 font-bold text-white hover:bg-reoda-dark transition">
                Kirim Pengajuan Kontrak
            </button>
            <a href="{{ url('/property/' . $property->property_code) }}"
                class="mt-3 flex w-full justify-center rounded-xl border border-stroke py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                ← Kembali ke Detail Properti
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('contractRequestForm', () => ({
        selectedPrice: 0,
        paymentCycle: 'monthly',
        duration: 1,
        yearlyDiscount: {{ $property->yearly_discount_percent ?? 0 }} / 100,

        selectUnit(price) {
            this.selectedPrice = price;
        },

        calcTotal() {
            if (!this.selectedPrice || !this.duration) return 0;
            let months = this.paymentCycle === 'yearly' ? this.duration * 12 : this.duration;
            let price  = this.selectedPrice;
            if (this.paymentCycle === 'yearly' && this.yearlyDiscount > 0) {
                price = price * (1 - this.yearlyDiscount);
            }
            return Math.round(price * months);
        },

        formatNum(n) {
            return Math.round(n).toLocaleString('id-ID');
        }
    }));
});

function validateForm() {
    var checkbox = document.getElementById('agree-terms');
    var warning  = document.getElementById('terms-warning');

    // Check if terms checkbox exists but is not checked
    if (checkbox && !checkbox.checked) {
        if (warning) warning.classList.remove('hidden');
        checkbox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        var container = checkbox.closest('div');
        container.style.transition = 'all 0.3s';
        container.classList.add('bg-red-50', 'p-3', '-mx-3', 'rounded-lg');
        checkbox.classList.add('ring-2', 'ring-red-500', 'ring-offset-2');
        
        setTimeout(function() {
            container.classList.remove('bg-red-50', 'p-3', '-mx-3', 'rounded-lg');
            checkbox.classList.remove('ring-2', 'ring-red-500', 'ring-offset-2');
        }, 1500);
        return false; // Prevent form submission
    }

    // Check unit selection
    var unitSelected = document.querySelector('input[name="unit_id"]:checked');
    if (!unitSelected) {
        alert('Harap pilih kamar/unit terlebih dahulu.');
        var firstUnit = document.querySelector('input[name="unit_id"]');
        if (firstUnit) firstUnit.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}
</script>
@endpush
