<!-- Mobile Sidebar Overlay -->
<div x-show="mobileSidebarOpen" 
     x-data="{ currentPath: window.location.pathname }" 
     x-on:livewire:navigated.window="currentPath = window.location.pathname"
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
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>

    <!-- Sidebar Panel -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 w-full max-w-xs bg-[#0F1D33] shadow-2xl flex flex-col h-full overflow-hidden border-r border-slate-800">
        
        <!-- Header -->
        <div class="px-5 py-5 flex items-center justify-between border-b border-slate-800 bg-[#0F1D33] shrink-0">
            <a href="{{ route('partner.dashboard') }}" wire:navigate class="bg-slate-50 rounded-xl p-2.5 flex items-center justify-center shadow-md">
                <img src="{{ asset('logo.png') }}" alt="Kingdom Recruitments" class="h-8 w-auto object-contain">
            </a>
            <button @click="mobileSidebarOpen = false" aria-label="Close menu" class="p-2 text-slate-400 hover:text-white rounded-xl hover:bg-white/10 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Scroll Area -->
        <div class="flex-1 overflow-y-auto sidebar-scrollbar p-3 space-y-1 pb-10">
            
            @php $isDash = request()->routeIs('partner.dashboard'); @endphp
            <a href="{{ route('partner.dashboard') }}" wire:navigate 
               class="relative group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 w-full text-left min-h-[46px]"
               :class="currentPath === new URL('{{ route('partner.dashboard') }}', window.location.origin).pathname ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'" 
               @click="mobileSidebarOpen = false">
                <span x-show="currentPath === new URL('{{ route('partner.dashboard') }}', window.location.origin).pathname" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-[#B89955] rounded-r-md"></span>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                     :class="currentPath === new URL('{{ route('partner.dashboard') }}', window.location.origin).pathname ? 'text-[#B89955] bg-[#B89955]/15' : 'text-slate-400 group-hover:text-[#B89955]'">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                </div>
                <span class="text-xs font-bold truncate">Dashboard</span>
            </a>

            <!-- Recruitment Hub -->
            <div class="px-3 mb-1.5 mt-4">
                <p class="text-[10px] uppercase tracking-widest text-[#B89955] font-bold">Recruitment Hub</p>
            </div>
            @php
                $partnerLinks1 = [
                    ['label' => 'My Bookings & Shifts', 'route' => 'partner.event-list', 'icon' => 'calendar'],
                    ['label' => 'Timesheets', 'route' => 'partner.timesheets', 'icon' => 'calendar-clock'],
                    ['label' => 'Invoices', 'route' => 'partner.billing', 'icon' => 'receipt'],
                    ['label' => 'Rate Card', 'route' => 'partner.rate-card', 'icon' => 'banknote'],
                    ['label' => 'Quotations', 'route' => 'partner.quotations', 'icon' => 'file-text'],
                ];
            @endphp
            @foreach($partnerLinks1 as $item)
            <a href="{{ route($item['route']) }}" wire:navigate 
               class="relative group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 w-full text-left min-h-[46px]"
               :class="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'" 
               @click="mobileSidebarOpen = false">
                <span x-show="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-[#B89955] rounded-r-md"></span>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                     :class="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname ? 'text-[#B89955] bg-[#B89955]/15' : 'text-slate-400 group-hover:text-[#B89955]'">
                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4"></i>
                </div>
                <span class="text-xs font-bold truncate">{{ $item['label'] }}</span>
            </a>
            @endforeach

            <!-- Management -->
            <div class="px-3 mb-1.5 mt-4">
                <p class="text-[10px] uppercase tracking-widest text-[#B89955] font-bold">Management</p>
            </div>
            @php
                $partnerLinks2 = [
                    ['label' => 'Staff Booking', 'route' => 'partner.book-staff', 'icon' => 'user-plus'],
                    ['label' => 'Favourite Staff', 'route' => 'partner.favourite-staff', 'icon' => 'star'],
                    ['label' => 'Notes & Messages', 'route' => 'partner.messages', 'icon' => 'message-square'],
                    ['label' => 'Contacts', 'route' => 'partner.contacts', 'icon' => 'users'],
                ];
            @endphp
            @foreach($partnerLinks2 as $item)
            <a href="{{ route($item['route']) }}" wire:navigate 
               class="relative group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 w-full text-left min-h-[46px]"
               :class="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'" 
               @click="mobileSidebarOpen = false">
                <span x-show="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-[#B89955] rounded-r-md"></span>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                     :class="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname ? 'text-[#B89955] bg-[#B89955]/15' : 'text-slate-400 group-hover:text-[#B89955]'">
                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4"></i>
                </div>
                <span class="text-xs font-bold truncate">{{ $item['label'] }}</span>
            </a>
            @endforeach

            <!-- Account -->
            <div class="px-3 mb-1.5 mt-4">
                <p class="text-[10px] uppercase tracking-widest text-[#B89955] font-bold">Account</p>
            </div>
            @php
                $partnerLinks3 = [
                    ['label' => 'News/Alerts', 'route' => 'partner.alerts', 'icon' => 'bell'],
                    ['label' => 'Profile', 'route' => 'partner.profile', 'icon' => 'user'],
                ];
            @endphp
            @foreach($partnerLinks3 as $item)
            <a href="{{ route($item['route']) }}" wire:navigate 
               class="relative group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 w-full text-left min-h-[46px]"
               :class="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'" 
               @click="mobileSidebarOpen = false">
                <span x-show="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-[#B89955] rounded-r-md"></span>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                     :class="currentPath === new URL('{{ route($item['route']) }}', window.location.origin).pathname ? 'text-[#B89955] bg-[#B89955]/15' : 'text-slate-400 group-hover:text-[#B89955]'">
                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4"></i>
                </div>
                <span class="text-xs font-bold truncate">{{ $item['label'] }}</span>
            </a>
            @endforeach

            <div class="my-3 border-t border-slate-800"></div>
            
            <a href="{{ route('home') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 w-full text-left hover:bg-white/5 text-slate-400 hover:text-white">
                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-500/10 text-cyan-400">
                    <i data-lucide="globe" class="w-4 h-4" stroke-width="2"></i>
                </div>
                <span class="text-xs font-bold">
                    Back to Website
                </span>
            </a>
        </div>
        
        <!-- Profile Footer -->
        <div class="p-4 border-t border-slate-800 bg-[#0c1729]">
            <div class="flex items-center gap-3">
                <x-avatar :src="Auth::user()?->profile_image" :name="Auth::user()?->name ?? 'Partner'" size="w-9 h-9" />
                <div class="flex flex-col min-w-0 flex-1">
                    <p class="text-xs font-bold truncate text-white">{{ Auth::user()?->name ?? 'Partner' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()?->email ?? 'partner@kingdom.com' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" aria-label="Logout" class="p-2 text-slate-400 hover:text-red-400 transition-colors bg-white/5 hover:bg-white/10 rounded-lg">
                        <i data-lucide="log-out" class="w-4 h-4" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
</div>
