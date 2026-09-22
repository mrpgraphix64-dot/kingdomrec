{{--
    Standard empty state — never a bare "No records found" in a blank white box.
    Always pair with a reason + a way forward.

    Usage:
    <x-ui.empty-state icon="calendar-x" title="No bookings yet" description="Create your first booking to start managing staff and quotations.">
        <a href="..." class="btn-primary">+ New Booking</a>
    </x-ui.empty-state>
--}}
@props(['icon' => 'inbox', 'title' => 'No records found', 'description' => null])

<div class="py-14 px-6 flex flex-col items-center justify-center text-center">
    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-4">
        <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
    </div>
    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $title }}</p>
    @if($description)
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 max-w-sm">{{ $description }}</p>
    @endif
    @isset($slot)
        @if(trim($slot))
            <div class="mt-5">{{ $slot }}</div>
        @endif
    @endisset
</div>
