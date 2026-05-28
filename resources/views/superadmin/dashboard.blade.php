@extends('layouts.app')

@section('title', 'Dashboard Superadmin - REODA')

@section('content')

{{-- Greeting --}}
<div class="mb-6">
    <h1 class="text-3xl font-extrabold text-reoda-dark">Panel Superadmin</h1>
    <p class="text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan platform REODA.</p>
</div>

{{-- Pending Alert --}}
@if($stats['pending_managers'] > 0)
<div class="mb-6 flex items-center gap-4 rounded-xl bg-amber-50 border border-amber-200 px-5 py-4">
    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 shrink-0">
        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="flex-1">
        <p class="font-semibold text-amber-800">Ada {{ $stats['pending_managers'] }} Pengelola Menunggu Persetujuan</p>
        <p class="text-sm text-amber-600">Segera review dan proses pendaftaran pengelola baru.</p>
    </div>
    <a href="{{ route('superadmin.managers.index', ['status' => 'pending']) }}" class="rounded-lg bg-amber-500 text-white text-sm font-semibold px-4 py-2 hover:bg-amber-600 transition shrink-0">
        Review Sekarang
    </a>
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
    {{-- Total Pengelola --}}
    <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-reoda/10 mb-3">
            <svg class="w-6 h-6 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <p class="text-3xl font-extrabold text-reoda-dark">{{ $stats['total_managers'] }}</p>
        <p class="text-sm font-semibold text-gray-500 mt-1">Total Pengelola</p>
        @if($stats['pending_managers'] > 0)
        <p class="text-xs text-amber-500 font-semibold mt-1">{{ $stats['pending_managers'] }} menunggu approval</p>
        @endif
    </div>

    {{-- Total Penyewa --}}
    <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 mb-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="text-3xl font-extrabold text-reoda-dark">{{ $stats['total_tenants'] }}</p>
        <p class="text-sm font-semibold text-gray-500 mt-1">Total Penyewa</p>
    </div>

    {{-- Total Properti --}}
    <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 mb-3">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <p class="text-3xl font-extrabold text-reoda-dark">{{ $stats['total_properties'] }}</p>
        <p class="text-sm font-semibold text-gray-500 mt-1">Total Properti</p>
    </div>

    {{-- Kontrak Aktif --}}
    <div class="rounded-2xl bg-reoda border-reoda p-5 shadow-sm">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 mb-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p class="text-3xl font-extrabold text-white">{{ $stats['active_contracts'] }}</p>
        <p class="text-sm font-semibold text-white/80 mt-1">Kontrak Aktif</p>
    </div>
</div>

{{-- Revenue Stats --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
    <div class="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
        <p class="text-sm font-semibold text-gray-500 mb-1">Total Pendapatan Platform</p>
        <p class="text-3xl font-extrabold text-reoda-dark">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Dari seluruh transaksi yang terverifikasi</p>
    </div>
    <div class="rounded-2xl bg-white border border-gray-200 p-6 shadow-sm">
        <p class="text-sm font-semibold text-gray-500 mb-1">Pendapatan Bulan Ini</p>
        <p class="text-3xl font-extrabold text-green-600">Rp {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ now()->format('F Y') }}</p>
    </div>
</div>

{{-- Pending Managers --}}
@if($pendingManagers->count() > 0)
<div class="rounded-2xl border border-amber-200 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-amber-100 bg-amber-50 flex items-center justify-between">
        <h3 class="text-base font-bold text-amber-800">⏳ Pengelola Menunggu Persetujuan</h3>
        <a href="{{ route('superadmin.managers.index', ['status' => 'pending']) }}" class="text-sm text-reoda font-semibold hover:underline">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Nama</th>
                    <th class="py-3 px-4 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                    <th class="py-3 px-4 text-left text-xs font-bold text-gray-500 uppercase">Daftar</th>
                    <th class="py-3 px-4 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pendingManagers as $manager)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3.5 px-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ $manager->avatar_url }}" alt="{{ $manager->name }}" class="h-8 w-8 rounded-full object-cover">
                            <span class="font-semibold text-gray-800">{{ $manager->name }}</span>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 text-sm text-gray-600">{{ $manager->email }}</td>
                    <td class="py-3.5 px-4 text-sm text-gray-500">{{ $manager->created_at->format('d M Y') }}</td>
                    <td class="py-3.5 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('superadmin.managers.approve', $manager) }}" class="inline">
                                @csrf
                                <button type="submit" class="rounded-lg bg-green-500 text-white text-xs font-semibold px-3 py-1.5 hover:bg-green-600 transition">✅ Setujui</button>
                            </form>
                            <a href="{{ route('superadmin.managers.index') }}#reject-{{ $manager->id }}" class="rounded-lg bg-red-100 text-red-600 text-xs font-semibold px-3 py-1.5 hover:bg-red-200 transition">❌ Tolak</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="rounded-2xl border border-gray-200 bg-white p-12 text-center shadow-sm">
    <svg class="mx-auto h-14 w-14 text-green-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-gray-500 font-semibold">Tidak ada pengelola yang menunggu persetujuan.</p>
</div>
@endif

@endsection
