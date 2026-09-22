{{-- Unified Support Widget --}}
@php
    $whatsappNumber = '447411154198'; // Kingdom Recruitments WhatsApp
    $fbUsername = 'kingdomrecruitments'; 
    $messengerUrl = 'https://m.me/' . $fbUsername;

    $isPartner = request()->is('partner/*') || request()->is('partner');
    $isAdmin = request()->is('admin/*') || request()->is('admin');

    if ($isPartner) {
        $defaultMessage = 'Hello Kingdom Recruitments! I need help with my partner portal.';
    } elseif ($isAdmin) {
        $defaultMessage = 'Hello, I need technical support for the admin dashboard.';
    } else {
        $defaultMessage = 'Hello Kingdom Recruitments! I would like to enquire about your staffing services.';
    }

    $whatsappUrl = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($defaultMessage);
@endphp

<style>
    .support-widget-shifted {
        bottom: 5.5rem !important;
    }
    @media (min-width: 640px) {
        .support-widget-shifted {
            bottom: 6.5rem !important;
        }
    }
</style>

<div x-data="unifiedSupportWidget()" 
     @scroll.window="handleScroll"
     class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[80] flex flex-col items-end gap-3 transition-all duration-300"
     :class="{ 'support-widget-shifted': hasStickyFooter }"
     x-cloak>
    
    {{-- Speed Dial Menu --}}
    <div x-show="isOpen" 
         @click.away="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="flex flex-col items-end gap-3 mb-2 origin-bottom-right">
        
        {{-- Facebook Messenger --}}
        <a href="{{ $messengerUrl }}" target="_blank" rel="noopener noreferrer" 
           class="flex items-center gap-3 group">
            <span class="bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm font-semibold px-3 py-1.5 rounded-lg shadow-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Messenger</span>
            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-[#0084FF] text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.923 1.488 5.485 3.791 7.155v3.428c0 .546.602.871 1.07.571l3.525-2.261A10.74 10.74 0 0012 20.516c5.523 0 10-4.145 10-9.258S17.523 2 12 2zm1.094 12.35l-2.73-2.91-5.32 2.91 5.86-6.22 2.82 2.91 5.23-2.91-5.86 6.22z"/>
                </svg>
            </div>
        </a>

        {{-- WhatsApp --}}
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-3 group">
            <span class="bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm font-semibold px-3 py-1.5 rounded-lg shadow-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">WhatsApp</span>
            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </div>
        </a>

        {{-- Live Chat (Bot) --}}
        <button @click="openChatbot()" class="flex items-center gap-3 group">
            <span class="bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm font-semibold px-3 py-1.5 rounded-lg shadow-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Live Chat</span>
            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-gradient-to-br from-[#B89955] to-[#9a7d3e] text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Main FAB Toggle --}}
    <button @click="isOpen = !isOpen" 
            class="h-12 w-12 sm:h-14 sm:w-14 rounded-full bg-[#0F1D33] text-white shadow-2xl hover:shadow-[#0F1D33]/40 flex items-center justify-center transition-all duration-300 relative"
            :class="{ 'scale-90 opacity-60': isScrolling, 'rotate-45 bg-slate-800': isOpen }">
        <svg x-show="!isOpen" class="w-6 h-6 sm:w-7 sm:h-7 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        <svg x-show="isOpen" x-cloak class="w-6 h-6 sm:w-7 sm:h-7 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    </button>
</div>

<script>
window.unifiedSupportWidget = function unifiedSupportWidget() {
    return {
        isOpen: false,
        isScrolling: false,
        scrollTimeout: null,
        hasStickyFooter: false,

        init() {
            this.checkFooterVisibility();
            const observer = new MutationObserver(() => {
                this.checkFooterVisibility();
            });
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        },

        checkFooterVisibility() {
            const el = document.querySelector('.sticky.bottom-0, .fixed.bottom-0, [x-show*="showRows"]');
            if (el) {
                const style = window.getComputedStyle(el);
                this.hasStickyFooter = el.offsetWidth > 0 && el.offsetHeight > 0 && style.display !== 'none' && style.visibility !== 'hidden';
            } else {
                this.hasStickyFooter = false;
            }
        },

        handleScroll() {
            this.isScrolling = true;
            clearTimeout(this.scrollTimeout);
            this.scrollTimeout = setTimeout(() => {
                this.isScrolling = false;
            }, 150);
        },

        openChatbot() {
            this.isOpen = false;
            this.$dispatch('open-chat');
        }
    };
}
</script>
