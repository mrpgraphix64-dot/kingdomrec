@extends('layouts.admin')

@section('title', 'Staff Bookings & Quotations | Kingdom Admin')

@section('content')
@php
    // Build category map once to avoid N+1 queries in the loop
    $categoryMap = [];
    foreach ($categories as $cat) {
        foreach ($cat->subCategories as $sub) {
            $categoryMap[$sub->name] = $cat->name;
        }
    }
@endphp

<style>
    .stat-card-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
    .stat-tile {
        background-color: #ffffff;
        border: 1px solid #dce2ea;
        border-radius: 12px;
        padding: clamp(16px, 2.5vw, 24px);
        transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 220ms cubic-bezier(0.16, 1, 0.3, 1), border-color 220ms ease;
        text-align: left;
        width: 100%;
        display: block;
        cursor: pointer;
    }
    .stat-tile:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -5px rgba(15, 29, 51, 0.08), 0 4px 10px -3px rgba(15, 29, 51, 0.04);
    }
    .stat-tile.is-active {
        ring: 2px solid #0F1D33;
        box-shadow: 0 4px 12px rgba(15, 29, 51, 0.12);
    }
    .stat-tile-label {
        font-size: clamp(10px, 1.2vw, 12px);
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: block;
    }
    .stat-tile-value {
        font-size: clamp(22px, 3vw, 28px);
        font-weight: 900;
        color: #0f172a;
        margin-top: 6px;
        display: block;
    }
    .main-card {
        background-color: #ffffff;
        border: 1px solid #dce2ea;
        border-radius: 12px;
        padding: 0px !important;
        overflow: hidden;
    }
    .main-table {
        width: 100%;
        border-collapse: collapse;
    }
    .main-table th {
        background-color: #1a2332;
        color: #ffffff;
        font-size: clamp(11px, 1.2vw, 12px);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        height: 44px;
        padding: 6px clamp(12px, 1.5vw, 18px);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid #e4e9f0;
    }
    .main-table th:last-child {
        border-right: none;
    }
    .main-table td {
        font-size: clamp(12px, 1.3vw, 14px);
        padding: clamp(10px, 1.3vw, 14px) clamp(12px, 1.5vw, 18px);
        height: 52px;
        border-right: 1px solid #e4e9f0;
        border-bottom: 1px solid #e4e9f0;
        vertical-align: middle;
    }
    .main-table td:last-child {
        border-right: none;
    }
    .main-table-row:hover td {
        background-color: #eef4ff !important;
    }
    .expand-row td {
        padding: 0px !important;
        border-right: none !important;
        border-bottom: 1px solid #e4e9f0 !important;
    }
    .expand-container {
        background-color: #f0f3f7 !important;
        padding: clamp(14px, 2vw, 20px) !important;
        border-top: 2px solid #dce2ea !important;
    }
    .inner-shifts-table th {
        background-color: #1a2332;
        color: #ffffff;
        font-size: clamp(10px, 1.2vw, 11px);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: clamp(6px, 0.8vw, 10px) clamp(8px, 1vw, 12px);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }
    .inner-shifts-table th:last-child {
        border-right: none;
    }
    .inner-shifts-table td {
        font-size: clamp(11px, 1.3vw, 13px);
        padding: clamp(8px, 1vw, 12px);
        border-right: 1px solid #e4e9f0;
        border-bottom: 1px solid #e4e9f0;
    }
    .inner-shifts-table td:last-child {
        border-right: none;
    }
    .inner-shifts-table tr:hover td {
        background-color: #eef4ff !important;
    }
    .inner-shifts-table-footer {
        background-color: #e8ecf1 !important;
        border-top: 2px solid #cdd4de !important;
        font-weight: 700 !important;
    }
    .inner-shifts-table-footer td {
        border-top: 2px solid #cdd4de !important;
        color: #1a2332;
    }
    .badge-standard {
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        padding: 2px 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Highlight/outline for expanded quotation group */
    .main-table-row.is-expanded td {
        border-top: 2px solid #94a3b8 !important;
        border-bottom: none !important;
        background-color: #f8fafc !important;
    }
    .main-table-row.is-expanded td:last-child {
        border-right: 2px solid #94a3b8 !important;
    }
    .expand-row.is-expanded td {
        border-right: 2px solid #94a3b8 !important;
        border-bottom: 2px solid #94a3b8 !important;
    }
</style>

<div class="w-full flex flex-col space-y-5" x-data="quotationTable()">
    
    <!-- Header Title + Actions -->
    <div class="flex flex-wrap items-center justify-between gap-3 pb-1">
        <div>
            <h1 class="text-[22px] font-black text-slate-900 uppercase tracking-wider">Staff Bookings & Quotations</h1>
            <p class="text-[13px] text-slate-500 mt-1">Manage client event bookings, shifts & approvals</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search Bar -->
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" x-model="searchQuery" @keyup.enter="applyFilters()" placeholder="Search bookings..."
                    class="pl-9 pr-9 py-2 w-52 bg-white border border-[#dce2ea] rounded-xl focus:outline-none focus:ring-1 focus:ring-slate-400 text-[13px] font-medium text-slate-700 transition-all">
                <button x-show="searchQuery" @click="clearFilters()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            
            <!-- Status Filter Dropdown (Ghost Style) -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2 px-3 py-2 bg-transparent text-slate-600 hover:bg-slate-200/50 hover:text-slate-950 rounded-xl text-[13px] font-bold transition-all border border-transparent hover:border-slate-200">
                    <i data-lucide="filter" class="w-4 h-4 text-slate-400"></i>
                    <span x-text="statusFilter ? statusFilter : 'All Status'"></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition x-cloak class="absolute right-0 mt-1.5 z-30 w-48 bg-white border border-[#dce2ea] rounded-xl shadow-lg py-1" style="display:none;">
                    <button @click="statusFilter = ''; open = false; applyFilters()" class="w-full text-left px-4 py-2 text-[13px] hover:bg-slate-50 flex items-center justify-between" :class="!statusFilter ? 'font-bold text-[#B89955]' : 'text-slate-600'">
                        <span>All Bookings</span>
                        <span class="text-xs text-slate-400">({{ $statTotalBookings ?? 0 }})</span>
                    </button>
                    @foreach(['Approved','Confirmed','Active','Pending','Draft','Rejected'] as $s)
                    <button @click="statusFilter = '{{ $s }}'; open = false; applyFilters()" class="w-full text-left px-4 py-2 text-[13px] hover:bg-slate-50" :class="statusFilter.toLowerCase() === '{{ strtolower($s) }}' ? 'font-bold text-[#B89955]' : 'text-slate-600'">{{ $s }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Select Mode (Secondary Outlined Style) -->
            <button @click="if(selectMode) { selectedRows = []; selectAll = false; } selectMode = !selectMode" 
                    class="flex items-center gap-2 border border-[#dce2ea] bg-white text-slate-600 hover:bg-slate-50 rounded-xl px-4 py-2 text-[13px] font-bold transition-all"
                    :class="selectMode ? 'border-indigo-600 bg-indigo-50/30 text-indigo-700' : ''">
                <i data-lucide="check-square" class="w-4 h-4" :class="selectMode ? 'text-indigo-600' : 'text-slate-400'"></i>
                <span x-text="selectMode ? 'Cancel Select' : 'Select'"></span>
            </button>

            <!-- Export (Secondary Outlined Style) -->
            <button @click="openExport = true" class="flex items-center gap-2 border border-[#dce2ea] bg-white text-slate-600 hover:bg-slate-50 rounded-xl px-4 py-2 text-[13px] font-bold transition-all">
                <i data-lucide="download" class="w-4 h-4 text-slate-400 font-semibold"></i>
                <span>Export</span>
            </button>

            <!-- New Booking (Primary CTA Style - filled dark button) -->
            <button @click="openCreateModal()" class="flex items-center gap-2 bg-[#0F1D33] text-white hover:bg-slate-800 rounded-xl px-4 py-2 text-[13px] font-bold transition-all shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Booking</span>
            </button>
        </div>
    </div>

    <!-- Stat Summary Cards (Interactive Filters) -->
    <div class="stat-card-grid">
        <!-- Total Bookings -->
        <button type="button" @click="statusFilter = ''; applyFilters()" 
                class="stat-tile group" 
                style="border-left: 4px solid #3b82f6;"
                :class="{ 'ring-2 ring-blue-500 bg-blue-50/20': !statusFilter && !searchQuery }">
            <span class="stat-tile-label flex items-center justify-between">
                <span>Total Bookings</span>
                <i data-lucide="list" class="w-3.5 h-3.5 text-blue-500 opacity-60 group-hover:opacity-100 transition-opacity"></i>
            </span>
            <span class="stat-tile-value">{{ $statTotalBookings ?? 0 }}</span>
        </button>
        <!-- Active & Approved -->
        <button type="button" @click="statusFilter = 'Approved'; applyFilters()" 
                class="stat-tile group" 
                style="border-left: 4px solid #10b981;"
                :class="{ 'ring-2 ring-emerald-500 bg-emerald-50/20': ['approved','confirmed','active'].includes((statusFilter || '').toLowerCase()) }">
            <span class="stat-tile-label flex items-center justify-between">
                <span>Approved & Active</span>
                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500 opacity-60 group-hover:opacity-100 transition-opacity"></i>
            </span>
            <span class="stat-tile-value">{{ $statActiveApproved ?? 0 }}</span>
        </button>
        <!-- Pending Bookings -->
        <button type="button" @click="statusFilter = 'Pending'; applyFilters()" 
                class="stat-tile group" 
                style="border-left: 4px solid #f59e0b;"
                :class="{ 'ring-2 ring-amber-500 bg-amber-50/20': ['pending','draft'].includes((statusFilter || '').toLowerCase()) }">
            <span class="stat-tile-label flex items-center justify-between">
                <span>Pending & Drafts</span>
                <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-500 opacity-60 group-hover:opacity-100 transition-opacity"></i>
            </span>
            <span class="stat-tile-value">{{ $statPending ?? 0 }}</span>
        </button>
        <!-- Estimated Value -->
        <div class="stat-tile" style="border-left: 4px solid #6366f1; cursor: default;">
            <span class="stat-tile-label flex items-center justify-between">
                <span>Estimated Value</span>
                <i data-lucide="banknote" class="w-3.5 h-3.5 text-indigo-500 opacity-60"></i>
            </span>
            <span class="stat-tile-value">£{{ number_format($statTotalValue ?? 0, 2) }}</span>
        </div>
    </div>

    <!-- Active Filter Status Bar -->
    @if(request()->filled('status') || request()->filled('search'))
        <div class="bg-amber-50/80 border border-amber-200 rounded-xl px-4 py-2.5 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-amber-900 font-semibold">
                <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0"></i>
                <span>
                    Filtered by: 
                    @if(request()->filled('status'))
                        <strong>Status: {{ ucfirst(request('status')) }}</strong>
                    @endif
                    @if(request()->filled('search'))
                        @if(request()->filled('status')) &bull; @endif
                        <strong>Search: "{{ request('search') }}"</strong>
                    @endif
                    &mdash; showing <strong>{{ $eventBookings->total() }}</strong> result(s)
                </span>
            </div>
            <button type="button" @click="clearFilters()" class="text-xs font-bold text-amber-900 hover:text-amber-950 underline flex items-center gap-1 shrink-0">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                <span>Show All ({{ $statTotalBookings ?? 0 }})</span>
            </button>
        </div>
    @endif

    <!-- Main Table Card -->
    <div class="main-card">
        <!-- Select Mode Info Bar (acts as the toolbar with 16px 20px padding) -->
        <div x-show="selectMode" x-cloak class="border-b border-[#dce2ea]" style="padding: 16px 20px; background-color: #f8fafc;" style="display:none;">
            <div class="flex items-center gap-3 bg-indigo-50/50 border border-indigo-100 rounded-lg p-3">
                <input type="checkbox" @change="toggleSelectAll()" x-model="selectAll" id="selectAllCheckbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                <label for="selectAllCheckbox" class="text-[13px] font-bold text-indigo-900 cursor-pointer">Select All Bookings on Current Page</label>
                <span class="text-xs text-indigo-500 font-semibold" x-text="'(' + selectedRows.length + ' selected)'"></span>
            </div>
        </div>

        @if($eventBookings->count() > 0)
        <div class="overflow-x-auto">
            <table class="main-table text-left">
                <colgroup>
                    <col style="width: 12%;">
                    <col style="width: 15%;">
                    <col style="width: 25%;">
                    <col style="width: 18%;">
                    <col style="width: 8%;">
                    <col style="width: 10%;">
                    <col style="width: 12%;">
                </colgroup>
                <thead>
                    <tr class="main-table-header">
                        <th>Reference</th>
                        <th>Client / Partner</th>
                        <th>Event Name</th>
                        <th>Date Range</th>
                        <th class="text-center">Shifts</th>
                        <th class="text-right">Total Cost</th>
                        <th>Status & Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#d1d9e0]">
                    @foreach($eventBookings as $booking)
                        @php
                            $cost = 0;
                            foreach ($booking->shifts as $s) {
                                $cost += floatval(preg_replace('/[^0-9.]/', '', $s->amount));
                            }
                            $statusString = strtolower($booking->status ?? 'pending');
                            
                            // Status Left Accent Border
                            $leftAccentStyle = match($statusString) {
                                'approved', 'confirmed', 'active' => 'border-left: 4px solid #10b981;',
                                'rejected' => 'border-left: 4px solid #ef4444;',
                                default => 'border-left: 4px solid #f59e0b;',
                            };
                            
                            $statusColor = match($statusString) {
                                'approved', 'confirmed', 'active' => '#10b981',
                                'rejected' => '#ef4444',
                                default => '#f59e0b',
                            };
                            
                            $badgeClass = match($statusString) {
                                'approved', 'confirmed', 'active' => 'bg-[#dcfce7] text-[#166534]',
                                'rejected' => 'bg-[#fee2e2] text-[#991b1b]',
                                default => 'bg-[#fef3c7] text-[#92400e]',
                            };
                        @endphp
                        
                        <!-- Main Booking Row -->
                        <tr class="main-table-row cursor-pointer transition-colors"
                            :class="{ 'bg-indigo-50/20 ring-1 ring-indigo-500/20': isSelected({{ $booking->id }}, 'booking'), 'is-expanded': isBookingExpanded({{ $booking->id }}) }"
                            style="{{ $loop->index % 2 === 0 ? 'background-color: #ffffff;' : 'background-color: #f5f7fa;' }}"
                            @click="selectMode ? toggleRowSelection({{ $booking->id }}, 'booking') : toggleBooking({{ $booking->id }})">
                            
                            <!-- Col 1: Ref & Date (with Left Accent applied directly to td style) -->
                            <td style="{{ $leftAccentStyle }}">
                                <div class="flex items-center gap-3">
                                    <div x-show="selectMode" class="flex items-center" @click.stop>
                                        <input type="checkbox" :value="{{ $booking->id }}" @change="toggleRowSelection({{ $booking->id }}, 'booking')" :checked="isSelected({{ $booking->id }}, 'booking')" class="rounded border-slate-300 text-kingdom-gold focus:ring-kingdom-gold w-4 h-4 cursor-pointer">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 font-mono tracking-wide">{{ $booking->booking_ref }}</span>
                                        <span class="text-[10px] text-slate-400 mt-0.5">{{ $booking->created_at ? $booking->created_at->format('M d, Y') : '' }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Col 2: Client -->
                            <td>
                                <span class="text-[13px] text-slate-700 font-semibold truncate block">{{ $booking->client ?: 'Guest' }}</span>
                            </td>
                            
                            <!-- Col 3: Event Name -->
                            <td>
                                <span class="text-[13px] text-slate-800 font-semibold block truncate" title="{{ $booking->event_name ?: 'Unnamed Event' }}">{{ $booking->event_name ?: 'Unnamed Event' }}</span>
                            </td>
                            
                            <!-- Col 4: Date Range -->
                            <td>
                                <span class="text-[13px] text-slate-600 font-semibold block">
                                    {{ $booking->start_date ? $booking->start_date->format('M d, Y') : '—' }}
                                    @if($booking->end_date && $booking->end_date != $booking->start_date)
                                        <span class="text-slate-400 text-xs">to</span> {{ $booking->end_date->format('M d, Y') }}
                                    @endif
                                </span>
                            </td>
                            
                            <!-- Col 5: Shifts Count -->
                            <td class="text-center">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs font-bold">{{ $booking->shifts->count() }}</span>
                            </td>
                            
                            <!-- Col 6: Total Cost -->
                            <td class="text-right">
                                <span class="text-[15px] font-bold text-slate-900">£{{ number_format($cost, 2) }}</span>
                            </td>
                            
                            <!-- Col 7: Status & Chevron -->
                            <td>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="badge-standard {{ $badgeClass }}">
                                        {{ $booking->status ?? 'Pending' }}
                                    </span>
                                    
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="isBookingExpanded({{ $booking->id }}) ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Expanded Detail Row -->
                        <tr class="expand-row" x-show="isBookingExpanded({{ $booking->id }})" x-cloak
                            :class="{ 'is-expanded': isBookingExpanded({{ $booking->id }}) }">
                            <td colspan="7" style="border-left: 4px solid {{ $statusColor }} !important;">
                                <div class="expand-container" x-show="isBookingExpanded({{ $booking->id }})" x-collapse>
                                    <div class="space-y-5">
                                        <!-- Expanded Header Actions -->
                                        <div class="flex justify-between items-center pb-3 border-b border-[#dce2ea]">
                                            <h3 class="text-[12px] font-bold text-slate-600 uppercase tracking-wider">Booking Action & Details</h3>
                                            <div class="flex items-center gap-2" @click.stop>
                                                 <!-- Quick Approval Form Actions -->
                                                 @if($statusString === 'pending' || $statusString === 'draft')
                                                     <form action="{{ route('admin.staff-quotation.update', $booking->id) }}" method="POST" class="inline">
                                                         @csrf
                                                         @method('PUT')
                                                         <input type="hidden" name="type" value="booking">
                                                         <input type="hidden" name="client" value="{{ $booking->client }}">
                                                         <input type="hidden" name="event_name" value="{{ $booking->event_name }}">
                                                         <input type="hidden" name="venue" value="{{ $booking->venue }}">
                                                         <input type="hidden" name="start_date" value="{{ $booking->start_date ? $booking->start_date->format('Y-m-d') : '' }}">
                                                         <input type="hidden" name="end_date" value="{{ $booking->end_date ? $booking->end_date->format('Y-m-d') : '' }}">
                                                         <input type="hidden" name="special_requirements" value="{{ $booking->special_requirements }}">
                                                         <input type="hidden" name="status" value="Approved">
                                                         <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold hover:bg-emerald-100 transition-colors shadow-sm">
                                                             <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Approve
                                                         </button>
                                                     </form>
                                                 @endif
    
                                                 @if(!in_array($statusString, ['approved', 'confirmed', 'active', 'billed']))
                                                     <button @click="editBooking({{ json_encode($booking) }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#dce2ea] text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-50 transition-colors shadow-sm">
                                                         <i data-lucide="edit-3" class="w-3.5 h-3.5 text-slate-400"></i> Edit
                                                     </button>
                                                     <button @click="confirmDelete('{{ route('admin.staff-quotation.destroy', $booking->id) }}', 'booking')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 border border-red-100 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors shadow-sm">
                                                         <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-500"></i> Delete
                                                     </button>
                                                 @endif
                                            </div>
                                        </div>
    
                                        <!-- Clean 4-Column Metadata Grid (white tiles on f0f3f7 background) -->
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <!-- Venue -->
                                            <div class="bg-white border border-[#dce2ea] rounded-[8px] p-4 flex flex-col justify-between">
                                                <div class="text-slate-500 shrink-0 mb-2">
                                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Venue / Location</span>
                                                    <span class="block text-[16px] font-bold text-slate-800 mt-1">{{ $booking->venue ?: '—' }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Event Dates -->
                                            <div class="bg-white border border-[#dce2ea] rounded-[8px] p-4 flex flex-col justify-between">
                                                <div class="text-slate-500 shrink-0 mb-2">
                                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Event Dates</span>
                                                    <span class="block text-[16px] font-bold text-slate-800 mt-1">
                                                        {{ $booking->start_date ? $booking->start_date->format('M d, Y') : '—' }}
                                                        @if($booking->end_date && $booking->end_date != $booking->start_date)
                                                            <span class="text-slate-400 font-normal text-xs">to</span> {{ $booking->end_date->format('M d, Y') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
    
                                            <!-- Total Shifts -->
                                            <div class="bg-white border border-[#dce2ea] rounded-[8px] p-4 flex flex-col justify-between">
                                                <div class="text-slate-500 shrink-0 mb-2">
                                                    <i data-lucide="layers" class="w-4 h-4"></i>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Shifts</span>
                                                    <span class="block text-[16px] font-bold text-slate-800 mt-1">{{ $booking->shifts->count() }} {{ Str::plural('shift', $booking->shifts->count()) }}</span>
                                                </div>
                                            </div>
    
                                            <!-- Estimated Budget -->
                                            <div class="bg-white border border-[#dce2ea] rounded-[8px] p-4 flex flex-col justify-between">
                                                <div class="text-slate-500 shrink-0 mb-2">
                                                    <i data-lucide="banknote" class="w-4 h-4"></i>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Estimated Budget</span>
                                                    <span class="block text-[16px] font-bold text-indigo-600 mt-1">£{{ number_format($cost, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
    
                                        <!-- Special Requirements callout box -->
                                        @if($booking->special_requirements)
                                            <div class="flex items-start gap-3 p-[12px_16px] bg-[#fffbeb] border-l-[3px] border-[#f59e0b] rounded-[0_6px_6px_0]">
                                                <i data-lucide="alert-triangle" class="w-5 h-5 text-[#f59e0b] mt-0.5 shrink-0"></i>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-[#b45309] uppercase tracking-wider">Special Requirements / Post Orders</span>
                                                    <p class="text-[13px] text-[#78350f] mt-1 font-semibold leading-relaxed">{{ $booking->special_requirements }}</p>
                                                </div>
                                            </div>
                                        @endif
    
                                        <!-- Child Shift Table -->
                                        <div class="overflow-x-auto rounded-lg border border-[#dce2ea] bg-white">
                                            <table class="inner-shifts-table text-left table-fixed w-full min-w-[900px]">
                                                <colgroup>
                                                    <col style="width: 4%;">
                                                    <col style="width: 15%;">
                                                    <col style="width: 18%;">
                                                    <col style="width: 7%;">
                                                    <col style="width: 10%;">
                                                    <col style="width: 7%;">
                                                    <col style="width: 10%;">
                                                    <col style="width: 11%;">
                                                    <col style="width: 10%;">
                                                    <col style="width: 11%;">
                                                </colgroup>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th>Staff Category</th>
                                                        <th>Staff Type / Role</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-center">Shift Hours</th>
                                                        <th class="text-center">Days</th>
                                                        <th class="text-right">Rate Nett</th>
                                                        <th class="text-right">Amount Gross</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Scheduling Event</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-[#dce2ea]">
                                                    @foreach($booking->shifts as $sIdx => $shift)
                                                        @php
                                                            $shRate = floatval(preg_replace('/[^0-9.]/', '', $shift->rate));
                                                            $shAmt = floatval(preg_replace('/[^0-9.]/', '', $shift->amount));
                                                            $shStatus = strtolower($shift->status ?? 'pending');
                                                            $shBadge = match($shStatus) {
                                                                'approved', 'confirmed', 'active' => 'bg-[#dcfce7] text-[#166534]',
                                                                'rejected' => 'bg-[#fee2e2] text-[#991b1b]',
                                                                default => 'bg-[#fef3c7] text-[#92400e]',
                                                            };
                                                            $shHours = floatval($shift->shift_hours) > 0 ? floatval($shift->shift_hours) : 8.0;
                                                            $shDays = intval($shift->total_days) > 0 ? intval($shift->total_days) : 1;
                                                            $shiftCategory = $shift->category ?: ($shift->sub_category ? ($categoryMap[$shift->sub_category] ?? 'General') : 'Not Specified');
                                                            
                                                            // Category Badge visual styles
                                                            $categoryLower = strtolower($shiftCategory);
                                                            $categoryBadgeClass = match(true) {
                                                                str_contains($categoryLower, 'sia') || str_contains($categoryLower, 'security') => 'bg-[#dbeafe] text-[#1e40af]',
                                                                str_contains($categoryLower, 'hospitality') || str_contains($categoryLower, 'bar') || str_contains($categoryLower, 'wait') => 'bg-orange-50 text-orange-700 border border-orange-200',
                                                                str_contains($categoryLower, 'management') || str_contains($categoryLower, 'supervisor') => 'bg-purple-50 text-purple-705 border border-purple-200',
                                                                default => 'bg-slate-100 text-slate-700 border border-slate-200',
                                                            };
                                                        @endphp
                                                        <tr style="{{ $sIdx % 2 === 0 ? 'background-color: #ffffff;' : 'background-color: #f5f7fa;' }}">
                                                            <td class="text-center text-[13px] text-slate-400 font-semibold border-r border-[#dce2ea]">{{ $sIdx + 1 }}</td>
                                                            <td class="border-r border-[#dce2ea]">
                                                                <span class="badge-standard {{ $categoryBadgeClass }}">
                                                                    {{ $shiftCategory }}
                                                                </span>
                                                            </td>
                                                            <td class="text-[13px] text-slate-800 font-bold border-r border-[#dce2ea]">{{ $shift->sub_category ?: 'Not Specified' }}</td>
                                                            <td class="text-center text-[13px] font-bold text-slate-700 border-r border-[#dce2ea]">{{ $shift->quantity }}</td>
                                                            <td class="text-center text-[13px] text-slate-600 font-semibold border-r border-[#dce2ea]">{{ $shHours }} hrs</td>
                                                            <td class="text-center text-[13px] text-slate-600 font-semibold border-r border-[#dce2ea]">{{ $shDays }} {{ $shDays > 1 ? 'days' : 'day' }}</td>
                                                            <td class="text-right font-sans font-semibold tabular-nums text-[13px] text-slate-500 border-r border-[#dce2ea]">£{{ number_format($shRate, 2) }}</td>
                                                            <td class="text-right font-sans font-extrabold tabular-nums text-[13px] text-slate-900 border-r border-[#dce2ea]">£{{ number_format($shAmt, 2) }}</td>
                                                            <td class="text-center border-r border-[#dce2ea]">
                                                                <span class="badge-standard {{ $shBadge }}">
                                                                    {{ ucfirst($shift->status ?? 'Pending') }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                @if($shift->events()->exists())
                                                                    <span class="text-emerald-600 font-bold text-[13px] inline-flex items-center justify-center gap-1">
                                                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 shrink-0"></i> Generated
                                                                    </span>
                                                                @else
                                                                    <span class="text-slate-400 text-[13px] inline-flex items-center justify-center gap-1">
                                                                        <i data-lucide="help-circle" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i> Pending
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="inner-shifts-table-footer font-bold text-[13px]">
                                                        <td colspan="3" class="py-2.5 px-4 text-right text-slate-500 uppercase tracking-wider text-[11px] border-r border-[#dce2ea]">Total:</td>
                                                        <td class="py-2.5 px-4 text-center text-slate-900 font-black tabular-nums border-r border-[#dce2ea]">{{ $booking->shifts->sum('quantity') }}</td>
                                                        <td class="py-2.5 px-4 text-center text-slate-500 border-r border-[#dce2ea]">—</td>
                                                        <td class="py-2.5 px-4 text-center text-slate-500 border-r border-[#dce2ea]">—</td>
                                                        <td class="py-2.5 px-4 text-right text-slate-500 border-r border-[#dce2ea]">—</td>
                                                        <td class="py-2.5 px-4 text-right text-slate-900 font-black tabular-nums border-r border-[#dce2ea]">£{{ number_format($cost, 2) }}</td>
                                                        <td colspan="2" class="py-2.5 px-4"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- Empty State — a real full-width block, not a colspan cell inside the wide scrolling table -->
        <div class="px-4 py-10 text-center bg-white">
            <div class="flex flex-col items-center justify-center max-w-md mx-auto py-2">
                <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3 border border-slate-200 shadow-sm">
                    <i data-lucide="calendar-x" class="w-6 h-6 text-slate-400"></i>
                </div>
                @if(request()->filled('status') || request()->filled('search'))
                    @php
                        $selectedStatus = strtolower(request('status', ''));
                    @endphp
                    @if(($selectedStatus === 'pending' || $selectedStatus === 'draft') && ($statActiveApproved ?? 0) > 0)
                        <h3 class="text-sm font-extrabold text-slate-900">No Pending Bookings Found</h3>
                        <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                            You have <strong class="text-emerald-700 font-bold">{{ $statActiveApproved }} active/approved {{ Str::plural('booking', $statActiveApproved) }}</strong> in the system.
                        </p>
                        <div class="flex flex-wrap items-center justify-center gap-2.5 mt-4">
                            <button type="button" @click="statusFilter = ''; applyFilters()" class="px-4 py-2 rounded-xl bg-[#0F1D33] text-white hover:bg-slate-800 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                                <i data-lucide="list" class="w-3.5 h-3.5"></i>
                                <span>Show All Bookings ({{ $statTotalBookings ?? 0 }})</span>
                            </button>
                            <button type="button" @click="statusFilter = 'Approved'; applyFilters()" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 text-xs font-bold transition-all flex items-center gap-1.5">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                <span>View Approved Bookings ({{ $statActiveApproved }})</span>
                            </button>
                        </div>
                    @else
                        <h3 class="text-sm font-extrabold text-slate-900">No Bookings Match Selected Filters</h3>
                        <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                            No bookings match the selected status or search term. There are <strong>{{ $statTotalBookings ?? 0 }} total bookings</strong> available.
                        </p>
                        <div class="flex items-center justify-center gap-2 mt-4">
                            <button type="button" @click="clearFilters()" class="px-4 py-2 rounded-xl bg-[#0F1D33] text-white hover:bg-slate-800 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>Clear Filters</span>
                            </button>
                        </div>
                    @endif
                @else
                    <h3 class="text-sm font-extrabold text-slate-900">No Bookings Created Yet</h3>
                    <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                        Start by creating a new client event booking or staff quotation.
                    </p>
                    <div class="mt-4">
                        <button type="button" @click="openCreateModal()" class="px-4 py-2 rounded-xl bg-[#0F1D33] text-white hover:bg-slate-800 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Create New Booking</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
        @endif

        @if($eventBookings->hasPages())
        <!-- Pagination Links -->
        <div class="mt-6 pt-4 border-t border-[#dce2ea] shrink-0" style="padding: 16px 20px;">
            {{ $eventBookings->links() }}
        </div>
        @endif
    </div>

    <!-- Create Booking Modal (Primary CTA "+ New Booking") -->
    <div x-show="createModalOpen" x-cloak style="display:none;" class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="createModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="createModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4" @click="createModalOpen = false">
            <div x-show="createModalOpen" @click.stop x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl transform rounded-xl bg-white shadow-xl border border-slate-200 overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <div>
                        <h3 class="font-bold text-[15px] text-slate-900 uppercase tracking-wider">Create New Grouped Booking</h3>
                        <p class="text-xs text-slate-550 mt-0.5">Initialize a client booking request and first shift slot.</p>
                    </div>
                    <button @click="createModalOpen = false" class="bg-white p-1 rounded-full text-slate-400 hover:text-slate-650 hover:bg-slate-100 border border-slate-200 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form action="{{ route('admin.staff-quotation.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4 bg-slate-50/50 p-4 rounded-lg border border-slate-200">
                        <div class="col-span-2 mb-1">
                            <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">1. General Info & Timing</h4>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Client / Partner Name</label>
                            <select name="client" x-model="createForm.client" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                                <option value="">Select Client / Partner</option>
                                <template x-for="p in partnersData" :key="p.id">
                                    <option :value="p.company_name || p.name" x-text="p.company_name || p.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Event / Job Name</label>
                            <input type="text" name="event_name" x-model="createForm.event_name" placeholder="e.g. London Gala Security" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                        </div>

                        <div class="space-y-1.5 col-span-2">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Venue / Location</label>
                            <input type="text" name="venue" x-model="createForm.venue" placeholder="e.g. O2 Arena, London" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Start Date</label>
                            <input type="date" name="start_date" x-model="createForm.start_date" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">End Date</label>
                            <input type="date" name="end_date" x-model="createForm.end_date" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400">
                        </div>

                        <div class="space-y-1.5 col-span-2">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Special Requirements / Notes</label>
                            <textarea name="special_requirements" x-model="createForm.special_requirements" placeholder="Add operational notes or specs..." rows="2" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400 resize-none"></textarea>
                        </div>

                        <div class="space-y-1.5 col-span-2">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Status</label>
                            <select name="status" x-model="createForm.status" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-bold outline-none focus:border-slate-400">
                                <option value="Pending">Pending</option>
                                <option value="Draft">Draft</option>
                                <option value="Approved">Approved</option>
                                <option value="Sent">Sent</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Active">Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-slate-50/50 p-4 rounded-lg border border-slate-200">
                        <div class="col-span-2 mb-1">
                            <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">2. First Shift Configuration</h4>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Staff Type (Sub Category)</label>
                            <select name="sub_category" x-model="createForm.sub_category" @change="recalculateCreateAmount()" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                                <option value="">Select Role</option>
                                <template x-for="subOpt in allSubCategories" :key="subOpt.name">
                                    <option :value="subOpt.name" x-text="subOpt.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Shift Label / Time Option</label>
                            <input type="text" name="shift" x-model="createForm.shift" placeholder="e.g. Day Shift, Night Shift" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Staff Quantity</label>
                            <input type="number" name="quantity" x-model="createForm.quantity" @input="recalculateCreateAmount()" min="1" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Shift Hours</label>
                            <input type="number" step="0.5" name="shift_hours" x-model="createForm.shift_hours" @input="recalculateCreateAmount()" min="0.5" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Rate Nett (&pound;)</label>
                            <input type="number" step="0.01" name="rate" x-model="createForm.rate" @input="recalculateCreateAmount()" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 pl-0.5">Amount Gross (&pound;)</label>
                            <input type="number" step="0.01" name="amount" x-model="createForm.amount" class="w-full px-3 py-2 rounded-lg bg-slate-100 border border-slate-200 text-xs font-bold outline-none cursor-default" readonly>
                        </div>

                        <input type="hidden" name="category" :value="createForm.category">
                        <input type="hidden" name="total_days" value="1">
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-3 border-t border-slate-200">
                        <button type="button" @click="createModalOpen = false" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[13px] font-bold transition-colors">Cancel</button>
                        <button type="submit" class="flex-[2] py-2.5 bg-[#0F1D33] hover:bg-slate-800 text-white rounded-lg text-[13px] font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i> Create Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal (Handles both Booking and Orphaned Shift types) -->
    <div x-show="editModalOpen" x-cloak style="display:none;" class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="editModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4" @click="editModalOpen = false">
            <div x-show="editModalOpen" @click.stop x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-3xl transform rounded-xl bg-white shadow-xl border border-slate-200 overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <div>
                        <h3 class="font-bold text-[15px] text-slate-900 uppercase tracking-wider">Edit Grouped Booking</h3>
                        <p class="text-xs text-slate-500" x-text="'Ref: ' + editForm.booking_ref"></p>
                    </div>
                    <button @click="editModalOpen = false" class="bg-white p-1 rounded-full text-slate-400 hover:text-slate-650 hover:bg-slate-100 border border-slate-200 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Grouped Booking Edit Form -->
                <template x-if="editType === 'booking'">
                    <form :action="'/admin/staff-quotation/' + editForm.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="type" value="booking">
                        
                        <!-- Event-level metadata -->
                        <div class="grid grid-cols-2 gap-4 bg-slate-50/50 p-4 rounded-lg border border-slate-200">
                            <div class="col-span-2 mb-1">
                                <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Event Details</h4>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 pl-0.5">Client / Partner Name</label>
                                <input type="text" name="client" x-model="editForm.client" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 pl-0.5">Event / Job Name</label>
                                <input type="text" name="event_name" x-model="editForm.event_name" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                            </div>
                            <div class="space-y-1.5 col-span-2">
                                <label class="block text-xs font-bold text-slate-700 pl-0.5">Venue / Location</label>
                                <input type="text" name="venue" x-model="editForm.venue" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 pl-0.5">Start Date</label>
                                <input type="date" name="start_date" x-model="editForm.start_date" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400" required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 pl-0.5">End Date</label>
                                <input type="date" name="end_date" x-model="editForm.end_date" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400">
                            </div>
                            <div class="space-y-1.5 col-span-2">
                                <label class="block text-xs font-bold text-slate-700 pl-0.5">Special Requirements / Notes</label>
                                <textarea name="special_requirements" x-model="editForm.special_requirements" rows="2" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold outline-none focus:border-slate-400 resize-none"></textarea>
                            </div>
                            <div class="space-y-1.5 col-span-2">
                                <label class="block text-xs font-bold text-slate-700 pl-0.5">Booking Status</label>
                                <select name="status" x-model="editForm.status" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs font-bold outline-none focus:border-slate-400">
                                    @foreach(['Draft', 'Pending', 'Sent', 'Approved', 'Rejected', 'Confirmed', 'Active'] as $sOpt)
                                        <option value="{{ $sOpt }}" :disabled="!canTransitionBooking(editForm.initialStatus, '{{ $sOpt }}')">{{ $sOpt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Nested Child Shift Items -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center px-1">
                                <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="layers" class="w-4 h-4 text-indigo-500"></i>
                                    Shift Line Items
                                </h4>
                                <button type="button" @click="addShiftItem()" class="flex items-center gap-1 px-3 py-1 bg-[#0F1D33] text-white rounded-lg text-xs font-bold hover:bg-slate-800 transition shadow-sm">
                                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                    Add Shift
                                </button>
                            </div>
                            
                            <div class="space-y-3 max-h-[250px] overflow-y-auto pr-1">
                                <template x-for="(item, idx) in editForm.items" :key="idx">
                                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl relative space-y-3">
                                        <button type="button" @click="removeShiftItem(idx)" class="absolute top-3 right-3 text-slate-400 hover:text-red-500 transition" title="Remove Shift">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                        
                                        <!-- Hidden ID to track edit vs new -->
                                        <input type="hidden" :name="'items['+idx+'][id]'" :value="item.id">
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold text-slate-550 uppercase">Role / Staff Type</label>
                                                <select :name="'items['+idx+'][sub_category]'" x-model="item.sub_category" @change="updateItemRate(item)" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 outline-none">
                                                    <option value="">Select Role</option>
                                                    <template x-for="subOpt in allSubCategories" :key="subOpt.name">
                                                        <option :value="subOpt.name" x-text="subOpt.name" :selected="item.sub_category === subOpt.name"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold text-slate-550 uppercase">Shift Timing (Label)</label>
                                                <input type="text" :name="'items['+idx+'][shift_label]'" x-model="item.shift_label" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 outline-none" placeholder="e.g. Day Shift, Evening Shift">
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-4 gap-3">
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold text-slate-555 uppercase">Qty</label>
                                                <input type="number" :name="'items['+idx+'][quantity]'" x-model="item.quantity" @input="recalculateItemAmount(item)" min="1" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 outline-none">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold text-slate-555 uppercase">Hours</label>
                                                <input type="number" step="0.5" :name="'items['+idx+'][calculated_hours]'" x-model="item.calculated_hours" @input="recalculateItemAmount(item)" min="0" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 outline-none">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold text-slate-555 uppercase">RateNet (&pound;)</label>
                                                <input type="number" step="0.01" :name="'items['+idx+'][rate]'" x-model="item.rate" @input="recalculateItemAmount(item)" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 outline-none">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold text-slate-555 uppercase">Amount (&pound;)</label>
                                                <input type="number" step="0.01" :name="'items['+idx+'][amount]'" x-model="item.amount" class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 outline-none" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex gap-3 pt-3 border-t border-slate-200">
                            <button type="button" @click="editModalOpen = false" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold transition-colors text-xs">Cancel</button>
                            <button type="submit" class="flex-[2] py-2.5 bg-[#0F1D33] text-white hover:bg-slate-800 rounded-lg font-bold transition-all shadow-sm text-xs flex items-center justify-center gap-1.5">
                                <i data-lucide="save" class="w-4 h-4"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </template>

            </div>
        </div>
    </div>
</div>

<script>
    window.quotationTable = function quotationTable() {
        return {
            searchQuery: '{{ request('search', '') }}',
            statusFilter: '{{ request('status', '') }}',
            categoriesData: @json($categories),
            venuesData: @json($venues),
            partnersData: @json($partners),
            
            expandedBookings: [],
            
            editModalOpen: @json(isset($editing) && $editing ? true : false),
            editType: 'booking',
            
            // Selection variables
            selectMode: false,
            selectedRows: [],
            selectAll: false,
            
            // Export variables
            exportType: 'all',
            exportLimit: 10,
            startDate: '',
            endDate: '',

            // Alpine Create form model
            createModalOpen: false,
            createForm: {
                client: '',
                event_name: '',
                venue: '',
                start_date: '',
                end_date: '',
                special_requirements: '',
                status: 'Pending',
                sub_category: '',
                category: '',
                quantity: 1,
                shift_hours: 8.0,
                rate: 0.0,
                amount: 0.0
            },

            // Alpine Edit form model
            editForm: {
                id: null,
                booking_ref: '',
                client: '',
                event_name: '',
                venue: '',
                start_date: '',
                end_date: '',
                special_requirements: '',
                status: 'Pending',
                initialStatus: 'Pending',
                items: []
            },

            init() {
                // If the server passed an item for editing, set it up
                @if(isset($editing) && $editing && $editingType === 'booking')
                    this.editBooking(@json($editing));
                @endif
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

            clearFilters() {
                this.searchQuery = '';
                this.statusFilter = '';
                this.applyFilters();
            },

            matchBooking(ref, client, eventName, status) {
                const q = this.searchQuery.toLowerCase();
                const matchText = !q ||
                    ref.toLowerCase().includes(q) ||
                    client.toLowerCase().includes(q) ||
                    eventName.toLowerCase().includes(q);
                const matchStatus = !this.statusFilter || status.toLowerCase() === this.statusFilter.toLowerCase();
                return matchText && matchStatus;
            },

            toggleBooking(id) {
                if (this.expandedBookings.includes(id)) {
                    this.expandedBookings = this.expandedBookings.filter(bId => bId !== id);
                } else {
                    this.expandedBookings.push(id);
                }
            },
            
            isBookingExpanded(id) {
                return this.expandedBookings.includes(id);
            },

            // Multi-row selection
            toggleRowSelection(id, type) {
                if (this.selectedRows.includes(id)) {
                    this.selectedRows = this.selectedRows.filter(rowId => rowId !== id);
                } else {
                    this.selectedRows.push(id);
                }
            },
            
            isSelected(id, type) {
                return this.selectedRows.includes(id);
            },

            toggleSelectAll() {
                let allIds = [
                    @foreach($eventBookings as $b)
                        @if($b->id) {{ $b->id }}, @endif
                    @endforeach
                ];

                if (this.selectAll) {
                    this.selectedRows = allIds;
                } else {
                    this.selectedRows = [];
                }
            },

            // Create Booking modal helpers
            openCreateModal() {
                const today = new Date().toISOString().split('T')[0];
                this.createForm = {
                    client: '',
                    event_name: '',
                    venue: '',
                    start_date: today,
                    end_date: today,
                    special_requirements: '',
                    status: 'Pending',
                    sub_category: '',
                    category: '',
                    quantity: 1,
                    shift_hours: 8.0,
                    rate: 0.0,
                    amount: 0.0
                };
                this.createModalOpen = true;
                this.$nextTick(() => { if(window.lucide) lucide.createIcons(); });
            },

            recalculateCreateAmount() {
                const sub = this.allSubCategories.find(s => s.name === this.createForm.sub_category);
                if (sub) {
                    this.createForm.category = sub.category;
                }
                const qty = parseFloat(this.createForm.quantity) || 0;
                const hours = parseFloat(this.createForm.shift_hours) || 0;
                const rate = parseFloat(this.createForm.rate) || 0;
                this.createForm.amount = parseFloat((qty * hours * rate).toFixed(2));
            },

            // Setup edit structures
            editBooking(booking) {
                this.editType = 'booking';
                this.editForm = {
                    id: booking.id,
                    booking_ref: booking.booking_ref || '',
                    client: booking.client || '',
                    event_name: booking.event_name || '',
                    venue: booking.venue || '',
                    start_date: booking.start_date ? booking.start_date.split('T')[0] : '',
                    end_date: booking.end_date ? booking.end_date.split('T')[0] : '',
                    special_requirements: booking.special_requirements || '',
                    status: booking.status || 'Pending',
                    initialStatus: booking.status || 'Pending',
                    items: (booking.shifts || []).map(s => ({
                        id: s.id,
                        sub_category: s.sub_category || '',
                        quantity: s.quantity || 1,
                        calculated_hours: s.calculated_hours || s.shift_hours || 8,
                        rate: s.rate ? parseFloat(String(s.rate).replace(/[^0-9.]/g, '')) : 0,
                        amount: s.amount ? parseFloat(String(s.amount).replace(/[^0-9.]/g, '')) : 0,
                        shift: s.shift || 'custom',
                        shift_label: s.shift_label || 'Custom'
                    }))
                };
                this.editModalOpen = true;
                this.$nextTick(() => { if(window.lucide) lucide.createIcons(); });
            },

            canTransitionBooking(fromStatus, toStatus) {
                const order = ['Draft', 'Pending', 'Sent', 'Approved', 'Rejected', 'Confirmed', 'Active'];
                const fromIdx = order.indexOf(fromStatus);
                const toIdx = order.indexOf(toStatus);
                if (fromIdx === -1 || toIdx === -1) return true;
                return toIdx >= fromIdx;
            },

            // Shifts items management
            addShiftItem() {
                this.editForm.items.push({
                    id: null,
                    sub_category: '',
                    quantity: 1,
                    calculated_hours: 8,
                    rate: 0,
                    amount: 0,
                    shift: 'custom',
                    shift_label: 'Custom'
                });
                this.$nextTick(() => { if(window.lucide) lucide.createIcons(); });
            },

            removeShiftItem(index) {
                this.editForm.items.splice(index, 1);
            },

            updateItemRate(item) {
                this.recalculateItemAmount(item);
            },

            recalculateItemAmount(item) {
                const qty = parseFloat(item.quantity) || 0;
                const hours = parseFloat(item.calculated_hours) || 0;
                const rate = parseFloat(item.rate) || 0;
                item.amount = parseFloat((qty * hours * rate).toFixed(2));
            },

            get allSubCategories() {
                let subs = [];
                this.categoriesData.forEach(c => {
                    if (c.sub_categories) {
                        c.sub_categories.forEach(s => {
                            subs.push({
                                name: s.name,
                                category: c.name
                            });
                        });
                    }
                });
                return subs;
            },

            confirmDelete(url, type) {
                this.$dispatch('open-confirm-modal', {
                    title: 'Delete Event Booking',
                    message: 'Are you sure you want to delete this Event Booking? All associated shifts and slots will be permanently deleted.',
                    onConfirm: () => {
                        this.submitDeleteForm(url, type);
                    }
                });
            },

            submitDeleteForm(url, type) {
                const form = document.createElement('form');
                form.action = url;
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="type" value="${type}">
                `;
                document.body.appendChild(form);
                form.submit();
            },

            triggerExport(format) {
                let url = `{{ route('admin.time-shifting.export') }}?format=${format}&type=${this.exportType}`;
                
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

                if (this.exportType === 'date_range') {
                    if (!this.startDate || !this.endDate) {
                        Swal.fire({ icon: 'warning', title: 'Missing Dates', text: 'Please select both start and end dates.', confirmButtonColor: '#0F1D33' });
                        return;
                    }
                    url += `&start_date=${this.startDate}&end_date=${this.endDate}`;
                }
                
                window.location.href = url;
                this.openExport = false;
            }
        }
    }
</script>
@endsection
