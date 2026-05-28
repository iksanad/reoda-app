@extends('layouts.app')
@section('title', 'Pengaturan Pengelola - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Pengaturan</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Pengaturan</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-4 gap-6" x-data="{ tab: 'umum' }">
    {{-- Tabs / Menu --}}
    <div class="md:col-span-1 space-y-2">
        <button @click="tab = 'umum'" :class="tab === 'umum' ? 'bg-[#003648] text-white' : 'text-gray-600 hover:bg-gray-100'" class="w-full text-left rounded-lg px-4 py-2.5 font-medium transition">Umum</button>
        <button @click="tab = 'notifikasi'" :class="tab === 'notifikasi' ? 'bg-[#003648] text-white' : 'text-gray-600 hover:bg-gray-100'" class="w-full text-left rounded-lg px-4 py-2.5 font-medium transition">Notifikasi</button>
        <button @click="tab = 'aturan'" :class="tab === 'aturan' ? 'bg-[#003648] text-white' : 'text-gray-600 hover:bg-gray-100'" class="w-full text-left rounded-lg px-4 py-2.5 font-medium transition">Aturan Properti</button>
        <button @click="tab = 'billing'" :class="tab === 'billing' ? 'bg-[#003648] text-white' : 'text-gray-600 hover:bg-gray-100'" class="w-full text-left rounded-lg px-4 py-2.5 font-medium transition">Billing & Pajak</button>
    </div>

    {{-- Content --}}
    <div class="md:col-span-3">
        <form x-show="tab === 'umum'" class="rounded-xl border border-stroke bg-white shadow-sm p-6 space-y-6">
            <h4 class="font-bold text-black border-b border-stroke pb-3">Pengaturan Umum Pengelola</h4>
            <div class="space-y-4">
                <div class="border-b border-stroke pb-4">
                    <label class="mb-1.5 block text-sm font-medium text-black">Jatuh Tempo Default (Tanggal)</label>
                    <input type="number" value="10" class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-black outline-none focus:border-reoda">
                    <p class="text-xs text-gray-500 mt-1.5">Tanggal default jatuh tempo tagihan setiap bulannya.</p>
                </div>
                <div class="border-b border-stroke pb-4">
                    <label class="mb-1.5 block text-sm font-medium text-black">Denda Keterlambatan (%)</label>
                    <input type="number" value="5" class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-black outline-none focus:border-reoda">
                </div>
            </div>
            <button type="button" onclick="alert('Ini adalah placeholder halaman pengaturan.')" class="rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">Simpan Pengaturan</button>
        </form>

        <form x-show="tab === 'notifikasi'" x-cloak class="rounded-xl border border-stroke bg-white shadow-sm p-6 space-y-6">
            <h4 class="font-bold text-black border-b border-stroke pb-3">Pengaturan Notifikasi</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-stroke pb-4">
                    <div>
                        <p class="font-medium text-black">Notifikasi WhatsApp</p>
                        <p class="text-xs text-gray-500 mt-1">Kirim notifikasi ke WA penyewa saat tagihan jatuh tempo.</p>
                    </div>
                    <label for="toggle1" class="flex cursor-pointer select-none items-center">
                        <div class="relative">
                            <input type="checkbox" id="toggle1" class="sr-only" checked />
                            <div class="block h-6 w-10 rounded-full bg-reoda transition"></div>
                            <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition transform translate-x-4"></div>
                        </div>
                    </label>
                </div>
                <div class="flex items-center justify-between border-b border-stroke pb-4">
                    <div>
                        <p class="font-medium text-black">Pemberitahuan Email Bulanan</p>
                        <p class="text-xs text-gray-500 mt-1">Dapatkan rekap laporan performa bulanan ke email Anda.</p>
                    </div>
                    <label for="toggle2" class="flex cursor-pointer select-none items-center">
                        <div class="relative">
                            <input type="checkbox" id="toggle2" class="sr-only" />
                            <div class="block h-6 w-10 rounded-full bg-gray-300 transition"></div>
                            <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition transform"></div>
                        </div>
                    </label>
                </div>
            </div>
            <button type="button" onclick="alert('Ini adalah placeholder halaman pengaturan.')" class="rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">Simpan Pengaturan</button>
        </form>

        <div x-show="tab === 'aturan'" x-cloak class="rounded-xl border border-stroke bg-white shadow-sm p-6">
            <h4 class="font-bold text-black border-b border-stroke pb-3 mb-6">Aturan Properti Global</h4>
            <p class="text-gray-500 text-sm mb-4">Fitur ini akan mengatur *terms & conditions* bawaan setiap kali kontrak sewa dibuat.</p>
            <textarea rows="5" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition" placeholder="Contoh: Dilarang membawa hewan peliharaan..."></textarea>
            <button type="button" class="mt-4 rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">Simpan Aturan</button>
        </div>

        <div x-show="tab === 'billing'" x-cloak class="rounded-xl border border-stroke bg-white shadow-sm p-6 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h4 class="font-bold text-black mb-2">Segera Hadir</h4>
            <p class="text-sm text-gray-500">Fitur manajemen pajak dan pembagian komisi sedang dalam pengembangan.</p>
        </div>
    </div>
</div>
@endsection
