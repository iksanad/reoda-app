@extends('layouts.app')
@section('title', 'Profil Penyewa - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Profil Saya</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.dashboard') }}">Dashboard /</a></li>
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
    <div class="rounded-xl border border-stroke bg-white shadow-sm p-6 flex flex-col items-center text-center">
        <div class="h-24 w-24 rounded-full overflow-hidden mb-4 ring-4 ring-reoda/20">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4C74AF&color=fff&size=96" alt="{{ $user->name }}">
        </div>
        <h3 class="text-lg font-bold text-black">{{ $user->name }}</h3>
        <p class="text-sm text-gray-500 mb-1">{{ $user->email }}</p>
        <span class="inline-flex rounded-full bg-reoda/10 px-3 py-1 text-xs font-semibold text-reoda">Penyewa</span>
        <div class="mt-5 w-full border-t border-stroke pt-4 text-left space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Bergabung</span><span class="font-medium">{{ $user->created_at->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Telepon</span><span class="font-medium">{{ $user->phone ?? '-' }}</span></div>
        </div>
        <div class="mt-6 w-full rounded-lg bg-gray-50 p-4 border border-stroke text-center">
            <p class="text-xs text-gray-500 mb-2">ID Penyewa (Tunjukkan ke Pengelola)</p>
            <div class="inline-block bg-white p-2 rounded border border-gray-200 mb-2">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($user->user_code ?? 'NO-CODE') !!}
            </div>
            <p class="font-bold text-reoda tracking-wider">{{ $user->user_code }}</p>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-5">
        <form action="{{ route('tenant.profile.index') }}" method="POST">
            @csrf
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
@endsection
