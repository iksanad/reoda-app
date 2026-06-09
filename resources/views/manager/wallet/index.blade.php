@extends('layouts.app')
@section('title', 'Dompet & Penarikan - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Dompet & Penarikan Dana</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Dompet</li>
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
@if($errors->any())
<div class="mb-5 rounded-xl border-l-4 border-red-400 bg-red-50 px-5 py-4 text-sm text-red-700">
    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

{{-- Balance Cards --}}
@php $available = $user->balance - ($user->balance_hold ?? 0); @endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    {{-- Total Saldo --}}
    <div class="rounded-2xl bg-linear-to-br from-reoda to-reoda-dark p-6 text-white shadow-lg col-span-1">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <p class="text-sm font-medium text-white/80">Total Saldo</p>
        </div>
        <p class="text-3xl font-extrabold tracking-tight">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
    </div>

    {{-- Saldo Tersedia --}}
    <div class="rounded-2xl bg-white border border-stroke p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">Saldo Tersedia</p>
        </div>
        <p class="text-2xl font-extrabold text-green-600">Rp {{ number_format(max(0, $available), 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Bisa ditarik sekarang</p>
    </div>

    {{-- Saldo Dalam Proses --}}
    <div class="rounded-2xl bg-white border border-stroke p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-100">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">Dalam Proses Penarikan</p>
        </div>
        <p class="text-2xl font-extrabold text-yellow-600">Rp {{ number_format($user->balance_hold ?? 0, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Menunggu konfirmasi admin</p>
    </div>
</div>

{{-- Rekening + Tarik Dana --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-6">
    {{-- Info Rekening --}}
    <div class="md:col-span-3 rounded-2xl bg-white border border-stroke p-6 shadow-sm">
        <h4 class="font-bold text-black mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
            Rekening Pencairan
        </h4>
        @if($user->bank_name)
        <div class="rounded-xl border border-stroke bg-gray-50 p-4 space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-500">Nama Bank</span>
                <span class="text-sm font-bold text-black">{{ $user->bank_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-500">Nomor Rekening</span>
                <span class="font-mono text-sm font-bold text-black">{{ $user->bank_account_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-500">Atas Nama</span>
                <span class="text-sm font-bold text-black">{{ $user->bank_account_name }}</span>
            </div>
        </div>
        @else
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-700">
            ⚠️ Belum ada rekening pencairan. <a href="{{ route('manager.profile.index') }}" class="font-bold hover:underline">Atur di Profil →</a>
        </div>
        @endif
        <p class="text-xs text-gray-400 mt-3">Ubah rekening di <a href="{{ route('manager.profile.index') }}" class="text-reoda hover:underline font-medium">Pengaturan Profil</a>.</p>
    </div>

    {{-- Form Tarik Dana --}}
    <div class="md:col-span-2 rounded-2xl bg-white border border-stroke p-6 shadow-sm" id="withdraw-section">
        <h4 class="font-bold text-black mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-reoda" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Tarik Dana
        </h4>
        @if($available > 0)
        <form action="{{ route('manager.wallet.withdraw') }}" method="POST" x-data="{ amount: '' }">
            @csrf
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Penarikan</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-sm text-gray-500 font-medium">Rp</span>
                    <input type="number" name="amount" x-model="amount"
                        min="10000" max="{{ $available }}" step="1000" required
                        placeholder="0"
                        class="w-full rounded-lg border border-stroke py-2.5 pl-10 pr-4 text-sm outline-none focus:border-reoda transition">
                </div>
                <p class="text-xs text-gray-400 mt-1">Maks: Rp {{ number_format($available, 0, ',', '.') }}</p>
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bank</label>
                <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" required
                    class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Rekening</label>
                <input type="text" name="bank_account" value="{{ old('bank_account', $user->bank_account_number) }}" required
                    class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Atas Nama</label>
                <input type="text" name="account_name" value="{{ old('account_name', $user->bank_account_name) }}" required
                    class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
            </div>
            <button type="submit"
                class="w-full rounded-lg bg-reoda py-3 font-bold text-white hover:bg-reoda-dark transition text-sm"
                onclick="return confirm('Ajukan penarikan Rp ' + parseInt(this.form.amount.value).toLocaleString('id-ID') + '?')">
                Ajukan Penarikan
            </button>
        </form>
        @else
        <div class="rounded-xl bg-gray-50 border border-stroke p-4 text-center text-sm text-gray-500">
            <svg class="mx-auto w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Tidak ada saldo yang dapat ditarik saat ini.
        </div>
        @endif
    </div>
</div>

{{-- Tabs: Riwayat --}}
<div x-data="{ tab: 'transactions' }" class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
    <div class="flex border-b border-stroke bg-gray-50/50">
        <button @click="tab = 'transactions'"
            :class="tab === 'transactions' ? 'border-b-2 border-reoda text-reoda font-bold' : 'text-gray-500 hover:text-black font-medium'"
            class="px-6 py-4 text-sm transition-colors">Riwayat Transaksi</button>
        <button @click="tab = 'withdrawals'"
            :class="tab === 'withdrawals' ? 'border-b-2 border-reoda text-reoda font-bold' : 'text-gray-500 hover:text-black font-medium'"
            class="px-6 py-4 text-sm transition-colors">Riwayat Penarikan</button>
    </div>

    {{-- Transactions --}}
    <div x-show="tab === 'transactions'">
        @if($transactions->isEmpty())
        <div class="py-12 text-center text-gray-400 text-sm">Belum ada riwayat transaksi.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 text-left border-b border-stroke text-xs uppercase text-gray-500 tracking-wide">
                        <th class="py-3 px-6 font-semibold">Tanggal</th>
                        <th class="py-3 px-6 font-semibold">Keterangan</th>
                        <th class="py-3 px-6 font-semibold">Referensi</th>
                        <th class="py-3 px-6 font-semibold text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke">
                    @foreach($transactions as $t)
                    @php
                        $isCredit = in_array($t->type, ['SALE','CREDIT','credit','WITHDRAW_REVERSAL']);
                        $typeLabel = match($t->type) {
                            'SALE'             => '💰 Penjualan',
                            'CREDIT'           => '➕ Kredit',
                            'WITHDRAW'         => '💸 Penarikan',
                            'WITHDRAW_REVERSAL'=> '↩️ Reversal',
                            'REFUND'           => '🔄 Refund',
                            default            => ucfirst($t->type),
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/60">
                        <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap">{{ $t->created_at->format('d M Y') }}<br><span class="text-xs">{{ $t->created_at->format('H:i') }}</span></td>
                        <td class="py-4 px-6">
                            <p class="text-sm font-medium text-black">{{ $t->description ?? '-' }}</p>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $isCredit ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $typeLabel }}
                            </span>
                        </td>
                        <td class="py-4 px-6 font-mono text-xs text-gray-400">{{ $t->reference_id ?? '-' }}</td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-extrabold text-sm {{ $isCredit ? 'text-green-600' : 'text-red-500' }}">
                                {{ $isCredit ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </span>
                            @if(isset($t->balance_after))
                            <p class="text-xs text-gray-400 mt-0.5">Saldo: Rp {{ number_format($t->balance_after, 0, ',', '.') }}</p>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stroke">{{ $transactions->links() }}</div>
        @endif
    </div>

    {{-- Withdrawals --}}
    <div x-show="tab === 'withdrawals'" x-cloak>
        @if($withdrawals->isEmpty())
        <div class="py-12 text-center text-gray-400 text-sm">Belum ada riwayat penarikan dana.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 text-left border-b border-stroke text-xs uppercase text-gray-500 tracking-wide">
                        <th class="py-3 px-6 font-semibold">Tanggal</th>
                        <th class="py-3 px-6 font-semibold">Tujuan</th>
                        <th class="py-3 px-6 font-semibold text-right">Jumlah</th>
                        <th class="py-3 px-6 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke">
                    @foreach($withdrawals as $w)
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
                            'SUCCESS'    => '✅ Berhasil',
                            'REJECTED'   => '❌ Ditolak',
                            'CANCELLED'  => '🚫 Dibatalkan',
                            default      => $w->status,
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/60">
                        <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap">{{ $w->created_at->format('d M Y') }}<br><span class="text-xs">{{ $w->created_at->format('H:i') }}</span></td>
                        <td class="py-4 px-6">
                            <p class="font-semibold text-sm text-black">{{ $w->bank_name }}</p>
                            <p class="font-mono text-xs text-gray-500">{{ $w->bank_account }}</p>
                            <p class="text-xs text-gray-400">a/n {{ $w->account_name }}</p>
                        </td>
                        <td class="py-4 px-6 text-right font-extrabold text-sm text-red-500">
                            Rp {{ number_format($w->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">{{ $label }}</span>
                            @if($w->rejection_reason)
                            <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">{{ $w->rejection_reason }}</p>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stroke">{{ $withdrawals->links() }}</div>
        @endif
    </div>
</div>
<style>[x-cloak]{display:none!important}</style>
@endsection
