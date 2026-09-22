<div x-data="{ mobileOpen: false, scrolled: false, logoutModalOpen: false }"
    x-init="$watch('mobileOpen', value => document.body.classList.toggle('overflow-hidden', value))"
    @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Top Contact Bar -->
    <div
        class="bg-white/95 dark:bg-black/95 backdrop-blur-sm border-b border-gray-100 dark:border-gray-800 py-2 hidden md:block transition-colors duration-300 relative z-[60]">
        <div
            class="container mx-auto px-4 flex justify-end items-center text-xs font-bold text-gray-600 dark:text-white tracking-wide uppercase">
            <div class="flex items-center space-x-6">
                <a href="tel:07411154198" class="flex items-center hover:text-kingdom-gold transition-colors"><span
                        class="material-icons text-kingdom-gold text-sm mr-2">call</span> 074 1115 4198</a>
                <a href="https://maps.google.com/?q=8-10+Greatorex+Street,+London+E1+5NF,+UK" target="_blank"
                    rel="noopener noreferrer" class="flex items-center hover:text-kingdom-gold transition-colors"><span
                        class="material-icons text-kingdom-gold text-sm mr-2">location_on</span> 8-10 Greatorex St, London E1 5NF</a>
            </div>
        </div>
    </div>

    <nav :class="{ 'absolute top-0 md:top-12 bg-transparent pt-2 md:pt-4': !scrolled, 'fixed top-0 bg-navy-dark/95 backdrop-blur-md shadow-md py-2': scrolled }"
        class="w-full z-50 transition-all duration-500 ease-in-out">
        <div class="container mx-auto px-4 flex justify-between items-center">

            <!-- Logo Container Box with Solid White Background & Dark Stroke Character Highlight -->
            <a href="{{ route('home') }}" class="flex items-center justify-center bg-white px-4 py-2 rounded-2xl shadow-xl border border-gray-200/90 transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                <img src="{{ asset('logo.png') }}" class="h-12 md:h-14 w-auto object-contain filter drop-shadow-[0_0_1px_rgba(0,0,0,0.85)] drop-shadow-[0_1px_3px_rgba(0,0,0,0.4)] contrast-125" alt="Kingdom Recruitments">
            </a>

            <!-- Right Side: Nav Pill + Theme Toggle -->
            <div class="hidden md:flex items-center">
                <div class="nav-pill">
                    <a class="nav-link hover:text-kingdom-gold transition-colors {{ (request()->routeIs('home') || request()->routeIs('home.video')) ? 'text-kingdom-gold' : '' }}"
                        href="{{ route('home') }}">Home</a>
                    <a class="nav-link hover:text-kingdom-gold transition-colors {{ request()->routeIs('about') ? 'text-kingdom-gold' : '' }}"
                        href="{{ route('about') }}">About</a>
                    <a class="nav-link hover:text-kingdom-gold transition-colors {{ request()->routeIs('team') ? 'text-kingdom-gold' : '' }}"
                        href="{{ route('team') }}">Our Team</a>
                    <a class="nav-link hover:text-kingdom-gold transition-colors {{ request()->routeIs('careers') ? 'text-kingdom-gold' : '' }}"
                        href="{{ route('careers') }}">Careers</a>
                    <a class="nav-link hover:text-kingdom-gold transition-colors {{ request()->routeIs('jobs.*') ? 'text-kingdom-gold' : '' }}"
                        href="{{ route('jobs.index') }}">Job List</a>
                    <a class="nav-link hover:text-kingdom-gold transition-colors {{ request()->routeIs('gallery') ? 'text-kingdom-gold' : '' }}"
                        href="{{ route('gallery') }}">Gallery</a>

                    <!-- Applicant Dropdown -->
                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true"
                        @mouseleave="open = false">
                        <a href="{{ route('applicants.index') }}"
                            class="nav-link flex items-center gap-1 hover:text-kingdom-gold transition-colors {{ (request()->routeIs('applicants.*') || request()->routeIs('applicant.*') || request()->routeIs('jobs.applied')) ? 'text-kingdom-gold' : '' }}">
                            APPLICANTS <span class="material-icons text-sm transition-transform duration-300"
                                :class="{ 'rotate-180': open }">expand_more</span>
                        </a>
                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full left-0 w-56 bg-white dark:bg-surface-dark shadow-xl rounded-b-lg border-t-2 border-kingdom-gold py-2 z-50"
                            style="display: none;">

                            <a href="{{ route('applicants.index') }}"
                                class="block px-6 py-3 text-sm font-bold text-kingdom-navy dark:text-white hover:text-kingdom-gold transition-colors group/link flex items-center {{ request()->routeIs('applicants.index') ? 'text-kingdom-gold' : '' }}">
                                <span
                                    class="material-icons text-kingdom-gold mr-2 text-base opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all duration-300">north_east</span>
                                Overview
                            </a>

                            @auth
                                @if(in_array(Auth::user()->role, ['applicant', 'candidate']))
                                    <a href="{{ route('applicant.profile') }}"
                                        class="block px-6 py-3 text-sm font-bold text-kingdom-navy dark:text-white hover:text-kingdom-gold transition-colors group/link flex items-center {{ request()->routeIs('applicant.profile*') ? 'text-kingdom-gold' : '' }}">
                                        <span class="material-icons text-kingdom-gold mr-2 text-base opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all duration-300">person</span>
                                        My Profile
                                    </a>
                                    <a href="{{ route('jobs.applied') }}"
                                        class="block px-6 py-3 text-sm font-bold text-kingdom-navy dark:text-white hover:text-kingdom-gold transition-colors group/link flex items-center {{ request()->routeIs('jobs.applied') ? 'text-kingdom-gold' : '' }}">
                                        <span class="material-icons text-kingdom-gold mr-2 text-base opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all duration-300">assignment</span>
                                        My Applications
                                    </a>
                                    <a href="{{ route('applicant.schedule') }}"
                                        class="block px-6 py-3 text-sm font-bold text-kingdom-navy dark:text-white hover:text-kingdom-gold transition-colors group/link flex items-center {{ request()->routeIs('applicant.schedule') ? 'text-kingdom-gold' : '' }}">
                                        <span class="material-icons text-kingdom-gold mr-2 text-base opacity-0 -translate-x-2 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all duration-300">calendar_month</span>
                                        My Schedule
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    
                    <a class="nav-link hover:text-kingdom-gold transition-colors {{ request()->routeIs('contact') ? 'text-kingdom-gold' : '' }}"
                        href="{{ route('contact') }}">Contact</a>

                    <div class="rounded-r-full overflow-hidden flex ml-2 mr-1 my-1">
                        @auth
                            <!-- Auth State: Profile & Logout -->
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}"
                                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase tracking-widest transition-colors mr-2 rounded-full flex items-center gap-1">
                                    <span class="material-icons text-sm">person</span> Admin
                                </a>
                            @else
                                <a href="{{ Auth::user()->role === 'partner' ? '/portal#partner-dashboard' : '/portal#applicant-profile' }}"
                                    onclick="if(window.location.pathname === '/portal') { handleNav(event, '{{ Auth::user()->role === 'partner' ? '#partner-dashboard' : '#applicant-profile' }}'); }"
                                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase tracking-widest transition-colors mr-2 rounded-full flex items-center gap-1">
                                    <span class="material-icons text-sm">person</span> Profile
                                </a>
                            @endif
                            <button @click="logoutModalOpen = true"
                                class="px-6 py-2 bg-kingdom-gold hover:bg-yellow-500 text-kingdom-navy font-bold text-xs uppercase tracking-widest transition-colors rounded-full flex items-center gap-1">
                                <span class="material-icons text-sm">logout</span> Logout
                            </button>
                        @endauth

                        @guest
                            <!-- Guest State: Login/Help -->
                            @if(request()->routeIs('portal'))
                                <a href="#help" onclick="handleNav(event, '#help')"
                                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase tracking-widest transition-colors mr-2 rounded-full flex items-center gap-1">
                                    <span class="material-icons text-sm">help</span> Help
                                </a>
                            @else
                                <a class="contact-block bg-kingdom-gold text-kingdom-navy hover:bg-yellow-500 transition-colors cursor-pointer"
                                    href="{{ route('portal') }}">
                                    Sign In
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>

            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center gap-2">
                <button @click="mobileOpen = !mobileOpen"
                    class="w-10 h-10 rounded bg-black/50 flex items-center justify-center text-white hover:bg-black/70 transition-colors">
                    <span class="material-icons">menu</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Side Drawer -->
    <div x-show="mobileOpen" class="fixed inset-0 z-[100] md:hidden" style="display: none;">
        <!-- Backdrop -->
        <div x-show="mobileOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm"
            @click="mobileOpen = false"></div>

        <!-- Drawer Panel -->
        <div x-show="mobileOpen" x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-full max-w-xs bg-navy-dark shadow-2xl overflow-y-auto border-l border-white/10 flex flex-col">

            <div class="px-6 py-8 flex items-center justify-between border-b border-white/10">
                <span class="text-xl font-black text-white tracking-tight uppercase">Menu</span>
                <button @click="mobileOpen = false" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-icons text-3xl">close</span>
                </button>
            </div>

            <div class="flex-1 px-6 py-6 space-y-2">
                <a class="block px-4 py-3 font-bold hover:bg-white/10 rounded-lg transition-colors border-l-4 {{ (request()->routeIs('home') || request()->routeIs('home.video')) ? 'text-kingdom-gold border-kingdom-gold bg-white/10' : 'text-white border-transparent hover:border-kingdom-gold' }}"
                    href="{{ route('home') }}">Home</a>
                <a class="block px-4 py-3 font-bold hover:bg-white/10 rounded-lg transition-colors border-l-4 {{ request()->routeIs('jobs.*') ? 'text-kingdom-gold border-kingdom-gold bg-white/10' : 'text-white border-transparent hover:border-kingdom-gold' }}"
                    href="{{ route('jobs.index') }}">Job List</a>
                <a class="block px-4 py-3 font-bold hover:bg-white/10 rounded-lg transition-colors border-l-4 {{ request()->routeIs('gallery') ? 'text-kingdom-gold border-kingdom-gold bg-white/10' : 'text-white border-transparent hover:border-kingdom-gold' }}"
                    href="{{ route('gallery') }}">Gallery</a>

                <!-- Applicants Group -->
                <div x-data="{ expanded: {{ (request()->routeIs('applicants.*') || request()->routeIs('applicant.*') || request()->routeIs('jobs.applied')) ? 'true' : 'false' }} }" class="border-t border-white/5 pt-2 mt-2">
                    <button @click="expanded = !expanded"
                        class="w-full flex justify-between items-center px-4 py-3 font-bold hover:bg-white/10 rounded-lg transition-colors border-l-4 {{ (request()->routeIs('applicants.*') || request()->routeIs('applicant.*') || request()->routeIs('jobs.applied')) ? 'text-kingdom-gold border-kingdom-gold bg-white/10' : 'text-white border-transparent hover:border-kingdom-gold' }}">
                        <span>Applicants</span>
                        <span class="material-icons text-sm transition-transform duration-300"
                            :class="{ 'rotate-180': expanded }">expand_more</span>
                    </button>
                    <div x-show="expanded" class="pl-4 space-y-1 bg-black/20 rounded-lg mt-1" x-collapse>
                        <a href="{{ route('applicants.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-colors flex items-center gap-2 {{ request()->routeIs('applicants.index') ? 'text-kingdom-gold bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            <span class="material-icons text-xs">north_east</span> Overview
                        </a>

                        @auth
                            @if(in_array(Auth::user()->role, ['applicant', 'candidate']))
                                <a href="{{ route('applicant.profile') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors flex items-center gap-2 {{ request()->routeIs('applicant.profile*') ? 'text-kingdom-gold bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                                    <span class="material-icons text-xs">person</span> My Profile
                                </a>
                                <a href="{{ route('jobs.applied') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors flex items-center gap-2 {{ request()->routeIs('jobs.applied') ? 'text-kingdom-gold bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                                    <span class="material-icons text-xs">assignment</span> My Applications
                                </a>
                                <a href="{{ route('applicant.schedule') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors flex items-center gap-2 {{ request()->routeIs('applicant.schedule') ? 'text-kingdom-gold bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                                    <span class="material-icons text-xs">calendar_month</span> My Schedule
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <div class="border-t border-white/5 pt-2 mt-2">
                    <a class="block px-4 py-3 font-bold hover:bg-white/10 rounded-lg transition-colors border-l-4 {{ request()->routeIs('about') ? 'text-kingdom-gold border-kingdom-gold bg-white/10' : 'text-white border-transparent hover:border-kingdom-gold' }}"
                        href="{{ route('about') }}">About</a>
                    <a class="block px-4 py-3 font-bold hover:bg-white/10 rounded-lg transition-colors border-l-4 {{ request()->routeIs('team') ? 'text-kingdom-gold border-kingdom-gold bg-white/10' : 'text-white border-transparent hover:border-kingdom-gold' }}"
                        href="{{ route('team') }}">Our Team</a>
                    <a class="block px-4 py-3 font-bold hover:bg-white/10 rounded-lg transition-colors border-l-4 {{ request()->routeIs('careers') ? 'text-kingdom-gold border-kingdom-gold bg-white/10' : 'text-white border-transparent hover:border-kingdom-gold' }}"
                        href="{{ route('careers') }}">Careers</a>
                    <a class="block px-4 py-3 font-bold hover:bg-white/10 rounded-lg transition-colors border-l-4 {{ request()->routeIs('contact') ? 'text-kingdom-gold border-kingdom-gold bg-white/10' : 'text-white border-transparent hover:border-kingdom-gold' }}"
                        href="{{ route('contact') }}">Contact</a>
                </div>
            </div>

            <div class="p-6 border-t border-white/10 bg-black/20">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="block w-full py-4 mb-2 bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold text-center uppercase tracking-widest rounded-lg transition-colors flex items-center justify-center gap-2">
                            <span class="material-icons text-sm">person</span> Admin Dashboard
                        </a>
                    @else
                        <a href="{{ Auth::user()->role === 'partner' ? '/portal#partner-dashboard' : '/portal#applicant-profile' }}"
                            onclick="if(window.location.pathname === '/portal') { handleNav(event, '{{ Auth::user()->role === 'partner' ? '#partner-dashboard' : '#applicant-profile' }}'); }"
                            @click="mobileOpen = false"
                            class="block w-full py-4 mb-2 bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold text-center uppercase tracking-widest rounded-lg transition-colors flex items-center justify-center gap-2">
                            <span class="material-icons text-sm">person</span> Profile
                        </a>
                    @endif
                    <button @click="logoutModalOpen = true"
                        class="block w-full py-4 bg-kingdom-navy hover:bg-kingdom-gold text-white font-bold text-center uppercase tracking-widest rounded-lg transition-colors shadow-lg shadow-kingdom-navy/30 flex items-center justify-center gap-2">
                        <span class="material-icons text-sm">logout</span> Sign Out
                    </button>
                @endauth

                @guest
                    @if(request()->routeIs('portal'))
                        <a href="#help" onclick="handleNav(event, '#help')" @click="mobileOpen = false"
                            class="block w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold text-center uppercase tracking-widest rounded-lg transition-colors">Help
                            Center</a>
                    @else
                        <a class="block w-full py-4 bg-primary hover:bg-primary-dark text-white font-bold text-center uppercase tracking-widest rounded-lg transition-colors shadow-lg shadow-primary/30"
                            href="{{ route('portal') }}">Sign In</a>
                    @endif
                @endguest
            </div>
        </div>
    </div>


<!-- Logout Confirmation Modal -->
<div x-show="logoutModalOpen" 
     class="fixed inset-0 z-[200] overflow-y-auto" 
     style="display: none;" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="logoutModalOpen" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" 
         @click="logoutModalOpen = false"></div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="logoutModalOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 p-6 text-left align-middle shadow-xl transition-all border border-gray-100 dark:border-gray-700">

            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    <span class="material-icons text-2xl">logout</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-6">
                        Sign Out
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Are you sure you want to end your session?
                    </p>
                </div>
            </div>

            <div class="mt-6 flex gap-3 justify-end">
                <button type="button" 
                        class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition-colors"
                        @click="logoutModalOpen = false">
                    Cancel
                </button>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-bold text-white bg-kingdom-red hover:bg-red-700 rounded-lg shadow-lg shadow-red-500/30 transition-all flex items-center gap-2">
                        <span>Sign Out</span>
                        <span class="material-icons text-xs">arrow_forward</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>