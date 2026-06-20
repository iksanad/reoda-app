@extends('layouts.auth')

@section('title', 'Masuk - REODA')

@section('content')
<div class="text-center mb-8">
    <h1 class="text-3xl font-extrabold text-[#6E9CE8] tracking-wide mb-3">Reoda</h1>
    <h2 class="text-xl font-bold text-[#3A5370] mb-2">Selamat Datang Kembali!</h2>
    <p class="text-[#8C9BAF]">Masuk untuk melanjutkan ke akun anda</p>
</div>

@if ($errors->any())
    <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <div>
        <label for="email" class="block text-sm font-bold text-[#3A5370] mb-2">Email</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan email Anda"
                class="w-full rounded-xl border-[#D1D9E6] focus:border-[#6E9CE8] focus:ring focus:ring-[#6E9CE8]/20 py-3 pl-10 pr-4 border text-[#3A5370] placeholder-[#A0ABC0] bg-[#F8FAFC]">
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-bold text-[#3A5370] mb-2">Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <input type="password" name="password" id="password" required placeholder="Masukkan kata sandi Anda"
                class="w-full rounded-xl border-[#D1D9E6] focus:border-[#6E9CE8] focus:ring focus:ring-[#6E9CE8]/20 py-3 pl-10 pr-10 border text-[#3A5370] placeholder-[#A0ABC0] bg-[#F8FAFC]">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600" onclick="const p = document.getElementById('password'); if(p.type === 'password') { p.type = 'text'; } else { p.type = 'password'; }">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between pt-1">
        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-[#D1D9E6] text-[#6E9CE8] focus:ring-[#6E9CE8]">
            <label for="remember" class="ml-2 block text-sm font-medium text-[#3A5370]">Ingat saya</label>
        </div>
        <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#6E9CE8] hover:text-[#5B88D6]">Lupa kata sandi?</a>
    </div>

    <div class="pt-2">
        <button type="submit"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md shadow-[#6E9CE8]/20 text-base font-bold text-white bg-[#75AAFA] hover:bg-[#6E9CE8] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6E9CE8] transition-all transform hover:-translate-y-0.5">
            Masuk
        </button>
    </div>
</form>

<div class="mt-8">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-[#D1D9E6]"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-[#8C9BAF] bg-white">atau masuk dengan</span>
        </div>
    </div>

    <div class="mt-6">
        <button type="button" disabled
            class="w-full flex justify-center items-center py-3 px-4 border border-[#D1D9E6] rounded-xl shadow-sm text-base font-bold text-[#3A5370] bg-white hover:bg-gray-50 transition cursor-not-allowed opacity-80">
            <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24" fill="currentColor">
                <path fill="#EA4335" d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.761H12.545z"/>
                <path fill="#34A853" d="M12.545,22c3.218,0,5.918-1.066,7.893-2.879l-4.782-3.708c-1.065,0.712-2.427,1.134-4.092,1.134c-3.149,0-5.819-2.128-6.772-4.992l-4.939,3.831C3.818,19.349,7.794,22,12.545,22z"/>
                <path fill="#4A90E2" d="M22.046,12.019c0-0.835-0.075-1.639-0.215-2.417H12.545v4.575h5.334c-0.23,1.482-1.118,2.738-2.392,3.582l4.782,3.708C23.067,18.892,24.646,15.1,22.046,12.019z"/>
                <path fill="#FBBC05" d="M5.773,11.555c-0.245-0.73-0.384-1.512-0.384-2.316s0.139-1.586,0.384-2.316L0.834,3.092C0.301,4.153,0,5.344,0,6.6s0.301,2.447,0.834,3.508L5.773,11.555z"/>
            </svg>
            Masuk dengan Google
        </button>
    </div>
</div>

<p class="mt-8 text-center text-sm text-[#8C9BAF]">
    Belum punya akun? <a href="#" class="font-medium text-reoda hover:text-reoda-dark">Hubungi administrator</a> atau <a href="{{ route('register') }}" class="font-medium text-reoda hover:text-reoda-dark">Sign Up</a>
</p>
@endsection
