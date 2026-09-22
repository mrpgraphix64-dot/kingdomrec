@extends('layouts.admin')

@section('title', 'Job Posts | Kingdom Admin')

@section('content')
<div x-data="jobPostManager()" @keydown.escape.window="modalOpen = false; detailsModalOpen = false" class="h-full flex flex-col space-y-3">
    <div class="dashboard-header flex flex-col sm:flex-row sm:items-center justify-between gap-2 shrink-0">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Job Posts</h1>
            <p class="text-slate-500 text-xs">Manage job openings & requirements</p>
        </div>
        <div class="flex gap-3">
             <!-- Status Filter -->
             <div class="flex items-center bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                 <button @click="statusFilter = ''" 
                         class="px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-wide transition-all"
                         :class="statusFilter === '' ? 'bg-kingdom-navy text-white' : 'text-slate-500 hover:bg-slate-50'">All</button>
                 <button @click="statusFilter = 'active'" 
                         class="px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-wide transition-all border-l border-slate-200"
                         :class="statusFilter === 'active' ? 'bg-emerald-500 text-white' : 'text-slate-500 hover:bg-slate-50'">Active</button>
                 <button @click="statusFilter = 'expired'" 
                         class="px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-wide transition-all border-l border-slate-200"
                         :class="statusFilter === 'expired' ? 'bg-amber-500 text-white' : 'text-slate-500 hover:bg-slate-50'">Expired</button>
             </div>

            <a href="{{ route('admin.job-category') }}" wire:navigate class="flex items-center gap-1.5 bg-white border border-slate-200 hover:border-kingdom-gold text-kingdom-navy px-4 py-2 rounded-xl font-bold text-xs shadow-sm hover:shadow-md transition-all">
                <i data-lucide="tags" class="w-3.5 h-3.5 text-kingdom-gold"></i> Manage Categories
            </a>

            <button @click="openCreateModal()" class="flex items-center gap-1.5 bg-[#0F1D33] hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold text-xs transition-all shadow-sm">
                <i data-lucide="file-plus" class="w-3.5 h-3.5"></i> Create Job
            </button>
        </div>
    </div>

    <!-- Job List View (Table) — sizes to its content; only caps height (with its own scroll) once there's enough rows to need it -->
    <div class="anim-fade-in-up flex flex-col bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-show="view === 'list'">

        <!-- Combined Table — hidden entirely (not just scrolled past) when there's nothing to show, so the empty state below is never off-screen inside the wide scroller -->
        <div class="overflow-auto w-full max-h-[calc(100vh-260px)] admin-table-container" x-show="Object.keys(jobStatuses).filter(id => isJobVisible(id)).length > 0">
            <table class="w-full min-w-[1000px] text-left border-collapse admin-table">
                <colgroup>
                    <col style="width: 70px;">
                    <col style="width: 22%;">
                    <col style="width: 18%;">
                    <col style="width: 18%;">
                    <col style="width: 14%;">
                    <col style="width: 14%;">
                    <col style="width: 14%;">
                </colgroup>
                <thead class="sticky top-0 z-20 bg-[#0f1f3d] shadow-sm">
                    <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-[11px] border-b border-slate-700">
                        <th class="py-2.5 px-3 text-[11px] text-center bg-[#0f1f3d]">ID</th>
                        <th class="py-2.5 px-3 text-[11px] bg-[#0f1f3d]">Job Role</th>
                        <th class="py-2.5 px-3 text-[11px] text-center bg-[#0f1f3d]">Company</th>
                        <th class="py-2.5 px-3 text-[11px] text-center bg-[#0f1f3d]">Details</th>
                        <th class="py-2.5 px-3 text-[11px] text-center bg-[#0f1f3d]">Status</th>
                        <th class="py-2.5 px-3 text-[11px] text-center bg-[#0f1f3d]">Dates</th>
                        <th class="py-2.5 px-3 text-[11px] text-center bg-[#0f1f3d]">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-transparent">
                    @foreach($jobs as $job)
                    @php
                        $initial = substr($job->sub_category, 0, 1);
                        $colors = ['bg-indigo-50 text-indigo-700 ring-indigo-200', 'bg-violet-50 text-violet-700 ring-violet-200', 'bg-purple-50 text-purple-700 ring-purple-200'];
                        $color = $colors[$loop->index % 3];
                        
                        $typeColor = match($job->type) {
                            'Full-time' => 'bg-green-50 text-green-600 border-green-200',
                            'Part-time' => 'bg-blue-50 text-blue-600 border-blue-200',
                            'Contract' => 'bg-yellow-50 text-yellow-600 border-yellow-200',
                            'Temporary' => 'bg-purple-50 text-purple-600 border-purple-200',
                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                        };
                         
                        $isInactive = $job->category && $job->category->status === 'Archived';
                    @endphp
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors {{ $isInactive ? 'opacity-60 grayscale-[0.8]' : '' }}"
                        x-show="isJobVisible({{ $job->id }})"
                        @click="openJobDetails({{ json_encode($job) }})"
                        x-cloak>
                        <!-- Sr. No. -->
                        <td class="py-4 px-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono text-[10px] font-bold border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-colors shadow-sm">{{ $loop->iteration }}</span>
                        </td>
                        <!-- Job Role -->
                        <td class="py-4 px-4 text-sm">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 shrink-0 rounded-full flex items-center justify-center font-bold text-xs ring-2 ring-offset-2 ring-white shadow-md transition-transform duration-300 group-hover:scale-110 {{ $color }}">
                                    <span>{{ $initial }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-slate-800 text-[13px] mb-0.5 group-hover:text-indigo-600 transition-colors truncate">{{ $job->sub_category ?: 'General' }}</p>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                         <span class="text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-bold border border-slate-200">
                                            @if($job->category)
                                                <span class="{{ $isInactive ? 'line-through decoration-slate-400' : '' }}">
                                                    {{ $job->dept }}
                                                </span>
                                                @if($isInactive)
                                                    <span class="text-orange-500 ml-1">(Archived)</span>
                                                @endif
                                            @else
                                                <span class="text-red-500 flex items-center gap-1">
                                                    <i data-lucide="alert-circle" class="w-2.5 h-2.5"></i> Deleted
                                                </span>
                                            @endif
                                        </span>
                                        @if($job->sub_category)
                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ $job->sub_category }}</span>
                                        @endif
                                        @if($job->staffQuotation)
                                            <span class="inline-flex items-center gap-1 text-[9px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded font-bold border border-amber-200" title="Created to cover a shortfall on this booking">
                                                <i data-lucide="megaphone" class="w-2.5 h-2.5"></i>
                                                {{ $job->staffQuotation->eventBooking->event_name ?? 'Booking shortfall' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <!-- Company -->
                        <td class="py-4 px-4 text-sm text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-[13px] font-bold text-slate-700 group-hover:text-indigo-600 transition-colors truncate">{{ $job->company_name ?? 'Kingdom Recruitments' }}</span>
                                <span class="text-[10px] text-slate-400 truncate max-w-[150px] font-medium">{{ $job->company_email ?? 'Kingdom Recruitments Team' }}</span>
                            </div>
                        </td>
                        <!-- Details -->
                        <td class="py-4 px-4 text-sm text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-flex max-w-max items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $typeColor }} border shadow-sm">{{ $job->type }}</span>
                                <div class="flex flex-wrap items-center gap-2 text-[10px] text-slate-500 font-medium mt-0.5">
                                    <span class="flex items-center gap-1" title="Salary"><i data-lucide="pound-sterling" class="w-3 h-3 text-slate-400"></i> {{ $job->salary }}</span>
                                    <span class="flex items-center gap-1" title="Location"><i data-lucide="map-pin" class="w-3 h-3 text-slate-400"></i> <span class="truncate max-w-[80px]">{{ $job->location }}</span></span>
                                </div>
                            </div>
                        </td>
                        <!-- Status Toggle -->
                        <td class="py-4 px-4 text-center" @click.stop>
                            <div class="flex flex-col items-center gap-1">
                                <button @click="toggleJobStatus({{ $job->id }})"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                                        :class="jobStatuses[{{ $job->id }}] === 'Active' ? 'bg-emerald-500' : (jobStatuses[{{ $job->id }}] === 'Expired' ? 'bg-amber-400' : 'bg-slate-350')"
                                        role="switch" :aria-checked="jobStatuses[{{ $job->id }}] === 'Active'"
                                        title="Toggle Status">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                                          :class="jobStatuses[{{ $job->id }}] === 'Active' ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                                <span class="text-[9px] font-bold uppercase tracking-wider"
                                      :class="jobStatuses[{{ $job->id }}] === 'Active' ? 'text-emerald-600' : (jobStatuses[{{ $job->id }}] === 'Expired' ? 'text-amber-500' : 'text-slate-500')"
                                      x-text="jobStatuses[{{ $job->id }}]"></span>
                            </div>
                        </td>
                         <!-- Dates -->
                        <td class="py-4 px-4 text-sm text-center">
                             <div class="space-y-1 inline-flex flex-col items-center">
                                <div class="flex items-center gap-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-wider" title="Date Posted">
                                    <i data-lucide="clock" class="w-3 h-3 text-slate-300"></i>
                                    <span>{{ $job->created_at->format('M d') }}</span>
                                </div>
                                 @if($job->deadline)
                                    <div class="flex items-center gap-1.5 text-[9px] font-bold text-kingdom-red bg-red-55 px-1.5 py-0.5 rounded w-fit border border-red-100" title="Deadline">
                                        <i data-lucide="calendar-clock" class="w-3 h-3"></i>
                                        <span>Ends: {{ \Carbon\Carbon::parse($job->deadline)->format('d M') }}</span>
                                    </div>
                                @endif
                             </div>
                        </td>
                        <!-- Actions -->
                        <td class="py-4 px-4 text-center" @click.stop>
                             <div class="flex items-center justify-center gap-1.5">
                                <button @click="openJobDetails({{ json_encode($job) }})" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-650 transition-colors" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button @click="openEditModal({{ json_encode($job) }})" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-650 transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.job-post.destroy', $job->id) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { title: 'Delete Job Post', message: 'Are you sure you want to delete this job post? This action cannot be undone.', onConfirm: () => submitDeleteForm('{{ route('admin.job-post.destroy', $job->id) }}') })">
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

        <!-- Empty State — a real full-width block, not a colspan cell inside the wide scrolling table
             (a centred colspan cell in a min-w-[1000px] table sits off-screen on narrow viewports) -->
        <div x-show="Object.keys(jobStatuses).filter(id => isJobVisible(id)).length === 0" x-cloak class="px-4 py-16 text-center text-slate-400 bg-white">
            <div class="flex flex-col items-center justify-center gap-2">
                <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 shadow-sm mb-1">
                    <i data-lucide="briefcase" class="w-7 h-7 text-slate-350"></i>
                </div>
                <span class="text-sm font-bold text-kingdom-navy" x-text="statusFilter ? 'No ' + statusFilter + ' job posts' : 'No job posts yet'"></span>
                <p class="text-xs text-slate-400 max-w-xs" x-text="statusFilter ? 'Try a different status, or clear the filter.' : 'Create your first job post to start attracting applicants.'"></p>
                <template x-if="!statusFilter">
                    <button @click="openCreateModal()" class="mt-3 flex items-center gap-1.5 bg-[#0F1D33] hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold text-xs transition-all shadow-sm">
                        <i data-lucide="file-plus" class="w-3.5 h-3.5"></i> Create Job
                    </button>
                </template>
                <template x-if="statusFilter">
                    <button @click="statusFilter = ''" class="mt-3 text-xs font-bold text-kingdom-gold hover:underline">Clear filter</button>
                </template>
            </div>
        </div>
        <!-- Pagination Links -->
        <div class="p-4 border-t border-slate-100 bg-white shrink-0">
            {{ $jobs->links() }}
        </div>
    </div>

    <!-- Job Grid View -->
    <div x-show="view === 'grid'" x-cloak class="anim-fade-in-up grid gap-6 flex-1 overflow-y-auto min-h-0 pb-4">
        @foreach($jobs as $job)
        @php
            $colors = ['bg-purple-100 text-purple-700', 'bg-blue-100 text-blue-700', 'bg-orange-100 text-orange-700'];
            $color = $colors[$loop->index % 3];
            $initial = substr($job->sub_category, 0, 1);
        @endphp
        <div class="bg-white p-6 rounded-3xl shadow-card hover:shadow-2xl hover:-translate-y-1 transition-all border border-slate-100 flex flex-col md:flex-row md:items-center gap-6 group relative overflow-hidden"
             x-show="isJobVisible({{ $job->id }})"
             x-cloak>
             <!-- Decor -->
             <div class="absolute -right-12 -top-12 w-32 h-32 bg-gradient-to-br from-slate-50 to-transparent rounded-full opacity-50 pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>

            <div class="h-20 w-20 rounded-2xl flex items-center justify-center shrink-0 {{ $color }} ring-4 ring-slate-50 shadow-sm group-hover:scale-105 group-hover:rotate-3 transition-transform duration-300 relative z-10">
                <span class="font-bold text-2xl shadow-sm">{{ $initial }}</span>
            </div>
            
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h3 class="text-lg font-bold text-kingdom-navy group-hover:text-kingdom-gold transition-colors">{{ $job->sub_category ?: 'General' }}</h3>
                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600">{{ $job->dept }}</span>
                     @php
                        $typeColor = match($job->type) {
                            'Full-time' => 'bg-green-50 text-green-600',
                            'Part-time' => 'bg-blue-50 text-blue-600',
                            'Contract' => 'bg-yellow-50 text-yellow-600',
                            'Temporary' => 'bg-purple-50 text-purple-600',
                            default => 'bg-slate-50 text-slate-600',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded text-xs font-bold {{ $typeColor }}">{{ $job->type }}</span>
                    <!-- Dynamic Status Badge/Toggle Switch in Grid View -->
                    <div class="flex items-center gap-2 border border-slate-100 bg-slate-50 px-2 py-1 rounded-xl shadow-sm">
                        <button @click="toggleJobStatus({{ $job->id }})"
                                class="relative inline-flex h-4 w-7 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                                :class="jobStatuses[{{ $job->id }}] === 'Active' ? 'bg-emerald-500' : (jobStatuses[{{ $job->id }}] === 'Expired' ? 'bg-amber-400' : 'bg-slate-400')"
                                role="switch" :aria-checked="jobStatuses[{{ $job->id }}] === 'Active'"
                                title="Toggle Status">
                            <span class="pointer-events-none inline-block h-3 w-3 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                                  :class="jobStatuses[{{ $job->id }}] === 'Active' ? 'translate-x-3' : 'translate-x-0'"></span>
                        </button>
                        <span class="text-[10px] font-bold uppercase tracking-wider"
                              :class="jobStatuses[{{ $job->id }}] === 'Active' ? 'text-emerald-600' : (jobStatuses[{{ $job->id }}] === 'Expired' ? 'text-amber-500' : 'text-slate-500')"
                              x-text="jobStatuses[{{ $job->id }}]"></span>
                    </div>
                </div>
                <p class="text-kingdom-gold font-bold text-sm mb-2">{{ $job->company_name ?? 'Kingdom Recruitments' }}</p>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500 font-medium">
                    <span class="flex items-center gap-1.5"><i data-lucide="pound-sterling" class="w-3.5 h-3.5 text-slate-400"></i> {{ $job->salary }}</span>
                    <span class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i> {{ $job->location }}</span>
                    <span class="flex items-center gap-1.5"><i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i> {{ $job->created_at->diffForHumans() }}</span>

                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                <button 
                    @click="openJobDetails({{ json_encode($job) }})"
                    class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors border border-slate-200">
                  View Details
                </button>
                <button @click="openEditModal({{ json_encode($job) }})" class="px-4 py-2 rounded-xl text-sm font-bold bg-kingdom-navy text-white hover:bg-slate-800 transition-colors shadow-lg shadow-kingdom-navy/20">Edit Role</button>
                <form action="{{ route('admin.job-post.destroy', $job->id) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { title: 'Delete Job Post', message: 'Are you sure you want to delete this job post? This action cannot be undone.', onConfirm: () => submitDeleteForm('{{ route('admin.job-post.destroy', $job->id) }}') })">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
        <!-- Empty State Grid -->
        <div x-show="Object.keys(jobStatuses).filter(id => isJobVisible(id)).length === 0" x-cloak class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex flex-col items-center justify-center gap-2">
                <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 shadow-sm mb-1">
                    <i data-lucide="briefcase" class="w-7 h-7 text-slate-350"></i>
                </div>
                <span class="text-sm font-bold text-kingdom-navy" x-text="statusFilter ? 'No ' + statusFilter + ' job posts' : 'No job posts yet'"></span>
                <p class="text-xs text-slate-400 max-w-xs" x-text="statusFilter ? 'Try a different status, or clear the filter.' : 'Create your first job post to start attracting applicants.'"></p>
                <template x-if="!statusFilter">
                    <button @click="openCreateModal()" class="mt-3 flex items-center gap-1.5 bg-[#0F1D33] hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold text-xs transition-all shadow-sm">
                        <i data-lucide="file-plus" class="w-3.5 h-3.5"></i> Create Job
                    </button>
                </template>
                <template x-if="statusFilter">
                    <button @click="statusFilter = ''" class="mt-3 text-xs font-bold text-kingdom-gold hover:underline">Clear filter</button>
                </template>
            </div>
        </div>
    </div>

    <!-- Create/Edit Job Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="modalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            @click="modalOpen = false"></div>
        
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="modalOpen"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-2xl transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                
                <div class="flex items-center justify-between px-6 py-3 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                    <h3 class="text-lg font-bold text-kingdom-navy" x-text="isEditing ? 'Edit Job Post' : 'Create New Job'"></h3>
                    <button @click="modalOpen = false" class="p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="px-6 pb-6 pt-2">
                    <form :action="formAction" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        <div x-html="methodField" class="hidden"></div>
                        <input type="hidden" name="staff_quotation_id" x-model="formData.staff_quotation_id">

                         @if($errors->any())
                             <div class="col-span-1 md:col-span-2 bg-red-50 text-red-600 p-4 rounded-xl border border-red-100 text-sm font-bold mb-2">
                                 <ul class="list-disc pl-5 space-y-1">
                                     @foreach($errors->all() as $error)
                                         <li>{{ $error }}</li>
                                     @endforeach
                                 </ul>
                             </div>
                         @endif

                        <!-- Job Role / Sub Category will be used instead of title -->

                        <div class="space-y-1.5">
                             <label class="block text-sm font-bold text-kingdom-navy pl-1">Job Category <span class="text-red-500">*</span></label>
                             <div class="relative group" x-data="{ open: false, newCategory: '' }" @click.outside="open = false">
                                <input type="hidden" name="dept" x-model="formData.dept">
                                 <button type="button" @click="open = !open"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy text-left flex items-center justify-between transition-all outline-none">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="layers" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <span x-text="formData.dept || 'Select Category'" :class="formData.dept ? 'text-kingdom-navy' : 'text-slate-400'"></span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                 </button>
                                
                                <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5 max-h-60 overflow-y-auto">
                                    @foreach($categories as $cat)
                                    <button type="button" @click="formData.dept = '{{ $cat->name }}'; formData.sub_category = ''; open = false" 
                                        class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-50 text-slate-700 hover:text-kingdom-navy transition-colors flex items-center justify-between">
                                        {{ $cat->name }}
                                        <i x-show="formData.dept === '{{ $cat->name }}'" data-lucide="check" class="w-4 h-4 text-kingdom-gold"></i>
                                    </button>
                                    @endforeach
                                    <div class="p-2 border-t border-slate-100 bg-slate-50">
                                        <div class="relative flex items-center">
                                            <input type="text" x-model="newCategory" @keydown.enter.prevent="if(newCategory.trim()) { formData.dept = newCategory.trim(); formData.sub_category = ''; newCategory = ''; open = false; }" placeholder="Type to add new category..." class="w-full pl-3 pr-8 py-2 text-sm font-medium rounded-lg border border-slate-200 focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold outline-none bg-white">
                                            <button type="button" @click="if(newCategory.trim()) { formData.dept = newCategory.trim(); formData.sub_category = ''; newCategory = ''; open = false; }" class="absolute right-1 p-1 text-slate-400 hover:text-kingdom-gold" title="Add"><i data-lucide="plus" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                         <div class="space-y-1.5">
                             <label class="block text-sm font-bold text-kingdom-navy pl-1">Sub Category <span class="text-red-500">*</span></label>
                             <div class="relative group" x-data="{ open: false, newSubCategory: '' }" @click.outside="open = false">
                                <input type="hidden" name="sub_category" x-model="formData.sub_category">
                                 <button type="button" @click="open = !open"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy text-left flex items-center justify-between transition-all outline-none"
                                    :class="!formData.dept ? 'opacity-50 cursor-not-allowed' : ''"
                                    :disabled="!formData.dept">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="git-branch" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <span x-text="formData.sub_category || 'Select Sub Category'" :class="formData.sub_category ? 'text-kingdom-navy' : 'text-slate-400'"></span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                 </button>
                                
                                <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5 max-h-60 overflow-y-auto">
                                    <template x-for="sub in filteredSubCategories" :key="sub.id">
                                        <button type="button" @click="formData.sub_category = sub.name; open = false" 
                                            class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-50 text-slate-700 hover:text-kingdom-navy transition-colors flex items-center justify-between">
                                            <span x-text="sub.name"></span>
                                            <i x-show="formData.sub_category === sub.name" data-lucide="check" class="w-4 h-4 text-kingdom-gold"></i>
                                        </button>
                                    </template>
                                    <div x-show="filteredSubCategories.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center">
                                        No sub-categories found
                                    </div>
                                    <div class="p-2 border-t border-slate-100 bg-slate-50">
                                        <div class="relative flex items-center">
                                            <input type="text" x-model="newSubCategory" @keydown.enter.prevent="if(newSubCategory.trim()) { formData.sub_category = newSubCategory.trim(); newSubCategory = ''; open = false; }" placeholder="Type to add new sub category..." class="w-full pl-3 pr-8 py-2 text-sm font-medium rounded-lg border border-slate-200 focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold outline-none bg-white">
                                            <button type="button" @click="if(newSubCategory.trim()) { formData.sub_category = newSubCategory.trim(); newSubCategory = ''; open = false; }" class="absolute right-1 p-1 text-slate-400 hover:text-kingdom-gold" title="Add"><i data-lucide="plus" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                             <label class="block text-sm font-bold text-kingdom-navy pl-1">Job Type <span class="text-red-500">*</span></label>
                             <div class="relative group" x-data="{ open: false }" @click.outside="open = false">
                                <input type="hidden" name="type" x-model="formData.type">
                                 <button type="button" @click="open = !open"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy text-left flex items-center justify-between transition-all outline-none">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="clock" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <span x-text="formData.type" class="text-kingdom-navy"></span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                 </button>
                                
                                <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5">
                                    @foreach(['Contract', 'Full-time', 'Part-time', 'Temporary'] as $type)
                                    <button type="button" @click="formData.type = '{{ $type }}'; open = false" 
                                        class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-50 text-slate-700 hover:text-kingdom-navy transition-colors flex items-center justify-between">
                                        {{ $type }}
                                        <i x-show="formData.type === '{{ $type }}'" data-lucide="check" class="w-4 h-4 text-kingdom-gold"></i>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Location <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                 <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="map-pin" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                </div>
                                <input type="text" name="location" x-model="formData.location" class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" placeholder="Location/ Address" required>
                            </div>
                        </div>
                        
                         <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Salary <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Type Dropdown -->
                                <div class="relative group" x-data="{ open: false }" @click.outside="open = false">
                                    <input type="hidden" name="salary_type" x-model="formData.salary_type">
                                    <button type="button" @click="open = !open"
                                        class="w-full pl-4 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy text-left flex items-center justify-between transition-all outline-none">
                                        <span x-text="formData.salary_type || 'Frequency'"></span>
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                    </button>
                                    <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5">
                                        <template x-for="type in ['Hourly', 'Daily', 'Weekly', 'Monthly', 'Yearly', 'Negotiable']">
                                            <button type="button" @click="formData.salary_type = type; open = false"
                                                class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-50 text-slate-700 hover:text-kingdom-navy transition-colors">
                                                <span x-text="type"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Amount Input -->
                                <div class="relative group" x-show="formData.salary_type !== 'Negotiable'">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold">£</span>
                                    </div>
                                    <input type="number" step="0.01" name="salary_amount" x-model="formData.salary_amount" class="w-full pl-8 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                         <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Application Deadline</label>
                            <div class="relative group">
                                 <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="calendar" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                </div>
                                <input type="date" name="deadline" x-model="formData.deadline" class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy transition-all outline-none placeholder-slate-400" placeholder="Select Date">
                            </div>
                        </div>



                        <div class="col-span-1 md:col-span-2 space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Description</label>
                            <textarea name="description" x-model="formData.description" rows="3" class="w-full custom-scrollbar px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" placeholder="Job details..."></textarea>
                        </div>

                        <div class="col-span-1 md:col-span-2 flex gap-4 mt-4 pt-4 border-t border-slate-100">
                            <button type="button" @click="modalOpen = false" class="flex-1 px-4 py-3.5 rounded-xl border-2 border-slate-100 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-800 transition-all">Cancel</button>
                            <button type="submit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 flex items-center justify-center gap-2">
                                 <span x-text="isEditing ? 'Update Job Post' : 'Create Job Post'"></span>
                                 <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Details Modal (Standardized) -->
    <div x-show="detailsModalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        
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

         <div class="flex min-h-full items-center justify-center p-4">
             <div x-show="detailsModalOpen"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden transition-all">
                
                  <div class="flex items-start gap-4 p-6 border-b border-slate-100">
                   <div x-bind:class="'h-16 w-16 rounded-2xl flex items-center justify-center shrink-0 ' + (selectedJob?.color || 'bg-slate-100')">
                        <span class="font-bold text-xl" x-text="selectedJob?.sub_category?.charAt(0)"></span>
                   </div>
                   <div class="flex-1">
                      <h3 class="text-xl font-bold text-kingdom-navy" x-text="selectedJob?.sub_category || 'General'"></h3>
                      <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm font-bold text-slate-500" x-text="selectedJob?.dept"></span>
                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span class="text-sm text-slate-400" x-text="selectedJob?.posted"></span>
                      </div>
                   </div>
                   <button @click="detailsModalOpen = false" class="p-2 rounded-full text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
    
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                       <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                          <label class="flex items-center gap-2 text-slate-500 mb-1 text-xs font-bold uppercase tracking-wide"><i data-lucide="building-2" class="w-4 h-4"></i> Company</label>
                          <p class="font-bold text-kingdom-navy" x-text="selectedJob?.company_name || 'Kingdom Recruitments'"></p>
                       </div>
                       <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                          <label class="flex items-center gap-2 text-slate-500 mb-1 text-xs font-bold uppercase tracking-wide"><i data-lucide="map-pin" class="w-4 h-4"></i> Location</label>
                          <p class="font-bold text-kingdom-navy" x-text="selectedJob?.location"></p>
                       </div>
                       <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                           <label class="flex items-center gap-2 text-slate-500 mb-1 text-xs font-bold uppercase tracking-wide"><i data-lucide="pound-sterling" class="w-4 h-4"></i> Salary</label>
                          <p class="font-bold text-kingdom-navy" x-text="selectedJob?.salary"></p>
                       </div>
                       <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                          <label class="flex items-center gap-2 text-slate-500 mb-1 text-xs font-bold uppercase tracking-wide"><i data-lucide="briefcase" class="w-4 h-4"></i> Job Type</label>
                          <p class="font-bold text-kingdom-navy" x-text="selectedJob?.type"></p>
                       </div>
                       <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 col-span-2">
                          <label class="flex items-center gap-2 text-slate-500 mb-1 text-xs font-bold uppercase tracking-wide"><i data-lucide="calendar" class="w-4 h-4"></i> Application Deadline</label>
                          <p class="font-bold text-kingdom-navy" x-text="selectedJob?.deadline ? new Date(selectedJob.deadline).toLocaleDateString() : 'Open Indefinitely'"></p>
                       </div>
                    </div>
    
                    <div>
                       <h4 class="text-sm font-bold text-kingdom-navy uppercase tracking-wide mb-2">Description</h4>
                       <p class="text-slate-600 leading-relaxed text-sm font-medium" x-text="selectedJob?.description || 'No description provided'"></p>
                    </div>
    
                    <div class="flex gap-3 pt-4">
                       <button @click="detailsModalOpen = false" class="flex-1 py-3.5 bg-kingdom-navy text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-colors shadow-lg shadow-kingdom-navy/20">
                          Close
                       </button>
                    </div>
                </div>
             </div>
         </div>
    </div>


    
    <script>
        window.jobPostManager = function jobPostManager() {
            return {
                view: 'list',
                modalOpen: {{ $errors->any() || request('action') === 'new' ? 'true' : 'false' }},
                detailsModalOpen: false,
                isEditing: false,
                statusFilter: '',
                isJobVisible(jobId) {
                    const status = this.jobStatuses[jobId];
                    if (this.statusFilter === '') {
                        return true;
                    }
                    if (this.statusFilter === 'active') {
                        return status === 'Active';
                    }
                    if (this.statusFilter === 'expired') {
                        return status === 'Expired' || status === 'Archived';
                    }
                    return true;
                },
                init() {
                    this.$watch('modalOpen', val => { if(!val && !this.detailsModalOpen) document.body.style.overflow = ''; else if(val) document.body.style.overflow = 'hidden'; });
                    this.$watch('detailsModalOpen', val => { if(!val && !this.modalOpen) document.body.style.overflow = ''; else if(val) document.body.style.overflow = 'hidden'; });
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }); });
                    this.$watch('statusFilter', () => this.$nextTick(() => { if (window.lucide) window.lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }); }));

                    // Arrived via "Post Job" from a short-staffed shift — prefill and open the create form
                    const params = new URLSearchParams(window.location.search);
                    const shiftId = params.get('post_for_shift');
                    if (shiftId) {
                        this.openCreateModal();
                        this.formData.staff_quotation_id = shiftId;
                        this.formData.dept = params.get('dept') || '';
                        this.formData.sub_category = params.get('sub_category') || '';
                        this.formData.location = params.get('location') || '';
                        this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                    }


                },
                selectedJob: null,
                allSubCategories: @json($subCategories),
                get filteredSubCategories() {
                    return this.allSubCategories.filter(sub => sub.parent_category === this.formData.dept);
                },
                // If there are validation errors, prepopulate with old input
                formData: {
                    id: null,
                    dept: {!! json_encode(old('dept', '')) !!},
                    sub_category: {!! json_encode(old('sub_category', '')) !!},
                    type: {!! json_encode(old('type', 'Contract')) !!},
                    company_name: {!! json_encode(old('company_name', '')) !!},
                    company_email: {!! json_encode(old('company_email', '')) !!},
                    location: {!! json_encode(old('location', '')) !!},
                    salary_type: {!! json_encode(old('salary_type', 'Hourly')) !!},
                    salary_amount: {!! json_encode(old('salary_amount', '')) !!},
                    deadline: {!! json_encode(old('deadline', '')) !!},
                    description: {!! json_encode(old('description', '')) !!},
                    staff_quotation_id: {!! json_encode(old('staff_quotation_id', '')) !!}
                },
                formAction: "{{ route('admin.job-post.store') }}",
                methodField: '',
                jobStatuses: {
                    @foreach($jobs as $job)
                        '{{ $job->id }}': '{{ $job->calculated_status }}',
                    @endforeach
                },
                async toggleJobStatus(jobId) {
                    const orig = this.jobStatuses[jobId];
                    const newStatus = orig === 'Active' ? 'Expired' : 'Active';
                    this.jobStatuses[jobId] = newStatus;

                    try {
                        const response = await fetch(`/admin/job-post/${jobId}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ status: newStatus })
                        });
                        const data = await response.json();
                        if (response.ok && data.calculated_status) {
                            this.jobStatuses[jobId] = data.calculated_status;
                        } else {
                            this.jobStatuses[jobId] = orig;
                        }
                    } catch {
                        this.jobStatuses[jobId] = orig;
                    }
                },

                openCreateModal() {
                    this.isEditing = false;
                    this.formData = {
                         id: null, dept: '', sub_category: '', type: 'Contract',
                         company_name: '', company_email: '',
                         location: '', salary_type: 'Hourly', salary_amount: '', deadline: '', description: '',
                         staff_quotation_id: ''
                    };
                    this.formAction = "{{ route('admin.job-post.store') }}";
                    this.methodField = '';
                    this.modalOpen = true;
                },

                openEditModal(job) {
                    this.isEditing = true;
                    this.formData = {
                        id: job.id,
                        dept: job.dept,
                        sub_category: job.sub_category || '',
                        type: job.type,
                        company_name: job.company_name,
                        company_email: job.company_email,
                        location: job.location,
                        salary: job.salary,
                        salary_type: '',
                        salary_amount: '',
                        deadline: job.deadline ? job.deadline.substring(0, 10) : '',
                        description: job.description || '',
                        staff_quotation_id: job.staff_quotation_id || ''
                    };

                    // Parse existing salary
                    let s = job.salary || '';
                    if (s.toLowerCase().includes('negotiable')) {
                        this.formData.salary_type = 'Negotiable';
                        this.formData.salary_amount = '';
                    } else {
                        // Extract amount (first number found)
                        let amountMatch = s.match(/[\d\.]+/);
                        this.formData.salary_amount = amountMatch ? amountMatch[0] : '';
                        
                        // Extract type
                        if (s.toLowerCase().includes('year')) this.formData.salary_type = 'Yearly';
                        else if (s.toLowerCase().includes('month')) this.formData.salary_type = 'Monthly';
                        else if (s.toLowerCase().includes('week')) this.formData.salary_type = 'Weekly';
                        else this.formData.salary_type = 'Hourly';
                    }

                    this.formAction = `/admin/job-post/${job.id}`;
                    this.methodField = '<input type="hidden" name="_method" value="PUT">';
                    this.modalOpen = true;
                },
                
                openJobDetails(job) {
                    this.selectedJob = job;
                    // Mock poster time if not available
                    this.selectedJob.posted = 'Recently';
                    this.detailsModalOpen = true;
                }
            }
        }

    </script>

    @include('partials.categories-manager', [
        'allCategories' => $allCategories,
        'allSubCategories' => $allSubCategories,
        'parentCategories' => $parentCategories,
    ])
</div>
@endsection
