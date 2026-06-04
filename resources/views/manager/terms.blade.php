@extends('layouts.app')
@section('title', 'Ketentuan Penggunaan - REODA')

@section('content')
<div class="min-h-[80vh] flex items-start justify-center pt-8">
    <div class="w-full max-w-3xl">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-reoda/10 mb-4">
                <svg class="w-8 h-8 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-reoda-dark">Ketentuan Penggunaan REODA</h1>
            <p class="text-sm text-gray-500 mt-1">Harap baca seluruh ketentuan berikut sebelum menggunakan platform ini sebagai Pengelola.</p>
        </div>

        {{-- T&C Content --}}
        <div class="rounded-2xl border border-stroke bg-white shadow-sm mb-6 overflow-hidden">
            <div class="border-b border-stroke px-6 py-4 bg-gray-50 flex items-center gap-2">
                <svg class="w-4 h-4 text-reoda" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <span class="text-sm font-semibold text-gray-700">Perjanjian Pengelola Properti REODA</span>
            </div>
            <div class="p-6 prose prose-sm max-w-none text-gray-700 space-y-5 max-h-[50vh] overflow-y-auto" id="terms-content">

                <h3 class="text-base font-bold text-gray-900">1. Definisi</h3>
                <p><strong>REODA</strong> adalah platform digital yang menghubungkan Pengelola properti (kos, kontrakan, apartemen) dengan Penyewa. <strong>Pengelola</strong> adalah pihak yang mendaftarkan properti dan unit hunian di REODA.</p>

                <h3 class="text-base font-bold text-gray-900">2. Kewajiban Pengelola</h3>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Memberikan informasi properti yang akurat, lengkap, dan tidak menyesatkan.</li>
                    <li>Menjaga kondisi hunian sesuai dengan yang ditampilkan di platform.</li>
                    <li>Merespons pengajuan kontrak dari Penyewa dalam waktu 3×24 jam kerja.</li>
                    <li>Tidak melakukan diskriminasi terhadap Penyewa berdasarkan suku, agama, ras, atau golongan.</li>
                    <li>Memberikan notifikasi kepada Penyewa terkait perubahan ketentuan hunian minimal 30 hari sebelumnya.</li>
                </ul>

                <h3 class="text-base font-bold text-gray-900">3. Hak Pengelola</h3>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Menerima atau menolak pengajuan kontrak dari Penyewa.</li>
                    <li>Menetapkan harga sewa, deposit, dan ketentuan hunian secara mandiri.</li>
                    <li>Mengakhiri kontrak sewa jika Penyewa melanggar ketentuan yang telah disepakati.</li>
                    <li>Menarik saldo pendapatan yang tersedia sesuai prosedur yang berlaku di REODA.</li>
                </ul>

                <h3 class="text-base font-bold text-gray-900">4. Sistem Pembayaran</h3>
                <p>Seluruh transaksi pembayaran sewa dilakukan melalui platform REODA. Dana Penyewa yang telah dikonfirmasi akan masuk ke saldo virtual Pengelola di sistem REODA. Pengelola dapat melakukan penarikan dana (withdrawal) sesuai dengan prosedur yang berlaku.</p>

                <h3 class="text-base font-bold text-gray-900">5. Larangan</h3>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Menerima pembayaran di luar sistem REODA tanpa persetujuan platform.</li>
                    <li>Mendaftarkan properti fiktif atau yang tidak dimiliki/dikuasai oleh Pengelola.</li>
                    <li>Menyalahgunakan data pribadi Penyewa untuk kepentingan di luar penyewaan.</li>
                </ul>

                <h3 class="text-base font-bold text-gray-900">6. Pelanggaran & Sanksi</h3>
                <p>Pelanggaran terhadap ketentuan ini dapat mengakibatkan penangguhan atau penghapusan akun Pengelola dari platform REODA tanpa pemberitahuan sebelumnya jika dianggap perlu.</p>

                <h3 class="text-base font-bold text-gray-900">7. Perubahan Ketentuan</h3>
                <p>REODA berhak mengubah ketentuan ini sewaktu-waktu. Pengelola akan diberitahu melalui email dan notifikasi di platform.</p>

                <p class="text-xs text-gray-400 pt-4">Terakhir diperbarui: Juni 2026. Versi 1.0</p>
            </div>
        </div>

        {{-- Accept Form --}}
        <div class="rounded-2xl border border-stroke bg-white shadow-sm p-6">
            <form action="{{ route('manager.terms.accept') }}" method="POST">
                @csrf
                <div class="flex items-start gap-3 mb-6">
                    <input type="checkbox" id="agree" required
                        class="mt-1 h-4 w-4 rounded border-gray-300 text-reoda focus:ring-reoda cursor-pointer">
                    <label for="agree" class="text-sm text-gray-700 cursor-pointer">
                        Saya telah membaca, memahami, dan menyetujui seluruh <strong>Ketentuan Penggunaan REODA</strong> di atas. Saya bertanggung jawab penuh atas pengelolaan properti yang saya daftarkan.
                    </label>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 rounded-xl bg-reoda py-3 font-semibold text-white hover:bg-reoda-dark transition text-center">
                        ✓ Setuju & Lanjutkan ke Dashboard
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
