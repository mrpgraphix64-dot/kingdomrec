{{--
    Standard page header — one consistent structure for every admin/partner page.
    Usage:
    <x-ui.page-header title="Staff Bookings & Quotations" description="Manage client event bookings, shifts & approvals.">
        <x-slot:actions>
            ... search / filter / primary button ...
        </x-slot:actions>
    </x-ui.page-header>
--}}
@props(['title', 'description' => null])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-1">
    <div class="min-w-0">
        <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight truncate">{{ $title }}</h1>
        @if($description)
            <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            {{ $actions }}
        </div>
    @endisset
</div>
