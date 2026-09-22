@extends('layouts.admin')

@section('title', 'Billing & Invoicing | Kingdom Admin')

@section('content')
<div class="space-y-5" x-data="{}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Billing & Invoicing</h1>
            <p class="text-[13px] text-slate-500 mt-1">Review events ready for billing, generate invoices, track payments, and download PDF receipts.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.staff-quotation') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-slate-500"></i>
                <span>Staff Bookings</span>
            </a>
            <a href="{{ route('admin.time-shifting') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                <i data-lucide="clock" class="w-4 h-4 text-slate-500"></i>
                <span>Timesheets</span>
            </a>
        </div>
    </div>

    <!-- Financial Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Total Invoiced -->
        <a href="{{ route('admin.finance', ['status' => 'Invoice Generated']) }}" 
           class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-blue-600 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all block group {{ request('status') === 'Invoice Generated' ? 'ring-2 ring-blue-500 bg-blue-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Invoiced</span>
                <i data-lucide="receipt" class="w-4 h-4 text-blue-600 opacity-70 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-2">
                £{{ number_format($stats['total_invoiced'] ?? 0, 2) }}
            </div>
            <span class="text-[11px] text-slate-500 font-semibold mt-0.5 block">
                {{ $stats['total_invoices_count'] ?? 0 }} generated {{ Str::plural('invoice', $stats['total_invoices_count'] ?? 0) }}
            </span>
        </a>

        <!-- Total Paid -->
        <a href="{{ route('admin.finance', ['status' => 'Paid']) }}" 
           class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-emerald-500 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all block group {{ request('status') === 'Paid' ? 'ring-2 ring-emerald-500 bg-emerald-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Paid</span>
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 opacity-70 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-emerald-700 mt-2">
                £{{ number_format($stats['total_paid'] ?? 0, 2) }}
            </div>
            <span class="text-[11px] text-slate-500 font-semibold mt-0.5 block">
                {{ $stats['total_paid_count'] ?? 0 }} settled {{ Str::plural('invoice', $stats['total_paid_count'] ?? 0) }}
            </span>
        </a>

        <!-- Outstanding / Unpaid -->
        <a href="{{ route('admin.finance', ['status' => 'Unpaid']) }}" 
           class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-amber-500 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all block group {{ request('status') === 'Unpaid' ? 'ring-2 ring-amber-500 bg-amber-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Outstanding</span>
                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 opacity-70 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-amber-700 mt-2">
                £{{ number_format($stats['outstanding'] ?? 0, 2) }}
            </div>
            <span class="text-[11px] text-slate-500 font-semibold mt-0.5 block">
                {{ $stats['total_unpaid_count'] ?? 0 }} pending payment
            </span>
        </a>

        <!-- Ready for Billing -->
        <a href="{{ route('admin.finance', ['status' => 'Awaiting Invoice']) }}" 
           class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-indigo-600 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all block group {{ request('status') === 'Awaiting Invoice' ? 'ring-2 ring-indigo-500 bg-indigo-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Ready for Invoicing</span>
                <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-600 opacity-70 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-indigo-700 mt-2">
                {{ $stats['total_ready_count'] ?? 0 }}
            </div>
            <span class="text-[11px] text-slate-500 font-semibold mt-0.5 block">
                Approved shifts to bill
            </span>
        </a>
    </div>

    <!-- Active Filter Status Notification -->
    @if(request()->anyFilled(['search', 'status', 'client_id', 'date_from', 'date_to']))
        <div class="bg-amber-50/80 border border-amber-200 rounded-xl px-4 py-2.5 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-amber-900 font-semibold">
                <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0"></i>
                <span>
                    Active filter: 
                    @if(request()->filled('status'))
                        <strong>Status: {{ request('status') }}</strong>
                    @endif
                    @if(request()->filled('search'))
                        @if(request()->filled('status')) &bull; @endif
                        <strong>Search: "{{ request('search') }}"</strong>
                    @endif
                    @if(request()->filled('client_id'))
                        &bull; <strong>Specific Client</strong>
                    @endif
                    @if(request()->filled('date_from') || request()->filled('date_to'))
                        &bull; <strong>Date Range Filtered</strong>
                    @endif
                </span>
            </div>
            <a href="{{ route('admin.finance') }}" class="text-xs font-bold text-amber-900 hover:text-amber-950 underline flex items-center gap-1 shrink-0">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                <span>Show All Billing Items</span>
            </a>
        </div>
    @endif

    <!-- Invoices & Billing Section -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Invoices & Billing Catalogue</h2>
                <p class="text-[11px] text-slate-500 font-medium">Manage unbilled shifts, generate invoices, and download generated PDF statements.</p>
            </div>
            
            <!-- Reset filter — status-specific filtering is already handled by the metric cards above,
                 which link to the same statuses and highlight the active one; this pill only needs
                 to cover the one thing they don't: clearing back to everything. -->
            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <a href="{{ route('admin.finance') }}"
                   class="px-2.5 py-1 rounded-lg font-bold transition-all {{ !request('status') ? 'bg-[#0F1D33] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    All Items
                </a>
            </div>
        </div>

        <!-- Filter Bar placed above table -->
        <form method="GET" action="{{ route('admin.finance') }}" class="p-3.5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-white shrink-0">
            <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[280px]">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[180px] max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Ref, Client, Event..." class="block w-full pl-9 pr-3 py-1.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-700">
                </div>

                <!-- Status Filter -->
                <select name="status" class="px-3 py-1.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-700">
                    <option value="">All Statuses</option>
                    <option value="Awaiting Invoice" {{ request('status') === 'Awaiting Invoice' ? 'selected' : '' }}>Events Ready for Billing</option>
                    <option value="Invoice Generated" {{ request('status') === 'Invoice Generated' ? 'selected' : '' }}>Invoices Generated</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>

                <!-- Client Filter -->
                <select name="client_id" class="px-3 py-1.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-700 max-w-xs">
                    <option value="">All Clients / Partners</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ request('client_id') == $partner->id ? 'selected' : '' }}>
                            {{ $partner->company_name ?: $partner->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Date Range -->
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                    <span>From:</span>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-2.5 py-1.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs text-slate-700">
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                    <span>To:</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-2.5 py-1.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs text-slate-700">
                </div>

                <!-- Action Buttons -->
                <button type="submit" class="px-3.5 py-1.5 bg-[#0F1D33] hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs shrink-0 flex items-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Apply</span>
                </button>

                @if(request()->anyFilled(['search', 'status', 'client_id', 'date_from', 'date_to']))
                    <a href="{{ route('admin.finance') }}" class="px-2.5 py-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 transition-colors flex items-center gap-1">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- Table only renders when there's something to show it for — the empty state moved
             below as a plain full-width sibling block, so on mobile it isn't stranded inside
             this table's horizontal scroll area (min-w-[850px] pushed it off-screen at 375px). --}}
        @if(!($readyBookings->isEmpty() && $invoices->isEmpty()))
        <div class="overflow-x-auto w-full admin-table-container">
            <table class="w-full min-w-[850px] text-left border-collapse whitespace-nowrap admin-table">
                <thead>
                    <tr class="bg-[#1a2332] text-white uppercase tracking-wider font-bold text-[11px] border-b border-slate-700">
                        <th class="px-4 py-3 text-left bg-[#1a2332]">Reference / Event</th>
                        <th class="px-4 py-3 text-left bg-[#1a2332]">Client / Partner</th>
                        <th class="px-4 py-3 text-left bg-[#1a2332]">Dates / Issue Date</th>
                        <th class="px-4 py-3 text-right bg-[#1a2332]">Amount</th>
                        <th class="px-4 py-3 text-center bg-[#1a2332]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <!-- 1. Ready for Invoicing bookings (shown on the first page of pagination) -->
                    @if($invoices->currentPage() == 1)
                        @foreach($readyBookings as $booking)
                            @php
                                $approvedHrs = $booking->shiftSlots->where('status', 'Approved')->sum(function($s) { return $s->getHours(); });
                                $netVal = $booking->shiftSlots->where('status', 'Approved')->sum(function($s) { return $s->getSubtotal(); });
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors text-xs text-slate-700">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-800 text-[10px] font-extrabold uppercase tracking-wider rounded border border-amber-200">Awaiting Invoice</span>
                                        <div class="font-bold text-slate-900 text-sm">{{ $booking->event_name }}</div>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase mt-0.5 ml-2">{{ $booking->booking_ref }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-slate-800">{{ $booking->client ?: ($booking->user->company_name ?? 'N/A') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs font-semibold text-slate-700">
                                        {{ $booking->start_date ? $booking->start_date->format('d M') : '-' }} - {{ $booking->end_date ? $booking->end_date->format('d M, Y') : '-' }}
                                    </div>
                                    <div class="text-[10px] text-indigo-700 font-bold mt-0.5">
                                        {{ number_format($approvedHrs, 2) }} approved hrs
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="font-extrabold text-slate-900 text-sm">£{{ number_format($netVal, 2) }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Est. Net</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center">
                                        <form method="POST" action="{{ route('admin.invoices.generate', $booking->id) }}" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').classList.add('opacity-50', 'cursor-not-allowed');">
                                            @csrf
                                            <button type="submit" class="bg-[#0F1D33] hover:bg-slate-800 text-white text-xs font-extrabold px-3 py-1.5 rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5">
                                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                                <span>Generate Invoice</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- 2. Generated Invoices -->
                    @foreach($invoices as $invoice)
                        <tr class="hover:bg-slate-50/80 transition-colors text-xs text-slate-700">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 text-[10px] font-extrabold uppercase tracking-wider rounded border border-emerald-200">Invoice Generated</span>
                                    <div class="font-bold text-slate-900 text-sm">{{ $invoice->invoice_ref }}</div>
                                </div>
                                <div class="text-[11px] text-slate-500 font-semibold mt-0.5 ml-2">{{ $invoice->eventBooking->event_name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-800">{{ $invoice->company_name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs font-semibold text-slate-700">
                                    Issued: {{ $invoice->issue_date ? $invoice->issue_date->format('d M, Y') : '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                    Due: {{ $invoice->due_date ? $invoice->due_date->format('d M, Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="font-extrabold text-slate-900 text-sm">£{{ number_format($invoice->total_amount, 2) }}</div>
                                <div class="text-[10px] {{ $invoice->status === 'Paid' ? 'text-emerald-700' : 'text-amber-700' }} font-bold uppercase tracking-wider">
                                    {{ $invoice->status }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($invoice->status !== 'Paid')
                                        <form id="markPaidForm{{ $invoice->id }}" method="POST" action="{{ route('admin.invoices.mark-paid', $invoice->id) }}"
                                              @submit.prevent="$dispatch('open-confirm-modal', { title: 'Mark Invoice as Paid', message: 'Confirm {{ $invoice->invoice_ref }} (£{{ number_format($invoice->total_amount, 2) }}) for {{ addslashes($invoice->company_name) }} has been paid? This closes the booking once all its shifts are billed.', onConfirm: () => document.getElementById('markPaidForm{{ $invoice->id }}').submit() })">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-all" title="Mark this invoice as paid">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>Mark Paid</span>
                                            </button>
                                        </form>
                                    @endif
                                    <!-- View/Download PDF -->
                                    <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 transition-colors shadow-xs shrink-0" title="Download Invoice PDF">
                                        <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i>
                                        <span>Download PDF</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
            {{ $invoices->links() }}
        </div>
        @endif
        @else
        {{-- Contextual empty state — a real full-width block, not a colspan cell inside the
             horizontally-scrolling table, so it's visible on mobile without scrolling sideways. --}}
        <div class="px-4 py-10 text-center bg-white">
            <div class="flex flex-col items-center justify-center max-w-md mx-auto py-2">
                <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3 border border-slate-200 shadow-sm">
                    <i data-lucide="receipt" class="w-6 h-6 text-slate-400"></i>
                </div>
                @if(request('status') === 'Awaiting Invoice' && ($stats['total_invoices_count'] ?? 0) > 0)
                    <h3 class="text-sm font-extrabold text-slate-900">No Events Awaiting Invoicing</h3>
                    <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                        All current eligible bookings have already been invoiced. You have <strong class="text-emerald-700 font-bold">{{ $stats['total_invoices_count'] }} generated {{ Str::plural('invoice', $stats['total_invoices_count']) }}</strong> totaling <strong>£{{ number_format($stats['total_invoiced'] ?? 0, 2) }}</strong>.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-2.5 mt-4">
                        <a href="{{ route('admin.finance', ['status' => 'Invoice Generated']) }}" class="px-4 py-2 rounded-xl bg-[#0F1D33] text-white hover:bg-slate-800 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                            <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                            <span>View Generated Invoices ({{ $stats['total_invoices_count'] }})</span>
                        </a>
                        <a href="{{ route('admin.finance') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition-all">
                            <span>Show All Items</span>
                        </a>
                    </div>
                @elseif(request('status') === 'Invoice Generated' && ($stats['total_ready_count'] ?? 0) > 0)
                    <h3 class="text-sm font-extrabold text-slate-900">No Generated Invoices Found</h3>
                    <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                        You currently have <strong class="text-indigo-700 font-bold">{{ $stats['total_ready_count'] }} booking(s) ready for billing</strong>.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('admin.finance', ['status' => 'Awaiting Invoice']) }}" class="px-4 py-2 rounded-xl bg-[#0F1D33] text-white hover:bg-slate-800 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>View Events Ready for Billing ({{ $stats['total_ready_count'] }})</span>
                        </a>
                    </div>
                @elseif(request()->anyFilled(['search', 'status', 'client_id', 'date_from', 'date_to']))
                    <h3 class="text-sm font-extrabold text-slate-900">No Records Match Selected Filters</h3>
                    <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                        No invoices or unbilled bookings match your search or date criteria. Total invoiced across system is <strong>£{{ number_format($stats['total_invoiced'] ?? 0, 2) }}</strong>.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('admin.finance') }}" class="px-4 py-2 rounded-xl bg-[#0F1D33] text-white hover:bg-slate-800 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>Clear All Filters</span>
                        </a>
                    </div>
                @else
                    <h3 class="text-sm font-extrabold text-slate-900">No Billing Records Yet</h3>
                    <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                        Completed bookings with approved shifts will automatically appear here ready for invoice generation.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('admin.staff-quotation') }}" class="px-4 py-2 rounded-xl bg-[#0F1D33] text-white hover:bg-slate-800 text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                            <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                            <span>Go to Staff Bookings</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
