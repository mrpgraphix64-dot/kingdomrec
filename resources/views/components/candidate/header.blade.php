<header class="h-14 sm:h-16 flex items-center justify-between px-3 sm:px-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/50 dark:border-slate-800/50 z-20 sticky top-0 gap-2 sm:gap-3">
    <!-- Mobile Menu Trigger -->
    <button @click="mobileSidebarOpen = !mobileSidebarOpen"
        class="lg:hidden p-2 text-slate-500 hover:bg-white rounded-full transition-colors focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:outline-none"
        aria-label="Toggle navigation menu">
        <i data-lucide="menu" class="text-[24px] w-5 h-5"></i>
    </button>

    <div class="flex-1"></div>

    <!-- Right Actions -->
    <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
        <a href="{{ route('jobs.index') }}" wire:navigate
           class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition-all">
            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
            Browse Jobs
        </a>

        <!-- Profile Link -->
        <a href="{{ route('applicant.profile') }}" wire:navigate class="h-8 w-8 sm:h-10 sm:w-10 flex items-center justify-center bg-slate-900 text-white rounded-full shadow-md hover:bg-slate-800 transition-colors font-bold text-xs sm:text-sm focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2 focus-visible:outline-none"
           aria-label="My Profile">
            {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'C' }}
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('open-confirm-modal', { detail: { title: 'Sign Out', message: 'Are you sure you want to end your session?', onConfirm: () => this.submit() } }));">
            @csrf
            <button type="submit" class="h-8 w-8 sm:h-10 sm:w-10 flex items-center justify-center rounded-full bg-red-50 text-red-400 border border-red-100 hover:bg-red-500 hover:text-white hover:border-transparent transition-all active:scale-90 duration-200 focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-none" title="Logout" aria-label="Sign out">
                <i data-lucide="log-out" class="text-[20px] w-4 h-4 sm:w-5 sm:h-5"></i>
            </button>
        </form>
    </div>
</header>
