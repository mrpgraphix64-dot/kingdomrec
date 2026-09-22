@extends('layouts.admin')

@section('title', 'Applications | Kingdom Admin')

@section('content')
<style>
    .applications-table {
        counter-reset: app-sec;
    }
    .application-row {
        counter-increment: app-sec;
    }
    .application-index::before {
        content: counter(app-sec);
    }
</style>
<div x-data="applicationManager()" @keydown.escape.window="detailModalOpen = false" class="flex flex-col space-y-3">

    {{-- Header --}}
    <div class="dashboard-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 shrink-0">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Job Applications</h1>
                <div class="flex items-center gap-1.5">
                    <span class="px-1.5 py-px rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200" x-text="stats.total + ' Total'">{{ $stats['total'] }} Total</span>
                    <span class="px-1.5 py-px rounded text-[9px] font-bold bg-amber-100 text-amber-700 border border-amber-200" x-text="stats.pending + ' Pending'">{{ $stats['pending'] }} Pending</span>
                    <span class="px-1.5 py-px rounded text-[9px] font-bold bg-blue-100 text-blue-700 border border-blue-200" x-text="stats.reviewed + ' Reviewed'">{{ $stats['reviewed'] }} Reviewed</span>
                    <span class="px-1.5 py-px rounded text-[9px] font-bold bg-green-100 text-green-700 border border-green-200" x-text="stats.qualified + ' Qualified'">{{ $stats['qualified'] }} Qualified</span>
                    <span class="px-1.5 py-px rounded text-[9px] font-bold bg-red-55 text-red-600 border border-red-100" x-text="stats.rejected + ' Rejected'">{{ $stats['rejected'] }} Rejected</span>
                </div>
            </div>
            <p class="text-slate-500 text-xs">Review, qualify, or reject applicant submissions</p>
        </div>
    </div>

    {{-- Table Container --}}
    <div class="anim-fade-in-up bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-visible w-full self-start">
        
        <!-- Filter Bar placed above table -->
        <div class="p-4 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 bg-slate-50/50 shrink-0">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" x-model="searchQuery" @keyup.enter="applyFilters()" placeholder="Search applications..." class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                </div>

                <!-- Status Filter -->
                <select x-model="statusFilter" @change="applyFilters()" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Reviewed">Reviewed</option>
                    <option value="Qualified">Qualified</option>
                    <option value="Rejected">Rejected</option>
                </select>

                <!-- Clear Filters Button -->
                <button @click="searchQuery = ''; statusFilter = ''; applyFilters()" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-kingdom-navy transition-colors flex items-center gap-1" x-show="searchQuery || statusFilter">
                    <i data-lucide="x-circle" class="w-4 h-4"></i> Clear
                </button>
            </div>
        </div>

        @if(count($applications) > 0)
        <div class="overflow-auto w-full max-h-[calc(100vh-210px)] admin-table-container custom-scrollbar">
            <table class="applications-table w-full min-w-[1000px] text-left border-collapse admin-table">
                <thead class="sticky top-0 z-20 bg-[#0f1f3d] shadow-sm">
                    <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-[11px] border-b border-slate-700">
                        <th class="py-2.5 px-3 w-16 text-center text-white bg-[#0f1f3d]">ID</th>
                        <th class="py-2.5 px-3 text-white bg-[#0f1f3d]">Applicant</th>
                        <th class="py-2.5 px-3 text-white bg-[#0f1f3d]">Job Title</th>
                        <th class="py-2.5 px-3 w-[18%] text-white hidden md:table-cell bg-[#0f1f3d]">Applied</th>
                        <th class="py-2.5 px-3 w-[15%] text-white bg-[#0f1f3d]">Status</th>
                        <th class="py-2.5 px-3 text-right w-44 text-white bg-[#0f1f3d]">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-transparent">
                    @foreach($applications as $app)
                    <tr x-data="{ appId: {{ $app->id }} }"
                        x-show="isVisible({{ json_encode([
                            'name' => $app->applicant?->name ?? '',
                            'email' => $app->applicant?->email ?? '',
                            'job' => $app->jobPost?->title ?? '',
                        ]) }}, statuses[appId])"
                        class="application-row border-b border-slate-100 hover:bg-slate-50 transition-colors"
                        @click="openDetail(appId)">

                        <td class="py-4 px-4 w-20 text-center">
                            <span class="application-index inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono text-[10px] font-bold border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-colors shadow-sm"></span>
                        </td>

                        {{-- Applicant Info --}}
                        <td class="py-4 px-4 text-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <x-avatar :user="$app->applicant" class="w-8 h-8 rounded-full ring-2 ring-slate-200 shadow-sm" />
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 text-sm max-w-[180px] block truncate" title="{{ $app->applicant?->name ?? 'N/A' }}">{{ $app->applicant?->name ?? 'N/A' }}</span>
                                    <span class="text-[10px] text-slate-400 max-w-[180px] block truncate" title="{{ $app->applicant?->email ?? '' }}">{{ $app->applicant?->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Job --}}
                        <td class="py-4 px-4 text-sm">
                            <span class="font-semibold text-slate-800 text-sm max-w-[200px] block truncate" title="{{ $app->jobPost?->sub_category ?? 'Deleted Job' }}">{{ $app->jobPost?->sub_category ?? 'Deleted Job' }}</span>
                            <span class="text-[10px] text-slate-400 max-w-[200px] block truncate" title="{{ $app->jobPost?->company_name ?? '' }}">{{ $app->jobPost?->company_name ?? '' }}</span>
                        </td>

                        {{-- Date --}}
                        <td class="py-4 px-4 w-[18%] text-slate-600 text-sm hidden md:table-cell">
                            <span class="font-medium block">{{ $app->created_at->format('d M Y') }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ $app->created_at->diffForHumans() }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="py-4 px-4 w-[15%] text-sm">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold border capitalize"
                                  :class="getStatusBgColor(statuses[appId])"
                                  x-text="statuses[appId]">
                                {{ $app->status }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="py-4 px-4 text-right w-48" @click.stop>
                            <div class="flex items-center justify-end gap-1.5">
                                <div x-data="{ open: false }" class="relative">
                                    <button @click.stop="open = !open" class="px-2.5 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-655 transition-all text-[10px] font-bold flex items-center gap-1 border border-slate-200 shadow-sm">
                                        Status <i data-lucide="chevron-down" class="w-3 h-3"></i>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition
                                        class="absolute right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-[9999] min-w-[140px] py-1">
                                        @foreach(['Pending', 'Reviewed', 'Qualified', 'Rejected'] as $status)
                                        <button type="button" 
                                            @click="if (canTransition(statuses[appId], '{{ $status }}', appId)) { updateStatus(appId, '{{ $status }}'); open = false; }"
                                            :disabled="!canTransition(statuses[appId], '{{ $status }}', appId)"
                                            class="w-full text-left px-3 py-2 text-xs font-bold hover:bg-slate-50 transition-colors flex items-center gap-2"
                                            :class="{
                                                'text-indigo-655': statuses[appId] === '{{ $status }}',
                                                'text-slate-600': statuses[appId] !== '{{ $status }}' && canTransition(statuses[appId], '{{ $status }}', appId),
                                                'text-slate-300 opacity-40 cursor-not-allowed': !canTransition(statuses[appId], '{{ $status }}', appId)
                                            }">
                                            <i data-lucide="check" class="w-3 h-3" x-show="statuses[appId] === '{{ $status }}'"></i>
                                            <span x-text="'{{ $status }}'"></span>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                                <button @click.stop="openDetail(appId)" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button @click.stop="$dispatch('open-confirm-modal', { 
                                            title: 'Delete Application', 
                                            message: 'Are you sure you want to delete this job application? This action cannot be undone.', 
                                            onConfirm: () => submitDeleteForm('/admin/applications/' + appId)
                                         })" 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" 
                                        title="Delete Application">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- Empty State — a real full-width block, not a colspan cell inside the wide scrolling table -->
        <div class="py-20 text-center text-slate-400">
            <div class="flex flex-col items-center justify-center">
                <div class="bg-slate-50 p-4 rounded-full mb-3 ring-4 ring-white shadow-sm">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-350"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-950">No applications yet</h3>
                <p class="text-slate-550 max-w-sm mx-auto mt-2 text-sm">When applicants apply for jobs, they'll appear here.</p>
            </div>
        </div>
        @endif
        <!-- Pagination Links -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
            {{ $applications->links() }}
        </div>
    </div>

    {{-- Detail Modal --}}
    <template x-if="detailModalOpen && selectedApp">
        <div class="fixed inset-0 z-[100] overflow-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
             <!-- Backdrop -->
             <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="detailModalOpen = false"></div>

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

             <div class="flex min-h-full items-center justify-center p-4" @click="detailModalOpen = false">
                 <div class="relative bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden transition-all flex flex-col max-h-[85vh] border border-slate-100" @click.stop>
                    
                      <!-- Premium Fixed Header -->
                      <div class="relative bg-[#0F1D33] px-8 py-6 text-white shrink-0 border-b-2 border-kingdom-gold/30">
                            <!-- Decorative background glow -->
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-kingdom-gold/10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none"></div>
                            
                            <!-- Floating Close Button -->
                            <button @click="detailModalOpen = false" class="absolute top-4 right-4 p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-md z-20 hover:rotate-90 duration-300">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Header Content (Avatar, Name, Role, Status) -->
                            <div class="flex flex-col sm:flex-row items-center gap-5 mt-2 relative z-10 text-center sm:text-left">
                                <div class="w-20 h-20 rounded-full bg-white p-0.5 shadow-lg ring-4 ring-kingdom-gold/30 shrink-0 relative">
                                    {{-- Initials fallback --}}
                                    <div class="w-full h-full rounded-full bg-gradient-to-br from-slate-700 to-slate-800 text-white flex items-center justify-center text-xl font-bold">
                                        <span x-text="selectedApp?.applicant_name ? selectedApp.applicant_name.substring(0,2).toUpperCase() : ''"></span>
                                    </div>
                                    {{-- Image overlay --}}
                                    <img x-show="selectedApp?.applicant_image"
                                         :src="selectedApp?.applicant_profile_photo_url || ''" 
                                         class="absolute inset-[2px] w-[calc(100%-4px)] h-[calc(100%-4px)] object-cover rounded-full"
                                         onerror="this.style.display='none'"
                                         x-cloak>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-center sm:justify-start gap-2.5">
                                        <h3 class="text-xl md:text-2xl font-black tracking-wide text-white truncate" x-text="selectedApp?.applicant_name"></h3>
                                        <span :class="getStatusBgColor(selectedApp?.status)" 
                                              class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase border border-current shadow-sm w-fit mx-auto sm:mx-0" 
                                              x-text="selectedApp?.status || 'Pending'"></span>
                                    </div>
                                    <p class="text-[#B89955] dark:text-[#B89955] font-bold text-xs uppercase tracking-widest flex items-center justify-center sm:justify-start gap-1.5 mt-2">
                                        <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                                        <span x-text="(selectedApp?.applicant_role === 'applicant' ? 'Applicant' : selectedApp?.applicant_role) || 'Applicant'"></span>
                                    </p>
                                </div>
                            </div>
                      </div>

                      <!-- Scrollable Body Content -->
                      <div class="px-8 py-6 overflow-y-auto custom-modal-scrollbar flex-1 space-y-6 min-h-0 bg-slate-50/30">
                           <!-- Details Grid (Contact Info & Talent Metrics) -->
                           <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Contact Info Card -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                        <i data-lucide="contact" class="w-3.5 h-3.5 text-[#B89955]"></i> Contact Info
                                    </h4>
                                    <div class="bg-white rounded-2xl p-4 border border-slate-100 space-y-3 shadow-sm">
                                        <div class="flex items-center gap-3 bg-blue-50/30 border border-blue-100/50 p-3 rounded-xl hover:bg-blue-50/60 transition-colors group">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm group-hover:bg-blue-100 transition-colors">
                                                <i data-lucide="mail" class="w-4 h-4"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[9px] text-blue-500 font-bold uppercase tracking-wider">Email</p>
                                                <p class="text-xs font-bold text-slate-800 break-all" x-text="selectedApp?.applicant_email"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 bg-emerald-50/30 border border-emerald-100/50 p-3 rounded-xl hover:bg-emerald-50/60 transition-colors group">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm group-hover:bg-emerald-100 transition-colors">
                                                <i data-lucide="phone" class="w-4 h-4"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[9px] text-emerald-500 font-bold uppercase tracking-wider">Phone</p>
                                                <p class="text-xs font-bold text-slate-800" x-text="selectedApp?.applicant_phone || 'Not provided'"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 bg-purple-50/30 border border-purple-100/50 p-3 rounded-xl hover:bg-purple-50/60 transition-colors group">
                                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-sm group-hover:bg-purple-100 transition-colors">
                                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[9px] text-purple-500 font-bold uppercase tracking-wider">Location</p>
                                                <p class="text-xs font-bold text-slate-800" x-text="selectedApp?.applicant_location || 'Not provided'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Talent Metrics & Actions -->
                                <div class="space-y-3 flex flex-col">
                                    <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                        <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-[#B89955]"></i> Job Application Details
                                    </h4>
                                    <div class="bg-white rounded-2xl p-4 border border-slate-100 flex-1 flex flex-col justify-between gap-4 shadow-sm">
                                         <div class="space-y-3">
                                             <div class="flex items-center gap-3 bg-indigo-50/30 border border-indigo-100/50 p-3 rounded-xl hover:bg-indigo-50/60 transition-colors group">
                                                 <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm group-hover:bg-indigo-100 transition-colors">
                                                     <i data-lucide="briefcase" class="w-4 h-4"></i>
                                                 </div>
                                                 <div>
                                                     <p class="text-[9px] text-indigo-500 font-bold uppercase tracking-wider">Applied Position</p>
                                                     <p class="text-xs font-bold text-slate-800" x-text="selectedApp?.job_title"></p>
                                                     <p class="text-[10px] text-slate-400" x-text="selectedApp?.company"></p>
                                                 </div>
                                             </div>
                                             <div class="flex items-center gap-3 bg-amber-50/30 border border-amber-100/50 p-3 rounded-xl hover:bg-amber-50/60 transition-colors group">
                                                 <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm group-hover:bg-amber-100 transition-colors">
                                                     <i data-lucide="calendar" class="w-4 h-4"></i>
                                                 </div>
                                                 <div>
                                                     <p class="text-[9px] text-amber-500 font-bold uppercase tracking-wider">Date Applied</p>
                                                     <p class="text-xs font-bold text-slate-800" x-text="selectedApp?.date"></p>
                                                 </div>
                                             </div>
                                         </div>
                                         
                                         <div class="flex flex-col gap-2">
                                             <!-- View Resume (New Tab) -->
                                             <a :href="selectedApp?.applicant_cv_link && selectedApp?.applicant_cv_exists ? `/admin/applicant/${selectedApp.applicant_id}/view-resume` : '#'" 
                                                :target="selectedApp?.applicant_cv_link && selectedApp?.applicant_cv_exists ? '_blank' : ''"
                                                class="flex items-center justify-center gap-2 w-full py-2 rounded-xl font-bold transition-all border group relative overflow-hidden text-xs"
                                                :class="selectedApp?.applicant_cv_link && selectedApp?.applicant_cv_exists ? 'bg-[#0F1D33] text-white hover:bg-slate-800 shadow-sm border-transparent' : 'bg-slate-50 text-slate-400 border-slate-100 cursor-not-allowed'">
                                                 <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                 <span x-text="selectedApp?.applicant_cv_link ? (selectedApp?.applicant_cv_exists ? 'View Resume' : 'Resume File Unavailable') : 'No Resume Uploaded'"></span>
                                             </a>

                                             <!-- Download Options -->
                                             <div class="flex gap-2">
                                                 <a :href="`/admin/applicant/${selectedApp?.applicant_id}/download-resume`" 
                                                    x-show="selectedApp?.applicant_cv_link && selectedApp?.applicant_cv_exists"
                                                    class="flex-1 flex items-center justify-center gap-1 py-2 rounded-xl font-bold transition-all border border-slate-200 text-kingdom-navy hover:bg-slate-50 hover:border-slate-300 text-[10px] shadow-sm bg-white">
                                                     <i data-lucide="download" class="w-3 h-3"></i>
                                                     <span>Download CV</span>
                                                 </a>
                                                <a :href="`/admin/applicant/${selectedApp?.applicant_id}/export-pdf`" 
                                                   class="flex-1 flex items-center justify-center gap-1 py-2 rounded-xl font-bold transition-all border border-red-100 text-red-600 bg-red-50 hover:bg-red-100/80 hover:border-red-200 text-[10px] shadow-sm">
                                                    <i data-lucide="file-text" class="w-3 h-3"></i>
                                                    <span>Export Profile</span>
                                                </a>
                                             </div>
                                         </div>
                                    </div>
                                </div>
                           </div>

                           <!-- Bio -->
                           <div class="space-y-3" x-show="selectedApp?.applicant_bio">
                                <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-[#B89955]"></i> Bio / About
                                </h4>
                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                     <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line" x-text="selectedApp?.applicant_bio"></p>
                                </div>
                           </div>

                           <!-- Specialization & Work Preferences -->
                           <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                               <div class="space-y-3" x-show="selectedApp?.applicant_sub_category">
                                    <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                        <i data-lucide="award" class="w-3.5 h-3.5 text-[#B89955]"></i> Specialization
                                    </h4>
                                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm h-full flex items-center">
                                         <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-800 font-bold text-xs">
                                             <span class="w-2 h-2 rounded-full bg-kingdom-gold"></span>
                                             <span x-text="selectedApp?.applicant_sub_category"></span>
                                         </span>
                                    </div>
                               </div>

                               <div class="space-y-3" x-show="selectedApp?.applicant_expected_salary || selectedApp?.applicant_availability">
                                    <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                        <i data-lucide="settings" class="w-3.5 h-3.5 text-[#B89955]"></i> Preferences
                                    </h4>
                                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm space-y-2 text-xs">
                                         <div class="flex justify-between" x-show="selectedApp?.applicant_expected_salary">
                                             <span class="text-slate-400 font-medium">Expected Salary:</span>
                                             <span class="font-bold text-slate-800" x-text="selectedApp?.applicant_expected_salary"></span>
                                         </div>
                                         <div class="flex justify-between" x-show="selectedApp?.applicant_availability">
                                             <span class="text-slate-400 font-medium">Availability:</span>
                                             <span class="font-bold text-slate-800" x-text="selectedApp?.applicant_availability"></span>
                                         </div>
                                    </div>
                               </div>
                           </div>

                           <!-- Cover Letter -->
                           <div class="space-y-3" x-show="selectedApp?.cover_letter">
                                <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#B89955]"></i> Cover Letter
                                </h4>
                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                     <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-wrap" x-text="selectedApp?.cover_letter"></p>
                                </div>
                           </div>

                           <!-- Admin Notes -->
                           <div class="space-y-3" x-show="selectedApp?.admin_notes">
                                <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                    <i data-lucide="message-square" class="w-3.5 h-3.5 text-[#B89955]"></i> Admin Notes
                                </h4>
                                <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 shadow-sm">
                                     <p class="text-xs text-blue-800 leading-relaxed whitespace-pre-wrap" x-text="selectedApp?.admin_notes"></p>
                                </div>
                           </div>

                           <!-- Professional Details (Timeline) -->
                           <div class="space-y-6" x-show="(selectedApp?.applicant_experiences && selectedApp.applicant_experiences.length > 0) || (selectedApp?.applicant_educations && selectedApp.applicant_educations.length > 0)">
                                
                                <!-- Work Experience Timeline -->
                                <div class="space-y-3" x-show="selectedApp?.applicant_experiences && selectedApp.applicant_experiences.length > 0">
                                    <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                        <i data-lucide="briefcase" class="w-3.5 h-3.5 text-[#B89955]"></i> Work Experience
                                    </h4>
                                    <div class="bg-white rounded-2xl p-6 border border-slate-100 space-y-6 relative overflow-hidden shadow-sm">
                                         <!-- Vertical line -->
                                         <div class="absolute left-9 top-8 bottom-8 w-0.5 bg-slate-150"></div>

                                         <template x-for="exp in selectedApp?.applicant_experiences" :key="exp.id">
                                              <div class="relative flex items-start gap-6 group/item">
                                                   <!-- Timeline Node -->
                                                   <div class="w-6 h-6 rounded-full bg-white border-4 border-kingdom-gold flex items-center justify-center shrink-0 shadow z-10 transition-transform group-hover/item:scale-110"></div>
                                                   
                                                   <!-- Card Content -->
                                                   <div class="bg-[#B89955]/5 p-4 rounded-xl border border-[#B89955]/20 shadow-sm flex-1 hover:border-[#B89955]/40 hover:bg-white transition-all">
                                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 mb-2">
                                                             <div>
                                                                 <h5 class="text-xs font-bold text-slate-900 leading-tight" x-text="exp.job_title"></h5>
                                                                 <p class="text-[10px] font-semibold text-slate-500" x-text="exp.company"></p>
                                                             </div>
                                                             <span class="text-[9px] font-bold text-slate-500 bg-white border border-slate-150 px-2 py-0.5 rounded-lg w-fit whitespace-nowrap shadow-sm" x-text="(exp.start_date ? new Date(exp.start_date).toLocaleDateString(undefined, {month:'short', year:'numeric'}) : 'N/A') + ' - ' + (exp.is_current ? 'Present' : (exp.end_date ? new Date(exp.end_date).toLocaleDateString(undefined, {month:'short', year:'numeric'}) : 'N/A'))"></span>
                                                        </div>
                                                        <p class="text-[11px] text-slate-600 leading-relaxed whitespace-pre-line mt-2 border-t border-slate-200/50 pt-2" x-show="exp.description" x-text="exp.description"></p>
                                                   </div>
                                              </div>
                                         </template>
                                    </div>
                                </div>

                                <!-- Education Timeline -->
                                <div class="space-y-3" x-show="selectedApp?.applicant_educations && selectedApp.applicant_educations.length > 0">
                                    <h4 class="text-xs font-bold text-kingdom-navy uppercase tracking-wider flex items-center gap-2 border-l-4 border-[#B89955] pl-2">
                                        <i data-lucide="school" class="w-3.5 h-3.5 text-[#B89955]"></i> Education
                                    </h4>
                                    <div class="bg-white rounded-2xl p-6 border border-slate-100 space-y-6 relative overflow-hidden shadow-sm">
                                         <!-- Vertical line -->
                                         <div class="absolute left-9 top-8 bottom-8 w-0.5 bg-slate-150"></div>

                                         <template x-for="edu in selectedApp?.applicant_educations" :key="edu.id">
                                              <div class="relative flex items-start gap-6 group/item">
                                                   <!-- Timeline Node -->
                                                   <div class="w-6 h-6 rounded-full bg-white border-4 border-blue-500 flex items-center justify-center shrink-0 shadow z-10 transition-transform group-hover/item:scale-110"></div>
                                                   
                                                   <!-- Card Content -->
                                                   <div class="bg-blue-50/10 p-4 rounded-xl border border-blue-200/40 shadow-sm flex-1 hover:border-blue-300 hover:bg-white transition-all">
                                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 mb-2">
                                                             <div>
                                                                 <h5 class="text-xs font-bold text-slate-900 leading-tight" x-text="edu.degree"></h5>
                                                                 <p class="text-[10px] font-semibold text-slate-500" x-text="edu.institution"></p>
                                                             </div>
                                                             <span class="text-[9px] font-bold text-slate-500 bg-white border border-slate-150 px-2 py-0.5 rounded-lg w-fit whitespace-nowrap shadow-sm" x-text="(edu.start_date ? new Date(edu.start_date).toLocaleDateString(undefined, {year:'numeric'}) : 'N/A') + ' - ' + (edu.is_current ? 'Present' : (edu.end_date ? new Date(edu.end_date).toLocaleDateString(undefined, {year:'numeric'}) : 'N/A'))"></span>
                                                        </div>
                                                        <p class="text-[11px] text-slate-600 leading-relaxed whitespace-pre-line mt-2 border-t border-slate-200/50 pt-2" x-show="edu.description" x-text="edu.description"></p>
                                                   </div>
                                              </div>
                                         </template>
                                    </div>
                                </div>
                           </div>
                      </div>

                      <!-- Fixed Footer -->
                      <div class="px-8 pt-4 pb-6 border-t border-slate-100 bg-slate-50/50 flex gap-3 shrink-0 rounded-b-3xl">
                           <button @click="detailModalOpen = false" class="flex-1 py-3 border-2 border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-100 hover:border-slate-300 transition-all shadow-sm">
                               Close
                           </button>
                      </div>
                 </div>
             </div>
        </div>
    </template>

</div>

@php
    $appData = $applications->map(function($a) {
        $path = str_replace(['storage/app/private/', 'storage/app/'], '', ltrim($a->applicant?->cv_link, '/'));
        $cvExists = $a->applicant?->cv_link ? \Illuminate\Support\Facades\Storage::disk('local')->exists($path) : false;
        return [
            'id' => $a->id,
            'applicant_id' => $a->applicant?->id,
            'applicant_name' => $a->applicant?->name ?? 'N/A',
            'applicant_email' => $a->applicant?->email ?? '',
            'applicant_phone' => $a->applicant?->phone ?? '',
            'applicant_location' => $a->applicant?->location ?? '',
            'applicant_role' => $a->applicant?->role ?? '',
            'applicant_sub_category' => $a->applicant?->sub_category ?? '',
            'applicant_cv_link' => $a->applicant?->cv_link ?? '',
            'applicant_cv_exists' => $cvExists,
            'applicant_image' => $a->applicant?->image ?? '',
            'applicant_bio' => $a->applicant?->bio ?? '',
            'applicant_profile_photo_url' => $a->applicant?->profile_photo_url ?? '',
            'applicant_expected_salary' => $a->applicant?->expected_salary ?? '',
            'applicant_availability' => $a->applicant?->availability ?? '',
            'applicant_linkedin_url' => $a->applicant?->linkedin_url ?? '',
            'applicant_portfolio_url' => $a->applicant?->portfolio_url ?? '',
            'applicant_educations' => $a->applicant?->educations ?? [],
            'applicant_experiences' => $a->applicant?->experiences ?? [],
            'job_title' => $a->jobPost?->sub_category ?? 'Deleted Job',
            'company' => $a->jobPost?->company_name ?? '',
            'status' => $a->status,
            'date' => $a->created_at->format('d M Y'),
            'cover_letter' => $a->cover_letter,
            'admin_notes' => $a->admin_notes,
        ];
    })->values();
@endphp

<script>
window.applicationManager = function applicationManager() {
    return {
        searchQuery: '{{ request('search', '') }}',
        statusFilter: '{{ request('status', '') }}',
        detailModalOpen: false,
        selectedApp: null,

        init() {
            this.$watch('detailModalOpen', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        },

        stats: {
            total: {{ $stats['total'] }},
            pending: {{ $stats['pending'] }},
            body_reviewed: {{ $stats['reviewed'] }},
            reviewed: {{ $stats['reviewed'] }},
            qualified: {{ $stats['qualified'] }},
            rejected: {{ $stats['rejected'] }}
        },

        statuses: {
            @foreach($applications as $app)
            '{{ $app->id }}': '{{ $app->status }}',
            @endforeach
        },

        requalifiedCounts: {
            @foreach($applications as $app)
            '{{ $app->id }}': {{ $app->requalified_count ?? 0 }},
            @endforeach
        },

        canTransition(fromStatus, toStatus, appId) {
            if (fromStatus === toStatus) return false;
            // Enforce that Rejected can only be moved to Qualified once
            if (fromStatus === 'Rejected' && toStatus === 'Qualified') {
                return (this.requalifiedCounts[appId] || 0) < 1;
            }
            return true;
        },

        // Client-side filter for search + status
        isVisible(row, status) {
            // Since filtering is done server-side now, we always return true to show all paginated items
            return true;
        },

        applyFilters() {
            let url = new URL(window.location.href);
            if (this.searchQuery) {
                url.searchParams.set('search', this.searchQuery);
            } else {
                url.searchParams.delete('search');
            }
            if (this.statusFilter) {
                url.searchParams.set('status', this.statusFilter);
            } else {
                url.searchParams.delete('status');
            }
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        },

        openDetail(id) {
            const apps = @json($appData);
            this.selectedApp = apps.find(a => a.id === id) || null;
            // Update modal status dynamically if changed in local statuses object
            if (this.selectedApp) {
                this.selectedApp.status = this.statuses[id];
                this.detailModalOpen = true;
                this.$nextTick(() => {
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                });
            }
        },

        async updateStatus(appId, newStatus) {
            const oldStatus = this.statuses[appId];
            if (oldStatus === newStatus) return;

            const isRequalification = (oldStatus === 'Rejected' && newStatus === 'Qualified');

            // Optimistically update local states
            this.statuses[appId] = newStatus;
            
            const oldKey = oldStatus.toLowerCase();
            const newKey = newStatus.toLowerCase();
            if (this.stats[oldKey] !== undefined) this.stats[oldKey]--;
            if (this.stats[newKey] !== undefined) this.stats[newKey]++;

            if (this.selectedApp && this.selectedApp.id === appId) {
                this.selectedApp.status = newStatus;
            }

            try {
                const response = await fetch(`/admin/applications/${appId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    throw new Error(errorData?.message || 'Failed to update status');
                }

                // Track requalification on success
                if (isRequalification) {
                    this.requalifiedCounts[appId] = (this.requalifiedCounts[appId] || 0) + 1;
                }

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Status updated successfully'
                });

            } catch (error) {
                console.error(error);
                // Revert state if request fails
                this.statuses[appId] = oldStatus;
                if (this.stats[oldKey] !== undefined) this.stats[oldKey]++;
                if (this.stats[newKey] !== undefined) this.stats[newKey]--;
                
                if (this.selectedApp && this.selectedApp.id === appId) {
                    this.selectedApp.status = oldStatus;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: error.message || 'Failed to update application status.',
                    confirmButtonColor: '#0F1D33'
                });
            }
        },

        getStatusBgColor(status) {
            if (status === 'Pending') return 'bg-amber-100 text-amber-700 border-amber-200';
            if (status === 'Reviewed') return 'bg-blue-100 text-blue-700 border-blue-200';
            if (status === 'Qualified') return 'bg-green-100 text-green-700 border-green-200';
            if (status === 'Rejected') return 'bg-red-100 text-red-700 border-red-200';
            return 'bg-slate-100 text-slate-600 border-slate-200';
        }
    }
}
</script>
@endsection
