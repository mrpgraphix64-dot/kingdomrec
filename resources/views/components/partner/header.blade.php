<header class="h-14 sm:h-16 flex items-center justify-between px-3 sm:px-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/50 dark:border-slate-800/50 z-20 sticky top-0 gap-2 sm:gap-3">
    <!-- Mobile Menu Trigger -->
    <button @click="mobileSidebarOpen = !mobileSidebarOpen"
        class="lg:hidden p-2 text-slate-500 hover:bg-white rounded-full transition-colors focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:outline-none"
        aria-label="Toggle navigation menu">
        <i data-lucide="menu" class="text-[24px] w-5 h-5"></i>
    </button>

    <!-- Search Bar -->
    <div
        class="hidden md:flex items-center bg-white h-10 sm:h-12 rounded-full shadow-sm px-4 sm:px-5 flex-1 max-w-sm lg:max-w-md xl:max-w-lg border border-slate-100 focus-within:ring-2 focus-within:ring-primary/10 focus-within:border-primary/20 transition-all">
        <i data-lucide="search" class="text-[20px] text-slate-400 mr-3 w-5 h-5"></i>
        <input
            class="w-full bg-transparent border-none focus:outline-none text-sm placeholder:text-slate-400 text-slate-900 font-medium h-full"
            placeholder="Search events, shifts..." type="text" aria-label="Search events and shifts" />
    </div>

    <!-- Emergency Marquee (Dynamic from Admin Settings) -->
    @php
        $emergencySettings = \App\Models\Setting::whereIn('key', ['emergency_enabled', 'emergency_message_1', 'emergency_message_2'])->pluck('value', 'key');
        $emergencyOn = ($emergencySettings['emergency_enabled'] ?? '0') === '1';
        $eMsg1 = $emergencySettings['emergency_message_1'] ?? '';
        $eMsg2 = $emergencySettings['emergency_message_2'] ?? '';
    @endphp
    @if($emergencyOn && ($eMsg1 || $eMsg2))
    <div class="hidden lg:flex flex-1 mx-6 bg-gradient-to-r from-red-700 via-rose-600 to-red-700 text-white overflow-hidden rounded-full shadow-inner shadow-black/20 h-10 border border-red-500/50">
        <div class="flex items-center w-full px-3 gap-3">
            <!-- Badge -->
            <div class="flex items-center gap-1.5 shrink-0 bg-white/20 px-2.5 py-0.5 rounded-full border border-white/30 backdrop-blur-sm">
                <i data-lucide="alert-triangle" class="text-white text-[14px] animate-pulse w-5 h-5"></i>
                <span class="text-[10px] font-bold text-white uppercase tracking-wider">Emergency</span>
            </div>

            <!-- Scrolling News Container -->
            <div class="flex-1 overflow-hidden relative mask-gradient">
                <style>
                    .marquee-track-inline {
                        animation: marquee-scroll-inline 25s linear infinite;
                    }
                    @keyframes marquee-scroll-inline {
                        0% { transform: translateX(0); }
                        100% { transform: translateX(-50%); }
                    }
                    .mask-gradient {
                        -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
                        mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
                    }
                </style>
                <div class="marquee-track-inline flex items-center gap-12 whitespace-nowrap hover:[animation-play-state:paused]">
                    {{-- First copy --}}
                    @if($eMsg1)
                    <span class="inline-flex items-center gap-2 text-[12px] font-bold text-white drop-shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-white shrink-0 animate-pulse"></span>
                        🚨 CRITICAL: {{ $eMsg1 }}
                    </span>
                    @endif
                    @if($eMsg2)
                    <span class="inline-flex items-center gap-2 text-[12px] font-bold text-white drop-shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-white shrink-0 animate-pulse"></span>
                        ⚠️ URGENT: {{ $eMsg2 }}
                    </span>
                    @endif
                    {{-- Duplicate for seamless loop --}}
                    @if($eMsg1)
                    <span class="inline-flex items-center gap-2 text-[12px] font-bold text-white drop-shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-white shrink-0 animate-pulse"></span>
                        🚨 CRITICAL: {{ $eMsg1 }}
                    </span>
                    @endif
                    @if($eMsg2)
                    <span class="inline-flex items-center gap-2 text-[12px] font-bold text-white drop-shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-white shrink-0 animate-pulse"></span>
                        ⚠️ URGENT: {{ $eMsg2 }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Right Actions -->
    <div class="flex items-center gap-1.5 sm:gap-4 ml-auto shrink-0">
        <div class="flex items-center gap-1 sm:gap-2">
            <button
                class="h-8 w-8 sm:h-10 sm:w-10 flex items-center justify-center bg-white rounded-full shadow-sm text-slate-500 hover:text-kingdom-gold hover:bg-kingdom-gold/5 hover:border-kingdom-gold/30 transition-all border border-slate-100/50 relative group active:scale-95 duration-200 focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:outline-none"
                aria-label="Notifications">
                <i data-lucide="bell" class="text-[20px] group-hover:scale-110 transition-transform w-5 h-5"></i>
                <span class="absolute top-2.5 right-2.5 w-1.5 h-1.5 bg-red-500 rounded-full border border-white opacity-0 group-hover:opacity-100 transition-opacity"></span>
            </button>
            <button
                class="h-8 w-8 sm:h-10 sm:w-10 hidden sm:flex items-center justify-center bg-white rounded-full shadow-sm text-slate-500 hover:text-kingdom-gold hover:bg-kingdom-gold/5 hover:border-kingdom-gold/30 transition-all border border-slate-100/50 active:scale-95 duration-200 group focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:outline-none"
                aria-label="Messages">
                <i data-lucide="message-square" class="text-[20px] group-hover:scale-110 transition-transform w-5 h-5"></i>
            </button>
        </div>
        <!-- Book Staff action removed -->
        
        <!-- Profile Link -->
        <a href="{{ route('partner.profile') }}" class="h-8 w-8 sm:h-10 sm:w-10 flex items-center justify-center bg-slate-900 text-white rounded-full shadow-md hover:bg-slate-800 transition-colors font-bold text-xs sm:text-sm ml-1 sm:ml-2 focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2 focus-visible:outline-none"
           aria-label="Profile">
            {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'P' }}
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="ml-1" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('open-confirm-modal', { detail: { title: 'Sign Out', message: 'Are you sure you want to end your partner session?', onConfirm: () => this.submit() } }));">
            @csrf
            <button type="submit" class="h-8 w-8 sm:h-10 sm:w-10 flex items-center justify-center rounded-full bg-red-50 text-red-400 border border-red-100 hover:bg-gradient-to-br hover:from-red-500 hover:to-rose-600 hover:text-white hover:border-transparent hover:shadow-lg hover:shadow-red-500/30 transition-all active:scale-90 duration-300 group focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-none" title="Logout" aria-label="Sign out">
                <i data-lucide="log-out" class="text-[20px] group-hover:scale-110 transition-transform w-5 h-5"></i>
            </button>
        </form>
    </div>
</header>

