@extends('layouts.partner')

@section('title', 'Time Shifting | Kingdom Partner')

@section('content')
@php
    function generateSheetsFromBookings($bookings) {
        $events = [];
        $grouped = collect($bookings)->groupBy(function($b) {
            return $b->event_name ?: 'Miscellaneous Bookings';
        });

        $idCounter = 1;
        $today = now()->startOfDay();

        foreach($grouped as $eventName => $eventBookings) {
            $rows = [];
            foreach($eventBookings as $b) {
                if ($b->shiftSlots) {
                    foreach($b->shiftSlots as $slot) {
                        if (!$slot->applicant_id) continue; // safety check
                        
                        $status = $slot->status ?? 'Draft';
                        if (in_array($status, ['Unassigned', 'Assigned', 'Pending', 'Checked In'])) {
                            $status = 'Draft';
                        } elseif (in_array($status, ['Checked Out', 'Completed'])) {
                            $status = 'Submitted';
                        }

                        $scheduledTime = ($b->start_time && $b->end_time)
                            ? substr($b->start_time, 0, 5) . ' - ' . substr($b->end_time, 0, 5)
                            : 'Not Scheduled';

                        $rows[] = [
                            'id' => 'row_' . $slot->id,
                            'db_id' => $slot->id, 
                            'role' => $slot->role_name ?? $b->sub_category,
                            'staff_name' => $slot->applicant ? $slot->applicant->name : 'Unassigned',
                            'date' => $slot->shift_date ? $slot->shift_date->format('Y-m-d') : ($b->start_date ? \Carbon\Carbon::parse($b->start_date)->format('Y-m-d') : date('Y-m-d')),
                            'scheduled_time' => $scheduledTime,
                            'start_time' => $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('H:i') : '09:00',
                            'end_time' => $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '17:00',
                            'break_mins' => $slot->break_mins ?? 30,
                            'rate' => (float)($slot->rate ?? $b->rate ?? 15.0),
                            'status' => $status,
                            'booking_ref' => $slot->staffQuotation->eventBooking->booking_ref ?? $slot->staffQuotation->quotation_ref ?? $b->eventBooking->booking_ref ?? $b->quotation_ref ?? '',
                            'event_name' => $eventName,
                            'client' => $slot->staffQuotation->client ?? $b->client ?? '',
                        ];
                    }
                }
            }
            
            if (!empty($rows)) {
                $sortedRows = collect($rows)->sortBy('date')->values();
                
                // Calculate staff count (unique workers)
                $staffCount = $sortedRows->pluck('staff_name')->unique()->filter(fn($name) => $name !== 'Unassigned')->count();
                if ($staffCount === 0) {
                    $staffCount = $sortedRows->pluck('staff_name')->unique()->count();
                }
                
                // Calculate worked hours (subtract breaks)
                $workedHoursSum = 0.0;
                foreach ($sortedRows as $r) {
                    try {
                        $start = \Carbon\Carbon::parse($r['start_time']);
                        $end = \Carbon\Carbon::parse($r['end_time']);
                        if ($end->lt($start)) {
                            $end->addDay();
                        }
                        $diff = $start->diffInMinutes($end) / 60;
                        $diff -= ($r['break_mins'] ?? 0) / 60;
                        $workedHoursSum += max(0.0, $diff);
                    } catch(\Exception $e) {
                        $workedHoursSum += 8.0; // fallback
                    }
                }

                // Operational Status
                $maxDateVal = $sortedRows->max('date');
                $minDateVal = $sortedRows->min('date');
                $maxDate = $maxDateVal ? \Carbon\Carbon::parse($maxDateVal)->startOfDay() : null;
                $minDate = $minDateVal ? \Carbon\Carbon::parse($minDateVal)->startOfDay() : null;

                $opStatus = 'Pending Completion';
                if ($maxDate && $maxDate->lt($today)) {
                    $opStatus = 'Completed';
                } elseif ($minDate && $maxDate && $today->gte($minDate) && $today->lte($maxDate)) {
                    $opStatus = 'Live Event';
                }

                $events[] = [
                    'id' => 'sheet_' . $idCounter++,
                    'name' => $eventName,
                    'status' => $opStatus,
                    'staff_count' => $staffCount,
                    'worked_hours' => round($workedHoursSum, 1),
                    'rows' => $sortedRows->toArray()
                ];
            }
        }
        
        return $events;
    }
    
    $sheetsData = generateSheetsFromBookings($bookings ?? collect([]));
    
    // Categories for dropdown
    $cats = \App\Models\JobCategory::with('subCategories')->get();
    $roleOptions = [];
    foreach($cats as $c) {
        if($c->subCategories->count() > 0) {
            foreach($c->subCategories as $sub) {
                $roleOptions[] = $c->name . ' - ' . $sub->name;
            }
        } else {
            $roleOptions[] = $c->name;
        }
    }
@endphp

<!-- Alpine Workflow for Timesheet Approval -->
<script>
    window.excelWorkbook = function excelWorkbook() {
        return {
            sheets: @json($sheetsData),
            availableRoles: @json($roleOptions),
            activeSheetId: null,
            isSaving: false,
            search: '',
            originalSheets: null,
            expandedRows: {},

            init() {
                this.originalSheets = JSON.parse(JSON.stringify(this.sheets));

                const params = new URLSearchParams(window.location.search);
                const eventParam = params.get('event') || params.get('booking');
                let foundMatch = false;

                if (eventParam) {
                    for (const sheet of this.sheets) {
                        let match = false;
                        if (sheet.name.toLowerCase() === eventParam.toLowerCase()) {
                            match = true;
                        } else if (sheet.rows.some(r => r.booking_ref && r.booking_ref.toLowerCase() === eventParam.toLowerCase())) {
                            match = true;
                        }

                        if (match) {
                            this.activeSheetId = sheet.id;
                            foundMatch = true;
                            break;
                        }
                    }
                }

                this.$nextTick(() => window.lucide && window.lucide.createIcons());
            },

            get activeSheet() {
                return this.sheets.find(s => s.id === this.activeSheetId) || null;
            },

            get filteredRows() {
                if (!this.activeSheet || !this.activeSheet.rows) return [];
                const q = this.search.toLowerCase();
                
                return this.activeSheet.rows.filter(r => {
                    if (q) {
                        const matchName = r.staff_name && r.staff_name.toLowerCase().includes(q);
                        const matchRole = r.role && r.role.toLowerCase().includes(q);
                        const matchRef = r.booking_ref && r.booking_ref.toLowerCase().includes(q);
                        const matchEvent = r.event_name && r.event_name.toLowerCase().includes(q);
                        return matchName || matchRole || matchRef || matchEvent;
                    }
                    return true;
                });
            },

            parseTime(timeStr) {
                if(!timeStr) return 0;
                let parts = timeStr.split(':');
                if(parts.length < 2) return 0;
                return parseInt(parts[0], 10) + (parseInt(parts[1], 10) / 60);
            },
            
            calcHours(row) {
                let start = this.parseTime(row.start_time);
                let end = this.parseTime(row.end_time);
                if (end < start) end += 24; // Cross-midnight shift
                let total = end - start;
                let breakHrs = (parseInt(row.break_mins) || 0) / 60;
                return Math.max(0, total - breakHrs);
            },

            calcSubtotal(row) {
                return this.calcHours(row) * (row.rate || 0);
            },

            get sheetTotalHours() {
                if (!this.activeSheet || !this.activeSheet.rows) return 0;
                return this.activeSheet.rows.reduce((sum, r) => sum + this.calcHours(r), 0);
            },

            get sheetTotalPay() {
                if (!this.activeSheet || !this.activeSheet.rows) return 0;
                return this.activeSheet.rows.reduce((sum, r) => sum + this.calcSubtotal(r), 0);
            },
            
            formatMoney(amount) {
                return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(amount);
            },

            get isDirty() {
                if (!this.originalSheets) return false;
                for (let sIdx = 0; sIdx < this.sheets.length; sIdx++) {
                    const sheet = this.sheets[sIdx];
                    const origSheet = this.originalSheets[sIdx];
                    if (!origSheet) continue;
                    for (let rIdx = 0; rIdx < sheet.rows.length; rIdx++) {
                        const row = sheet.rows[rIdx];
                        const origRow = origSheet.rows[rIdx];
                        if (!origRow) continue;
                        if (row.start_time !== origRow.start_time || 
                            row.end_time !== origRow.end_time || 
                            parseInt(row.break_mins) !== parseInt(origRow.break_mins)) {
                            return true;
                        }
                    }
                }
                return false;
            },

            revertChanges() {
                this.sheets = JSON.parse(JSON.stringify(this.originalSheets));
            },

            friendlyStatus(status) {
                const map = {
                    'Draft': 'Pending Review',
                    'Submitted': 'Submitted',
                    'Approved': 'Approved',
                    'Billed': 'Locked'
                };
                return map[status] || status;
            },

            toggleRow(rowId) {
                this.expandedRows[rowId] = !this.expandedRows[rowId];
            },

            isRowExpanded(rowId) {
                return !!this.expandedRows[rowId];
            },

            async saveWorkbook() {
                this.isSaving = true;
                try {
                    const response = await fetch('{{ route('partner.timesheets.sync') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sheets: this.sheets })
                    });
                    
                    const result = await response.json();
                    
                    if(result.success) {
                        this.originalSheets = JSON.parse(JSON.stringify(this.sheets));
                        Swal.fire({ icon: 'success', title: 'Synced!', text: 'Shift updates have been synced successfully.', confirmButtonColor: '#0F1D33', timer: 2000, showConfirmButton: false });
                    } else {
                        throw new Error('Sync failed');
                    }
                } catch(error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to sync timesheets to the server.', confirmButtonColor: '#0F1D33' });
                } finally {
                    this.isSaving = false;
                }
            }
        };
    }
</script>

<div class="flex flex-col h-full min-h-0" x-data="excelWorkbook()">
    
    <!-- CASE 1: No Timesheets Available -->
    <template x-if="sheets.length === 0">
        <div class="flex-1 flex flex-col items-center justify-center p-12 bg-white dark:bg-slate-900 border border-[#E6EAF0] dark:border-slate-800 rounded-3xl shadow-sm text-center my-auto">
            <div class="w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-850 flex items-center justify-center mx-auto mb-6 border border-slate-100 dark:border-slate-800 shadow-sm">
                <!-- Clipboard List SVG -->
                <svg class="w-10 h-10 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>
            </div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white">📋 No Timesheets Available</h3>
            <p class="text-slate-500 text-sm max-w-md mx-auto mt-2 mb-8 font-medium">You currently have no completed events requiring timesheet review.</p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('partner.book-staff') }}" class="px-6 py-3 bg-[#0F1C3F] hover:bg-[#1a2b5e] text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-[#0F1C3F]/20 hover:-translate-y-0.5">
                    Book Staff
                </a>
                <a href="{{ route('partner.event-list') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 rounded-xl font-bold text-sm transition-all">
                    View Bookings
                </a>
            </div>
        </div>
    </template>

    <!-- CASE 2: Event Dashboard Card List -->
    <template x-if="sheets.length > 0 && activeSheetId === null">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Timesheet Tracker</h1>
                <p class="text-slate-500 text-sm mt-0.5">Select an event to review shift attendance and finalize worked hours.</p>
            </div>

            <!-- Grid of event cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                <template x-for="sheet in sheets" :key="sheet.id">
                    <div class="card-interactive bg-white dark:bg-slate-900 border border-[#E6EAF0] dark:border-slate-800 rounded-3xl shadow-sm p-6 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <h3 class="text-[15px] font-black text-slate-900 dark:text-white line-clamp-1 group-hover:text-indigo-600 transition-colors" x-text="sheet.name"></h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider border shrink-0"
                                     :class="{
                                         'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800/40 dark:text-slate-400 dark:border-slate-700/50': sheet.status === 'Completed',
                                         'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/30': sheet.status === 'Live Event',
                                         'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30': sheet.status === 'Pending Completion'
                                     }"
                                     x-text="sheet.status"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 my-5 p-4 bg-slate-50/50 dark:bg-slate-850/30 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Assigned Staff</p>
                                    <p class="text-lg font-black text-slate-800 dark:text-white mt-1 tabular-nums" x-text="sheet.staff_count + ' Staff'"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Worked Hours</p>
                                    <p class="text-lg font-black text-slate-800 dark:text-white mt-1 tabular-nums" x-text="sheet.worked_hours + ' hrs'"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="activeSheetId = sheet.id"
                                    class="btn-interactive w-full flex items-center justify-center gap-2 py-2.5 bg-[#0F1C3F] hover:bg-[#1a2b5e] text-white rounded-xl font-bold text-xs shadow-md">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h20"/><path d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8"/><path d="M4 8V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2"/></svg>
                                Review Timesheet
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- CASE 3: Detailed Review Page -->
    <template x-if="sheets.length > 0 && activeSheetId !== null">
        <div class="flex-1 flex flex-col min-h-0 space-y-5">
            <!-- Back Navigation & Dropdown selector -->
            <div class="flex-none flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <button @click="activeSheetId = null" 
                            class="flex items-center justify-center p-2.5 bg-white border border-[#E6EAF0] hover:border-slate-300 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4 text-slate-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    
                    <!-- Dropdown selector -->
                    <div class="relative" x-data="{ eventOpen: false }">
                        <button @click="eventOpen = !eventOpen" @click.away="eventOpen = false"
                                class="flex items-center gap-2 font-black text-xl text-kingdom-navy hover:text-kingdom-gold transition-colors cursor-pointer">
                            <span x-text="activeSheet ? activeSheet.name : 'Select Event'"></span>
                            <svg class="w-5 h-5 transition-transform" :class="eventOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div x-show="eventOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                             class="absolute left-0 top-full mt-2 w-72 bg-white rounded-xl shadow-xl border border-slate-200 py-2 z-50" style="display:none;">
                             <template x-for="sheet in sheets" :key="sheet.id">
                                <button @click="activeSheetId = sheet.id; eventOpen = false"
                                        class="w-full text-left px-4 py-2.5 text-sm font-semibold transition-colors flex items-center gap-3"
                                        :class="activeSheetId === sheet.id ? 'bg-kingdom-navy/5 text-kingdom-navy font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-kingdom-navy'">
                                    <span class="w-2 h-2 rounded-full shrink-0" :class="activeSheetId === sheet.id ? 'bg-kingdom-gold' : 'bg-slate-300'"></span>
                                    <span x-text="sheet.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Filter / Search within sheet -->
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" x-model="search" placeholder="Search staff or roles..." 
                           class="pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 w-64 bg-white outline-none transition-all">
                </div>
            </div>

            <!-- Cleaner Summary Cards -->
            <div class="grid grid-cols-2 gap-4" x-show="activeSheet && activeSheet.rows.length > 0 && sheetTotalHours > 0">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-[#E6EAF0] dark:border-slate-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold">Assigned Staff</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white mt-1" x-text="activeSheet.staff_count + ' Staff'"></p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/45 text-indigo-600 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-[#E6EAF0] dark:border-slate-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold">Worked Hours</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white mt-1" x-text="sheetTotalHours.toFixed(1) + ' hrs'"></p>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/45 text-amber-600 rounded-xl border border-amber-100 dark:border-amber-900/30">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
            </div>

            <!-- Attendance List Wrapper -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col min-h-0">
                
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse table-auto whitespace-nowrap">
                        <thead>
                            <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-xs">
                                <th class="py-4 px-6">Worker Name</th>
                                <th class="py-4 px-4">Role</th>
                                <th class="py-4 px-4">Date</th>
                                <th class="py-4 px-4 text-center">Start Time</th>
                                <th class="py-4 px-4 text-center">End Time</th>
                                <th class="py-4 px-4 text-center">Hours</th>
                                <th class="py-4 px-4 text-center">Status</th>
                                <th class="py-4 px-6 text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-transparent">
                            <template x-if="filteredRows.length === 0">
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-sm text-slate-400 font-medium bg-slate-50/20 dark:bg-slate-900/10">
                                        No worker records found matching filter.
                                    </td>
                                </tr>
                            </template>
                            <template x-for="(row, index) in filteredRows" :key="row.id">
                                <template x-if="true">
                                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                        <!-- Worker Name -->
                                        <td class="py-4 px-6 font-black text-slate-900 dark:text-white" x-text="row.staff_name"></td>
                                        <!-- Role -->
                                        <td class="py-4 px-4 text-xs font-semibold text-slate-500" x-text="row.role"></td>
                                        <!-- Date -->
                                        <td class="py-4 px-4 text-xs font-semibold text-slate-500" x-text="row.date"></td>
                                        <!-- Start Time input -->
                                        <td class="py-3 px-4 text-center">
                                            <input type="time" x-model="row.start_time" :disabled="row.status === 'Approved' || row.status === 'Billed'"
                                                   class="mx-auto w-24 h-9 px-2 text-[12px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg outline-none text-center disabled:text-slate-400 disabled:bg-slate-50 focus:ring-2 focus:ring-kingdom-gold/20">
                                        </td>
                                        <!-- End Time input -->
                                        <td class="py-3 px-4 text-center">
                                            <input type="time" x-model="row.end_time" :disabled="row.status === 'Approved' || row.status === 'Billed'"
                                                   class="mx-auto w-24 h-9 px-2 text-[12px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg outline-none text-center disabled:text-slate-400 disabled:bg-slate-50 focus:ring-2 focus:ring-kingdom-gold/20">
                                        </td>
                                        <!-- Total Hours -->
                                        <td class="py-4 px-4 text-center font-black text-slate-800 dark:text-white" x-text="calcHours(row).toFixed(1) + 'h'"></td>
                                        <!-- Status -->
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider shadow-sm border"
                                                  :class="{
                                                      'bg-slate-50 text-slate-600 border-slate-200': row.status === 'Draft',
                                                      'bg-indigo-50 text-indigo-700 border-indigo-200': row.status === 'Submitted',
                                                      'bg-emerald-50 text-emerald-700 border-emerald-200': row.status === 'Approved',
                                                      'bg-slate-100 text-slate-500 border-slate-300': row.status === 'Billed'
                                                  }"
                                                  x-text="friendlyStatus(row.status)"></span>
                                        </td>
                                        <!-- Details Toggle -->
                                        <td class="py-4 px-6 text-right">
                                            <button type="button" @click="toggleRow(row.id)" 
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-indigo-100 text-slate-500 hover:text-indigo-600 transition-colors">
                                                <svg class="w-4 h-4 transition-transform duration-200" :class="isRowExpanded(row.id) ? 'rotate-180 text-indigo-600' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Collapsible Billing Section -->
                                    <tr x-show="isRowExpanded(row.id)" x-transition class="bg-slate-50/40 dark:bg-slate-850/10 border-t border-slate-100 dark:border-slate-800">
                                        <td colspan="8" class="p-4">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 px-8 py-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs font-bold text-slate-500">Break Deduction (mins):</span>
                                                    <input type="number" x-model="row.break_mins" :disabled="row.status === 'Approved' || row.status === 'Billed'" min="0" step="5"
                                                           class="w-20 h-9 px-2 text-[12px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg outline-none text-center disabled:text-slate-400 disabled:bg-slate-50 focus:ring-2 focus:ring-kingdom-gold/20">
                                                </div>
                                                <div class="flex items-center gap-6 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                    <div>
                                                        <span class="text-slate-400 font-medium">Hourly Rate:</span>
                                                        <span class="text-slate-800 dark:text-white font-bold ml-1" x-text="formatMoney(row.rate)"></span>
                                                    </div>
                                                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-800"></div>
                                                    <div>
                                                        <span class="text-slate-400 font-medium">Estimated Pay:</span>
                                                        <span class="text-kingdom-navy dark:text-kingdom-gold font-black text-sm ml-1" x-text="formatMoney(calcSubtotal(row))"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Stack View -->
                <div class="md:hidden divide-y divide-slate-100 p-4 space-y-4 overflow-y-auto">
                    <template x-if="filteredRows.length === 0">
                        <div class="p-8 text-center text-sm text-slate-400 font-medium bg-slate-50/20">
                            No worker records found matching filter.
                        </div>
                    </template>
                    <template x-for="row in filteredRows" :key="row.id">
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 space-y-3 shadow-sm">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-sm font-black text-slate-900" x-text="row.staff_name"></h3>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5" x-text="row.role + ' · ' + row.date"></p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider shadow-sm border"
                                      :class="{
                                          'bg-slate-50 text-slate-600 border-slate-200': row.status === 'Draft',
                                          'bg-indigo-50 text-indigo-700 border-indigo-200': row.status === 'Submitted',
                                          'bg-emerald-50 text-emerald-700 border-emerald-200': row.status === 'Approved',
                                          'bg-slate-100 text-slate-500 border-slate-300': row.status === 'Billed'
                                      }"
                                      x-text="friendlyStatus(row.status)"></span>
                            </div>

                            <!-- Inputs -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Start Time</label>
                                    <input type="time" x-model="row.start_time" :disabled="row.status === 'Approved' || row.status === 'Billed'"
                                           class="w-full h-9 px-2 text-[12px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg outline-none text-center disabled:text-slate-400 disabled:bg-slate-50 focus:ring-2 focus:ring-kingdom-gold/20">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">End Time</label>
                                    <input type="time" x-model="row.end_time" :disabled="row.status === 'Approved' || row.status === 'Billed'"
                                           class="w-full h-9 px-2 text-[12px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg outline-none text-center disabled:text-slate-400 disabled:bg-slate-50 focus:ring-2 focus:ring-kingdom-gold/20">
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2.5 border-t border-slate-100">
                                <div>
                                    <span class="text-xs font-semibold text-slate-400">Total Hours:</span>
                                    <span class="text-sm font-black text-slate-800 ml-1" x-text="calcHours(row).toFixed(1) + 'h'"></span>
                                </div>
                                
                                <button type="button" @click="toggleRow(row.id)" 
                                        class="text-xs font-bold text-indigo-600 flex items-center gap-1 focus:outline-none">
                                    <span x-text="isRowExpanded(row.id) ? 'Hide Billing' : 'Show Billing'"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="isRowExpanded(row.id) ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                            </div>

                            <!-- Mobile Collapsible Details -->
                            <div x-show="isRowExpanded(row.id)" x-transition class="p-3 bg-slate-50 rounded-xl space-y-2.5 text-xs font-semibold text-slate-600 mt-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 font-medium">Break Deduction (mins):</span>
                                    <input type="number" x-model="row.break_mins" :disabled="row.status === 'Approved' || row.status === 'Billed'" min="0" step="5"
                                           class="w-20 h-8 px-2 text-[12px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg outline-none text-center disabled:text-slate-400 disabled:bg-slate-50 focus:ring-2 focus:ring-kingdom-gold/20">
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 font-medium">Hourly Rate:</span>
                                    <span class="text-slate-800 font-bold" x-text="formatMoney(row.rate)"></span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-200/50 pt-2">
                                    <span class="text-slate-400 font-medium">Estimated Pay:</span>
                                    <span class="text-kingdom-navy font-black text-sm" x-text="formatMoney(calcSubtotal(row))"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </div>
    </template>

    <!-- Sticky Action Bar at the Bottom -->
    <div x-show="isDirty"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-12"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-12"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-[#0F1C3F] text-white px-6 py-4 rounded-2xl shadow-2xl border border-slate-750/50 flex items-center gap-6 justify-between w-[90%] max-w-xl"
         style="display: none;">
        <div class="flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            <span class="text-xs font-bold tracking-wide">Unsaved changes in timesheet</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="revertChanges()" 
                    class="px-4 py-2 bg-white/10 hover:bg-white/25 text-white rounded-xl font-bold text-xs transition-all">
                Discard
            </button>
            <button type="button" @click="saveWorkbook()" :disabled="isSaving"
                    class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-xs transition-all shadow-md shadow-emerald-500/10 flex items-center gap-2">
                <span x-show="!isSaving">Sync Timesheets</span>
                <span x-show="isSaving" class="flex items-center gap-1.5">
                    <svg class="animate-spin h-3.5 w-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25"></circle><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor" class="opacity-75"></path></svg>
                    Saving...
                </span>
            </button>
        </div>
    </div>

    <datalist id="roleList">
        <template x-for="role in availableRoles">
            <option :value="role"></option>
        </template>
    </datalist>
</div>
@endsection

