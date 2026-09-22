@extends('layouts.partner')

@section('title', 'Invoices | Kingdom Partner')

@section('content')
<div class="flex flex-col h-full max-h-full gap-6">
    <!-- Header -->
    <div class="flex-none">
        <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Invoices</h1>
        <p class="text-sm text-slate-500 mt-2 font-medium">View and download your event invoices.</p>
    </div>

    <!-- Summary Metrics -->
    <div class="flex-none grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Total Invoices Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Total Invoices</span>
                <span class="text-3xl font-black text-slate-850 dark:text-white block">{{ $stats['total_invoices'] }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center text-blue-500 shrink-0">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <!-- Latest Invoice Date Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Latest Invoice Date</span>
                <span class="text-xl font-black text-slate-850 dark:text-white block leading-tight mt-1">
                    {{ $stats['latest_invoice_date'] ? $stats['latest_invoice_date']->format('d M, Y') : 'N/A' }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-500 shrink-0">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Total Invoiced Amount Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Total Invoiced Amount</span>
                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400 block">£{{ number_format($stats['total_invoiced_amount'], 2) }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-500 shrink-0">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879-.659c1.171-.879 3.07-.879 4.242 0 1.172.879 1.172 2.303 0 3.182C13.536 22.219 12.768 22 12 22c-.768 0-1.536-.22-2.121-.659M9 8.818l.879-.659c1.171-.879 3.07-.879 4.242 0 1.172.879 1.172 2.303 0 3.182C13.536 12.219 12.768 12 12 12c-.768 0-1.536-.22-2.121-.659m8.781-4.02L12 2.25M6.219 6.219L12 2.25m0 0v20.25" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Invoices List Card -->
    <div class="flex-1 min-h-0 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex-none flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-md font-black text-slate-800 uppercase tracking-wider">Invoice History</h2>
        </div>

        <div class="flex-1 overflow-y-auto relative">
            @if($invoices->isEmpty())
                <div class="text-center py-20 bg-white dark:bg-slate-900">
                    <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-850 flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-800/80 shadow-sm">
                        <svg class="w-10 h-10 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">📄 No Invoices Available</h3>
                    <p class="text-slate-500 text-sm max-w-sm mx-auto mt-2 font-medium">Invoices will appear here once event timesheets have been approved.</p>
                </div>
            @else
                <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="sticky top-0 z-20">
                        <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-xs">
                            <th class="px-4 py-4">Ref</th>
                            <th class="px-4 py-4">Event</th>
                            <th class="px-4 py-4 text-center">Invoice Date</th>
                            <th class="px-4 py-4 text-right">Amount</th>
                            <th class="px-4 py-4 text-center">Status</th>
                            <th class="px-4 py-4 text-center">Download PDF</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent">
                        @foreach($invoices as $invoice)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4 align-middle font-bold text-slate-900">
                                {{ $invoice->invoice_ref }}
                            </td>
                            <td class="px-4 py-4 align-middle">
                                <div class="font-bold text-slate-800">{{ $invoice->eventBooking->event_name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">{{ $invoice->eventBooking->booking_ref ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-4 align-middle text-center text-xs font-semibold text-slate-500">
                                {{ $invoice->issue_date ? $invoice->issue_date->format('d M, Y') : '-' }}
                            </td>
                            <td class="px-4 py-4 align-middle text-right font-black text-slate-800">
                                £{{ number_format($invoice->total_amount, 2) }}
                            </td>
                            @php
                                $badgeClass = $invoice->status === 'Paid'
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30'
                                    : 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30';
                            @endphp
                            <td class="px-4 py-4 align-middle text-center">
                                <span class="inline-flex px-2.5 py-0.5 text-[10px] font-black rounded-lg border uppercase tracking-wider {{ $badgeClass }}">
                                    {{ $invoice->status === 'Paid' ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 align-middle text-center">
                                <a href="{{ route('partner.invoices.pdf', $invoice->id) }}" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Download PDF">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
