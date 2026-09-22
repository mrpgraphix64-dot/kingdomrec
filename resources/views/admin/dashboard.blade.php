@extends('layouts.admin')

@section('title', 'Daily Operations Control Center | Kingdom Admin')

@section('content')
<div class="relative dashboard-container flex flex-col gap-6 max-w-[1600px] mx-auto pb-8">

    <!-- 1. Header Section -->
    <div class="dashboard-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shrink-0 bg-white dark:bg-slate-900 px-6 py-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h1 class="text-base sm:text-lg font-black tracking-tight text-kingdom-navy dark:text-white">Daily Operations Control Center</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Welcome back, {{ Auth::user()?->name ?? 'Administrator' }} · Real-time staffing operations & live workflow pipeline</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2.5">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800/80 px-3.5 py-2 rounded-xl border border-slate-200/60 dark:border-slate-700">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-kingdom-gold"></i>
                <span>{{ date('l, M j, Y') }}</span>
            </div>
            <a href="{{ route('admin.assignments') }}" wire:navigate class="btn-interactive px-4 py-2 bg-kingdom-navy hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs">
                <i data-lucide="user-check" class="w-3.5 h-3.5 text-kingdom-gold"></i>
                <span>Live Roster</span>
            </a>
            <a href="{{ route('admin.job-post') }}" wire:navigate class="btn-interactive px-3 py-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5" title="View Platform Repository & Analytics">
                <span>Platform Analytics</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-slate-400"></i>
            </a>
        </div>
    </div>

    <!-- 2. CONCISE SINGLE-ROW OPERATIONAL KPI GRID (5-6 Focused Cards) -->
    <div class="section-surface--ops p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-3.5 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Live Operations</span>
                <span class="text-[10px] text-slate-500 font-medium">· Real-time demand, fulfillment & staffing metrics</span>
            </div>
            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500">
                <span class="inline-flex items-center gap-1 text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live System Feed
                </span>
            </div>
        </div>

        {{-- grid-cols-1 below sm: at 2-up on a 375px screen these cards are too narrow for
             their labels ("Staff Assigned", "Open Vacancies") and subtitles, which truncate
             into unreadable fragments ("STAFF...", "Across active e..."). --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 {{ $pipeline['pending_timesheets'] > 0 ? 'lg:grid-cols-6' : 'lg:grid-cols-5' }} gap-3 sm:gap-4">
            
            <!-- 1. Events Today -->
            <a href="{{ route('admin.events') }}" wire:navigate class="metric-card metric-card--accent-blue group">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="metric-label">Events Today</span>
                        @if($todayOps['events_today'] > 0)
                            <span class="inline-flex items-center text-[9px] font-black text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 px-1.5 py-0.2 rounded">LIVE</span>
                        @endif
                    </div>
                    <div class="icon-tile icon-tile--jobs">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="min-w-0">
                    <div class="metric-number">{{ number_format($todayOps['events_today']) }}</div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">Scheduled for today</p>
                </div>
            </a>

            <!-- 2. Staff Needed / Demand -->
            <a href="{{ route('admin.assignments') }}" wire:navigate class="metric-card {{ $todayOps['staff_required'] > 0 ? 'metric-card--accent-indigo' : '' }} group">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <span class="metric-label">Staff Needed</span>
                    <div class="icon-tile icon-tile--staff">
                        <i data-lucide="users-2" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="min-w-0">
                    <div class="metric-number">{{ number_format($todayOps['staff_required']) }}</div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">Across active events</p>
                </div>
            </a>

            <!-- 3. Staff Assigned -->
            <a href="{{ route('admin.assignments') }}" wire:navigate class="metric-card metric-card--success group">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <span class="metric-label text-emerald-700 dark:text-emerald-400">Staff Assigned</span>
                    <div class="icon-tile icon-tile--success">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="min-w-0">
                    <div class="flex items-baseline justify-between gap-1">
                        <div class="metric-number text-emerald-600 dark:text-emerald-400">{{ number_format($todayOps['staff_assigned']) }}</div>
                        <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $todayOps['staff_required'] > 0 ? round(($todayOps['staff_assigned'] / $todayOps['staff_required']) * 100) . '%' : '100%' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">Confirmed placements</p>
                </div>
            </a>

            <!-- 4. Open Vacancies (Primary Warning / Action Metric) -->
            <a href="{{ route('admin.assignments') }}" wire:navigate class="metric-card {{ ($todayOps['vacancies_today'] > 0 || $kpis['open_roles']['total'] > 0) ? 'metric-card--warning bg-amber-50/40 dark:bg-amber-950/20' : '' }} group">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <span class="metric-label {{ ($todayOps['vacancies_today'] > 0 || $kpis['open_roles']['total'] > 0) ? 'text-amber-800 dark:text-amber-300' : '' }}">Open Vacancies</span>
                    <div class="icon-tile {{ ($todayOps['vacancies_today'] > 0 || $kpis['open_roles']['total'] > 0) ? 'icon-tile--vacancies animate-pulse' : 'icon-tile--quotes' }}">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="min-w-0">
                    <div class="flex items-baseline justify-between gap-1">
                        <div class="metric-number {{ ($todayOps['vacancies_today'] > 0 || $kpis['open_roles']['total'] > 0) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300' }}">
                            {{ number_format($todayOps['vacancies_today'] > 0 ? $todayOps['vacancies_today'] : $kpis['open_roles']['total']) }}
                        </div>
                        @if($todayOps['vacancies_today'] > 0)
                            <span class="text-[10px] font-black text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/60 px-1.5 py-0.5 rounded">Urgent Today</span>
                        @elseif($kpis['open_roles']['total'] > 0)
                            <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400">Needs Action</span>
                        @else
                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">All Filled</span>
                        @endif
                    </div>
                    <p class="text-[11px] font-medium mt-1 truncate {{ ($todayOps['vacancies_today'] > 0 || $kpis['open_roles']['total'] > 0) ? 'text-amber-700 dark:text-amber-400' : 'text-slate-500' }}">
                        {{ $todayOps['vacancies_today'] > 0 ? 'Urgent shifts unassigned' : ($kpis['open_roles']['total'] > 0 ? 'Active vacancies requiring action' : 'No empty shifts') }}
                    </p>
                </div>
            </a>

            <!-- 5. On Shift Now (Live Attendance) -->
            <div class="metric-card group" title="{{ empty($todayOps['on_shift_names']) ? 'No staff currently checked in' : 'Active: ' . implode(', ', $todayOps['on_shift_names']) }}">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="metric-label">On Shift Now</span>
                        @if($todayOps['on_shift_count'] > 0)
                            <span class="inline-flex items-center gap-1 text-[9px] font-black text-teal-700 dark:text-teal-300 bg-teal-100 dark:bg-teal-900/50 px-1.5 py-0.2 rounded">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-ping"></span> LIVE
                            </span>
                        @endif
                    </div>
                    <div class="icon-tile icon-tile--live">
                        <i data-lucide="radio" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="min-w-0">
                    <div class="metric-number text-teal-600 dark:text-teal-400">{{ number_format($todayOps['on_shift_count']) }}</div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
                        {{ empty($todayOps['on_shift_names']) ? 'Checked-in staff' : implode(', ', array_slice($todayOps['on_shift_names'], 0, 2)) . (count($todayOps['on_shift_names']) > 2 ? ' +' . (count($todayOps['on_shift_names']) - 2) . ' more' : '') }}
                    </p>
                </div>
            </div>

            <!-- 6. Optional: Timesheets Pending (Only shown when pending > 0) -->
            @if($pipeline['pending_timesheets'] > 0)
            <a href="{{ route('admin.time-shifting') }}" wire:navigate class="metric-card metric-card--accent-purple group">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <span class="metric-label text-purple-700 dark:text-purple-300">Timesheets Pending</span>
                    <div class="icon-tile icon-tile--events">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="min-w-0">
                    <div class="metric-number text-purple-600 dark:text-purple-400">{{ number_format($pipeline['pending_timesheets']) }}</div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">Ready for review</p>
                </div>
            </a>
            @endif

        </div>
    </div>

    <!-- 3. OPERATIONAL WORKFLOW PIPELINE (Connected Process Flow) -->
    <div class="section-surface--workflow p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-3.5 px-1">
            <div class="flex items-center gap-2">
                <i data-lucide="git-commit" class="w-4 h-4 text-kingdom-gold"></i>
                <h2 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Operational Workflow Pipeline
                </h2>
            </div>
            <span class="text-[11px] font-medium text-slate-400">Linear progression · Click any stage for action</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 relative">
            
            <!-- Stage 1: Quotations -->
            <a href="{{ route('admin.staff-quotation', ['status' => 'Pending']) }}" wire:navigate class="workflow-stage {{ $pipeline['pending_quotes'] > 0 ? 'border-blue-300 dark:border-blue-700 ring-1 ring-blue-200 dark:ring-blue-900/40' : 'hover:border-slate-300' }} group">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full {{ $pipeline['pending_quotes'] > 0 ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600' }} text-xs font-black flex items-center justify-center">1</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">1. Quotations</span>
                    </div>
                    <span class="text-[11px] font-black px-2 py-0.5 rounded-full {{ $pipeline['pending_quotes'] > 0 ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                        {{ $pipeline['pending_quotes'] }}
                    </span>
                </div>
                <div class="mt-1">
                    <p class="text-sm font-extrabold {{ $pipeline['pending_quotes'] > 0 ? 'text-kingdom-navy dark:text-white group-hover:text-blue-600' : 'text-slate-600 dark:text-slate-400' }} transition-colors">
                        {{ $pipeline['pending_quotes'] }} Awaiting Approval
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Review client quotations</p>
                </div>
            </a>

            <!-- Stage 2: Staff Placements -->
            <a href="{{ route('admin.assignments') }}" wire:navigate class="workflow-stage {{ $pipeline['active_vacancies'] > 0 ? 'border-amber-300 dark:border-amber-700 ring-1 ring-amber-200 dark:ring-amber-900/40' : 'hover:border-slate-300' }} group">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full {{ $pipeline['active_vacancies'] > 0 ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600' }} text-xs font-black flex items-center justify-center">2</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">2. Staff Placements</span>
                    </div>
                    <span class="text-[11px] font-black px-2 py-0.5 rounded-full {{ $pipeline['active_vacancies'] > 0 ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 border border-amber-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                        {{ $pipeline['active_vacancies'] }}
                    </span>
                </div>
                <div class="mt-1">
                    <p class="text-sm font-extrabold {{ $pipeline['active_vacancies'] > 0 ? 'text-kingdom-navy dark:text-white group-hover:text-amber-600' : 'text-slate-600 dark:text-slate-400' }} transition-colors">
                        {{ $pipeline['active_vacancies'] }} Staff Needed
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Fill active vacancies</p>
                </div>
            </a>

            <!-- Stage 3: Timesheets -->
            <a href="{{ route('admin.time-shifting') }}" wire:navigate class="workflow-stage {{ $pipeline['pending_timesheets'] > 0 ? 'border-purple-300 dark:border-purple-700 ring-1 ring-purple-200 dark:ring-purple-900/40' : 'hover:border-slate-300' }} group">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full {{ $pipeline['pending_timesheets'] > 0 ? 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600' }} text-xs font-black flex items-center justify-center">3</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">3. Timesheets</span>
                    </div>
                    <span class="text-[11px] font-black px-2 py-0.5 rounded-full {{ $pipeline['pending_timesheets'] > 0 ? 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 border border-purple-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                        {{ $pipeline['pending_timesheets'] }}
                    </span>
                </div>
                <div class="mt-1">
                    <p class="text-sm font-extrabold {{ $pipeline['pending_timesheets'] > 0 ? 'text-kingdom-navy dark:text-white group-hover:text-purple-600' : 'text-slate-600 dark:text-slate-400' }} transition-colors">
                        {{ $pipeline['pending_timesheets'] }} To Finalise
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Review attendance & hours</p>
                </div>
            </a>

            <!-- Stage 4: Billing & Invoices -->
            <a href="{{ route('admin.finance', ['status' => 'Awaiting Invoice']) }}" wire:navigate class="workflow-stage {{ $pipeline['ready_for_billing'] > 0 ? 'border-emerald-300 dark:border-emerald-700 ring-1 ring-emerald-200 dark:ring-emerald-900/40' : 'hover:border-slate-300' }} group">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full {{ $pipeline['ready_for_billing'] > 0 ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600' }} text-xs font-black flex items-center justify-center">4</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">4. Billing & Invoices</span>
                    </div>
                    <span class="text-[11px] font-black px-2 py-0.5 rounded-full {{ $pipeline['ready_for_billing'] > 0 ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 border border-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                        {{ $pipeline['ready_for_billing'] }}
                    </span>
                </div>
                <div class="mt-1">
                    <p class="text-sm font-extrabold {{ $pipeline['ready_for_billing'] > 0 ? 'text-kingdom-navy dark:text-white group-hover:text-emerald-600' : 'text-slate-600 dark:text-slate-400' }} transition-colors">
                        {{ $pipeline['ready_for_billing'] }} Ready to Bill
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Generate client invoices</p>
                </div>
            </a>

        </div>
    </div>

    <!-- 4. MAIN OPERATIONAL WORKSPACE (Two-Column Layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Upcoming Operations Schedule (7 Cols) -->
        <div class="lg:col-span-7 section-surface p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="icon-tile icon-tile--jobs">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-kingdom-navy dark:text-white">Upcoming Operations Schedule</h2>
                            <p class="text-[10px] text-slate-400 font-medium">Live staffing progress from active shift slots</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.events') }}" wire:navigate class="btn-interactive text-xs font-bold text-kingdom-gold hover:text-slate-900 dark:hover:text-white bg-slate-50 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-700">
                        <span>View All Events</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <!-- Table Container with responsive scroll & max 6 items -->
                <div class="overflow-x-auto custom-scrollbar -mx-5 px-5">
                    @if($upcomingEvents->isEmpty())
                        <div class="py-12 text-center flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                                <i data-lucide="calendar-check-2" class="w-6 h-6"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">No Active Operations Today</p>
                            <p class="text-[11px] text-slate-400 mt-1">There are no events currently scheduled for today.</p>
                            <div class="flex items-center gap-2 mt-4">
                                <a href="{{ route('admin.events') }}" wire:navigate class="btn-interactive px-3 py-1.5 bg-kingdom-navy hover:bg-slate-800 text-white rounded-lg text-xs font-bold">
                                    View Upcoming Events
                                </a>
                            </div>
                        </div>
                    @else
                        <table class="w-full text-left border-collapse min-w-[540px]">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                    <th class="pb-2.5 font-bold">Event & Client</th>
                                    <th class="pb-2.5 font-bold">Date</th>
                                    <th class="pb-2.5 font-bold">Staffing Progress</th>
                                    <th class="pb-2.5 font-bold">Status</th>
                                    <th class="pb-2.5 font-bold text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                @foreach($upcomingEvents->take(6) as $evt)
                                <tr class="table-row-interactive hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                                    <td class="py-3 pr-2">
                                        <p class="text-xs font-bold text-kingdom-navy dark:text-white truncate max-w-[190px]">{{ $evt['event_name'] }}</p>
                                        <p class="text-[10px] text-slate-400 truncate max-w-[190px]">{{ $evt['client'] }} · {{ $evt['venue'] }}</p>
                                    </td>
                                    <td class="py-3 pr-2 whitespace-nowrap">
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $evt['date_display'] }}</span>
                                    </td>
                                    <td class="py-3 pr-2 min-w-[140px]">
                                        <div class="flex items-center justify-between text-[10px] font-bold mb-1">
                                            <span class="{{ $evt['vacancies'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                                {{ $evt['total_assigned'] }} / {{ $evt['total_required'] }} Staff
                                            </span>
                                            <span class="text-slate-400 font-medium">{{ $evt['progress'] }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                            <div class="progress-bar-fill h-full rounded-full {{ $evt['is_fully_staffed'] ? 'bg-emerald-500' : ($evt['progress'] > 50 ? 'bg-blue-500' : 'bg-amber-500') }}" style="width: min(100%, {{ $evt['progress'] }}%)"></div>
                                        </div>
                                    </td>
                                    <td class="py-3 pr-2 whitespace-nowrap">
                                        @if($evt['is_fully_staffed'])
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                                                <i data-lucide="check" class="w-3 h-3"></i> Fully Staffed
                                            </span>
                                        @elseif($evt['vacancies'] > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
                                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $evt['vacancies'] }} Needed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                {{ $evt['status'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right whitespace-nowrap">
                                        @if($evt['vacancies'] > 0)
                                            <a href="{{ route('admin.assignments', ['event' => $evt['event_name']]) }}" wire:navigate class="btn-interactive px-3 py-1.5 rounded-lg bg-kingdom-navy hover:bg-slate-800 text-white text-[11px] font-bold shadow-xs">
                                                <span>Assign Staff</span>
                                                <i data-lucide="user-plus" class="w-3.5 h-3.5 text-kingdom-gold"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.events') }}" wire:navigate class="btn-interactive px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[11px] font-bold">
                                                <span>View Details</span>
                                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Needs Your Attention (Action Center - 5 Cols) -->
        <div class="lg:col-span-5 section-surface--warm p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-amber-200/60 dark:border-amber-900/40">
                    <div class="flex items-center gap-2.5">
                        <div class="icon-tile icon-tile--talent">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Needs Your Attention</h2>
                            <p class="text-[10px] text-slate-500 font-medium">Prioritised action items requiring operational review</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 border border-amber-300/80">
                        {{ count($attentionItems) }} Actions
                    </span>
                </div>

                <!-- Attention Items Vertical List -->
                <div class="space-y-3">
                    @forelse($attentionItems->take(4) as $item)
                    <div class="action-card border-amber-200/80 dark:border-slate-800">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded border {{ $item['badge_color'] }}">
                                        {{ $item['badge'] }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 truncate">{{ $item['subtitle'] }}</span>
                                </div>
                                <h3 class="text-xs font-bold text-kingdom-navy dark:text-white truncate">{{ $item['title'] }}</h3>
                                <p class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 mt-0.5">{{ $item['highlight'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
                            <a href="{{ $item['action_url'] }}" wire:navigate class="btn-interactive px-3.5 py-1.5 rounded-lg text-xs font-bold {{ $item['action_style'] }}">
                                <i data-lucide="{{ $item['action_icon'] }}" class="w-3.5 h-3.5"></i>
                                <span>{{ $item['action_label'] }}</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center mb-3">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Everything is up to date</p>
                        <p class="text-[11px] text-slate-400 mt-1">There are no operational actions requiring attention right now.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
