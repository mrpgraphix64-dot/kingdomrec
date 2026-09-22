@extends('layouts.admin')

@section('title', 'Timesheets | Kingdom Admin')

@section('content')
@php
    function generateMasterWorkbook($slots) {
        $workbooks = [];
        
        $items = (is_object($slots) && method_exists($slots, 'items')) ? $slots->items() : $slots;
        
        // Group by Partner (client)
        $groupedByPartner = collect($items)->groupBy(function($s) {
            return $s->staffQuotation->eventBooking->client ?? $s->staffQuotation->client ?? 'Unknown Partner';
        });

        foreach($groupedByPartner as $partnerName => $partnerSlots) {
            $events = [];
            // Group by Event
            $groupedByEvent = collect($partnerSlots)->groupBy(function($s) {
                return $s->staffQuotation->eventBooking->event_name ?? $s->staffQuotation->event_name ?? 'Miscellaneous Bookings';
            });
            
            foreach($groupedByEvent as $eventName => $eventSlots) {
                $rows = [];
                foreach($eventSlots as $s) {
                    // Standardize status for display logic
                    $status = $s->status ?? 'Draft';
                    if (in_array($status, ['Unassigned', 'Assigned', 'Pending', 'Checked In'])) {
                        $status = 'Draft';
                    } elseif (in_array($status, ['Checked Out', 'Completed'])) {
                        $status = 'Submitted';
                    }

                    $scheduledTime = ($s->staffQuotation->start_time && $s->staffQuotation->end_time)
                        ? substr($s->staffQuotation->start_time, 0, 5) . ' - ' . substr($s->staffQuotation->end_time, 0, 5)
                        : 'Not Scheduled';

                    $rows[] = [
                        'id' => 'row_' . $s->id,
                        'db_id' => $s->id,
                        'role' => $s->role_name ?? ($s->staffQuotation->category . ($s->staffQuotation->sub_category ? ' - ' . $s->staffQuotation->sub_category : '')),
                        'staff_name' => $s->applicant->name ?? 'Unassigned',
                        'date' => $s->shift_date ? \Carbon\Carbon::parse($s->shift_date)->format('Y-m-d') : date('Y-m-d'),
                        'scheduled_time' => $scheduledTime,
                        'start_time' => $s->start_time ? substr($s->start_time, 0, 5) : '09:00',
                        'end_time' => $s->end_time ? substr($s->end_time, 0, 5) : '17:00',
                        'break_mins' => $s->break_mins ?? 30,
                        'rate' => (float)($s->rate ?? $s->staffQuotation->rate ?? 15.0),
                        'status' => $status,
                        'booking_ref' => $s->staffQuotation->eventBooking->booking_ref ?? $s->staffQuotation->quotation_ref ?? '',
                        'event_name' => $eventName,
                        'client' => $partnerName,
                    ];
                }
                $events[] = [
                    'id' => 'sheet_' . uniqid(),
                    'name' => $eventName,
                    'rows' => $rows
                ];
            }
            
            $workbooks[] = [
                'partner' => $partnerName,
                'sheets' => $events
            ];
        }
        
        return collect($workbooks)->sortBy('partner')->values()->all();
    }
    
    $masterData = generateMasterWorkbook($slots ?? collect([]));
    
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

<!-- Alpine Logic -->
<script>
    window.masterWorkbookSystem = function masterWorkbookSystem() {
        return {
            workbooks: @json($masterData),
            availableRoles: @json($roleOptions),
            searchQuery: '',
            staffSearchQuery: '',
            isDirty: false,
            refreshCounter: 0,
            statsOpen: false,
            
            activePartnerName: null,
            activeSheetId: null,
            selectedRows: [],
            showRows: true, // Show detailed rows by default for primary workspace focus

            init() {
                const params = new URLSearchParams(window.location.search);
                const eventParam = params.get('event');
                const bookingParam = params.get('booking');
                const staffParam = params.get('staff') || params.get('shift');
                let foundMatch = false;

                if (eventParam || bookingParam || staffParam) {
                    for (const wb of this.workbooks) {
                        for (const sheet of wb.sheets) {
                            let match = false;
                            if (eventParam && sheet.name.toLowerCase() === eventParam.toLowerCase()) {
                                match = true;
                            } else if (bookingParam) {
                                const hasBooking = sheet.rows.some(r => r.booking_ref && r.booking_ref.toLowerCase() === bookingParam.toLowerCase());
                                if (hasBooking) match = true;
                            } else if (staffParam) {
                                const hasStaff = sheet.rows.some(r => r.staff_name && r.staff_name.toLowerCase().includes(staffParam.toLowerCase()));
                                if (hasStaff) match = true;
                            }

                            if (match) {
                                this.activePartnerName = wb.partner;
                                this.activeSheetId = sheet.id;
                                this.showRows = true; // Auto-expand when deep-linked
                                foundMatch = true;
                                break;
                            }
                        }
                        if (foundMatch) break;
                    }
                }

                if (!foundMatch && this.workbooks.length > 0) {
                    this.selectPartner(this.workbooks[0]);
                }
                this.$watch('activeSheetId', () => {
                    this.selectedRows = [];
                    this.showRows = true; // keep detailed rows open by default
                    this.$nextTick(() => window.lucide && window.lucide.createIcons());
                });

                // Deep watch workbooks array to mark changes as dirty and trigger updates
                this.$watch('workbooks', () => {
                    this.isDirty = true;
                    this.refreshCounter++;
                }, { deep: true });

                window.addEventListener('beforeunload', (e) => {
                    if (this.isDirty && !window.isSavingAndReloading) {
                        e.preventDefault();
                        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                        return e.returnValue;
                    }
                });

                this.$nextTick(() => window.lucide && window.lucide.createIcons());
            },

            get filteredWorkbooks() {
                if (!this.searchQuery) return this.workbooks;
                const q = this.searchQuery.toLowerCase();
                return this.workbooks.filter(w => w.partner.toLowerCase().includes(q));
            },

            get activeWorkbook() {
                if(this.workbooks.length === 0 || !this.activePartnerName) return null;
                return this.workbooks.find(w => w.partner === this.activePartnerName) || null;
            },

            get activeSheet() {
                if(!this.activeWorkbook || !this.activeWorkbook.sheets) return null;
                return this.activeWorkbook.sheets.find(s => s.id === this.activeSheetId) || this.activeWorkbook.sheets[0];
            },

            get filteredRows() {
                this.refreshCounter;
                if (!this.activeSheet || !this.activeSheet.rows) return [];
                const params = new URLSearchParams(window.location.search);
                const bookingParam = params.get('booking');
                const staffParam = params.get('staff');
                
                return this.activeSheet.rows.filter(r => {
                    if (bookingParam && r.booking_ref.toLowerCase() !== bookingParam.toLowerCase()) {
                        return false;
                    }
                    if (staffParam && !r.staff_name.toLowerCase().includes(staffParam.toLowerCase())) {
                        return false;
                    }
                    if (this.staffSearchQuery) {
                        const q = this.staffSearchQuery.toLowerCase();
                        if (!r.staff_name.toLowerCase().includes(q) && !r.role.toLowerCase().includes(q)) {
                            return false;
                        }
                    }
                    return true;
                });
            },

            selectPartner(wb) {
                this.activePartnerName = wb?.partner || null;
                if(wb && wb.sheets && wb.sheets.length > 0) {
                    this.activeSheetId = wb.sheets[0].id;
                } else {
                    this.activeSheetId = null;
                }
            },

            onPartnerChange(partnerName) {
                this.activePartnerName = partnerName;
                const wb = this.workbooks.find(w => w.partner === partnerName);
                if (wb && wb.sheets && wb.sheets.length > 0) {
                    this.activeSheetId = wb.sheets[0].id;
                } else {
                    this.activeSheetId = null;
                }
                this.$nextTick(() => window.lucide && window.lucide.createIcons());
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
                if (end < start) end += 24; 
                let total = end - start;
                return Math.max(0, total);
            },

            calcSubtotal(row) {
                return this.calcHours(row) * (row.rate || 0);
            },

            get eventTotalStaff() {
                this.refreshCounter;
                return this.activeSheet && this.activeSheet.rows ? this.activeSheet.rows.length : 0;
            },

            get eventSubmittedStaff() {
                this.refreshCounter;
                if (!this.activeSheet || !this.activeSheet.rows) return 0;
                return this.activeSheet.rows.filter(r => r.status !== 'Draft').length;
            },

            get eventCompletionPercent() {
                this.refreshCounter;
                const total = this.eventTotalStaff;
                if (total === 0) return 0;
                return Math.round((this.eventSubmittedStaff / total) * 100);
            },

            get eventMissingTimesheetsCount() {
                this.refreshCounter;
                if (!this.activeSheet || !this.activeSheet.rows) return 0;
                return this.activeSheet.rows.filter(r => r.status === 'Draft').length;
            },

            get eventApprovedHours() {
                this.refreshCounter;
                if (!this.activeSheet || !this.activeSheet.rows) return 0;
                return this.activeSheet.rows
                    .filter(r => r.status === 'Approved')
                    .reduce((sum, r) => sum + this.calcHours(r), 0);
            },

            get eventApprovedValue() {
                this.refreshCounter;
                if (!this.activeSheet || !this.activeSheet.rows) return 0;
                return this.activeSheet.rows
                    .filter(r => r.status === 'Approved')
                    .reduce((sum, r) => sum + this.calcSubtotal(r), 0);
            },

            get eventPendingValue() {
                this.refreshCounter;
                if (!this.activeSheet || !this.activeSheet.rows) return 0;
                return this.activeSheet.rows
                    .filter(r => ['Draft', 'Submitted'].includes(r.status))
                    .reduce((sum, r) => sum + this.calcSubtotal(r), 0);
            },

            get eventBilledValue() {
                this.refreshCounter;
                if (!this.activeSheet || !this.activeSheet.rows) return 0;
                return this.activeSheet.rows
                    .filter(r => r.status === 'Billed')
                    .reduce((sum, r) => sum + this.calcSubtotal(r), 0);
            },

            get sheetTotalHours() {
                this.refreshCounter;
                if (!this.filteredRows) return 0;
                return this.filteredRows.reduce((sum, r) => sum + this.calcHours(r), 0);
            },

            get sheetTotalPay() {
                this.refreshCounter;
                if (!this.filteredRows) return 0;
                return this.filteredRows.reduce((sum, r) => sum + this.calcSubtotal(r), 0);
            },

            get billingReadiness() {
                this.refreshCounter;
                if (!this.activeSheet || !this.activeSheet.rows || this.activeSheet.rows.length === 0) {
                    return { status: 'Requires Review', class: 'bg-rose-100 text-rose-800 border-rose-200', icon: 'alert-triangle', text: 'No Shifts Scheduled' };
                }
                
                let hasDraft = this.activeSheet.rows.some(r => r.status === 'Draft');
                let hasSubmitted = this.activeSheet.rows.some(r => r.status === 'Submitted');
                let allApproved = this.activeSheet.rows.every(r => r.status === 'Approved');

                if (hasDraft) {
                    return { status: 'Requires Review', class: 'bg-amber-100 text-amber-800 border-amber-200', icon: 'alert-circle', text: 'Shifts Pending Completion' };
                }
                if (hasSubmitted) {
                    return { status: 'Pending Approval', class: 'bg-indigo-100 text-indigo-800 border-indigo-200', icon: 'clock', text: 'Hours Awaiting Approval' };
                }
                if (allApproved) {
                    return { status: 'Ready for Billing', class: 'bg-emerald-100 text-emerald-800 border-emerald-200', icon: 'check-circle', text: 'Ready for Billing' };
                }
                
                return { status: 'Requires Review', class: 'bg-amber-100 text-amber-800 border-amber-200', icon: 'alert-circle', text: 'Requires Review' };
            },
            
            formatMoney(amount) {
                return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(amount);
            },

            getStatusOptions(status) {
                return [
                    { value: 'Draft', label: 'Draft' },
                    { value: 'Submitted', label: 'Submitted' },
                    { value: 'Reviewed', label: 'Reviewed' },
                    { value: 'Approved', label: 'Approved' }
                ];
            },

            toggleSelectAll(event) {
                if (event.target.checked) {
                    this.selectedRows = this.filteredRows.map(r => r.id);
                } else {
                    this.selectedRows = [];
                }
            },

            approveSelected() {
                if (this.selectedRows.length === 0) return;
                this.selectedRows.forEach(rowId => {
                    const row = this.activeSheet.rows.find(r => r.id === rowId);
                    if (row) {
                        row.status = 'Approved';
                    }
                });
                this.selectedRows = [];
                // Approval must persist immediately — this used to only flip local Alpine
                // state, so it looked approved (badge, £0 pending) but a refresh silently
                // reverted it since only "Save Global Changes" actually writes to the DB.
                this.saveAdminChanges();
            },

            approveAllRows() {
                if (!this.activeSheet || !this.activeSheet.rows) return;
                this.activeSheet.rows.forEach(r => {
                    r.status = 'Approved';
                });
                // See note in approveSelected() above — same persistence bug.
                this.saveAdminChanges();
            },

            saveAdminChanges() {
                Swal.fire({
                    title: 'Saving changes...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch("{{ route('admin.timesheets.sync') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        sheets: this.workbooks.map(wb => ({
                            partner: wb.partner,
                            rows: wb.sheets.flatMap(s => s.rows)
                        }))
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.isSavingAndReloading = true;
                        this.isDirty = false;
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Admin Master Workbook saved successfully!',
                            confirmButtonColor: '#0F1D33',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to save timesheet changes.',
                            confirmButtonColor: '#0F1D33'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error saving:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'An error occurred while saving. Please try again.',
                        confirmButtonColor: '#0F1D33'
                    });
                });
            }
        };
    }
</script>

<div class="h-full flex flex-col gap-5 p-2 lg:p-0" x-data="masterWorkbookSystem()">
    
    <!-- Header -->
    <div class="flex-none bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-kingdom-gold/10 flex items-center justify-center shrink-0">
                        <i data-lucide="file-spreadsheet" class="text-kingdom-gold w-5 h-5"></i>
                    </div>
                    Timesheets
                </h1>
                <p class="text-[13px] text-slate-500 font-medium mt-1">Review worked shifts, check-in/out times, break deductions, and verify worked hours. Approved timesheets are locked and ready for billing.</p>
                
                <!-- Toggle link for Global KPI Panel -->
                <button @click="statsOpen = !statsOpen" class="text-[12px] font-bold text-[#0F1D33] hover:text-kingdom-gold transition-colors flex items-center gap-1.5 mt-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm">
                    <i :data-lucide="statsOpen ? 'chevron-up' : 'chevron-down'" class="w-3.5 h-3.5"></i>
                    <span x-text="statsOpen ? 'Hide System Stats' : 'Show System Stats'"></span>
                </button>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-slate-50 hover:bg-slate-200 border border-slate-200 text-slate-600 px-4 py-2.5 rounded-[10px] text-[13px] font-bold transition-all flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i> Export Data
                </button>
                <button @click="saveAdminChanges()" class="bg-[#0F1D33] hover:bg-slate-800 text-white px-5 py-2.5 rounded-[10px] text-[13px] font-bold shadow-sm transition-all flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Save Global Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Collapsible Global Reporting Panel -->
    <div x-show="statsOpen" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-4 flex-none bg-slate-900 text-white rounded-2xl p-5 border border-slate-800 shadow-xl" style="display: none;">
        <div class="flex flex-col">
            <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Pending Shifts</span>
            <span class="text-[28px] font-black text-amber-400 mt-1">{{ $stats['pending'] }}</span>
            <span class="text-[11px] text-slate-500 mt-1 font-medium">Awaiting check-out</span>
        </div>
        <div class="flex flex-col">
            <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Submitted Shifts</span>
            <span class="text-[28px] font-black text-indigo-400 mt-1">{{ $stats['submitted'] }}</span>
            <span class="text-[11px] text-slate-500 mt-1 font-medium">Awaiting admin review</span>
        </div>
        <div class="flex flex-col">
            <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Approved Shifts</span>
            <span class="text-[28px] font-black text-emerald-400 mt-1">{{ $stats['approved'] }}</span>
            <span class="text-[11px] text-slate-500 mt-1 font-medium">Sign-off completed</span>
        </div>
        <div class="flex flex-col">
            <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Ready For Billing</span>
            <span class="text-[28px] font-black text-kingdom-gold mt-1">£{{ number_format($stats['ready_for_invoice'], 2) }}</span>
            <span class="text-[11px] text-slate-500 mt-1 font-medium">Approved hours ready for billing</span>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="flex-1 min-h-0 flex flex-col relative">
        
        <!-- Main Workspace: The Excel Interface -->
        <div class="flex-1 min-h-0 min-w-0 bg-white shadow-sm border border-slate-200 rounded-2xl flex flex-col relative overflow-hidden" x-show="activeWorkbook">
            
            <!-- Excel Top Bar Header / Toolbar Filters -->
            <div class="p-3 bg-slate-50/50 border-b border-slate-200 flex flex-col md:flex-row items-center justify-between gap-3 flex-none">
                <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                    <!-- Partner Selection -->
                    <div class="relative flex items-center gap-2" x-data="{ open: false }">
                        <label class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider select-none">Partner:</label>
                        <button @click="open = !open" @click.away="open = false" 
                                class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-[12px] font-bold text-slate-800 focus:outline-none focus:border-kingdom-gold min-w-[160px] shadow-sm flex items-center justify-between gap-2 hover:bg-slate-50 transition-colors">
                            <span x-text="activePartnerName || 'Select Partner'"></span>
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div x-show="open" x-transition x-cloak 
                             class="absolute left-14 top-full mt-1 z-[100] min-w-[180px] bg-white border border-slate-200 rounded-xl shadow-xl py-1 max-h-60 overflow-y-auto custom-scrollbar">
                            <template x-for="wb in workbooks" :key="wb.partner">
                                <button @click="onPartnerChange(wb.partner); open = false" 
                                        class="w-full text-left px-3.5 py-2 text-[12px] font-semibold text-slate-700 hover:bg-slate-50 hover:text-kingdom-gold flex items-center justify-between" 
                                        :class="wb.partner === activePartnerName ? 'bg-slate-50 font-bold text-kingdom-gold' : ''">
                                    <span x-text="wb.partner"></span>
                                    <template x-if="wb.partner === activePartnerName">
                                        <svg class="w-3.5 h-3.5 text-kingdom-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Event/Sheet Selection -->
                    <div class="relative flex items-center gap-2" x-data="{ open: false }">
                        <label class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider select-none">Event/Sheet:</label>
                        <button @click="open = !open" @click.away="open = false" 
                                class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-[12px] font-bold text-slate-800 focus:outline-none focus:border-kingdom-gold min-w-[180px] shadow-sm flex items-center justify-between gap-2 hover:bg-slate-50 transition-colors">
                            <span x-text="activeSheet?.name || 'Select Event'"></span>
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div x-show="open" x-transition x-cloak 
                             class="absolute left-20 top-full mt-1 z-[100] min-w-[200px] bg-white border border-slate-200 rounded-xl shadow-xl py-1 max-h-60 overflow-y-auto custom-scrollbar">
                            <template x-for="sheet in (activeWorkbook?.sheets || [])" :key="sheet.id">
                                <button @click="activeSheetId = sheet.id; open = false" 
                                        class="w-full text-left px-3.5 py-2 text-[12px] font-semibold text-slate-700 hover:bg-slate-50 hover:text-kingdom-gold flex items-center justify-between" 
                                        :class="sheet.id === activeSheetId ? 'bg-slate-50 font-bold text-kingdom-gold' : ''">
                                    <span x-text="sheet.name"></span>
                                    <template x-if="sheet.id === activeSheetId">
                                        <svg class="w-3.5 h-3.5 text-kingdom-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Staff Search -->
                    <div class="relative min-w-[220px]">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5"></i>
                        <input type="text" x-model="staffSearchQuery" placeholder="Search staff or role..." class="w-full pl-8 pr-3 py-1.5 text-[12px] bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold text-slate-700 placeholder:text-slate-400 font-bold shadow-sm">
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-2 flex-wrap" x-show="activeSheet">
                    <button @click="approveSelected()" :disabled="selectedRows.length === 0" class="bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white px-3.5 py-1.5 rounded-lg text-[12px] font-bold shadow-sm transition-all flex items-center gap-1.5">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Approve Selected
                    </button>
                    <button @click="approveAllRows()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 rounded-lg text-[12px] font-bold shadow-sm transition-all flex items-center gap-1.5">
                        <i data-lucide="check-check" class="w-3.5 h-3.5"></i> Approve All
                    </button>
                </div>
            </div>

            <!-- Compact Event Overview Bar -->
            <div class="px-5 py-2.5 border-b border-slate-200 bg-slate-100/60 flex flex-wrap items-center justify-between gap-3 text-[12px] font-bold text-slate-700 flex-none" x-show="activeSheet">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="text-slate-900 font-black" x-text="activeSheet?.name"></span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-500" x-text="activeWorkbook?.partner"></span>
                    <span class="text-slate-300">|</span>
                    <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded text-[11px] font-extrabold" x-text="eventTotalStaff + ' Staff Assigned'"></span>
                    <span class="text-slate-300">|</span>
                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[11px] font-extrabold" x-text="eventSubmittedStaff + ' / ' + eventTotalStaff + ' Submitted (' + eventCompletionPercent + '%)'"></span>
                    <span class="text-slate-300">|</span>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-[11px] font-extrabold" x-text="'£' + eventPendingValue.toFixed(2) + ' Pending'"></span>
                    <span class="text-slate-300">|</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border" :class="billingReadiness.class">
                        <i :data-lucide="billingReadiness.icon" class="w-3 h-3"></i>
                        <span x-text="billingReadiness.status"></span>
                    </span>
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="hidden lg:flex items-center gap-1 text-[10px] font-semibold text-slate-400" x-show="showRows">
                        <i data-lucide="move-horizontal" class="w-3 h-3"></i>
                        Scroll to see all columns
                    </span>
                    <button @click="showRows = !showRows" class="text-kingdom-navy hover:text-kingdom-gold transition-colors flex items-center gap-1.5 text-[11px] font-bold">
                        <i :data-lucide="showRows ? 'eye-off' : 'eye'" class="w-3.5 h-3.5"></i>
                        <span x-text="showRows ? 'Hide Detailed Rows' : 'Show Detailed Rows'"></span>
                    </button>
                </div>
            </div>

            <!-- Compact Missing Timesheets Alert -->
            <div class="px-5 py-2 bg-amber-50 border-b border-amber-200 text-amber-800 text-[12px] font-bold flex items-center gap-2 flex-none" x-show="activeSheet && eventMissingTimesheetsCount > 0">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500 shrink-0"></i>
                <span x-text="eventMissingTimesheetsCount + ' Staff Have Not Submitted Timesheets'"></span>
            </div>

            <!-- Excel Grid Container (Collapsible detailed view) -->
            <div class="flex-1 min-h-0 max-h-[calc(100vh-220px)] overflow-auto bg-white custom-scrollbar relative admin-table-container" x-show="activeSheet && showRows" x-transition>
                <template x-if="activeSheet">
                    <table class="w-full text-left border-collapse whitespace-nowrap min-w-[1320px] admin-table">
                        <thead class="sticky top-0 z-30">
                            <tr class="text-[11px] font-semibold tracking-wide uppercase text-white bg-[#0f1f3d]">
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-[45px] py-2.5 text-center border-r border-slate-700/50">
                                    <input type="checkbox" @change="toggleSelectAll($event)" class="rounded border-slate-600 text-kingdom-gold focus:ring-kingdom-gold bg-slate-700">
                                </th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-[52px] py-2.5 text-center border-r border-slate-700/50">Sr. No.</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-36 px-2.5 py-2.5 border-r border-slate-700/50">Event Name</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-32 px-2.5 py-2.5 border-r border-slate-700/50">Client</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-32 px-2.5 py-2.5 border-r border-slate-700/50">Worker Name</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-32 px-2.5 py-2.5 border-r border-slate-700/50">Role</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-28 px-2.5 py-2.5 border-r border-slate-700/50">Date</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-28 px-3 py-2.5 border-r border-slate-700/50 text-center">Actual Start</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-28 px-3 py-2.5 border-r border-slate-700/50 text-center">Actual End</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-24 px-3 py-2.5 border-r border-slate-700/50 text-center">Total Hrs</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-28 px-3 py-2.5 border-r border-slate-700/50 text-right">Rate (£)</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-32 px-3 py-2.5 border-r border-slate-700/50 text-right">Subtotal</th>
                                <th class="sticky top-0 z-30 bg-[#0f1f3d] w-36 px-3 py-2.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0 bg-white">
                            <template x-for="(row, index) in filteredRows" :key="row.id">
                                <tr class="group transition-all duration-150 border-b-2 border-slate-200" 
                                    :class="row.status === 'Approved' ? 'bg-emerald-50/40' : (index % 2 === 0 ? 'bg-white' : 'bg-slate-100/60')">
                                    
                                    <!-- Bulk Selection Checkbox -->
                                    <td class="p-0 text-center border-r-2 border-slate-200">
                                        <input type="checkbox" :value="row.id" x-model="selectedRows" class="rounded border-slate-300 text-kingdom-navy focus:ring-kingdom-navy">
                                    </td>

                                    <!-- Row Number -->
                                    <td class="p-0 bg-transparent border-r-2 border-slate-200 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded font-mono text-[10px] font-bold border transition-colors shadow-sm"
                                              :class="row.status === 'Approved' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-indigo-50 text-indigo-700 border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600'"
                                              x-text="index + 1"></span>
                                    </td>
                                    
                                    <!-- Event Name (Read-only) -->
                                    <td class="p-2.5 border-r-2 border-slate-200 text-[12.5px] font-medium text-slate-700 truncate" :title="row.event_name" x-text="row.event_name"></td>

                                    <!-- Client (Read-only) -->
                                    <td class="p-2.5 border-r-2 border-slate-200 text-[12.5px] font-medium text-slate-700 truncate" :title="row.client" x-text="row.client"></td>

                                    <!-- Worker Name (Read-only) -->
                                    <td class="p-2.5 border-r-2 border-slate-200 text-[12.5px] font-semibold text-kingdom-navy truncate" :title="row.staff_name" x-text="row.staff_name"></td>

                                    <!-- Role (Read-only) -->
                                    <td class="p-2.5 border-r-2 border-slate-200 text-[12.5px] font-medium text-slate-600 truncate" :title="row.role" x-text="row.role"></td>

                                    <!-- Date (Read-only) -->
                                    <td class="p-2.5 border-r-2 border-slate-200 text-[12.5px] font-medium text-slate-600 whitespace-nowrap" x-text="row.date"></td>

                                    <!-- Actual Start Time (Editable if not Approved) -->
                                    <td class="p-0 border-r-2 border-slate-200 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-inset focus-within:z-10 bg-transparent">
                                        <input type="time" x-model="row.start_time" :disabled="row.status === 'Approved'"
                                               class="w-full h-11 px-3 text-[13px] font-bold text-slate-700 bg-transparent outline-none border-none focus:ring-0 text-center disabled:text-slate-400">
                                    </td>

                                    <!-- Actual End Time (Editable if not Approved) -->
                                    <td class="p-0 border-r-2 border-slate-200 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-inset focus-within:z-10 bg-transparent">
                                        <input type="time" x-model="row.end_time" :disabled="row.status === 'Approved'"
                                               class="w-full h-11 px-3 text-[13px] font-bold text-slate-700 bg-transparent outline-none border-none focus:ring-0 text-center disabled:text-slate-400">
                                    </td>

                                    <!-- Total Hours (Calculated) -->
                                    <td class="p-0 border-r-2 border-slate-200 bg-slate-50/50">
                                        <div class="w-full h-11 flex items-center justify-center px-3">
                                            <span class="text-[13px] font-black text-slate-700" x-text="calcHours(row).toFixed(2) + 'h'"></span>
                                        </div>
                                    </td>

                                    <!-- Rate (Editable if not Approved) -->
                                    <td class="p-0 border-r-2 border-slate-200 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-inset focus-within:z-10 bg-transparent">
                                        <input type="number" min="0" step="0.5" x-model.number="row.rate" :disabled="row.status === 'Approved'"
                                               class="w-full h-11 px-3 text-[13px] font-medium text-slate-700 bg-transparent outline-none border-none focus:ring-0 text-right disabled:text-slate-400">
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="p-0 border-r-2 border-slate-200 bg-indigo-50/30">
                                        <div class="w-full h-11 flex items-center justify-end px-4">
                                            <span class="text-[14px] font-black text-indigo-600" x-text="formatMoney(calcSubtotal(row))"></span>
                                        </div>
                                    </td>

                                    <!-- Status Column (Select option for admin) -->
                                    <td class="p-0 bg-transparent text-center">
                                        <div class="flex items-center justify-center h-11">
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" @click.away="open = false" 
                                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[12px] font-bold border shadow-sm transition-all focus:outline-none"
                                                        :class="row.status === 'Approved' ? 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200/50' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                                                    <span x-text="getStatusOptions(row.status).find(o => o.value === row.status)?.label || row.status"></span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                                </button>
                                                <div x-show="open" x-transition x-cloak 
                                                     class="absolute right-0 mt-1 z-[100] min-w-[155px] bg-white border border-slate-200 rounded-xl shadow-xl py-1 text-left">
                                                    <template x-for="opt in getStatusOptions(row.status)" :key="opt.value">
                                                        <button @click="row.status = opt.value; open = false" 
                                                                class="w-full text-left px-3 py-1.5 text-[12px] font-bold text-slate-700 hover:bg-slate-50 hover:text-kingdom-gold flex items-center justify-between" 
                                                                :class="opt.value === row.status ? 'bg-slate-50 font-black text-kingdom-gold' : ''">
                                                            <span x-text="opt.label"></span>
                                                            <template x-if="opt.value === row.status">
                                                                <svg class="w-3 h-3 text-kingdom-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                                            </template>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>
            </div>

            <!-- Sticky Sheet Footer (Totals) -->
            <div class="flex-none bg-[#0f1f3d] text-white border-t border-slate-800 px-4 sm:px-6 py-3 flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 z-30 sticky bottom-0 pr-16 sm:pr-24" x-show="activeSheet && showRows">
                <div class="flex items-center gap-6">
                    <div class="text-[13px] font-bold text-slate-300 flex items-center gap-2">
                        <i data-lucide="list-tree" class="w-4 h-4 text-slate-400"></i>
                        <span x-text="filteredRows.length + ' Staff Rows Shown'"></span>
                    </div>
                </div>
                
                <div class="flex items-center gap-8">
                    <div class="flex flex-col text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Total Hours</span>
                        <span class="text-[18px] font-black text-kingdom-gold leading-none" x-text="sheetTotalHours.toFixed(2) + 'h'"></span>
                    </div>
                    <div class="h-8 w-px bg-slate-700"></div>
                    <div class="flex flex-col text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Total Pay</span>
                        <span class="text-[20px] font-black text-emerald-400 leading-none" x-text="formatMoney(sheetTotalPay)"></span>
                    </div>
                </div>
            </div>

        </div>

        <template x-if="!activeWorkbook">
            <div class="flex-1 bg-white shadow-sm border border-slate-200 rounded-2xl flex flex-col items-center justify-center text-center p-12">
                <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                    <i data-lucide="folder-open" class="text-slate-300 w-10 h-10"></i>
                </div>
                <h3 class="text-[20px] font-black text-slate-800 mb-2">No Partner Available</h3>
                <p class="text-[13px] text-slate-500 max-w-sm">No interactive timesheet workbooks found for the available partners.</p>
            </div>
        </template>
    </div>
</div>

@endsection
