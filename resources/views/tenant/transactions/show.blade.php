@extends('layouts.app')

@section('title', 'Detail Tagihan - REODA')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('tenant.transactions.index') }}" class="text-sm font-semibold text-gray-500 hover:text-reoda flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali
        </a>
        <h1 class="text-3xl font-extrabold text-reoda-dark flex items-center gap-3">
            Tagihan #{{ $invoice->invoice_number }}
            @if($invoice->status === 'paid')
                <span class="rounded-full bg-success-100 px-3 py-1 text-sm font-bold text-success-700 border border-success-200">Lunas</span>
            @elseif($invoice->status === 'pending')
                <span class="rounded-full bg-warning-100 px-3 py-1 text-sm font-bold text-warning-700 border border-warning-200">Menunggu</span>
            @else
                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-bold text-gray-600 border border-gray-200">Belum Dibayar</span>
            @endif
        </h1>
    </div>
</div>

@if(session('info'))
<div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-6 py-4 text-sm text-blue-700 shadow-sm flex items-start gap-3">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <div>{{ session('info') }}</div>
</div>
@endif

@php
    $unit = $invoice->leaseContract->unit;
    $prop = $unit->property;
    $manager = $prop->manager;
    $subtotal = $invoice->amount + ($platformFee ?? 0);
    $totalBayar = $subtotal - ($discountAmount ?? 0);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left Content --}}
    <div class="lg:col-span-2">
        <div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-stroke bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-black text-lg">Informasi Tagihan</h3>
                <span class="text-sm text-gray-500">{{ $invoice->created_at->format('d M Y') }}</span>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Ditagihkan Kepada</p>
                        <p class="font-bold text-black">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="md:text-right">
                        <p class="text-xs text-gray-400 mb-1">Jatuh Tempo</p>
                        <p class="font-bold text-danger">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Total + Pay Button --}}
                <div class="pt-3 border-t border-stroke">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                        <div class="w-full sm:w-2/3">
                            <p class="text-xs text-gray-400 mb-2 font-bold uppercase tracking-wider">Rincian Pembayaran</p>
                            <div class="space-y-1.5 text-sm">
                                <div class="flex justify-between gap-8"><span class="text-gray-500">Tagihan pokok</span><span class="font-medium">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span></div>
                                @if(isset($platformFee) && $platformFee > 0)
                                <div class="flex justify-between gap-8"><span class="text-gray-500">Biaya admin REODA</span><span class="font-medium">Rp {{ number_format($platformFee, 0, ',', '.') }}</span></div>
                                @endif

                                @if(isset($discountAmount) && $discountAmount > 0)
                                <div class="flex justify-between gap-8"><span class="text-success-600">Diskon Referral</span><span class="font-medium text-success-600">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span></div>
                                @endif

                                <div id="gateway-fee-row" class="flex justify-between gap-8 hidden text-orange-600">
                                    <span>Biaya Payment Gateway</span>
                                    <span id="gateway-fee-amount" class="font-medium">Rp 0</span>
                                </div>

                                <div class="flex justify-between gap-8 pt-2 border-t mt-2">
                                    <span class="font-bold text-black">Total</span>
                                    <span id="total-amount-display" class="font-extrabold text-xl text-reoda">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(in_array($invoice->status, ['unpaid', 'pending']))
        {{-- Payment Methods Selection --}}
        <div id="payment-methods-section" class="mt-8 rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-stroke bg-gray-50">
                <h3 class="font-bold text-black text-lg">Pilih Metode Pembayaran</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="payment-methods-container">
                    <!-- VA BCA -->
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" value="bca_va" class="peer sr-only" onchange="updateFee('bca_va')">
                        <div class="rounded-xl border border-gray-200 p-4 hover:border-reoda peer-checked:border-reoda peer-checked:bg-reoda/5 peer-checked:ring-1 peer-checked:ring-reoda transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white border border-gray-100 rounded flex items-center justify-center p-1">
                                    <span class="font-bold text-blue-800 text-xs">BCA VA</span>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-black">BCA Virtual Account</p>
                                    <p class="text-xs text-gray-500">Biaya: Rp 4.440</p>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center peer-checked:border-reoda peer-checked:bg-reoda">
                                <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                        </div>
                    </label>

                    <!-- Mandiri VA -->
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" value="mandiri_va" class="peer sr-only" onchange="updateFee('mandiri_va')">
                        <div class="rounded-xl border border-gray-200 p-4 hover:border-reoda peer-checked:border-reoda peer-checked:bg-reoda/5 peer-checked:ring-1 peer-checked:ring-reoda transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white border border-gray-100 rounded flex items-center justify-center p-1">
                                    <span class="font-bold text-blue-900 text-[10px] leading-tight text-center">Mandiri VA</span>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-black">Mandiri Virtual Account</p>
                                    <p class="text-xs text-gray-500">Biaya: Rp 4.440</p>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center peer-checked:border-reoda peer-checked:bg-reoda">
                                <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                        </div>
                    </label>
                    
                    <!-- BNI VA -->
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" value="bni_va" class="peer sr-only" onchange="updateFee('bni_va')">
                        <div class="rounded-xl border border-gray-200 p-4 hover:border-reoda peer-checked:border-reoda peer-checked:bg-reoda/5 peer-checked:ring-1 peer-checked:ring-reoda transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white border border-gray-100 rounded flex items-center justify-center p-1">
                                    <span class="font-bold text-orange-500 text-[10px]">BNI VA</span>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-black">BNI Virtual Account</p>
                                    <p class="text-xs text-gray-500">Biaya: Rp 4.440</p>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center peer-checked:border-reoda peer-checked:bg-reoda">
                                <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                        </div>
                    </label>

                    <!-- BRI VA -->
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" value="bri_va" class="peer sr-only" onchange="updateFee('bri_va')">
                        <div class="rounded-xl border border-gray-200 p-4 hover:border-reoda peer-checked:border-reoda peer-checked:bg-reoda/5 peer-checked:ring-1 peer-checked:ring-reoda transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white border border-gray-100 rounded flex items-center justify-center p-1">
                                    <span class="font-bold text-blue-500 text-[10px]">BRI VA</span>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-black">BRI Virtual Account</p>
                                    <p class="text-xs text-gray-500">Biaya: Rp 4.440</p>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center peer-checked:border-reoda peer-checked:bg-reoda">
                                <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                        </div>
                    </label>

                    <!-- GoPay -->
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" value="gopay" class="peer sr-only" onchange="updateFee('gopay')">
                        <div class="rounded-xl border border-gray-200 p-4 hover:border-reoda peer-checked:border-reoda peer-checked:bg-reoda/5 peer-checked:ring-1 peer-checked:ring-reoda transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white border border-gray-100 rounded flex items-center justify-center p-1">
                                    <span class="font-bold text-green-500 text-[10px]">GoPay</span>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-black">GoPay</p>
                                    <p class="text-xs text-gray-500">Biaya: 2%</p>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center peer-checked:border-reoda peer-checked:bg-reoda">
                                <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                        </div>
                    </label>

                    <!-- QRIS -->
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" value="qris" class="peer sr-only" onchange="updateFee('qris')">
                        <div class="rounded-xl border border-gray-200 p-4 hover:border-reoda peer-checked:border-reoda peer-checked:bg-reoda/5 peer-checked:ring-1 peer-checked:ring-reoda transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white border border-gray-100 rounded flex items-center justify-center p-1">
                                    <span class="font-bold text-red-500 text-[10px]">QRIS</span>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-black">QRIS</p>
                                    <p class="text-xs text-gray-500">Biaya: 0.7%</p>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center peer-checked:border-reoda peer-checked:bg-reoda">
                                <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="mt-6 flex justify-end">
                    <button id="pay-button" onclick="processCheckout()" class="rounded-xl bg-reoda px-8 py-3 font-bold text-white hover:bg-reoda-dark transition shadow-md flex items-center gap-2 text-base opacity-50 cursor-not-allowed" disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Lanjutkan Pembayaran
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Pending state info --}}
        @if($invoice->status === 'pending')
        <div class="mt-6 rounded-xl border border-warning-200 bg-warning-50 px-6 py-4 text-sm text-warning-700">
            <p class="font-bold mb-1">⏳ Menunggu Konfirmasi Pembayaran</p>
            <p>Anda telah membuat permintaan pembayaran. Sistem akan otomatis mengkonfirmasi setelah pembayaran berhasil diverifikasi oleh Midtrans.</p>
            <p class="mt-2 text-xs opacity-80">Pilih metode di atas jika Anda ingin mengulangi proses bayar.</p>
        </div>
        @endif

        {{-- Paid state --}}
        @if($invoice->status === 'paid')
        <div class="mt-6 rounded-xl border border-success-200 bg-success-50 px-6 py-4 text-sm text-success-700">
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

@if(in_array($invoice->status, ['unpaid', 'pending']))
{{-- Embedded Midtrans Snap --}}
<div id="snap-embed-container" class="mt-5 hidden">
    <div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stroke flex items-center justify-between">
            <h4 class="font-bold text-black">Selesaikan Pembayaran</h4>
            <button onclick="closeSnapEmbed()" class="text-sm text-gray-400 hover:text-gray-600">✕ Ganti Metode</button>
        </div>
        <div id="snap-container" class="p-4"></div>
    </div>
</div>

<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    const subtotalAmount = Number("{{ $subtotal - ($discountAmount ?? 0) }}");
    
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function updateFee(method) {
        let gatewayFee = 0;
        if (['bca_va', 'mandiri_va', 'bni_va', 'bri_va', 'permata_va'].includes(method)) {
            gatewayFee = 4440;
        } else if (method === 'gopay') {
            gatewayFee = Math.ceil(subtotalAmount * 0.02);
        } else if (method === 'qris') {
            gatewayFee = Math.ceil(subtotalAmount * 0.007);
        }

        const total = subtotalAmount + gatewayFee;
        
        document.getElementById('gateway-fee-row').classList.remove('hidden');
        document.getElementById('gateway-fee-amount').innerText = formatRupiah(gatewayFee);
        document.getElementById('total-amount-display').innerText = formatRupiah(total);
        
        const btn = document.getElementById('pay-button');
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    function processCheckout() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedMethod) return;

        const btn = document.getElementById('pay-button');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        fetch('{{ route("tenant.transactions.pay", $invoice) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ method: selectedMethod.value })
        })
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            if (data.error) {
                alert(data.error);
                return;
            }

            if (data.snapToken) {
                openSnapEmbed(data.snapToken);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('Terjadi kesalahan saat memproses checkout.');
        });
    }

    function openSnapEmbed(token) {
        const container = document.getElementById('snap-embed-container');
        const methodsSection = document.getElementById('payment-methods-section');
        
        container.classList.remove('hidden');
        if (methodsSection) methodsSection.classList.add('hidden');
        
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });

        snap.embed(token, {
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
                // Do not reload automatically so they can see the QR/instructions if they close it, 
                // actually Midtrans handles UI inside the embed. If they close it, they might want to change method.
                // It's okay to let them click "Ganti Metode" to reload.
            }
        });
    }

    function closeSnapEmbed() {
        document.getElementById('snap-embed-container').classList.add('hidden');
        const methodsSection = document.getElementById('payment-methods-section');
        if (methodsSection) methodsSection.classList.remove('hidden');
        // We might need to refresh the page to clear the snap iframe fully
        window.location.reload();
    }
</script>
@endif

@endsection
