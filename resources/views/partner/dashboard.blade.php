@extends('layouts.partner')

@section('content')
<div class="flex flex-col min-h-full space-y-4 sm:space-y-5 pb-4">

    <!-- Top Header section -->
    <div class="flex-none flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4">
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-black text-kingdom-navy dark:text-white tracking-tight truncate">Partner Dashboard</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Review your real-time recruitment performance and active staffing status.</p>
        </div>
        <a href="mailto:info@kingdom.com?subject=Requesting%20New%20Staff" class="btn-interactive bg-kingdom-gold hover:bg-yellow-600 text-slate-900 px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl font-bold text-xs sm:text-[13px] shadow-lg shadow-kingdom-gold/20 flex items-center gap-1.5 w-fit shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Request New Staff
        </a>
    </div>

    <!-- Stats Grid — fluid responsive: 1→2→4 columns -->
    {{-- grid-cols-1 below sm: 2-up at 320/375px truncates "Active Placements" into
         "Active Placeme...". Same fix as the admin KPI grids. --}}
    <div class="flex-none grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 md:gap-4">

        <!-- Card 1: Active Placements -->
        <div class="card-interactive relative overflow-hidden bg-slate-50/80 dark:bg-slate-800/40 backdrop-blur-sm p-3 sm:p-4 lg:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/50 shadow-sm h-full flex flex-col justify-between min-h-[140px] sm:min-h-[160px]">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/15 transition-colors duration-500"></div>
            <div class="relative z-10 flex flex-col gap-1 sm:gap-1.5">
                <span class="text-[9px] sm:text-[10px] md:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-tight truncate">Active Placements</span>
                <div class="card-icon p-1.5 sm:p-2 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg sm:rounded-xl text-emerald-600 dark:text-emerald-400 w-fit">
                    <i data-lucide="users" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </div>
            </div>
            <div class="relative z-10 mt-2">
                <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-slate-800 dark:text-white tracking-tight leading-none truncate">{{ $partnerStats['active_placements'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- Card 2: Open Shifts -->
        <div class="card-interactive relative overflow-hidden bg-slate-50/80 dark:bg-slate-800/40 backdrop-blur-sm p-3 sm:p-4 lg:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/50 shadow-sm h-full flex flex-col justify-between min-h-[140px] sm:min-h-[160px]">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/15 transition-colors duration-500"></div>
            <div class="relative z-10 flex flex-col gap-1 sm:gap-1.5">
                <span class="text-[9px] sm:text-[10px] md:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-tight truncate">Open Shifts</span>
                <div class="card-icon p-1.5 sm:p-2 bg-blue-100 dark:bg-blue-500/20 rounded-lg sm:rounded-xl text-blue-600 dark:text-blue-400 w-fit">
                    <i data-lucide="calendar-clock" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </div>
            </div>
            <div class="relative z-10 mt-2">
                <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-slate-800 dark:text-white tracking-tight leading-none truncate">{{ $partnerStats['open_shifts'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- Card 3: Monthly Billing -->
        <div class="card-interactive relative overflow-hidden bg-slate-50/80 dark:bg-slate-800/40 backdrop-blur-sm p-3 sm:p-4 lg:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/50 shadow-sm h-full flex flex-col justify-between min-h-[140px] sm:min-h-[160px]">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/15 transition-colors duration-500"></div>
            <div class="relative z-10 flex flex-col gap-1 sm:gap-1.5">
                <span class="text-[9px] sm:text-[10px] md:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-tight truncate">Monthly Billing</span>
                <div class="card-icon p-1.5 sm:p-2 bg-amber-100 dark:bg-amber-500/20 rounded-lg sm:rounded-xl text-amber-600 dark:text-amber-400 w-fit">
                    <i data-lucide="banknote" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </div>
            </div>
            <div class="relative z-10 mt-2">
                <h3 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none truncate" title="£{{ $partnerStats['monthly_billing'] ?? '0.00' }}">£{{ $partnerStats['monthly_billing'] ?? '0.00' }}</h3>
            </div>
        </div>

        <!-- Card 4: Total Bookings -->
        <div class="card-interactive relative overflow-hidden bg-slate-50/80 dark:bg-slate-800/40 backdrop-blur-sm p-3 sm:p-4 lg:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/50 shadow-sm h-full flex flex-col justify-between min-h-[140px] sm:min-h-[160px]">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/15 transition-colors duration-500"></div>
            <div class="relative z-10 flex flex-col gap-1 sm:gap-1.5">
                <span class="text-[9px] sm:text-[10px] md:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-tight truncate">Total Bookings</span>
                <div class="card-icon p-1.5 sm:p-2 bg-purple-100 dark:bg-purple-500/20 rounded-lg sm:rounded-xl text-purple-600 dark:text-purple-400 w-fit">
                    <i data-lucide="badge-check" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </div>
            </div>
            <div class="relative z-10 mt-2">
                <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-slate-800 dark:text-white tracking-tight leading-none truncate">{{ $partnerStats['total_bookings'] ?? 0 }}</h3>
            </div>
        </div>

    </div>

    <!-- Main Content Area -->
    <div class="flex-1 relative flex flex-col min-h-0">
        <div class="flex-1 grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-5 md:gap-6 min-h-0 xl:h-full">
            
            <!-- LEFT COLUMN: Events Table -->
            <div class="xl:col-span-2 flex flex-col xl:h-full min-h-0">
    
                <!-- Upcoming Event Staffing -->
                <div class="flex-1 min-h-[150px] flex flex-col relative bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border-t border-l border-white/60 dark:border-white/10 overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.12)] transition-all duration-500 group">
                    <!-- Glow effect -->
                    <div class="absolute -left-32 -top-32 w-64 h-64 bg-kingdom-gold/5 rounded-full blur-3xl group-hover:bg-kingdom-gold/10 transition-colors duration-500 pointer-events-none"></div>
                    
                    <!-- Header -->
                    <div class="relative p-3 sm:p-4 lg:p-5 border-b border-slate-100 dark:border-slate-800/60 flex justify-between items-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 gap-3">
                        <h4 class="font-extrabold text-sm sm:text-base text-kingdom-navy dark:text-white flex items-center gap-2 sm:gap-3 min-w-0">
                            <div class="p-1.5 sm:p-2 bg-gradient-to-br from-[#0F1D33] to-[#1a2b4c] text-[#B89955] rounded-lg sm:rounded-xl shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shrink-0">
                                <i data-lucide="calendar-check" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            </div>
                            <span class="truncate">Upcoming Event Staffing</span>
                        </h4>
                        <a class="text-[10px] sm:text-xs text-[#B89955] font-bold hover:text-[#0F1D33] dark:hover:text-white transition-colors bg-[#B89955]/10 hover:bg-[#B89955]/20 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-lg whitespace-nowrap shrink-0" href="{{ route('partner.event-list') }}" wire:navigate>View All</a>
                    </div>

                    <!-- Table with responsive scroll -->
                    <div class="p-2 sm:p-3 md:p-4 flex-1 overflow-y-auto overflow-x-auto custom-scrollbar relative min-h-[150px] flex flex-col">
                        @if(isset($partnerJobs) && count($partnerJobs) > 0)
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden min-w-[500px] sm:min-w-0">
                                <table class="w-full text-left flex-none whitespace-nowrap border-collapse">
                                    <thead class="bg-[#0f1f3d] text-white sticky top-0 z-10">
                                        <tr class="bg-[#0f1f3d] text-white text-[10px] uppercase tracking-wide font-semibold">
                                            <th class="px-4 py-3 text-white w-[30%]">Event / Location</th>
                                            <th class="px-4 py-3 text-white">Role / Staff</th>
                                            <th class="px-4 py-3 text-white w-[20%]">Date</th>
                                            <th class="px-4 py-3 text-center text-white w-[15%]">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-transparent">
                                        @forelse($partnerJobs as $booking)
                                            @php
                                                $statusMap = [
                                                    'Approved' => ['class' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/30', 'text' => 'Approved'],
                                                    'Confirmed' => ['class' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/30', 'text' => 'Confirmed'],
                                                    'Pending' => ['class' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/30', 'text' => 'Pending'],
                                                    'Draft' => ['class' => 'bg-slate-50 text-slate-600 ring-1 ring-slate-500/20 dark:bg-slate-500/10 dark:text-slate-400 dark:ring-slate-500/30', 'text' => 'Draft'],
                                                    'Sent' => ['class' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/30', 'text' => 'Sent'],
                                                ];
                                                $st = $statusMap[$booking->status] ?? ['class' => 'bg-slate-50 text-slate-600 ring-1 ring-slate-500/20', 'text' => $booking->status];
                                            @endphp
                                            <tr class="table-row-interactive transition-colors duration-150 group border-b border-slate-100 hover:bg-slate-50 bg-white">
                                                <td class="px-4 py-3">
                                                    <p class="text-[12px] sm:text-[13px] font-bold text-slate-800 dark:text-slate-200 leading-tight">{{ $booking->event_name ?: $booking->sub_category }}</p>
                                                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5">{{ $booking->venue ?: 'TBD' }}</p>
                                                </td>
                                                <td class="px-4 py-3 text-[12px] sm:text-[13px] text-slate-600 dark:text-slate-300 font-medium">
                                                    <span class="hidden sm:inline">{{ $booking->sub_category }}</span>
                                                    <span class="sm:hidden">{{ Str::limit($booking->sub_category, 12) }}</span>
                                                    <span class="text-slate-400 mx-0.5 sm:mx-1">·</span>
                                                    <span class="font-bold text-kingdom-navy dark:text-white">{{ $booking->assigned_count ?? 0 }}/{{ $booking->quantity ?? 0 }} staff</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <p class="text-[11px] sm:text-[12px] font-bold text-kingdom-gold leading-tight">{{ $booking->start_date ? $booking->start_date->format('M d, Y') : '-' }}</p>
                                                    <p class="text-[10px] sm:text-[11px] mt-0.5 text-slate-500 font-medium">{{ $booking->shift_hours ?? 0 }}h shift</p>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md text-[9px] sm:text-[10px] font-bold uppercase {{ $st['class'] }}">{{ $st['text'] }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400 font-medium bg-slate-50/50 dark:bg-slate-800/30">
                                                    No records found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 min-h-[200px]">
                                <i data-lucide="calendar-x" class="text-4xl mb-3 opacity-50 w-8 h-8"></i>
                                <p class="font-medium text-sm">No upcoming events scheduled</p>
                                <p class="text-xs mt-1 opacity-70">Your upcoming confirmed staffing shifts will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
            
            <!-- RIGHT COLUMN: Pending Quotations -->
            <div class="flex flex-col xl:h-full min-h-0">
                <div class="flex-1 flex flex-col relative bg-gradient-to-br from-[#0F1D33] to-[#1a2b4c] text-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl shadow-[0_8px_30px_rgba(15,29,51,0.2)] hover:shadow-[0_20px_40px_rgba(15,29,51,0.4)] overflow-hidden ring-1 ring-white/10 hover:-translate-y-1 transition-all duration-500 group">
                    <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-[#B89955]/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700 ease-out"></div>
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-colors duration-500"></div>
                    <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity duration-500 group-hover:scale-110 group-hover:-rotate-12 transform origin-bottom-right">
                        <i data-lucide="receipt" class="text-[140px] w-5 h-5"></i>
                    </div>
                    
                    <div class="relative z-10 flex items-center gap-2.5 sm:gap-3 mb-4 sm:mb-6">
                        <div class="p-2 sm:p-2.5 bg-white/10 rounded-lg sm:rounded-xl backdrop-blur-md border border-white/10 text-[#B89955] group-hover:scale-110 transition-transform duration-300 shrink-0">
                            <i data-lucide="file-text" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </div>
                        <h4 class="text-[#B89955] text-[10px] sm:text-xs font-bold uppercase tracking-widest">Pending Quotations</h4>
                    </div>

                    <div class="space-y-3 sm:space-y-4 relative z-10 flex-1 flex flex-col">
                        @if(isset($partnerQuotations) && count($partnerQuotations) > 0)
                            @foreach($partnerQuotations as $quote)
                                <div class="flex justify-between items-center border-b border-white/10 pb-2.5 sm:pb-3 group/item gap-3">
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs sm:text-sm text-slate-300 group-hover/item:text-white transition-colors block truncate">{{ $quote->event_name ?: $quote->sub_category }}</span>
                                        <span class="text-[9px] sm:text-[10px] text-slate-500">{{ $quote->quotation_ref }} · {{ $quote->created_at ? $quote->created_at->format('M d') : '' }}</span>
                                    </div>
                                    <span class="font-bold text-white bg-white/5 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg border border-white/10 text-xs sm:text-sm whitespace-nowrap shrink-0">{{ $quote->amount ?: '£' . number_format(($quote->quantity ?? 0) * ($quote->rate ?? 0) * ($quote->shift_hours ?? 0), 2) }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center text-slate-400">
                                <i data-lucide="file-text" class="text-4xl mb-3 opacity-40 group-hover:scale-110 transition-transform duration-500 w-8 h-8"></i>
                                <p class="text-sm font-semibold text-slate-300">All caught up!</p>
                                <p class="text-[11px] opacity-60 mt-1 text-center font-medium">No pending quotes to review at this time.</p>
                            </div>
                        @endif
                        <a href="{{ route('partner.quotations') }}" wire:navigate class="block w-full mt-auto py-2.5 sm:py-3 bg-[#B89955] hover:bg-white text-white hover:text-[#0F1D33] rounded-xl text-[10px] sm:text-xs font-bold tracking-wider transition-all duration-300 shadow-lg shadow-[#B89955]/20 hover:shadow-white/20 text-center active:scale-95">REVIEW ALL QUOTES</a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
