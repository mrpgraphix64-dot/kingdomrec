@extends('layouts.app')

@section('title', 'Kingdom Recruitments')

@section('content')
    <div class="relative">
        <header
            class="relative w-full mx-auto min-h-[75vh] md:min-h-[85vh] overflow-hidden flex flex-col justify-start items-center shadow-2xl pb-24"
            style="padding-top: 8.5rem;">
            <div class="absolute inset-0 z-0">
                <img alt="Recruitment meeting handshake" class="w-full h-full object-cover object-[75%_50%]"
                    src="{{ asset('images/hero_v3.jpg') }}" />
                <div
                    class="absolute inset-0 bg-gradient-to-r from-navy-dark/90 to-navy-dark/40 dark:from-black/90 dark:to-black/50">
                </div>
            </div>
            <div class="absolute top-0 right-0 w-1/3 h-full z-10 pointer-events-none opacity-50">
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                    <polygon class="fill-primary" opacity="0.9" points="0,0 100,0 100,100"></polygon>
                    <polygon class="fill-red-700" opacity="0.6" points="50,0 100,0 100,50"></polygon>
                </svg>
            </div>
            <div class="relative z-10 container mx-auto px-4 text-white">
                <div class="max-w-3xl">
                    <h2
                        class="text-xs md:text-sm lg:text-lg font-bold uppercase tracking-[0.2em] mb-2 md:mb-3 text-kingdom-gold">
                        Decades of
                        Excellence</h2>
                    <h1 class="text-2xl md:text-4xl lg:text-6xl font-display font-extrabold leading-tight mb-4 md:mb-6">
                        Bridging Talent <br />&amp; Opportunity
                    </h1>
                    <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-xl leading-relaxed">
                        Connecting world-class professionals with industry-leading organizations across the UK. Your future
                        starts with a conversation.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('applicants.upload') }}"
                            class="relative z-40 group overflow-hidden px-8 py-4 rounded-xl font-bold text-white shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-red-500/30">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-500 transition-all duration-300 group-hover:scale-110">
                            </div>
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-500 opacity-0 group-hover:opacity-100 blur-xl transition-all duration-300">
                            </div>
                            <div class="relative flex items-center justify-center gap-3">
                                <span class="material-icons animate-bounce">cloud_upload</span>
                                <span class="tracking-wide">UPLOAD YOUR CV</span>
                                <span
                                    class="material-icons transition-transform duration-300 group-hover:translate-x-1">arrow_forward</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <section class="relative z-30 -mt-8 md:-mt-12 pb-12">
            <div class="container mx-auto px-4">
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 max-w-5xl mx-auto relative z-30 overflow-visible">
                    <form action="{{ route('jobs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Keywords</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-icons text-gray-400 text-sm">search</span>
                                </span>
                                <input name="keyword"
                                    class="w-full pl-10 pr-3 py-3 rounded border-gray-200 dark:border-gray-600 dark:bg-navy-dark dark:text-white text-sm focus:border-primary focus:ring-primary"
                                    placeholder="Job Title or Keyword" type="text" />
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Location</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-icons text-gray-400 text-sm">place</span>
                                </span>
                                <input name="location"
                                    class="w-full pl-10 pr-3 py-3 rounded border-gray-200 dark:border-gray-600 dark:bg-navy-dark dark:text-white text-sm focus:border-primary focus:ring-primary"
                                    placeholder="City or Postcode" type="text" />
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Category</label>
                            <div class="relative z-[100]" x-data="{ open: false, selected: 'All Categories' }">
                                <input type="hidden" name="category" :value="selected === 'All Categories' ? '' : selected">
                                <button @click="open = !open" @click.away="open = false"
                                    class="w-full pl-4 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-navy-dark dark:text-white text-sm font-medium focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all cursor-pointer hover:border-red-300 text-left flex items-center justify-between"
                                    type="button">
                                    <span x-text="selected"></span>
                                    <span class="material-icons text-xl text-gray-400 transition-transform duration-300"
                                        :class="{'rotate-180 text-red-500': open}">expand_more</span>
                                </button>

                                <!-- Custom Dropdown Menu -->
                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-2"
                                    class="absolute z-50 w-full mt-2 bg-white dark:bg-navy-dark border border-gray-100 dark:border-gray-600 rounded-xl shadow-xl overflow-hidden"
                                    style="display: none;">
                                    <ul class="py-1">
                                        <li>
                                            <button type="button" @click="selected = 'All Categories'; open = false"
                                                class="w-full px-4 py-2 text-left text-sm hover:bg-red-50 dark:hover:bg-white/5 hover:text-red-600 dark:hover:text-red-400 transition-colors flex items-center justify-between group"
                                                :class="{'text-red-500 font-bold bg-red-50/50 dark:bg-white/5': selected === 'All Categories', 'text-gray-700 dark:text-gray-300': selected !== 'All Categories'}">
                                                <span>All Categories</span>
                                                <span x-show="selected === 'All Categories'"
                                                    class="material-icons text-sm">check</span>
                                            </button>
                                        </li>
                                        @foreach($categories as $cat)
                                            <li>
                                                <button type="button" @click="selected = '{{ $cat['name'] }}'; open = false"
                                                    class="w-full px-4 py-2 text-left text-sm hover:bg-red-50 dark:hover:bg-white/5 hover:text-red-600 dark:hover:text-red-400 transition-colors flex items-center justify-between group"
                                                    :class="{'text-red-500 font-bold bg-red-50/50 dark:bg-white/5': selected === '{{ $cat['name'] }}', 'text-gray-700 dark:text-gray-300': selected !== '{{ $cat['name'] }}'}">
                                                    <span>{{ $cat['name'] }}</span>
                                                    <span x-show="selected === '{{ $cat['name'] }}'"
                                                        class="material-icons text-sm">check</span>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <button type="submit"
                                class="w-full bg-primary hover:bg-red-600 text-white font-bold py-3 px-4 rounded transition duration-300 flex items-center justify-center">
                                FIND A JOB <span class="material-icons ml-2 text-sm">search</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        {{-- Categories data now passed from PageController --}}

        <section class="w-full max-w-[1400px] px-6 py-8 relative z-20 mb-12 mx-auto">
            <div class="border border-white/5 rounded-3xl p-8 shadow-2xl bg-slate-400">
                <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-white mb-2 font-display">Choose Your Realm</h2>
                        <p class="text-white/50">Explore opportunities across different disciplines.</p>
                    </div>
                    <a href="{{ route('jobs.index') }}"
                        class="hidden sm:flex items-center gap-1 text-white hover:text-kingdom-gold transition-colors font-medium text-sm">
                        View all categories
                        <!-- ArrowRight Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 group/realm-grid md:[perspective:1200px]">
                    @foreach($categories as $cat)
                        @php
                            // Replicate the JS replacement logic: 'text-color' -> 'from-color' for gradients
                            $gradientFrom = str_replace('text-', 'from-', $cat['color']);
                        @endphp

                        <a href="{{ route('jobs.index', ['category' => $cat['name']]) }}"
                        class="group bg-white dark:bg-slate-900 border border-gray-200/90 dark:border-slate-800 text-slate-900 dark:text-white hover:bg-slate-900 hover:text-white dark:hover:bg-slate-900 hover:border-kingdom-gold/70 p-6 rounded-3xl flex flex-col items-center text-center transition-all duration-300 ease-out hover:-translate-y-2 hover:scale-[1.03] hover:shadow-2xl motion-reduce:transform-none motion-reduce:transition-none cursor-pointer relative overflow-hidden">
                        <!-- Hover Gradient Background -->
                        <div
                            class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-5 transition-opacity duration-500 {{ $gradientFrom }}/30 to-transparent">
                        </div>

                        <!-- Creative Watermark/Background Decoration -->
                        <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:opacity-20 transition-all duration-500 transform rotate-12 group-hover:rotate-0 group-hover:scale-110 pointer-events-none">
                             @if(Str::startsWith($cat['icon'], 'uploads/'))
                                <img src="{{ asset('media/' . $cat['icon']) }}" class="w-32 h-32 object-contain grayscale-0">
                            @elseif($cat['icon'] === 'shield')
                                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="{{ $cat['color'] }}">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                            @elseif($cat['icon'] === 'line-chart')
                                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="{{ $cat['color'] }}">
                                    <path d="M3 3v18h18" />
                                    <path d="m19 9-5 5-4-4-3 3" />
                                </svg>
                            @elseif($cat['icon'] === 'calendar')
                                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="{{ $cat['color'] }}">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            @elseif($cat['icon'] === 'users')
                                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="{{ $cat['color'] }}">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            @elseif($cat['icon'] === 'headphones')
                                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="{{ $cat['color'] }}">
                                    <path d="M3 17v3a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2Z" />
                                    <path d="M15 17v3a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2Z" />
                                    <path d="M4 17v-9a8 8 0 0 1 16 0v9" />
                                </svg>
                            @elseif($cat['icon'] === 'utensils')
                                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="{{ $cat['color'] }}">
                                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2" />
                                    <path d="M7 2v20" />
                                    <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7" />
                                </svg>
                            @else
                                <!-- Fallback Icon (Briefcase) -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="{{ $cat['color'] }}">
                                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2" />
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                </svg>
                            @endif
                        </div>

                            <div
                                class="size-16 rounded-2xl border border-white/10 shadow-inner flex items-center justify-center mb-4 transition-transform duration-300 group-hover:scale-110"
                                style="background-color: rgba(255, 255, 255, 0.05);">
                                <!-- Icons matching Lucide imports -->
                                @if(Str::startsWith($cat['icon'], 'uploads/'))
                                    <img src="{{ asset('media/' . $cat['icon']) }}" alt="{{ $cat['name'] }}" class="w-8 h-8 object-contain transition-opacity">
                                @elseif($cat['icon'] === 'shield')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="{{ $cat['color'] }}">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    </svg>
                                @elseif($cat['icon'] === 'line-chart')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="{{ $cat['color'] }}">
                                        <path d="M3 3v18h18" />
                                        <path d="m19 9-5 5-4-4-3 3" />
                                    </svg>
                                @elseif($cat['icon'] === 'calendar')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="{{ $cat['color'] }}">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                @elseif($cat['icon'] === 'users')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="{{ $cat['color'] }}">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                @elseif($cat['icon'] === 'headphones')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="{{ $cat['color'] }}">
                                        <path d="M3 17v3a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2Z" />
                                        <path d="M15 17v3a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2Z" />
                                        <path d="M4 17v-9a8 8 0 0 1 16 0v9" />
                                    </svg>
                                @elseif($cat['icon'] === 'utensils')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="{{ $cat['color'] }}">
                                        <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2" />
                                        <path d="M7 2v20" />
                                        <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7" />
                                    </svg>
                                @else
                                    <!-- Fallback Icon (Briefcase) -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="{{ $cat['color'] }}">
                                        <rect width="20" height="14" x="2" y="7" rx="2" ry="2" />
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                    </svg>
                                @endif
                            </div>

                            <h3 class="text-white font-bold text-sm md:text-base mb-0.5">{{ $cat['name'] }}</h3>
                            <p class="text-white/30 text-xs group-hover:text-white/50 transition-colors">{{ $cat['jobs'] }} Jobs
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>



        <section class="py-24 bg-gray-100 dark:bg-slate-900 relative overflow-hidden font-sans">
            <div class="container mx-auto px-4 relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6 leading-tight font-display">
                        Why Choose <br />
                        <span class="text-red-600">Kingdom Recruitments?</span>
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed max-w-2xl mx-auto">
                        We go beyond simple matchmaking. We build careers and empower businesses with the right talent.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Card 1: Advertise Job -->
                    <div class="group hover-card relative bg-white dark:bg-slate-800 p-10 rounded-[2rem] flex flex-col h-full overflow-hidden border border-gray-100 dark:border-slate-700 transition-all duration-700 hover:-translate-y-2 hover:shadow-2xl hover:shadow-red-600/20">
                        <!-- Fill Layer -->
                        <div class="hover-fill absolute inset-0 bg-red-600 z-0 pointer-events-none transition-all duration-1000 ease-in-out" style="clip-path: circle(0% at var(--x, 50%) var(--y, 50%));"></div>
                        
                         <!-- Background Decoration -->
                         <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-1000 z-0 pointer-events-none"></div>

                        <div class="relative z-10 flex flex-col h-full">
                            <div class="mb-6 inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 group-hover:bg-white/20 group-hover:text-white transition-colors duration-700">
                                <span class="material-symbols-outlined text-2xl">campaign</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white group-hover:text-white mb-6 transition-colors duration-700">Advertise Job</h3>
                            <p class="text-gray-500 dark:text-gray-400 group-hover:text-white/90 text-sm leading-relaxed mb-8 flex-grow transition-colors duration-700">
                                Kingdom Recruitments is a UK-based job site that specializes in recruitment for the hospitality, leisure, and tourism industries.
                            </p>
                            
                            <button onclick="openModal('advertise')"
                                class="inline-flex items-center text-red-600 dark:text-red-400 font-bold text-xs tracking-widest uppercase gap-2 group-hover:gap-3 group-hover:text-white transition-all duration-700">
                                KNOW MORE <span class="material-icons text-sm">arrow_forward</span>
                            </button>
                        </div>
                    </div>

                    <!-- Card 2: Recruiter Profiles -->
                    <div class="group hover-card relative bg-white dark:bg-slate-800 p-10 rounded-[2rem] flex flex-col h-full overflow-hidden border border-gray-100 dark:border-slate-700 transition-all duration-700 hover:-translate-y-2 hover:shadow-2xl hover:shadow-red-600/20">
                        <!-- Fill Layer -->
                        <div class="hover-fill absolute inset-0 bg-red-600 z-0 pointer-events-none transition-all duration-1000 ease-in-out" style="clip-path: circle(0% at var(--x, 50%) var(--y, 50%));"></div>

                        <div class="relative z-10 flex flex-col h-full">
                            <div class="mb-6 inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 group-hover:bg-white/20 group-hover:text-white transition-colors duration-700">
                                <span class="material-symbols-outlined text-2xl">person_search</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white group-hover:text-white mb-6 transition-colors duration-700">Recruiter Profiles</h3>
                            <p class="text-gray-500 dark:text-gray-400 group-hover:text-white/90 text-sm leading-relaxed mb-8 flex-grow transition-colors duration-700">
                                As Chairman & CEO of Kingdom Recruitments, Mr. Chowdhury has transformed the organization into a high-performing entity.
                            </p>
                            
                            <button onclick="openModal('recruiter')"
                                class="inline-flex items-center text-red-600 dark:text-red-400 font-bold text-xs tracking-widest uppercase gap-2 group-hover:gap-3 group-hover:text-white transition-all duration-700">
                                KNOW MORE <span class="material-icons text-sm">arrow_forward</span>
                            </button>
                        </div>
                    </div>

                    <!-- Card 3: Find Dream Job -->
                    <div class="group hover-card relative bg-white dark:bg-slate-800 p-10 rounded-[2rem] flex flex-col h-full overflow-hidden border border-gray-100 dark:border-slate-700 transition-all duration-700 hover:-translate-y-2 hover:shadow-2xl hover:shadow-red-600/20">
                        <!-- Fill Layer -->
                        <div class="hover-fill absolute inset-0 bg-red-600 z-0 pointer-events-none transition-all duration-1000 ease-in-out" style="clip-path: circle(0% at var(--x, 50%) var(--y, 50%));"></div>

                        <div class="relative z-10 flex flex-col h-full">
                            <div class="mb-6 inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 group-hover:bg-white/20 group-hover:text-white transition-colors duration-700">
                                <span class="material-symbols-outlined text-3xl">manage_search</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white group-hover:text-white mb-6 transition-colors duration-700">Find Dream Job</h3>
                            <p class="text-gray-500 dark:text-gray-400 group-hover:text-white/90 text-sm leading-relaxed mb-8 flex-grow transition-colors duration-700">
                                Finding your dream job can be a challenging but rewarding process. By utilizing Kingdom Recruitments, you can increase your chances.
                            </p>
                            
                            <button onclick="openModal('dream_job')"
                                class="inline-flex items-center text-red-600 dark:text-red-400 font-bold text-xs tracking-widest uppercase gap-2 group-hover:gap-3 group-hover:text-white transition-all duration-700">
                                KNOW MORE <span class="material-icons text-sm">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </div>

                <style>
                    /* Custom expansion effect */
                    .hover-card:hover .hover-fill {
                        clip-path: circle(200% at var(--x, 50%) var(--y, 50%)) !important;
                    }
                </style>
                
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const cards = document.querySelectorAll('.hover-card');
                        cards.forEach(card => {
                            card.addEventListener('mouseenter', (e) => {
                                const rect = card.getBoundingClientRect();
                                const x = e.clientX - rect.left;
                                const y = e.clientY - rect.top;
                                card.style.setProperty('--x', `${x}px`);
                                card.style.setProperty('--y', `${y}px`);
                            });
                        });
                    });
                </script>
            </div>
        </section>
        <?php
    $companies = [
        ["name" => "TOP LOOK", "logo" => asset('images/top-company/1.png')],
        ["name" => "TROUT IT", "logo" => asset('images/top-company/2.png')],
        ["name" => "SUSSEX LTD", "logo" => asset('images/top-company/3.png')],
        ["name" => "LAWN HOPPER", "logo" => asset('images/top-company/4.png')],
        // Duplicate to ensure smooth marquee flow
        ["name" => "TOP LOOK", "logo" => asset('images/top-company/1.png')],
        ["name" => "TROUT IT", "logo" => asset('images/top-company/2.png')],
        ["name" => "SUSSEX LTD", "logo" => asset('images/top-company/3.png')],
        ["name" => "LAWN HOPPER", "logo" => asset('images/top-company/4.png')],
    ];
                                                                                                ?>

        <section
            class="py-24 bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-white/5 relative overflow-hidden">
            {{-- Gradient Fade Edges --}}
            <div
                class="absolute left-0 top-0 bottom-0 w-20 md:w-40 bg-gradient-to-r from-white dark:from-slate-900 to-transparent z-10 pointer-events-none">
            </div>
            <div
                class="absolute right-0 top-0 bottom-0 w-20 md:w-40 bg-gradient-to-l from-white dark:from-slate-900 to-transparent z-10 pointer-events-none">
            </div>

            <div class="container mx-auto px-4 mb-16 text-center relative z-20 flex flex-col items-center gap-6">
                <div class="inline-block relative">
                    <!-- Glow Effect -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20 rounded-full blur-xl animate-pulse">
                    </div>

                    <h2
                        class="relative text-3xl md:text-5xl font-bold font-display text-white tracking-tight px-8 py-3 bg-gradient-to-r from-kingdom-navy via-blue-800 to-kingdom-navy border border-white/20 rounded-full shadow-xl shadow-blue-900/20">
                        Top Companies
                    </h2>
                </div>

                <p
                    class="text-gray-600 dark:text-gray-400 text-base md:text-lg font-light leading-relaxed max-w-3xl mx-auto">
                    We partner with the UK's leading brands across security, hospitality, events, and accounting. Our clients trust us to deliver exceptional professionals who elevate their business standards.
                </p>
            </div>

            <div class="flex overflow-hidden group select-none py-8">
                {{-- Marquee Row 1 --}}
                <div
                    class="flex animate-marquee whitespace-nowrap min-w-full shrink-0 items-center gap-16 md:gap-32 pr-16 md:pr-32 group-hover:[animation-play-state:paused]">
                    @foreach($companies as $company)
                        <div class="flex items-center gap-4 hover:scale-105 transition-transform duration-300 cursor-pointer">
                            <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}"
                                class="h-10 md:h-12 w-auto object-contain" />
                            <span
                                class="text-lg md:text-2xl font-display font-bold tracking-tight text-kingdom-navy dark:text-white">{{ $company['name'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Marquee Row 2 (Duplicate for seamless loop) --}}
                <div class="flex animate-marquee whitespace-nowrap min-w-full shrink-0 items-center gap-16 md:gap-32 pr-16 md:pr-32 group-hover:[animation-play-state:paused]"
                    aria-hidden="true">
                    @foreach($companies as $company)
                        <div class="flex items-center gap-4 hover:scale-105 transition-transform duration-300 cursor-pointer">
                            <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}"
                                class="h-10 md:h-12 w-auto object-contain" />
                            <span
                                class="text-lg md:text-2xl font-display font-bold tracking-tight text-kingdom-navy dark:text-white">{{ $company['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Employers and Applicants Split Section -->
        <section class="py-20 bg-navy-dark relative overflow-hidden transition-colors duration-300">
            <!-- Original Background Restored -->
            <div class="absolute inset-0 z-0">
                <img alt="Office Background" class="w-full h-full object-cover opacity-20"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCm_YccGUTslakR3retm4axiYnTGb7fm8l4QanUTycjmI7pQFZaxI4ZXBpLwzzzspHY6vTY09jq5NRHYhPBaVjSmzB7m-DQ7_BpIOeS2-dMNJ1HbJYVt6e1ptMyWnExVUFCP7WDoUg4r6tNseemP5dwzg7D7Xk3egKdvVjhs3671888xuEAMKDLbVBtIZEwKknpRn5ndzqmBHsWKXuRSW14OeqOR64GkjGXQd1dJILSXY4SvI2Neq-6VtSXG4PGfg8CbbOc_qFFQmHs" />
                <div class="absolute inset-0 bg-navy-dark/80"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row gap-8 lg:gap-12 justify-center">

                    <!-- Applicants Card (Red - Left) -->
                    <div
                        class="flex-1 group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700">
                        <div
                            class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-kingdom-red to-kingdom-navy transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                        </div>
                        <div class="p-8 lg:p-12 relative z-10 flex flex-col h-full">
                            <div
                                class="w-16 h-16 rounded-2xl bg-kingdom-red/10 dark:bg-kingdom-red/20 flex items-center justify-center mb-8 group-hover:bg-kingdom-red group-hover:text-white transition-colors duration-300">
                                <span
                                    class="material-icons text-3xl text-kingdom-red dark:text-red-400 group-hover:text-white">person_search</span>
                            </div>
                            <h3 class="text-3xl font-serif font-bold text-kingdom-navy dark:text-white mb-4">Looking For a
                                Job (Applicant)</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-8 flex-grow">
                                Your next role could be with one of these top leading organizations
                            </p>
                            <ul class="space-y-3 mb-8">
                                @foreach(['Exclusive Listings', 'Career Coaching', 'Confidential Search'] as $item)
                                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                                        <span
                                            class="material-icons text-kingdom-navy dark:text-gray-400 mr-2 text-sm">check_circle</span>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('portal') }}#applicant-login"
                                class="inline-flex items-center text-kingdom-red font-bold hover:translate-x-2 transition-transform duration-300">
                                Apply Now <span class="material-icons ml-2">double_arrow</span>
                            </a>
                        </div>
                    </div>

                    <!-- Employers Card (Navy - Right) -->
                    <div
                        class="flex-1 group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700">
                        <div
                            class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-kingdom-navy to-kingdom-red transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                        </div>
                        <div class="p-8 lg:p-12 relative z-10 flex flex-col h-full">
                            <div
                                class="w-16 h-16 rounded-2xl bg-kingdom-navy/5 dark:bg-kingdom-navy/20 flex items-center justify-center mb-8 group-hover:bg-kingdom-navy group-hover:text-white transition-colors duration-300">
                                <span
                                    class="material-icons text-3xl text-kingdom-navy dark:text-gray-200 group-hover:text-white">business</span>
                            </div>
                            <h3 class="text-3xl font-serif font-bold text-kingdom-navy dark:text-white mb-4">Are You
                                Recruiting?</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-8 flex-grow">
                                Your next role could be with one of these top leading organizations
                            </p>
                            <ul class="space-y-3 mb-8">
                                @foreach(['48h Shortlist Guarantee', 'AI-Powered Matching', 'Premium Support'] as $item)
                                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                                        <span class="material-icons text-kingdom-red mr-2 text-sm">check_circle</span>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('portal') }}"
                                class="inline-flex items-center text-kingdom-navy dark:text-white font-bold hover:translate-x-2 transition-transform duration-300">
                                Apply Now <span class="material-icons ml-2">double_arrow</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <?php
    $baseQuery = \App\Models\Applicant::whereHas('user', function($q) {
        $q->where('is_active', true);
    })->whereNotIn('email', ['candidate@test.com', 'test2@gmail.com']);

    if ((clone $baseQuery)->where('is_featured', true)->exists()) {
        $rawApplicants = (clone $baseQuery)->where('is_featured', true)->get();
    } else {
        $rawApplicants = $baseQuery->get();
    }

    $applicants = $rawApplicants->map(function($c) {
        $skills = ['Communication', 'Adaptability'];
        if (!empty($c->role)) {
            $skills[] = $c->role;
        }
        return (object) [
            'name' => $c->name,
            'title' => $c->sub_category ?? $c->role ?? 'Applicant',
            'bio' => $c->bio ?? "Highly skilled professional specializing in " . ($c->role ?? 'general recruitment') . ".",
            'image' => $c->image ? $c->profile_photo_url : null,
            'skills' => $skills,
            'moreSkillsCount' => 0,
            'available' => true
        ];
    });
    ?>

        @if($applicants->count() > 0)
        <section class="py-24 bg-background-light dark:bg-background-dark">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-display font-bold text-gray-900 dark:text-white mb-4">Featured
                        Applicants</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">Discover top talent ready to make an
                        impact in your organization.</p>
                </div>

                <div x-data="{ 
                                                                                                                currentIndex: 0, 
                                                                                                                total: {{ count($applicants) }},
                                                                                                                next() { 
                                                                                                                    this.currentIndex = (this.currentIndex + 1) % this.total;
                                                                                                                },
                                                                                                                prev() { 
                                                                                                                    this.currentIndex = (this.currentIndex - 1 + this.total) % this.total;
                                                                                                                }
                                                                                                            }"
                    class="relative w-full max-w-7xl mx-auto flex flex-col items-center justify-center py-8 min-h-[500px]">
                    <!-- Prev Button -->
                    <button @click="prev()"
                        class="absolute left-4 lg:left-8 z-20 w-12 h-12 bg-white dark:bg-slate-800 rounded-full shadow-lg flex items-center justify-center text-primary dark:text-white hover:scale-110 transition-transform cursor-pointer border border-slate-100 dark:border-slate-700 hidden md:flex"
                        aria-label="Previous Applicant" type="button">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>

                    <!-- Cards Stack -->
                    <div class="w-full flex-1 flex items-center justify-center relative">
                        @foreach($applicants as $index => $applicant)
                            <div class="w-full max-w-[960px] bg-white dark:bg-slate-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 ease-out overflow-hidden group absolute inset-0 m-auto border border-gray-100 dark:border-gray-700"
                                :class="currentIndex === {{ $index }} 
                                                                                                                                                                                                                ? 'opacity-100 scale-100 translate-y-0 relative pointer-events-auto z-10' 
                                                                                                                                                                                                                : 'opacity-0 scale-95 translate-y-4 pointer-events-none z-0'">
                                <div class="flex flex-col md:flex-row h-auto md:h-[480px]">
                                    <!-- Left: Portrait Image -->
                                    <div class="w-full md:w-1/2 relative overflow-hidden bg-slate-200">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10">
                                        </div>
                                        @if($applicant->image)
                                        <div class="w-full h-full bg-cover bg-center bg-no-repeat transition-transform duration-700 group-hover:scale-105"
                                            style="background-image: url('{{ $applicant->image }}')" role="img"
                                            aria-label="Portrait of {{ $applicant->name }}"></div>
                                        @else
                                        <div class="w-full h-full bg-gradient-to-br from-[#0F1D33] to-slate-700 flex items-center justify-center" role="img"
                                            aria-label="Portrait of {{ $applicant->name }}">
                                            <span class="text-white text-6xl font-bold select-none opacity-60">
                                                @php
                                                    $words = preg_split('/\s+/', trim($applicant->name ?? ''));
                                                    echo strtoupper(substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1));
                                                @endphp
                                            </span>
                                        </div>
                                        @endif

                                        <!-- Floating Badge -->
                                        @if($applicant->available)
                                            <div
                                                class="absolute top-6 left-6 z-20 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg shadow-sm border border-white/50">
                                                <span
                                                    class="text-green-600 text-xs font-bold tracking-wide flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                                    AVAILABLE NOW
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Right: Content Details -->
                                    <div
                                        class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-between bg-white dark:bg-slate-800 relative">
                                        <div>
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <h2
                                                        class="font-display text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-1">
                                                        {{ $applicant->name }}
                                                    </h2>
                                                    <p class="text-primary font-medium text-base uppercase tracking-wider">
                                                        {{ $applicant->title }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="w-12 h-1 bg-primary/20 rounded-full mt-4 mb-6 relative overflow-hidden">
                                                <div class="absolute left-0 top-0 h-full w-1/2 bg-primary"></div>
                                            </div>

                                            <p
                                                class="text-gray-600 dark:text-gray-300 font-body leading-relaxed text-base mb-8">
                                                {{ $applicant->bio }}
                                            </p>

                                            <!-- Skills Tags -->
                                            <div class="flex flex-wrap gap-2 mb-8">
                                                @foreach($applicant->skills as $skill)
                                                    <span
                                                        class="px-3 py-1 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 rounded-full text-xs font-bold uppercase tracking-wide hover:bg-primary hover:text-white transition-colors cursor-default select-none">
                                                        {{ $skill }}
                                                    </span>
                                                @endforeach

                                                @if(isset($applicant->moreSkillsCount) && $applicant->moreSkillsCount > 0)
                                                    <span
                                                        class="px-3 py-1 bg-gray-50 dark:bg-slate-800 text-gray-400 text-xs font-bold uppercase tracking-wide select-none border border-gray-200 dark:border-slate-700">
                                                        +{{ $applicant->moreSkillsCount }} MORE
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div
                                            class="flex items-center gap-4 pt-6 border-t border-gray-100 dark:border-slate-700">
                                            <button
                                                class="flex-1 h-12 bg-primary hover:bg-red-700 text-white font-bold text-sm rounded-lg transition-all shadow-lg shadow-primary/20 hover:shadow-primary/30 flex items-center justify-center gap-2">
                                                <span>Request Interview</span>
                                                <span class="material-symbols-outlined text-[18px]">send</span>
                                            </button>
                                            <button
                                                class="h-12 w-12 flex items-center justify-center rounded-lg border border-gray-200 dark:border-slate-600 hover:border-primary/30 hover:bg-red-50 dark:hover:bg-slate-700 group/heart transition-all">
                                                <span
                                                    class="material-symbols-outlined text-gray-400 group-hover/heart:text-primary transition-colors text-[24px]">favorite</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Next Button -->
                    <button @click="next()"
                        class="absolute right-4 lg:right-8 z-20 w-12 h-12 bg-white dark:bg-slate-800 rounded-full shadow-lg flex items-center justify-center text-primary dark:text-white hover:scale-110 transition-transform cursor-pointer border border-slate-100 dark:border-slate-700 hidden md:flex"
                        aria-label="Next Applicant" type="button">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                    <!-- Pagination Dots -->
                    <div class="flex items-center justify-center gap-3 mt-8">
                        @foreach($applicants as $index => $applicant)
                            <button @click="currentIndex = {{ $index }}"
                                class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                :class="currentIndex === {{ $index }} 
                                                                                                                                                                                                                ? 'bg-primary scale-125' 
                                                                                                                                                                                                                : 'bg-gray-300 dark:bg-gray-600 hover:bg-primary/50'"
                                aria-label="Go to slide {{ $index + 1 }}" type="button"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        @endif
        <section class="py-24 bg-navy-dark dark:bg-gray-900 relative overflow-hidden">
            <!-- Background Gradient & Pattern -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-slate-900 via-navy-dark to-slate-900 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 z-0">
            </div>
            <div class="absolute inset-0 z-0 opacity-5"
                style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

            <!-- Decoration Top Left (Refined) -->
            <div class="absolute top-0 left-0 z-0">
                <div class="w-0 h-0 border-t-[150px] border-r-[150px] border-t-primary/80 border-r-transparent"></div>
                <div class="absolute top-0 left-0 w-32 h-32 bg-primary/20 blur-3xl rounded-full"></div>
            </div>

            <!-- Decoration Bottom Right (Balance) -->
            <div class="absolute bottom-0 right-0 z-0 rotate-180">
                <div class="w-0 h-0 border-t-[150px] border-r-[150px] border-t-blue-900/20 border-r-transparent"></div>
            </div>

            <div class="container mx-auto px-4 relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8 relative max-w-6xl mx-auto">

                    <!-- Item 1 -->
                    <div class="relative group">
                        <div class="relative w-48 h-48 flex justify-center items-center z-10">
                            <!-- Rotating Border -->
                            <div
                                class="absolute inset-0 rounded-full border-4 border-dashed border-kingdom-gold/50 animate-spin-slow group-hover:paused bg-navy-dark shadow-lg shadow-kingdom-gold/20 md:shadow-none">
                            </div>
                            <!-- Content -->
                            <div class="relative z-10 text-center pointer-events-none">
                                <div class="text-4xl font-bold text-white mb-2">1225</div>
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-kingdom-gold text-2xl">work_outline</span>
                                    <div class="text-white/80 font-bold text-sm uppercase tracking-wide">Job Posted</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="relative group">
                        <div class="relative w-48 h-48 flex justify-center items-center z-10">
                            <!-- Rotating Border -->
                            <div
                                class="absolute inset-0 rounded-full border-4 border-dashed border-blue-400/50 animate-spin-slow group-hover:paused bg-navy-dark shadow-lg shadow-blue-400/20 md:shadow-none">
                            </div>
                            <!-- Content -->
                            <div class="relative z-10 text-center pointer-events-none">
                                <div class="text-4xl font-bold text-white mb-2">145</div>
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-blue-400 text-2xl">folder_shared</span>
                                    <div class="text-white/80 font-bold text-sm uppercase tracking-wide">Job Filled</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="relative group">
                        <div class="relative w-48 h-48 flex justify-center items-center z-10">
                            <!-- Rotating Border -->
                            <div
                                class="absolute inset-0 rounded-full border-4 border-dashed border-emerald-400/50 animate-spin-slow group-hover:paused bg-navy-dark shadow-lg shadow-emerald-400/20 md:shadow-none">
                            </div>
                            <!-- Content -->
                            <div class="relative z-10 text-center pointer-events-none">
                                <div class="text-4xl font-bold text-white mb-2">170</div>
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-emerald-400 text-2xl">domain</span>
                                    <div class="text-white/80 font-bold text-sm uppercase tracking-wide">Companies</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="relative group">
                        <div class="relative w-48 h-48 flex justify-center items-center z-10">
                            <!-- Rotating Border -->
                            <div
                                class="absolute inset-0 rounded-full border-4 border-dashed border-purple-400/50 animate-spin-slow group-hover:paused bg-navy-dark shadow-lg shadow-purple-400/20 md:shadow-none">
                            </div>
                            <!-- Content -->
                            <div class="relative z-10 text-center pointer-events-none">
                                <div class="text-4xl font-bold text-white mb-2">125</div>
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-purple-400 text-2xl">groups</span>
                                    <div class="text-white/80 font-bold text-sm uppercase tracking-wide">Members</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Jobs You May Be Interested In Section -->
        <!-- Jobs You May Be Interested In Section (New Design) -->
        <section
            class="relative flex min-h-[60vh] flex-col items-center justify-center overflow-hidden px-4 py-20 sm:px-6 lg:px-8 bg-gradient-to-b from-transparent to-gray-50/50 dark:to-background-dark/50 group">

            <!-- Particle Background Canvas -->
            <canvas id="particle-canvas" class="absolute inset-0 pointer-events-none z-0 opacity-50"></canvas>

            <!-- Interactive Vignettes & Ambient Glow -->
            <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden">
                <!-- Ambient Drifting Orbs -->
                <div
                    class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/5 dark:bg-primary/10 rounded-full blur-[100px] animate-pulse">
                </div>
                <div
                    class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-red-500/5 dark:bg-red-500/10 rounded-full blur-[80px] animate-float-slow">
                </div>
            </div>

            <!-- Decorative Doodles -->
            <!-- Doodle 1: Top Left -->
            <div class="js-doodle absolute top-[10%] left-[5%] md:left-[15%] w-16 h-16 opacity-80 hidden sm:block animate-float-slow select-none cursor-pointer transition-transform duration-300 ease-out"
                style="transform: rotate(-15deg);">
                <svg class="w-full h-full stroke-primary dark:stroke-blue-300" viewBox="0 0 24 24" fill="none"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M9 21h6v-1.5a2.5 2.5 0 0 0 2.5-2.5v-1.12a7.5 7.5 0 1 0-11 0v1.12A2.5 2.5 0 0 0 9 19.5V21zm2-17h2m-6 4h.01M17 8h.01">
                    </path>
                    <path class="stroke-kingdom-red" d="M12 15l-1 2h2l-1-2"></path>
                </svg>
            </div>

            <!-- Doodle 2: Bottom Right -->
            <div class="js-doodle absolute bottom-[15%] right-[5%] md:right-[15%] w-20 h-20 opacity-90 animate-float-medium select-none cursor-pointer transition-transform duration-300 ease-out"
                style="transform: rotate(-45deg);">
                <svg class="w-full h-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g class="stroke-primary dark:stroke-blue-300" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="8" r="5" />
                        <circle cx="12" cy="8" r="1.5" />
                        <path d="M12 13V19" />
                    </g>
                    <path
                        d="M12 22C12 22 9.5 20.5 9.5 19C9.5 18 10.2 17.2 11 17.2C11.5 17.2 11.8 17.4 12 17.8C12.2 17.4 12.5 17.2 13 17.2C13.8 17.2 14.5 18 14.5 19C14.5 20.5 12 22 12 22Z"
                        class="fill-kingdom-red stroke-kingdom-red" stroke-width="1.5" stroke-linejoin="round" />
                </svg>
            </div>

            <!-- Doodle 3: Top Right -->
            <div class="js-doodle absolute top-[20%] right-[8%] md:right-[20%] w-14 h-14 opacity-60 hidden md:block animate-float-delayed select-none cursor-pointer transition-transform duration-300 ease-out"
                style="transform: rotate(10deg);">
                <svg class="w-full h-full stroke-primary dark:stroke-blue-300" viewBox="0 0 24 24" fill="none"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect height="14" rx="2" ry="2" width="20" x="2" y="7"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
            </div>

            <!-- Doodle 4: Bottom Left -->
            <div class="js-doodle absolute bottom-[20%] left-[8%] md:left-[20%] w-16 h-16 opacity-80 animate-float-fast select-none cursor-pointer transition-transform duration-300 ease-out"
                style="transform: rotate(25deg);">
                <svg class="w-full h-full stroke-kingdom-red" viewBox="0 0 24 24" fill="none" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                    <path class="stroke-primary/50 dark:stroke-white/50" d="M11 8v6M8 11h6"></path>
                </svg>
            </div>

            <!-- Main Content -->
            <div class="relative z-10 flex max-w-4xl flex-col items-center text-center">
                <h1
                    class="mb-6 font-display text-5xl font-bold leading-[1.1] tracking-tight text-slate-900 dark:text-white sm:text-6xl md:text-7xl lg:text-8xl animate-fade-in-up">
                    Jobs You May Be<br /> Interested In
                </h1>
                <p class="mb-10 max-w-2xl text-lg font-normal leading-relaxed text-gray-500 dark:text-gray-300 sm:text-xl font-body animate-fade-in-up"
                    style="animation-delay: 0.2s;">
                    A stable career is out there. We will help you get it!
                </p>

                <!-- Social Proof -->
                <div class="relative flex flex-col items-center animate-fade-in-up" style="animation-delay: 0.4s;">
                    <!-- Animated decorative dots -->
                    <div
                        class="absolute -right-6 top-1/2 w-2 h-2 rounded-full bg-kingdom-red opacity-40 animate-float-fast">
                    </div>
                    <div class="absolute -left-8 top-0 w-3 h-3 rounded-full bg-blue-400 opacity-30 animate-float-slow">
                    </div>
                    <div
                        class="absolute right-12 -bottom-2 w-1.5 h-1.5 rounded-full bg-orange-400 opacity-40 animate-float-medium">
                    </div>

                    <div class="flex -space-x-4">
                        <div class="w-14 h-14 rounded-full border-[3px] border-white dark:border-background-dark bg-orange-100 shadow-sm transition-transform hover:-translate-y-1 hover:scale-105 hover:z-10 bg-cover bg-center"
                            style="background-image: url('https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80');">
                        </div>
                        <div class="w-14 h-14 rounded-full border-[3px] border-white dark:border-background-dark bg-blue-100 shadow-sm transition-transform hover:-translate-y-1 hover:scale-105 hover:z-10 bg-cover bg-center"
                            style="background-image: url('https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=100&q=80');">
                        </div>
                        <div class="w-14 h-14 rounded-full border-[3px] border-white dark:border-background-dark bg-green-100 shadow-sm transition-transform hover:-translate-y-1 hover:scale-105 hover:z-10 bg-cover bg-center"
                            style="background-image: url('https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80');">
                        </div>
                        <div
                            class="flex w-14 h-14 items-center justify-center rounded-full border-[3px] border-white dark:border-background-dark bg-kingdom-navy text-sm font-bold text-white shadow-sm transition-transform hover:-translate-y-1 hover:scale-105 hover:z-10">
                            +2k
                        </div>
                    </div>
                    <p class="mt-4 text-lg font-medium text-slate-900 dark:text-white/90 font-display">
                        Joined the Kingdom this month
                    </p>
                </div>
            </div>

        </section>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Doodle Interaction Logic
                const doodles = document.querySelectorAll('.js-doodle');
                doodles.forEach(doodle => {
                    // Initialize rotation
                    const initialRotation = doodle.style.transform || 'rotate(0deg)';
                    doodle.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        // Pop Animation
                        const svg = doodle.querySelector('svg');
                        if (svg) {
                            svg.animate([{
                                transform: 'scale(1)'
                            }, {
                                transform: 'scale(1.35)'
                            }, {
                                transform: 'scale(1)'
                            }], {
                                duration: 350,
                                easing: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)'
                            });
                        }
                        // Particle Burst
                        const rect = doodle.getBoundingClientRect();
                        const centerX = rect.left + rect.width / 2;
                        const centerY = rect.top + rect.height / 2;
                        const particleCount = 10;
                        const colors = ['#1e3e71', '#E6334D'];
                        for (let i = 0; i < particleCount; i++) {
                            const p = document.createElement('div');
                            const size = Math.random() * 5 + 4; // 4-9px
                            const color = colors[Math.floor(Math.random() * colors.length)];
                            p.style.width = `${size}px`;
                            p.style.height = `${size}px`;
                            p.style.backgroundColor = color;
                            p.style.position = 'fixed';
                            p.style.left = `${centerX}px`;
                            p.style.top = `${centerY}px`;
                            p.style.borderRadius = '50%';
                            p.style.pointerEvents = 'none';
                            p.style.zIndex = '100';
                            document.body.appendChild(p);
                            const angle = Math.random() * Math.PI * 2;
                            const distance = Math.random() * 60 + 40;
                            const tx = Math.cos(angle) * distance;
                            const ty = Math.sin(angle) * distance;
                            const anim = p.animate([{
                                transform: 'translate(-50%, -50%) translate(0, 0) scale(1)',
                                opacity: 1
                            }, {
                                transform: `translate(-50%, -50%) translate(${tx}px, ${ty}px) scale(0)`,
                                opacity: 0
                            }], {
                                duration: Math.random() * 300 + 400,
                                easing: 'cubic-bezier(0, .9, .57, 1)'
                            });
                            anim.onfinish = () => p.remove();
                        }
                    });
                    // Magnetic Effect (Optional but nice)
                    document.addEventListener('mousemove', (e) => {
                        const rect = doodle.getBoundingClientRect();
                        const doodleX = rect.left + rect.width / 2;
                        const doodleY = rect.top + rect.height / 2;
                        const dist = Math.sqrt(Math.pow(e.clientX - doodleX, 2) + Math.pow(e.clientY - doodleY, 2));
                        if (dist < 300) {
                            const strength = (300 - dist) / 300;
                            const moveX = (e.clientX - doodleX) * 0.25 * strength;
                            const moveY = (e.clientY - doodleY) * 0.25 * strength;
                            doodle.style.transform = `translate(${moveX}px, ${moveY}px) ${initialRotation}`;
                        } else {
                            doodle.style.transform = `translate(0px, 0px) ${initialRotation}`;
                        }
                    });
                });
                const canvas = document.getElementById('particle-canvas');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                if (!ctx) return;
                let animationFrameId;
                let particles = [];
                let mouseX = -1000;
                let mouseY = -1000;
                const colors = ['#1e3e71', '#E60026', '#586d8d'];
                class Particle {
                    constructor(w, h) {
                        this.x = Math.random() * w;
                        this.y = Math.random() * h;
                        this.size = Math.random() * 2 + 1;
                        this.color = colors[Math.floor(Math.random() * colors.length)];
                        this.speedX = Math.random() * 0.4 - 0.2;
                        this.speedY = Math.random() * 0.4 - 0.2;
                    }
                    update(w, h) {
                        this.x += this.speedX;
                        this.y += this.speedY;
                        // Mouse repulsion
                        const dx = mouseX - this.x;
                        const dy = mouseY - this.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        const maxDist = 120;
                        if (distance < maxDist) {
                            const force = (maxDist - distance) / maxDist;
                            const dirX = dx / distance;
                            const dirY = dy / distance;
                            this.x -= dirX * force * 2;
                            this.y -= dirY * force * 2;
                        }
                        if (this.x < 0) this.x = w;
                        if (this.x > w) this.x = 0;
                        if (this.y < 0) this.y = h;
                        if (this.y > h) this.y = 0;
                    }
                    draw() {
                        ctx.beginPath();
                        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                        ctx.fillStyle = this.color;
                        ctx.globalAlpha = 0.6;
                        ctx.fill();
                        ctx.globalAlpha = 1;
                    }
                }
                const init = () => {
                    particles = [];
                    const w = canvas.width;
                    const h = canvas.height;
                    const particleCount = Math.min(Math.floor((w * h) / 12000), 100);
                    for (let i = 0; i < particleCount; i++) {
                        particles.push(new Particle(w, h));
                    }
                };
                const animate = () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    particles.forEach((particle) => {
                        particle.update(canvas.width, canvas.height);
                        particle.draw();
                        // Lines to mouse
                        const dx = mouseX - particle.x;
                        const dy = mouseY - particle.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < 120) {
                            ctx.beginPath();
                            ctx.strokeStyle = particle.color;
                            ctx.lineWidth = 0.5;
                            ctx.globalAlpha = (1 - distance / 120) * 0.4;
                            ctx.moveTo(particle.x, particle.y);
                            ctx.lineTo(mouseX, mouseY);
                            ctx.stroke();
                            ctx.globalAlpha = 1;
                        }
                    });
                    animationFrameId = requestAnimationFrame(animate);
                };
                const handleResize = () => {
                    if (canvas.parentElement) {
                        canvas.width = canvas.parentElement.offsetWidth;
                        canvas.height = canvas.parentElement.offsetHeight;
                        init();
                    }
                };
                const handleMouseMove = (e) => {
                    const rect = canvas.getBoundingClientRect();
                    mouseX = e.clientX - rect.left;
                    mouseY = e.clientY - rect.top;
                };
                window.addEventListener('resize', handleResize);
                window.addEventListener('mousemove', handleMouseMove); // Listen on window to catch movement over section
                handleResize();
                animate();
            });
        </script>

        <?php
    $testimonials = [
        [
            "id" => 1,
            "quote" => "Kingdom recruitment's has been helping with our recruitment for years, placing some great applicants with us. They know the sort of people we look for & ensure they only send the right ones over. I look forward to carrying on our relationship & having kingdom recruitment's help fill our roles.",
            "author" => "Mr Sayed",
            "role" => "Event Manager",
            "company" => "Intercontinental Park Lane",
            "image" => "https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=200&auto=format&fit=crop"
        ],
        [
            "id" => 2,
            "quote" => "Whoever I speak to at Kingdom recruitments they are always incredibly supportive & attentive. They always go over & above to provide great applicants who match the brief & are quick with responses. I have enjoyed working with a number of their consultants.",
            "author" => "Event & Banqueting Manager",
            "role" => "Manager",
            "company" => "The Tower Hotel London (Guoman)",
            "image" => "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=200&auto=format&fit=crop"
        ]
    ];
                                                                                                ?>

        <section id="testimonials-section" class="py-16 px-6 relative overflow-hidden bg-[#020c1b]">
            {{-- Background Elements --}}
            <div
                class="absolute top-0 right-0 w-[600px] h-[600px] bg-[#FF3333]/5 rounded-full blur-[120px] pointer-events-none mix-blend-screen">
            </div>

            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-12 h-px bg-[#FF3333]"></span>
                            <span class="text-[#FF3333] text-xs font-bold tracking-[0.2em] uppercase">Executive
                                Endorsements</span>
                        </div>
                        <h2 class="text-3xl md:text-5xl font-bold text-white tracking-tight">
                            Voice of the <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-white to-[#8892b0]">Industry</span>
                        </h2>
                    </div>

                    {{-- Navigation Controls --}}
                    <div class="flex items-center gap-4">
                        <button onclick="prevSlide()"
                            class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-[#8892b0] hover:bg-white/5 hover:text-white hover:border-[#FF3333]/50 transition-all duration-300 group cursor-pointer">
                            <span
                                class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        </button>
                        <button onclick="nextSlide()"
                            class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-[#8892b0] hover:bg-white/5 hover:text-white hover:border-[#FF3333]/50 transition-all duration-300 group cursor-pointer">
                            <span
                                class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                    </div>
                </div>

                {{-- Main Content Container --}}
                <div
                    class="relative bg-[rgba(2,12,27,0.6)] backdrop-blur-md border border-[#FF3333]/20 rounded-2xl p-6 md:p-10 grid grid-cols-1 items-center overflow-hidden">
                    {{-- Decorative Quotation Mark --}}
                    <div class="absolute top-8 left-8 md:top-12 md:left-12 opacity-10 pointer-events-none z-0">
                        <span class="font-serif text-[10rem] leading-none text-[#FF3333]">"</span>
                    </div>

                    {{-- Slides Wrapper --}}
                    <div class="contents"> {{-- Use contents to let slides span the parent grid --}}
                        @foreach($testimonials as $index => $t)
                            <div class="testimonial-slide col-start-1 row-start-1 w-full transition-all duration-500 ease-in-out opacity-0 translate-y-4 pointer-events-none z-0"
                                data-index="{{ $index }}">
                                <div class="grid md:grid-cols-12 gap-8 md:gap-12 items-center relative">

                                    {{-- Text Content --}}
                                    <div class="md:col-span-8 space-y-6">
                                        <p
                                            class="text-xl md:text-2xl lg:text-3xl font-light leading-relaxed text-white relative z-10">
                                            {{ $t['quote'] }}
                                        </p>

                                        <div class="flex flex-col gap-1 relative z-10">
                                            <h4 class="text-xl font-bold text-white">{{ $t['author'] }}</h4>
                                            <div class="flex items-center gap-3 text-sm text-[#8892b0] font-mono">
                                                <span>{{ $t['role'] }}</span>
                                                <span class="w-1 h-1 bg-[#FF3333] rounded-full"></span>
                                                <span class="text-[#FF3333]">{{ $t['company'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Image / Visual --}}
                                    <div class="md:col-span-4 flex justify-center md:justify-end">
                                        <div class="relative w-40 h-40 md:w-56 md:h-56">
                                            <div
                                                class="absolute inset-0 border border-[#FF3333]/20 rounded-full animate-[spin_10s_linear_infinite]">
                                            </div>
                                            <div
                                                class="absolute inset-2 border border-white/10 rounded-full animate-[spin_15s_linear_infinite_reverse]">
                                            </div>
                                            <div
                                                class="absolute inset-4 rounded-full overflow-hidden border-2 border-[#020c1b] grayscale hover:grayscale-0 transition-all duration-500">
                                                <img src="{{ $t['image'] }}" alt="{{ $t['author'] }}"
                                                    class="w-full h-full object-cover" />
                                            </div>

                                            {{-- Tech Dots --}}
                                            <div
                                                class="absolute -top-2 left-1/2 w-2 h-2 bg-[#FF3333] rounded-full shadow-[0_0_10px_#FF3333]">
                                            </div>
                                            <div class="absolute top-1/2 -right-2 w-1 h-1 bg-white rounded-full"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Progress Bar --}}
                    <div id="testimonial-progress"
                        class="absolute bottom-0 left-0 h-1 bg-[#FF3333] transition-all duration-500 ease-linear z-30"
                        style="width: 0%"></div>
                </div>
            </div>

            {{-- Script for Slider Logic --}}
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let currentIndex = 0;
                    const slides = document.querySelectorAll('.testimonial-slide');
                    const totalSlides = slides.length;
                    const progressBar = document.getElementById('testimonial-progress');
                    let interval;
                    let isAnimating = false;

                    function showSlide(index) {
                        if (isAnimating || totalSlides === 0) return;
                        isAnimating = true;

                        // Update slides
                        slides.forEach(slide => {
                            slide.classList.remove('opacity-100', 'translate-y-0', 'z-20', 'pointer-events-auto');
                            slide.classList.add('opacity-0', 'translate-y-4', 'z-0', 'pointer-events-none');
                        });

                        const activeSlide = slides[index];
                        if (activeSlide) {
                            activeSlide.classList.remove('opacity-0', 'translate-y-4', 'z-0', 'pointer-events-none');
                            activeSlide.classList.add('opacity-100', 'translate-y-0', 'z-20', 'pointer-events-auto');
                        }

                        // Update Progress Bar
                        const progressWidth = ((index + 1) / totalSlides) * 100;
                        if (progressBar) progressBar.style.width = `${progressWidth}%`;

                        setTimeout(() => {
                            isAnimating = false;
                        }, 500);
                    }

                    window.nextSlide = function () {
                        currentIndex = (currentIndex + 1) % totalSlides;
                        showSlide(currentIndex);
                        resetTimer();
                    }

                    window.prevSlide = function () {
                        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                        showSlide(currentIndex);
                        resetTimer();
                    }

                    function startTimer() {
                        interval = setInterval(window.nextSlide, 6000);
                    }

                    function resetTimer() {
                        clearInterval(interval);
                        startTimer();
                    }

                    // Initialize
                    if (totalSlides > 0) {
                        showSlide(0);
                        startTimer();
                    }
                });
            </script>
        </section>
        @php
            $articles = [
                [
                    'id' => 1,
                    'category' => "Career Advice",
                    'title' => "How to Introduce Yourself in a Job Interview?",
                    'excerpt' => "First impressions matter. Learn the proven framework for answering 'Tell me about yourself' with confidence.",
                    'readTime' => "5 min read",
                    'date' => "Nov 24, 2023",
                    'image' => "https://images.unsplash.com/photo-1565688534245-05d6b5be184a?q=80&w=800&auto=format&fit=crop",
                    'body' => '<p>Introducing yourself in a job interview is your first and best opportunity to set the tone for the rest of the conversation. Many applicants make the mistake of repeating their resume chronologically, which can quickly bore the interviewer.</p><p>Instead, try the Present-Past-Future formula:</p><ul><li><strong>Present:</strong> Talk briefly about your current role, achievements, and responsibilities.</li><li><strong>Past:</strong> Mention key milestones or experiences that prepared you for this moment.</li><li><strong>Future:</strong> Explain why you are excited about this specific opportunity and how it aligns with your career goals.</li></ul><p>Keep your response under two minutes, practice beforehand, and focus on the value you bring to the table.</p>'
                ],
                [
                    'id' => 2,
                    'category' => "Management",
                    'title' => "Looking for Highly Motivated Product Teams to Build",
                    'excerpt' => "Why culture fit and intrinsic motivation outweigh raw technical skills when scaling your startup.",
                    'readTime' => "4 min read",
                    'date' => "Nov 22, 2023",
                    'image' => "https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop",
                    'body' => '<p>When building teams for high-growth projects, founders often prioritize raw technical prowess above all else. However, experience shows that alignment with company culture and intrinsic motivation are far more critical indicators of long-term success.</p><p>Motivated teams display high resilience, a willingness to learn, and better collaboration. To cultivate motivation, focus on three key pillars:</p><ul><li><strong>Autonomy:</strong> Give your team the freedom to decide how they solve problems.</li><li><strong>Mastery:</strong> Provide resources for continuous learning and professional growth.</li><li><strong>Purpose:</strong> Clearly connect daily tasks to the broader company mission.</li></ul><p>By hiring for attitude and potential, you build a sustainable foundation for innovation.</p>'
                ],
                [
                    'id' => 3,
                    'category' => "Industry Insights",
                    'title' => "The Reason Why Software Developer is the Best Job",
                    'excerpt' => "Analyzing salary trends, remote flexibility, and the creative satisfaction of building the future.",
                    'readTime' => "6 min read",
                    'date' => "Nov 15, 2023",
                    'image' => "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=800&auto=format&fit=crop",
                    'body' => '<p>Software engineering consistently ranks as one of the best careers globally, and it is not hard to see why. The combination of high demand, competitive salaries, and remote work flexibility makes it highly attractive.</p><p>Key factors driving software developer job satisfaction include:</p><ul><li><strong>Continuous Learning:</strong> Tech changes rapidly, offering constant opportunities to master new tools and frameworks.</li><li><strong>Creative Expression:</strong> Coding is fundamentally about problem-solving and building things from scratch, which provides immense creative satisfaction.</li><li><strong>Global Demand:</strong> Skills are highly transferable, allowing developers to work for companies all over the world.</li></ul><p>If you enjoy logical puzzles and constant innovation, a career in software development is an excellent path.</p>'
                ]
            ];
        @endphp

        <section id="news-section" class="py-20 border-t border-gray-100 dark:border-gray-800 scroll-mt-20" x-data="{ 
                                                                                                    selectedArticle: null,
                                                                                                    articles: {{ json_encode($articles) }},
                                                                                                    init() {
                                                                                                        this.$watch('selectedArticle', value => {
                                                                                                            if (value) {
                                                                                                                document.body.style.overflow = 'hidden';
                                                                                                                document.documentElement.classList.add('scrollbar-red');
                                                                                                            } else {
                                                                                                                document.body.style.overflow = '';
                                                                                                                document.documentElement.classList.remove('scrollbar-red');
                                                                                                            }
                                                                                                        });
                                                                                                    },
                                                                                                    viewArticle(article) {
                                                                                                        this.selectedArticle = article;
                                                                                                    },
                                                                                                    closeArticle() {
                                                                                                        this.selectedArticle = null;
                                                                                                    }
                                                                                                }"
            @keydown.escape.window="closeArticle()">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6 px-4 md:px-0 container mx-auto">
                <div class="max-w-2xl">
                    <span class="text-primary font-bold tracking-wider text-sm uppercase mb-2 block">Latest Insights</span>
                    <h2 class="text-primary dark:text-white text-3xl md:text-4xl font-black">News, Tips & Articles</h2>
                </div>
                <button
                    class="hidden md:flex items-center gap-2 text-primary dark:text-white font-bold hover:text-red-600 transition-colors">
                    View all articles <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-4 md:px-0 container mx-auto">
                @foreach($articles as $index => $article)
                    <article class="group cursor-pointer flex flex-col h-full" @click="viewArticle(articles[{{ $index }}])">
                        <div class="relative overflow-hidden rounded-2xl mb-6 aspect-[4/3] bg-gray-100 dark:bg-gray-800">
                            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                                class="object-cover w-full h-full transform group-hover:scale-105 transition-transform duration-700"
                                loading="lazy" />
                            <div class="absolute top-4 left-4">
                                <span
                                    class="bg-primary backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-white uppercase tracking-wide">
                                    {{ $article['category'] }}
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col">
                            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mb-3 font-medium">
                                <span>{{ $article['readTime'] }}</span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span>{{ $article['date'] }}</span>
                            </div>
                            <h3
                                class="text-xl font-bold text-primary dark:text-white mb-3 group-hover:text-red-500 transition-colors line-clamp-2">
                                {{ $article['title'] }}
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-4 line-clamp-2">
                                {{ $article['excerpt'] }}
                            </p>
                            <div class="mt-auto pt-4">
                                <span
                                    class="inline-flex items-center text-primary font-bold text-sm hover:underline group-hover:translate-x-1 transition-transform">
                                    Read More <span class="material-symbols-outlined text-[18px] ml-1">arrow_forward</span>
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <button
                class="md:hidden w-full mt-8 flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-3 rounded-xl text-primary dark:text-white font-bold px-4 mx-auto container">
                View all articles <span class="material-symbols-outlined">arrow_forward</span>
            </button>

            <!-- MODAL VIEW -->
            <template x-if="selectedArticle">
                <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-show="selectedArticle"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/50 dark:bg-black/60 backdrop-blur-sm transition-opacity"
                        @click="closeArticle()"></div>

                    <!-- Modal Panel -->
                    <div class="relative bg-white dark:bg-gray-900 rounded-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col"
                        x-show="selectedArticle" x-transition:enter="transition ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-8 scale-95">
                        <!-- Close Button -->
                        <button @click="closeArticle()"
                            class="absolute top-4 right-4 z-20 w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 text-gray-500 hover:text-red-600 rounded-full shadow-md border border-gray-100 dark:border-gray-700 transition-all hover:scale-110">
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>

                        <!-- Article Content Wrapper -->
                        <div class="p-6 md:p-12">
                            <!-- Article Header -->
                            <header class="mb-10 text-center md:text-left max-w-4xl mx-auto">
                                <div class="flex flex-wrap items-center gap-3 justify-center md:justify-start mb-6">
                                    <span
                                        class="bg-primary px-3 py-1 rounded-full text-xs font-bold text-white uppercase tracking-wide shadow-md"
                                        x-text="selectedArticle.category"></span>
                                    <span class="text-slate-400 dark:text-slate-500 text-sm font-medium"
                                        x-text="selectedArticle.date"></span>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                    <span class="text-slate-400 dark:text-slate-500 text-sm font-medium"
                                        x-text="selectedArticle.readTime"></span>
                                </div>

                                <h1 class="text-3xl md:text-5xl font-black text-primary dark:text-white leading-tight mb-8"
                                    x-text="selectedArticle.title"></h1>

                                <!-- Hero Image -->
                                <div class="rounded-2xl overflow-hidden aspect-video shadow-xl mb-12">
                                    <img :src="selectedArticle.image" :alt="selectedArticle.title"
                                        class="w-full h-full object-cover">
                                </div>
                            </header>

                            <!-- Content Area -->
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 max-w-4xl mx-auto">

                                <!-- Main Content -->
                                <div
                                    class="lg:col-span-8 prose dark:prose-invert prose-lg max-w-none text-slate-600 dark:text-slate-300">
                                    <p class="font-medium text-xl text-primary dark:text-white leading-relaxed mb-6"
                                        x-text="selectedArticle.excerpt"></p>

                                    <div x-html="selectedArticle.body || '<p>Content coming soon...</p>'"></div>
                                </div>

                                <!-- Sidebar / Author Info -->
                                <div class="lg:col-span-4 space-y-8">
                                    <div
                                        class="bg-gray-50 dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm sticky top-0">
                                        <h4
                                            class="font-bold text-primary dark:text-white mb-4 text-sm uppercase tracking-wider">
                                            About the Author</h4>
                                        <div class="flex items-center gap-4 mb-4">
                                                <x-avatar :src="null" name="Sarah Jenkins" size="w-12 h-12" />
                                            <div>
                                                <div class="font-bold text-primary dark:text-white">Sarah Jenkins</div>
                                                <div class="text-xs text-slate-500">Senior Editor</div>
                                            </div>
                                        </div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                                            Sarah writes about career development, leadership, and the future of work. She
                                            has interviewed over 500 industry leaders.
                                        </p>
                                        <button
                                            class="w-full py-2.5 rounded-lg border border-primary dark:border-white text-primary dark:text-white font-bold text-sm hover:bg-primary hover:text-white dark:hover:bg-white dark:hover:text-primary transition-all">
                                            Follow
                                        </button>

                                        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                                            <h4
                                                class="font-bold text-primary dark:text-white mb-4 text-sm uppercase tracking-wider">
                                                Share Article</h4>
                                            <div class="flex gap-2">
                                                <button
                                                    class="p-2 rounded-full bg-white dark:bg-gray-700 text-primary dark:text-white hover:bg-red-500 hover:text-white transition-colors shadow-sm">
                                                    <span class="material-symbols-outlined text-[20px]">link</span>
                                                </button>
                                                <button
                                                    class="p-2 rounded-full bg-white dark:bg-gray-700 text-primary dark:text-white hover:bg-blue-500 hover:text-white transition-colors shadow-sm">
                                                    <span class="material-symbols-outlined text-[20px]">share</span>
                                                </button>
                                                <button
                                                    class="p-2 rounded-full bg-white dark:bg-gray-700 text-primary dark:text-white hover:bg-pink-500 hover:text-white transition-colors shadow-sm">
                                                    <span class="material-symbols-outlined text-[20px]">favorite</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-12 text-center">
                                <button @click="closeArticle()"
                                    class="px-8 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-primary dark:text-white font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                    Close Article
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </section>



    </div>

    @push('scripts')
        @include('partials.modal_script')
        <!-- Script to map category names to Lucide icons -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Reuse the same logic from jobs page
                function getSectorIcon(sectorName) {
                    if (!sectorName) return 'briefcase';
                    
                    const name = sectorName.toLowerCase();
                    
                    if (name.includes('security') || name.includes('sia') || name.includes('guard')) return 'shield';
                    if (name.includes('construct') || name.includes('build') || name.includes('civil')) return 'hard-hat';
                    if (name.includes('engineer') || name.includes('mechanical') || name.includes('electrical')) return 'wrench';
                    if (name.includes('it ') || name.includes('tech') || name.includes('software') || name.includes('developer') || name.includes('data')) return 'monitor';
                    if (name.includes('account') || name.includes('finance') || name.includes('bank') || name.includes('payroll')) return 'line-chart';
                    if (name.includes('health') || name.includes('medical') || name.includes('nurse') || name.includes('care') || name.includes('pharma')) return 'heart-pulse';
                    if (name.includes('event') || name.includes('entertain')) return 'calendar';
                    if (name.includes('hospitality') || name.includes('hotel') || name.includes('cater') || name.includes('chef')) return 'chef-hat';
                    if (name.includes('wait') || name.includes('food') || name.includes('restaurant') || name.includes('bar')) return 'utensils';
                    if (name.includes('customer') || name.includes('support') || name.includes('service') || name.includes('help')) return 'headphones';
                    if (name.includes('retail') || name.includes('sales') || name.includes('shop')) return 'shopping-bag';
                    if (name.includes('warehouse') || name.includes('logistics') || name.includes('drive') || name.includes('deliver') || name.includes('transport')) return 'truck';
                    if (name.includes('educat') || name.includes('teach') || name.includes('tutor') || name.includes('school')) return 'graduation-cap';
                    if (name.includes('legal') || name.includes('law') || name.includes('solicitor')) return 'scale';
                    if (name.includes('market') || name.includes('media') || name.includes('design') || name.includes('creative') || name.includes('pr ')) return 'megaphone';
                    if (name.includes('clean') || name.includes('maintain') || name.includes('janitor')) return 'sparkles';
                    if (name.includes('admin') || name.includes('office') || name.includes('reception') || name.includes('clerk') || name.includes('data entry')) return 'file-text';
                    if (name.includes('hr ') || name.includes('human') || name.includes('recruit') || name.includes('talent')) return 'users';
                    if (name.includes('manufactur') || name.includes('factor') || name.includes('production')) return 'factory';
                    
                    return 'briefcase'; // Default fallback
                }

                // Find all elements with data-lucide starting with "category-icon-"
                const catIcons = document.querySelectorAll('[data-lucide^="category-icon-"]');
                catIcons.forEach(el => {
                    const slug = el.getAttribute('data-lucide').replace('category-icon-', '');
                    // Convert slug back to rough name for matching (e.g., "sia-security" -> "sia security")
                    const roughName = slug.replace(/-/g, ' '); 
                    const mappedIcon = getSectorIcon(roughName);
                    el.setAttribute('data-lucide', mappedIcon);
                });
                
                // If lucide is available globally, trigger rendering for the new icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        </script>
    @endpush
@endsection