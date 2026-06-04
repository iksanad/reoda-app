@extends('layouts.auth')

@section('title', 'Daftar - REODA')

@section('content')
<div class="max-w-md mx-auto" x-data="{ role: '{{ request()->query('role', 'tenant') }}' }">
    <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Buat Akun Baru</h1>
        <p class="text-gray-500">Bergabung dengan REODA untuk kemudahan pengelolaan sewa.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Role Selection -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <label class="cursor-pointer">
                <input type="radio" name="role" value="tenant" class="peer sr-only" x-model="role">
                <div class="rounded-lg border-2 border-gray-200 p-4 text-center hover:bg-gray-50 peer-checked:border-reoda peer-checked:bg-reoda-lightest transition">
                    <svg class="mx-auto h-8 w-8 text-gray-400 peer-checked:text-reoda mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="block text-sm font-semibold text-gray-900">Penyewa</span>
                    <span class="block text-xs text-gray-500">Cari & bayar kos</span>
                </div>
            </label>

            <label class="cursor-pointer">
                <input type="radio" name="role" value="manager" class="peer sr-only" x-model="role">
                <div class="rounded-lg border-2 border-gray-200 p-4 text-center hover:bg-gray-50 peer-checked:border-reoda peer-checked:bg-reoda-lightest transition">
                    <svg class="mx-auto h-8 w-8 text-gray-400 peer-checked:text-reoda mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="block text-sm font-semibold text-gray-900">Pengelola</span>
                    <span class="block text-xs text-gray-500">Kelola properti</span>
                </div>
            </label>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2 px-4 border text-gray-900">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2 px-4 border text-gray-900">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon / WA</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2 px-4 border text-gray-900">
        </div>

        {{-- Referral Code Field (PENDING - hide until finalized)
        <div>
            <label for="referral_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Referral <span class="text-xs text-gray-400 font-normal">(Opsional)</span></label>
            <input type="text" name="referral_code" id="referral_code" value="{{ old('referral_code') }}" placeholder="Contoh: REO-XXXXX"
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2 px-4 border text-gray-900 uppercase">
            <p class="mt-1 text-xs text-gray-500">Masukkan kode unik pengundang Anda untuk mendapatkan Voucher Diskon Rp 50.000!</p>
        </div>
        --}}

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2 px-4 border text-gray-900">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2 px-4 border text-gray-900">
            </div>
        </div>

        <button type="submit"
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-reoda hover:bg-reoda-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-reoda transition mt-4">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-reoda hover:text-reoda-dark">Masuk di sini</a>
    </p>
</div>
@endsection
