{{--
    Standard status pill. One shared color mapping so "Approved" looks the same
    everywhere it appears (bookings, invoices, timesheets, shifts).

    Usage: <x-ui.status-badge :status="$booking->status" />
    Override the map per-domain by passing :map="[...]" if a page has statuses
    not covered below (falls back to slate/neutral for unknown values).
--}}
@props(['status', 'map' => []])

@php
    $defaultMap = [
        'approved' => 'emerald', 'confirmed' => 'emerald', 'active' => 'emerald', 'paid' => 'emerald',
        'completed' => 'emerald', 'checked out' => 'emerald', 'billed' => 'emerald', 'hired' => 'emerald',
        'pending' => 'amber', 'draft' => 'amber', 'sent' => 'amber', 'submitted' => 'amber',
        'awaiting invoice' => 'amber', 'unpaid' => 'amber', 'assigned' => 'amber',
        'rejected' => 'red', 'cancelled' => 'red', 'no show' => 'red', 'declined' => 'red',
        'unassigned' => 'slate', 'closed' => 'slate', 'archived' => 'slate',
    ];
    $colors = array_merge($defaultMap, array_change_key_case($map));
    $key = strtolower(trim($status ?? ''));
    $color = $colors[$key] ?? 'slate';

    $styles = [
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/40',
        'amber'   => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/40',
        'red'     => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/30 dark:text-red-400 dark:border-red-900/40',
        'slate'   => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 text-[10px] font-black rounded-md border uppercase tracking-wider {$styles[$color]}"]) }}>
    {{ $status }}
</span>
