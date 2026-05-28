@extends('layouts.app')

@section('title', 'Edit Unit - REODA')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">
        Edit Unit / Kamar
    </h2>
    <nav>
        <ol class="flex items-center gap-2">
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.dashboard') }}">Dashboard /</a></li>
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.properties.index') }}">Properti /</a></li>
            <li><a class="font-medium hover:text-reoda" href="{{ route('manager.properties.show', $unit->property_id) }}">{{ $unit->property->name }} /</a></li>
            <li class="font-medium text-reoda">Edit Unit</li>
        </ol>
    </nav>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default">
    <div class="border-b border-stroke py-4 px-6.5">
        <h3 class="font-medium text-black">Form Edit Data Unit - {{ $unit->unit_code }}</h3>
    </div>
    
    <form action="{{ route('manager.units.update', $unit) }}" method="POST">
        @csrf
        @method('PUT')
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
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Kode Unit <span class="text-meta-1">*</span></label>
                    <input type="text" name="unit_code" value="{{ old('unit_code', $unit->unit_code) }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Nama / Kategori Unit <span class="text-meta-1">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $unit->name) }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
            </div>

            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/3">
                    <label class="mb-2.5 block text-black font-medium">Tipe / Ukuran <span class="text-meta-1">*</span></label>
                    <input type="text" name="type" value="{{ old('type', $unit->type) }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
                <div class="w-full xl:w-1/3">
                    <label class="mb-2.5 block text-black font-medium">Harga Sewa (Rp) / Bulan <span class="text-meta-1">*</span></label>
                    <input type="number" name="rent_price" value="{{ old('rent_price', $unit->rent_price) }}" min="0" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
                <div class="w-full xl:w-1/3">
                    <label class="mb-2.5 block text-black font-medium">Luas (m²)</label>
                    <input type="number" name="area_sqm" value="{{ old('area_sqm', $unit->area_sqm) }}" min="0" step="0.1" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
            </div>

            <div class="mb-6 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Lantai</label>
                    <input type="text" name="floor" value="{{ old('floor', $unit->floor) }}" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda" />
                </div>
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black font-medium">Status <span class="text-meta-1">*</span></label>
                    <select name="status" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda">
                        <option value="available" {{ old('status', $unit->status) == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                        <option value="rented" {{ old('status', $unit->status) == 'rented' ? 'selected' : '' }}>Disewa (Rented)</option>
                        <option value="maintenance" {{ old('status', $unit->status) == 'maintenance' ? 'selected' : '' }}>Perbaikan (Maintenance)</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="mb-2.5 block text-black font-medium">Deskripsi / Fasilitas Tambahan</label>
                <textarea name="description" rows="3" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-reoda active:border-reoda">{{ old('description', $unit->description) }}</textarea>
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-reoda p-3 font-medium text-white hover:bg-reoda-dark transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
