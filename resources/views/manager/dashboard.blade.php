@extends('layouts.app')

@section('title', 'Dashboard Pengelola - REODA')

@section('content')

{{-- Greeting --}}
<div class="mb-6">
    <h1 class="text-3xl font-extrabold text-reoda-dark">Hi, {{ auth()->user()->name }}!</h1>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Total Unit --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm flex items-center gap-6">
        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-reoda-lighter shrink-0">
            <svg class="w-8 h-8 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-reoda-dark uppercase tracking-wide">Total Unit Hunian</p>
            <h4 class="text-4xl font-extrabold text-reoda-dark mt-1">{{ $totalUnits }}</h4>
            <p class="text-xs font-medium text-gray-500 mt-1">Unit Terdaftar</p>
        </div>
    </div>

    {{-- Unit Disewa --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm flex items-center gap-6">
        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-reoda-lightest shrink-0">
            <svg class="w-8 h-8 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-reoda-dark uppercase tracking-wide">Unit Disewa</p>
            <h4 class="text-4xl font-extrabold text-reoda-dark mt-1">{{ $rentedUnits }}</h4>
            <p class="text-xs font-medium text-gray-500 mt-1">Unit</p>
        </div>
    </div>

    {{-- Total Pendapatan --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm flex items-center gap-6">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-reoda-lighter shrink-0">
            <svg class="w-8 h-8 text-reoda-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-reoda-dark uppercase tracking-wide">Total Pendapatan</p>
            <h4 class="text-2xl font-extrabold text-reoda-dark mt-2">Rp. {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
            <p class="text-xs font-medium text-gray-500 mt-1">Bulan Ini</p>
        </div>
    </div>
</div>

{{-- Lokasi Hunian Table --}}
<div class="mb-4">
    <h3 class="text-xl font-extrabold text-reoda-dark">Lokasi Hunian</h3>
</div>

<div class="rounded-2xl bg-white shadow-sm p-4 sm:p-6 overflow-hidden">
    @if($properties->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px] border-separate" style="border-spacing: 0 8px;">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-l-full">Lokasi</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Total Unit</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark mx-1">Unit Disewa</th>
                    <th class="px-6 py-3 bg-reoda-lightest text-center text-sm font-bold text-reoda-dark rounded-r-full">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($properties as $property)
                <tr class="even:bg-[#e6f4f1] odd:bg-reoda-lightest/20">
                    <td class="px-6 py-4 rounded-l-full">
                        <p class="font-extrabold text-reoda-dark text-sm">{{ $property->name }}<span class="font-medium text-gray-500 text-xs ml-1">, {{ $property->city }}</span></p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="font-bold text-reoda-dark text-sm">{{ $property->units_count }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="font-bold text-reoda-dark text-sm">{{ $property->rented_units_count }}</p>
                    </td>
                    <td class="px-6 py-4 rounded-r-full text-center">
                        <p class="font-bold text-reoda-dark text-sm">Rp. {{ number_format($property->revenue, 0, ',', '.') }}</p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-12 text-center">
        <p class="text-gray-500">Belum ada properti terdaftar.</p>
        <a href="{{ route('manager.properties.create') }}" class="inline-flex rounded-lg bg-reoda py-2 px-5 font-semibold text-white hover:bg-reoda-dark text-sm transition mt-4">Tambah Properti Pertama</a>
    </div>
    @endif
</div>

@endsection
