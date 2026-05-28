@extends('layouts.auth')

@section('title', 'Reset Password - REODA')

@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h1>
        <p class="text-gray-500">Silakan masukkan password baru Anda.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-sm border border-red-200">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}" required autofocus readonly
                class="w-full rounded-lg border-gray-300 bg-gray-50 py-2.5 px-4 border text-gray-500 cursor-not-allowed">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <input type="password" name="password" id="password" required
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2.5 px-4 border text-gray-900">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2.5 px-4 border text-gray-900">
        </div>

        <button type="submit"
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-reoda hover:bg-reoda-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-reoda transition">
            Reset Password
        </button>
    </form>
</div>
@endsection
