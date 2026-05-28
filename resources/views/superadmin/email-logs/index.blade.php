@extends('layouts.app')

@section('title', 'Log Email Notifikasi - Superadmin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Riwayat Pengiriman Email</h1>
    <p class="text-sm text-gray-500 mt-1">Pantau status pengiriman email notifikasi dan kirim ulang jika gagal.</p>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200">
    {{ session('error') }}
</div>
@endif

{{-- Status Tabs --}}
<div class="flex flex-wrap gap-2 mb-6">
    @foreach(['all' => 'Semua', 'pending' => 'Pending', 'sent' => 'Terkirim', 'failed' => 'Gagal'] as $key => $label)
    <a href="{{ route('superadmin.email-logs.index', ['status' => $key]) }}"
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

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="px-5 py-4 sm:px-6 flex items-center justify-between">
        <h3 class="text-base font-medium text-gray-800">Riwayat Pengiriman Email</h3>
    </div>
    <div class="max-w-full overflow-x-auto border-t border-gray-100">
        <table class="min-w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-5 py-3 sm:px-6 text-left">
                        <p class="font-medium text-gray-500 text-sm">Tanggal</p>
                    </th>
                    <th class="px-5 py-3 sm:px-6 text-left">
                        <p class="font-medium text-gray-500 text-sm">Penerima</p>
                    </th>
                    <th class="px-5 py-3 sm:px-6 text-left">
                        <p class="font-medium text-gray-500 text-sm">Subjek / Tipe</p>
                    </th>
                    <th class="px-5 py-3 sm:px-6 text-left">
                        <p class="font-medium text-gray-500 text-sm">Status</p>
                    </th>
                    <th class="px-5 py-3 sm:px-6 text-right">
                        <p class="font-medium text-gray-500 text-sm">Aksi</p>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="even:bg-[#e6f4f1] odd:bg-white transition">
                    <td class="px-5 py-4 sm:px-6 text-sm font-bold text-reoda-dark">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="font-bold text-reoda-dark text-sm">{{ optional($log->user)->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ optional($log->user)->email ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="font-bold text-reoda-dark text-sm line-clamp-1" title="{{ $log->title }}">{{ $log->title }}</p>
                        <span class="inline-flex rounded-full px-2 py-0.5 mt-1 text-xs font-semibold bg-gray-100 text-gray-600">{{ $log->type }}</span>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @if($log->email_status === 'sent')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Terkirim
                            </span>
                        @elseif($log->email_status === 'failed')
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200 w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Gagal
                                </span>
                                @if($log->email_error)
                                <span class="text-[10px] text-red-500 max-w-[200px] truncate" title="{{ $log->email_error }}">
                                    {{ $log->email_error }}
                                </span>
                                @endif
                            </div>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 sm:px-6 text-right">
                        @if($log->email_status === 'failed')
                        <form method="POST" action="{{ route('superadmin.email-logs.resend', $log) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-reoda hover:text-reoda-dark transition">Resend Email</button>
                        </form>
                        @else
                        <span class="text-gray-300">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 sm:px-6 text-center">
                        <svg class="mx-auto h-14 w-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <p class="text-gray-500 mb-1 font-medium">Belum ada riwayat email</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
