@extends('layouts.app')

@section('title', 'Approval Pengelola - REODA Superadmin')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-3xl font-extrabold text-reoda-dark">Approval Pengelola</h1>
    <p class="text-gray-500 mt-1">Kelola pendaftaran pengelola baru yang membutuhkan persetujuan.</p>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-5 flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-5 py-4">
    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-green-800 font-semibold text-sm">{{ session('success') }}</p>
</div>
@endif

{{-- Filters & Search --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    {{-- Status Tabs --}}
    <div class="flex flex-wrap gap-2">
        @foreach(['all' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $label)
        <a href="{{ route('superadmin.managers.index', ['status' => $key, 'search' => request('search')]) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition
               {{ $status === $key
                  ? 'bg-reoda text-white shadow'
                  : 'bg-white border border-gray-200 text-gray-600 hover:border-reoda hover:text-reoda' }}">
            {{ $label }}
            <span class="ml-1 {{ $status === $key ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }} text-xs font-bold px-2 py-0.5 rounded-full">
                {{ $counts[$key] }}
            </span>
        </a>
        @endforeach
    </div>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('superadmin.managers.index') }}" class="w-full sm:w-auto relative">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama atau email..."
            class="w-full sm:w-64 rounded-xl border border-gray-200 pl-10 pr-4 py-2 text-sm text-gray-800 focus:border-reoda focus:ring-2 focus:ring-reoda/20 focus:outline-none transition">
        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </form>
</div>

{{-- Managers Table --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="px-5 py-4 sm:px-6 flex items-center justify-between">
        <h3 class="text-base font-medium text-gray-800">Daftar Pengelola</h3>
    </div>
    @if($managers->count() > 0)
    <div class="max-w-full overflow-x-auto border-t border-gray-100">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-5 py-3 sm:px-6 text-left">
                        <p class="font-medium text-gray-500 text-sm">Pengelola</p>
                    </th>
                    <th class="px-5 py-3 sm:px-6 text-left">
                        <p class="font-medium text-gray-500 text-sm">Kontak</p>
                    </th>
                    <th class="px-5 py-3 sm:px-6 text-center">
                        <p class="font-medium text-gray-500 text-sm">Status</p>
                    </th>
                    <th class="px-5 py-3 sm:px-6 text-left">
                        <p class="font-medium text-gray-500 text-sm">Daftar Sejak</p>
                    </th>
                    <th class="px-5 py-3 sm:px-6 text-right">
                        <p class="font-medium text-gray-500 text-sm">Aksi</p>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($managers as $manager)
                <tr class="even:bg-[#e6f4f1] odd:bg-white" id="reject-{{ $manager->id }}">
                    <td class="px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ $manager->avatar_url }}" alt="{{ $manager->name }}" class="h-10 w-10 rounded-full object-cover">
                            <div>
                                <p class="font-bold text-reoda-dark text-sm">{{ $manager->name }}</p>
                                @if($manager->referral_code)
                                <p class="text-xs text-gray-500">Kode: {{ $manager->referral_code }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-bold text-reoda-dark">{{ $manager->email }}</p>
                        <p class="text-xs text-gray-500">{{ $manager->phone ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6 text-center">
                        @php
                            $statusStyles = [
                                'pending'  => 'bg-amber-100 text-amber-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabels = [
                                'pending'  => '⏳ Menunggu',
                                'approved' => '✅ Disetujui',
                                'rejected' => '❌ Ditolak',
                            ];
                        @endphp
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusStyles[$manager->manager_status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$manager->manager_status] ?? $manager->manager_status }}
                        </span>
                        @if($manager->manager_notes)
                        <p class="text-xs text-gray-500 mt-1 max-w-[160px] truncate" title="{{ $manager->manager_notes }}">{{ $manager->manager_notes }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-800">{{ $manager->created_at->format('d M Y') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6 text-right">
                        @if($manager->manager_status === 'pending')
                        <div class="flex items-center justify-end gap-2 flex-wrap" x-data="{ showReject: false }">
                            {{-- Approve --}}
                            <form method="POST" action="{{ route('superadmin.managers.approve', $manager) }}">
                                @csrf
                                <button type="submit" onclick="return confirm('Setujui pengelola {{ $manager->name }}?')"
                                    class="rounded-lg bg-green-500 text-white text-xs font-semibold px-3 py-1.5 hover:bg-green-600 transition">
                                    ✅ Setujui
                                </button>
                            </form>

                            {{-- Reject Toggle --}}
                            <button type="button" @click="showReject = !showReject"
                                class="rounded-lg bg-red-100 text-red-600 text-xs font-semibold px-3 py-1.5 hover:bg-red-200 transition">
                                ❌ Tolak
                            </button>

                            {{-- Reject Form (expandable) --}}
                            <div x-show="showReject" x-cloak class="w-full mt-2">
                                <form method="POST" action="{{ route('superadmin.managers.reject', $manager) }}">
                                    @csrf
                                    <textarea name="notes" rows="2" required
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-700 focus:border-red-400 focus:ring-0"
                                        placeholder="Tuliskan alasan penolakan..."></textarea>
                                    <div class="flex gap-2 mt-1.5">
                                        <button type="submit" class="flex-1 rounded-lg bg-red-500 text-white text-xs font-semibold py-1.5 hover:bg-red-600 transition">Kirim Penolakan</button>
                                        <button type="button" @click="showReject = false" class="flex-1 rounded-lg bg-gray-200 text-gray-700 text-xs font-semibold py-1.5 hover:bg-gray-300 transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @elseif($manager->manager_status === 'approved')
                        <form method="POST" action="{{ route('superadmin.managers.reject', $manager) }}" class="inline">
                            @csrf
                            <input type="hidden" name="notes" value="Akun dibekukan oleh superadmin.">
                            <button type="submit" onclick="return confirm('Cabut persetujuan pengelola ini?')"
                                class="rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1.5 hover:bg-red-100 hover:text-red-600 transition">
                                Cabut Persetujuan
                            </button>
                        </form>
                        @elseif($manager->manager_status === 'rejected')
                        <form method="POST" action="{{ route('superadmin.managers.approve', $manager) }}" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Setujui pengelola ini?')"
                                class="rounded-lg bg-green-100 text-green-600 text-xs font-semibold px-3 py-1.5 hover:bg-green-500 hover:text-white transition">
                                Setujui Ulang
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border-t border-gray-100 p-4">
        {{ $managers->links() }}
    </div>
    @else
    <div class="py-12 text-center border-t border-gray-100">
        <svg class="mx-auto h-14 w-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <p class="text-gray-500 mb-1 font-medium">Tidak ada pengelola dengan status "{{ $status }}".</p>
    </div>
    @endif
</div>

@endsection
