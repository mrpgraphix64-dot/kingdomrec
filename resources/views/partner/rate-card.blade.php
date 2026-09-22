@extends('layouts.partner')

@section('title', 'Rate Card | Kingdom Partner')

@section('content')
@php
    $categoryColors = [
        'Security' => 'bg-[#FEF2F2] text-[#DC2626] border-[#FECACA]',
        'SIA Security' => 'bg-[#FEF2F2] text-[#DC2626] border-[#FECACA]',
        'Events' => 'bg-[#EFF6FF] text-[#2563EB] border-[#BFDBFE]',
        'Hospitality' => 'bg-[#FFFBEB] text-[#D97706] border-[#FDE68A]',
        'Construction' => 'bg-[#F8FAFC] text-[#64748B] border-[#E2E8F0]',
        'Facilities' => 'bg-[#ECFDF5] text-[#059669] border-[#A7F3D0]',
        'Cleaning' => 'bg-[#ECFDF5] text-[#059669] border-[#A7F3D0]',
    ];
@endphp

<div class="flex flex-col h-full max-h-full gap-6">
    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Rate Card</h1>
            <p class="text-sm text-slate-500 mt-2">Standard billing rates for the 2026 fiscal year. <span class="font-semibold text-slate-400">({{ $rates->count() }} rates)</span></p>
        </div>
        <div class="relative" x-data="{ exportOpen: false }" @click.away="exportOpen = false">
            <button @click="exportOpen = !exportOpen" class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm text-slate-600 font-bold text-sm border border-slate-200 hover:bg-slate-50 transition-colors">
                <i data-lucide="download" class="text-[18px] w-5 h-5"></i>
                Download
                <i data-lucide="chevron-down" class="text-[16px] transition-transform duration-200 w-5 h-5" :class="exportOpen ? 'rotate-180' : ''"></i>
            </button>
            
            <div x-show="exportOpen" 
                 x-transition:enter="transition ease-out duration-100" 
                 x-transition:enter-start="transform opacity-0 scale-95" 
                 x-transition:enter-end="transform opacity-100 scale-100" 
                 x-transition:leave="transition ease-in duration-75" 
                 x-transition:leave-start="transform opacity-100 scale-100" 
                 x-transition:leave-end="transform opacity-0 scale-95" 
                 class="absolute right-0 mt-2 w-48 rounded-xl bg-white border border-slate-200 shadow-[0_10px_40px_rgba(0,0,0,0.08)] z-50 overflow-hidden" 
                 style="display: none;">
                <div class="p-1.5 flex flex-col gap-1">
                    <a href="{{ route('partner.rate-card.export', ['format' => 'pdf']) }}" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-slate-700 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors group">
                        <i data-lucide="file-text" class="text-[18px] text-red-500 group-hover:scale-110 transition-transform w-5 h-5"></i>
                        Download PDF
                    </a>
                    <a href="{{ route('partner.rate-card.export', ['format' => 'excel']) }}" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors group">
                        <i data-lucide="table" class="text-[18px] text-emerald-500 group-hover:scale-110 transition-transform w-5 h-5"></i>
                        Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rate Table -->
    <div class="flex-1 min-h-0 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($rates->count() > 0)
        <div class="overflow-x-auto flex-1 relative custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#0f1f3d] text-white sticky top-0 z-20">
                    <tr class="text-xs font-semibold tracking-wide text-white uppercase bg-[#0f1f3d]">
                        <th class="w-16 px-4 py-4 text-center">No.</th>
                        <th class="w-[25%] px-4 py-4 text-left">Staff Type</th>
                        <th class="w-[20%] px-4 py-4 text-center">Category</th>
                        <th class="w-[15%] px-4 py-4 text-right">Gross Rate</th>
                        <th class="w-[15%] px-4 py-4 text-right">Net Rate</th>
                        <th class="w-[20%] px-4 py-4 text-center">Company / Venue</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($rates as $rate)
                    @php
                        $venueName = $venues->firstWhere('id', $rate->venue_id)->name ?? 'N/A';
                        $colorClass = $categoryColors[$rate->type] ?? 'bg-[#F8FAFC] text-[#64748B] border-[#E2E8F0]';
                    @endphp
                    <tr class="transition-colors duration-150 group border-b border-slate-100 hover:bg-slate-50 bg-white">
                        <td class="px-4 py-4 align-middle text-center w-16">
                            <span class="text-[12px] font-medium text-[#6B7280]">{{ $loop->iteration }}</span>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <p class="text-[13px] font-bold text-slate-900 dark:text-white">{{ $rate->type ?: $rate->sub_category }}</p>
                            @if($rate->sub_category && $rate->type )
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $rate->sub_category }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center align-middle">
                            <span class="inline-flex w-fit items-center justify-center px-2 py-0.5 rounded-full text-[10px] uppercase font-bold border {{ $colorClass }}">{{ $rate->type }}</span>
                        </td>
                        <td class="px-4 py-4 text-right align-middle">
                            <div class="flex flex-col items-end justify-center">
                                <div class="flex items-center text-slate-900 dark:text-slate-100">
                                    <span class="text-[12px] text-slate-500 mr-1">£</span>
                                    <span class="text-[13px] font-bold tabular-nums">{{ number_format($rate->overtime_rate, 2) }}</span>
                                </div>
                                <span class="text-[11px] text-slate-400">/hour</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-right align-middle">
                            <div class="flex flex-col items-end justify-center">
                                <div class="flex items-center text-slate-900 dark:text-slate-100">
                                    <span class="text-[13px] text-slate-500 mr-1">£</span>
                                    <span class="text-[15px] font-bold tabular-nums">{{ number_format($rate->hourly_rate, 2) }}</span>
                                </div>
                                <span class="text-[11px] text-slate-400">/hour net</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center align-middle">
                            <div class="flex flex-col items-center gap-1">
                                @if($rate->company_name)
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-blue-500 shrink-0"></i>
                                        <span class="text-[12px] font-bold text-slate-900 dark:text-slate-100">{{ $rate->company_name }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                    <span class="text-[11px] font-medium text-slate-500">{{ $venueName }}</span>
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
        <div class="px-8 py-16 text-center flex-1">
            <div class="flex flex-col items-center gap-3">
                <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <i data-lucide="file-x" class="w-8 h-8 text-slate-300"></i>
                </div>
                <p class="text-slate-400 font-semibold">No rate cards available yet.</p>
                <p class="text-slate-300 text-sm">Rate cards will appear here once the admin adds them.</p>
            </div>
        </div>
        @endif
        <div class="flex-none pt-[16px] mt-[16px] border-t border-[#EEF2F7]">
            <p class="text-[12px] text-[#94A3B8]">* All rates are exclusive of VAT. Overtime applies after 8 hours continuous work. Holiday rates may vary.</p>
        </div>
    </div>
</div>
@endsection
