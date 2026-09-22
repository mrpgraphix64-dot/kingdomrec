@extends('layouts.app')

@section('title', 'Photo & Video Gallery | Kingdom Recruitments')

@section('content')
<div x-data="publicGalleryManager()"
     @keydown.right.window="nextImage()"
     @keydown.left.window="prevImage()"
     @keydown.escape.window="closeLightbox()"
     class="bg-background-light text-slate-700 min-h-screen pb-20 relative transition-colors duration-300">

    <!-- Hero Section (Aligned with Meet Our Team rounded layout exactly) -->
    <div class="relative w-[95%] mx-auto rounded-3xl overflow-hidden shadow-2xl bg-kingdom-navy pt-24 pb-8 md:pt-36 md:pb-12 mt-0 mb-8 lg:mb-16">
        <!-- Gold/Navy Gradient overlay + Radial Dot Pattern -->
        <div class="absolute inset-0 bg-gradient-to-tr from-kingdom-navy via-slate-900 to-black opacity-95 z-0"></div>
        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#B89955_1px,transparent_1px)] bg-[size:24px_24px] animate-pulse z-0"></div>
        
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center z-10">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-kingdom-red text-xs font-bold uppercase tracking-wider mb-4 border border-white/20 backdrop-blur-sm">Kingdom Recruitments</span>
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-kingdom-red to-rose-400 tracking-tight mb-4 max-w-none leading-tight w-full">
                Gallery
            </h1>
            <p class="text-slate-200 text-lg md:text-xl max-w-2xl font-medium leading-relaxed">
                Step inside our journey. Explore recent corporate events, recruitment drives, training sessions, team moments, and key highlights.
            </p>
        </div>
    </div>

    <!-- Gallery Container (Expanded 1400-1500px desktop layout) -->
    <div class="max-w-[1450px] mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        
        <!-- Filter Pills directly below Hero (24px-32px spacing) -->
        <div class="flex items-center justify-start gap-3 mb-8 flex-wrap">
            <button @click="setTypeFilter('')"
                    class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 border shadow-sm"
                    :class="activeType === '' ? 'bg-kingdom-gold text-kingdom-navy border-kingdom-gold shadow-kingdom-gold/20 scale-105' : 'bg-kingdom-navy text-slate-300 border-white/10 hover:bg-slate-800 hover:text-white hover:scale-105 active:scale-95'">
                All Media
            </button>
            <button @click="setTypeFilter('image')"
                    class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 border shadow-sm flex items-center gap-1.5"
                    :class="activeType === 'image' ? 'bg-kingdom-gold text-kingdom-navy border-kingdom-gold shadow-kingdom-gold/20 scale-105' : 'bg-kingdom-navy text-slate-300 border-white/10 hover:bg-slate-800 hover:text-white hover:scale-105 active:scale-95'">
                <span class="material-symbols-outlined text-[15px] font-bold">image</span>
                Photos
            </button>
            <button @click="setTypeFilter('video')"
                    class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 border shadow-sm flex items-center gap-1.5"
                    :class="activeType === 'video' ? 'bg-kingdom-gold text-kingdom-navy border-kingdom-gold shadow-kingdom-gold/20 scale-105' : 'bg-kingdom-navy text-slate-300 border-white/10 hover:bg-slate-800 hover:text-white hover:scale-105 active:scale-95'">
                <span class="material-symbols-outlined text-[15px] font-bold">movie</span>
                Videos
            </button>
        </div>

        <!-- Empty State -->
        <div x-show="filteredImages.length === 0" 
             class="max-w-md mx-auto text-center py-20 bg-white border border-slate-200 rounded-[18px] p-8 shadow-md">
            <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <span class="material-symbols-outlined text-3xl text-[#B89955]">hide_image</span>
            </div>
            <h3 class="text-sm font-bold text-slate-800">No gallery items available.</h3>
            <p class="text-xs text-slate-500 mt-1.5 max-w-xs mx-auto leading-relaxed">Photos and videos will appear here once published.</p>
        </div>

        <!-- True Masonry Grid (Equal Gaps, Custom Columns) -->
        <div x-show="filteredImages.length > 0"
             class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6">
            
            <template x-for="(img, index) in filteredImages" :key="img.id">
                <div class="break-inside-avoid bg-white rounded-[18px] overflow-hidden border border-slate-200 shadow-lg hover:shadow-2xl hover:shadow-kingdom-gold/10 hover:-translate-y-2 transition-all duration-500 group/card cursor-pointer relative"
                     @click="openLightbox(index)">
                    
                    <!-- Media container -->
                    <div class="relative overflow-hidden w-full h-auto bg-slate-100">
                        
                        <!-- Image item (Thumbnails first for speed, lazy loaded) -->
                        <template x-if="img.type === 'image'">
                            <img :src="img.thumbnail_url"
                                 :alt="img.alt_text || img.title"
                                 class="w-full h-auto block group-hover/card:scale-105 transition-transform duration-700 ease-out"
                                 loading="lazy">
                        </template>

                        <!-- Video item (Preloaded preview frame) -->
                        <template x-if="img.type === 'video'">
                            <div class="w-full h-auto relative">
                                <template x-if="img.thumbnail_path">
                                    <img :src="img.thumbnail_url" :alt="img.alt_text || img.title" class="w-full h-auto block group-hover/card:scale-105 transition-transform duration-700 ease-out">
                                </template>
                                <template x-if="!img.thumbnail_path">
                                    <video :src="img.url + '#t=0.001'" preload="metadata" muted autoplay loop playsinline class="w-full h-auto block group-hover/card:scale-105 transition-transform duration-700 ease-out pointer-events-none"></video>
                                </template>
                                <!-- Play Overlay in the center -->
                                <div class="absolute inset-0 bg-black/10 flex items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-white/95 text-kingdom-navy flex items-center justify-center shadow-lg group-hover/card:scale-110 transition-transform duration-300">
                                        <span class="material-symbols-outlined text-2xl font-bold ml-1">play_arrow</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Dark Hover Info Overlay (Category badge, title, and action icon appear on hover) -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-5 text-left">
                            
                            <!-- Category Badge -->
                            <div class="mb-2">
                                <span class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-wide bg-kingdom-gold text-kingdom-navy"
                                      x-text="img.type === 'video' ? 'VIDEO' : 'IMAGE'">
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-extrabold text-white leading-tight mb-2 truncate" x-text="img.title"></h3>

                            <!-- View action -->
                            <div class="flex items-center gap-1.5 text-kingdom-gold hover:text-white transition-colors duration-300 text-xs font-bold uppercase tracking-wider shrink-0 select-none">
                                <span class="material-symbols-outlined text-sm font-bold" x-text="img.type === 'video' ? 'play_circle' : 'zoom_in'"></span>
                                View <span x-text="img.type"></span>
                                <span class="material-symbols-outlined text-sm font-bold ml-0.5">arrow_forward</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Load More Button -->
        <div x-show="nextPageUrl" class="text-center mt-12">
            <button @click="loadMore()"
                    :disabled="loading"
                    class="bg-kingdom-navy hover:bg-slate-800 text-white font-extrabold text-xs px-8 py-3.5 rounded-full shadow-lg transition-all duration-300 active:scale-95 flex items-center gap-2 mx-auto disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="loading">
                    <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></div>
                </template>
                <span x-text="loading ? 'Loading...' : 'Load More'"></span>
            </button>
        </div>

    </div>

    <!-- Premium Fullscreen Lightbox Overlay -->
    <div x-show="lightboxOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @touchstart="touchStart($event)"
         @touchend="touchEnd($event)"
         class="fixed inset-0 z-[150] bg-slate-950/98 backdrop-blur-xl flex flex-col justify-between"
         style="display: none;">
        
        <!-- Lightbox Top bar (Title, Category Album, Counter, Close) -->
        <div class="flex items-center justify-between p-5 text-white/80 border-b border-white/5 relative z-[160] shrink-0">
            <div class="text-left min-w-0 flex flex-col gap-0.5">
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-kingdom-gold bg-white/5 border border-white/10 px-2 py-0.5 rounded capitalize" x-text="activeImage().type"></span>
                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-white/40" x-text="activeImage().type === 'video' ? 'VIDEO ALBUM' : 'PHOTO ALBUM'"></span>
                </div>
                <h3 class="text-xs sm:text-sm font-bold text-white truncate max-w-[240px] sm:max-w-md" x-text="activeImage().title"></h3>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-slate-300 bg-white/5 border border-white/10 px-3 py-1 rounded-xl" x-text="`${activeImageIndex + 1} / ${filteredImages.length}`"></span>
                <button @click="closeLightbox()" class="p-2 rounded-xl bg-white/5 hover:bg-kingdom-gold hover:text-kingdom-navy transition-all text-slate-300 flex items-center justify-center border border-white/10 hover:border-kingdom-gold shadow-md hover:scale-105 active:scale-95" title="Close (Esc)">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
        </div>

        <!-- Lightbox Content Area -->
        <div class="flex-1 flex items-center justify-center relative p-4 max-h-[75vh]">
            
            <!-- Left Navigator Button -->
            <button @click="prevImage()"
                    class="absolute left-6 z-[160] p-3.5 rounded-full bg-white/5 hover:bg-kingdom-gold text-white/80 hover:text-kingdom-navy hover:scale-105 active:scale-95 transition-all border border-white/10 hover:border-kingdom-gold shadow-2xl flex items-center justify-center hidden sm:flex"
                    title="Previous (Left Arrow)">
                <span class="material-symbols-outlined text-2xl font-bold">chevron_left</span>
            </button>

            <!-- Media viewport -->
            <div class="h-full w-full flex items-center justify-center relative">
                <div x-show="imageLoading" class="absolute inset-0 flex items-center justify-center text-white/50 z-10">
                    <div class="w-8 h-8 border-2 border-kingdom-gold border-t-transparent rounded-full animate-spin"></div>
                </div>

                <!-- Full res Image item -->
                <template x-if="activeImage().type === 'image'">
                    <img :src="activeImage().url"
                         :alt="activeImage().alt_text || activeImage().title"
                         @load="imageLoading = false"
                         class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl border border-white/5 select-none animate-in zoom-in-95 duration-350"
                         :key="activeImage().id">
                </template>

                <!-- Full video item -->
                <template x-if="activeImage().type === 'video'">
                    <video :src="activeImage().url"
                           controls
                           autoplay
                           playsinline
                           @loadedmetadata="imageLoading = false"
                           class="max-w-full max-h-full rounded-2xl shadow-2xl border border-white/5 select-none animate-in zoom-in-95 duration-350"
                           :key="activeImage().id"></video>
                </template>
            </div>

            <!-- Right Navigator Button -->
            <button @click="nextImage()"
                    class="absolute right-6 z-[160] p-3.5 rounded-full bg-white/5 hover:bg-kingdom-gold text-white/80 hover:text-kingdom-navy hover:scale-105 active:scale-95 transition-all border border-white/10 hover:border-kingdom-gold shadow-2xl flex items-center justify-center hidden sm:flex"
                    title="Next (Right Arrow)">
                <span class="material-symbols-outlined text-2xl font-bold">chevron_right</span>
            </button>
        </div>

        <!-- Lightbox Bottom Description drawer -->
        <div class="p-6 bg-slate-900/60 border-t border-white/5 relative z-[160] shrink-0 text-center backdrop-blur-md">
            <template x-if="activeImage().description">
                <p class="text-xs sm:text-sm text-slate-300 font-medium max-w-xl mx-auto leading-relaxed" x-text="activeImage().description"></p>
            </template>
            <template x-if="!activeImage().description">
                <p class="text-xs text-slate-500 font-medium italic">No description provided for this item.</p>
            </template>
        </div>

    </div>

</div>

<!-- Alpine Public Gallery JS Logic -->
<script data-navigate-once>
window.publicGalleryManager = function publicGalleryManager() {
    return {
        // Items loaded from controller
        images: @js($items->items()),
        nextPageUrl: @js($items->nextPageUrl()),
        
        // UI states
        loading: false,
        lightboxOpen: false,
        activeImageIndex: 0,
        imageLoading: false,
        activeType: '', // type filtering state: '', 'image', 'video'
        
        // Touch swipe variables
        touchStartX: 0,

        init() {
            // Re-render Lucide icons if any
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        // Client side filtering for photos vs videos to ensure instant tab transitions
        get filteredImages() {
            if (this.activeType === '') {
                return this.images;
            }
            return this.images.filter(img => img.type === this.activeType);
        },

        setTypeFilter(type) {
            this.activeType = type;
        },

        // Fetch subsequent pages dynamically
        loadMore() {
            if (!this.nextPageUrl || this.loading) return;

            this.loading = true;

            fetch(this.nextPageUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                this.images = [...this.images, ...data.data];
                this.nextPageUrl = data.next_page_url;
            })
            .catch(() => alert('Failed to load more media.'))
            .finally(() => {
                this.loading = false;
            });
        },

        // Active image selection helper
        activeImage() {
            return this.filteredImages[this.activeImageIndex] || { id: null, title: '', file_path: '', alt_text: '', description: '', type: 'image' };
        },

        // Open Lightbox
        openLightbox(index) {
            this.activeImageIndex = index;
            this.imageLoading = true;
            this.lightboxOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeLightbox() {
            this.lightboxOpen = false;
            document.body.style.overflow = '';
        },

        // Lightbox Navigation
        nextImage() {
            if (!this.lightboxOpen) return;
            this.imageLoading = true;
            this.activeImageIndex = (this.activeImageIndex + 1) % this.filteredImages.length;
        },

        prevImage() {
            if (!this.lightboxOpen) return;
            this.imageLoading = true;
            this.activeImageIndex = (this.activeImageIndex - 1 + this.filteredImages.length) % this.filteredImages.length;
        },

        // Mobile Touch Gestures
        touchStart(e) {
            this.touchStartX = e.changedTouches[0].screenX;
        },

        touchEnd(e) {
            let diff = e.changedTouches[0].screenX - this.touchStartX;
            if (diff > 55) {
                this.prevImage();
            } else if (diff < -55) {
                this.nextImage();
            }
        }
    };
}
</script>
@endsection
