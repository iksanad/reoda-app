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

<div class="max-w-3xl mx-auto">
    <div class="rounded-xl border border-stroke bg-white shadow-sm p-6 sm:p-8">
        <h3 class="text-xl font-bold text-black mb-6 border-b border-stroke pb-4">Pengaturan Notifikasi</h3>
        
        <form method="POST" action="#" class="space-y-6">
            @csrf
            {{-- We use a dummy action for now as requested --}}
            
            <div class="space-y-5">
                <div class="flex items-center justify-between border-b border-stroke pb-5">
                    <div class="pr-4">
                        <h4 class="font-medium text-black text-base">Notifikasi WhatsApp</h4>
                        <p class="text-sm text-gray-500 mt-1">Kirim notifikasi otomatis ke WhatsApp penyewa saat tagihan jatuh tempo.</p>
                    </div>
                    <label for="toggle_wa" class="flex cursor-pointer select-none items-center shrink-0">
                        <div class="relative">
                            <input type="checkbox" id="toggle_wa" class="sr-only" checked />
                            <div class="block h-7 w-12 rounded-full bg-reoda transition"></div>
                            <div class="dot absolute left-1 top-1 h-5 w-5 rounded-full bg-white transition transform translate-x-5"></div>
                        </div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between border-b border-stroke pb-5">
                    <div class="pr-4">
                        <h4 class="font-medium text-black text-base">Pemberitahuan Email Bulanan</h4>
                        <p class="text-sm text-gray-500 mt-1">Terima rekap laporan performa properti bulanan langsung ke email Anda.</p>
                    </div>
                    <label for="toggle_email" class="flex cursor-pointer select-none items-center shrink-0">
                        <div class="relative">
                            <input type="checkbox" id="toggle_email" class="sr-only" />
                            <div class="block h-7 w-12 rounded-full bg-gray-300 transition"></div>
                            <div class="dot absolute left-1 top-1 h-5 w-5 rounded-full bg-white transition transform"></div>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="pt-4 flex justify-end">
                <button type="button" onclick="alert('Pengaturan berhasil disimpan. (Fitur dalam tahap pengembangan)')" class="w-full sm:w-auto rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition-all transform hover:-translate-y-0.5 shadow-md">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

