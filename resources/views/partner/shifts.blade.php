@extends('layouts.partner')

@section('title', ($eventFilter ? $eventFilter . " — " : "") . 'Shift Listings | Kingdom Partner')

@section('content')
@php
    $statusStyles = [
        'Pending' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200', 'Draft' => 'bg-slate-50 text-slate-600 ring-1 ring-slate-200',
        'Sent' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200', 'Approved' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'Confirmed' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200', 'Cancelled' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
    ];
    $totalHours = $bookings->sum(fn($b) => ($b->quantity ?? 0) * ($b->shift_hours ?? 0));
    $totalStaff = $bookings->sum('quantity');
    $totalCostAll = $bookings->sum(fn($b) => ($b->quantity ?? 0) * ($b->rate ?? 0) * ($b->shift_hours ?? 0));
    $groupedBookings = $bookings->groupBy(fn($b) => $b->event_name ?: 'Untitled Event');
@endphp

<div class="flex flex-col h-full max-h-full gap-6" x-data="{ search: '', statusFilter: '' }">

    <!-- Back to Events Breadcrumb (when filtered) -->
    @if($eventFilter)
    <div class="flex-none">
        <a href="{{ route('partner.event-list') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#0F1C3F] transition-colors group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Back to Events
        </a>
    </div>
    @endif

    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-gradient-to-br from-[#0F1C3F] to-[#1a2b5e] rounded-xl text-white shadow-lg">
                <i data-lucide="list" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                    @if($eventFilter)
                        {{ $eventFilter }}
                    @else
                        Shift Listings
                    @endif
                </h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    @if($eventFilter)
                        Shift schedule & staff assignments for this event.
                    @else
                        Track individual shifts, staff assignments, and hours across all events.
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" x-model="search" placeholder="Search shifts..."
                    class="pl-9 pr-9 py-2.5 w-56 bg-white border border-[#E6EAF0] rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 transition-all">
                <button x-show="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
            </div>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white border border-[#E6EAF0] rounded-xl text-sm font-semibold text-slate-600 hover:border-slate-300 transition-all">
                    <i data-lucide="filter" class="w-4 h-4 text-slate-400"></i>
                    <span x-text="statusFilter || 'All'"></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-transition x-cloak class="absolute right-0 z-50 mt-1 w-44 bg-[#0F1C3F] rounded-xl shadow-2xl border border-[#1a2b4c] py-1 px-1" style="display:none;">
                    @foreach(['All', 'Pending', 'Approved', 'Confirmed', 'Draft', 'Cancelled'] as $f)
                    <button @click="statusFilter = '{{ $f === 'All' ? '' : $f }}'; open = false"
                        class="w-full text-left px-3 py-2.5 rounded-lg text-sm transition-all"
                        :class="statusFilter === '{{ $f === 'All' ? '' : $f }}' ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5'">{{ $f }}</button>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('partner.book-staff') }}" class="flex items-center gap-2 px-5 py-2.5 bg-[#0F1C3F] hover:bg-[#1a2b5e] text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-[#0F1C3F]/20 hover:-translate-y-0.5 whitespace-nowrap">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                New Booking
            </a>
        </div>
    </div>

    <!-- Summary Stats (teal accent) -->
    <div class="flex-none grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php $shiftStats = [
            ['label' => 'Total Shifts', 'value' => $bookings->count(), 'icon' => 'list', 'color' => 'from-blue-400 to-blue-600 shadow-blue-500/30'],
            ['label' => 'Staff Required', 'value' => $totalStaff, 'icon' => 'users', 'color' => 'from-emerald-400 to-emerald-600 shadow-emerald-500/30'],
            ['label' => 'Total Hours', 'value' => $totalHours . 'h', 'icon' => 'clock', 'color' => 'from-amber-400 to-amber-500 shadow-amber-500/20'],
            ['label' => 'Cost', 'value' => '£' . number_format($totalCostAll, 0), 'icon' => 'banknote', 'color' => 'from-emerald-400 to-emerald-500 shadow-emerald-500/20'],
        ]; @endphp
        @foreach($shiftStats as $stat)
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 lg:p-5 border border-[#E6EAF0] dark:border-slate-800 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 flex items-center justify-between group">
            <div class="flex flex-col gap-2">
                <div class="p-2.5 bg-gradient-to-br {{ $stat['color'] }} rounded-xl text-white shadow-sm w-fit group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300"><i data-lucide="{{ $stat['icon'] }}" class="w-5 h-5"></i></div>
                <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">{{ $stat['label'] }}</span>
            </div>
            <p class="text-3xl lg:text-4xl font-black text-slate-800 dark:text-white">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    @if($bookings->count() > 0)
    <!-- Grouped Shifts by Event -->
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar flex flex-col gap-4">
        @foreach($groupedBookings as $eventName => $eventShifts)
            @php
                $eventStaff = $eventShifts->sum('quantity');
                $eventHours = $eventShifts->sum(fn($b) => ($b->quantity ?? 0) * ($b->shift_hours ?? 0));
                $eventCost = $eventShifts->sum(fn($b) => ($b->quantity ?? 0) * ($b->rate ?? 0) * ($b->shift_hours ?? 0));
                
                // Get venue and date from first shift in group
                $firstShift = $eventShifts->first();
                $eventVenue = $firstShift->venue ?? 'No Venue';
                $eventStartDate = $firstShift->start_date ? $firstShift->start_date->format('M d, Y') : 'TBD';
                $eventEndDate = $firstShift->end_date ? $firstShift->end_date->format('M d, Y') : null;
                $eventDateStr = $eventStartDate;
                if ($eventEndDate && $eventEndDate !== $eventStartDate) {
                    $eventDateStr .= ' - ' . $eventEndDate;
                }
                
                // Event booking status
                $eventStatus = $firstShift->eventBooking->status ?? $firstShift->status ?? 'Pending';
                $eventStClass = $statusStyles[$eventStatus] ?? $statusStyles['Pending'];
            @endphp
            
            <div class="bg-white dark:bg-slate-900 border border-[#E6EAF0] dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
                 x-data="{ isOpen: false }"
                 x-show="[
                     @foreach($eventShifts as $booking)
                         { 
                             subCategory: '{{ addslashes($booking->sub_category ?? '') }}', 
                             eventName: '{{ addslashes($booking->event_name ?? '') }}', 
                             status: '{{ $booking->status ?? 'Pending' }}' 
                         },
                     @endforeach
                 ].some(s => (!search || s.subCategory.toLowerCase().includes(search.toLowerCase()) || s.eventName.toLowerCase().includes(search.toLowerCase())) && (!statusFilter || s.status === statusFilter))"
                 x-effect="if (search !== '' || statusFilter !== '') isOpen = true">
                 
                 <!-- Accordion Header -->
                 <div @click="isOpen = !isOpen" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-5 cursor-pointer hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors select-none">
                     <div class="flex items-start gap-3.5">
                         <div class="p-3 bg-gradient-to-br from-[#0F1C3F] to-[#1a2b5e] rounded-xl text-white shadow-md shadow-[#0F1C3F]/10 shrink-0">
                             <i data-lucide="calendar" class="w-5 h-5"></i>
                         </div>
                         <div class="min-w-0">
                             <h3 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2 flex-wrap">
                                 <span>{{ $eventName }}</span>
                                 <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $eventStClass }}">{{ $eventStatus }}</span>
                             </h3>
                             <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5 flex-wrap">
                                 <span class="flex items-center gap-1 font-medium"><i data-lucide="map-pin" class="w-3.5 h-3.5 shrink-0 text-slate-400"></i>{{ $eventVenue }}</span>
                                 <span class="text-slate-300 dark:text-slate-700 font-bold">•</span>
                                 <span class="flex items-center gap-1 font-medium"><i data-lucide="calendar-days" class="w-3.5 h-3.5 shrink-0 text-slate-400"></i>{{ $eventDateStr }}</span>
                             </p>
                         </div>
                     </div>
                     
                     <div class="flex items-center justify-between lg:justify-end gap-5 border-t border-slate-100 dark:border-slate-800 pt-3 lg:pt-0 lg:border-none">
                         <!-- Mini Stats Row -->
                         <div class="flex items-center gap-3">
                             <!-- Shifts Count -->
                             <div class="px-2.5 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-bold flex items-center gap-1.5 border border-blue-100/50 dark:border-blue-900/30">
                                 <i data-lucide="list" class="w-3.5 h-3.5"></i>
                                 <span>{{ $eventShifts->count() }} {{ $eventShifts->count() > 1 ? 'Shifts' : 'Shift' }}</span>
                             </div>
                             <!-- Staff Requested -->
                             <div class="px-2.5 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 rounded-lg text-xs font-bold flex items-center gap-1.5 border border-emerald-100/50 dark:border-emerald-900/30">
                                 <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                 <span>{{ $eventStaff }} Staff</span>
                             </div>
                             <!-- Cost -->
                             <div class="px-2.5 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 rounded-lg text-xs font-bold flex items-center gap-1.5 border border-emerald-100/50 dark:border-emerald-900/30">
                                 <i data-lucide="banknote" class="w-3.5 h-3.5"></i>
                                 <span>£{{ number_format($eventCost, 2) }}</span>
                             </div>
                         </div>
                         
                         <!-- Toggle Chevron -->
                         <div class="p-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                             <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="isOpen ? 'rotate-180' : ''"></i>
                         </div>
                     </div>
                 </div>
                 
                 <!-- Collapsible Table -->
                 <div x-show="isOpen" x-collapse style="display: none;">
                     <div class="border-t border-[#E6EAF0] dark:border-slate-800 overflow-x-auto custom-scrollbar">
                         <table class="w-full text-left" style="border-collapse: collapse;">
                             <thead class="bg-[#0f1f3d] sticky top-0 z-10 text-white">
                                 <tr class="text-xs font-semibold tracking-wide text-white uppercase bg-[#0f1f3d]">
                                     <th class="px-4 py-4 text-center w-12">#</th>
                                     <th class="px-4 py-4 text-center w-28">Status</th>
                                     <th class="px-4 py-4 text-center">Role & Shift</th>
                                     <th class="px-4 py-4 text-center">Schedule</th>
                                     <th class="px-4 py-4 text-center">Staff</th>
                                     <th class="px-4 py-4 text-center">Gross Cost</th>
                                     <th class="px-4 py-4 text-center w-20">Actions</th>
                                 </tr>
                             </thead>
                             <tbody class="divide-y divide-slate-100 dark:divide-slate-800" x-data="{ expandedShiftId: null }">
                                 @foreach($eventShifts as $index => $booking)
                                     @php
                                         $status = $booking->status ?? 'Pending';
                                         $stClass = $statusStyles[$status] ?? $statusStyles['Pending'];
                                         $shiftCost = ($booking->quantity ?? 0) * ($booking->rate ?? 0) * ($booking->shift_hours ?? 0);
                                         $shiftTotalH = ($booking->quantity ?? 0) * ($booking->shift_hours ?? 0);
                                         $assignedCount = 0;
                                         foreach($booking->events as $ev) { $assignedCount += $ev->assignments->whereIn('status', ['Assigned','Confirmed'])->count(); }
                                     @endphp
                                     <tr class="hover:bg-slate-50 transition-colors group cursor-pointer border-b border-slate-100"
                                         @click="expandedShiftId = expandedShiftId === {{ $booking->id }} ? null : {{ $booking->id }}"
                                         x-show="(!search || '{{ addslashes($booking->sub_category ?? '') }}'.toLowerCase().includes(search.toLowerCase()) || '{{ addslashes($booking->event_name ?? '') }}'.toLowerCase().includes(search.toLowerCase())) && (!statusFilter || '{{ $status }}' === statusFilter)">
                                         <td class="px-4 py-4 text-center">
                                             <span class="text-[12px] font-bold text-slate-500">{{ $index + 1 }}</span>
                                         </td>
                                         <td class="px-4 py-4 text-center">
                                             <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $stClass }}">{{ $status }}</span>
                                         </td>
                                         <td class="px-4 py-4 text-center">
                                             <p class="text-[13px] font-bold text-slate-800 dark:text-slate-200">{{ $booking->sub_category ?? 'Staff' }}</p>
                                             @if($booking->shift)
                                             <p class="text-[11px] text-slate-400 capitalize">{{ $booking->shift }} shift</p>
                                             @endif
                                         </td>
                                         <td class="px-4 py-4 text-center">
                                             <p class="text-[12px] font-bold text-slate-700 dark:text-slate-300">
                                                 {{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('H:i') : '—' }}
                                                 –
                                                 {{ $booking->end_time ? \Carbon\Carbon::parse($booking->end_time)->format('H:i') : '—' }}
                                             </p>
                                             <p class="text-[10px] text-slate-400 font-bold">{{ $booking->shift_hours ?? 0 }}h per staff</p>
                                         </td>
                                         <td class="px-4 py-4 text-center">
                                             <p class="text-[13px] font-black {{ $assignedCount >= ($booking->quantity ?? 1) ? 'text-emerald-600' : 'text-slate-800 dark:text-slate-200' }}">{{ $assignedCount }}/{{ $booking->quantity ?? 0 }}</p>
                                             <p class="text-[10px] text-slate-400 font-bold">assigned</p>
                                         </td>
                                         <td class="px-4 py-4 text-center">
                                             <p class="text-[13px] font-bold text-slate-800 dark:text-slate-200">£{{ number_format($shiftCost, 2) }}</p>
                                             <p class="text-[10px] text-teal-600 font-bold bg-teal-50 dark:bg-teal-950/30 dark:text-teal-400 px-1.5 py-0.5 rounded inline-block">{{ $shiftTotalH }}h total</p>
                                         </td>
                                         <td class="px-4 py-3.5 text-center">
                                             <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                                                 <button @click="expandedShiftId = expandedShiftId === {{ $booking->id }} ? null : {{ $booking->id }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-md transition-colors"><i data-lucide="eye" class="w-4 h-4"></i></button>
                                                 @if($booking->isEditable() && $booking->event_booking_id)
                                                 <a href="{{ route('partner.booking.edit', $booking->event_booking_id) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 rounded-md transition-colors"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                                                 @endif
                                             </div>
                                         </td>
                                     </tr>
                                     
                                     <!-- Expanded Shift Row Details -->
                                     <tr x-show="expandedShiftId === {{ $booking->id }}" x-collapse class="bg-slate-50/30 dark:bg-slate-800/10">
                                         <td colspan="7" class="p-0">
                                             <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                                                 <!-- Shift Details -->
                                                 <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 border-t-[3px] border-t-kingdom-gold shadow-sm">
                                                     <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                                                         <div class="p-1.5 bg-yellow-50 dark:bg-yellow-950/30 rounded-lg"><i data-lucide="clipboard-check" class="w-4 h-4 text-kingdom-gold"></i></div>
                                                         <h4 class="text-[13px] font-bold text-slate-800 dark:text-white uppercase tracking-wider">Shift Details</h4>
                                                     </div>
                                                     <div class="space-y-3 text-[13px]">
                                                         <div class="flex items-start gap-3">
                                                             <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 mt-0.5 shrink-0"></i>
                                                             <div>
                                                                 <p class="text-[11px] text-slate-400 font-bold uppercase">Venue</p>
                                                                 <p class="font-medium text-slate-700 dark:text-slate-300">{{ $booking->venue ?? 'No venue' }}</p>
                                                             </div>
                                                         </div>
                                                         <div class="flex items-start gap-3">
                                                             <i data-lucide="calendar" class="w-4 h-4 text-slate-400 mt-0.5 shrink-0"></i>
                                                             <div>
                                                                 <p class="text-[11px] text-slate-400 font-bold uppercase">Date</p>
                                                                 <p class="font-medium text-slate-700 dark:text-slate-300">{{ $booking->start_date ? $booking->start_date->format('M d, Y') : 'TBD' }}</p>
                                                             </div>
                                                         </div>
                                                         @if($booking->special_requirements)
                                                         <div class="flex items-start gap-3">
                                                             <i data-lucide="info" class="w-4 h-4 text-slate-400 mt-0.5 shrink-0"></i>
                                                             <div>
                                                                 <p class="text-[11px] text-slate-400 font-bold uppercase">Notes</p>
                                                                 <p class="font-medium text-slate-600 dark:text-slate-400 italic">{{ $booking->special_requirements }}</p>
                                                             </div>
                                                         </div>
                                                         @endif
                                                     </div>
                                                 </div>
                                                 
                                                 <!-- Assigned Staff -->
                                                 <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 border-t-[3px] border-t-blue-500 shadow-sm">
                                                     <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                                                         <div class="flex items-center gap-2">
                                                             <div class="p-1.5 bg-blue-50 dark:bg-blue-950/30 rounded-lg"><i data-lucide="users" class="w-4 h-4 text-blue-600"></i></div>
                                                             <h4 class="text-[13px] font-bold text-slate-800 dark:text-white uppercase tracking-wider">Assigned Staff</h4>
                                                         </div>
                                                         <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $assignedCount >= ($booking->quantity ?? 1) ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">{{ $assignedCount }}/{{ $booking->quantity ?? 0 }}</span>
                                                     </div>
                                                     @if($assignedCount > 0)
                                                     <div class="space-y-2">
                                                         @foreach($booking->events as $ev)
                                                             @foreach($ev->assignments->whereIn('status', ['Assigned','Confirmed']) as $a)
                                                             <div class="flex items-center gap-3 p-2 rounded-lg border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                                                 <x-avatar :user="$a->applicant" />
                                                                 <div class="flex-1 min-w-0">
                                                                     <p class="text-[12px] font-bold text-slate-800 dark:text-white truncate">{{ $a->applicant->name ?? 'Staff' }}</p>
                                                                     <div class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase">Confirmed</span></div>
                                                                 </div>
                                                             </div>
                                                             @endforeach
                                                         @endforeach
                                                     </div>
                                                     @else
                                                     <div class="text-center py-6">
                                                         <i data-lucide="clock" class="w-8 h-8 text-slate-300 dark:text-slate-700 mx-auto mb-2"></i>
                                                         <p class="text-[12px] font-bold text-slate-500 dark:text-slate-400">Pending Assignment</p>
                                                         <p class="text-[11px] text-slate-400 dark:text-slate-500">Staff will be assigned once approved.</p>
                                                     </div>
                                                     @endif
                                                 </div>
                                             </div>
                                         </td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     </div>
                 </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="flex-1 min-h-0 flex flex-col items-center justify-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center"><i data-lucide="list" class="w-8 h-8 text-slate-400"></i></div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No shifts {{ $eventFilter ? 'for this event' : 'yet' }}</h3>
        <p class="text-sm text-slate-500 mb-6">{{ $eventFilter ? 'No shifts found for "' . $eventFilter . '".' : 'Shifts will appear here once you book staff.' }}</p>
        <a href="{{ route('partner.book-staff') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0F1C3F] hover:bg-[#1a2b5e] text-white rounded-xl font-bold text-sm transition-all shadow-lg"><i data-lucide="plus-circle" class="w-5 h-5"></i> New Booking</a>
    </div>
    @endif
</div>
@endsection


