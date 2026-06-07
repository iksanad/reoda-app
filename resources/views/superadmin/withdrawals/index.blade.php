@extends('layouts.app')
@section('title', 'Permintaan Penarikan Dana - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Permintaan Penarikan Dana</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('superadmin.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Penarikan</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 flex items-start gap-3 rounded-xl border-l-4 border-green-500 bg-green-50 px-5 py-4 text-sm font-medium text-green-700">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-5 flex items-start gap-3 rounded-xl border-l-4 border-red-500 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- Stats Bar --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
        $statuses = ['PENDING'=>['label'=>'Menunggu','color'=>'yellow'],
                     'PROCESSING'=>['label'=>'Diproses','color'=>'blue'],
                     'SUCCESS'=>['label'=>'Sukses','color'=>'green'],
                     'REJECTED'=>['label'=>'Ditolak','color'=>'red']];
    @endphp
    @foreach($statuses as $st => $info)
    <div class="rounded-xl border border-stroke bg-white p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">{{ $info['label'] }}</p>
        <p class="text-2xl font-extrabold text-gray-800">{{ $withdrawals->where('status', $st)->count() }}</p>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<form method="GET" class="mb-5 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
        <select name="status" class="rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda min-w-[140px]">
            <option value="">Semua Status</option>
            <option value="PENDING" {{ request('status')=='PENDING'?'selected':'' }}>Menunggu</option>
            <option value="PROCESSING" {{ request('status')=='PROCESSING'?'selected':'' }}>Diproses</option>
            <option value="SUCCESS" {{ request('status')=='SUCCESS'?'selected':'' }}>Sukses</option>
            <option value="REJECTED" {{ request('status')=='REJECTED'?'selected':'' }}>Ditolak</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Pengelola</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Email..."
            class="rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda w-48">
    </div>
    <button type="submit" class="rounded-lg bg-reoda px-5 py-2.5 text-sm font-semibold text-white hover:bg-reoda-dark transition">Filter</button>
    @if(request()->hasAny(['status','search']))
    <a href="{{ route('superadmin.withdrawals.index') }}" class="rounded-lg border border-stroke px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Reset</a>
    @endif
</form>

<div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-50 text-left border-b border-stroke">
                    <th class="py-4 px-6 font-semibold text-xs uppercase text-gray-500 tracking-wide">Tgl Pengajuan</th>
                    <th class="py-4 px-6 font-semibold text-xs uppercase text-gray-500 tracking-wide">Pengelola</th>
                    <th class="py-4 px-6 font-semibold text-xs uppercase text-gray-500 tracking-wide">Jumlah</th>
                    <th class="py-4 px-6 font-semibold text-xs uppercase text-gray-500 tracking-wide">Rekening Tujuan</th>
                    <th class="py-4 px-6 font-semibold text-xs uppercase text-gray-500 tracking-wide">Status</th>
                    <th class="py-4 px-6 font-semibold text-xs uppercase text-gray-500 tracking-wide text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stroke">
                @forelse($withdrawals as $w)
                <tr class="hover:bg-gray-50 transition" x-data="{ openApprove: false, openReject: false }">
                    <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap">{{ $w->created_at->format('d M Y') }}<br><span class="text-xs">{{ $w->created_at->format('H:i') }}</span></td>
                    <td class="py-4 px-6">
                        <p class="font-bold text-sm text-black">{{ $w->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $w->user->email }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <p class="font-extrabold text-reoda text-base">Rp {{ number_format($w->amount, 0, ',', '.') }}</p>
                    </td>
                    <td class="py-4 px-6 text-sm">
                        <p class="font-semibold text-black">{{ $w->bank_name }}</p>
                        <p class="font-mono text-xs text-gray-600">{{ $w->bank_account }}</p>
                        <p class="text-xs text-gray-400">a/n {{ $w->account_name }}</p>
                    </td>
                    <td class="py-4 px-6">
                        @php
                            $badge = match($w->status) {
                                'PENDING'    => 'bg-yellow-100 text-yellow-700',
                                'PROCESSING' => 'bg-blue-100 text-blue-700',
                                'SUCCESS'    => 'bg-green-100 text-green-700',
                                'REJECTED'   => 'bg-red-100 text-red-700',
                                'CANCELLED'  => 'bg-gray-100 text-gray-500',
                                default      => 'bg-gray-100 text-gray-500',
                            };
                            $label = match($w->status) {
                                'PENDING'    => '⏳ Menunggu',
                                'PROCESSING' => '🔄 Diproses',
                                'SUCCESS'    => '✅ Sukses',
                                'REJECTED'   => '❌ Ditolak',
                                'CANCELLED'  => '🚫 Dibatalkan',
                                default      => $w->status,
                            };
                        @endphp
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">{{ $label }}</span>
                        @if($w->processed_at)
                            <p class="text-xs text-gray-400 mt-1">{{ $w->processed_at->format('d M Y H:i') }}</p>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right">
                        @if(in_array($w->status, ['PENDING','PROCESSING']))
                        <div class="flex items-center justify-end gap-2">
                            {{-- Konfirmasi Transfer --}}
                            <button @click="openApprove = true"
                                class="rounded-lg bg-green-500 hover:bg-green-600 px-3 py-1.5 text-xs font-bold text-white transition">
                                ✅ Transfer OK
                            </button>
                            {{-- Tolak --}}
                            <button @click="openReject = true"
                                class="rounded-lg bg-red-500 hover:bg-red-600 px-3 py-1.5 text-xs font-bold text-white transition">
                                ❌ Tolak
                            </button>
                        </div>

                        {{-- Approve Modal (no file upload needed) --}}
                        <div x-show="openApprove" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl text-left mx-4" @click.outside="openApprove = false">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 text-lg">✅</div>
                                    <h3 class="text-lg font-bold text-black">Konfirmasi Transfer Selesai</h3>
                                </div>
                                <div class="rounded-xl bg-gray-50 p-4 mb-5">
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-500">Pengelola</span>
                                        <span class="font-bold text-black">{{ $w->user->name }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-500">Jumlah</span>
                                        <span class="font-extrabold text-reoda text-base">Rp {{ number_format($w->amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-500">Bank</span>
                                        <span class="font-bold">{{ $w->bank_name }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">No. Rekening</span>
                                        <span class="font-mono font-bold">{{ $w->bank_account }}</span>
                                    </div>
                                </div>
                                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-5">
                                    ⚠️ Klik tombol ini <strong>hanya setelah</strong> Anda sudah mentransfer dana ke rekening pengelola. Saldo akan langsung dipotong.
                                </p>
                                <form action="{{ route('superadmin.withdrawals.approve', $w->id) }}" method="POST">
                                    @csrf
                                    <div class="flex gap-3 justify-end">
                                        <button type="button" @click="openApprove = false"
                                            class="rounded-lg border border-stroke px-5 py-2 font-semibold text-gray-600 hover:bg-gray-50">Batal</button>
                                        <button type="submit"
                                            class="rounded-lg bg-green-500 hover:bg-green-600 px-5 py-2 font-bold text-white transition">
                                            Ya, Transfer Selesai
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Reject Modal --}}
                        <div x-show="openReject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl text-left mx-4" @click.outside="openReject = false">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 text-lg">❌</div>
                                    <h3 class="text-lg font-bold text-black">Tolak Penarikan Dana</h3>
                                </div>
                                <p class="text-sm text-gray-500 mb-4">Saldo <strong>Rp {{ number_format($w->amount, 0, ',', '.') }}</strong> akan dikembalikan ke saldo tersedia pengelola.</p>
                                <form action="{{ route('superadmin.withdrawals.reject', $w->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-black mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="rejection_reason" required rows="3"
                                            class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda"
                                            placeholder="Misal: Nomor rekening tidak valid, data tidak sesuai..."></textarea>
                                    </div>
                                    <div class="flex gap-3 justify-end">
                                        <button type="button" @click="openReject = false"
                                            class="rounded-lg border border-stroke px-5 py-2 font-semibold text-gray-600 hover:bg-gray-50">Batal</button>
                                        <button type="submit"
                                            class="rounded-lg bg-red-500 hover:bg-red-600 px-5 py-2 font-bold text-white transition">
                                            Tolak & Kembalikan Saldo
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @elseif($w->status === 'SUCCESS')
                            <span class="text-xs text-green-600 font-medium">Transfer dikonfirmasi</span>
                        @elseif($w->rejection_reason)
                            <span class="text-xs text-gray-400" title="{{ $w->rejection_reason }}">Alasan: {{ Str::limit($w->rejection_reason, 30) }}</span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <svg class="mx-auto w-14 h-14 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <p class="text-gray-400 font-medium">Belum ada permintaan penarikan dana.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())
    <div class="p-4 border-t border-stroke">{{ $withdrawals->links() }}</div>
    @endif
</div>
<style>[x-cloak]{display:none!important}</style>
@endsection
