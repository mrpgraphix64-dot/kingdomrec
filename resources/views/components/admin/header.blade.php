<header class="h-14 shrink-0 flex items-center justify-between gap-3 px-4 sm:px-6 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/60 dark:border-slate-800/60 z-20 sticky top-0">
    <!-- Mobile Menu Trigger -->
    <button @click="mobileSidebarOpen = !mobileSidebarOpen" aria-label="Open menu"
        class="lg:hidden p-1.5 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
        <i data-lucide="menu" class="w-5 h-5" aria-hidden="true"></i>
    </button>

    <!-- Search Bar -->
    <form action="{{ route('admin.job-post') }}" method="GET" role="search"
        class="hidden md:flex items-center bg-slate-50 dark:bg-slate-800 h-9.5 rounded-full px-4 w-72 lg:w-80 border border-slate-200/80 dark:border-slate-700/80 focus-within:ring-2 focus-within:ring-kingdom-gold/20 focus-within:border-kingdom-gold/40 transition-all">
        <button type="submit" aria-label="Search" class="flex items-center justify-center p-0 bg-transparent border-none">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 mr-2.5 hover:text-kingdom-gold transition-colors cursor-pointer" aria-hidden="true"></i>
        </button>
        <label for="admin-header-search" class="sr-only">Search for applicants, jobs</label>
        <input id="admin-header-search" name="search"
            class="w-full bg-transparent border-none focus:outline-none text-xs placeholder:text-slate-400 text-slate-900 dark:text-white font-medium h-full"
            placeholder="Search applicants, jobs..." type="text" value="{{ request('search') }}" />
    </form>

    <!-- Right Actions -->
    <div class="flex items-center gap-2.5 ml-auto">
        <div class="flex items-center gap-1.5">
            <button aria-label="Notifications"
                class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-800 rounded-full shadow-xs text-slate-500 dark:text-slate-400 hover:text-kingdom-gold hover:bg-kingdom-gold/5 hover:border-kingdom-gold/30 transition-all border border-slate-200/60 dark:border-slate-700/60 relative group active:scale-95 duration-200 focus-visible:ring-2 focus-visible:ring-kingdom-gold">
                <i data-lucide="bell" class="w-4 h-4 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
                <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-red-500 rounded-full border border-white opacity-0 group-hover:opacity-100 transition-opacity"></span>
            </button>
            <button aria-label="Messages"
                class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-800 rounded-full shadow-xs text-slate-500 dark:text-slate-400 hover:text-kingdom-gold hover:bg-kingdom-gold/5 hover:border-kingdom-gold/30 transition-all border border-slate-200/60 dark:border-slate-700/60 active:scale-95 duration-200 group focus-visible:ring-2 focus-visible:ring-kingdom-gold">
                <i data-lucide="message-square" class="w-4 h-4 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Profile Link -->
        <a href="{{ route('admin.profile') }}" aria-label="Your profile" class="h-9 w-9 flex items-center justify-center bg-slate-900 text-white rounded-full shadow-xs hover:bg-slate-800 transition-colors font-bold text-xs ml-1 focus-visible:ring-2 focus-visible:ring-kingdom-gold">
            {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'G' }}
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="ml-0.5" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('open-confirm-modal', { detail: { title: 'Sign Out', message: 'Are you sure you want to end your admin session?', onConfirm: () => this.submit() } }));">
            @csrf
            <button type="submit" aria-label="Logout" class="h-9 w-9 flex items-center justify-center rounded-full bg-red-50 dark:bg-red-950/40 text-red-500 dark:text-red-400 border border-red-100 dark:border-red-900/40 hover:bg-red-600 hover:text-white hover:border-transparent transition-all active:scale-90 duration-300 group focus-visible:ring-2 focus-visible:ring-red-400" title="Logout">
                <i data-lucide="log-out" class="w-4 h-4 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
            </button>
        </form>
    </div>
</header>
