@php
    $segments = request()->segments();
    $breadcrumbs = [];
    $url = '';
    
    // Map route segments to readable names
    $nameMap = [
        'partner' => 'Partner Portal',
        'dashboard' => 'Dashboard',
        'events' => 'Event List',
        'shifts' => 'Shift Listings',
        'timesheets' => 'Timesheets',
        'quotations' => 'Quotations',
        'book-staff' => 'Staff Booking',
        'rate-card' => 'Rate Card',
        'favourite-staff' => 'Favourite Staff',
        'messages' => 'Notes & Messages',
        'contacts' => 'Contacts',
        'alerts' => 'News/Alerts',
        'profile' => 'Profile',
    ];
    
    foreach ($segments as $i => $segment) {
        $url .= '/' . $segment;
        $name = $nameMap[$segment] ?? ucwords(str_replace('-', ' ', $segment));
        $isLast = $i === count($segments) - 1;
        $breadcrumbs[] = ['name' => $name, 'url' => $url, 'isLast' => $isLast];
    }
@endphp

@if(count($breadcrumbs) > 1)
<nav aria-label="Breadcrumb" class="px-8 py-2 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm border-b border-slate-100/50 dark:border-slate-800/50">
    <ol class="flex items-center gap-1.5 text-xs">
        @foreach($breadcrumbs as $crumb)
            @if(!$crumb['isLast'])
                <li class="flex items-center gap-1.5">
                    <a href="{{ $crumb['url'] }}" class="text-slate-400 hover:text-kingdom-gold transition-colors font-medium" wire:navigate>{{ $crumb['name'] }}</a>
                    <i data-lucide="chevron-right" class="text-[14px] text-slate-300 w-5 h-5"></i>
                </li>
            @else
                <li>
                    <span class="text-slate-700 dark:text-slate-200 font-bold">{{ $crumb['name'] }}</span>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
