@props(['images' => [], 'alt' => 'Image', 'heightClass' => 'h-48'])

@php
    // Jika tidak ada gambar, berikan gambar fallback
    if (empty($images)) {
        $images = ['https://placehold.co/600x400/4C74AF/ffffff?text=' . urlencode($alt)];
    }
@endphp

<div x-data="{ 
        activeSlide: 0, 
        slides: {{ json_encode($images) }}, 
        lightboxOpen: false, 
        next() { this.activeSlide = (this.activeSlide === this.slides.length - 1) ? 0 : this.activeSlide + 1 },
        prev() { this.activeSlide = (this.activeSlide === 0) ? this.slides.length - 1 : this.activeSlide - 1 }
    }" 
    class="relative w-full {{ $heightClass }} overflow-hidden bg-gray-100 group">
    
    <!-- Slides -->
    <template x-for="(slide, index) in slides" :key="index">
        <img x-show="activeSlide === index" 
             x-transition.opacity.duration.300ms
             :src="slide" 
             alt="{{ $alt }}"
             @click.prevent.stop="lightboxOpen = true"
             class="absolute inset-0 w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-500">
    </template>
    
    <!-- Navigation (Only if > 1 slide) -->
    <div x-show="slides.length > 1" class="absolute inset-0 flex items-center justify-between px-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
        <button @click.prevent.stop="prev()" title="Sebelumnya" class="pointer-events-auto bg-black/40 hover:bg-black/60 text-white rounded-full p-1.5 backdrop-blur-sm transition z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click.prevent.stop="next()" title="Selanjutnya" class="pointer-events-auto bg-black/40 hover:bg-black/60 text-white rounded-full p-1.5 backdrop-blur-sm transition z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <!-- Dots -->
    <div x-show="slides.length > 1" class="absolute bottom-2 left-0 right-0 flex justify-center gap-1.5 z-10 pointer-events-none">
        <template x-for="(slide, index) in slides" :key="index">
            <button @click.prevent.stop="activeSlide = index" 
                    :class="activeSlide === index ? 'w-4 bg-reoda' : 'w-1.5 bg-white/70 hover:bg-white'"
                    class="pointer-events-auto h-1.5 rounded-full transition-all duration-300 shadow-sm"></button>
        </template>
    </div>

    <!-- Lightbox (Teleport to body) -->
    <template x-teleport="body">
        <div x-show="lightboxOpen" 
             class="fixed inset-0 z-[99999] bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm" 
             style="display: none;"
             @keydown.escape.window="lightboxOpen = false">
             
            <!-- Close button -->
            <button @click="lightboxOpen = false" class="absolute top-4 right-4 text-white hover:text-gray-300 transition p-2 z-50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            
            <!-- Prev Lighbox -->
            <button x-show="slides.length > 1" @click.stop="prev()" class="absolute left-2 md:left-6 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 p-2 z-50">
                <svg class="w-8 h-8 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <!-- Main Image with click outside to close -->
            <div class="relative max-w-full max-h-full flex items-center justify-center w-full h-full" @click="lightboxOpen = false">
                <img :src="slides[activeSlide]" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl" @click.stop="">
            </div>
            
            <!-- Next Lighbox -->
            <button x-show="slides.length > 1" @click.stop="next()" class="absolute right-2 md:right-6 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 p-2 z-50">
                <svg class="w-8 h-8 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            
            <!-- Indicators text -->
            <div x-show="slides.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white font-medium bg-black/50 px-4 py-1.5 rounded-full text-sm backdrop-blur-md z-50">
                <span x-text="activeSlide + 1"></span> / <span x-text="slides.length"></span>
            </div>
        </div>
    </template>
</div>
