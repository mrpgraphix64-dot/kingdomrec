@extends('layouts.candidate')

@section('title', 'My Schedule - ' . config('app.name', 'Kingdom Recruitments'))

@section('content')
    <div class="font-manrope max-w-4xl mx-auto flex flex-col gap-1">

        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">My Schedule</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">View your upcoming assigned events and shifts.</p>
        </div>

        <div class="mt-4">
            @if($assignments->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-navy-dark rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm text-center">
                    <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-full mb-4">
                        <i data-lucide="calendar-x" class="text-slate-300 dark:text-gray-500 w-12 h-12"></i>
                    </div>
                    <h3 class="text-xl font-bold text-kingdom-navy dark:text-white mb-2">No upcoming shifts</h3>
                    <p class="text-slate-500 dark:text-gray-400 max-w-xs mb-6">You haven't been assigned to any events yet.</p>
                    <a href="{{ route('jobs.index') }}" class="px-6 py-2.5 bg-kingdom-red hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg">
                        Browse Jobs to Apply
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($assignments as $assignment)
                        @php
                            $statusConfig = [
                                'Assigned' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'border' => 'border-blue-200 dark:border-blue-700', 'icon' => 'calendar'],
                                'Submitted' => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/30', 'text' => 'text-indigo-700 dark:text-indigo-400', 'border' => 'border-indigo-200 dark:border-indigo-700', 'icon' => 'check-circle'],
                                'Reviewed' => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/30', 'text' => 'text-indigo-700 dark:text-indigo-400', 'border' => 'border-indigo-200 dark:border-indigo-700', 'icon' => 'check-circle'],
                                'Approved' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400', 'border' => 'border-green-200 dark:border-green-700', 'icon' => 'check-circle'],
                                'Billed' => ['bg' => 'bg-slate-100 dark:bg-slate-900/30', 'text' => 'text-slate-700 dark:text-slate-400', 'border' => 'border-slate-200 dark:border-slate-700', 'icon' => 'archive'],
                            ];
                            $sc = $statusConfig[$assignment->status] ?? $statusConfig['Assigned'];
                            $eventTitle = $assignment->staffQuotation->eventBooking->event_name ?? $assignment->role_name ?? 'Staffing Shift';
                            $eventLocation = $assignment->staffQuotation->eventBooking->venue ?? 'Venue TBD';
                        @endphp

                        <div class="card-interactive bg-white dark:bg-navy-dark rounded-2xl p-5 border border-slate-100 dark:border-gray-700 shadow-sm group relative overflow-hidden">
                            <!-- Left Border Accent -->
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-kingdom-red opacity-0 group-hover:opacity-100 transition-opacity"></div>

                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-kingdom-red dark:text-red-400 border border-slate-100 dark:border-slate-700 shrink-0">
                                            <i data-lucide="calendar" class="w-5 h-5"></i>
                                            <span class="text-[10px] absolute font-bold mt-2">{{ $assignment->shift_date ? \Carbon\Carbon::parse($assignment->shift_date)->format('d') : 'TBD' }}</span>
                                        </div>
                                        <div class="min-w-0 pt-0.5">
                                            <h3 class="font-bold text-kingdom-navy dark:text-white text-lg truncate group-hover:text-kingdom-red transition-colors">
                                                {{ $eventTitle }}
                                            </h3>
                                            <div class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-gray-400 mt-0.5">
                                                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                                <span class="truncate">{{ $eventLocation }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 ml-14">
                                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                            <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                            {{-- Scheduled shift window comes from the booking (staffQuotation), not
                                                 $assignment->start_time/end_time — those record the candidate's
                                                 actual clock-in/out time and are null until they clock in. --}}
                                            <span class="font-medium">{{ $assignment->staffQuotation?->start_time ? substr($assignment->staffQuotation->start_time, 0, 5) : '09:00' }} - {{ $assignment->staffQuotation?->end_time ? substr($assignment->staffQuotation->end_time, 0, 5) : '17:00' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                            <i data-lucide="calendar-days" class="w-4 h-4 text-slate-400"></i>
                                            <span class="font-medium">
                                                {{ $assignment->shift_date ? \Carbon\Carbon::parse($assignment->shift_date)->format('M d, Y') : 'TBD' }}
                                            </span>
                                        </div>
                                        @if($assignment->role_name)
                                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                            <i data-lucide="briefcase" class="w-4 h-4 text-slate-400"></i>
                                            <span class="font-medium">{{ $assignment->role_name }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    @if($assignment->staffQuotation->special_requirements)
                                    <div class="mt-4 ml-14 p-3 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-xl">
                                        <p class="text-xs font-semibold text-kingdom-red dark:text-red-400 mb-1 flex items-center gap-1.5"><i data-lucide="info" class="w-3.5 h-3.5"></i> Special Instructions</p>
                                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ $assignment->staffQuotation->special_requirements }}</p>
                                    </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }}">
                                        <i data-lucide="{{ $sc['icon'] }}" class="w-3.5 h-3.5"></i>
                                        {{ $assignment->start_time && !$assignment->end_time ? 'Checked In' : $assignment->status }}
                                    </span>

                                    @if($assignment->status === 'Assigned' && !$assignment->start_time)
                                        <form method="POST" action="{{ route('applicant.timesheets.clockIn') }}">
                                            @csrf
                                            <input type="hidden" name="event_assignment_id" value="{{ $assignment->id }}">
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-kingdom-red hover:bg-red-700 text-white rounded-xl text-sm font-bold shadow-md transition-colors w-full justify-center">
                                                <i data-lucide="clock" class="w-4 h-4"></i> Clock In
                                            </button>
                                        </form>
                                    @elseif($assignment->status === 'Assigned' && $assignment->start_time)
                                        <span class="text-xs text-green-600 font-bold mb-1"><i data-lucide="check-circle" class="w-3 h-3 inline"></i> Clocked in at {{ substr($assignment->start_time, 0, 5) }}</span>
                                        <form method="POST" action="{{ route('applicant.timesheets.clockOut', $assignment->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold shadow-md transition-colors w-full justify-center">
                                                <i data-lucide="log-out" class="w-4 h-4"></i> Clock Out
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
