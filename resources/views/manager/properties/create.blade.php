@extends('layouts.app')

@section('title', 'Tambah Properti - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">
        Tambah Lokasi Properti
    </h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.properties.index') }}">Properti /</a></li>
            <li class="font-medium text-reoda">Tambah</li>
        </ol>
    </nav>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default">
    <div class="border-b border-stroke py-4 px-6.5">
        <h3 class="font-medium text-black">Form Data Lokasi</h3>
    </div>
    
    <form action="{{ route('manager.properties.store') }}" method="POST">
        @csrf
        <div class="p-6.5">
            @if ($errors->any())
                <div class="mb-5 bg-red-50 text-red-600 p-4 rounded-md">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-2/3">
                    <label class="mb-2.5 block text-black font-medium">Nama Properti <span class="text-meta-1">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Kos Bunga Mawar" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>

                <div class="w-full xl:w-1/3">
                    <label class="mb-2.5 block text-black font-medium">Jenis <span class="text-meta-1">*</span></label>
                    <select name="type" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda">
                        <option value="">Pilih Jenis</option>
                        <option value="kos" {{ old('type') == 'kos' ? 'selected' : '' }}>Kos-kosan</option>
                        <option value="kontrakan" {{ old('type') == 'kontrakan' ? 'selected' : '' }}>Kontrakan</option>
                        <option value="apartemen" {{ old('type') == 'apartemen' ? 'selected' : '' }}>Apartemen / Unit</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="mb-2.5 block text-black font-medium">Deskripsi (Opsional)</label>
                <textarea name="description" rows="4" placeholder="Tuliskan deskripsi umum mengenai properti ini..." class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda">{{ old('description') }}</textarea>
            </div>

            <h4 class="mb-4 font-bold text-black border-b border-gray-200 pb-2">Informasi Alamat</h4>

            <div class="mb-4.5">
                <label class="mb-2.5 block text-black font-medium">Alamat Lengkap (Jalan, No) <span class="text-meta-1">*</span></label>
                <textarea name="address" rows="2" placeholder="Contoh: Jl. Sudirman No 123" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda">{{ old('address') }}</textarea>
            </div>

            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Provinsi <span class="text-meta-1">*</span></label>
                    <input type="text" name="province" value="{{ old('province') }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Kota/Kabupaten <span class="text-meta-1">*</span></label>
                    <input type="text" name="city" value="{{ old('city') }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
            </div>

            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Kecamatan <span class="text-meta-1">*</span></label>
                    <input type="text" name="district" value="{{ old('district') }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Desa/Kelurahan <span class="text-meta-1">*</span></label>
                    <input type="text" name="village" value="{{ old('village') }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
            </div>

            <div class="mb-6 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">RT/RW (Opsional)</label>
                    <input type="text" name="rt_rw" value="{{ old('rt_rw') }}" placeholder="Contoh: 001/002" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Kode Pos <span class="text-meta-1">*</span></label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-reoda p-3 font-medium text-white hover:bg-reoda-dark transition">
                Simpan Properti
            </button>
        </div>
    </form>
</div>
@endsection
