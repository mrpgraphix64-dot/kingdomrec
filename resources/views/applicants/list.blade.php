@extends('layouts.app')

@section('title', 'Applicant Management - Kingdom Recruitments')

@section('content')
    <!-- Manrope Font (Specific to this page design) -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Page-specific font override */
        .font-manrope { font-family: 'Manrope', sans-serif; }
        


        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in-right {
            animation: slideInRight 0.3s ease-out forwards;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }
    </style>

    <div class="font-manrope bg-kingdom-bg text-kingdom-navy min-h-screen flex flex-col overflow-hidden">
        
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-8 flex flex-col overflow-hidden relative">
            
            <!-- Header & Stats -->
            <div class="space-y-6 mb-8">
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-kingdom-navy via-slate-700 to-kingdom-navy tracking-tight drop-shadow-sm mb-1">Applicant Management</h1>
                        <p class="text-slate-500 font-medium text-sm">Track active talent pools and process new applications</p>
                    </div>
                </div>

                <!-- 3D Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Total -->
                    <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 group border border-blue-100 hover:border-blue-300 flex items-center justify-between overflow-hidden relative cursor-default">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-blue-100/50"></div>
                        <div class="relative z-10 p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <div class="relative z-10 text-right">
                            <p class="text-[10px] font-bold text-blue-900/60 uppercase tracking-wider">Total Applicants</p>
                            <p class="text-2xl font-black text-slate-800 group-hover:text-blue-600 transition-colors">{{ $stats['total'] }}</p>
                        </div>
                    </div>

                    <!-- New -->
                    <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 group border border-emerald-100 hover:border-emerald-300 flex items-center justify-between overflow-hidden relative cursor-default">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-emerald-100/50"></div>
                        <div class="relative z-10 p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <div class="relative z-10 text-right">
                            <p class="text-[10px] font-bold text-emerald-900/60 uppercase tracking-wider">New</p>
                            <p class="text-2xl font-black text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $stats['new'] }}</p>
                        </div>
                    </div>

                    <!-- Interviewing -->
                    <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 group border border-amber-100 hover:border-amber-300 flex items-center justify-between overflow-hidden relative cursor-default">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-amber-100/50"></div>
                        <div class="relative z-10 p-3 rounded-xl bg-gradient-to-br from-kingdom-gold to-amber-500 text-white shadow-md shadow-amber-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                        </div>
                        <div class="relative z-10 text-right">
                            <p class="text-[10px] font-bold text-amber-900/60 uppercase tracking-wider">Interviewing</p>
                            <p class="text-2xl font-black text-slate-800 group-hover:text-kingdom-gold transition-colors">{{ $stats['interviewing'] }}</p>
                        </div>
                    </div>

                    <!-- Shortlisted -->
                    <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 group border border-violet-100 hover:border-violet-300 flex items-center justify-between overflow-hidden relative cursor-default">
                        <div class="absolute inset-0 bg-gradient-to-br from-violet-50/50 to-violet-100/50"></div>
                        <div class="relative z-10 p-3 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 text-white shadow-md shadow-violet-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                            <i data-lucide="star" class="w-5 h-5"></i>
                        </div>
                        <div class="relative z-10 text-right">
                            <p class="text-[10px] font-bold text-violet-900/60 uppercase tracking-wider">Shortlisted</p>
                            <p class="text-2xl font-black text-slate-800 group-hover:text-violet-600 transition-colors">{{ $stats['shortlisted'] }}</p>
                        </div>
                    </div>

                    <!-- Offers -->
                    <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 group border border-indigo-100 hover:border-indigo-300 flex items-center justify-between overflow-hidden relative cursor-default">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-indigo-100/50"></div>
                        <div class="relative z-10 p-3 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                        <div class="relative z-10 text-right">
                            <p class="text-[10px] font-bold text-indigo-900/60 uppercase tracking-wider">Offers</p>
                            <p class="text-2xl font-black text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $stats['offer'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heading & Controls -->
            <div class="flex flex-col md:flex-row justify-end items-end gap-6 mb-6 flex-shrink-0">
                <!-- Header removed, moved to hero -->

                <!-- Tab Switcher -->
                <div class="bg-slate-200/50 dark:bg-gray-800 p-1.5 rounded-xl inline-flex font-bold text-sm">
                    <button id="btn-tab-list"
                        class="px-6 py-2.5 rounded-lg flex items-center gap-2 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white shadow-sm cursor-default">
                        <i data-lucide="file-text" class="w-4 h-4"></i> Applicant List
                    </button>
                    <a href="{{ route('applicants.upload') }}" id="btn-tab-upload"
                        class="px-6 py-2.5 rounded-lg transition-all duration-200 flex items-center gap-2 text-slate-500 hover:text-kingdom-navy dark:text-gray-400 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-700">
                        <i data-lucide="cloud-upload" class="w-4 h-4"></i> Upload CV
                    </a>
                </div>
            </div>

            <!-- LIST TAB CONTENT -->
            <div id="tab-list" class="flex flex-col flex-1 min-h-0 fade-in">
                <!-- Toolbar -->
                <div class="flex flex-col xl:flex-row gap-4 justify-between mb-6 flex-shrink-0">
                    <div class="flex flex-col sm:flex-row gap-4 flex-1">
                        <div class="flex-1 max-w-2xl relative">
                            <i data-lucide="search"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                            <input type="text"
                                class="w-full h-12 pl-10 pr-4 rounded-lg border border-slate-200 dark:border-gray-700 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white focus:border-kingdom-navy focus:ring-1 focus:ring-kingdom-navy outline-none"
                                placeholder="Search by name, role, or skill..." />
                        </div>
                        <button onclick="toggleFilters()"
                            class="h-12 px-6 border border-slate-200 dark:border-gray-700 rounded-lg bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white font-bold flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors">
                            <i data-lucide="filter" class="w-4 h-4"></i> Filters
                        </button>
                    </div>

                    <div class="flex gap-4">
                        <!-- View Toggle -->
                        <div class="bg-slate-200/50 dark:bg-gray-800 p-1 rounded-lg flex items-center h-12">
                            <button onclick="switchViewMode('list')" id="btn-view-list"
                                class="h-full px-3 rounded-md flex items-center gap-2 transition-all bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white shadow-sm"
                                title="List View">
                                <i data-lucide="list" class="w-4 h-4"></i>
                            </button>
                            <button onclick="switchViewMode('board')" id="btn-view-board"
                                class="h-full px-3 rounded-md flex items-center gap-2 transition-all text-slate-500 hover:text-kingdom-navy dark:text-gray-400 dark:hover:text-white"
                                title="Board View">
                                <i data-lucide="layout-grid" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <a href="{{ route('applicants.upload') }}"
                            class="h-12 px-6 bg-kingdom-navy text-white rounded-lg font-bold flex items-center gap-2 hover:bg-slate-800 transition-colors shadow-lg shadow-kingdom-navy/20">
                            <i data-lucide="plus" class="w-4 h-4"></i> Add Applicant
                        </a>
                    </div>
                </div>

                <!-- Filters Panel (Hidden by default) -->
                <div id="filters-panel"
                    class="hidden grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 p-5 mb-6 bg-white dark:bg-navy-dark border border-slate-200 dark:border-gray-700 rounded-xl shadow-sm fade-in flex-shrink-0">
                    <!-- Selects... -->
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Status</label>
                        <div class="relative">
                            <select
                                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 text-sm font-medium text-kingdom-navy dark:text-white outline-none appearance-none cursor-pointer">
                                <option>All Statuses</option>
                                <option>New</option>
                                <option>Interviewing</option>
                                <option>Shortlisted</option>
                                <option>Rejected</option>
                            </select>
                            <i data-lucide="chevron-down"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Role</label>
                        <div class="relative">
                            <select
                                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 text-sm font-medium text-kingdom-navy dark:text-white outline-none appearance-none cursor-pointer">
                                <option>All Roles</option>
                                @foreach($categories as $category)
                                    <option>{{ $category }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Date
                            Applied</label>
                        <div class="relative">
                            <select
                                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 text-sm font-medium text-kingdom-navy dark:text-white outline-none appearance-none cursor-pointer">
                                <option>Any Time</option>
                                <option>Last 24 Hours</option>
                                <option>Last 7 Days</option>
                            </select>
                            <i data-lucide="chevron-down"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button
                            class="h-11 w-full bg-slate-100 dark:bg-gray-800 hover:bg-slate-200 dark:hover:bg-gray-700 text-slate-600 dark:text-gray-300 font-bold rounded-lg text-sm transition-colors border border-slate-200 dark:border-gray-700">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Board View -->
                <div id="view-board" class="hidden flex-1 overflow-y-auto md:overflow-y-hidden md:overflow-x-auto min-h-[500px] bg-slate-50/50 dark:bg-navy-dark/50">
                    <div class="flex flex-col md:flex-row gap-4 h-auto md:h-full pb-4 items-stretch w-full md:min-w-full md:w-max px-1" id="kanban-container">
                        <!-- Columns injected by JS -->
                    </div>
                </div>

                <!-- List View -->
                <div id="view-list"
                    class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex-1 overflow-y-auto min-h-[500px]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-[#0f1f3d] text-white sticky top-0 z-10">
                                <tr class="text-xs font-semibold tracking-wide text-white uppercase bg-[#0f1f3d]">
                                    <th class="px-6 py-4 text-white">Applicant</th>
                                    <th class="px-6 py-4 text-white">Role</th>
                                    <th class="px-6 py-4 text-white">Status</th>
                                    <th class="px-6 py-4 text-white">Applied Date</th>
                                    <th class="px-6 py-4 text-right text-white">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent" id="list-tbody">
                                <!-- Rows injected by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- UPLOAD TAB CONTENT -->
            <div id="tab-upload" class="hidden max-w-4xl mx-auto w-full overflow-y-auto fade-in">
                <div class="bg-white dark:bg-navy-dark rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-kingdom-navy dark:text-white">Upload New Applicant</h2>
                        <p class="text-slate-500 dark:text-gray-400 mt-1">Add a new applicant to your talent pool manually.</p>
                    </div>

                    <form class="p-8 space-y-8" onsubmit="event.preventDefault()">
                        <!-- Drag Drop Zone -->
                        <div
                            class="border-2 border-dashed border-slate-300 dark:border-gray-600 hover:border-kingdom-red rounded-xl p-10 flex flex-col items-center justify-center bg-slate-50 dark:bg-gray-800/50 hover:bg-red-50/10 transition-all cursor-pointer group">
                            <div
                                class="w-16 h-16 bg-white dark:bg-navy-dark rounded-full shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i data-lucide="cloud-upload" class="text-kingdom-red w-8 h-8"></i>
                            </div>
                            <h3 class="text-lg font-bold text-kingdom-navy dark:text-white mb-1">Drag & Drop Resume</h3>
                            <p class="text-slate-500 dark:text-gray-400 text-sm mb-4">Supported formats: PDF, DOCX, RTF (Max 10MB)</p>
                            <button
                                class="px-6 py-2 bg-white dark:bg-navy-dark border border-slate-300 dark:border-gray-600 rounded-lg text-sm font-bold text-kingdom-navy dark:text-white hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors">Browse
                                Files</button>
                        </div>

                        <!-- Details inputs -->
                        <div class="space-y-6">
                            <h3
                                class="text-sm font-bold text-kingdom-navy dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-gray-700 pb-2">
                                Applicant Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="text-sm font-bold text-kingdom-navy dark:text-white">Full Name</label>
                                    <input type="text"
                                        class="w-full h-11 px-4 rounded-lg border border-slate-300 dark:border-gray-600 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white focus:border-kingdom-navy outline-none transition-all"
                                        placeholder="e.g. John Doe" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-sm font-bold text-kingdom-navy dark:text-white">Email Address</label>
                                    <input type="email"
                                        class="w-full h-11 px-4 rounded-lg border border-slate-300 dark:border-gray-600 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white focus:border-kingdom-navy outline-none transition-all"
                                        placeholder="e.g. john@example.com" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-sm font-bold text-kingdom-navy dark:text-white">Current Role</label>
                                    <input type="text"
                                        class="w-full h-11 px-4 rounded-lg border border-slate-300 dark:border-gray-600 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white focus:border-kingdom-navy outline-none transition-all"
                                        placeholder="e.g. Senior Developer" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-sm font-bold text-kingdom-navy dark:text-white">Phone Number</label>
                                    <input type="tel"
                                        class="w-full h-11 px-4 rounded-lg border border-slate-300 dark:border-gray-600 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white focus:border-kingdom-navy outline-none transition-all"
                                        placeholder="e.g. +44 7700 900000" />
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="text-sm font-bold text-kingdom-navy dark:text-white">LinkedIn Profile (Optional)</label>
                                    <input type="url"
                                        class="w-full h-11 px-4 rounded-lg border border-slate-300 dark:border-gray-600 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white focus:border-kingdom-navy outline-none transition-all"
                                        placeholder="https://linkedin.com/in/..." />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-gray-700">
                            <button
                                class="px-6 py-3 rounded-lg border border-slate-200 dark:border-gray-600 text-kingdom-navy dark:text-white font-bold hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors">Cancel</button>
                            <button
                                class="px-8 py-3 rounded-lg bg-kingdom-red text-white font-bold hover:bg-red-700 shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5">Save
                                Applicant</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <!-- Slide-Over Panel -->
    <div id="slide-over" class="fixed inset-0 z-[100] flex justify-end hidden">
        <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm" onclick="closeSlideOver()"></div>
        <div class="relative w-full max-w-2xl bg-white dark:bg-navy-dark h-full shadow-2xl flex flex-col slide-in-right transform transition-transform">
            <!-- Content injected by JS -->
            <div id="slide-over-content" class="h-full flex flex-col"></div>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        // Data
        const applicants = @json($applicants);

        const statuses = ['New', 'Interviewing', 'Shortlisted', 'Offer', 'Rejected'];

        function getInitials(name) {
            if (!name) return '';
            const words = name.trim().split(/\s+/);
            if (words.length >= 2) {
                return (words[0].substring(0, 1) + words[1].substring(0, 1)).toUpperCase();
            } else if (words.length >= 1) {
                return words[0].substring(0, 1).toUpperCase();
            }
            return '';
        }

        function renderAvatarHtml(imgUrl, name, sizeClass = 'w-8 h-8', extraClasses = '', imagePosition = 'object-center') {
            const hasImage = imgUrl && !imgUrl.includes('ui-avatars.com') && imgUrl !== 'null' && imgUrl !== 'undefined';
            const initials = getInitials(name);
            
            const fallbackMarkup = initials 
                ? `<div class="${sizeClass} rounded-full bg-[#0F1D33] text-white flex items-center justify-center font-semibold text-[11px] select-none ${extraClasses}">${initials}</div>`
                : `<div class="${sizeClass} rounded-full bg-[#0F1D33] text-white flex items-center justify-center font-semibold text-[11px] select-none ${extraClasses}">
                       <svg class="w-1/2 h-1/2 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                           <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0 1 12.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
                       </svg>
                   </div>`;

            if (hasImage) {
                return `
                    <div class="relative inline-block ${sizeClass}">
                        <img src="${imgUrl}" 
                             class="${sizeClass} rounded-full object-cover ${imagePosition} ${extraClasses}" 
                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');" />
                        <div class="hidden ${sizeClass} rounded-full bg-[#0F1D33] text-white flex items-center justify-center font-semibold text-[11px] select-none ${extraClasses}">
                            ${initials ? initials : `
                                <svg class="w-1/2 h-1/2 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0 1 12.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
                                </svg>
                            `}
                        </div>
                    </div>
                `;
            } else {
                return fallbackMarkup;
            }
        }

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            renderBoard();
            renderList();
            if(window.lucide) {
                lucide.createIcons();
            }
        });

        // Tabs
        function switchMainTab(tab) {
            document.getElementById('tab-list').classList.toggle('hidden', tab !== 'list');
            document.getElementById('tab-upload').classList.toggle('hidden', tab !== 'upload');

            // Update buttons
            const btnList = document.getElementById('btn-tab-list');
            const btnUpload = document.getElementById('btn-tab-upload');

            if (tab === 'list') {
                btnList.className = 'px-6 py-2.5 rounded-lg transition-all duration-200 flex items-center gap-2 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white shadow-sm';
                btnUpload.className = 'px-6 py-2.5 rounded-lg transition-all duration-200 flex items-center gap-2 text-slate-500 hover:text-kingdom-navy dark:text-gray-400 dark:hover:text-white';
            } else {
                btnList.className = 'px-6 py-2.5 rounded-lg transition-all duration-200 flex items-center gap-2 text-slate-500 hover:text-kingdom-navy dark:text-gray-400 dark:hover:text-white';
                btnUpload.className = 'px-6 py-2.5 rounded-lg transition-all duration-200 flex items-center gap-2 bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white shadow-sm';
            }
        }

        function switchViewMode(mode) {
            document.getElementById('view-list').classList.toggle('hidden', mode !== 'list');
            document.getElementById('view-board').classList.toggle('hidden', mode !== 'board');

            const btnViewList = document.getElementById('btn-view-list');
            const btnViewBoard = document.getElementById('btn-view-board');

            if (mode === 'list') {
                btnViewList.className = 'h-full px-3 rounded-md flex items-center gap-2 transition-all bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white shadow-sm';
                btnViewBoard.className = 'h-full px-3 rounded-md flex items-center gap-2 transition-all text-slate-500 hover:text-kingdom-navy dark:text-gray-400 dark:hover:text-white';
            } else {
                btnViewList.className = 'h-full px-3 rounded-md flex items-center gap-2 transition-all text-slate-500 hover:text-kingdom-navy dark:text-gray-400 dark:hover:text-white';
                btnViewBoard.className = 'h-full px-3 rounded-md flex items-center gap-2 transition-all bg-white dark:bg-navy-dark text-kingdom-navy dark:text-white shadow-sm';
            }
        }

        function toggleFilters() {
            const panel = document.getElementById('filters-panel');
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                panel.classList.add('grid');
            } else {
                panel.classList.remove('grid');
            }
        }

        // Render Functions
        function getStatusColor(status) {
            switch (status) {
                case 'New': return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800';
                case 'Interviewing': return 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800';
                case 'Shortlisted': return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800';
                case 'Offer': return 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800';
                case 'Rejected': return 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700';
                default: return 'bg-slate-100 dark:bg-gray-800';
            }
        }

        function getColumnBorderColor(status) {
            switch (status) {
                case 'New': return 'border-t-blue-500';
                case 'Interviewing': return 'border-t-yellow-500';
                case 'Shortlisted': return 'border-t-purple-500';
                case 'Offer': return 'border-t-green-500';
                case 'Rejected': return 'border-t-slate-500';
                default: return 'border-t-slate-200 dark:border-t-gray-700';
            }
        }

        function renderBoard() {
            const container = document.getElementById('kanban-container');
            container.innerHTML = statuses.map(status => {
                const columnApplicants = applicants.filter(c => c.status === status);
                return `
                <div class="flex-none md:flex-1 w-full md:w-auto md:] h-auto md:h-full bg-slate-100/50 dark:bg-gray-800/50 rounded-xl flex flex-col border border-slate-200 dark:border-gray-700 border-t-4 ${getColumnBorderColor(status)}"
                     ondragover="allowDrop(event)" ondrop="drop(event, '${status}')">
                    <div class="p-3 border-b border-slate-100 dark:border-gray-700 flex justify-between items-center bg-white/50 dark:bg-navy-dark/50 backdrop-blur-sm rounded-t-lg">
                        <h3 class="font-bold text-kingdom-navy dark:text-white flex items-center gap-2 text-sm">
                            ${status}
                            <span class="bg-slate-200 dark:bg-gray-700 text-slate-600 dark:text-gray-300 text-[10px] font-extrabold py-0.5 px-1.5 rounded-full">${columnApplicants.length}</span>
                        </h3>
                        <i data-lucide="more-horizontal" class="w-4 h-4 text-slate-400 cursor-pointer hover:text-kingdom-navy dark:hover:text-white"></i>
                    </div>
                    <div class="p-2 flex-none md:flex-1 overflow-y-visible md:overflow-y-auto space-y-2 custom-scrollbar">
                        ${columnApplicants.map(c => `
                            <div draggable="true" ondragstart="drag(event, ${c.id})" onclick="openSlideOver(${c.id})"
                                 class="bg-white dark:bg-navy-dark p-3 rounded-lg shadow-sm border border-slate-200 dark:border-gray-700 cursor-move hover:shadow-md hover:border-kingdom-navy/30 transition-all group active:cursor-grabbing">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="grip-vertical" class="w-3 h-3 text-slate-300 opacity-0 group-hover:opacity-100"></i>
                                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 dark:bg-gray-800 px-1.5 rounded border border-slate-100 dark:border-gray-700">${c.id}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mb-2">
                                    ${renderAvatarHtml(c.img, c.name, 'w-8 h-8', 'border border-slate-100 dark:border-gray-700 flex-shrink-0', c.image_position)}
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-kingdom-navy dark:text-white text-sm leading-tight truncate group-hover:text-kingdom-red transition-colors">${c.name}</h4>
                                        <p class="text-xs text-slate-500 dark:text-gray-400 truncate max-w-[120px]">${c.role}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1 mb-2">
                                    ${c.skills.slice(0, 2).map(skill => `<span class="text-[10px] px-1.5 py-0.5 bg-slate-100 dark:bg-gray-800 text-slate-500 dark:text-gray-400 rounded border border-slate-200 dark:border-gray-700">${skill}</span>`).join('')}
                                    ${c.skills.length > 2 ? `<span class="text-[10px] px-1.5 py-0.5 bg-slate-50 dark:bg-gray-800 text-slate-400 rounded">+${c.skills.length - 2}</span>` : ''}
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-slate-50 dark:border-gray-800 text-[10px] text-slate-400">
                                    <span>${c.date}</span>
                                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="p-1 hover:bg-slate-100 dark:hover:bg-gray-800 rounded text-slate-400 hover:text-kingdom-navy dark:hover:text-white" onclick="event.stopPropagation(); openSlideOver(${c.id})"><i data-lucide="eye" class="w-3 h-3"></i></button>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>`;
            }).join('');
            if(window.lucide) lucide.createIcons();
        }

        function renderList() {
            const tbody = document.getElementById('list-tbody');
            tbody.innerHTML = applicants.map(c => `
                <tr class="transition-colors duration-150 group cursor-pointer border-b border-slate-100 hover:bg-slate-50 bg-white" onclick="openSlideOver(${c.id})">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            ${renderAvatarHtml(c.img, c.name, 'w-10 h-10', 'flex-shrink-0', c.image_position)}
                            <div>
                                <div class="font-bold text-kingdom-navy dark:text-white group-hover:text-kingdom-red transition-colors">${c.name}</div>
                                <div class="text-xs text-slate-500 dark:text-gray-400">${c.email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-slate-700 dark:text-gray-300">${c.role}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border ${getStatusColor(c.status)}">
                            ${c.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-gray-400">${c.date}</td>
                    <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openSlideOver(${c.id})" class="p-2 text-slate-400 hover:text-kingdom-navy dark:hover:text-white hover:bg-slate-200 dark:hover:bg-gray-700 rounded-lg transition-colors"><i data-lucide="eye" class="w-4 h-4"></i></button>
                            <button onclick="deleteApplicant(${c.id})" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Delete Applicant"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
            if(window.lucide) lucide.createIcons();
        }

        // Drag & Drop
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev, id) {
            ev.dataTransfer.setData("text", id);
        }

        function drop(ev, status) {
            ev.preventDefault();
            const id = parseInt(ev.dataTransfer.getData("text"));
            const applicant = applicants.find(c => c.id === id);
            if (applicant) {
                applicant.status = status;
                renderBoard();
                renderList();
            }
        }

        // Slide Over
        function openSlideOver(id) {
            const c = applicants.find(x => x.id === id);
            if (!c) return;

            const content = document.getElementById('slide-over-content');
            content.innerHTML = `
                <div class="flex items-start justify-between p-6 border-b border-slate-100 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-4">
                        ${renderAvatarHtml(c.img, c.name, 'w-16 h-16', 'border-2 border-white dark:border-gray-700 shadow-md text-lg flex-shrink-0', c.image_position)}
                        <div>
                            <h2 class="text-2xl font-bold text-kingdom-navy dark:text-white">${c.name}</h2>
                            <p class="text-slate-500 dark:text-gray-400 font-medium">${c.role}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border ${getStatusColor(c.status)}">${c.status}</span>
                                <span class="text-xs text-slate-400 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> Applied ${c.date}</span>
                            </div>
                        </div>
                    </div>
                    <button onclick="closeSlideOver()" class="p-2 text-slate-400 hover:text-kingdom-navy dark:hover:text-white hover:bg-slate-200 dark:hover:bg-gray-700 rounded-lg transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-8 custom-scrollbar">
                    <div class="flex gap-3 pb-6 border-b border-slate-100 dark:border-gray-700">
                        <button class="flex-1 h-10 bg-kingdom-navy text-white rounded-lg font-bold text-sm hover:bg-slate-800 transition-colors flex items-center justify-center gap-2"><i data-lucide="calendar" class="w-4 h-4"></i> Schedule Interview</button>
                        <button class="flex-1 h-10 bg-white dark:bg-navy-dark border border-slate-200 dark:border-gray-700 text-kingdom-navy dark:text-white rounded-lg font-bold text-sm hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors flex items-center justify-center gap-2"><i data-lucide="mail" class="w-4 h-4"></i> Email</button>
                        <button onclick="deleteApplicant(${c.id})" class="h-10 w-10 border border-slate-200 dark:border-gray-700 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-100 dark:hover:border-red-900 transition-colors" title="Delete Applicant"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        <button class="h-10 w-10 border border-slate-200 dark:border-gray-700 rounded-lg flex items-center justify-center text-slate-400 hover:text-kingdom-red hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-100 dark:hover:border-red-900 transition-colors"><i data-lucide="x-circle" class="w-4 h-4"></i></button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-slate-50 dark:bg-gray-800 rounded-lg border border-slate-100 dark:border-gray-700">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email</div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-kingdom-navy dark:text-white"><i data-lucide="mail" class="w-3 h-3 text-kingdom-red"></i> ${c.email}</div>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-gray-800 rounded-lg border border-slate-100 dark:border-gray-700">
                             <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Phone</div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-kingdom-navy dark:text-white"><i data-lucide="phone" class="w-3 h-3 text-kingdom-red"></i> ${c.phone}</div>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-gray-800 rounded-lg border border-slate-100 dark:border-gray-700">
                             <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Location</div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-kingdom-navy dark:text-white"><i data-lucide="map-pin" class="w-3 h-3 text-kingdom-red"></i> ${c.location}</div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-kingdom-navy dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2"><i data-lucide="file-text" class="w-4 h-4 text-kingdom-red"></i> About</h3>
                        <p class="text-slate-600 dark:text-gray-400 text-sm leading-relaxed">${c.about}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-kingdom-navy dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-kingdom-red"></i> Skills</h3>
                        <div class="flex flex-wrap gap-2">
                            ${c.skills.map(s => `<span class="px-3 py-1 bg-white dark:bg-navy-dark border border-slate-200 dark:border-gray-700 rounded-full text-sm font-medium text-slate-700 dark:text-gray-300 shadow-sm">${s}</span>`).join('')}
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 flex justify-between items-center">
                    <button class="text-sm font-bold text-slate-500 dark:text-gray-400 hover:text-kingdom-navy dark:hover:text-white">View Full Profile</button>
                    <button class="px-6 py-2 bg-kingdom-red text-white font-bold rounded-lg hover:bg-red-700 shadow-lg shadow-red-500/20 transition-all">Move to Offer</button>
                </div>
            `;

            document.getElementById('slide-over').classList.remove('hidden');
            if(window.lucide) lucide.createIcons();
        }

        function deleteApplicant(id) {
            Swal.fire({
                title: 'Delete Applicant',
                text: 'Are you sure you want to delete this applicant? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0F1D33',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/applicant/${id}`;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    
                    form.appendChild(csrf);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function closeSlideOver() {
            document.getElementById('slide-over').classList.add('hidden');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSlideOver();
        });

    </script>
@endsection
