@extends('layouts.partner')

@section('title', 'News & Alerts | Kingdom Partner')

@section('content')
@php
    $priorityStyles = [
        'urgent' => 'bg-red-100 text-red-700 border-red-200',
        'important' => 'bg-amber-100 text-amber-700 border-amber-200',
        'info' => 'bg-blue-100 text-blue-700 border-blue-200',
        'notice' => 'bg-purple-100 text-purple-700 border-purple-200',
    ];
    $iconBg = [
        'urgent' => 'from-red-400 to-red-600 shadow-red-500/30',
        'important' => 'from-amber-400 to-amber-600 shadow-amber-500/30',
        'info' => 'from-blue-400 to-blue-600 shadow-blue-500/30',
        'notice' => 'from-purple-400 to-purple-600 shadow-purple-500/30',
    ];
    $defaultIcons = [
        'urgent' => 'alert-triangle',
        'important' => 'banknote',
        'info' => 'info',
        'notice' => 'bell',
    ];
@endphp

<div class="flex flex-col h-full max-h-full gap-6">
    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">News & Alerts</h1>
            <p class="text-sm text-slate-500 mt-2">Stay updated with announcements and important notifications. <span class="font-semibold text-slate-400">({{ $notifications->count() }} alerts)</span></p>
        </div>
        @if($notifications->whereNull('read_at')->count() > 0)
        <form method="POST" action="{{ route('partner.alerts') }}">
            @csrf
            <input type="hidden" name="mark_all_read" value="1">
            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm text-slate-600 font-bold text-sm border border-slate-200 hover:bg-slate-50 transition-colors">
                <i data-lucide="check-check" class="w-4 h-4"></i>
                Mark all as read
            </button>
        </form>
        @endif
    </div>

    @if($notifications->count() > 0)
    <!-- Alert Cards -->
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar pr-2 pb-4 space-y-4">
        @foreach($notifications as $notification)
        @php
            $data = $notification->data;
            $priority = $data['priority'] ?? 'info';
            $icon = $data['icon'] ?? ($defaultIcons[$priority] ?? 'bell');
            $colorKey = $priority;
            $bgGradient = $iconBg[$colorKey] ?? $iconBg['info'];
            $badgeStyle = $priorityStyles[$colorKey] ?? $priorityStyles['info'];
        @endphp
        <div class="group bg-white dark:bg-slate-900 rounded-[14px] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden {{ is_null($notification->read_at) ? 'ring-2 ring-kingdom-gold/20' : '' }}">
            <div class="p-5 flex gap-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="p-2.5 bg-gradient-to-br {{ $bgGradient }} rounded-[10px] text-white shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5 z-10 text-white"></i>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-kingdom-gold transition-colors">{{ $data['title'] ?? 'Notification' }}</h3>
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase border {{ $badgeStyle }}">{{ ucfirst($priority) }}</span>
                        @if(is_null($notification->read_at))
                        <span class="w-2.5 h-2.5 rounded-full bg-kingdom-gold shadow-[0_0_6px_rgba(218,165,32,0.5)]"></span>
                        @endif
                    </div>
                    <p class="text-[13px] text-slate-500 leading-relaxed mb-2">{{ $data['message'] ?? $data['desc'] ?? '' }}</p>
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        {{ $notification->created_at->format('M d, Y') }}
                        <span class="text-slate-300">·</span>
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="flex-1 flex items-center justify-center">
        <div class="text-center p-8">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="bell-off" class="w-10 h-10 text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No Alerts Yet</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">You're all caught up! New alerts and announcements from Kingdom Recruitments will appear here.</p>
        </div>
    </div>
    @endif
</div>
@endsection
