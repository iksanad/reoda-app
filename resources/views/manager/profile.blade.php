@extends('layouts.app')
@section('title', 'Profil Pengelola - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Profil Saya</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Profil</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-5 rounded-md border-l-4 border-error-400 bg-error-50 px-5 py-3 text-sm text-error-700">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Avatar Card --}}
    <div class="rounded-xl border border-stroke bg-white shadow-sm p-6 flex flex-col items-center text-center">
        <div class="h-24 w-24 rounded-full overflow-hidden mb-4 ring-4 ring-reoda/20">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4C74AF&color=fff&size=96" alt="{{ $user->name }}">
        </div>
        <h3 class="text-lg font-bold text-black">{{ $user->name }}</h3>
        <p class="text-sm text-gray-500 mb-1">{{ $user->email }}</p>
        <span class="inline-flex rounded-full bg-reoda/10 px-3 py-1 text-xs font-semibold text-reoda">Pengelola</span>
        <div class="mt-5 w-full border-t border-stroke pt-4 text-left space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Bergabung</span><span class="font-medium">{{ $user->created_at->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Telepon</span><span class="font-medium">{{ $user->phone ?? '-' }}</span></div>
        </div>

        {{-- Referral Card (PENDING - hide until finalized) --}}
        {{-- <div class="mt-4 w-full rounded-xl bg-gradient-to-br from-reoda to-reoda-dark p-5 text-white text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <p class="text-sm font-bold">Kode Referral Saya</p>
            </div>
            <p class="font-mono text-2xl font-extrabold tracking-widest mb-1">{{ $user->user_code }}</p>
            <p class="text-xs text-white/70 mb-4">Bagikan ke calon penyewa! Mereka dapat diskon Rp 50.000 dan kamu dapat hadiah setelah mereka bayar sewa pertama.</p>
            @if($user->discount_quota > 0)
            <div class="bg-white/20 rounded-lg px-4 py-2 mb-3">
                <p class="text-xs text-white/80">Voucher Diskon Anda</p>
                <p class="text-xl font-extrabold text-yellow-300">{{ $user->discount_quota }}x Diskon Rp 50.000</p>
            </div>
            @endif
            <button onclick="copyReferral('{{ $user->user_code }}')"
                class="w-full flex items-center justify-center gap-2 rounded-lg bg-white/20 hover:bg-white/30 border border-white/30 py-2.5 text-sm font-semibold transition">
                <svg id="copy-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <span id="copy-text">Salin Kode</span>
            </button>
        </div> --}}
    </div>

    {{-- Edit Form --}}
    <div class="lg:col-span-2 space-y-5">
        <form action="{{ route('manager.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Info Dasar --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm mb-5">
                <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Informasi Dasar</h4></div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-error-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">No. Telepon / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xx-xxxx-xxxx" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-lg border border-stroke py-3 px-4 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Email tidak dapat diubah.</p>
                    </div>
                </div>
            </div>

            {{-- Info Bank --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm mb-5">
                <div class="border-b border-stroke px-6 py-4">
                    <h4 class="font-bold text-black">Rekening Bank Penerima</h4>
                    <p class="text-xs text-gray-400 mt-0.5">Rekening ini akan ditampilkan kepada penyewa saat melakukan pembayaran.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="BCA, BRI, BNI, Mandiri..." class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">No. Rekening</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $user->bank_account_number) }}" placeholder="1234567890" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Atas Nama</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $user->bank_account_name) }}" placeholder="Nama pemilik rekening" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    </div>
                </div>
            </div>

            {{-- Ganti Password --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm mb-5">
                <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Ganti Password</h4></div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Password Saat Ini</label>
                        <input type="password" name="current_password" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Password Baru</label>
                        <input type="password" name="new_password" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="w-full rounded-lg border border-stroke py-3 px-4 text-sm outline-none focus:border-reoda transition">
                    </div>
                </div>
            </div>

            <button type="submit" class="rounded-lg bg-reoda px-8 py-3 font-semibold text-white hover:bg-reoda-dark transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function copyReferral(code) {
    navigator.clipboard.writeText(code).then(() => {
        const txt = document.getElementById('copy-text');
        const ico = document.getElementById('copy-icon');
        txt.textContent = 'Tersalin!';
        ico.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
        setTimeout(() => {
            txt.textContent = 'Salin Kode';
            ico.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>';
        }, 2000);
    });
}
</script>
@endpush
@endsection
