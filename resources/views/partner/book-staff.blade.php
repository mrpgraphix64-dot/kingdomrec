@extends('layouts.partner')

@section('title', isset($booking) ? 'Edit Booking | Kingdom Partner' : 'Staff Booking | Kingdom Partner')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex-none">
        <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
            {{ isset($booking) ? 'Edit Booking' : 'Staff Booking' }}
            @if(isset($booking))
                <span class="text-[12px] bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm mt-1">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                    Editing Existing Shift
                </span>
            @endif
        </h1>
        <p class="text-sm text-slate-500 mt-2">
            {{ isset($booking) ? 'Update the details for this specific operational shift.' : 'Create your dynamic workforce schedule. Add roles, assign times, and allocate preferred staff.' }}
        </p>
        @if(isset($booking) && $booking->isEditable())
            <div class="mt-3 flex items-center gap-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 px-4 py-2.5 rounded-xl w-fit">
                <i data-lucide="timer" class="text-[16px] w-5 h-5"></i>
                <span class="font-bold">Edit window closes {{ $booking->created_at->addHours(3)->diffForHumans() }}</span>
            </div>
            <div class="mt-2 flex items-center gap-2 text-xs text-slate-500 px-1">
                <span class="font-bold uppercase tracking-widest text-[10px]">Booking Ref:</span> {{ $booking->booking_ref }}
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-lg text-sm font-medium flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-lg text-sm font-medium shadow-sm">
            <ul class="list-disc list-inside space-y-1 text-[13px]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $scheduleData = [];
        if (old('items')) {
            $flatItems = old('items');
            $grouped = [];
            foreach ($flatItems as $item) {
                $roleName = $item['sub_category'] ?? 'General Staff';
                if (!isset($grouped[$roleName])) {
                    $grouped[$roleName] = [
                        'role_name' => $roleName,
                        'rate' => (float)($item['rate'] ?? 0),
                        'icon' => 'briefcase',
                        'desc' => '',
                        'expanded' => true,
                        'shifts' => []
                    ];
                }
                
                $prefStaff = [];
                if (isset($item['preferred_staff']) && is_string($item['preferred_staff'])) {
                    $prefStaff = json_decode($item['preferred_staff'], true) ?: [];
                } elseif (isset($item['preferred_staff']) && is_array($item['preferred_staff'])) {
                    $prefStaff = $item['preferred_staff'];
                }
                
                $grouped[$roleName]['shifts'][] = [
                    'id' => isset($item['id']) ? (int)$item['id'] : null,
                    'quantity' => (int)($item['quantity'] ?? 1),
                    'shift' => $item['shift'] ?? 'custom',
                    'shift_label' => $item['shift_label'] ?? 'Custom',
                    'start_time' => $item['start_time'] ?? '06:00',
                    'end_time' => $item['end_time'] ?? '12:00',
                    'calculated_hours' => (float)($item['calculated_hours'] ?? 6.0),
                    'preferred_staff' => $prefStaff,
                    'expanded' => true,
                    'conflictWarning' => false,
                    'showAllStaff' => false
                ];
            }
            $scheduleData = array_values($grouped);
        } elseif (isset($booking) && $booking->shifts) {
            $groupedShifts = $booking->shifts->groupBy('sub_category');
            foreach ($groupedShifts as $roleName => $shifts) {
                $scheduleData[] = [
                    'role_name' => $roleName,
                    'rate' => (float)$shifts->first()->rate,
                    'icon' => 'briefcase',
                    'desc' => '',
                    'expanded' => true,
                    'shifts' => $shifts->map(function($shift) {
                        $prefStaff = [];
                        if ($shift->preferred_staff) {
                            $prefStaff = is_string($shift->preferred_staff) ? json_decode($shift->preferred_staff, true) : $shift->preferred_staff;
                        }
                        return [
                            'id' => $shift->id,
                            'quantity' => $shift->quantity,
                            'shift' => $shift->shift ?? 'custom',
                            'shift_label' => $shift->shift_label ?? 'Custom',
                            'start_time' => $shift->start_time ? date('H:i', strtotime($shift->start_time)) : '06:00',
                            'end_time' => $shift->end_time ? date('H:i', strtotime($shift->end_time)) : '12:00',
                            'calculated_hours' => (float)($shift->calculated_hours ?? $shift->shift_hours ?? 6),
                            'preferred_staff' => $prefStaff ?: [],
                            'expanded' => true,
                            'conflictWarning' => false,
                            'showAllStaff' => false
                        ];
                    })->toArray()
                ];
            }
        }
    @endphp

    <script>
        window.__rolesData = @json($rolesJson);
        window.__favouriteStaffData = @json($favouriteStaff);
        window.__scheduleInitialData = @json($scheduleData);

        function partnerBookingForm() {
            return {
                init() {
                    // Match icons and descriptions on initialization
                    this.schedule.forEach(g => {
                        const roleMatch = this.roles.find(r => r.name.toLowerCase() === g.role_name.toLowerCase());
                        if (roleMatch) {
                            g.icon = roleMatch.icon || 'briefcase';
                            g.desc = roleMatch.desc || '';
                        }
                    });

                    this.$watch('schedule', () => {
                        this.$nextTick(() => { if (typeof window.lucide !== 'undefined') window.lucide.createIcons(); });
                    }, {deep: true});
                },
                roles: window.__rolesData || [],
                favouriteStaff: window.__favouriteStaffData || [],
                search: '',
                errors: {},
                submitted: false,
                submitting: false,
                templates: {
                    'morning': { label: 'Morning', start: '06:00', end: '12:00', icon: 'sun', color: 'from-amber-400 to-orange-400' },
                    'afternoon': { label: 'Afternoon', start: '12:00', end: '18:00', icon: 'sunset', color: 'from-orange-400 to-rose-400' },
                    'evening': { label: 'Evening', start: '18:00', end: '00:00', icon: 'moon', color: 'from-indigo-400 to-purple-500' },
                    'night': { label: 'Night', start: '00:00', end: '06:00', icon: 'moon-star', color: 'from-slate-600 to-slate-800' },
                    'custom': { label: 'Custom', start: '', end: '', icon: 'clock', color: 'from-blue-400 to-blue-600' }
                },

                schedule: window.__scheduleInitialData || [],

                isRoleAdded(roleName) { return this.schedule.some(g => g.role_name === roleName); },

                addRole(role) {
                    if (this.isRoleAdded(role.name)) return;
                    this.schedule.push({
                        role_name: role.name, rate: role.rate, icon: role.icon, desc: role.desc, expanded: true,
                        shifts: [this._newShift()]
                    });
                    this.$nextTick(() => { const el = document.getElementById('role-group-' + (this.schedule.length-1)); if(el) el.scrollIntoView({behavior:'smooth',block:'center'}); });
                },

                addShiftToRole(group) {
                    group.shifts.push(this._newShift());
                    group.expanded = true;
                },

                _newShift() {
                    return { id: Date.now()+Math.random(), quantity:1, shift:'morning', shift_label:'Morning', start_time:'06:00', end_time:'12:00', calculated_hours:6, preferred_staff:[], expanded:true, conflictWarning:false, showAllStaff: false };
                },

                removeRole(idx) { this.schedule.splice(idx, 1); },
                removeShift(group, sIdx) { group.shifts.splice(sIdx, 1); if(group.shifts.length === 0) { this.schedule = this.schedule.filter(g => g !== group); } },

                updateTemplate(shift, key) {
                    shift.shift = key;
                    if(key !== 'custom') { shift.start_time = this.templates[key].start; shift.end_time = this.templates[key].end; shift.shift_label = this.templates[key].label; } else { shift.shift_label = 'Custom'; }
                    this.calcHours(shift);
                },

                calcHours(shift) {
                    if(!shift.start_time || !shift.end_time) { shift.calculated_hours = 0; return; }
                    let s = new Date('1970-01-01T'+shift.start_time+':00Z'), e = new Date('1970-01-01T'+shift.end_time+':00Z');
                    let d = (e-s)/3600000; if(d<0) d+=24; shift.calculated_hours = d;
                },

                toggleStaff(shift, staff) {
                    const idx = shift.preferred_staff.findIndex(s => s.id === staff.id);
                    if(idx >= 0) shift.preferred_staff.splice(idx,1);
                    else if(shift.preferred_staff.length < shift.quantity) shift.preferred_staff.push(staff);
                },
                isStaffAssigned(shift, staffId) { return shift.preferred_staff.some(s => s.id === staffId); },
                isStaffBookedElsewhere(shift, staffId) {
                    return this.allShifts.some(s => s.id !== shift.id && s.preferred_staff.some(ps => ps.id === staffId) && this.isOverlapping(shift, s));
                },

                isOverlapping(a, b) {
                    if(!a.start_time||!a.end_time||!b.start_time||!b.end_time) return false;
                    let aS=parseInt(a.start_time.replace(':','')), aE=parseInt(a.end_time.replace(':',''));
                    if(aE<=aS) aE+=2400;
                    let bS=parseInt(b.start_time.replace(':','')), bE=parseInt(b.end_time.replace(':',''));
                    if(bE<=bS) bE+=2400;
                    return Math.max(aS,bS) < Math.min(aE,bE);
                },

                get allShifts() {
                    let items = []; let idx = 0;
                    this.schedule.forEach(g => { g.shifts.forEach(s => { items.push({...s, sub_category: g.role_name, rate: g.rate, icon: g.icon, _flatIdx: idx++}); }); });
                    return items;
                },
                get grandTotal() { return this.allShifts.reduce((s,i) => s + i.quantity * i.calculated_hours * i.rate, 0); },
                get totalStaff() { return this.allShifts.reduce((s,i) => s + parseInt(i.quantity||0), 0); },
                get totalHours() { return this.allShifts.reduce((s,i) => s + parseInt(i.quantity||0) * parseFloat(i.calculated_hours||0), 0); },
                get totalShifts() { return this.allShifts.length; },

                get filteredRoles() {
                    if (!this.search) return this.roles;
                    return this.roles.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()));
                },

                validate() {
                    this.errors = {}; this.submitted = true;
                    if (!this.$refs.eventName?.value?.trim()) this.errors.event_name = 'Please enter an event or job name';
                    if (this.schedule.length === 0) this.errors.cart = 'Please add at least one role to your schedule';
                    if (!this.$refs.startDate?.value) this.errors.start_date = 'Please select an event date';
                    if (!this.$refs.venue?.value?.trim()) this.errors.venue = 'Please enter a venue or location';
                    if (this.allShifts.some(s => !s.start_time || !s.end_time)) this.errors.cart_times = 'Please ensure all shifts have start and end times.';
                    return Object.keys(this.errors).length === 0;
                },

                submitForm(e) {
                    if (!this.validate()) { e.preventDefault(); this.$nextTick(() => { const el = this.$el.querySelector('[data-error]'); if(el) el.scrollIntoView({behavior:'smooth',block:'center'}); }); }
                    else { this.submitting = true; }
                }
            };
        }
    </script>

    <div class="flex-1 min-h-0 w-full" x-data="partnerBookingForm()">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 xl:gap-8">
            <!-- Main Form Column -->
            <div class="xl:col-span-2 flex flex-col gap-6">
                <form method="POST" novalidate @submit="submitForm($event)"
                      action="{{ isset($booking) ? route('partner.booking.update', $booking->id) : route('partner.book-staff.store') }}"
                      class="flex flex-col gap-6">
                    @csrf
                    @if(isset($booking))
                        @method('PUT')
                    @endif

                    <!-- 1. Event Details -->
                    <div class="bg-white dark:bg-slate-900 rounded-[12px] border border-[#E6EAF0] dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-[#E6EAF0] dark:border-slate-800 flex items-center gap-3">
                            <div class="p-2 bg-kingdom-gold/10 rounded-lg">
                                <i data-lucide="calendar" class="text-kingdom-gold w-4 h-4"></i>
                            </div>
                            <h2 class="text-[15px] font-bold text-slate-900 dark:text-white">
                                {{ isset($booking) ? 'Update Booking Details' : '1. Event & Location' }}
                            </h2>
                        </div>
                        
                        <div class="p-6 space-y-5">
                            <!-- Event Name -->
                            <div class="space-y-2">
                                <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300">Event / Job Name <span class="text-red-500">*</span></label>
                                <input type="text" name="event_name" x-ref="eventName" @input="if(submitted) { errors.event_name = $el.value.trim() ? '' : 'Please enter an event or job name' }"
                                       value="{{ old('event_name', $booking->event_name ?? '') }}"
                                       placeholder="e.g. Winter Gala Security, Corporate Awards Night..."
                                       class="w-full px-4 py-2.5 bg-[#F6F8FB] dark:bg-slate-800 border rounded-[8px] text-[13px] font-medium text-slate-700 dark:text-slate-300 focus:ring-1 focus:ring-kingdom-gold focus:border-kingdom-gold outline-none transition-all"
                                       :class="errors.event_name ? 'border-red-400 bg-red-50/50' : 'border-[#E6EAF0] dark:border-slate-700'">
                                <p x-show="errors.event_name" x-text="errors.event_name" data-error
                                   class="text-xs text-red-500 font-medium flex items-center gap-1 mt-1"
                                   x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"></p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <!-- Start Date -->
                                <div class="space-y-2">
                                    <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300">Event Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" x-ref="startDate" @change="if(submitted) { errors.start_date = $el.value ? '' : 'Please select an event date' }; if($refs.endDate.value && $refs.endDate.value < $el.value) { $refs.endDate.value = $el.value; }"
                                           value="{{ old('start_date', isset($booking) && $booking->start_date ? $booking->start_date->format('Y-m-d') : '') }}"
                                           class="w-full px-4 py-2.5 bg-[#F6F8FB] dark:bg-slate-800 border rounded-[8px] text-[13px] font-medium text-slate-700 dark:text-slate-300 focus:ring-1 focus:ring-kingdom-gold focus:border-kingdom-gold outline-none transition-all"
                                           :class="errors.start_date ? 'border-red-400 bg-red-50/50' : 'border-[#E6EAF0] dark:border-slate-700'">
                                    <p x-show="errors.start_date" x-text="errors.start_date" data-error
                                       class="text-xs text-red-500 font-medium mt-1"></p>
                                </div>
                                <!-- End Date -->
                                <div class="space-y-2">
                                    <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300">End Date <span class="text-[#94A3B8] font-normal text-xs">(Optional)</span></label>
                                    <input type="date" name="end_date" x-ref="endDate" x-bind:min="$refs.startDate ? $refs.startDate.value : null"
                                           value="{{ old('end_date', isset($booking) && $booking->end_date ? $booking->end_date->format('Y-m-d') : '') }}"
                                           class="w-full px-4 py-2.5 bg-[#F6F8FB] dark:bg-slate-800 border rounded-[8px] text-[13px] font-medium text-slate-700 dark:text-slate-300 focus:ring-1 focus:ring-kingdom-gold focus:border-kingdom-gold border-[#E6EAF0] dark:border-slate-700 outline-none transition-all">
                                </div>
                                <!-- Venue -->
                                <div class="space-y-2">
                                    <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300">Venue / Location <span class="text-red-500">*</span></label>
                                    <input type="text" name="venue" x-ref="venue" @input="if(submitted) { errors.venue = $el.value.trim() ? '' : 'Please enter a venue or location' }"
                                           value="{{ old('venue', $booking->venue ?? '') }}"
                                           placeholder="e.g. O2 Arena, London"
                                           class="w-full px-4 py-2.5 bg-[#F6F8FB] dark:bg-slate-800 border rounded-[8px] text-[13px] font-medium text-slate-700 dark:text-slate-300 focus:ring-1 focus:ring-kingdom-gold focus:border-kingdom-gold outline-none transition-all"
                                           :class="errors.venue ? 'border-red-400 bg-red-50/50' : 'border-[#E6EAF0] dark:border-slate-700'">
                                    <p x-show="errors.venue" x-text="errors.venue" data-error
                                       class="text-xs text-red-500 font-medium mt-1"></p>
                                </div>
                            </div>

                            <!-- Special Requirements -->
                            <div class="space-y-2">
                                <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300">Special Requirements / Post Orders</label>
                                <textarea name="special_requirements" rows="2" placeholder="Any specific requirements, certifications, or operational preferences..."
                                          class="w-full px-4 py-2.5 bg-[#F6F8FB] dark:bg-slate-800 border border-[#E6EAF0] dark:border-slate-700 rounded-[8px] text-[13px] font-medium text-slate-700 dark:text-slate-300 focus:ring-1 focus:ring-kingdom-gold focus:border-kingdom-gold outline-none transition-all resize-none">{{ old('special_requirements', $booking->special_requirements ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Add Roles Catalog -->
                    <div class="bg-white dark:bg-slate-900 rounded-[12px] border border-[#E6EAF0] dark:border-slate-800 shadow-sm overflow-hidden" id="staff-types-section">
                        <div class="p-5 border-b border-[#E6EAF0] dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-kingdom-gold/10 rounded-lg">
                                    <i data-lucide="users" class="w-4 h-4 text-kingdom-gold"></i>
                                </div>
                                <div>
                                    <h2 class="text-[15px] font-bold text-slate-900 dark:text-white">2. Staff Category</h2>
                                    <p class="text-[11px] text-[#6B7280]">Select the roles required for this event.</p>
                                </div>
                            </div>
                            <!-- Search -->
                            <div class="relative w-full sm:w-56">
                                <i data-lucide="search" class="text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4"></i>
                                <input type="text" x-model="search" placeholder="Search roles..."
                                       class="w-full pl-9 pr-4 py-2 bg-[#F6F8FB] dark:bg-slate-800 border border-[#E6EAF0] dark:border-slate-700 rounded-lg text-xs text-slate-700 dark:text-slate-300 outline-none focus:ring-1 focus:ring-kingdom-gold focus:border-kingdom-gold transition-all">
                            </div>
                        </div>

                         <!-- Cart validation error -->
                        <template x-if="errors.cart">
                            <div class="mx-6 mt-5 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 font-medium flex items-center gap-2" data-error>
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                <span x-text="errors.cart"></span>
                            </div>
                        </template>

                        <!-- Role Cards Grid -->
                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[300px] overflow-y-auto custom-scrollbar">
                            <template x-for="role in filteredRoles" :key="role.id ?? role.name">
                                <button type="button" @click="addRole(role)" :disabled="isRoleAdded(role.name)"
                                        class="w-full text-left p-3.5 rounded-xl border transition-all duration-200 group relative flex flex-col justify-between min-h-[76px]"
                                        :class="isRoleAdded(role.name) ? 'border-emerald-200 bg-emerald-50/50 dark:bg-emerald-900/10 cursor-default opacity-75' : 'border-[#E6EAF0] dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-kingdom-gold hover:shadow-sm'">
                                    
                                    <!-- Added checkmark -->
                                    <div x-show="isRoleAdded(role.name)" class="absolute -top-2 -right-2 w-6 h-6 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-md">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <!-- Add plus icon -->
                                    <div x-show="!isRoleAdded(role.name)" class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 group-hover:-translate-y-1 transition-all duration-200 w-6 h-6 bg-kingdom-gold text-white rounded-full flex items-center justify-center shadow-md">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg transition-colors duration-200"
                                             :class="isRoleAdded(role.name) ? 'bg-emerald-100 text-emerald-600' : 'bg-[#F6F8FB] dark:bg-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-kingdom-gold/10 group-hover:text-kingdom-gold'">
                                            <i :data-lucide="role.icon" class="w-4 h-4"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1.5">
                                                <h3 class="text-[12px] font-bold truncate" :class="isRoleAdded(role.name) ? 'text-emerald-700' : 'text-slate-900 dark:text-white'" x-text="role.name"></h3>
                                                <span class="text-[11px] font-bold px-2 py-0.5 rounded flex-shrink-0 transition-colors"
                                                      :class="isRoleAdded(role.name) ? 'bg-emerald-100 text-emerald-600' : 'bg-[#F6F8FB] dark:bg-slate-700 text-slate-600 dark:text-slate-400 group-hover:bg-kingdom-gold group-hover:text-white'"
                                                      x-text="isRoleAdded(role.name) ? '✓ Added' : '£' + role.rate.toFixed(2) + '/hr'"></span>
                                            </div>
                                            <p class="text-[11px] text-[#6B7280] mt-0.5 truncate" x-text="role.desc"></p>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- 3. Grouped Shift Scheduling -->
                    <div x-show="schedule.length > 0" x-collapse style="display: none;" class="space-y-4">
                        <div class="flex items-center gap-3 mb-2 px-1">
                            <div class="p-2 bg-kingdom-gold/10 rounded-lg"><i data-lucide="calendar-clock" class="w-4 h-4 text-kingdom-gold"></i></div>
                            <div>
                                <h2 class="text-[15px] font-bold text-slate-900 dark:text-white">3. Schedule & Assignments</h2>
                                <p class="text-[11px] text-[#6B7280]">Configure shift times and allocate your workforce per role.</p>
                            </div>
                        </div>

                        <template x-if="errors.cart_times">
                            <div class="px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 font-medium flex items-center gap-2" data-error>
                                <i data-lucide="alert-circle" class="w-5 h-5"></i><span x-text="errors.cart_times"></span>
                            </div>
                        </template>

                        <!-- Role Group Containers -->
                        <template x-for="(group, gIdx) in schedule" :key="group.role_name">
                            <div :id="'role-group-' + gIdx" class="bg-white dark:bg-slate-900 rounded-[12px] border border-[#E6EAF0] dark:border-slate-800 shadow-sm overflow-hidden">
                                
                                <!-- Role Group Header -->
                                <div class="p-4 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-900 border-b border-[#E6EAF0] dark:border-slate-800 flex items-center justify-between gap-4 cursor-pointer" @click="group.expanded = !group.expanded">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-kingdom-navy/5 dark:bg-slate-700 text-kingdom-navy dark:text-slate-300 flex items-center justify-center border border-kingdom-navy/10 shrink-0">
                                            <i :data-lucide="group.icon" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-[14px] font-bold text-slate-900 dark:text-white" x-text="group.role_name"></h3>
                                            <div class="flex items-center gap-2 text-[11px] text-slate-500 font-medium mt-0.5">
                                                <span x-text="group.shifts.length + ' shift' + (group.shifts.length > 1 ? 's' : '') + ' configured'"></span>
                                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                <span x-text="'£' + group.rate.toFixed(2) + '/hr'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-right hidden sm:block mr-2">
                                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Role Total</p>
                                            <p class="text-[14px] font-black text-slate-800 dark:text-white" x-text="'£' + group.shifts.reduce((s,sh) => s + sh.quantity * sh.calculated_hours * group.rate, 0).toFixed(2)"></p>
                                        </div>
                                        <button type="button" @click.stop="removeRole(gIdx)" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Remove Role"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                        <button type="button" class="p-2 text-slate-400 hover:text-slate-700 rounded-lg transition-transform duration-200" :class="group.expanded ? 'rotate-180' : ''"><i data-lucide="chevron-down" class="w-4 h-4"></i></button>
                                    </div>
                                </div>

                                <!-- Shifts inside this role group -->
                                <div x-show="group.expanded" x-collapse>
                                    <template x-for="(shift, sIdx) in group.shifts" :key="shift.id">
                                        <div class="border-b border-[#E6EAF0] dark:border-slate-800 last:border-b-0">
                                            <!-- Hidden inputs flattened for form submission -->
                                            <template x-for="flatItem in [allShifts.find(f => f.id === shift.id)]" :key="'inp-'+shift.id">
                                                <div>
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][sub_category]'" :value="group.role_name">
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][rate]'" :value="group.rate">
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][quantity]'" :value="shift.quantity">
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][shift]'" :value="shift.shift">
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][shift_label]'" :value="shift.shift_label">
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][start_time]'" :value="shift.start_time">
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][end_time]'" :value="shift.end_time">
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][calculated_hours]'" :value="shift.calculated_hours">
                                                    <input type="hidden" :name="'items[' + flatItem._flatIdx + '][preferred_staff]'" :value="JSON.stringify(shift.preferred_staff)">
                                                </div>
                                            </template>

                                            <!-- Shift sub-header -->
                                            <div class="px-4 py-3 bg-white dark:bg-slate-900 flex items-center justify-between cursor-pointer hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors" @click="shift.expanded = !shift.expanded">
                                                <div class="flex items-center gap-3">
                                                    <span class="w-6 h-6 rounded-md bg-[#F6F8FB] dark:bg-slate-800 border border-[#E6EAF0] dark:border-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center text-[11px] font-black" x-text="sIdx + 1"></span>
                                                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                                                        <span class="text-[13px] font-bold text-slate-900 dark:text-white" x-text="shift.shift_label + ' Shift'"></span>
                                                        <div class="hidden sm:block w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium" x-text="shift.start_time + ' – ' + shift.end_time + ' · ' + shift.calculated_hours + 'h · ' + shift.quantity + ' staff'"></span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-[13px] font-black text-slate-900 dark:text-white" x-text="'£' + (shift.quantity * shift.calculated_hours * group.rate).toFixed(2)"></span>
                                                    <div class="flex items-center gap-1 border-l border-slate-200 dark:border-slate-700 pl-3">
                                                        <button type="button" @click.stop="removeShift(group, sIdx)" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-md transition-colors" title="Remove Shift"><i data-lucide="x" class="w-4 h-4"></i></button>
                                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform" :class="shift.expanded ? 'rotate-180' : ''"></i>
                                                    </div>
                                                </div>
                                            </div>
                                    <div class="p-4 md:p-5 bg-slate-50 dark:bg-slate-800/50 border-t border-[#E6EAF0] dark:border-slate-800 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)]" x-show="shift.expanded" x-collapse>

                                        <!-- Unified Scheduling Toolbar -->
                                        <div class="flex flex-col xl:flex-row gap-5 xl:items-start">
                                            
                                            <!-- Required Staff -->
                                            <div class="xl:w-[140px] shrink-0">
                                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Required Staff</label>
                                                <div class="flex items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-1 shadow-sm h-[42px]">
                                                    <button type="button" @click="shift.quantity = Math.max(1, shift.quantity - 1); if(shift.preferred_staff.length > shift.quantity) shift.preferred_staff.pop();" class="w-8 h-8 flex items-center justify-center rounded bg-[#F6F8FB] dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"><i data-lucide="minus" class="w-3.5 h-3.5"></i></button>
                                                    <div class="flex-1 text-center font-bold text-[14px] text-slate-900 dark:text-white" x-text="shift.quantity"></div>
                                                    <button type="button" @click="shift.quantity = Math.min(100, shift.quantity + 1);" class="w-8 h-8 flex items-center justify-center rounded bg-kingdom-gold text-white hover:bg-yellow-600 transition-colors shadow-sm"><i data-lucide="plus" class="w-3.5 h-3.5"></i></button>
                                                </div>
                                            </div>

                                            <!-- Shift Template -->
                                            <div class="flex-1 min-w-0">
                                                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Shift Template</label>
                                                <div class="flex flex-wrap gap-1.5 bg-slate-100 dark:bg-slate-800 p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-inner">
                                                    <template x-for="(tpl, key) in templates" :key="key">
                                                        <button type="button" @click="updateTemplate(shift, key)" class="flex-1 min-w-[80px] flex items-center justify-center rounded transition-all text-[11px] font-bold px-2 py-2" :class="shift.shift === key ? 'bg-white dark:bg-slate-700 shadow border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white transform scale-[1.02]' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 dark:text-slate-400 dark:hover:text-slate-300'">
                                                            <div class="flex items-center justify-center gap-1.5">
                                                                <i :data-lucide="tpl.icon" class="w-3.5 h-3.5 shrink-0" :class="shift.shift === key ? tpl.color : 'text-slate-400 dark:text-slate-500'"></i>
                                                                <span class="whitespace-nowrap" x-text="tpl.label"></span>
                                                            </div>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Time Controls -->
                                            <div class="flex gap-3 xl:w-auto shrink-0">
                                                <div class="flex flex-col gap-3">
                                                    <div>
                                                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Start Time</label>
                                                        <input type="time" x-model="shift.start_time" @change="shift.shift = 'custom'; shift.shift_label = 'Custom'; calcHours(shift);" class="w-[125px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-[13px] font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-kingdom-gold outline-none h-[42px] shadow-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">End Time</label>
                                                        <input type="time" x-model="shift.end_time" @change="shift.shift = 'custom'; shift.shift_label = 'Custom'; calcHours(shift);" class="w-[125px] px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-[13px] font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-kingdom-gold outline-none h-[42px] shadow-sm">
                                                    </div>
                                                </div>
                                                <div class="flex flex-col">
                                                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Hours</label>
                                                    <div class="px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-center h-[42px] min-w-[55px] shadow-sm">
                                                        <span class="text-[13px] font-black text-slate-700 dark:text-slate-300" x-text="shift.calculated_hours + 'h'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Workforce Assignment (Favourites) -->
                                        <div class="mt-5 pt-4 border-t border-[#E6EAF0] dark:border-slate-800">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                                                <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                                    <i data-lucide="star" class="w-3.5 h-3.5 text-kingdom-gold"></i> Favourite Staff
                                                </label>
                                                <div class="flex items-center gap-3">
                                                    <label class="flex items-center gap-1.5 cursor-pointer mr-2">
                                                        <input type="checkbox" x-model="shift.showAllStaff" class="rounded border-slate-300 text-kingdom-gold focus:ring-kingdom-gold w-3.5 h-3.5 cursor-pointer">
                                                        <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider cursor-pointer">Show All Roles</span>
                                                    </label>
                                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider bg-white dark:bg-slate-900 px-2 py-0.5 rounded border border-[#E6EAF0] dark:border-slate-700">Assigned: <span class="text-slate-800 dark:text-white" x-text="shift.preferred_staff.length"></span></div>
                                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider bg-white dark:bg-slate-900 px-2 py-0.5 rounded border border-[#E6EAF0] dark:border-slate-700">Open Slots: <span :class="shift.quantity - shift.preferred_staff.length === 0 ? 'text-emerald-600' : 'text-blue-600'" x-text="shift.quantity - shift.preferred_staff.length"></span></div>
                                                </div>
                                            </div>
                                            <template x-if="favouriteStaff.length === 0">
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-900 rounded-lg border border-dashed border-[#E6EAF0] dark:border-slate-700">
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">No favourite staff members saved.</p>
                                                    <a href="{{ route('partner.favourite-staff') }}" class="text-[11px] font-bold text-kingdom-gold hover:underline">Manage Favourites</a>
                                                </div>
                                            </template>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-if="favouriteStaff.filter(s => shift.showAllStaff || s.role === group.role_name).length === 0 && favouriteStaff.length > 0">
                                                    <div class="w-full text-center py-3 text-[11px] text-slate-400 font-medium bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-dashed border-slate-200 dark:border-slate-700">
                                                        No favourite staff match this role. <button type="button" @click="shift.showAllStaff = true" class="text-kingdom-gold hover:underline font-bold">Show all favourites</button>
                                                    </div>
                                                </template>
                                                <template x-for="staff in favouriteStaff.filter(s => shift.showAllStaff || s.role === group.role_name)" :key="staff.id">
                                                    <button type="button" @click="toggleStaff(shift, staff)" :disabled="isStaffBookedElsewhere(shift, staff.id) && !isStaffAssigned(shift, staff.id)"
                                                            class="flex items-center gap-2 p-1 pr-3 rounded-full border transition-all duration-200 text-left relative focus:outline-none focus:ring-2 focus:ring-kingdom-gold focus:ring-offset-1"
                                                            :class="isStaffAssigned(shift, staff.id) ? 'border-kingdom-gold bg-kingdom-gold/5 shadow-sm' : (isStaffBookedElsewhere(shift, staff.id) ? 'border-[#E6EAF0] dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 opacity-50 cursor-not-allowed' : 'border-[#E6EAF0] dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-sm')">
                                                        
                                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-600 flex items-center justify-center text-slate-600 dark:text-slate-300 text-[9px] font-bold shrink-0 overflow-hidden relative border border-white/50 dark:border-black/50">
                                                            <span x-text="staff.name.substring(0,2).toUpperCase()" class="relative z-0"></span>
                                                            <template x-if="staff.image">
                                                                <img :src="staff.image.startsWith('http') ? staff.image : ('/' + staff.image)" :alt="staff.name" class="w-full h-full object-cover absolute inset-0" x-on:error="$el.style.display='none'">
                                                            </template>
                                                            
                                                            <!-- Checkmark Overlay -->
                                                            <div x-show="isStaffAssigned(shift, staff.id)" class="absolute inset-0 bg-kingdom-gold/90 flex items-center justify-center z-10" x-transition>
                                                                <i data-lucide="check" class="text-white w-3.5 h-3.5"></i>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex flex-col justify-center min-w-[50px] max-w-[120px]">
                                                            <p class="text-[11px] font-bold text-slate-800 dark:text-slate-200 leading-none truncate" x-text="staff.name"></p>
                                                            <p class="text-[9px] font-bold uppercase leading-none mt-1 truncate" :class="isStaffBookedElsewhere(shift, staff.id) && !isStaffAssigned(shift, staff.id) ? 'text-red-500' : 'text-slate-400 dark:text-slate-500'" x-text="isStaffBookedElsewhere(shift, staff.id) && !isStaffAssigned(shift, staff.id) ? 'Unavailable' : staff.role"></p>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                        </div>
                                    </template>

                                    <!-- Add Another Shift / Create Separate Shift -->
                                    <div class="px-4 py-3 border-t border-[#E6EAF0] dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/50 flex justify-center sm:justify-start">
                                        <button type="button" @click="addShiftToRole(group)" class="text-[12px] font-bold text-kingdom-gold hover:text-yellow-700 dark:hover:text-kingdom-gold flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-kingdom-gold/10 transition-colors focus:outline-none focus:ring-2 focus:ring-kingdom-gold focus:ring-offset-1">
                                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                            Add Another Shift
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Submit & Delete Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-10">
                        @if(isset($booking))
                            <!-- Delete Form -->
                            <div class="sm:w-1/3">
                                <button type="button" @click="$dispatch('open-confirm-modal', {
                                        title: 'Delete Shift',
                                        message: 'Are you sure you want to delete this shift? This action cannot be undone.',
                                        onConfirm: () => document.getElementById('delete-booking-form').submit()
                                    })"
                                        class="w-full py-4 bg-white dark:bg-slate-800 border-2 border-red-100 hover:border-red-500 hover:bg-red-50 text-red-500 rounded-[10px] font-black text-[15px] uppercase tracking-wider transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    Delete Shift
                                </button>
                            </div>
                        @endif

                        <button type="submit" :disabled="submitting || schedule.length === 0"
                                class="{{ isset($booking) ? 'sm:w-2/3' : 'w-full' }} py-4 bg-kingdom-gold hover:bg-yellow-600 text-white rounded-[10px] font-black text-[15px] uppercase tracking-wider transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2 focus-visible:outline-none">
                            <template x-if="!submitting">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="{{ isset($booking) ? 'save' : 'send' }}" class="w-5 h-5"></i>
                                    <span x-text="schedule.length > 0 ? '{{ isset($booking) ? 'Update Operations Schedule' : 'Submit Booking Request' }} (' + totalShifts + ' shifts · ' + schedule.length + ' roles)' : 'Add staff to schedule first'"></span>
                                </span>
                            </template>
                            <template x-if="submitting">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing Operation...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
                
                @if(isset($booking))
                <!-- Hidden Delete Form (Triggered by Alpine) -->
                <form id="delete-booking-form" action="{{ route('partner.booking.destroy', $booking->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                @endif
            </div>

            <!-- Sticky Cart Sidebar -->
            <div class="xl:col-span-1 border-t xl:border-t-0 border-[#E6EAF0] dark:border-slate-800 pt-6 xl:pt-0 pb-10 xl:pb-0">
                <div class="sticky top-[88px] space-y-4">
                    
                    <!-- Operational Summary -->
                    <div x-ref="cartPanel" class="bg-kingdom-navy border border-kingdom-navy rounded-[12px] p-1 shadow-lg">
                        <div class="bg-white dark:bg-slate-900 rounded-[10px] p-5 h-full">
                            
                            <div class="flex items-center justify-between pb-4 border-b border-[#E6EAF0] dark:border-slate-800 mb-4">
                                <h3 class="text-[13px] font-black text-kingdom-navy dark:text-white uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="clipboard-check" class="text-kingdom-gold w-4 h-4"></i>
                                    Operations Summary
                                </h3>
                            </div>
                            
                            <!-- Empty State -->
                            <div x-show="schedule.length === 0" class="text-center py-6 text-slate-400">
                                <i data-lucide="calendar-off" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                <p class="text-[13px] font-bold text-slate-500">No roles scheduled</p>
                                <p class="text-[11px] mt-1">Add roles to build your roster.</p>
                            </div>

                            <!-- Role Groups in Sidebar -->
                            <div x-show="schedule.length > 0" class="space-y-4 mb-6 max-h-[400px] overflow-y-auto custom-scrollbar pr-2" style="display:none;">
                                <template x-for="group in schedule" :key="group.role_name">
                                    <div class="relative">
                                        <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                            <i :data-lucide="group.icon" class="w-3.5 h-3.5 text-kingdom-gold"></i>
                                            <span x-text="group.role_name"></span>
                                        </h4>
                                        <template x-for="sh in group.shifts" :key="sh.id">
                                            <div class="ml-5 mb-1.5 flex justify-between items-start text-[10px]">
                                                <div>
                                                    <p class="font-bold text-slate-600" x-text="sh.shift_label + ' · ' + sh.start_time + '–' + sh.end_time"></p>
                                                    <p class="text-slate-400 font-medium" x-text="sh.quantity + ' staff · ' + sh.calculated_hours + 'h each'"></p>
                                                </div>
                                                <span class="font-black text-slate-700 text-[11px]" x-text="'£' + (sh.quantity * sh.calculated_hours * group.rate).toFixed(2)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Totals -->
                            <div x-show="schedule.length > 0" class="border-t border-[#E6EAF0] dark:border-slate-800 pt-4 space-y-2 mt-4" style="display:none;" x-transition>
                                <div class="flex justify-between items-center text-[12px]">
                                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Roles</span>
                                    <span class="font-black text-slate-800 dark:text-white" x-text="schedule.length"></span>
                                </div>
                                <div class="flex justify-between items-center text-[12px]">
                                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Total Shifts</span>
                                    <span class="font-black text-slate-800 dark:text-white" x-text="totalShifts"></span>
                                </div>
                                <div class="flex justify-between items-center text-[12px]">
                                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Total Staff</span>
                                    <span class="font-black text-slate-800 dark:text-white" x-text="totalStaff"></span>
                                </div>
                                <div class="flex justify-between items-center text-[12px]">
                                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Total Billed Hours</span>
                                    <span class="font-black text-slate-800 dark:text-white" x-text="totalHours"></span>
                                </div>
                                <div class="py-3 border-t-2 border-kingdom-navy dark:border-slate-800 mt-3 flex justify-between items-end">
                                    <span class="text-[12px] text-kingdom-navy font-black uppercase tracking-wider">Est. Budget</span>
                                    <span class="text-[20px] font-black text-kingdom-gold leading-none" x-text="'£' + grandTotal.toLocaleString('en-GB', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                </div>
                                <p class="text-[9px] text-slate-400 text-center uppercase tracking-widest font-bold mt-1">Excludes VAT & Expenses</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-slate-900 rounded-[12px] p-5 border border-[#E6EAF0] dark:border-slate-800 shadow-sm">
                        <ul class="space-y-3 text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                            <li class="flex gap-2.5">
                                <i data-lucide="shield-check" class="text-emerald-500 shrink-0 mt-0.5 w-4 h-4"></i>
                                <span>All deployed staff are vetted, licensed, and DBS checked.</span>
                            </li>
                            <li class="flex gap-2.5">
                                <i data-lucide="clock" class="text-kingdom-gold shrink-0 mt-0.5 w-4 h-4"></i>
                                <span>Roster locks <strong>24 hours</strong> before event start.</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
