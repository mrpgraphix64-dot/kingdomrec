@extends('layouts.admin')

@section('title', 'Shifts Schedule | Kingdom Admin')

@section('content')
<div class="h-full flex flex-col space-y-6">
    <!-- Header -->
    <div class="dashboard-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Shifts Schedule</h1>
            <p class="text-slate-500 font-medium text-sm">Assign team members to active shifts & manage rosters</p>
        </div>
    </div>

    <!-- Shifts Table -->
    <div class="anim-fade-in-up bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col flex-1 min-h-0 relative">
        <div class="overflow-x-auto flex-1 relative custom-scrollbar">
            <table class="w-full min-w-[900px] text-center border-collapse">
                <colgroup>
                    <col style="width: 80px;">
                    <col style="width: 20%;">
                    <col style="width: 20%;">
                    <col style="width: 15%;">
                    <col style="width: 25%;">
                    <col style="width: 20%;">
                </colgroup>
                <thead class="bg-[#0f1f3d] sticky top-0 z-50">
                    <tr class="text-xs font-semibold tracking-wide text-white uppercase bg-[#0f1f3d]">
                        <th class="py-4 px-4 text-center">ID</th>
                        <th class="px-4 py-4 text-center">Shift Name</th>
                        <th class="px-4 py-4 text-center">Timing</th>
                        <th class="px-4 py-4 text-center">Status</th>
                        <th class="px-4 py-4 text-center">Assigned Staff</th>
                        <th class="px-4 py-4 text-center">Assign Staff</th>
                    </tr>
                </thead>
                <tbody class="bg-transparent">
                    @foreach($shifts as $index => $shift)
                    <tr class="transition-colors duration-150 group cursor-pointer border-b border-slate-100 hover:bg-slate-50 bg-white">
                        <!-- # -->
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono text-[10px] font-bold border border-indigo-100 group-hover:bg-[#0f1f3d] group-hover:text-white group-hover:border-[#0f1f3d] transition-colors shadow-sm">{{ $index + 1 }}</span>
                        </td>
                        <!-- Shift Name -->
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <div class="h-8 w-8 shrink-0 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-[11px] shadow-sm border border-indigo-100 group-hover:scale-110 transition-transform">
                                    S{{ $shift->id }}
                                </div>
                                <p class="font-bold text-slate-800 text-xs group-hover:text-kingdom-navy transition-colors">{{ $shift->title ?? 'Shift #' . $shift->id }}</p>
                            </div>
                        </td>
                        <!-- Timing -->
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2 text-xs font-medium text-slate-500 group-hover:text-slate-700 transition-colors">
                                <div class="p-1.5 rounded-full bg-indigo-50 text-indigo-600">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                </div>
                                <span class="font-bold text-slate-700">{{ $shift->time ?? 'All Day' }}</span>
                            </div>
                        </td>
                        <!-- Status -->
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold border capitalize shadow-sm bg-emerald-50 text-emerald-600 border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Active
                            </span>
                        </td>
                        <!-- Assigned Staff -->
                        <td class="px-4 py-4 text-center">
                            @if(empty($shift->staff_names))
                                <span class="text-xs text-slate-400 italic">No staff assigned</span>
                            @else
                                <div class="flex flex-wrap justify-center gap-1.5">
                                    @foreach($shift->staff_names as $name)
                                        <div class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-lg pl-2.5 pr-1.5 py-1 transition-all hover:bg-red-50 hover:border-red-100 hover:shadow-sm group/staff">
                                            <span class="text-xs font-bold text-slate-700 group-hover/staff:text-red-700">{{ $name }}</span>
                                            <form action="{{ route('admin.shift.remove', $shift->id) }}" method="POST" class="flex items-center">
                                                @csrf
                                                <input type="hidden" name="staff_name" value="{{ $name }}">
                                                <button type="submit" class="p-0.5 rounded text-slate-400 hover:text-red-600 hover:bg-red-100 transition-colors" title="Remove">
                                                    <i data-lucide="x" class="w-3 h-3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <!-- Assign Staff Action -->
                        <td class="px-4 py-4 text-center">
                            <form action="{{ route('admin.shift.assign') }}" method="POST" class="flex gap-2 items-center justify-center">
                                @csrf
                                <input type="hidden" name="shift_id" value="{{ $shift->id }}">
                                <select name="staff_member_id"
                                    class="flex-1 pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[11px] font-bold text-kingdom-navy transition-all outline-none hover:border-slate-300 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%206%206%206-6%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[right_0.5rem_center]">
                                    <option value="" disabled selected>Select staff...</option>
                                    @php $availableStaff = collect($teamMembers)->filter(fn($t) => !in_array($t->name, $shift->staff_names ?? [])); @endphp
                                    @if($availableStaff->isEmpty())
                                        <option value="" disabled>No staff available</option>
                                    @else
                                        @foreach($availableStaff as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <button type="submit" class="bg-[#0F1D33] hover:bg-slate-800 text-white p-2 rounded-xl font-bold text-sm transition-all shadow-lg flex items-center justify-center hover:scale-105 active:scale-95">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
 
        <!-- Empty State -->
        @if(count($shifts) === 0)
        <div class="h-full flex flex-col items-center justify-center p-12 text-center text-slate-400">
            <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 ring-4 ring-white shadow-sm">
                <i data-lucide="calendar-clock" class="w-8 h-8 text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">No shifts found</h3>
            <p class="text-slate-500 max-w-sm mx-auto mt-2 text-sm">There are no active shifts to display.</p>
        </div>
        @endif
    </div>
</div>
 

@endsection
