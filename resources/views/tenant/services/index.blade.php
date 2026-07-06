@extends('layouts.app')
@section('title', 'Layanan - REODA')
@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black">Layanan Penyewa</h2>
    <nav><ol class="flex items-center gap-2">
        <li><a class="font-medium hover:text-reoda" href="{{ route('tenant.dashboard') }}">Dashboard /</a></li>
        <li class="font-medium text-reoda">Layanan</li>
    </ol></nav>
</div>

@if(session('success'))
<div class="mb-5 rounded-md border-l-4 border-success-500 bg-success-50 px-5 py-3 text-sm font-medium text-success-700">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-5 rounded-md border-l-4 border-error-400 bg-error-50 px-5 py-3 text-sm font-medium text-error-700">{{ session('error') }}</div>
@endif

@if(!$contract)
<div class="rounded-xl border border-stroke bg-white shadow-sm p-10 text-center">
    <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <h3 class="font-bold text-black mb-2">Tidak Ada Kontrak Aktif</h3>
    <p class="text-sm text-gray-500">Fitur layanan hanya tersedia bagi penyewa yang memiliki kontrak aktif.</p>
</div>
@else

{{-- Hunian Info Card --}}
<div class="rounded-xl border border-stroke bg-white shadow-sm p-6 mb-6">
    <h4 class="font-bold text-black mb-4">Informasi Hunian Aktif</h4>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><p class="text-xs text-gray-400 mb-0.5">Properti</p><p class="font-semibold">{{ $contract->unit->property->name }}</p></div>
        <div><p class="text-xs text-gray-400 mb-0.5">Unit</p><p class="font-semibold">{{ $contract->unit->unit_code }}</p></div>
        <div><p class="text-xs text-gray-400 mb-0.5">Kontrak Berakhir</p><p class="font-semibold">{{ $contract->end_date ? $contract->end_date->format('d M Y') : 'Tanpa Batas' }}</p></div>
        <div><p class="text-xs text-gray-400 mb-0.5">Pengelola</p>
            <p class="font-semibold">{{ $contract->unit->property->manager->name ?? '-' }}</p>
            @if($contract->unit->property->manager->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$contract->unit->property->manager->phone) }}" target="_blank" class="text-xs text-success-600 hover:underline">WA: {{ $contract->unit->property->manager->phone }}</a>
            @endif
        </div>
    </div>
    @if($contract->unit->facilities->count() > 0)
    <div class="mt-4 pt-4 border-t border-stroke">
        <p class="text-xs text-gray-400 mb-2">Fasilitas Unit</p>
        <div class="flex flex-wrap gap-2">
            @foreach($contract->unit->facilities as $f)
            <span class="inline-flex rounded-full border border-stroke px-3 py-1 text-xs font-medium text-gray-600">{{ $f->name }}</span>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Service Tabs --}}
<div x-data="{ tab: 'facility' }">
    <div class="flex flex-wrap gap-2 mb-5">
        <button @click="tab='facility'" :class="tab==='facility'?'bg-reoda text-white':'bg-white text-gray-600 border border-stroke hover:bg-gray-50'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">Permintaan Fasilitas</button>
        <button @click="tab='emergency'" :class="tab==='emergency'?'bg-error-600 text-white':'bg-white text-gray-600 border border-stroke hover:bg-gray-50'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">Laporan Darurat</button>
        <button @click="tab='contract'" :class="tab==='contract'?'bg-[#003648] text-white':'bg-white text-gray-600 border border-stroke hover:bg-gray-50'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">Perpanjang / Batalkan Kontrak</button>
    </div>

    {{-- Facility Request --}}
    <div x-show="tab==='facility'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Ajukan Permintaan Fasilitas</h4></div>
            <form action="{{ route('tenant.services.facility') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Judul Permintaan <span class="text-error-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Penambahan kipas angin" class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Deskripsi <span class="text-error-500">*</span></label>
                    <textarea name="description" rows="4" required placeholder="Jelaskan kebutuhan Anda secara detail..." class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="rounded-lg bg-reoda px-6 py-2.5 font-semibold text-white hover:bg-reoda-dark transition">Kirim Permintaan</button>
            </form>
        </div>
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Riwayat Permintaan ({{ $facilityRequests->count() }})</h4></div>
            @if($facilityRequests->count() > 0)
            <div class="divide-y divide-stroke">
                @foreach($facilityRequests as $fr)
                @php $badge = match($fr->status){
                    'approved'=>'bg-success-50 text-success-700','rejected'=>'bg-error-50 text-error-700',
                    'done'=>'bg-reoda/10 text-reoda','reviewed'=>'bg-yellow-50 text-yellow-700',
                    default=>'bg-gray-100 text-gray-600'};
                @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-black text-sm">{{ $fr->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $fr->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge }} shrink-0">{{ ucfirst($fr->status) }}</span>
                    </div>
                    @if($fr->manager_response)<p class="text-xs text-gray-500 mt-1.5 italic">Respons: {{ $fr->manager_response }}</p>@endif
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-sm text-gray-400">Belum ada permintaan fasilitas.</div>
            @endif
        </div>
    </div>

    {{-- Emergency Report --}}
    <div x-show="tab==='emergency'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h4 class="font-bold text-black">Laporkan Kejadian Darurat</h4>
            </div>
            <form action="{{ route('tenant.services.emergency') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Kategori <span class="text-error-500">*</span></label>
                        <select name="category" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                            <option value="electricity">Listrik</option>
                            <option value="water">Air / PDAM</option>
                            <option value="structural">Bangunan / Struktur</option>
                            <option value="security">Keamanan</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Prioritas <span class="text-error-500">*</span></label>
                        <select name="priority" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                            <option value="low">Rendah</option>
                            <option value="medium" selected>Sedang</option>
                            <option value="high">Tinggi</option>
                            <option value="critical">Kritis</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Judul <span class="text-error-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Atap bocor di kamar mandi" class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Deskripsi <span class="text-error-500">*</span></label>
                    <textarea name="description" rows="3" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Foto (Opsional)</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-reoda/10 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-reoda hover:file:bg-reoda/20">
                </div>
                <button type="submit" class="rounded-lg bg-error-600 px-6 py-2.5 font-semibold text-white hover:bg-error-700 transition">Kirim Laporan</button>
            </form>
        </div>
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Riwayat Laporan ({{ $emergencyReports->count() }})</h4></div>
            @if($emergencyReports->count() > 0)
            <div class="divide-y divide-stroke">
                @foreach($emergencyReports as $er)
                @php $badge = match($er->status){
                    'resolved'=>'bg-success-50 text-success-700','closed'=>'bg-gray-100 text-gray-500',
                    'in_progress'=>'bg-yellow-50 text-yellow-700',default=>'bg-error-50 text-error-700'};
                    $prio = match($er->priority){
                    'critical'=>'text-error-600','high'=>'text-orange-500','medium'=>'text-yellow-600',default=>'text-gray-400'};
                @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-black text-sm">{{ $er->title }}</p>
                            <p class="text-xs {{ $prio }}">{{ ucfirst($er->priority) }} · {{ $er->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge }} shrink-0">{{ str_replace('_',' ',ucfirst($er->status)) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-sm text-gray-400">Belum ada laporan darurat.</div>
            @endif
        </div>
    </div>

    {{-- Contract Request --}}
    <div x-show="tab==='contract'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Perpanjang / Batalkan Kontrak</h4></div>
            <form action="{{ route('tenant.services.contract') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Jenis Permintaan <span class="text-error-500">*</span></label>
                    <select name="type" required class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                        <option value="renewal">Perpanjangan Kontrak</option>
                        <option value="termination">Pembatalan / Mengakhiri Kontrak</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal yang Diinginkan (Opsional)</label>
                    <input type="date" name="requested_date" class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Alasan / Keterangan <span class="text-error-500">*</span></label>
                    <textarea name="reason" rows="4" required placeholder="Sampaikan alasan atau detail permintaan Anda..." class="w-full rounded-lg border border-stroke py-2.5 px-4 text-sm outline-none focus:border-reoda transition">{{ old('reason') }}</textarea>
                </div>
                <button type="submit" class="rounded-lg bg-[#003648] px-6 py-2.5 font-semibold text-white hover:opacity-90 transition">Kirim Permintaan</button>
            </form>
        </div>
        <div class="rounded-xl border border-stroke bg-white shadow-sm">
            <div class="border-b border-stroke px-6 py-4"><h4 class="font-bold text-black">Riwayat Permintaan ({{ $contractRequests->count() }})</h4></div>
            @if($contractRequests->count() > 0)
            <div class="divide-y divide-stroke">
                @foreach($contractRequests as $cr)
                @php $badge = match($cr->status){
                    'approved'=>'bg-success-50 text-success-700','rejected'=>'bg-error-50 text-error-700',
                    default=>'bg-gray-100 text-gray-600'};
                @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-black text-sm">{{ $cr->type === 'renewal' ? 'Perpanjangan' : 'Pembatalan' }} Kontrak</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $cr->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge }} shrink-0">{{ ucfirst($cr->status) }}</span>
                    </div>
                    @if($cr->manager_response)<p class="text-xs text-gray-500 mt-1.5 italic">Respons: {{ $cr->manager_response }}</p>@endif
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-sm text-gray-400">Belum ada permintaan kontrak.</div>
            @endif
        </div>
    </div>
</div>
@endif
@endsection
