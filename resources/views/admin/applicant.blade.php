@extends('layouts.admin')

@section('title', 'Applicants | Kingdom Admin')

@section('content')
<div x-data="applicantTable()" @keydown.escape.window="detailsModalOpen = false; editingId = null; categoryReviewModalOpen = false" class="h-auto lg:h-full flex flex-col space-y-3">

    <div class="dashboard-header flex flex-col sm:flex-row justify-between items-start sm:items-center mb-1 gap-2 relative z-60">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Applicants</h1>
                <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2 py-0.5 rounded-full ml-1">{{ $applicants->total() ?? $applicants->count() }} Total</span>
                @if(($uncategorizedCount ?? 0) > 0)
                    <a href="{{ request('filter') === 'uncategorized' ? route('admin.applicant') : route('admin.applicant', ['filter' => 'uncategorized']) }}" 
                       class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold transition-all shadow-sm {{ request('filter') === 'uncategorized' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100' }}"
                       title="Filter applicants with Other / Not Listed role">
                        <span class="w-2 h-2 rounded-full {{ request('filter') === 'uncategorized' ? 'bg-white' : 'bg-amber-500 animate-pulse' }}"></span>
                        <span>Uncategorised Requests — {{ $uncategorizedCount }}</span>
                        @if(request('filter') === 'uncategorized')
                            <i data-lucide="x" class="w-3 h-3 ml-0.5"></i>
                        @endif
                    </a>
                @endif
            </div>
            <p class="text-xs text-slate-400">Manage your talent pool and review applicant categories</p>
        </div>
        
        <div class="flex gap-3 w-full sm:w-auto">
            {{-- Search lives in the filter bar above the table — kept once, not duplicated. --}}
             <!-- View Toggle -->
             <div class="flex items-center gap-1.5">
                <button @click="view = 'list'" 
                        class="p-2 rounded-lg border border-slate-200 transition-all shadow-sm"
                        :class="view === 'list' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-400 hover:bg-indigo-50 hover:text-slate-600'"
                        title="List View">
                    <i data-lucide="list" class="w-4 h-4"></i>
                </button>
                <button @click="view = 'grid'" 
                        class="p-2 rounded-lg border border-slate-200 transition-all shadow-sm"
                        :class="view === 'grid' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-400 hover:bg-indigo-50 hover:text-slate-600'"
                        title="Grid View">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                </button>
             </div>
            
            <button @click="if(selectMode) { selectedRows = []; selectAll = false; } selectMode = !selectMode" 
                    class="flex items-center gap-2 border rounded-xl px-4 py-2 transition-all shadow-sm active:scale-95 text-sm font-medium"
                    :class="selectMode ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700' : 'border-slate-300 text-slate-600 hover:bg-slate-50'">
                <i data-lucide="check-square" class="w-4 h-4"></i>
                <span class="hidden sm:inline" x-text="selectMode ? 'Done' : 'Select'"></span>
            </button>

            <div class="relative" x-data="{ openExport: false }">
                <button @click="openExport = true" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl px-4 py-2 text-sm font-medium transition-all shadow-sm active:scale-95">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Export</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="openExport ? 'rotate-180' : ''"></i>
                </button>
                
                <!-- Export Modal -->
                <div x-show="openExport" 
                     x-cloak
                     style="display: none;"
                     class="fixed inset-0 z-[100] overflow-y-auto" 
                     aria-labelledby="modal-title" 
                     role="dialog" 
                     aria-modal="true">
                    
                    <div x-show="openExport"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                         @click="openExport = false"></div>

                    <div class="flex min-h-full items-center justify-center p-4 text-center" @click="openExport = false">
                        <div x-show="openExport"
                             @click.stop
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="relative w-full max-w-md transform rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-100">
                            
                            <div class="px-6 py-4 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                                <h3 class="font-bold text-lg text-kingdom-navy">Export Options</h3>
                                <button @click="openExport = false" class="bg-white p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>

                            <div class="p-6 space-y-4">
                                <!-- Export Type Selection -->
                                <div class="space-y-3">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Export Mode</label>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <!-- All -->
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="exportType" value="all" x-model="exportType" class="peer sr-only">
                                            <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50">
                                                <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">All Records</span>
                                            </div>
                                        </label>

                                        <!-- Selected -->
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="exportType" value="selected" x-model="exportType" class="peer sr-only" :disabled="selectedRows.length === 0">
                                            <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50"
                                                 :class="selectedRows.length === 0 ? 'opacity-50 cursor-not-allowed bg-slate-50' : ''">
                                                <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">Selected</span>
                                                <span class="text-[10px] text-slate-400" x-text="'(' + selectedRows.length + ')'"></span>
                                            </div>
                                        </label>

                                        <!-- Last N -->
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="exportType" value="last" x-model="exportType" class="peer sr-only">
                                            <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50">
                                                <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">Last (Newest)</span>
                                            </div>
                                        </label>

                                        <!-- First N -->
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="exportType" value="first" x-model="exportType" class="peer sr-only">
                                            <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50">
                                                <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">First (Oldest)</span>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <!-- Date Range Option (New) -->
                                    <div class="mt-3">
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="exportType" value="date_range" x-model="exportType" class="peer sr-only">
                                            <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50 flex items-center justify-center gap-2">
                                                <i data-lucide="calendar-range" class="w-4 h-4 text-slate-400 peer-checked:text-kingdom-gold"></i>
                                                <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">Date Range</span>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Date Range Inputs -->
                                    <div x-show="exportType === 'date_range'" x-transition class="grid grid-cols-2 gap-3 mt-3">
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Start Date</label>
                                            <input type="date" x-model="startDate" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none text-xs">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">End Date</label>
                                            <input type="date" x-model="endDate" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none text-xs">
                                        </div>
                                    </div>
                                </div>

                                <!-- Limit Input (Show only for Last/First) -->
                                <div x-show="exportType === 'last' || exportType === 'first'" x-transition class="space-y-2">
                                     <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Quantity</label>
                                     <input type="number" x-model="exportLimit" min="1" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none">
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3 pt-2">
                                    <button @click="triggerExport('csv')" class="flex-1 py-3 bg-green-50 text-green-600 hover:bg-green-100 rounded-xl font-bold transition-colors flex items-center justify-center gap-2 border border-green-100">
                                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> CSV
                                    </button>
                                    <button @click="triggerExport('pdf')" class="flex-1 py-3 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-bold transition-colors flex items-center justify-center gap-2 border border-red-100">
                                        <i data-lucide="file-text" class="w-4 h-4"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        if (!function_exists('getStatusColor')) {
            function getStatusColor($status) {
                return match($status) {
                    'Hired' => 'bg-green-100 text-green-700',
                    'Rejected' => 'bg-red-50 text-red-600',
                    default => 'bg-kingdom-gold/20 text-[#B89955]',
                };
            }
        }
    @endphp

    <!-- List View Table Card -->
    <div class="anim-fade-in-up bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden w-full self-start" x-show="view === 'list'">
        <!-- Filter Bar placed above table -->
        <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 bg-slate-50/50 shrink-0">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1 w-full">
                <!-- Search Input -->
                <div class="relative w-full sm:max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" x-model="filters.search" @keyup.enter="applyFilters()" placeholder="Search applicants..." class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                </div>

                <!-- Status Filter -->
                <select x-model="filters.status" @change="applyFilters()" class="w-full sm:w-auto px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Hired">Hired</option>
                    <option value="Rejected">Rejected</option>
                </select>

                <!-- Role Filter -->
                <div class="relative w-full sm:max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="briefcase" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" x-model="filters.role" @keyup.enter="applyFilters()" placeholder="Filter by role..." class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                </div>

                <!-- Clear Filters Button -->
                <button @click="clearFilters()" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-kingdom-navy transition-colors flex items-center justify-center gap-1 w-full sm:w-auto" x-show="filters.search || filters.status || filters.role">
                    <i data-lucide="x-circle" class="w-4 h-4"></i> Clear
                </button>
            </div>
        </div>

        @if(count($applicants) > 0)
        <div class="overflow-auto w-full max-h-[calc(100vh-210px)] admin-table-container">
            <table class="applicants-table w-full min-w-[800px] text-left border-collapse admin-table">
                <thead class="sticky top-0 z-20 bg-[#0f1f3d] shadow-sm">
                    <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-[11px] border-b border-slate-700">
                        <th :class="selectMode ? '' : 'hidden'" class="py-2.5 px-3 text-center w-10 bg-[#0f1f3d]">
                              <input type="checkbox" @change="toggleSelectAll()" x-model="selectAll" class="rounded border-slate-300 text-kingdom-gold focus:ring-kingdom-gold w-3.5 h-3.5 cursor-pointer">
                        </th>
                        <th class="py-2.5 px-3 bg-[#0f1f3d]">Applicant</th>
                        <th class="py-2.5 px-3 w-[26%] bg-[#0f1f3d]">Contact</th>
                        <th class="py-2.5 px-3 w-[15%] bg-[#0f1f3d]">Role</th>
                        <th class="py-2.5 px-3 text-center w-24 bg-[#0f1f3d]">Applications</th>
                        <th class="py-2.5 px-3 text-center w-24 bg-[#0f1f3d]">Status</th>
                        <th class="py-2.5 px-3 text-right w-32 bg-[#0f1f3d]">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-transparent">
                    @foreach($applicants as $c)
                    @php
                        $roleLower = strtolower($c->role);
                        $roleLabel = ucfirst($roleLower);
                        if ($roleLower === 'applicant' || $roleLower === 'new' || $roleLower === 'unassigned' || empty($roleLower)) {
                            $roleLabel = 'New / Unassigned';
                            $badgeClasses = 'bg-slate-100 text-slate-500 border border-slate-200';
                        } elseif ($c->role === 'Other / Not Listed' || $c->role === 'Other') {
                            $roleLabel = $c->other_category_description ? 'Other: ' . $c->other_category_description : 'Other / Not Listed';
                            $badgeClasses = 'bg-amber-100 text-amber-800 border border-amber-200';
                        } elseif (str_contains($roleLower, 'chef')) {
                            $roleLabel = 'Head Chef';
                            $badgeClasses = 'bg-orange-100 text-orange-700 border border-orange-200';
                        } elseif (str_contains($roleLower, 'security')) {
                            $roleLabel = 'Security Specialist';
                            $badgeClasses = 'bg-blue-100 text-blue-700 border border-blue-200';
                        } else {
                            $roleLabel = ucfirst($c->role);
                            $badgeClasses = 'bg-indigo-100 text-indigo-700 border border-indigo-200';
                        }
                    @endphp
                    <tr class="applicant-row group cursor-pointer transition-colors duration-150 border-b border-slate-100 hover:bg-slate-50"
                        {{-- Filtering is server-side via applyFilters() (full page navigation) — this row is always a real, already-filtered result. --}}
                        :class="selectedRows.includes({{ $c->id }}) ? 'bg-indigo-50/50' : ''"
                        @click="selectMode ? toggleRowSelection({{ $c->id }}) : null">
                        
                        <!-- Checkbox -->
                        <td :class="selectMode ? '' : 'hidden'" class="py-4 px-4 text-center" @click.stop>
                            <input type="checkbox" :value="{{ $c->id }}" x-model="selectedRows" class="rounded border-slate-300 text-indigo-650 focus:ring-indigo-550 w-3.5 h-3.5 cursor-pointer">
                        </td>
 
                        <!-- Applicant -->
                        <td class="py-4 px-4 min-w-0">
                            <div class="flex items-center gap-3">
                                <div class="relative shrink-0 w-8 h-8">
                                    <x-avatar :user="$c" class="w-8 h-8 rounded-full ring-2 ring-slate-200 ring-offset-1 shadow-sm" />
                                </div>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 text-sm max-w-[180px] block truncate" title="{{ $c->name }}">{{ $c->name }}</span>
                                    @if($c->location)
                                        <span class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5 truncate max-w-[180px]" title="{{ $c->location }}">
                                            <i data-lucide="map-pin" class="w-2.5 h-2.5 shrink-0"></i>{{ $c->location }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Contact (email + phone stacked, one column) -->
                        <td class="py-4 px-4 text-sm min-w-0">
                            @if($c->email)
                                <a href="mailto:{{ $c->email }}" class="text-indigo-600 hover:text-indigo-800 hover:underline max-w-[220px] block truncate" title="{{ $c->email }}">
                                    {{ $c->email }}
                                </a>
                            @else
                                <span class="text-slate-300 italic">No email</span>
                            @endif
                            <span class="text-[11px] text-slate-400 font-medium block truncate mt-0.5">{{ $c->phone ?: 'No phone on file' }}</span>
                        </td>

                        <!-- Role -->
                        <td class="py-4 px-4 text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border capitalize {{ $badgeClasses }}" title="{{ $roleLabel }}">
                                <span class="truncate max-w-[120px]">{{ $roleLabel }}</span>
                            </span>
                        </td>
                        
                        <!-- Applications Count -->
                        <td class="py-4 px-4 text-center">
                            @php
                                $appCount = $c->job_applications_count ?? $c->jobApplications()->count();
                            @endphp
                            @if($appCount > 0)
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-semibold">
                                    {{ $appCount }}
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-400 text-xs font-semibold">0</span>
                            @endif
                        </td>
                        
                        <!-- Status Toggle -->
                        <td class="py-4 px-4 text-center" @click.stop>
                            @if($c->user)
                                <button @click="toggleUserStatus({{ $c->user->id }})"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                                        :class="isActiveUser[{{ $c->user->id }}] ? 'bg-emerald-500' : 'bg-slate-350'"
                                        role="switch" :aria-checked="isActiveUser[{{ $c->user->id }}]">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition-transform duration-205 ease-in-out"
                                          :class="isActiveUser[{{ $c->user->id }}] ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                            @else
                                <span class="text-xs text-slate-450 italic">No account</span>
                            @endif
                        </td>
 
                        <!-- Actions -->
                        <td class="py-4 px-4 text-right" @click.stop>
                            <div class="flex items-center justify-end gap-1.5">
                                @if($c->role === 'Other / Not Listed' || $c->role === 'Other')
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition-colors shadow-sm" title="Review & Map Category" @click="openCategoryReviewModal({{ json_encode($c) }})">
                                        <i data-lucide="tag" class="w-4 h-4"></i>
                                    </button>
                                @endif
                                <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="View applicant" @click="openDetailsModal({{ json_encode($c) }})">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-650 transition-colors" title="Edit" @click="openEditApplicantModal({{ json_encode($c) }})">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.applicant.destroy', $c->id) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { title: 'Delete Applicant', message: 'Are you sure you want to delete this applicant?', onConfirm: () => submitDeleteForm('{{ route('admin.applicant.destroy', $c->id) }}') })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- Empty State — a real full-width block, not a colspan cell inside the wide scrolling table -->
        <div class="py-12 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 ring-4 ring-white shadow-sm">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-300"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">No applicants found</h3>
                <p class="text-slate-500 max-w-sm mx-auto mt-2 text-sm">Waiting for new applications to arrive.</p>
            </div>
        </div>
        @endif
        <!-- Pagination Links -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
            {{ $applicants->links() }}
        </div>
    </div>

    <!-- Grid View -->
    <div class="anim-fade-in-up grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 flex-1 lg:overflow-auto lg:section-scroll p-1 relative z-10 min-h-0" x-show="view === 'grid'" style="display: none;">
        @foreach($applicants as $c)
        @php
            $gridRoleLabel = ucfirst($c->role);
            if ($c->role === 'Other / Not Listed' || $c->role === 'Other') {
                $gridRoleLabel = $c->other_category_description ? 'Other: ' . $c->other_category_description : 'Other / Not Listed';
            } elseif ($c->role === 'applicant') {
                $gridRoleLabel = 'Applicant';
            }
        @endphp
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-card hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group relative flex flex-col gap-4 overflow-hidden">
             {{-- Filtering is server-side via applyFilters() (full page navigation) — this card is always a real, already-filtered result. --}}

            <!-- Decorative Glow -->
             <div class="absolute -right-6 -top-6 w-24 h-24 bg-kingdom-gold/5 rounded-full blur-2xl group-hover:bg-kingdom-gold/10 transition-colors"></div>

            <div class="flex items-start justify-between relative z-10">
                <div class="flex items-center gap-3">
                      <x-avatar :user="$c" class="w-14 h-14 rounded-full ring-4 ring-white shadow-md transition-transform group-hover:scale-105" />
                    <div>
                        <h3 class="font-bold text-kingdom-navy text-lg leading-tight mb-0.5 group-hover:text-kingdom-gold transition-colors">{{ $c->name }}</h3>
                    </div>
                </div>
            </div>

            <div class="space-y-2 relative z-10">
                <div class="flex items-center gap-2 text-slate-700 bg-slate-50/80 p-2.5 rounded-xl border border-slate-100 group-hover:border-slate-200 transition-colors">
                    <div class="p-1 rounded bg-white shadow-sm">
                        <i data-lucide="briefcase" class="w-3.5 h-3.5 {{ ($c->role === 'Other / Not Listed' || $c->role === 'Other') ? 'text-amber-600' : 'text-kingdom-gold' }}"></i>
                    </div>
                    <span class="text-xs font-bold truncate {{ ($c->role === 'Other / Not Listed' || $c->role === 'Other') ? 'text-amber-800' : '' }}">{{ $gridRoleLabel }}</span>
                </div>
                 <div class="flex items-center gap-2 text-slate-600 bg-slate-50/50 p-2 rounded-lg">
                    <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                    <span class="text-xs font-medium truncate">{{ $c->email ?? 'N/A' }}</span>
                </div>
                 <div class="flex items-center gap-2 text-slate-600 bg-slate-50/50 p-2 rounded-lg">
                    <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                    <span class="text-xs font-medium truncate">{{ $c->phone ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="pt-4 mt-auto border-t border-slate-100 flex items-center justify-between gap-3 relative z-10">
                 <span class="text-[10px] font-bold text-indigo-600 flex items-center gap-1.5 bg-indigo-50 border border-indigo-100 px-2 py-1 rounded-lg">
                    <i data-lucide="layers" class="w-3 h-3"></i>
                    {{ $c->job_applications_count ?? $c->jobApplications()->count() }} Applications
                </span>
                
                <div class="flex items-center gap-2">
                    @if($c->role === 'Other / Not Listed' || $c->role === 'Other')
                        <button class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl transition-all hover:scale-110 border border-amber-200" 
                                title="Review & Map Category" 
                                @click="openCategoryReviewModal({{ json_encode($c) }})">
                            <i data-lucide="tag" class="w-4 h-4"></i>
                        </button>
                    @endif
                    <button class="p-2 hover:bg-slate-100 text-slate-400 hover:text-kingdom-navy rounded-xl transition-all hover:scale-110" 
                            title="View Details" 
                            @click="openDetailsModal({{ json_encode($c) }})">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    <button class="p-2 hover:bg-slate-100 text-slate-400 hover:text-kingdom-navy rounded-xl transition-all hover:scale-110" 
                            title="Edit Status" 
                            @click="openEditApplicantModal({{ json_encode($c) }})">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- View Details Modal -->
    <div x-show="detailsModalOpen" class="fixed inset-0 z-[100] overflow-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
         <!-- Backdrop -->
         <div x-show="detailsModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="detailsModalOpen = false"></div>

         <style>
             .custom-modal-scrollbar::-webkit-scrollbar {
                 width: 6px;
                 height: 6px;
             }
             .custom-modal-scrollbar::-webkit-scrollbar-track {
                 background: transparent;
             }
             .custom-modal-scrollbar::-webkit-scrollbar-thumb {
                 background-color: rgba(184, 153, 85, 0.4);
                 border-radius: 9999px;
             }
             .custom-modal-scrollbar::-webkit-scrollbar-thumb:hover {
                 background-color: rgba(184, 153, 85, 0.7);
             }
         </style>

         <div class="flex min-h-full items-center justify-center p-4" @click="detailsModalOpen = false">
             <div x-show="detailsModalOpen"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden transition-all flex flex-col max-h-[85vh] border border-slate-100">
                
                  <!-- Premium Fixed Header -->
                  <div class="relative bg-[#0F1D33] px-8 py-6 text-white shrink-0 border-b-2 border-kingdom-gold/30">
                        <!-- Decorative background glow -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-kingdom-gold/10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none"></div>
                        
                        <!-- Floating Close Button -->
                        <button @click="detailsModalOpen = false" class="absolute top-4 right-4 p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-md z-20 hover:rotate-90 duration-300">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                        
                        <!-- Header Content (Avatar, Name, Role, Status) -->
                        <div class="flex flex-col sm:flex-row items-center gap-5 mt-2 relative z-10 text-center sm:text-left">
                            <div class="w-20 h-20 rounded-full bg-white p-0.5 shadow-lg ring-4 ring-kingdom-gold/30 shrink-0 relative">
                                {{-- Initials fallback --}}
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-slate-700 to-slate-800 text-white flex items-center justify-center text-xl font-bold">
                                    <span x-text="selectedApplicant?.name ? selectedApplicant.name.substring(0,2).toUpperCase() : ''"></span>
                                </div>
                                {{-- Image overlay --}}
                                <img x-show="selectedApplicant?.image"
                                     :src="selectedApplicant?.profile_photo_url || ''" 
                                     class="absolute inset-[2px] w-[calc(100%-4px)] h-[calc(100%-4px)] object-cover rounded-full"
                                     onerror="this.style.display='none'"
                                     x-cloak>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-center sm:justify-start gap-2.5">
                                    <h3 class="text-xl md:text-2xl font-black tracking-wide text-white truncate" x-text="selectedApplicant?.name"></h3>
                                    <span :class="getStatusBgColor(selectedApplicant?.status)" 
                                          class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase border border-current shadow-sm w-fit mx-auto sm:mx-0" 
                                          x-text="selectedApplicant?.status || 'Pending'"></span>
                                </div>
                                <p class="text-[#B89955] dark:text-[#B89955] font-bold text-xs uppercase tracking-widest flex items-center justify-center sm:justify-start gap-1.5 mt-2">
                                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                                    <span x-text="((selectedApplicant?.role === 'Other / Not Listed' || selectedApplicant?.role === 'Other') && selectedApplicant?.other_category_description) ? ('Other: ' + selectedApplicant.other_category_description) : (selectedApplicant?.role === 'applicant' ? 'Applicant' : selectedApplicant?.role)"></span>
                                </p>
                            </div>
                        </div>
                  </div>

                  <!-- Scrollable Body Content -->
                  <div class="px-8 py-6 overflow-y-auto custom-modal-scrollbar flex-1 space-y-6 min-h-0 bg-slate-50/30">
                       <!-- Category Review Section (When Other / Not Listed) -->
                       <div x-show="selectedApplicant?.role === 'Other / Not Listed' || selectedApplicant?.role === 'Other'"
                            class="p-5 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/40 border border-amber-200/90 shadow-sm space-y-4">
                           <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-amber-200/70">
                               <div class="flex items-center gap-3">
                                   <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shadow-inner shrink-0">
                                       <i data-lucide="tag" class="w-5 h-5 text-amber-700"></i>
                                   </div>
                                   <div>
                                       <h4 class="text-sm font-black text-amber-950 uppercase tracking-wide">Category Review Required</h4>
                                       <p class="text-xs text-amber-800">Applicant requested a role not currently mapped to an official category</p>
                                   </div>
                               </div>
                               <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-200 text-amber-900 w-fit">
                                   <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                   Uncategorised
                               </span>
                           </div>

                           <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-white/90 p-4 rounded-xl border border-amber-200/70">
                               <div>
                                   <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">Applicant Requested Role</span>
                                   <span class="text-sm font-black text-slate-900 flex items-center gap-2" x-text="selectedApplicant?.other_category_description || 'None specified'"></span>
                               </div>
                               <div>
                                   <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">Current System Category</span>
                                   <span class="text-sm font-bold text-amber-700">Other / Not Listed</span>
                               </div>
                           </div>

                           <div class="flex flex-col sm:flex-row items-center gap-2.5 pt-1">
                               <button type="button" 
                                       @click="openCategoryReviewModal(selectedApplicant); detailsModalOpen = false;"
                                       class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-kingdom-navy hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                                   <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-kingdom-gold"></i>
                                   <span>Assign to Existing Category</span>
                               </button>
                               <a :href="`{{ route('admin.job-category') }}?new_name=${encodeURIComponent(selectedApplicant?.other_category_description || '')}`"
                                  target="_blank"
                                  class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2 text-center">
                                   <i data-lucide="folder-plus" class="w-3.5 h-3.5 text-slate-500"></i>
                                   <span>Create Category from Request</span>
                               </a>
                           </div>
                       </div>

                       <!-- Details Grid (Contact Info & Talent Metrics) -->
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Contact Info Card -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-kingdom-gold pl-2">
                                    <i data-lucide="contact" class="w-3.5 h-3.5 text-kingdom-gold"></i> Contact Info
                                </h4>
                                <div class="bg-white rounded-2xl p-4 border border-slate-100 space-y-3 shadow-sm">
                                    <div class="flex items-center gap-3 bg-blue-50/30 border border-blue-100/50 p-3 rounded-xl hover:bg-blue-50/60 transition-colors group">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm group-hover:bg-blue-100 transition-colors">
                                            <i data-lucide="mail" class="w-4 h-4"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[9px] text-blue-500 font-bold uppercase tracking-wider">Email</p>
                                            <p class="text-xs font-bold text-slate-800 break-all" x-text="selectedApplicant?.email"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 bg-emerald-50/30 border border-emerald-100/50 p-3 rounded-xl hover:bg-emerald-50/60 transition-colors group">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm group-hover:bg-emerald-100 transition-colors">
                                            <i data-lucide="phone" class="w-4 h-4"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[9px] text-emerald-500 font-bold uppercase tracking-wider">Phone</p>
                                            <p class="text-xs font-bold text-slate-800" x-text="selectedApplicant?.phone || 'N/A'"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 bg-purple-50/30 border border-purple-100/50 p-3 rounded-xl hover:bg-purple-50/60 transition-colors group">
                                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-sm group-hover:bg-purple-100 transition-colors">
                                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[9px] text-purple-500 font-bold uppercase tracking-wider">Location</p>
                                            <p class="text-xs font-bold text-slate-800" x-text="selectedApplicant?.location || 'N/A'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Talent Metrics & Actions -->
                            <div class="space-y-3 flex flex-col">
                                <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-kingdom-gold pl-2">
                                    <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-kingdom-gold"></i> Talent Metrics
                                </h4>
                                <div class="bg-white rounded-2xl p-4 border border-slate-100 flex-1 flex flex-col justify-between gap-4 shadow-sm">
                                     <div class="flex items-center gap-3 bg-indigo-50/30 border border-indigo-100/50 p-3 rounded-xl hover:bg-indigo-50/60 transition-colors group">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm group-hover:bg-indigo-100 transition-colors">
                                            <i data-lucide="layers" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-indigo-500 font-bold uppercase tracking-wider">Total Applications</p>
                                            <p class="text-xs font-bold text-slate-800" x-text="(selectedApplicant?.job_applications_count || 0) + ' Jobs'"></p>
                                        </div>
                                    </div>
                                    
                                     <div class="flex flex-col gap-2">
                                         <!-- View Resume (New Tab) -->
                                         <a :href="selectedApplicant?.cv_link && selectedApplicant?.cv_exists ? `/admin/applicant/${selectedApplicant.id}/view-resume` : '#'" 
                                            :target="selectedApplicant?.cv_link && selectedApplicant?.cv_exists ? '_blank' : ''"
                                            class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl font-bold transition-all border group relative overflow-hidden text-sm"
                                            :class="selectedApplicant?.cv_link && selectedApplicant?.cv_exists ? 'bg-kingdom-navy text-white hover:bg-slate-800 shadow-sm border-transparent' : 'bg-slate-50 text-slate-400 border-slate-100 cursor-not-allowed'">
                                             <i data-lucide="eye" class="w-4 h-4"></i>
                                             <span x-text="selectedApplicant?.cv_link ? (selectedApplicant?.cv_exists ? 'View Resume' : 'Resume File Unavailable') : 'No Resume Uploaded'"></span>
                                         </a>

                                         <!-- Download Options -->
                                         <div class="flex gap-2">
                                             <a :href="`/admin/applicant/${selectedApplicant?.id}/download-resume`" 
                                                x-show="selectedApplicant?.cv_link && selectedApplicant?.cv_exists"
                                                class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl font-bold transition-all border border-slate-200 text-kingdom-navy hover:bg-slate-50 hover:border-slate-300 text-xs shadow-sm bg-white">
                                                 <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                                 <span>Download CV</span>
                                             </a>
                                            <a :href="`/admin/applicant/${selectedApplicant?.id}/export-pdf`" 
                                               class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl font-bold transition-all border border-red-100 text-red-600 bg-red-50 hover:bg-red-100/80 hover:border-red-200 text-xs shadow-sm">
                                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                                <span>Export Profile</span>
                                            </a>
                                         </div>
                                     </div>
                                </div>
                            </div>
                       </div>

                       <!-- Specialization -->
                       <div class="space-y-3" x-show="selectedApplicant?.sub_category">
                            <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-kingdom-gold pl-2">
                                <i data-lucide="award" class="w-3.5 h-3.5 text-kingdom-gold"></i> Specialization
                            </h4>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                 <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-800 font-bold text-sm">
                                     <span class="w-2 h-2 rounded-full bg-kingdom-gold"></span>
                                     <span x-text="selectedApplicant?.sub_category"></span>
                                 </span>
                            </div>
                       </div>

                       <!-- Professional Details (Experience & Education Timeline) -->
                       <div class="space-y-6" x-show="(selectedApplicant?.experiences && selectedApplicant?.experiences.length > 0) || (selectedApplicant?.educations && selectedApplicant?.educations.length > 0)">
                            
                            <!-- Work Experience Timeline -->
                            <div class="space-y-3" x-show="selectedApplicant?.experiences && selectedApplicant.experiences.length > 0">
                                <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-kingdom-gold pl-2">
                                    <i data-lucide="briefcase" class="w-3.5 h-3.5 text-kingdom-gold"></i> Work Experience
                                </h4>
                                <div class="bg-white rounded-2xl p-6 border border-slate-100 space-y-6 relative overflow-hidden shadow-sm">
                                     <!-- Vertical line -->
                                     <div class="absolute left-9 top-8 bottom-8 w-0.5 bg-slate-150"></div>

                                     <template x-for="exp in selectedApplicant?.experiences" :key="exp.id">
                                          <div class="relative flex items-start gap-6 group/item">
                                               <!-- Timeline Node -->
                                               <div class="w-6 h-6 rounded-full bg-white border-4 border-kingdom-gold flex items-center justify-center shrink-0 shadow z-10 transition-transform group-hover/item:scale-110"></div>
                                               
                                               <!-- Card Content -->
                                               <div class="bg-[#B89955]/5 p-4 rounded-xl border border-[#B89955]/20 shadow-sm flex-1 hover:border-[#B89955]/40 hover:bg-white transition-all">
                                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 mb-2">
                                                         <div>
                                                             <h5 class="text-sm font-bold text-slate-900 leading-tight" x-text="exp.job_title"></h5>
                                                             <p class="text-xs font-semibold text-slate-500" x-text="exp.company"></p>
                                                         </div>
                                                         <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-150 px-2.5 py-1 rounded-lg w-fit whitespace-nowrap shadow-sm" x-text="(exp.start_date ? new Date(exp.start_date).toLocaleDateString(undefined, {month:'short', year:'numeric'}) : 'N/A') + ' - ' + (exp.is_current ? 'Present' : (exp.end_date ? new Date(exp.end_date).toLocaleDateString(undefined, {month:'short', year:'numeric'}) : 'N/A'))"></span>
                                                    </div>
                                                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line mt-2 border-t border-slate-200/50 pt-2" x-show="exp.description" x-text="exp.description"></p>
                                               </div>
                                          </div>
                                     </template>
                                </div>
                            </div>

                            <!-- Education Timeline -->
                            <div class="space-y-3" x-show="selectedApplicant?.educations && selectedApplicant.educations.length > 0">
                                <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-kingdom-gold pl-2">
                                    <i data-lucide="school" class="w-3.5 h-3.5 text-kingdom-gold"></i> Education
                                </h4>
                                <div class="bg-white rounded-2xl p-6 border border-slate-100 space-y-6 relative overflow-hidden shadow-sm">
                                     <!-- Vertical line -->
                                     <div class="absolute left-9 top-8 bottom-8 w-0.5 bg-slate-150"></div>

                                     <template x-for="edu in selectedApplicant?.educations" :key="edu.id">
                                          <div class="relative flex items-start gap-6 group/item">
                                               <!-- Timeline Node -->
                                               <div class="w-6 h-6 rounded-full bg-white border-4 border-blue-500 flex items-center justify-center shrink-0 shadow z-10 transition-transform group-hover/item:scale-110"></div>
                                               
                                               <!-- Card Content -->
                                               <div class="bg-blue-50/10 p-4 rounded-xl border border-blue-200/40 shadow-sm flex-1 hover:border-blue-300 hover:bg-white transition-all">
                                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 mb-2">
                                                         <div>
                                                             <h5 class="text-sm font-bold text-slate-900 leading-tight" x-text="edu.degree"></h5>
                                                             <p class="text-xs font-semibold text-slate-500" x-text="edu.institution"></p>
                                                         </div>
                                                         <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-150 px-2.5 py-1 rounded-lg w-fit whitespace-nowrap shadow-sm" x-text="(edu.start_date ? new Date(edu.start_date).toLocaleDateString(undefined, {year:'numeric'}) : 'N/A') + ' - ' + (edu.is_current ? 'Present' : (edu.end_date ? new Date(edu.end_date).toLocaleDateString(undefined, {year:'numeric'}) : 'N/A'))"></span>
                                                    </div>
                                                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line mt-2 border-t border-slate-200/50 pt-2" x-show="edu.description" x-text="edu.description"></p>
                                               </div>
                                          </div>
                                     </template>
                                </div>
                            </div>
                       </div>
                  </div>

                  <!-- Fixed Footer -->
                  <div class="px-8 pt-4 pb-6 border-t border-slate-100 bg-slate-50/50 flex gap-3 shrink-0 rounded-b-3xl">
                       <button @click="detailsModalOpen = false" class="flex-1 py-3 border-2 border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-100 hover:border-slate-300 transition-all shadow-sm">
                           Close
                       </button>
                       <button @click="openEditApplicantModal(selectedApplicant); detailsModalOpen = false" class="flex-1 py-3 bg-[#0F1D33] text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-md shadow-kingdom-navy/15 flex items-center justify-center gap-2">
                           <i data-lucide="pencil" class="w-4 h-4"></i> Edit Status
                       </button>
                  </div>
             </div>
         </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editingId" class="fixed inset-0 z-[100] overflow-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="editingId"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="editingId = null"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center" @click="editingId = null">
            <div x-show="editingId"
                 @click.stop
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-md transform rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-100">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-3xl">
                    <h3 x-text="'Edit Status — ' + editingName" class="font-bold text-lg text-[#0F1D33]"></h3>
                    <button @click="editingId = null" class="bg-white p-2 rounded-full text-slate-400 hover:text-slate-650 hover:bg-slate-100 transition-all shadow-sm border border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="px-6 pb-6 pt-4">
                    <form @submit.prevent="submitStatusUpdate" class="flex flex-col gap-4">
                        <input type="hidden" name="id" x-model="editingId">
                        
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider pl-1">Applicant Name</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                </div>
                                <input type="text" x-model="editingName" 
                                       class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 font-bold cursor-not-allowed text-sm shadow-inner" disabled>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5" x-data="{ open: false }">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider pl-1">Application Status <span class="text-red-500">*</span></label>
                            <input type="hidden" name="status" x-model="editingStatus">
                            <div class="relative">
                                <button type="button" @click="open = !open" @click.outside="open = false"
                                        class="w-full pl-3 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-left flex items-center justify-between transition-all group outline-none shadow-sm hover:border-kingdom-gold/30">
                                    <div class="flex items-center gap-3">
                                        <div class="p-1.5 rounded-lg transition-colors"
                                             :class="getStatusBgColor(editingStatus)">
                                            <i data-lucide="activity" class="w-3.5 h-3.5" :class="getStatusTextColor(editingStatus)"></i>
                                        </div>
                                        <span x-text="editingStatus || 'Select Status'" 
                                              class="font-bold text-sm text-kingdom-navy"></span>
                                    </div>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5"
                                     style="display: none;">
                                    <template x-for="option in statusOptions" :key="option">
                                         <button type="button" 
                                                 @click="if (canTransition(editingApplicantRef?.status, option)) { editingStatus = option; open = false; }"
                                                 :disabled="!canTransition(editingApplicantRef?.status, option)"
                                                 class="w-full text-left px-4 py-2.5 text-sm font-bold flex items-center gap-3 group transition-colors border-b border-slate-50 last:border-0"
                                                 :class="{
                                                     'bg-kingdom-gold/5 text-kingdom-navy': editingStatus === option,
                                                     'text-slate-600 hover:bg-slate-50 hover:text-kingdom-navy': editingStatus !== option && canTransition(editingApplicantRef?.status, option),
                                                     'text-slate-300 opacity-40 cursor-not-allowed': !canTransition(editingApplicantRef?.status, option)
                                                 }">
                                             <div class="p-1.5 rounded-lg transition-colors"
                                                  :class="{
                                                      'bg-kingdom-gold/20 text-kingdom-gold': editingStatus === option,
                                                      'bg-slate-50 text-slate-400 group-hover:bg-kingdom-gold/10 group-hover:text-kingdom-gold': editingStatus !== option && canTransition(editingApplicantRef?.status, option),
                                                      'bg-slate-50 text-slate-200': !canTransition(editingApplicantRef?.status, option)
                                                  }">
                                                 <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                                             </div>
                                             <span x-text="option"></span>
                                             <i data-lucide="check" class="w-4 h-4 text-kingdom-gold ml-auto opacity-0 transition-opacity" :class="editingStatus === option ? 'opacity-100' : ''"></i>
                                         </button>
                                     </template>
                                </div>
                            </div>
                        </div>

                        <!-- Resume Upload -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-450 uppercase tracking-wider pl-1">Resume / CV (PDF)</label>
                            <input type="file" @change="uploadedCvFile = $event.target.files[0]" accept="application/pdf"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold outline-none focus:border-kingdom-gold text-kingdom-navy">
                            
                            <template x-if="editingApplicantRef?.cv_link && editingApplicantRef?.cv_exists">
                                <p class="text-xs text-green-600 font-bold mt-1.5 flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-green-600"></i>
                                    <span>Current resume file is stored on the server.</span>
                                </p>
                            </template>
                            <template x-if="editingApplicantRef?.cv_link && !editingApplicantRef?.cv_exists">
                                <p class="text-xs text-red-500 font-bold mt-1.5 flex items-center gap-1">
                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-red-500"></i>
                                    <span>Warning: Resume file is unavailable. Please upload a copy.</span>
                                </p>
                            </template>
                            <template x-if="!editingApplicantRef?.cv_link">
                                <p class="text-xs text-slate-400 font-medium mt-1.5">No resume uploaded yet.</p>
                            </template>
                        </div>

                        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
                            <button type="button" @click="editingId = null" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-350 transition-all text-sm">Cancel</button>
                            <button type="submit" class="flex-1 bg-[#0F1D33] hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 flex items-center justify-center gap-2 text-sm">
                                 <span>Update Status</span>
                                 <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Review & Mapping Modal -->
    <div x-show="categoryReviewModalOpen" class="fixed inset-0 z-[110] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <!-- Backdrop -->
        <div x-show="categoryReviewModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="categoryReviewModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center" @click="categoryReviewModalOpen = false">
            <div x-show="categoryReviewModalOpen"
                 @click.stop
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg transform rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-100 overflow-hidden">
                
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-[#0F1D33] text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-kingdom-gold flex items-center justify-center border border-amber-500/30 shadow-inner">
                            <i data-lucide="tag" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-white">Category Review & Mapping</h3>
                            <p class="text-xs text-slate-300">Applicant: <span class="font-bold text-white" x-text="categoryApplicant?.name"></span></p>
                        </div>
                    </div>
                    <button @click="categoryReviewModalOpen = false" class="bg-white/10 hover:bg-white/20 p-2 rounded-full text-white transition-all">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form :action="categoryApplicant ? `/admin/applicant/${categoryApplicant.id}/assign-category` : '#'" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Applicant Request Context Summary -->
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100/30 rounded-2xl p-4 border border-amber-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-900">Applicant Requested Role</span>
                            <span class="text-xs font-bold text-amber-900 bg-amber-200/80 px-2.5 py-0.5 rounded-full border border-amber-300" x-text="categoryApplicant?.other_category_description || 'Custom Role'"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span>Current System Status:</span>
                            <span class="font-semibold text-amber-800">Other / Not Listed (Uncategorised)</span>
                        </div>
                    </div>

                    <!-- Action 1: Assign to Existing Category -->
                    <div class="space-y-4">
                        <div class="border-b border-slate-100 pb-1">
                            <h4 class="text-xs font-extrabold text-kingdom-navy uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-kingdom-navy text-white flex items-center justify-center text-[10px]">1</span>
                                Assign to Official Category
                            </h4>
                        </div>

                        <!-- Category Select -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Official Category <span class="text-red-500">*</span></label>
                            <select name="role" x-model="categoryForm.role" required @change="onCategoryChange()" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-bold text-slate-800 focus:bg-white focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 outline-none transition-all">
                                <option value="">Select official category...</option>
                                <template x-for="cat in allCategories" :key="cat.id">
                                    <option :value="cat.name" x-text="cat.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Sub-Category Select -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Sub-Category / Specialization <span class="text-slate-400 font-normal">(Optional)</span></label>
                            <template x-if="availableSubCategories.length > 0">
                                <select name="sub_category" x-model="categoryForm.sub_category" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-bold text-slate-800 focus:bg-white focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 outline-none transition-all">
                                    <option value="">Select sub-category...</option>
                                    <template x-for="sub in availableSubCategories" :key="sub.id">
                                        <option :value="sub.name" x-text="sub.name"></option>
                                    </template>
                                </select>
                            </template>
                            <template x-if="availableSubCategories.length === 0">
                                <input type="text" name="sub_category" x-model="categoryForm.sub_category" placeholder="e.g. Head Chef, Event Assistant" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium text-slate-800 focus:bg-white focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 outline-none transition-all">
                            </template>
                        </div>
                    </div>

                    <div class="pt-2 flex gap-3 border-t border-slate-100">
                        <button type="button" @click="categoryReviewModalOpen = false" class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-2.5 px-4 bg-kingdom-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-1.5">
                            <i data-lucide="check" class="w-4 h-4 text-kingdom-gold"></i>
                            <span>Confirm & Map</span>
                        </button>
                    </div>

                    <!-- Action 2: Create Category from Request -->
                    <div class="mt-4 pt-4 border-t border-slate-100/80 bg-slate-50/50 -mx-6 -mb-6 p-6 rounded-b-3xl">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h5 class="text-xs font-bold text-slate-800">Need to create a new category?</h5>
                                <p class="text-[11px] text-slate-500">Pre-fill category creation with "<span class="font-bold text-slate-700" x-text="categoryApplicant?.other_category_description"></span>"</p>
                            </div>
                            <a :href="categoryApplicant ? `{{ route('admin.job-category') }}?new_name=${encodeURIComponent(categoryApplicant.other_category_description || '')}` : '#'" 
                               target="_blank"
                               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs shadow-sm transition-all whitespace-nowrap">
                                <i data-lucide="folder-plus" class="w-3.5 h-3.5 text-kingdom-navy"></i>
                                <span>Create Category from Request</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    window.applicantTable = function applicantTable() {
        return {
            view: 'list',
            filters: {
                search: '{{ request('search', '') }}',
                role: '{{ request('role', '') }}',
                status: '{{ request('status', '') }}',
                date: '{{ request('date', '') }}'
            },
            showFilters: true,
            hasUncategorizedFilter: {{ request('filter') === 'uncategorized' ? 'true' : 'false' }},
            allCategories: @js($categories ?? []),
            allSubCategories: @js($subCategories ?? []),
            
            // Category Review Modal State
            categoryReviewModalOpen: false,
            categoryApplicant: null,
            categoryForm: {
                role: '',
                sub_category: ''
            },
            availableSubCategories: [],

            openCategoryReviewModal(applicant) {
                this.categoryApplicant = applicant;
                this.categoryForm.role = (applicant.role !== 'Other' && applicant.role !== 'Other / Not Listed') ? applicant.role : '';
                this.categoryForm.sub_category = applicant.sub_category || applicant.other_category_description || '';
                this.onCategoryChange();
                this.categoryReviewModalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            onCategoryChange() {
                if (!this.categoryForm.role) {
                    this.availableSubCategories = [];
                    return;
                }
                this.availableSubCategories = this.allSubCategories.filter(s => s.parent_category === this.categoryForm.role && s.status === 'Active');
            },

            isActiveUser: {
                @foreach($applicants as $c)
                    @if($c->user)
                        '{{ $c->user->id }}': {{ $c->user->is_active ? 'true' : 'false' }},
                    @endif
                @endforeach
            },
            async toggleUserStatus(userId) {
                const orig = this.isActiveUser[userId];
                const newStatus = !orig;
                this.isActiveUser[userId] = newStatus;

                try {
                    const response = await fetch(`/admin/users/${userId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ is_active: newStatus })
                    });
                    if (!response.ok) this.isActiveUser[userId] = orig;
                } catch {
                    this.isActiveUser[userId] = orig;
                }
            },
            
            // Edit Modal State
            editingId: null,
            editingName: '',
            editingStatus: 'Pending',
            updateUrl: '',
            statusOptions: ['Pending', 'Hired', 'Rejected'],
            statusDropdownOpen: false,
            uploadedCvFile: null,

            canTransition(fromStatus, toStatus) {
                const order = ['Pending', 'Hired', 'Rejected'];
                const fromIndex = order.indexOf(fromStatus);
                if (fromIndex === -1) return true;
                return order.indexOf(toStatus) >= fromIndex;
            },

            // Export State
            exportType: 'all',
            exportLimit: 10,
            startDate: '',
            endDate: '',
            selectMode: false,
            selectedRows: [],
            selectAll: false,

            toggleRowSelection(id) {
                if (this.selectedRows.includes(id)) {
                    this.selectedRows = this.selectedRows.filter(rowId => rowId !== id);
                } else {
                    this.selectedRows.push(id);
                }
            },

            // Details Modal State
            detailsModalOpen: false,
            selectedApplicant: null,
            
            openDetailsModal(applicant) {
                this.selectedApplicant = applicant;
                this.detailsModalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            applyFilters() {
                let url = new URL(window.location.href);
                if (this.filters.search) {
                    url.searchParams.set('search', this.filters.search);
                } else {
                    url.searchParams.delete('search');
                }
                if (this.filters.role) {
                    url.searchParams.set('role', this.filters.role);
                } else {
                    url.searchParams.delete('role');
                }
                if (this.filters.status) {
                    url.searchParams.set('status', this.filters.status);
                } else {
                    url.searchParams.delete('status');
                }
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            },
            clearFilters() {
                this.filters.search = '';
                this.filters.status = '';
                this.filters.role = '';
                let url = new URL(window.location.href);
                url.searchParams.delete('search');
                url.searchParams.delete('status');
                url.searchParams.delete('role');
                url.searchParams.delete('filter');
                url.searchParams.delete('category_review');
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            },

            editingApplicantRef: null,
            openEditApplicantModal(applicant) {
                this.editingApplicantRef = applicant;
                this.editingId = applicant.id;
                this.editingName = applicant.name;
                this.editingStatus = applicant.status;
                this.uploadedCvFile = null;
                this.updateUrl = `/admin/applicant/${applicant.id}/status`;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            async submitStatusUpdate() {
                const appId = this.editingId;
                const newStatus = this.editingStatus;

                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('status', newStatus);
                if (this.uploadedCvFile) {
                    formData.append('cv', this.uploadedCvFile);
                }

                try {
                    const response = await fetch(this.updateUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error('Failed to update status');
                    }

                    const data = await response.json();

                    // Success!
                    if (this.editingApplicantRef) {
                        this.editingApplicantRef.status = newStatus;
                        if (data.cv_link !== undefined) {
                            this.editingApplicantRef.cv_link = data.cv_link;
                            this.editingApplicantRef.cv_exists = data.cv_exists;
                        }
                    }
                    if (this.selectedApplicant && this.selectedApplicant.id === appId) {
                        this.selectedApplicant.status = newStatus;
                        if (data.cv_link !== undefined) {
                            this.selectedApplicant.cv_link = data.cv_link;
                            this.selectedApplicant.cv_exists = data.cv_exists;
                        }
                    }

                    this.editingId = null;
                    this.uploadedCvFile = null;

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Applicant details updated successfully'
                    });

                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });

                } catch (error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'Failed to update applicant status.',
                        confirmButtonColor: '#0F1D33'
                    });
                }
            },
            
            getStatusBgColor(status) {
                if(status === 'Hired') return 'bg-green-100 text-green-700';
                if(status === 'Rejected') return 'bg-red-50 text-red-600';
                return 'bg-kingdom-gold/20 text-kingdom-gold';
            },
            
            getStatusTextColor(status) {
                 if(status === 'Hired') return 'text-green-700';
                 if(status === 'Rejected') return 'text-red-600';
                 return 'text-kingdom-gold';
            },

            triggerExport(format) {
                let url = `{{ route('admin.applicant.export') }}?format=${format}&type=${this.exportType}`;
                
                if (this.exportType === 'last' || this.exportType === 'first') {
                    url += `&limit=${this.exportLimit}`;
                }
                
                if (this.exportType === 'selected') {
                    if (this.selectedRows.length === 0) {
                        Swal.fire({ icon: 'warning', title: 'No Selection', text: 'Please select at least one row.', confirmButtonColor: '#0F1D33' });
                        return;
                    }
                    url += `&ids=${this.selectedRows.join(',')}`;
                }
                
                window.location.href = url;
                this.openExport = false;
            },

            toggleSelectAll() {
                 const allIds = @json($applicants->pluck('id'));
                 
                if (this.selectAll) {
                    this.selectedRows = allIds;
                } else {
                    this.selectedRows = [];
                }
            }
        }
    }


    
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
@endsection
