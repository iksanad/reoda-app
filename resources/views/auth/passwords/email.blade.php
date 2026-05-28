@extends('layouts.auth')

@section('title', 'Lupa Password - REODA')

@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Lupa Password?</h1>
        <p class="text-gray-500">Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset password.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 text-green-700 p-3 rounded-lg text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-sm border border-red-200">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="w-full rounded-lg border-gray-300 focus:border-reoda focus:ring-reoda py-2.5 px-4 border text-gray-900">
        </div>

        <button type="submit"
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-reoda hover:bg-reoda-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-reoda transition">
            Kirim Tautan Reset Password
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-gray-600">
        Ingat password Anda?
        <a href="{{ route('login') }}" class="font-medium text-reoda hover:text-reoda-dark">Masuk di sini</a>
    </p>
</div>
@endsection
