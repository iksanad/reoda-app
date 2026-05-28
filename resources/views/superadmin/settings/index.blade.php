@extends('layouts.app')

@section('title', 'Pengaturan Global - REODA Superadmin')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-extrabold text-reoda-dark">Pengaturan Global</h1>
    <p class="text-gray-500 mt-1">Kelola konfigurasi platform REODA secara global.</p>
</div>

@if(session('success'))
<div class="mb-5 flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-5 py-4">
    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-green-800 font-semibold text-sm">{{ session('success') }}</p>
</div>
@endif

<form method="POST" action="{{ route('superadmin.settings.update') }}">
    @csrf
    @method('PUT')

    {{-- Identitas Platform --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 mb-6">
        <h3 class="text-base font-bold text-reoda-dark mb-5 pb-3 border-b border-gray-100">🏠 Identitas Platform</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Aplikasi</label>
                <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'REODA' }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition"
                    placeholder="REODA">
                @error('site_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tagline</label>
                <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] ?? 'Solusi Hunian Terpercaya' }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition"
                    placeholder="Solusi Hunian Terpercaya">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Kontak</label>
                <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition"
                    placeholder="support@reoda.com">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Telepon Kontak</label>
                <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition"
                    placeholder="021-12345678">
            </div>
        </div>
    </div>

    {{-- Kebijakan Pembayaran --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 mb-6">
        <h3 class="text-base font-bold text-reoda-dark mb-5 pb-3 border-b border-gray-100">💰 Kebijakan Pembayaran</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Denda Keterlambatan Default (%)</label>
                    <input type="number" name="default_late_fee_percent" value="{{ $settings['default_late_fee_percent'] ?? 5 }}" min="0" max="100" step="0.5"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Batas Maksimal Denda (%)</label>
                    <input type="number" name="max_late_fee_percent" value="{{ $settings['max_late_fee_percent'] ?? 10 }}" min="0" max="100" step="0.5"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition">
                    <p class="text-xs text-gray-400 mt-1">Pengelola tidak bisa mengatur denda di atas batas maksimal ini.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Grace Period Default (Hari)</label>
                    <input type="number" name="default_grace_period_days" value="{{ $settings['default_grace_period_days'] ?? 3 }}" min="0" max="30"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Batas Maksimal Grace Period (Hari)</label>
                    <input type="number" name="max_grace_period_days" value="{{ $settings['max_grace_period_days'] ?? 7 }}" min="0" max="30"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition">
                    <p class="text-xs text-gray-400 mt-1">Toleransi keterlambatan maksimal yang boleh diatur pengelola.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengaturan Email / SMTP --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 mb-6">
        <h3 class="text-base font-bold text-reoda-dark mb-5 pb-3 border-b border-gray-100">📧 Konfigurasi Notifikasi Email (SMTP)</h3>
        <p class="text-sm text-gray-500 mb-5">Masukkan kredensial aplikasi untuk mengirimkan notifikasi persetujuan pengelola atau reset password.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">SMTP Host</label>
                <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition"
                    placeholder="smtp.gmail.com">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">SMTP Port</label>
                <input type="number" name="smtp_port" value="{{ $settings['smtp_port'] ?? '' }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition"
                    placeholder="587">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Pengirim (SMTP User)</label>
                <input type="email" name="smtp_email" value="{{ $settings['smtp_email'] ?? '' }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition"
                    placeholder="no-reply@reoda.com">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Aplikasi (SMTP Password)</label>
                <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition"
                    placeholder="••••••••••••">
                <p class="text-xs text-gray-400 mt-1">Gunakan password khusus aplikasi (App Password), bukan password akun email.</p>
            </div>
        </div>
    </div>

    {{-- Action --}}
    <div class="flex justify-end gap-3">
        <button type="submit" class="rounded-xl bg-reoda px-6 py-2.5 text-sm font-bold text-white shadow hover:bg-reoda-dark transition">
            💾 Simpan Pengaturan
        </button>
    </div>
</form>

@endsection
