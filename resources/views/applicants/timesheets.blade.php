@extends('layouts.candidate')

@section('title', 'Timesheets - ' . config('app.name', 'Kingdom Recruitments'))

@section('content')
    @php
        function getHoursDiff($start, $end, $break = 0) {
            if (!$start || !$end) return 0;
            $t1 = strtotime($start);
            $t2 = strtotime($end);
            if ($t2 < $t1) $t2 += 86400; // overnight
            $diff = ($t2 - $t1) / 3600;
            return max(0, round($diff, 2));
        }
    @endphp

    <div class="font-manrope max-w-5xl mx-auto flex flex-col gap-1">

        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">My Timesheets</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Record hours and view approval status.</p>
        </div>

        <div class="mt-4">
            @if($timesheets->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-navy-dark rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm text-center">
                    <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-full mb-4">
                        <i data-lucide="clock-3" class="text-slate-300 dark:text-gray-500 w-12 h-12"></i>
                    </div>
                    <h3 class="text-xl font-bold text-kingdom-navy dark:text-white mb-2">No timesheets yet</h3>
                    <p class="text-slate-500 dark:text-gray-400 max-w-xs mb-6">Clock into a shift from your schedule to start recording hours.</p>
                    <a href="{{ route('applicant.schedule') }}" class="px-6 py-2.5 bg-kingdom-red hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg inline-flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4"></i> View Schedule
                    </a>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead class="bg-[#0f1f3d] text-white">
                                <tr class="text-xs font-semibold tracking-wide text-white uppercase bg-[#0f1f3d]">
                                    <th class="p-4 text-left text-white">Event / Shift</th>
                                    <th class="p-4 text-center text-white">Shift Date</th>
                                    <th class="p-4 text-center text-white">Clock In</th>
                                    <th class="p-4 text-center text-white">Clock Out</th>
                                    <th class="p-4 text-right text-white">Total Hr</th>
                                    <th class="p-4 text-center text-white">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent">
                                @forelse($timesheets as $timesheet)
                                    @php
                                        $eventTitle = $timesheet->staffQuotation->eventBooking->event_name ?? $timesheet->role_name ?? 'Staffing Shift';
                                        $eventLocation = $timesheet->staffQuotation->eventBooking->venue ?? 'N/A';
                                        $statusClass = [
                                            'Draft' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                            'Submitted' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            'Approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                            'Billed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                        ][$timesheet->status] ?? 'bg-slate-100 text-slate-700';
                                    @endphp
                                    <tr class="table-row-interactive transition-colors duration-150 group border-b border-slate-100 hover:bg-slate-50 bg-white">
                                        <td class="p-4">
                                            <div class="font-bold text-kingdom-navy dark:text-white text-sm mb-1">{{ $eventTitle }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $eventLocation }}
                                            </div>
                                        </td>
                                        <td class="p-4 text-center text-sm font-medium text-slate-700 dark:text-slate-300">
                                            {{ $timesheet->shift_date ? \Carbon\Carbon::parse($timesheet->shift_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="p-4 text-center">
                                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                {{ $timesheet->start_time ? substr($timesheet->start_time, 0, 5) : '--:--' }}
                                            </div>
                                        </td>
                                        <td class="p-4 text-center">
                                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                {{ $timesheet->end_time ? substr($timesheet->end_time, 0, 5) : '--:--' }}
                                            </div>
                                        </td>
                                        <td class="p-4 text-right text-sm font-bold text-kingdom-navy dark:text-white">
                                            {{ getHoursDiff($timesheet->start_time, $timesheet->end_time, $timesheet->break_mins) }} h
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide {{ $statusClass }}">
                                                {{ $timesheet->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-sm text-slate-400 font-medium bg-slate-50/50 dark:bg-slate-800/30">
                                            No records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
