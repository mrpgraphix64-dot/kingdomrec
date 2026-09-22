@extends('layouts.admin')

@section('title', 'Operations Dashboard & Events | Kingdom Admin')

@section('content')

<div class="w-full flex flex-col space-y-4" x-data="eventManager()">
    
    <!-- Header -->
    <div class="dashboard-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-3.5 md:p-4 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden shrink-0">
        <div class="relative z-10">
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Operational Events Command Center</h1>
            <p class="text-slate-400 text-xs">Workforce management, staffing assignments, scheduling, and timesheets</p>
        </div>
        <div class="flex items-center gap-2.5 relative z-10 w-full sm:w-auto mt-2 sm:mt-0 font-sans">
            {{-- Search Bar — flexible at every width, capped at 256px, instead of a fixed
                 w-56/w-64. A fixed width here plus the "View Bookings" button next to it
                 overflowed both a 375px viewport (clipping the button) and a 768px tablet
                 viewport (overlapping the button), since neither element could shrink. --}}
            <div class="relative flex-1 min-w-0 max-w-64">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" x-model="searchQuery" placeholder="Search events..."
                    class="pl-9 pr-4 py-2 w-full bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all shadow-sm">
            </div>
            <!-- View Quotations Link -->
            <a href="{{ route('admin.staff-quotation') }}" 
               class="flex items-center gap-1.5 bg-slate-800 text-white rounded-xl px-3.5 py-2 text-xs font-bold hover:bg-slate-700 transition-all shadow-sm active:scale-95 shrink-0">
                <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-kingdom-gold"></i>
                <span>View Bookings</span>
            </a>
        </div>
    </div>

    <!-- Dashboard stats cards -->
    {{-- grid-cols-1 below sm: 2-up at 375px truncates "Active Events"/"Fully Staffed" into
         "ACTIVE EV..."/"FULLY STAF...". Same fix as Dashboard/Assignments KPI grids. --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 shrink-0">
        <!-- Active Events -->
        <div class="card-interactive bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-indigo-600 shadow-sm flex items-center justify-between group h-full min-h-[76px]">
            <div class="space-y-1 min-w-0 flex-1 pr-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate">Active Events</span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight block truncate">{{ $stats['active_events'] }}</span>
            </div>
            <div class="card-icon w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm shrink-0">
                <i data-lucide="calendar" class="w-5 h-5"></i>
            </div>
        </div>
 
        <!-- Events This Week -->
        <div class="card-interactive bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-sky-500 shadow-sm flex items-center justify-between group h-full min-h-[76px]">
            <div class="space-y-1 min-w-0 flex-1 pr-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate">This Week</span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight block truncate">{{ $stats['events_this_week'] }}</span>
            </div>
            <div class="card-icon w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 shadow-sm shrink-0">
                <i data-lucide="calendar-days" class="w-5 h-5"></i>
            </div>
        </div>
 
        <!-- Fully Staffed Events -->
        <div class="card-interactive bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-emerald-500 shadow-sm flex items-center justify-between group h-full min-h-[76px]">
            <div class="space-y-1 min-w-0 flex-1 pr-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate">Fully Staffed</span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight block truncate">{{ $stats['fully_staffed_events'] }}</span>
            </div>
            <div class="card-icon w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm shrink-0">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>
 
        <!-- Events Requiring Staff -->
        <div class="card-interactive bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-amber-500 shadow-sm flex items-center justify-between group h-full min-h-[76px]">
            <div class="space-y-1 min-w-0 flex-1 pr-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate">Need Staff</span>
                <span class="text-xl sm:text-2xl font-black text-amber-600 tracking-tight block truncate">{{ $stats['events_requiring_staff'] }}</span>
            </div>
            <div class="card-icon w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-sm shrink-0">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>
 
        <!-- Live Events Today -->
        <div class="card-interactive bg-white p-4 rounded-2xl border border-slate-200 border-l-4 border-l-rose-500 shadow-sm flex items-center justify-between group h-full min-h-[76px]">
            <div class="space-y-1 min-w-0 flex-1 pr-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate">Live Today</span>
                <span class="text-xl sm:text-2xl font-black text-rose-600 tracking-tight block truncate">{{ $stats['live_events_today'] }}</span>
            </div>
            <div class="card-icon w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 shadow-sm shrink-0">
                <i data-lucide="play" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Grid Container -->
    <div class="flex-1 min-h-0 lg:overflow-y-auto lg:custom-scrollbar">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-6">
            @forelse($eventBookings as $booking)
                @php
                    $fillPct = $booking->completion_percentage;
                    $priority = $booking->priority;
                    $readiness = $booking->readiness;
                    
                    // Style mappings
                    $priorityStyles = [
                        'Critical'  => 'bg-rose-50 text-rose-700 border-rose-200',
                        'Warning'   => 'bg-amber-50 text-amber-700 border-amber-200',
                        'Healthy'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'Completed' => 'bg-slate-50 text-slate-600 border-slate-200',
                    ];
                    
                    $readinessStyles = [
                        'Ready for Operations' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'Needs Staffing'       => 'bg-blue-100 text-blue-800 border-blue-200',
                        'Critical Shortage'    => 'bg-rose-100 text-rose-800 border-rose-200',
                    ];

                    $opStatusStyles = [
                        'Completed'            => 'bg-slate-100 text-slate-700 border-slate-300',
                        'Cancelled'            => 'bg-red-50 text-red-700 border-red-200',
                        'Live Event'           => 'bg-rose-50 text-rose-700 border-rose-200 animate-pulse',
                        'Fully Staffed'        => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'Staffing In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'Approved'             => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'Ready'                => 'bg-sky-50 text-sky-700 border-sky-200',
                    ];
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden"
                     x-show="matchesSearch({
                         booking_ref: '{{ addslashes($booking->booking_ref) }}',
                         event_name: '{{ addslashes($booking->event_name) }}',
                         client: '{{ addslashes($booking->client) }}',
                         venue: '{{ addslashes($booking->venue) }}',
                         operational_status: '{{ addslashes($booking->operational_status) }}'
                     })"
                     x-data="{ expanded: isEventExpanded({{ $booking->id }}) }"
                     x-init="$watch('expandedEvents', val => expanded = val.includes({{ $booking->id }}))">
                     
                     <!-- Card Body -->
                     <div class="p-6 flex-1 flex flex-col space-y-4">
                         
                         <!-- Title and Badges -->
                         <div class="flex justify-between items-start gap-4">
                             <div>
                                 <span class="inline-flex px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-mono font-bold border border-slate-200 mb-1.5 uppercase tracking-wide">
                                     #{{ $booking->booking_ref }}
                                 </span>
                                 <h2 class="text-base font-extrabold text-kingdom-navy leading-snug line-clamp-1" title="{{ $booking->event_name }}">
                                     {{ $booking->event_name }}
                                 </h2>
                             </div>
                             <div class="flex flex-col sm:flex-row gap-1.5 shrink-0 items-end sm:items-center">
                                 <!-- Operational Status Badge -->
                                 <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider border {{ $opStatusStyles[$booking->operational_status] ?? 'bg-indigo-50 text-indigo-700 border-indigo-200' }}">
                                     Status: {{ $booking->operational_status }}
                                 </span>
                                 <!-- Priority Badge -->
                                 <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider border {{ $priorityStyles[$priority] ?? $priorityStyles['Healthy'] }}">
                                     {{ $priority }}
                                 </span>
                                 <!-- Readiness Label -->
                                 <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider border {{ $readinessStyles[$readiness] ?? $readinessStyles['Needs Staffing'] }}">
                                     {{ $readiness }}
                                 </span>
                             </div>
                         </div>

                         {{-- Details Info Grid — stacked on mobile, side-by-side from sm: up.
                              Was a 2-up grid unconditionally, which halved the width available
                              to "Client"/"Venue" at 375px and truncated real names down to a
                              few letters ("QA Part..."). Using flex (not grid) here + min-w-0
                              on each row so `truncate` actually has a constrained box to clip
                              against instead of being allowed to overflow. --}}
                         <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 text-xs font-sans">
                             <div class="flex items-center gap-2 text-slate-500 min-w-0 sm:flex-1">
                                 <i data-lucide="building" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                 <span class="truncate" title="Client: {{ $booking->client }}"><strong class="text-slate-700">Client:</strong> {{ $booking->client }}</span>
                             </div>
                             {{-- flex-wrap: on a very narrow phone (320px), the "View Venue"
                                  button next to the venue name left almost no room for the
                                  name itself ("QA L..."); wrapping lets the button drop to its
                                  own line instead of squeezing the text. --}}
                             <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-slate-500 min-w-0 sm:flex-1">
                                 <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                 <span class="truncate max-w-full" title="Venue: {{ $booking->venue }}"><strong class="text-slate-700">Venue:</strong> {{ $booking->venue }}</span>
                                 @php
                                     $matchedVenue = $venues->first(function ($v) use ($booking) {
                                         return strtolower(trim($v->name)) === strtolower(trim($booking->venue));
                                     });
                                 @endphp
                                 @if($matchedVenue)
                                     <button @click.prevent="openVenueModal({{ json_encode($matchedVenue) }})" class="ml-1.5 px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-650 text-[10px] font-bold border border-indigo-100 transition-colors flex items-center gap-0.5 shadow-sm hover:scale-105 active:scale-95 shrink-0" title="View Venue Details">
                                         <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                         <span>View Venue</span>
                                     </button>
                                 @endif
                             </div>
                             <div class="flex items-center gap-2 text-slate-500 col-span-2">
                                 <i data-lucide="calendar-range" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                 <span>
                                     <strong class="text-slate-700">Date:</strong>
                                     @if($booking->start_date)
                                         {{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}
                                         @if($booking->end_date && $booking->end_date != $booking->start_date)
                                             — {{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}
                                         @endif
                                     @else
                                         TBD
                                     @endif
                                 </span>
                             </div>
                         </div>

                         <!-- Staffing Fulfillment Progress -->
                         <div class="space-y-1.5 pt-2 border-t border-slate-100">
                             <div class="flex justify-between items-center text-xs">
                                 <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Staffing Fulfillment</span>
                                 <span class="font-black {{ $fillPct >= 100 ? 'text-emerald-600' : ($fillPct >= 50 ? 'text-indigo-600' : 'text-rose-600') }}">
                                     {{ $booking->total_staff_assigned }} / {{ $booking->total_staff_required }} Staffed ({{ $booking->remaining_vacancies }} vacant)
                                 </span>
                             </div>
                             <!-- Progress bar wrapper -->
                             <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                 <div class="h-full rounded-full transition-all duration-500 {{ $fillPct >= 100 ? 'bg-emerald-500' : ($fillPct >= 50 ? 'bg-indigo-500' : 'bg-rose-500') }}"
                                      style="width: {{ $fillPct }}%"></div>
                             </div>
                         </div>

                     </div>

                     <!-- Expandable Accordion (Shift Breakdown) -->
                     <div x-show="expanded" x-collapse class="border-t border-slate-100 bg-slate-50/50 p-6 space-y-4" style="display: none;">
                         <div class="flex justify-between items-center">
                             <h4 class="text-xs uppercase font-extrabold text-slate-500 tracking-wider">Shift Breakdown</h4>
                             <span class="text-[10px] text-slate-400 font-bold bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                 {{ $booking->shifts->count() }} Role Groups
                             </span>
                         </div>
                         
                         <div class="space-y-3">
                             @foreach($booking->shifts as $shift)
                                 @php
                                     $shiftAssignedCount = $shift->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
                                     $shiftVacantCount = max(0, $shift->quantity - $shiftAssignedCount);
                                 @endphp
                                 <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
                                     <!-- Role name and status details -->
                                     <div class="flex justify-between items-start gap-4">
                                         <div>
                                             <span class="text-xs font-black text-slate-800 block">
                                                 {{ $shift->category }} · {{ $shift->sub_category }}
                                             </span>
                                             <span class="text-[10px] text-slate-400 font-bold mt-0.5 block">
                                                 Timing: {{ $shift->shift ?? 'TBD' }} ({{ $shift->shift_hours }} hours)
                                             </span>
                                         </div>
                                         <div class="text-right shrink-0">
                                             <span class="inline-block text-[11px] font-black px-2 py-1 rounded-lg {{ $shiftVacantCount == 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                                 {{ $shiftAssignedCount }} / {{ $shift->quantity }} Filled
                                             </span>
                                         </div>
                                     </div>

                                     <!-- Assigned Applicants Tag list -->
                                     <div class="pt-2.5 border-t border-slate-100">
                                         <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1.5">Assigned Workers</span>
                                         <div class="flex flex-wrap gap-1.5">
                                             @php
                                                 $assignedSlots = $shift->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0);
                                             @endphp
                                             @forelse($assignedSlots as $slot)
                                                 <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-slate-50 text-slate-600 px-2.5 py-0.5 rounded-full border border-slate-200/60 shadow-sm">
                                                     <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                     {{ $slot->applicant->name ?? 'Applicant' }}
                                                 </span>
                                             @empty
                                                 <span class="text-[10px] text-slate-400 font-medium italic">No applicants assigned to this shift yet</span>
                                             @endforelse
                                         </div>
                                     </div>

                                     <!-- Shortfall: not enough staff in the pool, recruit via a Job Post -->
                                     @if($shiftVacantCount > 0)
                                     <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-3">
                                         <div class="min-w-0">
                                             <span class="text-[10px] font-bold text-amber-700 flex items-center gap-1.5">
                                                 <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                                 {{ $shiftVacantCount }} still needed
                                             </span>
                                             @if($shift->jobPosts->isNotEmpty())
                                                 <span class="text-[9px] text-slate-400 font-medium block mt-0.5">
                                                     {{ $shift->jobPosts->count() }} job posting(s) already recruiting for this role
                                                 </span>
                                             @endif
                                         </div>
                                         <a href="{{ route('admin.job-post') }}?post_for_shift={{ $shift->id }}&dept={{ urlencode($shift->category) }}&sub_category={{ urlencode($shift->sub_category) }}&location={{ urlencode($shift->venue ?? '') }}"
                                            class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg font-bold text-[10px] transition-all active:scale-95">
                                             <i data-lucide="megaphone" class="w-3.5 h-3.5"></i> Post Job
                                         </a>
                                     </div>
                                     @endif
                                 </div>
                             @endforeach
                         </div>
                     </div>

                     <!-- Card Footer: Quick Actions & Toggle Button -->
                     <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-auto">
                          <!-- Details Toggle -->
                          <button @click="toggleEvent({{ $booking->id }})" 
                                  class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95">
                              <i data-lucide="list" class="w-3.5 h-3.5"></i>
                              <span x-text="expanded ? 'Hide Event' : 'View Event'"></span>
                              <i :class="expanded ? 'rotate-180' : ''" data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300"></i>
                          </button>
                          
                          <!-- Action Links Grid -->
                          <div class="grid grid-cols-3 gap-2">
                              <a href="{{ route('admin.assignments') }}?booking={{ urlencode($booking->booking_ref) }}" 
                                 class="flex items-center justify-center gap-1 px-3 py-2.5 bg-white hover:bg-indigo-50 text-indigo-600 border border-indigo-200 hover:border-indigo-300 rounded-xl font-bold text-[10px] transition-all text-center active:scale-95 shadow-sm"
                                 title="Manage assignments on the Staff Assignments board">
                                  <i data-lucide="user-cog" class="w-3.5 h-3.5"></i>
                                  <span>Manage Assignments</span>
                              </a>
                              <a href="{{ route('admin.time-shifting') }}?booking={{ urlencode($booking->booking_ref) }}" 
                                 class="flex items-center justify-center gap-1 px-3 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300 rounded-xl font-bold text-[10px] transition-all text-center active:scale-95 shadow-sm"
                                 title="Review and sign off timesheet shifts">
                                  <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                  <span>View Timesheets</span>
                              </a>
                              <a href="{{ route('admin.staff-quotation') }}?search={{ urlencode($booking->booking_ref) }}" 
                                 class="flex items-center justify-center gap-1 px-3 py-2.5 bg-white hover:bg-amber-50 text-amber-700 border border-amber-200 hover:border-amber-300 rounded-xl font-bold text-[10px] transition-all text-center active:scale-95 shadow-sm"
                                 title="View booking quotes and invoice billing">
                                  <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                  <span>Billing</span>
                              </a>
                          </div>
                      </div>

                </div>
            @empty
                <div class="col-span-full py-10 px-4 text-center bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div class="h-12 w-12 bg-slate-50 rounded-full flex items-center justify-center mb-3 border border-slate-200 shadow-sm">
                            <i data-lucide="calendar-x" class="w-6 h-6 text-slate-400"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">No Operational Events Found</h3>
                        <p class="text-slate-500 max-w-sm mx-auto mt-1 text-xs leading-relaxed">
                            Approved staff bookings will automatically appear here for staffing assignment and shift tracking.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('admin.staff-quotation') }}" class="px-4 py-2 bg-[#0F1D33] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-sm inline-flex items-center gap-1.5">
                                <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                                <span>View Staff Bookings</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($eventBookings->hasPages())
            <!-- Pagination Links -->
            <div class="mt-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm shrink-0">
                {{ $eventBookings->links() }}
            </div>
        @endif
    </div>


    <!-- Venue Details Modal -->
    <div x-show="venueModalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="venueModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center" @click="venueModalOpen = false">
            <div x-show="venueModalOpen"
                x-transition:enter="transition ease-out duration-350"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative w-full max-w-md transform rounded-2xl bg-white text-left shadow-2xl transition-all p-6 border border-slate-100">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="font-extrabold text-base text-kingdom-navy" x-text="selectedVenue.name"></h3>
                    <button @click="venueModalOpen = false" class="bg-slate-50 hover:bg-slate-100 p-1.5 rounded-full text-slate-450 transition-colors border border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="space-y-3.5 text-xs text-slate-600 font-sans">
                    <div class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded bg-indigo-50/50 flex items-center justify-center shrink-0 border border-indigo-100/50">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-indigo-650"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-slate-450 uppercase text-[9px] tracking-wide">Contact Number</span>
                            <span class="text-slate-800 font-bold" x-text="selectedVenue.contact_info || 'Not provided'"></span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded bg-indigo-50/50 flex items-center justify-center shrink-0 border border-indigo-100/50">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-indigo-650"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-slate-450 uppercase text-[9px] tracking-wide">Email Address</span>
                            <span class="text-slate-800 font-bold" x-text="selectedVenue.email || 'Not provided'"></span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded bg-indigo-50/50 flex items-center justify-center shrink-0 border border-indigo-100/50">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-indigo-650"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-slate-450 uppercase text-[9px] tracking-wide">Location</span>
                            <span class="text-slate-800 font-bold" x-text="[selectedVenue.address, selectedVenue.city, selectedVenue.postcode, selectedVenue.country].filter(Boolean).join(', ')"></span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5 border-t border-slate-100 pt-3.5">
                        <div class="w-7 h-7 rounded bg-indigo-50/50 flex items-center justify-center shrink-0 border border-indigo-100/50">
                            <i data-lucide="align-left" class="w-3.5 h-3.5 text-indigo-650"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block font-bold text-slate-450 uppercase text-[9px] tracking-wide mb-1">Description</span>
                            <p class="text-slate-750 leading-relaxed text-xs whitespace-pre-line" x-text="selectedVenue.description || 'No description provided'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
window.eventManager = function eventManager() {
    return {
        searchQuery: '',
        expandedEvents: [],
        venueModalOpen: false,
        selectedVenue: {},
        
        init() {
            const params = new URLSearchParams(window.location.search);
            const bookingParam = params.get('booking');
            const eventParam = params.get('event');

            if (bookingParam) {
                this.searchQuery = bookingParam;
                const refMap = @json($eventBookings->pluck('id', 'booking_ref'));
                const matchedKey = Object.keys(refMap).find(k => k.toLowerCase() === bookingParam.toLowerCase());
                if (matchedKey) {
                    const matchedId = refMap[matchedKey];
                    this.expandedEvents.push(matchedId);
                }
            } else if (eventParam) {
                this.searchQuery = eventParam;
                const nameMap = @json($eventBookings->pluck('id', 'event_name'));
                const matchedKey = Object.keys(nameMap).find(k => k.toLowerCase() === eventParam.toLowerCase());
                if (matchedKey) {
                    const matchedId = nameMap[matchedKey];
                    this.expandedEvents.push(matchedId);
                }
            }
        },
        
        openVenueModal(venue) {
            this.selectedVenue = venue;
            this.venueModalOpen = true;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },
        
        toggleEvent(id) {
            if (this.expandedEvents.includes(id)) {
                this.expandedEvents = this.expandedEvents.filter(x => x !== id);
            } else {
                this.expandedEvents.push(id);
            }
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },
        
        isEventExpanded(id) {
            return this.expandedEvents.includes(id);
        },

        matchesSearch(booking) {
            if (!this.searchQuery) return true;
            
            const query = this.searchQuery.toLowerCase();
            const refMatch = (booking.booking_ref || '').toLowerCase().includes(query);
            const nameMatch = (booking.event_name || '').toLowerCase().includes(query);
            const clientMatch = (booking.client || '').toLowerCase().includes(query);
            const venueMatch = (booking.venue || '').toLowerCase().includes(query);
            const statusMatch = (booking.operational_status || '').toLowerCase().includes(query);
            
            return refMatch || nameMatch || clientMatch || venueMatch || statusMatch;
        }
    }
}
</script>
@endsection
