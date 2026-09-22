@extends('layouts.admin')

@section('title', 'Staff Assignments | Kingdom Admin')

@section('content')
@php
    // Prepare assigned assignments for Kanban cards
    $assignmentData = $assignments->map(function($a) {
        $startTime = $a->start_time ?: ($a->staffQuotation->start_time ?? '09:00');
        $endTime = $a->end_time ?: ($a->staffQuotation->end_time ?? '17:00');
        return [
            'id' => $a->id,
            'status' => $a->status ?? 'Assigned',
            'applicant_id' => $a->applicant_id,
            'applicant_name' => $a->applicant->name ?? 'Unknown',
            'applicant_email' => $a->applicant->email ?? '',
            'event_title' => $a->staffQuotation->eventBooking->event_name ?? $a->staffQuotation->event_name ?? 'No Event',
            'event_date' => $a->shift_date ? \Carbon\Carbon::parse($a->shift_date)->format('M j, Y') : null,
            'raw_date' => $a->shift_date ? \Carbon\Carbon::parse($a->shift_date)->toDateString() : null,
            'venue' => $a->staffQuotation->eventBooking->venue ?? $a->staffQuotation->venue ?? 'TBD',
            'time' => $startTime . ' - ' . $endTime,
            'start_time' => $startTime,
            'role' => $a->role_name ?? $a->staffQuotation->sub_category ?? 'General Staff',
            'booking_ref' => $a->staffQuotation->eventBooking->booking_ref ?? $a->staffQuotation->quotation_ref ?? '',
            'worked_hours' => $a->getHours() . ' hrs',
        ];
    });

    // Prepare active EventBookings for left staffing queue tracking
    $eventBookingsData = $eventBookings->map(function($booking) {
        $startDate = \Carbon\Carbon::parse($booking->start_date);
        $hoursToStart = now()->diffInHours($startDate, false);
        
        $required = 0;
        $assigned = 0;
        $shifts = $booking->shifts->map(function($s) use (&$required, &$assigned) {
            $req = $s->quantity;
            $ass = $s->shiftSlots->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
            $required += $req;
            $assigned += $ass;
            
            $slotsData = $s->shiftSlots->map(function($slot) {
                return [
                    'id' => $slot->id,
                    'applicant_id' => $slot->applicant_id,
                    'applicant_name' => $slot->applicant->name ?? null,
                    'applicant_email' => $slot->applicant->email ?? null,
                    'status' => $slot->status ?? 'Unassigned',
                    'role_name' => $slot->role_name ?? $slot->staffQuotation->sub_category,
                    'shift_date' => $slot->shift_date ? $slot->shift_date->toDateString() : null,
                    'start_time' => $slot->start_time ?: ($slot->staffQuotation->start_time ?: '09:00'),
                    'end_time' => $slot->end_time ?: ($slot->staffQuotation->end_time ?: '17:00'),
                ];
            });

            return [
                'id' => $s->id,
                'sub_category' => $s->sub_category,
                'category' => $s->category,
                'quantity' => $req,
                'assigned' => $ass,
                'vacant' => max(0, $req - $ass),
                'slots' => $slotsData
            ];
        });
        
        $vacant = max(0, $required - $assigned);
        $filledPercent = $required > 0 ? min(100, round(($assigned / $required) * 100)) : 100;
        
        if ($vacant > 0 && $hoursToStart <= 24) {
            $priority = 'Critical';
        } elseif ($vacant > 0 && $hoursToStart <= 72) {
            $priority = 'Warning';
        } else {
            $priority = 'Healthy';
        }

        return [
            'id' => $booking->id,
            'booking_ref' => $booking->booking_ref,
            'event_name' => $booking->event_name,
            'venue' => $booking->venue,
            'start_date' => $booking->start_date ? $booking->start_date->toDateString() : null,
            'end_date' => $booking->end_date ? $booking->end_date->toDateString() : null,
            'required' => $required,
            'assigned' => $assigned,
            'vacant' => $vacant,
            'filled_percent' => $filledPercent,
            'priority' => $priority,
            'shifts' => $shifts
        ];
    });

    // Prepare flat slots list for Alpine.js initialization
    $slotsData = $slots->map(function($slot) {
        return [
            'id' => $slot->id,
            'applicant_id' => $slot->applicant_id,
            'applicant_name' => $slot->applicant->name ?? null,
            'applicant_email' => $slot->applicant->email ?? null,
            'status' => $slot->status ?? 'Unassigned',
            'role_name' => $slot->role_name ?? ($slot->staffQuotation->sub_category ?? 'General Staff'),
            'shift_date' => $slot->shift_date ? \Carbon\Carbon::parse($slot->shift_date)->toDateString() : null,
            'start_time' => $slot->start_time ?: ($slot->staffQuotation->start_time ?? '09:00'),
            'end_time' => $slot->end_time ?: ($slot->staffQuotation->end_time ?? '17:00'),
        ];
    });
@endphp

<div class="h-full flex flex-col overflow-y-auto custom-scrollbar" x-data="assignmentBoard()">
    <!-- Header -->
    <div class="dashboard-header mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shrink-0">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Staff Placements & Assignments</h1>
            <p class="text-slate-500 text-xs mt-0.5">Fill vacancies, match qualified candidates, and manage live operational shifts.</p>
        </div>
        {{-- flex-wrap: the search box + event-filter button don't fit on one line on a very
             narrow phone (320px) even with the search box flexible, since the filter button's
             own content (icon + up to 140px of label + chevron) has a fixed minimum width and
             was pushing the row past the viewport edge. --}}
        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto justify-between sm:justify-end">
            <!-- Search -->
            <div class="relative flex-1 sm:flex-initial min-w-[140px]">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" x-model="searchQuery" placeholder="Search board..."
                    class="pl-9 pr-8 py-2 w-full sm:w-56 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/20 transition-all">
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            <!-- Event filter -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                    class="btn-interactive flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:border-slate-300 shadow-xs transition-all">
                    <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                    <span x-text="selectedEventName || 'All Events'" class="max-w-[140px] truncate"></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition x-cloak class="absolute right-0 mt-1.5 z-30 w-64 bg-white border border-slate-200 rounded-xl shadow-xl py-1.5 max-h-64 overflow-y-auto custom-scrollbar" style="display:none;">
                    <button @click="selectedEventName = ''; eventFilter = ''; open = false" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 text-slate-600 border-b border-slate-100 flex items-center justify-between">
                        <span>Show All Events</span>
                        <span x-show="!selectedEventName" class="text-kingdom-gold font-bold">✓</span>
                    </button>
                    <template x-for="evt in eventList" :key="evt">
                        <button @click="selectedEventName = evt; eventFilter = ''; open = false" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 truncate flex items-center justify-between" :class="selectedEventName.toLowerCase() === evt.toLowerCase() ? 'font-bold text-kingdom-gold bg-kingdom-gold/5' : 'text-slate-700'">
                            <span x-text="evt" class="truncate"></span>
                            <span x-show="selectedEventName.toLowerCase() === evt.toLowerCase()" class="text-kingdom-gold font-bold shrink-0 ml-2">✓</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Stats Panel (Scalable Responsive Metric Cards) -->
    {{-- grid-cols-1 below sm: at 2-up on a 375px screen these cards are too narrow for the
         "metric-label" (nowrap+ellipsis) and subtitle, which truncate unreadably
         ("OPEN V...", "Unfilled staffing sl..."). Same fix as the Dashboard KPI grid. --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mb-5 shrink-0">
        <!-- Open Vacancies -->
        <div class="metric-card metric-card--warning p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <span class="metric-label">Open Vacancies</span>
                    <span class="metric-number text-amber-600 mt-1 block" x-text="stats.total_open_vacancies"></span>
                </div>
                <div class="icon-tile icon-tile--vacancies">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium mt-2 truncate">Unfilled staffing slots</p>
        </div>

        <!-- Fully Staffed Events -->
        <div class="metric-card metric-card--success p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <span class="metric-label">Fully Staffed Events</span>
                    <span class="metric-number text-emerald-600 mt-1 block" x-text="stats.fully_staffed_events"></span>
                </div>
                <div class="icon-tile icon-tile--success">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium mt-2 truncate">100% placement complete</p>
        </div>

        <!-- More Staff Needed -->
        <div class="metric-card metric-card--critical p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <span class="metric-label">More Staff Needed</span>
                    <span class="metric-number text-rose-600 mt-1 block" x-text="stats.events_requiring_attention"></span>
                </div>
                <div class="icon-tile bg-rose-50 text-rose-600 border border-rose-100">
                    <i data-lucide="bell-ring" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium mt-2 truncate">Events requiring action</p>
        </div>

        <!-- Assigned Today -->
        <div class="metric-card metric-card--accent-blue p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <span class="metric-label">Assigned Today</span>
                    <span class="metric-number text-blue-600 mt-1 block" x-text="stats.assigned_today"></span>
                </div>
                <div class="icon-tile icon-tile--staff">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium mt-2 truncate">Active on today's shifts</p>
        </div>
    </div>

    <!-- Main Workspace: 
         - Mobile/Tablet (< 1024px): Stacked flow
         - Medium Desktop (1024px - 1439px): Balanced 3-column / 2-column with responsive wrapping
         - Large Desktop (≥ 1440px / 2xl): Full 3-column side-by-side with tracking board as primary workspace
    -->
    <div class="flex-1 flex flex-col xl:flex-row gap-4 min-h-0 pb-2">

        <!-- LEFT PANEL: Staffing Queue -->
        <div class="w-full xl:w-[320px] 2xl:w-[360px] h-[380px] xl:h-full bg-white border border-slate-200 rounded-2xl shadow-xs flex flex-col overflow-hidden shrink-0">
            <!-- Queue Header -->
            <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Staffing Queue</h3>
                </div>
                <span class="text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 px-2.5 py-0.5 rounded-full" x-text="getFilteredEvents().length + ' Events'"></span>
            </div>
            
            <!-- Events & Requirements List -->
            <div class="flex-1 overflow-y-auto p-3 space-y-3.5 custom-scrollbar">
                <template x-for="evt in getFilteredEvents()" :key="evt.id">
                    <div @click="selectedEventName = evt.event_name; eventFilter = ''"
                         class="card-interactive border rounded-xl p-3.5 space-y-3 bg-white hover:border-slate-300 transition-all cursor-pointer"
                         :class="selectedEventName.toLowerCase() === evt.event_name.toLowerCase() ? 'border-kingdom-gold ring-2 ring-kingdom-gold/20 bg-amber-50/10' : 'border-slate-200'">
                        
                        <!-- Event Details -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold text-slate-900 truncate" x-text="evt.event_name"></h4>
                                <div class="flex items-center gap-1 text-[10px] text-slate-500 font-medium truncate mt-0.5">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                    <span x-text="evt.venue" class="truncate"></span>
                                </div>
                                <div class="flex items-center gap-1 text-[10px] text-slate-500 font-medium truncate mt-0.5">
                                    <i data-lucide="calendar" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                    <span x-text="formatEventDates(evt.start_date, evt.end_date)" class="truncate"></span>
                                </div>
                            </div>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-md border shrink-0 uppercase tracking-wider" 
                                  :class="getPriorityClasses(evt.priority)" 
                                  x-text="evt.priority"></span>
                        </div>

                        <!-- Progress Section -->
                        <div class="space-y-1.5 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <div class="flex justify-between items-center text-[10px] font-bold">
                                <span class="text-slate-500 uppercase tracking-wider">Staffing Progress</span>
                                <span class="text-slate-900 font-extrabold" x-text="evt.assigned + '/' + evt.required + ' (' + evt.filled_percent + '%)'"></span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-kingdom-gold to-yellow-600 h-full rounded-full transition-all duration-300" :style="'width: ' + evt.filled_percent + '%'"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-600 font-semibold pt-0.5">
                                <span>Required: <strong class="text-slate-800" x-text="evt.required"></strong></span>
                                <span>Assigned: <strong class="text-emerald-700" x-text="evt.assigned"></strong></span>
                                <span>Vacant: <strong class="text-amber-700" x-text="evt.vacant"></strong></span>
                            </div>
                        </div>

                        <!-- Role List -->
                        <div class="space-y-2">
                            <template x-for="shift in evt.shifts" :key="shift.id">
                                <div @click="selectRequirement(evt, shift)"
                                     class="border rounded-xl p-2.5 cursor-pointer transition-all duration-150 bg-white"
                                     :class="isActiveRequirement(evt, shift) ? 'border-kingdom-gold ring-2 ring-kingdom-gold/20 bg-amber-50/20' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'">
                                    
                                    <div class="flex justify-between items-start gap-1">
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-slate-800 block truncate" x-text="shift.sub_category"></span>
                                            <span class="text-[10px] text-slate-500 block mt-0.5 font-medium" x-text="shift.assigned + ' Assigned • ' + shift.vacant + ' Vacancies'"></span>
                                        </div>
                                        <span x-show="shift.vacant > 0" class="text-[9px] font-bold px-2 py-0.5 bg-amber-50 text-amber-700 rounded-md border border-amber-200 shrink-0 uppercase tracking-wider">Vacant</span>
                                        <span x-show="shift.vacant === 0" class="text-[9px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md border border-emerald-200 shrink-0 uppercase tracking-wider">Filled</span>
                                    </div>

                                    <!-- Quick actions under role row -->
                                    <div class="flex items-center gap-2 mt-2 pt-2 border-t border-slate-100">
                                        <button @click.stop="selectRequirement(evt, shift)" 
                                                class="btn-interactive flex-1 px-2.5 py-1.5 bg-kingdom-navy hover:bg-kingdom-navy/90 text-white rounded-lg text-[10px] font-bold transition-all shadow-xs">
                                            Find Applicants
                                        </button>
                                        <a :href="'/admin/job-post?create=1&role=' + encodeURIComponent(shift.sub_category)" 
                                           @click.stop=""
                                           class="btn-interactive px-2.5 py-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-[10px] font-bold transition-all">
                                            Post Job
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Empty state for staffing queue -->
                <div x-show="getFilteredEvents().length === 0" class="text-center py-12 px-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                        <i data-lucide="calendar-x" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700">No active events found</p>
                    <p class="text-[11px] text-slate-400 mt-1">Try resetting the event filter or create a new booking.</p>
                </div>
            </div>
        </div>

        <!-- CENTER PANEL: Tracking Board (Kanban Columns) -->
        <div class="w-full xl:flex-1 h-[520px] xl:h-full bg-white border border-slate-200 rounded-2xl shadow-xs flex flex-col overflow-hidden min-w-0 shrink-0 xl:shrink">
            <!-- Board Header -->
            <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i data-lucide="kanban" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Tracking Board</h3>
                </div>
                <span class="text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200 px-2.5 py-0.5 rounded-full" x-text="allAssignments.length + ' Placements'"></span>
            </div>

            <!-- Kanban Columns Container -->
            <div class="flex-1 min-h-0 p-3.5 overflow-x-auto kanban-scroll">
                <div class="flex gap-3.5 h-full pb-1 min-w-max lg:min-w-0">
                    <template x-for="col in columns" :key="col.id">
                        <div class="kanban-column flex-1 min-w-[260px] sm:min-w-[280px] bg-slate-50/70 border border-slate-200 rounded-xl flex flex-col h-full overflow-hidden"
                             @dragover.prevent="dragOverColumn(col.id, $event)"
                             @dragleave="dragLeaveColumn(col.id)"
                             @drop.prevent="dropOnColumn(col.id)">
                            
                            <!-- Column Header -->
                            <div class="px-3.5 py-2.5 border-b border-slate-200 flex items-center justify-between shrink-0 bg-white">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center" :class="col.iconBg">
                                        <i :data-lucide="col.icon" class="w-3.5 h-3.5" :class="col.iconColor"></i>
                                    </div>
                                    <h3 class="text-xs font-bold text-slate-800" x-text="col.label"></h3>
                                </div>
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full" :class="col.badgeBg" x-text="getColumnCards(col.id).length"></span>
                            </div>

                            <!-- Column Cards list -->
                            <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-2.5 space-y-2.5"
                                 :class="{'bg-blue-50/60 ring-2 ring-blue-400/50 ring-inset': dragTarget === col.id}"
                                 :id="'col-' + col.id">
                                
                                <template x-for="card in getColumnCards(col.id)" :key="card.id">
                                    <div class="kanban-card bg-white rounded-xl border border-slate-200 p-3 cursor-grab active:cursor-grabbing shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-150 group relative"
                                         draggable="true"
                                         @dragstart="dragStart(card, $event)"
                                         @dragend="dragEnd()">
                                        
                                        <!-- Header row -->
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md" :class="col.tagBg" x-text="card.status"></span>
                                                <span x-show="isLate(card)" class="inline-flex items-center gap-0.5 text-[9px] font-black bg-rose-50 text-rose-700 border border-rose-200 px-1.5 py-0.5 rounded-md uppercase tracking-wider animate-pulse">
                                                    LATE
                                                </span>
                                            </div>
                                            <button @click.stop="unassignSlot(card.id)" class="p-1 text-slate-400 hover:text-rose-600 rounded-md hover:bg-rose-50 transition-colors opacity-0 group-hover:opacity-100" title="Remove Assignment">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>

                                        <!-- Applicant profile row -->
                                        <div class="flex items-center gap-2.5 mb-2.5">
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white font-black text-[10px] shrink-0 shadow-xs"
                                                 :style="'background-color:' + stringToColor(card.applicant_name)">
                                                <span x-text="card.applicant_name ? card.applicant_name.charAt(0).toUpperCase() : '?'"></span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5">
                                                    <a @click.stop="" :href="'/admin/applicant?search=' + encodeURIComponent(card.applicant_name)" target="_blank" class="text-xs font-bold text-slate-800 hover:text-kingdom-gold transition-colors truncate block" x-text="card.applicant_name"></a>
                                                    <template x-if="card.applicant_id">
                                                        <a @click.stop="" :href="'/admin/applicant/' + card.applicant_id + '/view-resume'" target="_blank" class="text-slate-400 hover:text-kingdom-gold transition-colors inline-flex items-center" title="View Resume">
                                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                                        </a>
                                                    </template>
                                                </div>
                                                <p class="text-[10px] text-slate-500 truncate" x-text="card.applicant_email"></p>
                                            </div>
                                        </div>

                                        <!-- Event details -->
                                        <div class="bg-slate-50 rounded-lg p-2 space-y-1 text-[10px] border border-slate-100">
                                            <p class="text-slate-800 font-bold truncate" x-text="card.event_title"></p>
                                            
                                            <!-- Completed Column Layout -->
                                            <template x-if="col.id === 'Completed'">
                                                <div class="space-y-0.5">
                                                    <div class="flex items-center justify-between text-emerald-700 font-bold pt-0.5">
                                                        <span x-text="card.status"></span>
                                                        <span x-text="card.event_date"></span>
                                                    </div>
                                                    <p class="text-[10px] text-slate-600 font-semibold">
                                                        Worked: <span class="text-slate-800 font-bold" x-text="card.worked_hours || 'N/A'"></span>
                                                    </p>
                                                </div>
                                            </template>
                                            
                                            <!-- No Show Column Layout -->
                                            <template x-if="col.id === 'No Show'">
                                                <div class="space-y-0.5">
                                                    <div class="flex items-center justify-between text-rose-700 font-bold pt-0.5">
                                                        <span>Absent</span>
                                                        <span x-text="card.event_date"></span>
                                                    </div>
                                                    <p class="text-[10px] text-slate-600 font-semibold">
                                                        Scheduled: <span class="text-slate-800 font-bold" x-text="card.time"></span>
                                                    </p>
                                                </div>
                                            </template>
                                            
                                            <!-- Assigned Column Layout -->
                                            <template x-if="col.id === 'Assigned'">
                                                <div class="flex items-center justify-between text-slate-500 font-medium pt-0.5">
                                                    <span x-text="card.event_date"></span>
                                                    <span x-text="card.time" class="font-semibold text-slate-700"></span>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Footer tags -->
                                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                                            <span class="text-[9px] font-bold text-kingdom-gold bg-kingdom-gold/10 px-2 py-0.5 rounded border border-kingdom-gold/20 uppercase tracking-wider" x-text="card.role"></span>
                                            <i data-lucide="grip-vertical" class="w-3.5 h-3.5 text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"></i>
                                        </div>

                                        <!-- Conflict alert warning -->
                                        <div x-show="conflicts[card.id]" class="mt-2 p-1.5 bg-rose-50 border border-rose-200 rounded-lg flex items-start gap-1.5">
                                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-rose-600 shrink-0 mt-0.5"></i>
                                            <span class="text-[9px] font-bold text-rose-700 leading-tight" x-text="conflicts[card.id]?.message"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Polished Column Empty States -->
                                <div x-show="getColumnCards(col.id).length === 0" class="flex flex-col items-center justify-center py-10 px-4 text-center rounded-xl bg-white/60 border border-dashed border-slate-200 m-1">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-2">
                                        <i :data-lucide="col.icon" class="w-4 h-4"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700" x-text="'No ' + col.label + ' Staff'"></p>
                                    <p class="text-[10px] text-slate-400 mt-1 leading-relaxed" 
                                       x-text="col.id === 'Assigned' ? 'Assign candidates from the Applicants panel.' : (col.id === 'Completed' ? 'Completed shifts will appear here.' : 'No absence incidents reported.')"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Applicants (Light, Human-Friendly Candidate Workspace) -->
        <div class="w-full xl:w-[320px] 2xl:w-[360px] h-[480px] xl:h-full bg-[#f8fafc] border border-slate-200 rounded-2xl shadow-xs flex flex-col overflow-hidden shrink-0">
            <!-- Suggestions Header -->
            <div class="p-3.5 bg-white border-b border-slate-200 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Candidate Pool</h3>
                    </div>
                    <button x-show="applicantSearchQuery" @click="applicantSearchQuery = ''" class="text-[10px] font-bold text-kingdom-gold hover:text-yellow-600 transition-colors">Clear</button>
                </div>
                
                <!-- Toggle Tabs: All / Suitable -->
                <div class="flex items-center bg-slate-100 rounded-lg p-0.5 mt-2.5 shrink-0 border border-slate-200/80">
                    <button @click="applicantTab = 'all'" 
                            :class="applicantTab === 'all' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="flex-1 text-center py-1 text-[10px] rounded-md transition-all font-semibold uppercase tracking-wider">
                        All
                    </button>
                    <button @click="if(activeAssignSlot) applicantTab = 'suitable'" 
                            :disabled="!activeAssignSlot"
                            :class="applicantTab === 'suitable' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : (activeAssignSlot ? 'text-slate-600 hover:text-slate-900' : 'text-slate-400 opacity-50 cursor-not-allowed')"
                            class="flex-1 text-center py-1 text-[10px] rounded-md transition-all font-semibold uppercase tracking-wider relative group">
                        Suitable
                        <span x-show="!activeAssignSlot" class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-slate-900 text-white text-[9px] rounded-md whitespace-nowrap z-50 shadow-md">Select role to match</span>
                    </button>
                </div>

                <!-- Search input -->
                <div class="relative mt-2.5">
                    <i data-lucide="search" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                    <input type="text" x-model="applicantSearchQuery" placeholder="Search candidate by name or skill..."
                        class="pl-8 pr-8 py-1.5 w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg text-xs placeholder-slate-400 focus:bg-white focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold/20 transition-all">
                </div>
            </div>

            <!-- Suggestions list -->
            <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                <!-- Guidance banner: No event selected -->
                <div x-show="!activeAssignSlot && !selectedEventName" class="p-3 bg-amber-50 border border-amber-200/80 rounded-xl text-center">
                    <p class="text-[11px] text-amber-800 font-bold">No Event Selected</p>
                    <p class="text-[10px] text-amber-700/90 mt-0.5 leading-relaxed">Select an event from the staffing queue to start assigning candidates.</p>
                </div>
                <!-- Guidance banner: Quick assign mode -->
                <div x-show="!activeAssignSlot && selectedEventName" class="p-3 bg-blue-50 border border-blue-200/80 rounded-xl text-center">
                    <p class="text-[11px] text-blue-800 font-bold">Quick Placement Mode</p>
                    <p class="text-[10px] text-blue-700/90 mt-0.5 leading-relaxed">Select a candidate and assign them to an available role, or pick a specific vacant role for targeted placement.</p>
                </div>

                <!-- Suggestions list with applicants -->
                <div class="space-y-2.5">
                    <p x-show="activeAssignSlot && applicantTab === 'suitable'" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-1" x-text="'Matching: ' + (activeAssignSlot?.role_name || 'this role')"></p>
                    <p x-show="activeAssignSlot && applicantTab === 'all'" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-1">All Available Candidates</p>
                    
                    <template x-for="suggestion in filteredApplicants" :key="suggestion.applicant.id">
                        <div class="card-interactive p-3 border rounded-xl bg-white hover:border-slate-300 shadow-xs transition-all flex flex-col gap-2 relative"
                             :class="{'border-rose-300 bg-rose-50/20': suggestion.hasConflict, 'border-emerald-300 bg-emerald-50/20': suggestion.isQualifiedForShift && !suggestion.hasConflict, 'border-slate-200': !suggestion.hasConflict && !suggestion.isQualifiedForShift}">

                            <!-- Header with name & resume -->
                            <div class="flex justify-between items-start gap-1.5">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a :href="'/admin/applicant?search=' + encodeURIComponent(suggestion.applicant.name)" target="_blank" class="text-xs font-bold text-slate-900 hover:text-kingdom-gold transition-colors truncate block" x-text="suggestion.applicant.name"></a>
                                        <a :href="'/admin/applicant/' + suggestion.applicant.id + '/view-resume'" target="_blank" class="text-slate-400 hover:text-kingdom-gold transition-colors inline-flex items-center" title="View Resume">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <span x-show="suggestion.isQualifiedForShift" class="inline-flex items-center gap-1 text-[8px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded uppercase tracking-wide">
                                            <i data-lucide="badge-check" class="w-2.5 h-2.5"></i> Qualified
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-500 block truncate mt-0.5" x-text="suggestion.applicant.email"></span>
                                </div>
                            </div>

                            <!-- Info Row: Status, Location, Role -->
                            <div class="flex flex-wrap items-center text-[10px] font-semibold text-slate-600 gap-y-1">
                                <div class="flex items-center mr-3">
                                    <span class="w-2 h-2 rounded-full shrink-0 mr-1.5" :class="getApplicantStatusDotClass(suggestion.availability)"></span>
                                    <span class="text-slate-700" x-text="suggestion.availability"></span>
                                </div>
                                <div class="flex items-center mr-3">
                                    <i data-lucide="map-pin" class="w-3 h-3 shrink-0 text-slate-400 mr-1"></i>
                                    <span class="text-slate-600" x-text="suggestion.distance"></span>
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="briefcase" class="w-3 h-3 shrink-0 text-slate-400 mr-1"></i>
                                    <span class="text-slate-600 truncate max-w-[120px]" x-text="suggestion.applicant.role"></span>
                                </div>
                            </div>

                            <!-- Suitability details (only show if in suitable tab and has match notes) -->
                            <p x-show="activeAssignSlot && applicantTab === 'suitable' && suggestion.notes" 
                               class="text-[10px] text-slate-500 font-medium leading-relaxed italic bg-slate-50 p-1.5 rounded-lg border border-slate-100" 
                               x-text="suggestion.notes"></p>

                            <!-- Actions & Conflicts -->
                            <div class="flex items-center justify-between mt-1 pt-1.5 border-t border-slate-100 shrink-0" x-show="suggestion.hasConflict || activeAssignSlot || selectedEventName">
                                <span x-show="suggestion.hasConflict" class="text-[10px] font-bold text-rose-600 flex items-center gap-1">
                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 shrink-0"></i>
                                    <span>Time Conflict</span>
                                </span>
                                <span x-show="!suggestion.hasConflict"></span> <!-- Spacer -->
                                
                                <button x-show="activeAssignSlot || selectedEventName" 
                                        @click="handleAssignClick(suggestion.applicant.id)"
                                        class="btn-interactive px-3 py-1.5 bg-kingdom-gold hover:bg-yellow-500 text-slate-900 rounded-lg text-[10px] font-extrabold transition-all shadow-xs shrink-0 active:scale-95 duration-100">
                                    Assign
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Empty state -->
                    <div x-show="filteredApplicants.length === 0" class="text-center py-8 px-4 bg-white border border-dashed border-slate-200 rounded-2xl mt-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400">
                            <i data-lucide="user-x" class="w-4 h-4"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-800">No candidates found</p>
                        <p class="text-[10px] text-slate-500 mt-1" x-text="applicantTab === 'suitable' ? 'Try switching to the All tab to see all candidates.' : 'Try adjusting your search criteria.'"></p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MOBILE DRAWER: Applicants (Mobile overlay) -->
    <div x-show="suggestionsDrawerOpen" class="fixed inset-0 z-[100] xl:hidden flex flex-col justify-end bg-slate-950/60 backdrop-blur-sm" x-transition x-cloak style="display: none;">
        <div class="bg-white border-t border-slate-200 rounded-t-3xl max-h-[85vh] flex flex-col p-4 shadow-2xl" @click.outside="suggestionsDrawerOpen = false">
            <!-- Drawer handle bar -->
            <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-4"></div>
            
            <!-- Drawer Header -->
            <div class="flex justify-between items-center mb-3 border-b border-slate-150 pb-2">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Candidate Pool</h3>
                </div>
                <button @click="suggestionsDrawerOpen = false" class="p-1 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-slate-800 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Toggle Tabs and Search (Mobile) -->
            <div class="mb-3 space-y-2 shrink-0">
                <div class="flex items-center bg-slate-100 rounded-lg p-0.5 border border-slate-200">
                    <button @click="applicantTab = 'all'" 
                            :class="applicantTab === 'all' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="flex-1 text-center py-1 text-[10px] rounded-md transition-all font-semibold uppercase tracking-wider">
                        All
                    </button>
                    <button @click="if(activeAssignSlot) applicantTab = 'suitable'" 
                            :disabled="!activeAssignSlot"
                            :class="applicantTab === 'suitable' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : (activeAssignSlot ? 'text-slate-600 hover:text-slate-900' : 'text-slate-400 opacity-50 cursor-not-allowed')"
                            class="flex-1 text-center py-1 text-[10px] rounded-md transition-all font-semibold uppercase tracking-wider">
                        Suitable
                    </button>
                </div>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                    <input type="text" x-model="applicantSearchQuery" placeholder="Search candidates..."
                        class="pl-8 pr-8 py-1.5 w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg text-xs placeholder-slate-400 focus:bg-white focus:outline-none focus:border-kingdom-gold transition-all">
                </div>
            </div>

            <!-- Drawer scrollable list -->
            <div class="flex-1 overflow-y-auto space-y-3 custom-scrollbar pr-1 pb-6">
                <p x-show="activeAssignSlot && applicantTab === 'suitable'" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider" x-text="'Matching: ' + (activeAssignSlot?.role_name || 'this role')"></p>
                <p x-show="activeAssignSlot && applicantTab === 'all'" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">All Candidates</p>
                
                <template x-for="suggestion in filteredApplicants" :key="suggestion.applicant.id">
                    <div class="p-3 border rounded-xl bg-white hover:border-slate-300 shadow-xs transition-all flex flex-col gap-2 relative"
                         :class="{'border-rose-300 bg-rose-50/20': suggestion.hasConflict, 'border-emerald-300 bg-emerald-50/20': suggestion.isQualifiedForShift && !suggestion.hasConflict, 'border-slate-200': !suggestion.hasConflict && !suggestion.isQualifiedForShift}">
                        
                        <!-- Header with name & resume -->
                        <div class="flex justify-between items-start gap-1.5">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <a :href="'/admin/applicant?search=' + encodeURIComponent(suggestion.applicant.name)" target="_blank" class="text-xs font-bold text-slate-900 hover:text-kingdom-gold transition-colors truncate block" x-text="suggestion.applicant.name"></a>
                                    <a :href="'/admin/applicant/' + suggestion.applicant.id + '/view-resume'" target="_blank" class="text-slate-400 hover:text-kingdom-gold transition-colors inline-flex items-center" title="View Resume">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                                <span class="text-[10px] text-slate-500 block truncate mt-0.5" x-text="suggestion.applicant.email"></span>
                            </div>
                        </div>

                        <!-- Info Row: Status, Location, Role -->
                        <div class="flex flex-wrap items-center text-[10px] font-semibold text-slate-600 gap-y-1">
                            <div class="flex items-center mr-3">
                                <span class="w-2 h-2 rounded-full shrink-0 mr-1.5" :class="getApplicantStatusDotClass(suggestion.availability)"></span>
                                <span class="text-slate-700" x-text="suggestion.availability"></span>
                            </div>
                            <div class="flex items-center mr-3">
                                <i data-lucide="map-pin" class="w-3 h-3 shrink-0 text-slate-400 mr-1"></i>
                                <span class="text-slate-600" x-text="suggestion.distance"></span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="briefcase" class="w-3 h-3 shrink-0 text-slate-400 mr-1"></i>
                                <span class="text-slate-600" x-text="suggestion.applicant.role"></span>
                            </div>
                        </div>

                        <!-- Suitability details -->
                        <p x-show="activeAssignSlot && applicantTab === 'suitable' && suggestion.notes" 
                           class="text-[10px] text-slate-500 font-medium leading-relaxed italic bg-slate-50 p-1.5 rounded-lg border border-slate-100" 
                           x-text="suggestion.notes"></p>

                        <!-- Actions & Conflicts -->
                        <div class="flex items-center justify-between mt-1 pt-1.5 border-t border-slate-100 shrink-0" x-show="suggestion.hasConflict || activeAssignSlot || selectedEventName">
                            <span x-show="suggestion.hasConflict" class="text-[10px] font-bold text-rose-600 flex items-center gap-1">
                                <i data-lucide="alert-triangle" class="w-3.5 h-3.5 shrink-0"></i>
                                <span>Time Conflict</span>
                            </span>
                            <span x-show="!suggestion.hasConflict"></span> <!-- Spacer -->
                            
                            <button x-show="activeAssignSlot || selectedEventName"
                                    @click="handleAssignClick(suggestion.applicant.id)"
                                    class="btn-interactive px-3 py-1.5 bg-kingdom-gold hover:bg-yellow-500 text-slate-900 rounded-lg text-[10px] font-extrabold transition-all shadow-xs shrink-0 active:scale-95 duration-100">
                                Assign
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="filteredApplicants.length === 0" class="text-center py-8 px-4 bg-slate-50 border border-dashed border-slate-200 rounded-2xl mt-3">
                    <p class="text-xs font-bold text-slate-800">No candidates found.</p>
                    <div class="flex flex-col gap-2 mt-3 max-w-[200px] mx-auto">
                        <button @click="applicantSearchQuery = ''; suggestionsDrawerOpen = false" class="btn-interactive px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-bold shadow-xs transition-all">
                            View All Candidates
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .kanban-card[draggable="true"]:active {
        opacity: 0.65;
        transform: rotate(1.5deg) scale(0.98);
    }
    .kanban-column {
        transition: background-color 0.18s ease;
    }

    .kanban-scroll::-webkit-scrollbar {
        height: 8px;
    }
    .kanban-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 6px;
        margin: 0 4px;
    }
    .kanban-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #B89955, #d4b06a);
        border-radius: 6px;
        border: 2px solid #f1f5f9;
    }
    .kanban-scroll::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(90deg, #9A7D40, #B89955);
    }
    .kanban-scroll {
        scrollbar-width: thin;
        scrollbar-color: #B89955 #f1f5f9;
    }
</style>

<script>
window.assignmentBoard = function assignmentBoard() {
    return {
        searchQuery: '',
        eventFilter: '',
        bookingFilter: '',
        shiftFilter: '',
        dragTarget: null,
        draggedCard: null,
        selectedEventName: '',

        // Suggestions/Matching properties
        applicantSearchQuery: '',
        draggedApplicant: null,
        dragTargetSlot: null,
        suggestionsDrawerOpen: false,
        activeAssignSlot: null,
        activeRequirement: null,
        applicantTab: 'all',

        init() {
            const params = new URLSearchParams(window.location.search);
            const eventParam = params.get('event');
            const bookingParam = params.get('booking');
            const shiftParam = params.get('shift');

            if (eventParam) {
                const found = this.eventList.find(e => e.toLowerCase() === eventParam.toLowerCase());
                this.eventFilter = found || eventParam;
                this.selectedEventName = this.eventFilter;
            } else if (bookingParam) {
                this.bookingFilter = bookingParam;
                const match = this.allAssignments.find(a => a.booking_ref && a.booking_ref.toLowerCase() === bookingParam.toLowerCase());
                if (match) {
                    this.selectedEventName = match.event_title;
                }
            } else if (shiftParam) {
                this.shiftFilter = shiftParam;
                const match = this.allAssignments.find(a => a.notes && a.notes.toLowerCase() === shiftParam.toLowerCase());
                if (match) {
                    this.selectedEventName = match.event_title;
                }
            }

            if (!this.selectedEventName && this.eventBookings && this.eventBookings.length > 0) {
                this.selectedEventName = this.eventBookings[0].event_name;
            }
        },

        columns: [
            { id: 'Assigned',    label: 'Assigned',     icon: 'user-check',  headerBg: 'bg-white',  iconBg: 'bg-blue-50',    iconColor: 'text-blue-600',    badgeBg: 'bg-blue-50 text-blue-700 border border-blue-100',    tagBg: 'bg-blue-50 text-blue-700 border border-blue-100' },
            { id: 'Completed',   label: 'Completed',    icon: 'badge-check', headerBg: 'bg-white', iconBg: 'bg-emerald-50', iconColor: 'text-emerald-600', badgeBg: 'bg-emerald-50 text-emerald-700 border border-emerald-100', tagBg: 'bg-emerald-50 text-emerald-700 border border-emerald-100' },
            { id: 'No Show',     label: 'No Show',      icon: 'user-x',      headerBg: 'bg-white', iconBg: 'bg-rose-50',    iconColor: 'text-rose-600',    badgeBg: 'bg-rose-50 text-rose-700 border border-rose-100',      tagBg: 'bg-rose-50 text-rose-700 border border-rose-100' },
        ],

        isLate(card) {
            if (card.status !== 'Assigned' && card.status !== 'Confirmed') return false;
            if (!card.raw_date || !card.start_time) return false;
            
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const todayStr = `${year}-${month}-${day}`;
            
            if (card.raw_date <= todayStr) {
                const parts = card.start_time.split(':').map(Number);
                const shiftTime = new Date();
                shiftTime.setHours(parts[0], parts[1], 0, 0);
                
                if (card.raw_date < todayStr || (card.raw_date === todayStr && now > new Date(shiftTime.getTime() + 15 * 60 * 1000))) {
                    return true;
                }
            }
            return false;
        },

        allAssignments: @json($assignmentData),
        applicantPool: @json($applicantPool),
        rolePoolCounts: @json($rolePoolCounts),
        stats: @json($stats),
        conflicts: @json($conflicts),
        eventBookings: @json($eventBookingsData),
        slots: @json($slotsData),

        get eventList() {
            return [...new Set(this.allAssignments.map(a => a.event_title))].filter(Boolean).sort();
        },

        getColumnCards(colId) {
            return this.allAssignments.filter(a => {
                const status = a.status || 'Assigned';
                let statusMatch = false;
                if (colId === 'Assigned') {
                    statusMatch = ['Assigned', 'Confirmed', 'Checked In', 'Checked Out'].includes(status);
                } else if (colId === 'Completed') {
                    statusMatch = ['Completed', 'Approved', 'Billed'].includes(status);
                } else {
                    statusMatch = status === colId;
                }
                const searchMatch = !this.searchQuery || 
                    (a.applicant_name || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (a.event_title || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (a.role || '').toLowerCase().includes(this.searchQuery.toLowerCase());
                const targetEvent = this.eventFilter || this.selectedEventName;
                const eventMatch = !targetEvent || a.event_title.toLowerCase() === targetEvent.toLowerCase();
                
                let bookingMatch = true;
                if (this.bookingFilter) {
                    bookingMatch = a.booking_ref && a.booking_ref.toLowerCase() === this.bookingFilter.toLowerCase();
                }

                let shiftMatch = true;
                if (this.shiftFilter) {
                    shiftMatch = (a.role && a.role.toLowerCase() === this.shiftFilter.toLowerCase()) ||
                                 (a.notes && a.notes.toLowerCase() === this.shiftFilter.toLowerCase());
                }

                return statusMatch && searchMatch && eventMatch && bookingMatch && shiftMatch;
            });
        },

        getFilteredEvents() {
            return this.eventBookings.filter(eb => {
                if (!this.eventFilter) return true;
                return eb.event_name.toLowerCase() === this.eventFilter.toLowerCase();
            });
        },

        formatEventDates(startStr, endStr) {
            if (!startStr) return 'TBD';
            const start = new Date(startStr);
            const startOpt = { month: 'short', day: 'numeric', year: 'numeric' };
            if (!endStr || startStr === endStr) {
                return start.toLocaleDateString('en-US', startOpt);
            }
            const end = new Date(endStr);
            return start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ' - ' + end.toLocaleDateString('en-US', startOpt);
        },

        getPriorityClasses(priority) {
            if (priority === 'Critical') return 'bg-rose-50 text-rose-700 border-rose-200';
            if (priority === 'Warning') return 'bg-amber-50 text-amber-700 border-amber-200';
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        },

        getStatusClasses(status) {
            const classes = {
                'Assigned': 'bg-blue-50 text-blue-700 border-blue-100',
                'Confirmed': 'bg-indigo-50 text-indigo-700 border-indigo-100',
                'Checked In': 'bg-amber-50 text-amber-700 border-amber-100',
                'Checked Out': 'bg-orange-50 text-orange-700 border-orange-100',
                'Completed': 'bg-emerald-50 text-emerald-700 border-emerald-100',
                'Approved': 'bg-emerald-100 text-emerald-800 border-emerald-250',
                'Billed': 'bg-slate-100 text-slate-700 border-slate-350',
                'No Show': 'bg-rose-50 text-rose-700 border-rose-100',
                'Unassigned': 'bg-slate-50 text-slate-500 border-slate-200'
            };
            return classes[status] || classes['Unassigned'];
        },

        getApplicantStatusDotClass(status) {
            if (status === 'Available') return 'bg-emerald-500 ring-2 ring-emerald-100';
            if (status.includes('Assigned') || status.includes('Working')) return 'bg-blue-500 ring-2 ring-blue-100';
            if (status === 'Conflict') return 'bg-rose-500 ring-2 ring-rose-100';
            return 'bg-slate-400 ring-2 ring-slate-100';
        },

        selectRequirement(evt, shift) {
            // Find first vacant slot
            const vacantSlot = shift.slots.find(s => !s.applicant_id || s.applicant_id == 0);
            this.activeAssignSlot = vacantSlot ? {
                id: vacantSlot.id,
                role_name: shift.sub_category,
                event_name: evt.event_name,
                shift_date: shift.slots[0]?.shift_date,
                start_time: vacantSlot.start_time,
                end_time: vacantSlot.end_time
            } : {
                id: shift.slots[0]?.id || 0,
                role_name: shift.sub_category,
                event_name: evt.event_name,
                shift_date: shift.slots[0]?.shift_date,
                start_time: shift.slots[0]?.start_time,
                end_time: shift.slots[0]?.end_time
            };

            this.activeRequirement = {
                event_id: evt.id,
                event_name: evt.event_name,
                role_name: shift.sub_category,
                shift: shift
            };

            this.selectedEventName = evt.event_name;
            this.applicantSearchQuery = '';
            this.suggestionsDrawerOpen = true; // Opens slide-up drawer on mobile
            this.applicantTab = 'suitable'; // Automatically switch to suitable tab when selecting slot
        },

        isActiveRequirement(evt, shift) {
            return this.activeRequirement && 
                   this.activeRequirement.event_id === evt.id && 
                   this.activeRequirement.role_name === shift.sub_category;
        },

        get suggestedApplicants() {
            if (!this.activeAssignSlot) return [];
            
            const roleName = this.activeAssignSlot.role_name;
            const targetDate = this.activeAssignSlot.shift_date;
            const shiftId = this.activeAssignSlot.staff_quotation_id;

            const scored = this.applicantPool.map(c => {
                let score = 0;
                let notes = [];

                // 0. Qualified via a job posting created specifically for this shift's shortfall
                const isQualifiedForShift = shiftId && Array.isArray(c.qualified_staff_quotation_ids) && c.qualified_staff_quotation_ids.includes(shiftId);
                if (isQualifiedForShift) {
                    score += 40;
                    notes.push('Qualified for this shift');
                }

                // 1. Role Match
                if (c.role.toLowerCase() === roleName.toLowerCase()) {
                    score += 50;
                    notes.push('Exact Role Match');
                } else {
                    const keywords = {
                        'security': ['security', 'cctv', 'door supervisor', 'steward', 'guard', 'sia'],
                        'hospitality': ['hospitality', 'waiter', 'waiting', 'chef', 'bar', 'staff', 'server'],
                        'event': ['event', 'management', 'planner', 'coordinator', 'hostess', 'steward']
                    };
                    
                    let related = false;
                    for (let cat in keywords) {
                        if (keywords[cat].some(k => roleName.toLowerCase().includes(k)) &&
                            keywords[cat].some(k => c.role.toLowerCase().includes(k))) {
                            related = true;
                            break;
                        }
                    }
                    
                    if (related) {
                        score += 35;
                        notes.push('Related Experience');
                    } else {
                        score += 15;
                        notes.push('General Staff');
                    }
                }
                
                // 2. Active status check
                const isHired = ['hired', 'active', 'available'].includes(c.status.toLowerCase());
                if (isHired) {
                    score += 15;
                }
                
                // 3. Shift Conflict / Overlap Check on target date
                let hasConflict = false;
                let isAssignedToday = false;
                
                if (targetDate) {
                    const sameDaySlots = this.slots.filter(s => s.applicant_id === c.id && s.shift_date === targetDate);
                    if (sameDaySlots.length > 0) {
                        isAssignedToday = true;
                        
                        const slotStart = this.timeToMins(this.activeAssignSlot.start_time || '09:00');
                        const slotEnd = this.timeToMins(this.activeAssignSlot.end_time || '17:00');
                        
                        for (let other of sameDaySlots) {
                            const otherStart = this.timeToMins(other.start_time || '09:00');
                            const otherEnd = this.timeToMins(other.end_time || '17:00');
                            if (slotStart < otherEnd && otherStart < slotEnd) {
                                hasConflict = true;
                                break;
                            }
                        }
                    }
                }
                
                if (hasConflict) {
                    score -= 45; // Penalize conflict heavily
                    notes.push('Shift Conflict');
                } else if (isAssignedToday) {
                    score += 5; // Working but different time
                    notes.push('Working other shift today');
                } else {
                    score += 20; // Available
                    notes.push('No Conflicts');
                }
                
                // 4. Location Match
                let distanceLabel = 'Not Specified';
                if (c.location && c.location !== 'Not provided' && c.location.trim() !== '') {
                    distanceLabel = c.location;
                    if (c.location.toLowerCase().includes('london') || c.location.toLowerCase().includes('local')) {
                        score += 15;
                    } else {
                        score += 5;
                    }
                }
                
                let availabilityStatus = 'Available';
                if (hasConflict) {
                    availabilityStatus = 'Conflict';
                } else if (isAssignedToday) {
                    availabilityStatus = 'Assigned Today';
                } else if (c.status === 'Unavailable') {
                    availabilityStatus = 'Unavailable';
                }

                // Add search filtering logic
                let matchesSearch = true;
                if (this.applicantSearchQuery) {
                    const q = this.applicantSearchQuery.toLowerCase();
                    matchesSearch = c.name.toLowerCase().includes(q) || c.role.toLowerCase().includes(q);
                }

                return {
                    applicant: c,
                    score: Math.max(0, Math.min(100, score)),
                    availability: availabilityStatus,
                    distance: distanceLabel,
                    notes: notes.join(' • '),
                    hasConflict: hasConflict,
                    matchesSearch: matchesSearch,
                    isQualifiedForShift: isQualifiedForShift
                };
            });

            return scored
                .filter(s => s.matchesSearch && (s.isQualifiedForShift || s.notes.includes('Exact Role Match') || s.notes.includes('Related Experience')))
                .sort((a, b) => {
                    if (a.isQualifiedForShift !== b.isQualifiedForShift) return a.isQualifiedForShift ? -1 : 1;
                    if (b.score !== a.score) return b.score - a.score;
                    return a.applicant.name.localeCompare(b.applicant.name);
                });
        },

        get filteredApplicants() {
            const tab = this.activeAssignSlot ? this.applicantTab : 'all';
            
            if (tab === 'suitable' && this.activeAssignSlot) {
                return this.suggestedApplicants;
            }
            
            // Return all applicants, filtered by search query
            let pool = this.applicantPool;
            
            if (this.applicantSearchQuery) {
                const q = this.applicantSearchQuery.toLowerCase();
                pool = pool.filter(c => 
                    c.name.toLowerCase().includes(q) || 
                    c.role.toLowerCase().includes(q)
                );
            }
            
            const targetDate = this.activeAssignSlot?.shift_date;
            const slotStart = this.activeAssignSlot ? this.timeToMins(this.activeAssignSlot.start_time || '09:00') : 0;
            const slotEnd = this.activeAssignSlot ? this.timeToMins(this.activeAssignSlot.end_time || '17:00') : 0;
            
            return pool.map(c => {
                let hasConflict = false;
                let isAssignedToday = false;
                
                if (targetDate) {
                    const sameDaySlots = this.slots.filter(s => s.applicant_id === c.id && s.shift_date === targetDate);
                    if (sameDaySlots.length > 0) {
                        isAssignedToday = true;
                        
                        for (let other of sameDaySlots) {
                            const otherStart = this.timeToMins(other.start_time || '09:00');
                            const otherEnd = this.timeToMins(other.end_time || '17:00');
                            if (slotStart < otherEnd && otherStart < slotEnd) {
                                  hasConflict = true;
                                  break;
                            }
                        }
                    }
                }
                
                let availabilityStatus = c.status || 'Available';
                if (hasConflict) {
                    availabilityStatus = 'Conflict';
                } else if (isAssignedToday) {
                    availabilityStatus = 'Assigned Today';
                }
                
                let distanceLabel = 'Not Specified';
                if (c.location && c.location !== 'Not provided' && c.location.trim() !== '') {
                    distanceLabel = c.location;
                }
                
                return {
                    applicant: c,
                    availability: availabilityStatus,
                    distance: distanceLabel,
                    notes: c.role || 'General Staff',
                    hasConflict: hasConflict
                };
            }).sort((a, b) => a.applicant.name.localeCompare(b.applicant.name));
        },

        stringToColor(str) {
            if (!str) return '#94a3b8';
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            const colors = ['#3b82f6','#8b5cf6','#ec4899','#f59e0b','#10b981','#ef4444','#6366f1','#14b8a6','#f97316','#06b6d4'];
            return colors[Math.abs(hash) % colors.length];
        },

        // Applicant drag-and-drop
        dragApplicantStart(applicant, event) {
            this.draggedApplicant = applicant;
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', applicant.id);
        },

        dragApplicantEnd() {
            this.draggedApplicant = null;
            this.dragTargetSlot = null;
        },

        dropApplicantOnSlot(slotId, event) {
            const applicantId = parseInt(event.dataTransfer.getData('text/plain'));
            if (applicantId) {
                this.assignApplicantToSlot(slotId, applicantId, 'Assigned');
            }
            this.dragTargetSlot = null;
        },

        getFirstVacantSlotId() {
            if (!this.selectedEventName) return null;
            const evt = this.eventBookings.find(eb => eb.event_name.toLowerCase() === this.selectedEventName.toLowerCase());
            if (!evt) return null;
            
            for (let shift of evt.shifts) {
                const vacantSlot = shift.slots.find(s => !s.applicant_id || s.applicant_id == 0);
                if (vacantSlot) return vacantSlot.id;
            }
            return null;
        },

        handleAssignClick(applicantId) {
            if (this.activeAssignSlot) {
                this.assignApplicantToSlot(this.activeAssignSlot.id, applicantId, 'Assigned');
            } else {
                const slotId = this.getFirstVacantSlotId();
                if (slotId) {
                    this.assignApplicantToSlot(slotId, applicantId, 'Assigned');
                } else {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Vacancies Available',
                            text: 'All slots for this event are fully staffed. Select an event with open vacancies.',
                            confirmButtonColor: '#0F1D33'
                        });
                    }
                }
            }
        },

        async assignApplicantToSlot(slotId, applicantId, statusOverride = 'Assigned') {
            try {
                const res = await fetch(`/admin/assignments/${slotId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        applicant_id: applicantId,
                        status: statusOverride
                    })
                });

                const data = await res.json();
                if (data.success) {
                    const applicant = this.applicantPool.find(c => c.id === applicantId);
                    const applicantName = applicant ? applicant.name : 'Unknown';
                    const applicantEmail = applicant ? applicant.email : '';
                    const applicantRole = applicant ? applicant.role : '';

                    if (applicant) {
                        applicant.status = 'Assigned';
                    }

                    const slot = this.slots.find(s => s.id === slotId);
                    if (slot) {
                        slot.applicant_id = applicantId;
                        slot.applicant_name = applicantName;
                        slot.status = statusOverride;
                    }

                    for (let booking of this.eventBookings) {
                        for (let shift of booking.shifts) {
                            const sl = shift.slots.find(s => s.id === slotId);
                            if (sl) {
                                sl.applicant_id = applicantId;
                                sl.applicant_name = applicantName;
                                sl.applicant_email = applicantEmail;
                                sl.status = statusOverride;
                                
                                shift.assigned = shift.slots.filter(s => s.applicant_id && s.applicant_id != 0).length;
                                shift.vacant = Math.max(0, shift.quantity - shift.assigned);
                            }
                        }
                        
                        let req = 0;
                        let ass = 0;
                        for (let shift of booking.shifts) {
                            req += shift.quantity;
                            ass += shift.assigned;
                        }
                        booking.required = req;
                        booking.assigned = ass;
                        booking.vacant = Math.max(0, req - ass);
                        booking.filled_percent = req > 0 ? Math.min(100, Math.round((ass / req) * 100)) : 100;
                    }

                    let eventTitle = 'No Event';
                    let eventDate = null;
                    let venue = 'TBD';
                    let shiftTime = '09:00 - 17:00';
                    let rawDate = null;
                    let startTime = '09:00';

                    const linkedBooking = this.eventBookings.find(eb => eb.shifts.some(sh => sh.slots.some(sl => sl.id === slotId)));
                    if (linkedBooking) {
                        eventTitle = linkedBooking.event_name;
                        eventDate = linkedBooking.start_date;
                        venue = linkedBooking.venue;
                        const linkedShift = linkedBooking.shifts.find(sh => sh.slots.some(sl => sl.id === slotId));
                        if (linkedShift) {
                            const foundSlot = linkedShift.slots.find(sl => sl.id === slotId);
                            shiftTime = (foundSlot.start_time || '09:00') + ' - ' + (foundSlot.end_time || '17:00');
                            rawDate = foundSlot.shift_date;
                            startTime = foundSlot.start_time || '09:00';
                        }
                    }

                    const formattedDate = eventDate ? new Date(eventDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : null;

                    const cardIndex = this.allAssignments.findIndex(a => a.id === slotId);
                    const cardData = {
                        id: slotId,
                        status: statusOverride,
                        applicant_id: applicantId,
                        applicant_name: applicantName,
                        applicant_email: applicantEmail,
                        event_title: eventTitle,
                        event_date: formattedDate,
                        raw_date: rawDate,
                        venue: venue,
                        time: shiftTime,
                        start_time: startTime,
                        role: applicantRole,
                        booking_ref: linkedBooking ? linkedBooking.booking_ref : '',
                        worked_hours: 'N/A'
                    };

                    if (cardIndex !== -1) {
                        this.allAssignments[cardIndex] = cardData;
                    } else {
                        this.allAssignments.push(cardData);
                    }

                    this.recalculateStats();
                    this.recalculateConflicts();

                    this.suggestionsDrawerOpen = false; // Close mobile drawer

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Candidate Assigned',
                            text: `${applicantName} placed on ${eventTitle} (Confirmed).`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } else {
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Assignment Failed', text: data.message || 'Could not assign candidate.' });
                    }
                }
            } catch (e) {
                console.error('Assignment error:', e);
            }
            
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        async unassignSlot(slotId) {
            if (window.Swal) {
                const result = await Swal.fire({
                    title: 'Remove Assignment?',
                    text: "Are you sure you want to unassign this candidate?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0F1D33',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, unassign'
                });
                if (!result.isConfirmed) return;
            }

            try {
                const res = await fetch(`/admin/assignments/${slotId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'Unassigned',
                        applicant_id: null
                    })
                });

                const data = await res.json();
                if (data.success) {
                    const slot = this.slots.find(s => s.id === slotId);
                    const prevApplicantId = slot ? slot.applicant_id : null;

                    if (slot) {
                        slot.applicant_id = null;
                        slot.applicant_name = null;
                        slot.status = 'Unassigned';
                    }

                    for (let booking of this.eventBookings) {
                        for (let shift of booking.shifts) {
                            const sl = shift.slots.find(s => s.id === slotId);
                            if (sl) {
                                sl.applicant_id = null;
                                sl.applicant_name = null;
                                sl.applicant_email = null;
                                sl.status = 'Unassigned';
                                
                                shift.assigned = shift.slots.filter(s => s.applicant_id && s.applicant_id != 0).length;
                                shift.vacant = Math.max(0, shift.quantity - shift.assigned);
                            }
                        }
                        
                        let req = 0;
                        let ass = 0;
                        for (let shift of booking.shifts) {
                            req += shift.quantity;
                            ass += shift.assigned;
                        }
                        booking.required = req;
                        booking.assigned = ass;
                        booking.vacant = Math.max(0, req - ass);
                        booking.filled_percent = req > 0 ? Math.min(100, Math.round((ass / req) * 100)) : 100;
                    }

                    this.allAssignments = this.allAssignments.filter(a => a.id !== slotId);

                    if (prevApplicantId) {
                        const stillAssigned = this.slots.some(s => s.applicant_id === prevApplicantId);
                        const applicant = this.applicantPool.find(c => c.id === prevApplicantId);
                        if (applicant) {
                            applicant.status = stillAssigned ? 'Assigned' : 'Available';
                        }
                    }

                    this.recalculateStats();
                    this.recalculateConflicts();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Assignment Removed',
                            text: 'Candidate unassigned successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } else {
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Unassign Failed', text: data.message || 'Could not remove assignment.' });
                    }
                }
            } catch (e) {
                console.error('Unassign error:', e);
            }

            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        // Kanban Card drag-and-drop
        dragStart(card, event) {
            this.draggedCard = card;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.id);
        },

        dragEnd() {
            this.draggedCard = null;
            this.dragTarget = null;
        },

        dragOverColumn(colId, event) {
            if (this.draggedCard && this.draggedCard.status !== colId) {
                this.dragTarget = colId;
            }
            event.dataTransfer.dropEffect = 'move';
        },

        dragLeaveColumn(colId) {
            if (this.dragTarget === colId) {
                this.dragTarget = null;
            }
        },

        async dropOnColumn(colId) {
            if (!this.draggedCard || this.draggedCard.status === colId) {
                this.dragTarget = null;
                return;
            }

            const cardId = this.draggedCard.id;
            const oldStatus = this.draggedCard.status;

            this.draggedCard.status = colId;
            this.dragTarget = null;

            const slot = this.slots.find(s => s.id === cardId);
            if (slot) {
                slot.status = colId;
            }

            for (let booking of this.eventBookings) {
                for (let shift of booking.shifts) {
                    const s = shift.slots.find(sl => sl.id === cardId);
                    if (s) {
                        s.status = colId;
                    }
                }
            }

            try {
                const res = await fetch(`/admin/assignments/${cardId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: colId })
                });

                const data = await res.json();
                if (!data.success) {
                    const card = this.allAssignments.find(a => a.id === cardId);
                    if (card) card.status = oldStatus;
                    if (slot) slot.status = oldStatus;
                    
                    for (let booking of this.eventBookings) {
                        for (let shift of booking.shifts) {
                            const s = shift.slots.find(sl => sl.id === cardId);
                            if (s) s.status = oldStatus;
                        }
                    }

                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Update Failed', text: data.message || 'Could not update status.', confirmButtonColor: '#0F1D33' });
                    }
                }
            } catch (e) {
                const card = this.allAssignments.find(a => a.id === cardId);
                if (card) card.status = oldStatus;
                if (slot) slot.status = oldStatus;
                console.error('Assignment status update failed:', e);
            }

            this.recalculateStats();
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        recalculateStats() {
            const vacantCount = this.slots.filter(s => !s.applicant_id || s.applicant_id == 0).length;
            
            let fullyStaffed = 0;
            let attentionNeeded = 0;
            
            for (let booking of this.eventBookings) {
                if (booking.required > 0 && booking.assigned >= booking.required) {
                    fullyStaffed++;
                } else {
                    attentionNeeded++;
                }
            }

            const todayStr = new Date().toISOString().split('T')[0];
            const assignedTodayCount = this.slots.filter(s => s.applicant_id && s.applicant_id != 0 && s.shift_date === todayStr).length;

            this.stats = {
                total_open_vacancies: vacantCount,
                fully_staffed_events: fullyStaffed,
                events_requiring_attention: attentionNeeded,
                assigned_today: assignedTodayCount
            };
        },

        timeToMins(timeStr) {
            if (!timeStr) return 0;
            const parts = timeStr.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1] || 0);
        },

        recalculateConflicts() {
            this.conflicts = {};
            const assigned = this.slots.filter(s => s.applicant_id && s.applicant_id != 0);
            
            for (let slot of assigned) {
                const date = slot.shift_date;
                const appId = slot.applicant_id;
                if (!date || !appId) continue;
                
                const overlapping = assigned.filter(other => {
                    if (other.id === slot.id) return false;
                    if (other.applicant_id !== appId) return false;
                    if (other.shift_date !== date) return false;
                    
                    const start1 = this.timeToMins(slot.start_time || '09:00');
                    const end1 = this.timeToMins(slot.end_time || '17:00');
                    const start2 = this.timeToMins(other.start_time || '09:00');
                    const end2 = this.timeToMins(other.end_time || '17:00');
                    
                    return (start1 < end2 && start2 < end1);
                });
                
                if (overlapping.length > 0) {
                    const otherEventNames = overlapping.map(o => {
                        const b = this.eventBookings.find(eb => eb.shifts.some(sh => sh.id === o.staff_quotation_id));
                        return b ? b.event_name : 'other shift';
                    });
                    const uniqueNames = [...new Set(otherEventNames)].join(', ');
                    this.conflicts[slot.id] = {
                        has_conflict: true,
                        message: 'Overlaps with: ' + uniqueNames
                    };
                }
            }
        }
    }
}
</script>
@endsection
