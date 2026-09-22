{{--
    Standard summary metric tile. Use at most 3-4 per page — only for numbers an admin
    would actually act on, never "because there's space."

    Usage:
    <x-ui.stat-card label="Total Bookings" :value="$total" icon="list" accent="blue" href="..." />
    <x-ui.stat-card label="Pending" :value="$pending" icon="clock" accent="amber" :active="$statusFilter === 'Pending'" />

    accent: blue | emerald | amber | red | slate (default)
--}}
@props([
    'label',
    'value',
    'icon' => null,
    'accent' => 'slate',
    'href' => null,
    'active' => false,
    'sub' => null,
])

@php
    $accents = [
        'blue'    => ['border' => 'border-l-blue-500', 'icon' => 'text-blue-500', 'ring' => 'ring-blue-500 bg-blue-50/30 dark:bg-blue-950/20'],
        'emerald' => ['border' => 'border-l-emerald-500', 'icon' => 'text-emerald-500', 'ring' => 'ring-emerald-500 bg-emerald-50/30 dark:bg-emerald-950/20'],
        'amber'   => ['border' => 'border-l-amber-500', 'icon' => 'text-amber-500', 'ring' => 'ring-amber-500 bg-amber-50/30 dark:bg-amber-950/20'],
        'red'     => ['border' => 'border-l-red-500', 'icon' => 'text-red-500', 'ring' => 'ring-red-500 bg-red-50/30 dark:bg-red-950/20'],
        'slate'   => ['border' => 'border-l-slate-400', 'icon' => 'text-slate-400', 'ring' => 'ring-slate-400 bg-slate-50/30 dark:bg-slate-800/40'],
    ];
    $a = $accents[$accent] ?? $accents['slate'];
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" wire:navigate @else type="button" @endif
    {{ $attributes->merge(['class' => "stat-card group text-left w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 border-l-4 {$a['border']} rounded-xl px-4 py-3.5 transition-all duration-150 hover:shadow-md hover:-translate-y-0.5"]) }}
    @class(['ring-2' => $active, $a['ring'] => $active])
>
    <span class="flex items-start justify-between gap-2 mb-1.5">
        <span class="text-[10.5px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $label }}</span>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5 {{ $a['icon'] }} opacity-70 group-hover:opacity-100 transition-opacity shrink-0"></i>
        @endif
    </span>
    <span class="block text-2xl font-black text-slate-900 dark:text-white tabular-nums leading-none">{{ $value }}</span>
    @if($sub)
        <span class="block text-[11px] text-slate-400 dark:text-slate-500 font-medium mt-1 truncate">{{ $sub }}</span>
    @endif
</{{ $tag }}>
