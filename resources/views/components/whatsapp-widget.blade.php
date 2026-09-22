{{-- WhatsApp Chat Widget — Floating Messenger Button --}}
{{-- Opens WhatsApp chat with a pre-filled message --}}
@php
    $whatsappNumber = '447411154198'; // Kingdom Recruitments WhatsApp
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

<div x-data="{ showTooltip: false, pulse: true }" x-init="setTimeout(() => pulse = false, 5000)" x-cloak>
    {{-- WhatsApp Floating Button --}}
    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
       @mouseenter="showTooltip = true" 
       @mouseleave="showTooltip = false"
       class="fixed bottom-[3.75rem] right-4 sm:bottom-24 sm:right-6 z-[99] h-11 w-11 sm:h-14 sm:w-14 rounded-full bg-[#25D366] text-white shadow-2xl shadow-[#25D366]/30 hover:shadow-[#25D366]/50 hover:scale-110 transition-all duration-300 flex items-center justify-center group"
       aria-label="Chat on WhatsApp">
        
        {{-- Pulse ring animation --}}
        <span x-show="pulse" 
              x-transition:leave="transition ease-in duration-500"
              x-transition:leave-start="opacity-100"
              x-transition:leave-end="opacity-0"
              class="absolute inset-0 rounded-full animate-ping bg-[#25D366]/40 pointer-events-none"></span>
        
        {{-- WhatsApp icon --}}
        <svg class="w-5 h-5 sm:w-7 sm:h-7 group-hover:scale-110 transition-transform relative z-10" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
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
         class="fixed bottom-[5rem] sm:bottom-[6.5rem] right-[4.25rem] sm:right-[5.5rem] z-[99] bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs sm:text-sm font-semibold px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 whitespace-nowrap pointer-events-none hidden sm:block">
        <span class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-[#25D366] animate-pulse"></span>
            Chat with us on WhatsApp
        </span>
        {{-- Arrow --}}
        <div class="absolute top-1/2 -right-1.5 -translate-y-1/2 w-3 h-3 bg-white dark:bg-slate-800 border-r border-b border-slate-100 dark:border-slate-700 rotate-[-45deg]"></div>
    </div>
</div>
