@extends('layouts.app')

@section('title', 'Saldo & Penarikan - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Saldo & Penarikan</h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li class="font-medium text-reoda">Dompet</li>
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

@if($errors->any())
<div class="mb-5 rounded-md border-l-4 border-error-400 bg-error-50 px-5 py-3 text-sm text-error-700">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Saldo Card --}}
    <div class="md:col-span-1 rounded-2xl bg-white p-6 shadow-sm border border-stroke flex flex-col">
        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide">Total Saldo Aktif</p>
        <h4 class="text-4xl font-extrabold text-reoda mt-2 mb-4">Rp {{ number_format($user->balance, 0, ',', '.') }}</h4>
        
        <button onclick="document.getElementById('withdraw-modal').classList.remove('hidden')" class="w-full flex items-center justify-center gap-2 rounded-lg bg-reoda py-3 font-bold text-white hover:bg-reoda-dark transition mt-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Tarik Dana
        </button>
    </div>

    {{-- Info Rekening --}}
    <div class="md:col-span-2 rounded-2xl bg-white p-6 shadow-sm border border-stroke">
        <h4 class="font-bold text-black mb-4">Informasi Rekening Pencairan</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-400 mb-1">Nama Bank</p>
                <p class="font-semibold text-black">{{ $user->bank_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Nomor Rekening</p>
                <p class="font-mono font-semibold text-black">{{ $user->bank_account_number ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 mb-1">Atas Nama</p>
                <p class="font-semibold text-black">{{ $user->bank_account_name ?? '-' }}</p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-stroke">
            <p class="text-sm text-gray-500">Pastikan rekening ini aktif dan atas nama yang sesuai. Anda bisa mengubahnya di <a href="{{ route('manager.profile.index') }}" class="text-reoda hover:underline font-medium">Pengaturan Profil</a>.</p>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div x-data="{ tab: 'transactions' }" class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden">
    <div class="flex border-b border-stroke bg-gray-50/50">
        <button @click="tab = 'transactions'" :class="tab === 'transactions' ? 'border-b-2 border-reoda text-reoda font-bold' : 'text-gray-500 hover:text-black font-medium'" class="px-6 py-4 transition-colors">
            Riwayat Saldo
        </button>
        <button @click="tab = 'withdrawals'" :class="tab === 'withdrawals' ? 'border-b-2 border-reoda text-reoda font-bold' : 'text-gray-500 hover:text-black font-medium'" class="px-6 py-4 transition-colors">
            Riwayat Penarikan
        </button>
    </div>

    {{-- Transactions Tab --}}
    <div x-show="tab === 'transactions'" class="p-0">
        @if($transactions->isEmpty())
        <div class="p-6 text-center text-gray-500">Belum ada riwayat saldo.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 text-left border-b border-stroke">
                        <th class="py-3 px-6 font-medium text-black">Tanggal</th>
                        <th class="py-3 px-6 font-medium text-black">Keterangan</th>
                        <th class="py-3 px-6 font-medium text-black">Jumlah</th>
                        <th class="py-3 px-6 font-medium text-black text-right">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr class="border-b border-stroke hover:bg-gray-50">
                        <td class="py-4 px-6 text-sm text-gray-500">{{ $t->created_at->format('d M Y, H:i') }}</td>
                        <td class="py-4 px-6 font-medium text-black">{{ $t->description }}</td>
                        <td class="py-4 px-6 font-bold {{ $t->type == 'credit' ? 'text-success-500' : 'text-error-500' }}">
                            {{ $t->type == 'credit' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="inline-flex rounded px-2.5 py-0.5 text-xs font-medium {{ $t->type == 'credit' ? 'bg-success-50 text-success-700' : 'bg-error-50 text-error-700' }}">
                                {{ $t->type == 'credit' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stroke">{{ $transactions->links() }}</div>
        @endif
    </div>

    {{-- Withdrawals Tab --}}
    <div x-show="tab === 'withdrawals'" class="p-0" x-cloak>
        @if($withdrawals->isEmpty())
        <div class="p-6 text-center text-gray-500">Belum ada riwayat penarikan dana.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 text-left border-b border-stroke">
                        <th class="py-3 px-6 font-medium text-black">Tanggal</th>
                        <th class="py-3 px-6 font-medium text-black">Tujuan</th>
                        <th class="py-3 px-6 font-medium text-black">Jumlah</th>
                        <th class="py-3 px-6 font-medium text-black text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $w)
                    <tr class="border-b border-stroke hover:bg-gray-50">
                        <td class="py-4 px-6 text-sm text-gray-500">{{ $w->created_at->format('d M Y, H:i') }}</td>
                        <td class="py-4 px-6">
                            <p class="font-medium text-black">{{ $w->bank_name }} - {{ $w->bank_account }}</p>
                            <p class="text-xs text-gray-500">a/n {{ $w->account_name }}</p>
                        </td>
                        <td class="py-4 px-6 font-bold text-black">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-right">
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
                            @if($w->status == 'rejected')
                                <p class="text-xs text-error-500 mt-1" title="{{ $w->rejection_reason }}">{{ Str::limit($w->rejection_reason, 20) }}</p>
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

{{-- Withdraw Modal --}}
<div id="withdraw-modal" class="fixed inset-0 z-50 hidden bg-black/50 p-4 transition-opacity">
    <div class="flex h-full w-full items-center justify-center">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl relative" @click.outside="document.getElementById('withdraw-modal').classList.add('hidden')">
            <button onclick="document.getElementById('withdraw-modal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-black">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        
        <h3 class="mb-5 text-xl font-bold text-black">Tarik Dana</h3>
        
        @if($user->balance < 10000)
            <div class="rounded-md border-l-4 border-warning-500 bg-warning-50 p-4 mb-4">
                <p class="text-sm text-warning-700 font-medium">Saldo Anda kurang dari batas minimum penarikan (Rp 10.000).</p>
            </div>
            <button type="button" onclick="document.getElementById('withdraw-modal').classList.add('hidden')" class="w-full mt-2 rounded-lg bg-gray-100 py-2 font-semibold text-gray-600 hover:bg-gray-200">Batal</button>
        @else
            <form action="{{ route('manager.wallet.withdraw') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-black">Nominal Tarik Dana (Maks: Rp {{ number_format($user->balance, 0, ',', '.') }})</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-semibold text-gray-500">Rp</span>
                        <input type="number" name="amount" min="10000" max="{{ (int) $user->balance }}" required 
                            class="w-full rounded-lg border border-stroke bg-transparent py-3 pl-12 pr-4 text-black outline-none focus:border-reoda transition" placeholder="Contoh: 500000">
                    </div>
                </div>

                <div class="mb-2 rounded-lg bg-gray-50 border border-stroke p-4 text-sm">
                    <p class="text-gray-500 mb-2">Dana akan ditransfer ke rekening utama Anda:</p>
                    <p class="font-semibold text-black">{{ $user->bank_name ?? 'Bank belum diatur' }} - {{ $user->bank_account_number ?? '-' }}</p>
                    <p class="text-xs text-gray-400">a.n {{ $user->bank_account_name ?? '-' }}</p>
                    
                    <input type="hidden" name="bank_name" value="{{ $user->bank_name }}">
                    <input type="hidden" name="bank_account" value="{{ $user->bank_account_number }}">
                    <input type="hidden" name="account_name" value="{{ $user->bank_account_name ?? $user->name }}">
                </div>
                
                @if(empty($user->bank_name) || empty($user->bank_account_number))
                    <div class="mb-4 p-3 rounded bg-error-50 text-error-700 text-xs font-medium">
                        Anda harus melengkapi profil rekening Anda sebelum menarik dana. <a href="{{ route('manager.profile.index') }}" class="underline">Lengkapi sekarang</a>.
                    </div>
                    <button type="button" disabled class="w-full rounded-lg bg-gray-300 py-3 font-bold text-gray-500 cursor-not-allowed">Rekening Belum Lengkap</button>
                @else
                    <button type="submit" class="mt-4 w-full rounded-lg bg-reoda py-3 font-bold text-white hover:bg-reoda-dark transition">Ajukan Penarikan</button>
                @endif
            </form>
        @endif
    </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
