<div x-show="mobileSidebarOpen"
     x-data="{
        currentPath: window.location.pathname,
        currentSearch: window.location.search,
        isLinkActive(href) {
            const u = new URL(href, window.location.origin);
            if (u.search) return u.pathname === this.currentPath && u.search === this.currentSearch;
            return u.pathname === this.currentPath;
        }
     }"
     x-on:livewire:navigated.window="currentPath = window.location.pathname; currentSearch = window.location.search"
     class="fixed inset-0 z-[100] lg:hidden"
     style="display: none;">
    <!-- Backdrop -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/70 backdrop-blur-sm" 
         @click="mobileSidebarOpen = false"></div>

    <!-- Drawer Panel (Dark Sleek Theme) -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 w-full max-w-xs bg-[#0F1D33] shadow-2xl overflow-y-auto flex flex-col h-full border-r border-slate-800">
        
        <!-- Logo Header (Fixed) -->
        <div class="px-4 py-4 flex items-center justify-between border-b border-slate-800/90 bg-[#0F1D33] shrink-0">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="bg-slate-50 rounded-xl p-2 flex items-center justify-center shadow-md">
                <img src="{{ asset('logo.png') }}" alt="Kingdom Recruitments" class="h-7 w-auto object-contain">
            </a>
            <button @click="mobileSidebarOpen = false" aria-label="Close menu" class="p-2 text-slate-400 hover:text-white transition-colors focus-visible:ring-2 focus-visible:ring-amber-500 rounded-xl bg-white/5">
                <i data-lucide="x" class="w-5 h-5" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Navigation Scroll Area (Flex 1) -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto sidebar-scrollbar pb-8 min-h-0">
            @php
                $navGroups = config('admin-nav.groups');
            @endphp

            @foreach($navGroups as $groupName => $items)
                @php
                    // Same permission-based visibility filter as the desktop sidebar
                    // (components/admin/sidebar.blade.php) — route middleware is the
                    // actual security boundary, this only keeps the drawer in sync.
                    $visibleItems = collect($items)->filter(fn($item) => empty($item['permission']) || auth()->user()->can($item['permission']))->values()->all();
                @endphp
                @continue(empty($visibleItems))

                @if($groupName !== 'Overview')
                    <div class="px-2 mb-1 mt-4">
                        <p class="text-[10px] uppercase tracking-widest text-[#B89955] font-extrabold">{{ $groupName }}</p>
                    </div>
                @endif

                @foreach($visibleItems as $item)
                    @php
                        $flatItems = !empty($item['children']) ? $item['children'] : [$item];
                    @endphp
                    @foreach($flatItems as $i => $flatItem)
                        @php
                            $href = route($flatItem['route'], $flatItem['params'] ?? []);
                            $isSubItem = !empty($item['children']) && $i > 0;
                        @endphp
                        <a href="{{ $href }}" wire:navigate @click="mobileSidebarOpen = false"
                           class="relative group flex items-center gap-2.5 px-2.5 py-2 rounded-xl transition-all duration-180 w-full text-left min-h-[42px] {{ $isSubItem ? 'ml-3 w-[calc(100%-12px)]' : '' }}"
                           :class="isLinkActive('{{ $href }}') ? 'bg-white/10 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white'">
                            
                            <!-- Active Left Indicator -->
                            <span x-show="isLinkActive('{{ $href }}')" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-[#B89955] rounded-r-md shadow-[0_0_8px_rgba(184,153,85,0.6)]"></span>

                            <div class="w-7 h-7 flex items-center justify-center rounded-lg transition-all duration-180 shrink-0"
                                 :class="isLinkActive('{{ $href }}') ? 'text-[#B89955] bg-[#B89955]/20' : 'text-slate-400 group-hover:text-[#B89955] bg-white/[0.03]'">
                                <i data-lucide="{{ $flatItem['icon'] }}" class="w-4 h-4" :stroke-width="isLinkActive('{{ $href }}') ? '2.5' : '2'"></i>
                            </div>
                            <span class="text-xs font-bold truncate">
                                {{ $flatItem['label'] }}
                            </span>
                        </a>
                    @endforeach
                @endforeach
            @endforeach
        </nav>
        
        <!-- Bottom Utility & Profile Area (Fixed) -->
        <div class="p-3 border-t border-slate-800/90 bg-[#0a1424] shrink-0 space-y-2.5 shadow-[0_-4px_12px_rgba(0,0,0,0.15)]">
            <a href="{{ route('home') }}" 
               class="group w-full px-3 py-2 rounded-xl bg-white/[0.04] hover:bg-white/[0.09] text-slate-300 hover:text-white border border-white/5 hover:border-white/10 transition-all duration-200 flex items-center gap-2.5 shadow-sm">
                <div class="w-6 h-6 rounded-lg flex items-center justify-center bg-cyan-500/15 text-cyan-400 group-hover:bg-cyan-500/25 transition-colors shrink-0">
                    <i data-lucide="globe" class="w-3.5 h-3.5" stroke-width="2"></i>
                </div>
                <span class="text-xs font-bold truncate">Back to Website</span>
                <i data-lucide="external-link" class="w-3 h-3 ml-auto text-slate-500 group-hover:text-slate-300 transition-colors shrink-0"></i>
            </a>

            <div class="flex items-center gap-2.5 pt-2 border-t border-slate-800/60">
                <x-avatar :src="Auth::user()?->profile_image" :name="Auth::user()?->name ?? 'Admin'" size="w-8 h-8" />
                <div class="flex flex-col min-w-0 flex-1">
                    <p class="text-xs font-bold truncate text-white">{{ Auth::user()?->name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()?->email ?? 'admin@kingdom.com' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" aria-label="Logout" class="p-1.5 text-slate-400 hover:text-red-400 transition-colors bg-white/5 hover:bg-white/10 rounded-lg">
                        <i data-lucide="log-out" class="w-4 h-4" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
</div>
