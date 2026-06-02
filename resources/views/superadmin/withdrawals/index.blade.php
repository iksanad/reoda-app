@extends('layouts.app')

@section('title', 'Permintaan Penarikan Dana - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Permintaan Penarikan Dana</h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('superadmin.dashboard') }}">Dashboard /</a></li>
            <li class="font-medium text-reoda">Penarikan</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-5 rounded-md border-l-4 border-error-500 bg-error-50 px-5 py-3 text-sm font-medium text-error-700">
    {{ session('error') }}
</div>
@endif

<div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-50 text-left border-b border-stroke">
                    <th class="py-4 px-6 font-medium text-black">Tgl Pengajuan</th>
                    <th class="py-4 px-6 font-medium text-black">Pengelola</th>
                    <th class="py-4 px-6 font-medium text-black">Jumlah</th>
                    <th class="py-4 px-6 font-medium text-black">Rekening Tujuan</th>
                    <th class="py-4 px-6 font-medium text-black">Status</th>
                    <th class="py-4 px-6 font-medium text-black text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr class="border-b border-stroke hover:bg-gray-50" x-data="{ openApprove: false, openReject: false }">
                    <td class="py-4 px-6 text-sm text-gray-500">{{ $w->created_at->format('d M Y, H:i') }}</td>
                    <td class="py-4 px-6">
                        <p class="font-bold text-black">{{ $w->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $w->user->email }}</p>
                    </td>
                    <td class="py-4 px-6 font-bold text-reoda">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                    <td class="py-4 px-6 text-sm">
                        <p class="font-medium text-black">{{ $w->bank_name }} - {{ $w->bank_account }}</p>
                        <p class="text-xs text-gray-500">a/n {{ $w->account_name }}</p>
                    </td>
                    <td class="py-4 px-6">
                        @php
                            $sc = match($w->status) {
                                'pending' => 'bg-warning-50 text-warning-700',
                                'approved' => 'bg-success-50 text-success-700',
                                'rejected' => 'bg-error-50 text-error-700',
                            };
                        @endphp
                        <span class="inline-flex rounded px-2.5 py-0.5 text-xs font-medium {{ $sc }}">
                            {{ ucfirst($w->status) }}
                        </span>
                        @if($w->status == 'approved' && $w->proof_of_transfer)
                            <div class="mt-1">
                                <a href="{{ asset('storage/' . $w->proof_of_transfer) }}" target="_blank" class="text-xs text-reoda hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Bukti Transfer
                                </a>
                            </div>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right">
                        @if($w->status == 'pending')
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openApprove = true" class="rounded bg-success-500 px-3 py-1 text-sm font-medium text-white hover:bg-success-600">Setujui</button>
                                <button @click="openReject = true" class="rounded bg-error-500 px-3 py-1 text-sm font-medium text-white hover:bg-error-600">Tolak</button>
                            </div>
                            
                            {{-- Approve Modal --}}
                            <div x-show="openApprove" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
                                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl relative text-left" @click.outside="openApprove = false">
                                    <h3 class="mb-4 text-lg font-bold text-black">Upload Bukti Transfer</h3>
                                    <p class="text-sm text-gray-500 mb-4">Pastikan Anda telah mentransfer sejumlah <strong>Rp {{ number_format($w->amount, 0, ',', '.') }}</strong> ke rekening pengelola ini. Lalu unggah bukti transfer di bawah ini.</p>
                                    
                                    <form action="{{ route('superadmin.withdrawals.approve', $w->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-4">
                                            <input type="file" name="proof_of_transfer" required accept="image/*" class="w-full rounded-lg border border-stroke py-2 px-3 text-sm">
                                        </div>
                                        <div class="flex gap-2 justify-end">
                                            <button type="button" @click="openApprove = false" class="rounded-lg bg-gray-200 px-4 py-2 font-semibold text-gray-700">Batal</button>
                                            <button type="submit" class="rounded-lg bg-success-500 px-4 py-2 font-semibold text-white">Selesai & Setujui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            {{-- Reject Modal --}}
                            <div x-show="openReject" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
                                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl relative text-left" @click.outside="openReject = false">
                                    <h3 class="mb-4 text-lg font-bold text-black">Tolak Penarikan</h3>
                                    <p class="text-sm text-gray-500 mb-4">Uang akan dikembalikan ke saldo pengelola.</p>
                                    
                                    <form action="{{ route('superadmin.withdrawals.reject', $w->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-black mb-1">Alasan Penolakan</label>
                                            <textarea name="rejection_reason" required class="w-full rounded-lg border border-stroke py-2 px-3 text-sm outline-none focus:border-reoda h-24" placeholder="Misal: Nomor rekening tidak valid"></textarea>
                                        </div>
                                        <div class="flex gap-2 justify-end">
                                            <button type="button" @click="openReject = false" class="rounded-lg bg-gray-200 px-4 py-2 font-semibold text-gray-700">Batal</button>
                                            <button type="submit" class="rounded-lg bg-error-500 px-4 py-2 font-semibold text-white">Tolak Penarikan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">Belum ada permintaan penarikan dana.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())
    <div class="p-4 border-t border-stroke">
        {{ $withdrawals->links() }}
    </div>
    @endif
</div>
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
