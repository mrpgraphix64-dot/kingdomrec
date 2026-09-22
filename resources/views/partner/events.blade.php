@extends('layouts.partner')

@section('title', 'My Bookings & Shifts | Kingdom Partner')

@section('content')
@php
    $statusStyles = [
        'Pending'              => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30',
        'Draft'                => 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800/40 dark:text-slate-400 dark:border-slate-700/50',
        'Sent'                 => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30',
        'Approved'             => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30',
        'Confirmed'            => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30',
        'Cancelled'            => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/30',
        'Live Event'           => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/30',
        'Completed'            => 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800/40 dark:text-slate-400 dark:border-slate-700/50',
        'Staffing In Progress' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30',
        'Ready'                => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-indigo-900/30',
        'Fully Staffed'        => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30',
    ];
    $statusAccents = [
        'Pending'              => 'border-l-amber-400',
        'Draft'                => 'border-l-slate-400',
        'Sent'                 => 'border-l-blue-400',
        'Approved'             => 'border-l-emerald-500',
        'Confirmed'            => 'border-l-emerald-500',
        'Cancelled'            => 'border-l-red-400',
        'Live Event'           => 'border-l-rose-500',
        'Completed'            => 'border-l-slate-400',
        'Staffing In Progress' => 'border-l-amber-400',
        'Ready'                => 'border-l-indigo-500',
        'Fully Staffed'        => 'border-l-emerald-500',
    ];
    $statusIcons = [
        'Pending'              => 'clock',
        'Draft'                => 'file-edit',
        'Sent'                 => 'send',
        'Approved'             => 'check-circle',
        'Confirmed'            => 'check-circle',
        'Cancelled'            => 'x-circle',
        'Live Event'           => 'play',
        'Completed'            => 'archive',
        'Staffing In Progress' => 'users',
        'Ready'                => 'sparkles',
        'Fully Staffed'        => 'shield-check',
    ];
    $totalEvents = $events->count();
    $activeCount = $events->whereIn('status', ['Live Event', 'Fully Staffed', 'Ready', 'Staffing In Progress'])->count();
    $totalStaff = $allBookings->sum('quantity');
    $totalBudget = $allBookings->sum(fn($b) => ($b->quantity ?? 0) * ($b->rate ?? 0) * ($b->shift_hours ?? 0));
@endphp

<div class="flex flex-col h-full max-h-full gap-6" x-data="{ 
    search: new URLSearchParams(window.location.search).get('booking') || new URLSearchParams(window.location.search).get('event') || '', 
    statusFilter: '',
    deepBookingRef: new URLSearchParams(window.location.search).get('booking') || '',
    deepEventName: new URLSearchParams(window.location.search).get('event') || ''
}">
    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-gradient-to-br from-[#0F1C3F] to-[#1a2b5e] rounded-xl text-white shadow-lg">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">My Bookings & Shifts</h1>
                    <p class="text-sm text-slate-500 mt-0.5">High-level overview of all your staffing events and shift details.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" x-model="search" placeholder="Search events..."
                    class="pl-9 pr-9 py-2.5 w-56 bg-white border border-[#E6EAF0] rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 transition-all">
                <button x-show="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white border border-[#E6EAF0] rounded-xl text-sm font-semibold text-slate-600 hover:border-slate-300 transition-all">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span x-text="statusFilter || 'All Status'"></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition x-cloak class="absolute right-0 z-50 mt-1 w-44 bg-[#0F1C3F] rounded-xl shadow-2xl border border-[#1a2b4c] py-1 px-1" style="display:none;">
                    @foreach(['All', 'Pending', 'Approved', 'Confirmed', 'Draft', 'Cancelled', 'Live Event', 'Completed', 'Staffing In Progress', 'Ready', 'Fully Staffed'] as $filter)
                    <button @click="statusFilter = '{{ $filter === 'All' ? '' : $filter }}'; open = false"
                        class="w-full text-left px-3 py-2.5 rounded-lg text-sm transition-all"
                        :class="statusFilter === '{{ $filter === 'All' ? '' : $filter }}' ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white'">{{ $filter }}</button>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('partner.book-staff') }}" class="flex items-center gap-2 px-5 py-2.5 bg-[#0F1C3F] hover:bg-[#1a2b5e] text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-[#0F1C3F]/20 hover:-translate-y-0.5 whitespace-nowrap">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                New Event
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="flex-none grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Total Events', 'value' => $totalEvents, 'icon' => 'calendar', 'color' => 'from-[#0F1C3F] to-[#1a2b5e] shadow-[#0F1C3F]/20'],
                ['label' => 'Active Now', 'value' => $activeCount, 'icon' => 'play-circle', 'color' => 'from-emerald-400 to-emerald-500 shadow-emerald-500/20'],
                ['label' => 'Staff Requested', 'value' => $totalStaff, 'icon' => 'users', 'color' => 'from-blue-500 to-blue-600 shadow-blue-500/20'],
                ['label' => 'Total Budget', 'value' => '£' . number_format($totalBudget, 0), 'icon' => 'banknote', 'color' => 'from-amber-400 to-amber-500 shadow-amber-500/20'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="card-interactive bg-white dark:bg-slate-900 rounded-2xl p-4 lg:p-5 border border-[#E6EAF0] dark:border-slate-800 shadow-sm flex items-center justify-between group">
            <div class="flex flex-col gap-2">
                <div class="card-icon p-2.5 bg-gradient-to-br {{ $stat['color'] }} rounded-xl text-white shadow-sm w-fit">
                    <i data-lucide="{{ $stat['icon'] }}" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ $stat['label'] }}</span>
            </div>
            <p class="text-3xl lg:text-4xl font-black text-slate-800 dark:text-white">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    @if($events->count() > 0)
    <!-- Event Cards Grid -->
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 pb-4">
            @foreach($events as $event)
            @php
                $status = $event->status ?? 'Pending';
                $stClass = $statusStyles[$status] ?? $statusStyles['Pending'];
                $accentClass = $statusAccents[$status] ?? $statusAccents['Pending'];
                $fillPct = $event->total_staff > 0 ? min(100, round(($event->assigned_count / $event->total_staff) * 100)) : 0;
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-[#E6EAF0] dark:border-slate-800 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 overflow-hidden border-l-4 {{ $accentClass }} flex flex-col"
                 x-data="{ expanded: (deepBookingRef && '{{ strtolower($event->quotation_ref) }}' === deepBookingRef.toLowerCase()) || (deepEventName && '{{ strtolower($event->name) }}' === deepEventName.toLowerCase()) }"
                 x-show="('{{ addslashes($event->name) }}'.toLowerCase().includes(search.toLowerCase()) || '{{ addslashes($event->venue) }}'.toLowerCase().includes(search.toLowerCase()) || '{{ addslashes($event->quotation_ref) }}'.toLowerCase().includes(search.toLowerCase()) || !search) && (!statusFilter || '{{ $status }}' === statusFilter)">
                
                <!-- Card Header -->
                <div class="p-5 pb-4">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-[15px] font-black text-slate-900 dark:text-white truncate mb-1">{{ $event->name }}</h3>
                            <div class="flex items-center gap-1.5 text-[12px] text-slate-500">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                <span class="truncate">{{ $event->venue }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border shrink-0 {{ $stClass }}">
                            <i data-lucide="{{ $statusIcons[$status] ?? 'info' }}" class="w-3 h-3"></i>
                            {{ $status }}
                        </span>
                    </div>

                    <!-- Date -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-1.5 text-[12px] text-slate-500">
                            <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-slate-400"></i>
                            @if($event->start_date)
                                {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                @if($event->end_date && $event->end_date != $event->start_date)
                                    — {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                                @endif
                            @else
                                Date TBD
                            @endif
                        </div>
                        <div class="text-[10px] font-bold text-slate-400" title="Created on">
                            Created: {{ \Carbon\Carbon::parse($event->created_at)->format('M d, Y') }}
                        </div>
                    </div>

                    <!-- Staffing Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center text-[11px] font-bold mb-1.5">
                            <span class="text-slate-500 uppercase tracking-wider">Staffing Progress</span>
                            <span class="{{ $fillPct >= 100 ? 'text-emerald-600' : ($fillPct > 0 ? 'text-blue-600' : 'text-slate-400') }}">{{ $event->assigned_count }} / {{ $event->total_staff }} filled</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $fillPct >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $fillPct }}%"></div>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="grid grid-cols-3 gap-3 p-3 bg-slate-50/80 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                        <div class="text-center">
                            <p class="text-[18px] font-black text-slate-800 dark:text-white">{{ $event->total_shifts }}</p>
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Shifts</p>
                        </div>
                        <div class="text-center border-x border-slate-200 dark:border-slate-700">
                            <p class="text-[18px] font-black text-slate-800 dark:text-white">{{ $event->total_staff }}</p>
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Staff</p>
                        </div>
                        <div class="text-center">
                            <p class="text-[18px] font-black text-kingdom-gold">£{{ number_format($event->total_cost, 0) }}</p>
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Budget</p>
                        </div>
                    </div>

                    @if($status === 'Completed' && $event->event_booking_id)
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                            @if($event->has_rating)
                                <div class="flex items-center justify-between text-xs font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/20 dark:text-emerald-400 px-3 py-2.5 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Feedback Submitted
                                    </span>
                                </div>
                            @elseif($event->are_timesheets_finalized)
                                <button type="button" 
                                        @click="$dispatch('open-feedback-modal', { event_id: '{{ $event->event_booking_id }}', event_name: '{{ addslashes($event->name) }}' })"
                                        class="w-full flex items-center justify-center gap-1.5 py-2.5 bg-[#0F1C3F] hover:bg-[#1a2b5e] text-white rounded-xl font-bold text-xs shadow-md shadow-[#0F1C3F]/10 transition-all hover:-translate-y-0.5">
                                    <i data-lucide="star" class="w-4 h-4"></i>
                                    Leave Event Feedback
                                </button>
                            @else
                                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 bg-slate-50 dark:bg-slate-800/40 px-3 py-2.5 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                        Timesheets Pending Finalization
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Expanded Shift Details Section -->
                <div x-show="expanded" x-collapse class="border-t border-slate-100 dark:border-slate-800 p-5 bg-slate-50/50 dark:bg-slate-900/40" style="display: none;">
                    <h4 class="text-[11px] uppercase font-bold text-slate-400 tracking-wider mb-2.5">Shift Breakdown</h4>
                    <div class="space-y-2.5">
                        @foreach($event->shifts as $shift)
                            <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-[#E6EAF0] dark:border-slate-800 shadow-sm flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $shift->category }} - {{ $shift->sub_category }}</span>
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $shift->start_time ?? '09:00' }} - {{ $shift->end_time ?? '17:00' }} · {{ $shift->shift_hours }}h</p>
                                    </div>
                                    <span class="text-xs font-black text-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 dark:text-indigo-400 px-2.5 py-1 rounded-lg">
                                        Qty: {{ $shift->quantity }}
                                    </span>
                                </div>
                                
                                <!-- Assigned Staff list -->
                                <div class="mt-1 pt-1.5 border-t border-slate-50 dark:border-slate-800">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Assigned Staff</span>
                                    <div class="flex flex-wrap gap-1.5 mt-1">
                                        @php $assignedStaff = $shift->shiftSlots->whereNotNull('applicant_id'); @endphp
                                        @if($assignedStaff->count() > 0)
                                            @foreach($assignedStaff as $slot)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 px-2 py-0.5 rounded-full border border-emerald-100 dark:border-emerald-900/30">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    {{ $slot->applicant->name ?? 'Applicant' }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-[10px] text-slate-400 font-medium italic">No staff assigned yet</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Shortcut to Timesheets -->
                    <a href="{{ route('partner.timesheets') }}?booking={{ urlencode($event->quotation_ref) }}" 
                       class="mt-4 w-full flex items-center justify-center gap-1.5 py-2.5 bg-kingdom-gold hover:bg-yellow-600 text-slate-900 rounded-lg font-bold text-xs shadow-md shadow-kingdom-gold/10 transition-all">
                        <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i>
                        Go to Timesheet Tracker
                        <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                    </a>
                </div>

                <!-- Card Footer Actions -->
                <div class="mt-auto px-5 py-3 border-t border-[#E6EAF0] dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 flex items-center justify-between gap-2">
                    <button @click="expanded = !expanded" 
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-[#0F1C3F] hover:bg-[#1a2b5e] text-white rounded-lg font-bold text-[12px] transition-all shadow-sm hover:shadow-md">
                        <i data-lucide="list" class="w-3.5 h-3.5"></i>
                        <span x-text="expanded ? 'Hide Shifts' : 'View Shifts'"></span>
                        <i :class="expanded ? 'rotate-180' : ''" data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform"></i>
                    </button>
                    <div class="flex items-center gap-1">
                        @if($event->is_editable)
                        <a href="{{ route('partner.booking.edit', $event->id) }}" title="Edit" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        @endif
                        <button type="button" x-data @click="$dispatch('open-confirm-modal', { title: 'Delete Event', message: 'This will remove all shifts under this event. This action cannot be undone.', onConfirm: () => submitDeleteForm('{{ route('partner.booking.destroy', $event->id) }}') })" title="Delete" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="flex-1 min-h-0 flex flex-col items-center justify-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
            <i data-lucide="calendar" class="w-8 h-8 text-slate-400"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No events yet</h3>
        <p class="text-sm text-slate-500 mb-6">Book staff to create your first event.</p>
        <a href="{{ route('partner.book-staff') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-kingdom-gold hover:bg-yellow-600 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-kingdom-gold/20">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            Staff Booking
        </a>
    </div>
    @endif
</div>

<!-- Feedback Modal -->
<div x-data="{ 
    open: false,
    eventBookingId: '',
    eventName: '',
    rating: 0,
    staffRating: 0,
    comment: '',
    hoverRating: 0,
    hoverStaffRating: 0
}"
@open-feedback-modal.window="
    open = true;
    eventBookingId = $event.detail.event_id;
    eventName = $event.detail.event_name;
    rating = 0;
    staffRating = 0;
    comment = '';
"
x-show="open"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 scale-95"
x-transition:enter-end="opacity-100 scale-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100 scale-100"
x-transition:leave-end="opacity-0 scale-95"
class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
x-cloak
style="display: none;">
    
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl w-full max-w-lg overflow-hidden" @click.outside="open = false">
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Leave Event Feedback</h3>
                <p class="text-xs text-slate-500 mt-0.5" x-text="eventName"></p>
            </div>
            <button @click="open = false" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('partner.rateEvent') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="event_booking_id" :value="eventBookingId">
            <input type="hidden" name="rating" :value="rating">
            <input type="hidden" name="staff_performance_rating" :value="staffRating">

            <!-- Overall Rating -->
            <div>
                <label class="block text-sm font-bold text-slate-800 dark:text-white mb-2">Overall Experience</label>
                <div class="flex items-center gap-2">
                    <template x-for="i in 5">
                        <button type="button" 
                                @click="rating = i"
                                @mouseenter="hoverRating = i"
                                @mouseleave="hoverRating = 0"
                                class="p-1 text-slate-300 hover:text-kingdom-gold transition-colors focus:outline-none">
                            <svg class="w-8 h-8 transition-all duration-150" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 :class="{ 
                                     'fill-amber-400 text-amber-400 scale-110': i <= (hoverRating || rating), 
                                     'text-slate-300 dark:text-slate-700 fill-none': i > (hoverRating || rating) 
                                 }">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Staff Performance Rating -->
            <div>
                <label class="block text-sm font-bold text-slate-800 dark:text-white mb-2">Staff Performance</label>
                <div class="flex items-center gap-2">
                    <template x-for="i in 5">
                        <button type="button" 
                                @click="staffRating = i"
                                @mouseenter="hoverStaffRating = i"
                                @mouseleave="hoverStaffRating = 0"
                                class="p-1 text-slate-300 hover:text-kingdom-gold transition-colors focus:outline-none">
                            <svg class="w-8 h-8 transition-all duration-150" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 :class="{ 
                                     'fill-amber-400 text-amber-400 scale-110': i <= (hoverStaffRating || staffRating), 
                                     'text-slate-300 dark:text-slate-700 fill-none': i > (hoverStaffRating || staffRating) 
                                 }">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Optional Comment -->
            <div>
                <label for="review" class="block text-sm font-bold text-slate-800 dark:text-white mb-2">Optional Comments</label>
                <textarea name="review" id="review" rows="4" x-model="comment" placeholder="Tell us how we did, what went well, or any suggestions..."
                          class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 transition-all resize-none"></textarea>
            </div>

            <!-- Submit/Cancel Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="open = false"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 text-slate-700 rounded-xl font-bold text-sm transition-all">
                    Cancel
                </button>
                <button type="submit" :disabled="!rating || !staffRating"
                        class="px-6 py-2.5 bg-[#0F1C3F] hover:bg-[#1a2b5e] disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-[#0F1C3F]/10">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
