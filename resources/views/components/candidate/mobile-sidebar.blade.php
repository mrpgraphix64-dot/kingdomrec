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
            <a href="{{ route('applicant.profile') }}" wire:navigate class="bg-slate-50 rounded-xl p-2.5 flex items-center justify-center shadow-md">
                <img src="{{ asset('logo.png') }}" alt="Kingdom Recruitments" class="h-8 w-auto object-contain">
            </a>
            <button @click="mobileSidebarOpen = false" aria-label="Close menu" class="p-2 text-slate-400 hover:text-white rounded-xl hover:bg-white/10 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto sidebar-scrollbar p-3 space-y-1 pb-10">
            @php
                $candidateLinks = [
                    ['label' => 'My Profile', 'route' => 'applicant.profile', 'icon' => 'user-circle'],
                    ['label' => 'My Schedule', 'route' => 'applicant.schedule', 'icon' => 'calendar'],
                    ['label' => 'Timesheets', 'route' => 'applicant.timesheets', 'icon' => 'clock'],
                    ['label' => 'My Applications', 'route' => 'jobs.applied', 'icon' => 'briefcase'],
                ];
            @endphp
            @foreach($candidateLinks as $item)
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

            <div class="px-3 mb-1.5 mt-4">
                <p class="text-[10px] uppercase tracking-widest text-[#B89955] font-bold">Account</p>
            </div>
            <a href="{{ route('applicant.profile.edit') }}" wire:navigate
               class="relative group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 w-full text-left min-h-[46px]"
               :class="currentPath === new URL('{{ route('applicant.profile.edit') }}', window.location.origin).pathname ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'"
               @click="mobileSidebarOpen = false">
                <div class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0 text-slate-400 group-hover:text-[#B89955]">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <span class="text-xs font-bold truncate">Edit Profile</span>
            </a>
            <a href="{{ route('jobs.index') }}" wire:navigate
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 w-full text-left text-slate-300 hover:bg-white/5 hover:text-white min-h-[46px]"
               @click="mobileSidebarOpen = false">
                <div class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0 text-slate-400 group-hover:text-[#B89955]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <span class="text-xs font-bold truncate">Browse Jobs</span>
            </a>

            <div class="my-3 border-t border-slate-800"></div>

            <a href="{{ route('home') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 w-full text-left hover:bg-white/5 text-slate-400 hover:text-white">
                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-500/10 text-cyan-400">
                    <i data-lucide="globe" class="w-4 h-4" stroke-width="2"></i>
                </div>
                <span class="text-xs font-bold">Back to Website</span>
            </a>
        </div>

        <!-- Profile Footer -->
        <div class="p-4 border-t border-slate-800 bg-[#0c1729]">
            <div class="flex items-center gap-3">
                <x-avatar :user="auth()->user()" class="w-9 h-9 rounded-full" />
                <div class="flex flex-col min-w-0 flex-1">
                    <p class="text-xs font-bold truncate text-white">{{ Auth::user()?->name ?? 'Candidate' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()?->email ?? '' }}</p>
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
