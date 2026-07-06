{{--
    Komponen Galeri Gambar yang bisa digunakan untuk Properti maupun Unit.

    Props:
        $mediaItems  — Collection of PropertyMedia
        $uploadRoute — Route name untuk upload (string)
        $uploadParams — Route params array (e.g. ['property' => $property] or ['unit' => $unit])
        $maxImages   — int, default 5
        $title       — string, judul section
--}}

@php
    $maxImages  = $maxImages ?? 5;
    $count      = $mediaItems->count();
    $remaining  = $maxImages - $count;
    $title      = $title ?? 'Galeri Foto';
@endphp

<div class="rounded-xl border border-stroke bg-white shadow-sm overflow-hidden" id="gallery-section">
    {{-- Header --}}
    <div class="border-b border-stroke px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h4 class="font-bold text-black">{{ $title }}</h4>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $count >= $maxImages ? 'bg-red-100 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                {{ $count }}/{{ $maxImages }} foto
            </span>
        </div>
        @if($count < $maxImages)
        <label for="media-upload-trigger" class="cursor-pointer inline-flex items-center gap-2 rounded-lg bg-reoda px-4 py-2 text-sm font-semibold text-white hover:bg-reoda-dark transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Upload Foto
        </label>
        @endif
    </div>

    {{-- Upload Form (hidden, triggered by label) --}}
    @if($count < $maxImages)
    <form action="{{ route($uploadRoute, $uploadParams) }}" method="POST" enctype="multipart/form-data" id="media-upload-form">
        @csrf
        <input type="file" id="media-upload-trigger" name="images[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp"
               class="hidden" onchange="previewAndSubmit(this)">
    </form>
    @endif

    {{-- Preview area (drag/upload feedback) --}}
    <div id="upload-preview" class="hidden px-6 pt-4">
        <div class="flex items-center gap-3 rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">
            <svg class="w-5 h-5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <span id="upload-preview-text">Mengupload gambar...</span>
        </div>
    </div>

    {{-- Gallery Grid --}}
    <div class="p-6">
        @if($count === 0)
        <div class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-reoda hover:bg-blue-50/30 transition group"
             onclick="document.getElementById('media-upload-trigger')?.click()">
            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-3 group-hover:bg-blue-100 transition">
                <svg class="w-7 h-7 text-gray-400 group-hover:text-reoda transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-gray-500 group-hover:text-reoda transition">Belum ada foto</p>
            <p class="text-xs text-gray-400 mt-1">Klik untuk upload (maks. {{ $maxImages }} foto, JPG/PNG/WebP, 5 MB)</p>
        </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @foreach($mediaItems as $media)
            <div class="group relative rounded-xl overflow-hidden border-2 {{ $media->is_primary ? 'border-reoda shadow-md' : 'border-gray-200' }} bg-gray-50 aspect-square">
                {{-- Image --}}
                <img src="{{ $media->url }}"
                     alt="{{ $media->file_name }}"
                     class="w-full h-full object-cover cursor-pointer transition group-hover:scale-105 duration-300"
                     onclick="openLightbox('{{ $media->url }}')">

                {{-- Primary Badge --}}
                @if($media->is_primary)
                <span class="absolute top-2 left-2 bg-reoda text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
                    Utama
                </span>
                @endif

                {{-- Overlay actions --}}
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition flex items-end justify-center pb-3 gap-2 opacity-0 group-hover:opacity-100">
                    @if(!$media->is_primary)
                    <form action="{{ route('manager.media.set-primary', $media) }}" method="POST">
                        @csrf
                        <button type="submit" title="Jadikan Gambar Utama"
                                class="bg-white/90 hover:bg-white text-gray-700 rounded-lg px-2.5 py-1.5 text-xs font-semibold shadow transition">
                            ⭐ Utama
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('manager.media.destroy', $media) }}" method="POST"
                          onsubmit="return confirm('Hapus gambar ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Hapus Gambar"
                                class="bg-red-500/90 hover:bg-red-600 text-white rounded-lg px-2.5 py-1.5 text-xs font-semibold shadow transition">
                            🗑 Hapus
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            {{-- Add more slot --}}
            @if($count < $maxImages)
            <div class="relative rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 aspect-square flex flex-col items-center justify-center cursor-pointer hover:border-reoda hover:bg-blue-50/30 transition"
                 onclick="document.getElementById('media-upload-trigger')?.click()">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                <p class="text-[10px] text-gray-400 mt-1">Tambah</p>
                <p class="text-[10px] text-gray-400">{{ $remaining }} slot</p>
            </div>
            @endif
        </div>
        @endif

        <p class="text-xs text-gray-400 mt-4">Format: JPG, PNG, WebP. Maks 5 MB per gambar. Maks {{ $maxImages }} foto total.</p>
    </div>
</div>

{{-- Lightbox --}}
<div id="gallery-lightbox" class="fixed inset-0 z-[9999] bg-black/80 hidden items-center justify-center p-4" onclick="closeLightbox()">
    <button class="absolute top-4 right-4 text-white text-3xl font-bold leading-none hover:text-gray-300" onclick="closeLightbox()">×</button>
    <img id="lightbox-img" src="" alt="" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl object-contain" onclick="event.stopPropagation()">
</div>

@push('scripts')
<script>
function openLightbox(url) {
    document.getElementById('lightbox-img').src = url;
    document.getElementById('gallery-lightbox').classList.remove('hidden');
    document.getElementById('gallery-lightbox').classList.add('flex');
}
function closeLightbox() {
    document.getElementById('gallery-lightbox').classList.add('hidden');
    document.getElementById('gallery-lightbox').classList.remove('flex');
    document.getElementById('lightbox-img').src = '';
}
function previewAndSubmit(input) {
    if (!input.files || input.files.length === 0) return;
    const preview = document.getElementById('upload-preview');
    const text    = document.getElementById('upload-preview-text');
    preview.classList.remove('hidden');
    text.textContent = `Mengupload ${input.files.length} gambar...`;
    document.getElementById('media-upload-form').submit();
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
@endpush
