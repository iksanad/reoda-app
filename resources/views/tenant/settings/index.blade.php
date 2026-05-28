@extends('layouts.app')
@section('title', 'Pengaturan Penyewa - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Pengaturan</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Pengaturan</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-4 gap-6" x-data="{ tab: 'notifikasi' }">
    <div class="md:col-span-1 space-y-2">
        <button @click="tab = 'notifikasi'" :class="tab === 'notifikasi' ? 'bg-[#003648] text-white' : 'text-gray-600 hover:bg-gray-100'" class="w-full text-left rounded-lg px-4 py-2.5 font-medium transition">Notifikasi</button>
        <button @click="tab = 'privasi'" :class="tab === 'privasi' ? 'bg-[#003648] text-white' : 'text-gray-600 hover:bg-gray-100'" class="w-full text-left rounded-lg px-4 py-2.5 font-medium transition">Privasi</button>
    </div>

    <div class="md:col-span-3">
        <form x-show="tab === 'notifikasi'" class="rounded-xl border border-stroke bg-white shadow-sm p-6 space-y-6">
            <h4 class="font-bold text-black border-b border-stroke pb-3">Pengaturan Notifikasi</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-stroke pb-4">
                    <div>
                        <p class="font-medium text-black">Email Tagihan Baru</p>
                        <p class="text-xs text-gray-500 mt-1">Terima notifikasi via email ketika pengelola menerbitkan tagihan baru.</p>
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
                        <p class="font-medium text-black">Notifikasi WhatsApp</p>
                        <p class="text-xs text-gray-500 mt-1">Terima pesan pengingat tagihan dan informasi dari pengelola via WA.</p>
                    </div>
                    <label for="toggle2" class="flex cursor-pointer select-none items-center">
                        <div class="relative">
                            <input type="checkbox" id="toggle2" class="sr-only" checked />
                            <div class="block h-6 w-10 rounded-full bg-reoda transition"></div>
                            <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition transform translate-x-4"></div>
                        </div>
                    </label>
                </div>
            </div>
            <button type="button" onclick="alert('Ini adalah placeholder halaman pengaturan.')" class="rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">Simpan Pengaturan</button>
        </form>

        <form x-show="tab === 'privasi'" x-cloak class="rounded-xl border border-stroke bg-white shadow-sm p-6 space-y-6">
            <h4 class="font-bold text-black border-b border-stroke pb-3">Pengaturan Privasi</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-stroke pb-4">
                    <div>
                        <p class="font-medium text-black">Tampilkan Profil ke Penyewa Lain</p>
                        <p class="text-xs text-gray-500 mt-1">Jika diaktifkan, penghuni lain dalam satu properti dapat melihat nama Anda.</p>
                    </div>
                    <label for="toggle3" class="flex cursor-pointer select-none items-center">
                        <div class="relative">
                            <input type="checkbox" id="toggle3" class="sr-only" />
                            <div class="block h-6 w-10 rounded-full bg-gray-300 transition"></div>
                            <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition transform"></div>
                        </div>
                    </label>
                </div>
            </div>
            <button type="button" onclick="alert('Ini adalah placeholder halaman pengaturan.')" class="rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">Simpan Pengaturan</button>
        </form>
    </div>
</div>
@endsection
