@extends('layouts.app')

@section('title', 'Find Executive Jobs - ' . config('app.name', 'Kingdom Recruitments'))

@section('content')
    {{-- Page Specific Assets --}}
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@1.25.0"></script>

    <style>
        /* Override font for this page if needed, or inherit from app */
        .font-manrope { font-family: 'Manrope', sans-serif; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Range Slider Styling */
        input[type=range] {
            -webkit-appearance: none; 
            background: transparent; 
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 20px;
            width: 20px;
            border-radius: 50%;
            background: #E60026;
            cursor: pointer;
            margin-top: -8px; 
            box-shadow: 0 2px 6px rgba(230, 0, 38, 0.4);
        }
        input[type=range]::-webkit-slider-runnable-track {
            width: 100%;
            height: 4px;
            cursor: pointer;
            background: #e2e8f0;
            border-radius: 2px;
        }
        
        /* Animations */
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="font-manrope bg-kingdom-bg dark:bg-background-dark transition-colors duration-300">
        {{-- Hero Section --}}
        <div class="relative w-[95%] mx-auto rounded-3xl overflow-hidden shadow-2xl bg-kingdom-navy pt-32 pb-12 md:pt-48 md:pb-20 mt-0">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#E60026 1px, transparent 1px); background-size: 32px 32px;"></div>
            <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#B89955_1px,transparent_1px)] bg-[size:24px_24px] animate-pulse"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-4">
                    Find Your Next <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-red to-rose-400">Executive Role</span>
                </h1>
                <p class="text-slate-200 text-lg md:text-xl max-w-2xl mb-10 font-medium">
                    Browse hundreds of premium opportunities across the UK's leading sectors.
                </p>

                <div class="w-full max-w-4xl bg-white dark:bg-navy-dark dark:border dark:border-gray-700 p-2 rounded-xl shadow-2xl flex flex-col md:flex-row gap-2 transition-colors duration-300">
                    <div class="flex-1 flex items-center px-4 h-14 bg-slate-50 dark:bg-slate-800/50 rounded-lg md:bg-transparent md:dark:bg-transparent">
                        <i data-lucide="search" class="text-gray-400 mr-3 w-5 h-5"></i>
                        <input id="search-input" class="w-full bg-transparent border-none focus:ring-0 text-kingdom-navy dark:text-white placeholder-gray-400 font-medium outline-none" type="text" placeholder="Job title, keywords, or company" />
                    </div>
                    <div class="hidden md:block w-px h-8 bg-slate-200 dark:bg-gray-700 my-auto"></div>
                    <div class="flex-1 flex items-center px-4 h-14 bg-slate-50 dark:bg-slate-800/50 rounded-lg md:bg-transparent md:dark:bg-transparent">
                        <i data-lucide="map-pin" class="text-gray-400 mr-3 w-5 h-5"></i>
                        <input id="location-input" class="w-full bg-transparent border-none focus:ring-0 text-kingdom-navy dark:text-white placeholder-gray-400 font-medium outline-none" type="text" placeholder="City, county, or postcode" />
                    </div>
                    <button id="search-btn" class="h-14 px-8 bg-kingdom-red hover:bg-red-700 text-white font-bold rounded-lg transition-all shadow-lg flex items-center justify-center gap-2">
                        Search
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <aside class="lg:col-span-3 hidden lg:block sticky top-24 pr-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-kingdom-navy dark:text-white">Filters</h3>
                        <button id="clear-filters" class="text-sm text-kingdom-red font-semibold hover:underline">Clear all</button>
                    </div>
                    
                    <div class="border-b border-slate-200 dark:border-gray-700 pb-6 mb-6">
                        <h4 class="text-sm font-bold text-kingdom-navy dark:text-gray-200 uppercase tracking-wider mb-4">Sector</h4>
                        <div class="relative mb-3">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3 h-3"></i>
                            <input id="sector-search" type="text" placeholder="Search sectors..." class="w-full pl-8 pr-3 py-2 bg-slate-50 dark:bg-navy-dark border border-slate-200 dark:border-gray-700 rounded-lg text-sm outline-none focus:border-kingdom-red focus:ring-1 focus:ring-kingdom-red/20 dark:text-white transition-all placeholder:text-slate-400" />
                        </div>
                        <div id="sector-list" class="space-y-2 max-h-56 overflow-y-auto pr-1"></div>
                    </div>

                    <div class="border-b border-slate-200 dark:border-gray-700 pb-6 mb-6">
                        <h4 class="text-sm font-bold text-kingdom-navy dark:text-gray-200 uppercase tracking-wider mb-4">Annual Salary</h4>
                        <div class="px-1">
                            <input id="salary-range" type="range" min="15000" max="100000" step="5000" value="100000" class="w-full h-1 bg-slate-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer" />
                            <div class="mt-4 flex gap-2 items-center">
                                <div class="bg-white dark:bg-navy-dark border border-slate-300 dark:border-gray-700 rounded px-3 py-2 w-full text-center text-sm font-bold text-kingdom-navy dark:text-white">£15k</div>
                                <span class="text-slate-400 font-medium text-xs">TO</span>
                                <div id="salary-display" class="bg-white dark:bg-navy-dark border border-slate-300 dark:border-gray-700 rounded px-3 py-2 w-full text-center text-sm font-bold text-kingdom-navy dark:text-white">£100k</div>
                            </div>
                        </div>
                    </div>

                    <div class="pb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-kingdom-navy dark:text-gray-200 uppercase tracking-wider">Job Type</h4>
                            <i data-lucide="chevron-up" class="w-4 h-4 text-slate-400 dark:text-gray-500"></i>
                        </div>
                        <div class="space-y-2" id="type-list"></div>
                    </div>

                    <div class="bg-kingdom-navy rounded-xl p-6 text-white mt-8 overflow-hidden relative group">
                        <i data-lucide="rocket" class="absolute -right-4 -bottom-4 text-white/5 w-32 h-32 transform group-hover:scale-110 transition-transform duration-500"></i>
                        <h5 class="text-lg font-bold mb-2 relative z-10">Upload your CV</h5>
                        <p class="text-slate-300 text-sm mb-4 relative z-10">Let top employers find you. It only takes a few seconds.</p>
                        <a href="{{ route('applicants.upload') }}" class="block text-center w-full py-2 bg-kingdom-red hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-colors relative z-10">
                            Upload Now
                        </a>
                    </div>
                </aside>

                <section class="col-span-1 lg:col-span-9 flex flex-col gap-6">
                    
                    {{-- Mobile Filter Toggle (Optional addition for better UX, though not in original) --}}
                    
                    <div id="results-header" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-navy-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-bold text-kingdom-navy dark:text-white">Showing <span id="job-count" class="text-kingdom-red">0</span> executive roles</h2>
                            <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Based on your preferences</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-slate-600 dark:text-gray-400 whitespace-nowrap">Sort by:</span>
                            {{-- Explicit background color for options to ensure readability in all browsers --}}
                            <style>
                                .dark option { background-color: #0F1D33; color: white; }
                            </style>
                            <select id="sort-select" class="bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm font-bold text-kingdom-navy dark:text-white py-2 pl-4 pr-10 cursor-pointer focus:ring-2 focus:ring-kingdom-red/20 outline-none">
                                <option value="relevant" class="dark:bg-navy-dark dark:text-white">Most Relevant</option>
                                <option value="newest" class="dark:bg-navy-dark dark:text-white">Newest First</option>
                                <option value="salary" class="dark:bg-navy-dark dark:text-white">Salary (High to Low)</option>
                            </select>
                        </div>
                    </div>

                    <div id="job-container" class="flex flex-col gap-6 min-h-[800px]">
                        <!-- Jobs injected by JS -->
                    </div>

                    <div id="pagination" class="flex items-center justify-center pt-8 pb-12">
                        <!-- Pagination injected by JS -->
                    </div>

                </section>
            </div>
        </div>
        </div>

        {{-- New Job Notifications Section --}}


        <!-- Confetti Container (hidden but globally available) -->
        <div id="confetti-container" class="fixed top-0 left-0 w-full h-full pointer-events-none z-[100] overflow-hidden"></div>

    </div>

    {{-- Logic Script --}}
    <script>
        // --- DATA ---
        // --- DATA ---
        const JOBS_DATA = @json($jobs);

        const SECTORS = @json($categories);
        const JOB_TYPES = ['Permanent', 'Contract / Interim', 'Part-time'];

        // --- ICON HELPER ---
        function getSectorIcon(sectors) {
            const s = (sectors[0] || '').toLowerCase();

            // Security & Safety
            if (s.includes('security') || s.includes('sia') || s.includes('guard')) return 'shield';
            // Construction & Engineering
            if (s.includes('construct') || s.includes('building') || s.includes('civil')) return 'hard-hat';
            if (s.includes('engineer') || s.includes('mechanical') || s.includes('electrical')) return 'wrench';
            // IT & Technology
            if (s.includes('it') || s.includes('tech') || s.includes('software') || s.includes('developer') || s.includes('data') || s.includes('cyber')) return 'monitor';
            // Finance & Accounting
            if (s.includes('account') || s.includes('finance') || s.includes('banking') || s.includes('audit')) return 'line-chart';
            // Healthcare & Medical
            if (s.includes('health') || s.includes('medical') || s.includes('nurs') || s.includes('pharma') || s.includes('care')) return 'heart-pulse';
            // Events & Entertainment
            if (s.includes('event') || s.includes('entertainment') || s.includes('festival')) return 'calendar';
            // Hospitality & Catering
            if (s.includes('hospital') || s.includes('hotel') || s.includes('catering') || s.includes('chef') || s.includes('kitchen')) return 'chef-hat';
            // Food & Waiting Staff
            if (s.includes('waiting') || s.includes('waiter') || s.includes('food') || s.includes('restaurant') || s.includes('bar')) return 'utensils';
            // Customer Service & Support
            if (s.includes('customer') || s.includes('support') || s.includes('call centre') || s.includes('helpdesk')) return 'headphones';
            // Retail & Sales
            if (s.includes('retail') || s.includes('sales') || s.includes('shop') || s.includes('store')) return 'shopping-bag';
            // Warehouse & Logistics
            if (s.includes('warehouse') || s.includes('logistics') || s.includes('delivery') || s.includes('driver') || s.includes('transport')) return 'truck';
            // Education & Training
            if (s.includes('education') || s.includes('teach') || s.includes('train') || s.includes('tutor')) return 'graduation-cap';
            // Legal
            if (s.includes('legal') || s.includes('law') || s.includes('solicitor')) return 'scale';
            // Marketing & Media
            if (s.includes('market') || s.includes('media') || s.includes('design') || s.includes('creative')) return 'megaphone';
            // Cleaning & Maintenance
            if (s.includes('clean') || s.includes('maint') || s.includes('janitor') || s.includes('facilit')) return 'sparkles';
            // Admin & Office
            if (s.includes('admin') || s.includes('office') || s.includes('recept') || s.includes('secretary')) return 'file-text';
            // HR & Recruitment
            if (s.includes('hr') || s.includes('human') || s.includes('recruit')) return 'users';
            // Manufacturing
            if (s.includes('manufactur') || s.includes('production') || s.includes('factory')) return 'factory';

            return 'briefcase'; // Default
        }

        // --- STATE ---
        let state = {
            salary: 100000,
            selectedSectors: [],
            selectedTypes: [],
            searchQuery: '',
            sectorSearch: '',
            currentPage: 1,
            appliedJobs: @auth @php
                $appliedIds = [];
                $applicant = \App\Models\Applicant::where('email', auth()->user()->email)->first();
                if ($applicant) {
                    $appliedIds = \App\Models\JobApplication::where('applicant_id', $applicant->id)->pluck('job_post_id')->toArray();
                }
            @endphp {!! json_encode($appliedIds) !!} @else [] @endauth,
            savedJobs: []
        };

        // --- DOM ELEMENTS ---
        const salaryRange = document.getElementById('salary-range');
        const salaryDisplay = document.getElementById('salary-display');
        const sectorList = document.getElementById('sector-list');
        const typeList = document.getElementById('type-list');
        const jobContainer = document.getElementById('job-container');
        const jobCount = document.getElementById('job-count');
        const pagination = document.getElementById('pagination');
        const searchInput = document.getElementById('search-input');
        const locationInput = document.getElementById('location-input');
        const searchBtn = document.getElementById('search-btn');
        const sortSelect = document.getElementById('sort-select');
        const sectorSearchInput = document.getElementById('sector-search');
        // Note: global mobile-menu-btn is handled by layout, but we might encounter issues if IDs match. 
        // The layout probably has its own mobile menu. The sidebar here is desktop only anyway (hidden lg:block).

        // --- INIT ---
        function init() {
            // Parse Query Params
            const urlParams = new URLSearchParams(window.location.search);
            const categoryParam = urlParams.get('category');
            
            if(categoryParam) {
                // Find matching sector case-insensitively
                const matchedSector = SECTORS.find(s => s.toLowerCase() === categoryParam.toLowerCase());
                if(matchedSector) {
                    state.selectedSectors.push(matchedSector);
                }
            }

            const keywordParam = urlParams.get('keyword');
            if (keywordParam) {
                state.searchQuery = keywordParam.toLowerCase();
                if(searchInput) searchInput.value = keywordParam;
            }

            const locationParam = urlParams.get('location');
            if (locationParam) {
                state.locationQuery = locationParam.toLowerCase();
                if(locationInput) locationInput.value = locationParam;
            }

            renderFilters();
            renderJobs();
            
            // Event Listeners
            if(salaryRange) {
                // Update display immediately while dragging
                salaryRange.addEventListener('input', (e) => {
                    const val = parseInt(e.target.value);
                    salaryDisplay.textContent = `£${Math.floor(val/1000)}k`;
                });

                // Trigger filter only when drag ends
                salaryRange.addEventListener('change', (e) => {
                    state.salary = parseInt(e.target.value);
                    state.currentPage = 1;
                    renderJobs();
                    scrollToResults();
                });
            }

            const clearFiltersBtn = document.getElementById('clear-filters');
            if(clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', () => {
                    state.salary = 100000;
                    if(salaryRange) salaryRange.value = 100000;
                    if(salaryDisplay) salaryDisplay.textContent = '£100k';
                    state.selectedSectors = [];
                    state.selectedTypes = [];
                    state.searchQuery = '';
                    state.sectorSearch = '';
                    state.currentPage = 1;
                    if(searchInput) searchInput.value = '';
                    if(locationInput) locationInput.value = '';
                    if(sectorSearchInput) sectorSearchInput.value = '';
                    state.locationQuery = '';
                    
                    // Uncheck all boxes
                    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                    
                    renderFilters(); // Re-render to clear hidden sectors
                    renderJobs();
                    scrollToResults();
                });
            }

            if(searchBtn) {
                searchBtn.addEventListener('click', () => {
                    state.searchQuery = searchInput ? searchInput.value.toLowerCase() : '';
                    state.locationQuery = locationInput ? locationInput.value.toLowerCase() : '';
                    state.currentPage = 1;
                    renderJobs();
                    scrollToResults();
                });
            }

            if(sortSelect) {
                sortSelect.addEventListener('change', () => {
                    renderJobs();
                });
            }

            if(sectorSearchInput) {
                sectorSearchInput.addEventListener('input', (e) => {
                    state.sectorSearch = e.target.value.toLowerCase();
                    renderFilters();
                });
            }
            
            // Re-run icons
            if(window.lucide) {
                lucide.createIcons();
            }
        }

        // Start
        document.addEventListener('DOMContentLoaded', init);
        function renderFilters() {
            if(!sectorList) return;
            // Render Sectors
            const filteredSectors = SECTORS.filter(s => s.toLowerCase().includes(state.sectorSearch));
            sectorList.innerHTML = filteredSectors.map(sector => `
                <label class="flex items-center gap-3 cursor-pointer group hover:bg-slate-50 dark:hover:bg-white/5 p-1 rounded transition-colors">
                    <input type="checkbox" value="${sector}" 
                        ${state.selectedSectors.includes(sector) ? 'checked' : ''}
                        onchange="toggleSector('${sector}')"
                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 accent-[#E60026] cursor-pointer" />
                    <span class="text-slate-600 dark:text-slate-400 text-sm font-medium group-hover:text-kingdom-navy dark:group-hover:text-white transition-colors">${sector}</span>
                </label>
            `).join('') || '<p class="text-xs text-slate-400 italic">No sectors found</p>';

            // Render Types
            // Render Types
            if (typeList && typeList.children.length === 0) {
                typeList.innerHTML = JOB_TYPES.map(type => `
                    <label class="flex items-center gap-3 cursor-pointer group hover:bg-slate-50 dark:hover:bg-white/5 p-1 rounded transition-colors">
                        <input type="checkbox" value="${type}" 
                            ${state.selectedTypes.includes(type) ? 'checked' : ''}
                            onchange="toggleType('${type}')"
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 accent-[#E60026] cursor-pointer" />
                        <span class="text-slate-600 dark:text-slate-400 text-sm font-medium group-hover:text-kingdom-navy dark:group-hover:text-white transition-colors">${type}</span>
                    </label>
                `).join('');
            }
        }

        function renderJobs() {
            if(!jobContainer) return;

            // Filter Data
            let filtered = JOBS_DATA.filter(job => {
                const matchSalary = job.salaryMax <= state.salary;
                const matchSector = state.selectedSectors.length === 0 || job.sectors.some(s => state.selectedSectors.includes(s));
                const matchType = state.selectedTypes.length === 0 || state.selectedTypes.includes(job.type);
                const matchSearch = !state.searchQuery || 
                                  job.title.toLowerCase().includes(state.searchQuery) || 
                                  job.company.toLowerCase().includes(state.searchQuery);
                const matchLocation = !state.locationQuery || 
                                    (job.location && job.location.toLowerCase().includes(state.locationQuery));

                return matchSalary && matchSector && matchType && matchSearch && matchLocation;
            });

            // Sort Data
            if(sortSelect) {
                const sortVal = sortSelect.value;
                if (sortVal === 'newest') {
                    // Mock sort by id descending as proxy for date
                    filtered.sort((a, b) => b.id - a.id);
                } else if (sortVal === 'salary') {
                    filtered.sort((a, b) => b.salaryMax - a.salaryMax);
                }
            }

            if(jobCount) jobCount.textContent = filtered.length;

            // Pagination Logic
            const itemsPerPage = 5;
            const totalPages = Math.ceil(filtered.length / itemsPerPage);
            const start = (state.currentPage - 1) * itemsPerPage;
            const pageData = filtered.slice(start, start + itemsPerPage);

            // Render Jobs
            if (pageData.length > 0) {
                jobContainer.innerHTML = pageData.map((job, index) => {
                    const absIndex = start + index;
                    const isDark = absIndex % 2 !== 0; // Alternating pattern
                    const isApplied = state.appliedJobs.includes(job.id);
                    const isSaved = state.savedJobs.includes(job.id);
                    const iconName = getSectorIcon(job.sectors);
                    
                    // Create icon HTML (either custom image or Lucide fallback)
                    const bgIconHtml = job.categoryIcon 
                        ? `<img src="${job.categoryIcon}" class="w-32 h-32 object-contain opacity-20 filter grayscale invert" alt="Category Icon">`
                        : `<i data-lucide="${iconName}" class="w-32 h-32"></i>`;
                        
                    const mainIconHtml = job.categoryIcon
                        ? `<img src="${job.categoryIcon}" class="w-8 h-8 object-contain" alt="Category Icon">`
                        : `<i data-lucide="${iconName}" class="w-8 h-8"></i>`;
                    
                    if (isDark) {
                        return `
                        <article style="animation-delay: ${index * 100}ms" class="bg-gradient-to-r from-kingdom-navy to-slate-800 rounded-xl p-6 shadow-xl border border-slate-700 hover:border-kingdom-red hover:shadow-[0_20px_40px_-15px_rgba(230,0,38,0.3)] hover:scale-[1.02] hover:-translate-y-1 relative group transition-all duration-300 transform fade-in overflow-hidden">
                            <!-- Background Decoration Icon -->
                            <div class="absolute top-0 right-0 p-6 opacity-50 pointer-events-none">
                                ${bgIconHtml}
                            </div>

                            <div class="relative z-10 flex flex-col md:flex-row gap-6">
                                <div class="hidden md:flex flex-shrink-0 w-16 h-16 bg-white/10 backdrop-blur-sm rounded-lg items-center justify-center text-white border border-white/20">
                                    ${mainIconHtml}
                                </div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-xl font-extrabold text-white group-hover:text-kingdom-red transition-colors cursor-pointer"><a href="/jobs/${job.id}">${job.title}</a></h3>
                                            <p class="text-slate-300 font-medium">${job.company}</p>
                                        </div>
                                         <div class="flex items-center gap-2">
                                             <div class="relative group/tooltip cursor-help">
                                                ${job.isFeatured ? '<span class="px-2 py-0.5 rounded text-xs font-bold bg-kingdom-gold/20 text-kingdom-gold border border-kingdom-gold/30">Featured</span>' : ''}
                                                 <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                    Status
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                                </div>
                                             </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-slate-300 my-4">
                                        <div class="relative group/tooltip flex items-center gap-1 cursor-help">
                                            <i data-lucide="pound-sterling" class="w-4 h-4 text-kingdom-red"></i> 
                                            <span class="border-b border-dotted border-slate-500">${job.salaryRange}</span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                Salary
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                            </div>
                                        </div>
                                        <div class="relative group/tooltip flex items-center gap-1 cursor-help">
                                            <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i> 
                                            <span>${job.location}</span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                Location
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                            </div>
                                        </div>
                                        <div class="relative group/tooltip flex items-center gap-1 cursor-help">
                                            <i data-lucide="briefcase" class="w-4 h-4 text-slate-400"></i> 
                                            <span>${job.type}</span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                Job Type
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                            </div>
                                        </div>
                                        <div class="relative group/tooltip flex items-center gap-1 cursor-help">
                                            <i data-lucide="${job.deadline ? 'calendar-clock' : 'clock'}" class="w-4 h-4 text-slate-400"></i> 
                                            <span>${job.deadline ? 'Ends: ' + job.deadline : job.posted}</span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                ${job.deadline ? 'Application Deadline' : 'Date Posted'}
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-slate-300 text-sm leading-relaxed mb-4 line-clamp-2">${job.description}</p>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-white/10">
                                        <div class="flex gap-2">
                                            ${job.tags.map(t => `<span class="px-3 py-1 bg-white/10 text-slate-200 text-xs font-bold rounded-full">${t}</span>`).join('')}
                                        </div>
                                        <div class="flex gap-3">

                                            @if(auth()->guest() || auth()->user()->role === 'applicant' || auth()->user()->role === 'applicant')
                                            <button onclick="applyJob(${job.id})" class="px-5 py-2 text-sm font-bold rounded-lg transition-all shadow-lg flex items-center gap-2 ${isApplied ? 'bg-green-600 text-white cursor-default' : 'bg-white text-kingdom-navy hover:bg-kingdom-red hover:text-white'}">
                                                ${isApplied ? '<span>Applied</span>' : 'Apply Now'}
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>`;
                    } else {
                        // Light variant specific styling for background icon opacity
                        const lightBgIconHtml = job.categoryIcon 
                            ? `<img src="${job.categoryIcon}" class="w-32 h-32 object-contain opacity-[0.05] dark:opacity-[0.1] filter grayscale" alt="Category Icon">`
                            : `<i data-lucide="${iconName}" class="w-32 h-32 text-kingdom-navy dark:text-white"></i>`;

                        return `
                        <article style="animation-delay: ${index * 100}ms" class="bg-white dark:bg-navy-dark rounded-xl p-6 shadow-sm border border-slate-100 dark:border-gray-700 hover:border-kingdom-red hover:shadow-[0_20px_40px_-15px_rgba(230,0,38,0.3)] hover:scale-[1.02] hover:-translate-y-1 relative group transition-all duration-300 transform fade-in overflow-hidden">
                            <!-- Background Decoration Icon -->
                            <div class="absolute top-0 right-0 p-6 opacity-[0.08] dark:opacity-[0.1] pointer-events-none">
                                ${lightBgIconHtml}
                            </div>

                            <div class="flex flex-col md:flex-row gap-6 relative z-10">
                                <div class="hidden md:flex flex-shrink-0 w-16 h-16 bg-blue-50 dark:bg-slate-800 rounded-lg items-center justify-center text-kingdom-navy dark:text-white">
                                    ${mainIconHtml}
                                </div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-xl font-extrabold text-kingdom-navy dark:text-white group-hover:text-kingdom-red transition-colors cursor-pointer"><a href="/jobs/${job.id}">${job.title}</a></h3>
                                            <p class="text-slate-500 dark:text-gray-400 font-medium">${job.company}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                             <div class="relative group/tooltip cursor-help">
                                                ${job.isNew ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">New</span>' : ''}
                                                 <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                    Status
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                                </div>
                                             </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-slate-600 dark:text-gray-300 my-4">
                                        <div class="relative group/tooltip flex items-center gap-1 cursor-help">
                                            <i data-lucide="pound-sterling" class="w-4 h-4 text-kingdom-red"></i> 
                                            <span class="border-b border-dotted border-slate-400">${job.salaryRange}</span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                Salary
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                            </div>
                                        </div>
                                        <div class="relative group/tooltip flex items-center gap-1 cursor-help">
                                            <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i> 
                                            <span>${job.location}</span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                Location
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                            </div>
                                        </div>
                                        <div class="relative group/tooltip flex items-center gap-1 cursor-help">
                                            <i data-lucide="briefcase" class="w-4 h-4 text-slate-400"></i>
                                            <span>${job.type}</span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                Job Type
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                            </div>
                                        </div>
                                        <div class="relative group/tooltip flex items-center gap-1 cursor-help">
                                            <i data-lucide="${job.deadline ? 'calendar-clock' : 'clock'}" class="w-4 h-4 text-slate-400"></i> 
                                            <span>${job.deadline ? 'Ends: ' + job.deadline : job.posted}</span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-kingdom-navy text-white text-xs font-bold rounded shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-20 pb-1">
                                                ${job.deadline ? 'Application Deadline' : 'Date Posted'}
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-kingdom-navy"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-slate-500 dark:text-gray-400 text-sm leading-relaxed mb-4 line-clamp-2">${job.description}</p>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100 dark:border-gray-700">
                                        <div class="flex gap-2">
                                            ${job.tags.map(t => `<span class="px-3 py-1 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-full">${t}</span>`).join('')}
                                        </div>
                                        <div class="flex gap-3">

                                            @if(auth()->guest() || auth()->user()->role === 'applicant' || auth()->user()->role === 'applicant')
                                            <button onclick="applyJob(${job.id})" class="px-5 py-2 text-sm font-bold rounded-lg transition-all shadow-sm flex items-center gap-2 ${isApplied ? 'bg-green-600 text-white border border-green-600' : 'text-kingdom-red dark:text-white border border-kingdom-red/20 dark:border-gray-600 hover:bg-kingdom-red hover:text-white dark:hover:bg-kingdom-red'}">
                                                 ${isApplied ? '<span>Applied</span>' : 'Apply Now'}
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>`;
                    }
                }).join('');
            } else {
                jobContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-navy-dark rounded-xl border border-slate-100 dark:border-gray-700 shadow-sm text-center fade-in">
                        <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-full mb-4">
                            <i data-lucide="search" class="text-slate-300 dark:text-gray-500 w-12 h-12"></i>
                        </div>
                        <h3 class="text-xl font-bold text-kingdom-navy dark:text-white mb-2">No jobs found</h3>
                        <p class="text-slate-500 dark:text-gray-400 max-w-xs mb-6">Try adjusting your filters.</p>
                        <button onclick="document.getElementById('clear-filters').click()" class="px-6 py-2 bg-kingdom-navy text-white font-bold rounded-lg hover:bg-slate-800">Clear filters</button>
                    </div>
                `;
            }

            // Render Pagination
            if (pagination) {
                if (totalPages > 1) {
                    let btns = '';
                    for (let i = 1; i <= totalPages; i++) {
                        btns += `<button onclick="changePage(${i})" class="w-10 h-10 flex items-center justify-center rounded-lg border transition-colors font-medium ${state.currentPage === i ? 'bg-kingdom-red text-white border-kingdom-red shadow-md' : 'border-slate-200 dark:border-gray-700 text-slate-600 dark:text-gray-400 hover:border-kingdom-red hover:text-kingdom-red dark:hover:text-white'}">${i}</button>`;
                    }
                    
                    pagination.innerHTML = `
                        <nav class="flex gap-2">
                            <button onclick="changePage(${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''} class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-gray-700 text-slate-500 dark:text-gray-400 hover:border-kingdom-red hover:text-kingdom-red disabled:opacity-50 disabled:cursor-not-allowed">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            </button>
                            ${btns}
                            <button onclick="changePage(${state.currentPage + 1})" ${state.currentPage === totalPages ? 'disabled' : ''} class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-gray-700 text-slate-500 dark:text-gray-400 hover:border-kingdom-red hover:text-kingdom-red disabled:opacity-50 disabled:cursor-not-allowed">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </nav>
                    `;
                } else {
                    pagination.innerHTML = '';
                }
            }

            // Re-run icons for new elements
            if(window.lucide) {
                lucide.createIcons();
            }
        }

        // --- ACTIONS ---
        // --- ACTIONS ---
        function scrollToResults() {
            const header = document.getElementById('results-header');
            if (!header) return;
            const headerTop = header.getBoundingClientRect().top + window.pageYOffset;
            const offset = 100; // Adjusted offset for better visibility
            
            // Should scroll if we are below the header
            if (window.pageYOffset > headerTop - offset) {
                window.scrollTo({
                    top: headerTop - offset,
                    behavior: 'smooth'
                });
            }
        }

        window.toggleSector = function(sector) {
            if (state.selectedSectors.includes(sector)) {
                state.selectedSectors = state.selectedSectors.filter(s => s !== sector);
            } else {
                state.selectedSectors.push(sector);
            }
            state.currentPage = 1;
            renderJobs();
            scrollToResults();
        };

        window.toggleType = function(type) {
            if (state.selectedTypes.includes(type)) {
                state.selectedTypes = state.selectedTypes.filter(t => t !== type);
            } else {
                state.selectedTypes.push(type);
            }
            state.currentPage = 1;
            renderJobs();
            scrollToResults();
        };

        window.changePage = function(page) {
            state.currentPage = page;
            renderJobs();
            // Always scroll to top of results header on page change
            const header = document.getElementById('results-header');
            if (header) {
                const headerTop = header.getBoundingClientRect().top + window.pageYOffset;
                window.scrollTo({ top: headerTop - 100, behavior: 'smooth' });
            }
        };

        window.toggleSave = function(id) {
            if (state.savedJobs.includes(id)) {
                state.savedJobs = state.savedJobs.filter(jid => jid !== id);
            } else {
                state.savedJobs.push(id);
            }
            renderJobs(); 
        };

        window.applyJob = function(id) {
            @guest
                // Redirect unauthenticated users to the applicant login
                window.location.href = "{{ route('portal') }}#applicant-login";
                return;
            @endguest

            @auth
                @if(auth()->user()->role !== 'applicant' && auth()->user()->role !== 'applicant')
                    Swal.fire({ icon: 'info', title: 'Not Allowed', text: 'Only applicants can apply for jobs.', confirmButtonColor: '#0F1D33' });
                    return;
                @endif
            @endauth

            if (state.appliedJobs.includes(id)) return;

            // Make the API call
            fetch(`/jobs/${id}/apply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    Swal.fire({ icon: 'error', title: 'Application Error', text: data.error, confirmButtonColor: '#0F1D33' });
                    return;
                }
                state.appliedJobs.push(id);
                renderJobs();
            })
            .catch(err => {
                console.error('Apply failed:', err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to apply. Please try again.', confirmButtonColor: '#0F1D33' });
            });
        };

        // Start
        document.addEventListener('DOMContentLoaded', init);

    </script>
@endsection
