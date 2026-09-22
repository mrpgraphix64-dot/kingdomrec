@extends('layouts.app')

@section('title', 'Kingdom Recruitments — Executive Staffing & Talent Solutions')

@section('content')
    <div class="relative bg-slate-50 dark:bg-black font-sans">
        
        <!-- ==========================================
             1. HERO SECTION WITH BRIGHT VIDEO BACKGROUND (CENTERED)
        =========================================== -->
        <header class="relative w-full mx-auto min-h-[78vh] md:min-h-[85vh] overflow-hidden flex flex-col justify-start items-center shadow-2xl pb-32" style="padding-top: 8.5rem;">
            
            <!-- HTML5 Video Backdrop (DiDi-Style Bright & Vivid Video Background) -->
            <div class="absolute inset-0 z-0 bg-slate-900 overflow-hidden">
                <video id="bgHeroVideo" 
                       class="w-full h-full object-cover transition-opacity duration-700 opacity-100 scale-105 brightness-[1.08] contrast-[1.03] saturate-[1.05]" 
                       autoplay 
                       muted 
                       playsinline 
                       poster="{{ asset('images/hero_v3.jpg') }}">
                    <source id="bgHeroSource" src="{{ asset('videos/recruitment-hero.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>

                <!-- DiDi-Style Centered Gradient Overlay for Maximum Legibility & Brightness -->
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/35 to-black/60 dark:from-black/75 dark:via-black/45 dark:to-black/80 pointer-events-none"></div>
                <div class="absolute inset-0 bg-radial from-transparent via-black/20 to-black/50 pointer-events-none"></div>
            </div>

            <!-- DiDi Brand Accent Vector Overlays -->
            <div class="absolute top-0 right-0 w-1/3 h-full z-10 pointer-events-none opacity-20">
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                    <polygon class="fill-primary" opacity="0.9" points="0,0 100,0 100,100"></polygon>
                    <polygon class="fill-red-700" opacity="0.6" points="50,0 100,0 100,50"></polygon>
                </svg>
            </div>

            <!-- Hero Text Content (Horizontally Centered) -->
            <div class="relative z-20 container mx-auto px-4 text-white">
                <div class="max-w-5xl mx-auto text-center flex flex-col items-center">
                    
                    <!-- Live Showcase Pill Badge -->
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-black/40 border border-white/30 backdrop-blur-md mb-6 shadow-2xl">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-bold tracking-widest uppercase text-gray-100">Kingdom Staffing Network</span>
                    </div>

                    <!-- Main Headline (Centered) -->
                    <h1 class="text-3xl md:text-5xl lg:text-6xl font-display font-extrabold leading-[1.15] mb-5 tracking-tight drop-shadow-[0_4px_20px_rgba(0,0,0,0.9)] max-w-5xl">
                        Connecting World-Class Talent <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-gold via-white to-red-400">With Leading UK <br /> Organizations</span>
                    </h1>
                    
                    <!-- Subtitle (Centered) -->
                    <p class="text-base md:text-xl text-gray-100 mb-8 max-w-2xl font-medium leading-relaxed drop-shadow-[0_2px_10px_rgba(0,0,0,0.85)]">
                        Whether you are seeking your next career opportunity or requesting qualified personnel across all sectors, Kingdom Recruitments delivers with speed and precision.
                    </p>

                    <!-- Hero Call to Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('applicants.upload') }}" class="px-7 py-3.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-xl shadow-red-600/30 transition-all duration-300 hover:scale-105 flex items-center gap-2">
                            <span class="material-icons text-base">cloud_upload</span>
                            <span>UPLOAD YOUR CV</span>
                        </a>
                        <a href="{{ route('jobs.index') }}" class="px-7 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/30 backdrop-blur-md text-white font-bold text-sm shadow-xl transition-all duration-300 hover:scale-105 flex items-center gap-2">
                            <span>EXPLORE ALL JOBS</span>
                            <span class="material-icons text-base">arrow_forward</span>
                        </a>
                    </div>

                </div>
            </div>
        </header>

        <!-- ==========================================
             2. DIDI-STYLE ELEVATED FLOATING TAB CARD
        =========================================== -->
        <section class="relative z-40 -mt-20 md:-mt-24 pb-16" x-data="{ activeTab: 'candidate' }">
            <div class="container mx-auto px-4">
                
                <!-- Main Floating Widget Card -->
                <div class="bg-white dark:bg-navy-dark rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 max-w-5xl mx-auto overflow-hidden">
                    
                    <!-- Top Dynamic Segmented Tabs (DiDi Global Style) -->
                    <div class="grid grid-cols-2 md:grid-cols-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/80 dark:bg-black/40">
                        
                        <!-- Tab 1: Candidate / Find Jobs -->
                        <button @click="activeTab = 'candidate'"
                                :class="activeTab === 'candidate' ? 'bg-white dark:bg-navy-dark text-red-600 dark:text-kingdom-gold border-b-4 border-red-600 dark:border-kingdom-gold font-extrabold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/50 dark:hover:bg-white/5 font-semibold'"
                                class="py-5 px-4 text-center transition-all duration-200 flex flex-col md:flex-row items-center justify-center gap-2.5 cursor-pointer">
                            <div :class="activeTab === 'candidate' ? 'bg-red-50 text-red-600 dark:bg-gold/10 dark:text-kingdom-gold' : 'bg-gray-200/60 text-gray-500 dark:bg-white/10 dark:text-gray-400'"
                                 class="p-2 rounded-xl transition-all">
                                <span class="material-icons text-xl">search</span>
                            </div>
                            <div class="text-left">
                                <div class="text-xs uppercase tracking-wider opacity-75 font-semibold">For Candidates</div>
                                <div class="text-sm font-bold">Find Jobs</div>
                            </div>
                        </button>

                        <!-- Tab 2: Employer / Hire Staff -->
                        <button @click="activeTab = 'employer'"
                                :class="activeTab === 'employer' ? 'bg-white dark:bg-navy-dark text-red-600 dark:text-kingdom-gold border-b-4 border-red-600 dark:border-kingdom-gold font-extrabold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/50 dark:hover:bg-white/5 font-semibold'"
                                class="py-5 px-4 text-center transition-all duration-200 flex flex-col md:flex-row items-center justify-center gap-2.5 cursor-pointer">
                            <div :class="activeTab === 'employer' ? 'bg-red-50 text-red-600 dark:bg-gold/10 dark:text-kingdom-gold' : 'bg-gray-200/60 text-gray-500 dark:bg-white/10 dark:text-gray-400'"
                                 class="p-2 rounded-xl transition-all">
                                <span class="material-icons text-xl">groups</span>
                            </div>
                            <div class="text-left">
                                <div class="text-xs uppercase tracking-wider opacity-75 font-semibold">For Employers</div>
                                <div class="text-sm font-bold">Hire Talent</div>
                            </div>
                        </button>

                        <!-- Tab 3: Multi-Sector Staffing -->
                        <button @click="activeTab = 'security'"
                                :class="activeTab === 'security' ? 'bg-white dark:bg-navy-dark text-red-600 dark:text-kingdom-gold border-b-4 border-red-600 dark:border-kingdom-gold font-extrabold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/50 dark:hover:bg-white/5 font-semibold'"
                                class="py-5 px-4 text-center transition-all duration-200 flex flex-col md:flex-row items-center justify-center gap-2.5 cursor-pointer">
                            <div :class="activeTab === 'security' ? 'bg-red-50 text-red-600 dark:bg-gold/10 dark:text-kingdom-gold' : 'bg-gray-200/60 text-gray-500 dark:bg-white/10 dark:text-gray-400'"
                                 class="p-2 rounded-xl transition-all">
                                <span class="material-icons text-xl">category</span>
                            </div>
                            <div class="text-left">
                                <div class="text-xs uppercase tracking-wider opacity-75 font-semibold">Solutions</div>
                                <div class="text-sm font-bold">All Sectors</div>
                            </div>
                        </button>

                        <!-- Tab 4: Partner Network -->
                        <button @click="activeTab = 'partner'"
                                :class="activeTab === 'partner' ? 'bg-white dark:bg-navy-dark text-red-600 dark:text-kingdom-gold border-b-4 border-red-600 dark:border-kingdom-gold font-extrabold shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/50 dark:hover:bg-white/5 font-semibold'"
                                class="py-5 px-4 text-center transition-all duration-200 flex flex-col md:flex-row items-center justify-center gap-2.5 cursor-pointer">
                            <div :class="activeTab === 'partner' ? 'bg-red-50 text-red-600 dark:bg-gold/10 dark:text-kingdom-gold' : 'bg-gray-200/60 text-gray-500 dark:bg-white/10 dark:text-gray-400'"
                                 class="p-2 rounded-xl transition-all">
                                <span class="material-icons text-xl">handshake</span>
                            </div>
                            <div class="text-left">
                                <div class="text-xs uppercase tracking-wider opacity-75 font-semibold">Agencies</div>
                                <div class="text-sm font-bold">Partner Portal</div>
                            </div>
                        </button>

                    </div>

                    <!-- Dynamic Form Content Body -->
                    <div class="p-6 md:p-8">

                        <!-- TAB 1: FIND JOBS -->
                        <div x-show="activeTab === 'candidate'" x-transition:enter="transition ease-out duration-300 opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Search Open Positions Across the UK</h2>
                            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-6">Browse hundreds of SIA security, hospitality, event management, and corporate roles with fast application response.</p>

                            <form action="{{ route('jobs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Job Role or Keywords</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <span class="material-icons text-gray-400 text-sm">search</span>
                                        </span>
                                        <input name="keyword" class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-black/60 dark:text-white text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="e.g. SIA Security Officer, Event Manager" type="text" />
                                    </div>
                                </div>

                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Location / City</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <span class="material-icons text-gray-400 text-sm">place</span>
                                        </span>
                                        <input name="location" class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-black/60 dark:text-white text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="London, Manchester, Birmingham..." type="text" />
                                    </div>
                                </div>

                                <div class="md:col-span-4">
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-6 rounded-xl transition duration-300 shadow-lg shadow-red-600/30 flex items-center justify-center gap-2">
                                        <span>SEARCH JOBS</span>
                                        <span class="material-icons text-sm">arrow_forward</span>
                                    </button>
                                </div>
                            </form>

                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex flex-wrap items-center gap-6 text-xs text-gray-600 dark:text-gray-300 font-medium">
                                    <span class="flex items-center gap-1.5"><span class="material-icons text-emerald-500 text-base">check_circle</span> 100% Free Candidate Application</span>
                                    <span class="flex items-center gap-1.5"><span class="material-icons text-emerald-500 text-base">check_circle</span> Fast Track CV Upload</span>
                                    <span class="flex items-center gap-1.5"><span class="material-icons text-emerald-500 text-base">check_circle</span> Direct Employer Feedback</span>
                                </div>
                                <a href="{{ route('applicants.upload') }}" class="text-xs font-bold text-red-600 dark:text-kingdom-gold hover:underline flex items-center gap-1">
                                    <span>Upload CV Directly</span> <span class="material-icons text-xs">north_east</span>
                                </a>
                            </div>
                        </div>

                        <!-- TAB 2: HIRE TALENT -->
                        <div x-show="activeTab === 'employer'" x-transition:enter="transition ease-out duration-300 opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Request Qualified Staff & Temporary Shifts</h2>
                            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-6">Need pre-screened security, hospitality, or corporate personnel? Submit your requirement for rapid dispatch.</p>

                            <form action="{{ route('contact') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Required Category</label>
                                    <select class="w-full px-4 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-black/60 dark:text-white text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                        <option value="SIA Security">SIA Security Guards & Door Supervisors</option>
                                        <option value="Event Management">Event Staff & Stewards</option>
                                        <option value="Hospitality">Hospitality & Waiting Staff</option>
                                        <option value="Accounting">Finance & Corporate Professionals</option>
                                    </select>
                                </div>

                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Staff Quantity & Location</label>
                                    <input class="w-full px-4 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-black/60 dark:text-white text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="e.g. 5 Guards in Central London" type="text" />
                                </div>

                                <div class="md:col-span-4">
                                    <button type="submit" class="w-full bg-kingdom-navy hover:bg-black dark:bg-kingdom-gold dark:hover:bg-amber-400 dark:text-black text-white font-bold py-3.5 px-6 rounded-xl transition duration-300 shadow-lg flex items-center justify-center gap-2">
                                        <span>REQUEST STAFF TODAY</span>
                                        <span class="material-icons text-sm">send</span>
                                    </button>
                                </div>
                            </form>

                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex flex-wrap items-center gap-6 text-xs text-gray-600 dark:text-gray-300 font-medium">
                                    <span class="flex items-center gap-1.5"><span class="material-icons text-red-500 text-base">verified</span> DBS Checked & Right-to-Work Audited</span>
                                    <span class="flex items-center gap-1.5"><span class="material-icons text-red-500 text-base">bolt</span> 24/7 Rapid Emergency Response</span>
                                    <span class="flex items-center gap-1.5"><span class="material-icons text-red-500 text-base">security</span> Full SIA Compliance</span>
                                </div>
                                <a href="tel:07411154198" class="text-xs font-bold text-kingdom-navy dark:text-white hover:underline flex items-center gap-1">
                                    <span class="material-icons text-sm text-kingdom-gold">call</span> <span>Call Dispatch: 074 1115 4198</span>
                                </a>
                            </div>
                        </div>

                        <!-- TAB 3: ALL SECTORS -->
                        <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-300 opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Multi-Sector Recruitment & Staffing Solutions</h2>
                            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-6">Comprehensive staffing services across Security, Hospitality, Event Management, Corporate Administration, Finance, and Facilities.</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-black/30 flex items-start gap-3">
                                    <div class="p-2.5 rounded-lg bg-blue-500/10 text-blue-500 font-bold"><span class="material-icons">shield</span></div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Security & Guarding</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">SIA Door supervisors, venue security & corporate guarding.</p>
                                    </div>
                                </div>
                                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-black/30 flex items-start gap-3">
                                    <div class="p-2.5 rounded-lg bg-purple-500/10 text-purple-500 font-bold"><span class="material-icons">groups</span></div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Hospitality & Events</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Event stewards, waiting staff, & venue management personnel.</p>
                                    </div>
                                </div>
                                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-black/30 flex items-start gap-3">
                                    <div class="p-2.5 rounded-lg bg-emerald-500/10 text-emerald-500 font-bold"><span class="material-icons">business</span></div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Corporate & Commercial</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Finance, customer service, cleaning & office administration.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Fully vetted, Right-to-Work audited, and compliant personnel across all key sectors.</span>
                                <a href="{{ route('jobs.index') }}" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition duration-300 shadow-md text-center text-sm flex items-center justify-center gap-2">
                                    <span>EXPLORE ALL SECTORS</span>
                                    <span class="material-icons text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                        <!-- TAB 4: PARTNER PORTAL -->
                        <div x-show="activeTab === 'partner'" x-transition:enter="transition ease-out duration-300 opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Agency Sub-Contracting & Vendor Partnership</h2>
                            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-6">Partner with Kingdom Recruitments for shared staffing pools, sub-contracting fulfillment, and tier-1 vendor support.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="p-5 rounded-2xl border border-gray-100 dark:border-gray-800 bg-gradient-to-br from-red-500/5 to-transparent">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">For Partner Agencies</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Collaborate on large-scale shift fulfillment and share talent pools with guaranteed contract compliance.</p>
                                    <a href="{{ route('register.partner') }}" class="text-xs font-bold text-red-600 dark:text-kingdom-gold hover:underline flex items-center gap-1">
                                        <span>Register as Partner Agency</span> <span class="material-icons text-xs">arrow_forward</span>
                                    </a>
                                </div>

                                <div class="p-5 rounded-2xl border border-gray-100 dark:border-gray-800 bg-gradient-to-br from-kingdom-navy/5 to-transparent">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Partner Portal Login</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Access active shift assignments, timesheets, and candidate rosters via our dedicated partner portal.</p>
                                    <a href="{{ route('login') }}" class="text-xs font-bold text-kingdom-navy dark:text-white hover:underline flex items-center gap-1">
                                        <span>Access Partner Login</span> <span class="material-icons text-xs">login</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>        <!-- ==========================================
             3. CATEGORIES SHOWCASE ("CHOOSE YOUR REALM")
        =========================================== -->
        <section class="py-20 md:py-24 bg-gradient-to-b from-slate-50 via-slate-100/80 to-slate-200/60 dark:from-[#060913] dark:via-[#0B1120] dark:to-[#02040A] relative overflow-visible z-20 border-b border-slate-300/80 dark:border-slate-800/80 shadow-md font-sans">
            <!-- Subtle Radial Gradient Backlight (No Grid Lines) -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-500/10 via-amber-500/5 to-transparent pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-9 md:mb-10 gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-kingdom-gold border border-red-200/60 dark:border-red-900/40 text-xs font-bold uppercase tracking-wider mb-2.5 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-kingdom-gold animate-pulse"></span>
                            <span>Specialized Sectors</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white font-display tracking-tight leading-tight">Choose Your Realm</h2>
                    </div>
                    <a href="{{ route('jobs.index') }}"
                       class="group/link inline-flex items-center gap-2 text-sm font-bold text-red-600 dark:text-kingdom-gold hover:text-red-700 dark:hover:text-amber-400 transition-colors">
                        <span>View all categories</span>
                        <span class="material-icons text-base group-hover/link:translate-x-1.5 transition-transform duration-300">arrow_forward</span>
                    </a>
                </div>

                <!-- Grid Layout: Uniform 4 Columns Desktop, 2 Tablet, 1 Mobile -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 overflow-visible">
                    @foreach($categories as $index => $cat)
                        @php
                            $isFeatured = ($index === 0);
                        @endphp

                        <a href="{{ route('jobs.index', ['category' => $cat['name']]) }}"
                           class="group relative rounded-3xl p-6 flex flex-col justify-between overflow-hidden bg-white dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800/90 text-slate-900 dark:text-white shadow-sm transition-all duration-300 ease-out hover:-translate-y-2 hover:scale-[1.02] hover:shadow-2xl hover:border-red-500/50 dark:hover:border-kingdom-gold/50 hover:bg-slate-900 hover:text-white dark:hover:bg-slate-900 h-full min-h-[220px]">
                            
                            <!-- Subtle Ambient Radial Glow on Hover -->
                            <div class="absolute -right-8 -bottom-8 w-44 h-44 rounded-full blur-2xl opacity-0 group-hover:opacity-25 transition-opacity duration-500 bg-gradient-to-br from-red-600 to-amber-400 pointer-events-none"></div>

                            <!-- Top Info Row -->
                            <div class="flex items-center justify-between mb-5 relative z-10">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-kingdom-gold transition-all duration-300 shadow-sm group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-kingdom-gold dark:group-hover:text-black group-hover:scale-105 group-hover:shadow-md">
                                        @if(Str::startsWith($cat['icon'], 'uploads/'))
                                            <img src="{{ asset('media/' . $cat['icon']) }}" alt="{{ $cat['name'] }}" class="w-6 h-6 object-contain">
                                        @elseif($cat['icon'] === 'shield')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                                        @elseif($cat['icon'] === 'line-chart')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18" /><path d="m19 9-5 5-4-4-3 3" /></svg>
                                        @elseif($cat['icon'] === 'calendar')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        @elseif($cat['icon'] === 'users')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 1 0 7.75"></path></svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2" /><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" /></svg>
                                        @endif
                                    </div>
                                    @if($isFeatured)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider uppercase bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-700/60 shadow-sm transition-colors duration-300 group-hover:bg-amber-400/20 group-hover:text-amber-300 group-hover:border-amber-400/40">Featured</span>
                                    @endif
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm transition-all duration-300 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 group-hover:bg-white/15 group-hover:text-white">
                                    {{ $cat['jobs'] }} Jobs
                                </span>
                            </div>

                            <!-- Card Bottom Content -->
                            <div class="relative z-10">
                                <h3 class="text-xl font-bold font-display mb-1.5 transition-colors duration-300 text-slate-900 dark:text-white group-hover:text-white dark:group-hover:text-kingdom-gold">{{ $cat['name'] }}</h3>
                                <p class="text-xs leading-relaxed transition-colors duration-300 line-clamp-2 text-slate-500 dark:text-slate-400 group-hover:text-slate-300">
                                    Pre-screened candidates and immediate shift opportunities across the UK.
                                </p>

                                <!-- View Jobs CTA Indicator on Hover -->
                                <div class="flex items-center text-xs font-bold transition-all duration-300 mt-4 gap-1.5 text-red-600 dark:text-kingdom-gold group-hover:text-amber-400 group-hover:translate-x-1">
                                    <span>View Jobs</span>
                                    <span class="material-icons text-sm transform group-hover:translate-x-1 transition-transform duration-300">arrow_forward</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ==========================================
             4. WHY CHOOSE KINGDOM (BENTO FEATURE CARDS WITH CURSORGRID BACKDROP)
        =========================================== -->
        <section id="whyChooseKingdomSection" class="py-20 bg-slate-100 dark:bg-black relative overflow-hidden font-sans border-t border-gray-200/80 dark:border-gray-900">
            <div class="container mx-auto px-4 relative z-10 max-w-7xl">
                <div class="text-center mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-red-600 dark:text-kingdom-gold mb-2 block">Our Value Proposition</span>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-4 leading-tight font-display">
                        Why Choose Kingdom Recruitments?
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 text-base md:text-lg leading-relaxed max-w-2xl mx-auto">
                        We go beyond simple matchmaking. We build long-term careers and empower businesses with audited, compliant talent.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Feature 01 -->
                    <div class="group relative bg-white/90 dark:bg-navy-dark/90 backdrop-blur-sm p-8 rounded-3xl flex flex-col justify-between h-full border border-gray-200/80 dark:border-gray-800 shadow-xl transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-2xl">campaign</span>
                                </div>
                                <span class="text-3xl font-extrabold font-mono text-gray-200 dark:text-gray-800 group-hover:text-red-600 transition-colors">01</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Advertise Job</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">
                                Kingdom Recruitments is a UK-based job platform specializing in high-volume recruitment for hospitality, SIA security, events, and corporate sectors.
                            </p>
                        </div>
                        <button onclick="openModal('advertise')" class="inline-flex items-center text-red-600 dark:text-kingdom-gold font-bold text-xs tracking-widest uppercase gap-2 hover:gap-3 transition-all">
                            <span>KNOW MORE</span> <span class="material-icons text-sm">arrow_forward</span>
                        </button>
                    </div>

                    <!-- Feature 02 -->
                    <div class="group relative bg-white/90 dark:bg-navy-dark/90 backdrop-blur-sm p-8 rounded-3xl flex flex-col justify-between h-full border border-gray-200/80 dark:border-gray-800 shadow-xl transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-2xl">person_search</span>
                                </div>
                                <span class="text-3xl font-extrabold font-mono text-gray-200 dark:text-gray-800 group-hover:text-blue-600 transition-colors">02</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Recruiter Profiles</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">
                                Led by Mr. Chowdhury (Chairman &amp; CEO), our seasoned recruiters bring decades of industry expertise to deliver high-performing candidate shortlists.
                            </p>
                        </div>
                        <button onclick="openModal('recruiter')" class="inline-flex items-center text-blue-600 dark:text-blue-400 font-bold text-xs tracking-widest uppercase gap-2 hover:gap-3 transition-all">
                            <span>KNOW MORE</span> <span class="material-icons text-sm">arrow_forward</span>
                        </button>
                    </div>

                    <!-- Feature 03 -->
                    <div class="group relative bg-white/90 dark:bg-navy-dark/90 backdrop-blur-sm p-8 rounded-3xl flex flex-col justify-between h-full border border-gray-200/80 dark:border-gray-800 shadow-xl transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-2xl">manage_search</span>
                                </div>
                                <span class="text-3xl font-extrabold font-mono text-gray-200 dark:text-gray-800 group-hover:text-emerald-600 transition-colors">03</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Find Dream Job</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">
                                Unlock confidential job vacancies, fast-track interview requests, and personalized career guidance with top UK employers.
                            </p>
                        </div>
                        <button onclick="openModal('dream_job')" class="inline-flex items-center text-emerald-600 dark:text-emerald-400 font-bold text-xs tracking-widest uppercase gap-2 hover:gap-3 transition-all">
                            <span>KNOW MORE</span> <span class="material-icons text-sm">arrow_forward</span>
                        </button>
                    </div>

                </div>
            </div>
        </section>

        @include('partials.cursor_grid_script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const section = document.getElementById('whyChooseKingdomSection');
                if (section && window.initCursorGrid) {
                    window.initCursorGrid(section, {
                        cellSize: 70,
                        color: '#DC2626',
                        radius: 150,
                        falloff: 'smooth',
                        holdTime: 400,
                        fadeDuration: 800,
                        lineWidth: 1.2,
                        maxOpacity: 0.9,
                        fillOpacity: 0.08,
                        gridOpacity: 0.05,
                        cellRadius: 8,
                        clickPulse: true,
                        pulseSpeed: 650
                    });
                }
            });
        </script>

        <!-- ==========================================
             5. TOP COMPANIES PARTNER MARQUEE
        =========================================== -->
        @php
            $companies = [
                ["name" => "TOP LOOK", "logo" => asset('images/top-company/1.png')],
                ["name" => "TROUT IT", "logo" => asset('images/top-company/2.png')],
                ["name" => "SUSSEX LTD", "logo" => asset('images/top-company/3.png')],
                ["name" => "LAWN HOPPER", "logo" => asset('images/top-company/4.png')],
                ["name" => "TOP LOOK", "logo" => asset('images/top-company/1.png')],
                ["name" => "TROUT IT", "logo" => asset('images/top-company/2.png')],
                ["name" => "SUSSEX LTD", "logo" => asset('images/top-company/3.png')],
                ["name" => "LAWN HOPPER", "logo" => asset('images/top-company/4.png')],
            ];
        @endphp

        <section class="py-16 bg-white dark:bg-slate-900 border-y border-gray-200 dark:border-white/5 relative overflow-hidden">
            <div class="container mx-auto px-4 mb-8 text-center relative z-20">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-100 dark:bg-white/10 border border-gray-200 dark:border-white/10 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200 mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    Trusted By Industry Leaders
                </div>
                <h3 class="text-xl md:text-2xl font-bold font-display text-gray-900 dark:text-white">Our Corporate Partner Network</h3>
            </div>

            <div class="flex overflow-hidden group select-none py-2">
                <div class="flex animate-marquee whitespace-nowrap min-w-full shrink-0 items-center gap-16 md:gap-28 pr-16 md:pr-28 group-hover:[animation-play-state:paused]">
                    @foreach($companies as $company)
                        <div class="flex items-center gap-4 hover:scale-105 transition-transform duration-300 cursor-pointer grayscale opacity-70 hover:grayscale-0 hover:opacity-100">
                            <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-10 md:h-12 w-auto object-contain" />
                            <span class="text-lg md:text-xl font-display font-bold tracking-tight text-kingdom-navy dark:text-white">{{ $company['name'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex animate-marquee whitespace-nowrap min-w-full shrink-0 items-center gap-16 md:gap-28 pr-16 md:pr-28 group-hover:[animation-play-state:paused]" aria-hidden="true">
                    @foreach($companies as $company)
                        <div class="flex items-center gap-4 hover:scale-105 transition-transform duration-300 cursor-pointer grayscale opacity-70 hover:grayscale-0 hover:opacity-100">
                            <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-10 md:h-12 w-auto object-contain" />
                            <span class="text-lg md:text-xl font-display font-bold tracking-tight text-kingdom-navy dark:text-white">{{ $company['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ==========================================
             6. EMPLOYERS & APPLICANTS DUAL SPLIT HERO CARDS (WITH STARBORDER EFFECT)
        =========================================== -->
        @include('partials.star_border_styles')
        <section class="py-20 bg-slate-900 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- Candidate Side Card (StarBorder) -->
                    <div class="star-border-container relative rounded-3xl w-full">
                        <div class="border-gradient-bottom" style="background: radial-gradient(circle, #ef4444, transparent 25%); animation-duration: 5s;"></div>
                        <div class="border-gradient-top" style="background: radial-gradient(circle, #ef4444, transparent 25%); animation-duration: 5s;"></div>
                        
                        <div class="inner-content rounded-3xl p-8 lg:p-12 bg-gradient-to-br from-red-950/90 via-black to-slate-900 flex flex-col justify-between h-full border border-red-500/30 shadow-2xl">
                            <div>
                                <div class="w-14 h-14 rounded-2xl bg-red-600/20 text-red-500 flex items-center justify-center mb-6">
                                    <span class="material-icons text-3xl">person_search</span>
                                </div>
                                <h3 class="text-3xl font-display font-bold text-white mb-3">Looking For a Job?</h3>
                                <p class="text-gray-300 text-sm leading-relaxed mb-6">
                                    Discover verified SIA security, hospitality, event management, and corporate roles with top UK employers.
                                </p>
                                <div class="flex flex-wrap gap-2 mb-8">
                                    <span class="px-3 py-1 rounded-full bg-white/10 text-xs text-gray-200 font-medium">Exclusive Listings</span>
                                    <span class="px-3 py-1 rounded-full bg-white/10 text-xs text-gray-200 font-medium">Direct Interview Requests</span>
                                    <span class="px-3 py-1 rounded-full bg-white/10 text-xs text-gray-200 font-medium">Free CV Hosting</span>
                                </div>
                            </div>

                            <!-- Apply CTA Button (StarBorder) -->
                            <a href="{{ route('portal') }}#applicant-login" class="star-border-container group inline-block w-full sm:w-auto">
                                <div class="border-gradient-bottom" style="background: radial-gradient(circle, #f87171, transparent 20%); animation-duration: 4s;"></div>
                                <div class="border-gradient-top" style="background: radial-gradient(circle, #f87171, transparent 20%); animation-duration: 4s;"></div>
                                <div class="inner-content px-7 py-3.5 rounded-xl bg-red-600 group-hover:bg-red-700 text-white font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2 shadow-lg">
                                    <span>APPLY NOW</span> <span class="material-icons text-sm">arrow_forward</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Employer Side Card (StarBorder) -->
                    <div class="star-border-container relative rounded-3xl w-full">
                        <div class="border-gradient-bottom" style="background: radial-gradient(circle, #B89955, transparent 25%); animation-duration: 5s;"></div>
                        <div class="border-gradient-top" style="background: radial-gradient(circle, #B89955, transparent 25%); animation-duration: 5s;"></div>
                        
                        <div class="inner-content rounded-3xl p-8 lg:p-12 bg-gradient-to-br from-navy-dark via-slate-900 to-black flex flex-col justify-between h-full border border-kingdom-gold/30 shadow-2xl">
                            <div>
                                <div class="w-14 h-14 rounded-2xl bg-blue-600/20 text-blue-400 flex items-center justify-center mb-6">
                                    <span class="material-icons text-3xl">business</span>
                                </div>
                                <h3 class="text-3xl font-display font-bold text-white mb-3">Are You Recruiting?</h3>
                                <p class="text-gray-300 text-sm leading-relaxed mb-6">
                                    Access pre-audited, SIA licensed, and DBS checked personnel for temporary shifts or permanent contracts.
                                </p>
                                <div class="flex flex-wrap gap-2 mb-8">
                                    <span class="px-3 py-1 rounded-full bg-white/10 text-xs text-gray-200 font-medium">48h Shortlist Guarantee</span>
                                    <span class="px-3 py-1 rounded-full bg-white/10 text-xs text-gray-200 font-medium">Full SIA Audit</span>
                                    <span class="px-3 py-1 rounded-full bg-white/10 text-xs text-gray-200 font-medium">24/7 Rapid Dispatch</span>
                                </div>
                            </div>

                            <!-- Recruit CTA Button (StarBorder) -->
                            <a href="{{ route('portal') }}" class="star-border-container group inline-block w-full sm:w-auto">
                                <div class="border-gradient-bottom" style="background: radial-gradient(circle, #f59e0b, transparent 20%); animation-duration: 4s;"></div>
                                <div class="border-gradient-top" style="background: radial-gradient(circle, #f59e0b, transparent 20%); animation-duration: 4s;"></div>
                                <div class="inner-content px-7 py-3.5 rounded-xl bg-kingdom-gold group-hover:bg-amber-400 text-black font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2 shadow-lg">
                                    <span>REQUEST CANDIDATES</span> <span class="material-icons text-sm">arrow_forward</span>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ==========================================
             7. FEATURED APPLICANTS SHOWCASE HUB
        =========================================== -->
        @php
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
                    'available' => true
                ];
            });
        @endphp

        @if($applicants->count() > 0)
        <section class="py-20 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-gray-800">
            <div class="container mx-auto px-4 max-w-6xl">
                <div class="text-center mb-12">
                    <span class="text-xs font-bold text-red-600 dark:text-kingdom-gold uppercase tracking-widest mb-1 block">Audited Talent Pool</span>
                    <h2 class="text-3xl md:text-5xl font-display font-bold text-gray-900 dark:text-white mb-2">Featured Applicants</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto text-sm">Discover pre-screened professionals ready for immediate dispatch.</p>
                </div>

                <div x-data="{ 
                        currentIndex: 0, 
                        total: {{ count($applicants) }},
                        next() { this.currentIndex = (this.currentIndex + 1) % this.total; },
                        prev() { this.currentIndex = (this.currentIndex - 1 + this.total) % this.total; }
                     }"
                     class="relative w-full flex flex-col items-center justify-center min-h-[460px]">
                    
                    <button @click="prev()" class="absolute left-2 z-20 w-12 h-12 bg-white dark:bg-slate-800 rounded-full shadow-xl flex items-center justify-center text-red-600 dark:text-white hover:scale-110 transition-transform border border-gray-200 dark:border-slate-700 hidden md:flex" aria-label="Previous">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>

                    <div class="w-full flex-1 flex items-center justify-center relative">
                        @foreach($applicants as $index => $applicant)
                            <div class="w-full max-w-[850px] bg-slate-50 dark:bg-slate-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 ease-out overflow-hidden group absolute inset-0 m-auto border border-gray-200 dark:border-gray-700"
                                :class="currentIndex === {{ $index }} ? 'opacity-100 scale-100 translate-y-0 relative pointer-events-auto z-10' : 'opacity-0 scale-95 translate-y-4 pointer-events-none z-0'">
                                <div class="flex flex-col md:flex-row h-auto md:h-[420px]">
                                    
                                    <div class="w-full md:w-1/2 relative overflow-hidden bg-slate-200">
                                        @if($applicant->image)
                                            <div class="w-full h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $applicant->image }}')"></div>
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-navy-dark to-slate-800 flex items-center justify-center">
                                                <span class="text-white text-5xl font-bold opacity-60">
                                                    @php
                                                        $words = preg_split('/\s+/', trim($applicant->name ?? ''));
                                                        echo strtoupper(substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1));
                                                    @endphp
                                                </span>
                                            </div>
                                        @endif

                                        <div class="absolute top-4 left-4 z-20 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full shadow-sm">
                                            <span class="text-emerald-600 text-xs font-bold flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                AVAILABLE NOW
                                            </span>
                                        </div>
                                    </div>

                                    <div class="w-full md:w-1/2 p-8 flex flex-col justify-between bg-white dark:bg-slate-800">
                                        <div>
                                            <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $applicant->name }}</h3>
                                            <p class="text-red-600 dark:text-kingdom-gold font-bold text-xs uppercase tracking-wider mb-4">{{ $applicant->title }}</p>
                                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-6">{{ $applicant->bio }}</p>

                                            <div class="flex flex-wrap gap-2 mb-6">
                                                @foreach($applicant->skills as $skill)
                                                    <span class="px-3 py-1 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-bold uppercase">
                                                        {{ $skill }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <a href="{{ route('contact') }}" class="py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-md text-center flex items-center justify-center gap-2">
                                            <span>Request Interview</span>
                                            <span class="material-symbols-outlined text-[16px]">send</span>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button @click="next()" class="absolute right-2 z-20 w-12 h-12 bg-white dark:bg-slate-800 rounded-full shadow-xl flex items-center justify-center text-red-600 dark:text-white hover:scale-110 transition-transform border border-gray-200 dark:border-slate-700 hidden md:flex" aria-label="Next">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>

                    <div class="flex items-center justify-center gap-2 mt-6">
                        @foreach($applicants as $index => $applicant)
                            <button @click="currentIndex = {{ $index }}" class="w-2.5 h-2.5 rounded-full transition-all duration-300" :class="currentIndex === {{ $index }} ? 'bg-red-600 scale-125' : 'bg-gray-300 dark:bg-gray-600'"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ==========================================
             8. LIVE METRICS COUNTER BAR
        =========================================== -->
        <section class="py-16 bg-navy-dark text-white relative overflow-hidden">
            <div class="container mx-auto px-4 relative z-10 max-w-6xl">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">

                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <div class="text-4xl md:text-5xl font-extrabold text-kingdom-gold mb-2 font-display">1,225+</div>
                        <div class="text-gray-300 font-bold text-xs uppercase tracking-wider">Jobs Posted</div>
                    </div>

                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <div class="text-4xl md:text-5xl font-extrabold text-blue-400 mb-2 font-display">145+</div>
                        <div class="text-gray-300 font-bold text-xs uppercase tracking-wider">Jobs Filled</div>
                    </div>

                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <div class="text-4xl md:text-5xl font-extrabold text-emerald-400 mb-2 font-display">170+</div>
                        <div class="text-gray-300 font-bold text-xs uppercase tracking-wider">Companies</div>
                    </div>

                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <div class="text-4xl md:text-5xl font-extrabold text-purple-400 mb-2 font-display">125+</div>
                        <div class="text-gray-300 font-bold text-xs uppercase tracking-wider">Team Members</div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ==========================================
             9. INTERACTIVE DOODLES & PARTICLE CANVAS
        =========================================== -->
        <section class="relative flex min-h-[45vh] flex-col items-center justify-center overflow-hidden px-4 py-16 bg-gradient-to-b from-transparent to-gray-100/50 dark:to-background-dark/50 group">
            <canvas id="particle-canvas" class="absolute inset-0 pointer-events-none z-0 opacity-50"></canvas>

            <div class="relative z-10 flex max-w-4xl flex-col items-center text-center">
                <h2 class="mb-4 font-display text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Jobs You May Be<br /> Interested In
                </h2>
                <p class="mb-8 max-w-xl text-base md:text-lg text-gray-600 dark:text-gray-300">
                    A stable career is out there. We will help you get it!
                </p>

                <div class="relative flex flex-col items-center">
                    <div class="flex -space-x-4">
                        <div class="w-12 h-12 rounded-full border-2 border-white dark:border-background-dark bg-cover bg-center shadow-md" style="background-image: url('https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80');"></div>
                        <div class="w-12 h-12 rounded-full border-2 border-white dark:border-background-dark bg-cover bg-center shadow-md" style="background-image: url('https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=100&q=80');"></div>
                        <div class="w-12 h-12 rounded-full border-2 border-white dark:border-background-dark bg-cover bg-center shadow-md" style="background-image: url('https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80');"></div>
                        <div class="flex w-12 h-12 items-center justify-center rounded-full border-2 border-white dark:border-background-dark bg-kingdom-navy text-xs font-bold text-white shadow-md">
                            +2k
                        </div>
                    </div>
                    <p class="mt-3 text-sm font-bold text-slate-900 dark:text-white font-display">
                        Joined the Kingdom this month
                    </p>
                </div>
            </div>
        </section>

        <!-- ==========================================
             10. EXECUTIVE ENDORSEMENTS / TESTIMONIALS SLIDER
        =========================================== -->
        @php
            $testimonials = [
                [
                    "id" => 1,
                    "quote" => "Kingdom recruitment's has been helping with our recruitment for years, placing some great applicants with us. They know the sort of people we look for & ensure they only send the right ones over.",
                    "author" => "Mr Sayed",
                    "role" => "Event Manager",
                    "company" => "Intercontinental Park Lane",
                    "image" => "https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=200&auto=format&fit=crop"
                ],
                [
                    "id" => 2,
                    "quote" => "Whoever I speak to at Kingdom recruitments they are always incredibly supportive & attentive. They always go over & above to provide great applicants who match the brief & are quick with responses.",
                    "author" => "Event & Banqueting Manager",
                    "role" => "Manager",
                    "company" => "The Tower Hotel London (Guoman)",
                    "image" => "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=200&auto=format&fit=crop"
                ]
            ];
        @endphp

        <section id="testimonials-section" class="py-20 px-6 relative overflow-hidden bg-[#020c1b]">
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-col md:flex-row items-end justify-between mb-10 gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-10 h-px bg-red-600"></span>
                            <span class="text-red-500 text-xs font-bold tracking-widest uppercase">Executive Endorsements</span>
                        </div>
                        <h2 class="text-3xl md:text-5xl font-bold text-white tracking-tight font-display">
                            Voice of the <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-[#8892b0]">Industry</span>
                        </h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <button onclick="prevSlide()" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-[#8892b0] hover:bg-white/10 hover:text-white transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                        </button>
                        <button onclick="nextSlide()" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-[#8892b0] hover:bg-white/10 hover:text-white transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <div class="relative bg-[rgba(2,12,27,0.8)] backdrop-blur-md border border-red-500/20 rounded-3xl p-8 md:p-12 grid grid-cols-1 items-center overflow-hidden">
                    <div class="contents">
                        @foreach($testimonials as $index => $t)
                            <div class="testimonial-slide col-start-1 row-start-1 w-full transition-all duration-500 ease-in-out opacity-0 translate-y-4 pointer-events-none z-0" data-index="{{ $index }}">
                                <div class="grid md:grid-cols-12 gap-8 items-center relative">
                                    <div class="md:col-span-8 space-y-4">
                                        <p class="text-lg md:text-2xl font-light leading-relaxed text-white relative z-10">
                                            "{{ $t['quote'] }}"
                                        </p>
                                        <div class="flex flex-col gap-1 relative z-10 pt-2">
                                            <h4 class="text-lg font-bold text-white">{{ $t['author'] }}</h4>
                                            <div class="flex items-center gap-2 text-xs text-[#8892b0]">
                                                <span>{{ $t['role'] }}</span>
                                                <span class="w-1 h-1 bg-red-500 rounded-full"></span>
                                                <span class="text-red-400 font-bold">{{ $t['company'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="md:col-span-4 flex justify-center md:justify-end">
                                        <div class="relative w-40 h-40">
                                            <div class="absolute inset-0 border border-red-500/30 rounded-full animate-[spin_10s_linear_infinite]"></div>
                                            <div class="absolute inset-3 rounded-full overflow-hidden border-2 border-[#020c1b]">
                                                <img src="{{ $t['image'] }}" alt="{{ $t['author'] }}" class="w-full h-full object-cover" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="testimonial-progress" class="absolute bottom-0 left-0 h-1 bg-red-600 transition-all duration-500 ease-linear z-30" style="width: 0%"></div>
                </div>
            </div>

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

                        slides.forEach(slide => {
                            slide.classList.remove('opacity-100', 'translate-y-0', 'z-20', 'pointer-events-auto');
                            slide.classList.add('opacity-0', 'translate-y-4', 'z-0', 'pointer-events-none');
                        });

                        const activeSlide = slides[index];
                        if (activeSlide) {
                            activeSlide.classList.remove('opacity-0', 'translate-y-4', 'z-0', 'pointer-events-none');
                            activeSlide.classList.add('opacity-100', 'translate-y-0', 'z-20', 'pointer-events-auto');
                        }

                        const progressWidth = ((index + 1) / totalSlides) * 100;
                        if (progressBar) progressBar.style.width = `${progressWidth}%`;

                        setTimeout(() => { isAnimating = false; }, 500);
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

                    function startTimer() { interval = setInterval(window.nextSlide, 6000); }
                    function resetTimer() { clearInterval(interval); startTimer(); }

                    if (totalSlides > 0) { showSlide(0); startTimer(); }
                });
            </script>
        </section>

        <!-- ==========================================
             11. NEWS, TIPS & ARTICLES READER
        =========================================== -->
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
                    'body' => '<p>Introducing yourself in a job interview is your first and best opportunity to set the tone for the rest of the conversation.</p><p>Use the Present-Past-Future formula:</p><ul><li><strong>Present:</strong> Talk briefly about your current role and achievements.</li><li><strong>Past:</strong> Mention key milestones or experiences.</li><li><strong>Future:</strong> Explain why you are excited about this opportunity.</li></ul>'
                ],
                [
                    'id' => 2,
                    'category' => "Management",
                    'title' => "Looking for Highly Motivated Product Teams to Build",
                    'excerpt' => "Why culture fit and intrinsic motivation outweigh raw technical skills when scaling your startup.",
                    'readTime' => "4 min read",
                    'date' => "Nov 22, 2023",
                    'image' => "https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop",
                    'body' => '<p>When building teams for high-growth projects, founders often prioritize raw technical prowess above all else. However, alignment with company culture and intrinsic motivation are far more critical indicators of long-term success.</p>'
                ],
                [
                    'id' => 3,
                    'category' => "Industry Insights",
                    'title' => "The Reason Why Software Developer is the Best Job",
                    'excerpt' => "Analyzing salary trends, remote flexibility, and the creative satisfaction of building the future.",
                    'readTime' => "6 min read",
                    'date' => "Nov 15, 2023",
                    'image' => "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=800&auto=format&fit=crop",
                    'body' => '<p>Software engineering consistently ranks as one of the best careers globally due to high demand, competitive salaries, and remote work flexibility.</p>'
                ]
            ];
        @endphp

        <section id="news-section" class="py-20 border-t border-gray-100 dark:border-gray-800 bg-slate-50 dark:bg-black"
                 x-data="{ 
                    selectedArticle: null,
                    articles: {{ json_encode($articles) }},
                    viewArticle(article) { this.selectedArticle = article; },
                    closeArticle() { this.selectedArticle = null; }
                 }"
                 @keydown.escape.window="closeArticle()">
            
            <div class="container mx-auto px-4 max-w-6xl">
                <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-6">
                    <div>
                        <span class="text-red-600 dark:text-kingdom-gold font-bold tracking-wider text-xs uppercase mb-1 block">Latest Insights</span>
                        <h2 class="text-gray-900 dark:text-white text-3xl md:text-4xl font-extrabold font-display">News, Tips &amp; Articles</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($articles as $index => $article)
                        <article class="group cursor-pointer flex flex-col h-full bg-white dark:bg-navy-dark rounded-3xl p-5 border border-gray-200/80 dark:border-gray-800 shadow-sm hover:shadow-xl transition-all duration-300" @click="viewArticle(articles[{{ $index }}])">
                            <div class="relative overflow-hidden rounded-2xl mb-4 aspect-[4/3] bg-gray-100 dark:bg-gray-800">
                                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="object-cover w-full h-full transform group-hover:scale-105 transition-transform duration-700" loading="lazy" />
                                <div class="absolute top-3 left-3">
                                    <span class="bg-red-600 px-3 py-1 rounded-full text-xs font-bold text-white uppercase tracking-wide">
                                        {{ $article['category'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex-1 flex flex-col">
                                <div class="flex items-center gap-3 text-xs text-gray-400 mb-2">
                                    <span>{{ $article['readTime'] }}</span>
                                    <span>•</span>
                                    <span>{{ $article['date'] }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-kingdom-gold transition-colors line-clamp-2">
                                    {{ $article['title'] }}
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400 text-xs leading-relaxed mb-4 line-clamp-2">
                                    {{ $article['excerpt'] }}
                                </p>
                                <div class="mt-auto pt-2">
                                    <span class="inline-flex items-center text-red-600 dark:text-kingdom-gold font-bold text-xs hover:underline gap-1">
                                        <span>Read Article</span> <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <!-- Article Modal View -->
            <template x-if="selectedArticle">
                <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="selectedArticle" style="display: none;">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeArticle()"></div>
                    <div class="relative bg-white dark:bg-navy-dark rounded-3xl w-full max-w-4xl max-h-[85vh] overflow-y-auto shadow-2xl p-6 md:p-10 border border-gray-100 dark:border-gray-700">
                        <button @click="closeArticle()" class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-black/50 rounded-full text-gray-600 dark:text-white hover:bg-red-600 hover:text-white transition-all">
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>
                        <header class="mb-6">
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider" x-text="selectedArticle.category"></span>
                            <h2 class="text-2xl md:text-4xl font-extrabold text-gray-900 dark:text-white mt-3 mb-2" x-text="selectedArticle.title"></h2>
                            <div class="text-xs text-gray-400" x-text="selectedArticle.date + ' • ' + selectedArticle.readTime"></div>
                        </header>
                        <div class="rounded-2xl overflow-hidden mb-6 aspect-video">
                            <img :src="selectedArticle.image" class="w-full h-full object-cover">
                        </div>
                        <div class="prose dark:prose-invert max-w-none text-sm text-gray-600 dark:text-gray-300 leading-relaxed" x-html="selectedArticle.body"></div>
                    </div>
                </div>
            </template>
        </section>

    </div>

    @push('scripts')
        @include('partials.modal_script')

        <!-- Video Continuous Loop Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const video = document.getElementById('bgHeroVideo');
                const source = document.getElementById('bgHeroSource');
                const clips = [
                    "{{ asset('videos/recruitment-hero.mp4') }}",
                    "{{ asset('videos/recruitment-hero-2.mp4') }}",
                    "{{ asset('videos/recruitment-hero-3.mp4') }}"
                ];
                let currentClipIndex = 0;

                if (video && source) {
                    video.addEventListener('ended', function () {
                        currentClipIndex = (currentClipIndex + 1) % clips.length;
                        video.classList.add('opacity-0');
                        setTimeout(function () {
                            source.src = clips[currentClipIndex];
                            video.load();
                            video.play().then(function () {
                                video.classList.remove('opacity-0');
                            }).catch(function (err) {
                                console.log('Autoplay error:', err);
                            });
                        }, 300);
                    });
                }
            });
        </script>

        <!-- Particle Canvas Script -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const canvas = document.getElementById('particle-canvas');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                if (!ctx) return;
                let animationFrameId, particles = [], mouseX = -1000, mouseY = -1000;
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
                        const dx = mouseX - this.x, dy = mouseY - this.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < 120) {
                            const force = (120 - distance) / 120;
                            this.x -= (dx / distance) * force * 2;
                            this.y -= (dy / distance) * force * 2;
                        }
                        if (this.x < 0) this.x = w; if (this.x > w) this.x = 0;
                        if (this.y < 0) this.y = h; if (this.y > h) this.y = 0;
                    }
                    draw() {
                        ctx.beginPath(); ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                        ctx.fillStyle = this.color; ctx.globalAlpha = 0.6; ctx.fill(); ctx.globalAlpha = 1;
                    }
                }
                const init = () => {
                    particles = [];
                    const w = canvas.width = canvas.parentElement.offsetWidth;
                    const h = canvas.height = canvas.parentElement.offsetHeight;
                    for (let i = 0; i < Math.min((w * h) / 12000, 100); i++) particles.push(new Particle(w, h));
                };
                const animate = () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    particles.forEach(p => { p.update(canvas.width, canvas.height); p.draw(); });
                    animationFrameId = requestAnimationFrame(animate);
                };
                window.addEventListener('resize', init);
                window.addEventListener('mousemove', e => {
                    const rect = canvas.getBoundingClientRect();
                    mouseX = e.clientX - rect.left; mouseY = e.clientY - rect.top;
                });
                init(); animate();
            });
        </script>
    @endpush
@endsection
