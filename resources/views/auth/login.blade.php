@extends('layouts.auth')

@section('title', 'Masuk - REODA')

@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h1>
        <p class="text-gray-500">Masuk ke akun REODA Anda untuk melanjutkan.</p>
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
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2.5 px-4 border text-gray-900">
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-reoda hover:text-reoda-dark">Lupa password?</a>
            </div>
            <input type="password" name="password" id="password" required
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2.5 px-4 border text-gray-900">
        </div>

        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-reoda focus:ring-reoda">
            <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat Saya</label>
        </div>

        <button type="submit"
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-reoda hover:bg-reoda-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-reoda transition">
            Masuk
        </button>
    </form>

    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Atau lanjutkan dengan</span>
            </div>
        </div>

        <div class="mt-6">
            <button type="button" disabled
                class="w-full flex justify-center items-center py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-500 bg-gray-50 cursor-not-allowed">
                <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path fill="#EA4335" d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.761H12.545z"/>
                </svg>
                Google (Segera Hadir)
            </button>
        </div>
    </div>

    <p class="mt-8 text-center text-sm text-gray-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-medium text-reoda hover:text-reoda-dark">Daftar sekarang</a>
    </p>
</div>
@endsection
