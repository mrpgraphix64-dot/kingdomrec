{{-- Facebook Messenger Chat Widget — Floating Button --}}
@php
    // Facebook Page Username or ID for Messenger
    $fbUsername = 'kingdomrecruitments'; 
    $messengerUrl = 'https://m.me/' . $fbUsername;
@endphp

<div x-data="{ showTooltip: false }" x-cloak>
    {{-- Facebook Floating Button --}}
    {{-- Placed above the WhatsApp widget --}}
    <a href="{{ $messengerUrl }}" target="_blank" rel="noopener noreferrer"
       @mouseenter="showTooltip = true" 
       @mouseleave="showTooltip = false"
       class="fixed bottom-[7.25rem] right-4 sm:bottom-[10.5rem] sm:right-6 z-[98] h-11 w-11 sm:h-14 sm:w-14 rounded-full bg-[#0084FF] text-white shadow-2xl shadow-[#0084FF]/30 hover:shadow-[#0084FF]/50 hover:scale-110 transition-all duration-300 flex items-center justify-center group"
       aria-label="Chat on Messenger">
        
        {{-- Facebook Messenger icon --}}
        <svg class="w-6 h-6 sm:w-8 sm:h-8 group-hover:scale-110 transition-transform relative z-10" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.923 1.488 5.485 3.791 7.155v3.428c0 .546.602.871 1.07.571l3.525-2.261A10.74 10.74 0 0012 20.516c5.523 0 10-4.145 10-9.258S17.523 2 12 2zm1.094 12.35l-2.73-2.91-5.32 2.91 5.86-6.22 2.82 2.91 5.23-2.91-5.86 6.22z"/>
        </svg>
    </a>

    {{-- Tooltip --}}
    <div x-show="showTooltip"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-x-2"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-2"
         class="fixed bottom-[8.5rem] sm:bottom-[11.5rem] right-[4.25rem] sm:right-[5.5rem] z-[98] bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs sm:text-sm font-semibold px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 whitespace-nowrap pointer-events-none hidden sm:block">
        <span class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-[#0084FF] animate-pulse"></span>
            Chat with us on Messenger
        </span>
        {{-- Arrow --}}
        <div class="absolute top-1/2 -right-1.5 -translate-y-1/2 w-3 h-3 bg-white dark:bg-slate-800 border-r border-b border-slate-100 dark:border-slate-700 rotate-[-45deg]"></div>
    </div>
</div>
