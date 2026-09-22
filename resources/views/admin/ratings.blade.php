@extends('layouts.admin')

@section('title', 'Ratings & Feedback | Kingdom Admin')

@section('content')
<div class="h-auto lg:h-full flex flex-col overflow-y-auto pr-2 custom-scrollbar" x-data="ratingsTable()">
    <!-- Header -->
    <div class="dashboard-header mb-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Ratings & Feedback</h1>
            <p class="text-slate-500 text-xs mt-0.5">Post-service performance reviews from partners</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Search -->
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" x-model="searchQuery" placeholder="Search partner or event..."
                    class="pl-9 pr-9 py-2.5 w-56 bg-white dark:bg-slate-900 border border-[#E6EAF0] dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 transition-all">
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            <!-- Rating Filter -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-[#E6EAF0] dark:border-slate-800 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-350 hover:border-slate-350 transition-all">
                    <i data-lucide="star" class="w-4 h-4"></i>
                    <span x-text="ratingFilter ? ratingFilter + '★' : 'All Ratings'" class="hidden sm:inline"></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition x-cloak class="absolute right-0 mt-1 z-[100] w-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl py-1" style="display:none;">
                    <button @click="ratingFilter = ''; open = false" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-850" :class="!ratingFilter ? 'font-bold text-kingdom-gold' : 'text-slate-600 dark:text-slate-400'">All Ratings</button>
                    @for($s = 5; $s >= 1; $s--)
                    <button @click="ratingFilter = '{{ $s }}'; open = false" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-850" :class="ratingFilter === '{{ $s }}' ? 'font-bold text-kingdom-gold' : 'text-slate-600 dark:text-slate-400'">{{ $s }} Star{{ $s > 1 ? 's' : '' }}</button>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start pb-6">
        <!-- Left Column: Review Feed -->
        <div class="lg:col-span-2 space-y-4">
            @forelse($ratings as $rating)
            <div class="bg-white dark:bg-slate-900 border border-[#E6EAF0] dark:border-slate-800 rounded-2xl shadow-sm p-5 hover:shadow-md transition-all duration-200"
                 x-show="matchRating('{{ addslashes($rating->partner->name ?? '') }}', '{{ addslashes($rating->eventBooking->event_name ?? '') }}', '{{ $rating->rating }}')">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#0F1C3F] dark:bg-indigo-950 flex items-center justify-center text-white text-sm font-bold shrink-0">
                            {{ substr($rating->partner->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-black text-slate-900 dark:text-white">{{ $rating->partner->name ?? 'Unknown Partner' }}</span>
                                <span class="text-xs text-slate-400 font-medium">on {{ $rating->created_at->format('M d, Y') }}</span>
                            </div>
                            <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">
                                Event: {{ $rating->eventBooking->event_name ?? 'N/A' }}
                            </h4>
                        </div>
                    </div>
                    
                    <!-- Delete Action -->
                    <button type="button" @click.stop="$dispatch('open-confirm-modal', { 
                                title: 'Delete Rating', 
                                message: 'Are you sure you want to delete this rating and review? This cannot be undone.', 
                                onConfirm: () => submitDeleteForm('{{ route('admin.ratings.destroy', $rating->id) }}')
                             })" 
                            class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors"
                            title="Delete Rating">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Star Ratings Block -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mt-4 p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-800">
                    <!-- Overall Rating -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 w-24">Overall:</span>
                        <div class="flex items-center gap-0.5">
                            @for($s = 1; $s <= 5; $s++)
                                <i data-lucide="star" class="w-4 h-4 {{ $s <= $rating->rating ? 'text-amber-400 fill-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <!-- Staff Rating -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 w-24 sm:w-auto">Staff Performance:</span>
                        <div class="flex items-center gap-0.5">
                            @for($s = 1; $s <= 5; $s++)
                                <i data-lucide="star" class="w-4 h-4 {{ $s <= $rating->staff_performance_rating ? 'text-amber-400 fill-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>

                @if($rating->review)
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-4 leading-relaxed bg-slate-50/50 dark:bg-slate-900/30 p-3.5 rounded-xl border border-dashed border-slate-100 dark:border-slate-800">
                        "{{ $rating->review }}"
                    </p>
                @else
                    <p class="text-xs italic text-slate-400 mt-4">No comments left.</p>
                @endif
            </div>
            @empty
            <div class="py-16 text-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl">
                <div class="h-16 w-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="star" class="w-8 h-8 text-slate-300"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Reviews Found</h3>
                <p class="text-slate-500 max-w-sm mx-auto mt-2 text-sm">Feedback submitted by partners will display here.</p>
            </div>
            @endforelse

            @if($ratings->hasPages())
            <!-- Pagination Links -->
            <div class="bg-white dark:bg-slate-900 p-4 border border-[#E6EAF0] dark:border-slate-800 rounded-2xl shadow-sm shrink-0">
                {{ $ratings->links() }}
            </div>
            @endif
        </div>

        <!-- Right Column: Stats & Top Rated Staff -->
        <div class="space-y-6">
            <!-- Summary Stats Card -->
            <div class="bg-white dark:bg-slate-900 border border-[#E6EAF0] dark:border-slate-800 rounded-2xl shadow-sm p-5 space-y-4">
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Summary Statistics</h3>
                
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Average Rating</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($averageRating, 1) }}★</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Reviews</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $totalReviews }}</p>
                    </div>
                </div>

                <!-- Star Distribution -->
                <div class="space-y-3">
                    @for($star = 5; $star >= 1; $star--)
                    @php
                        $count = $starCounts[$star] ?? 0;
                        $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3 cursor-pointer group" @click="ratingFilter = ratingFilter === '{{ $star }}' ? '' : '{{ $star }}'">
                        <span class="text-xs font-black text-slate-600 dark:text-slate-400 w-6 group-hover:text-kingdom-gold transition-colors">{{ $star }}★</span>
                        <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-amber-400 transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-slate-500 w-12 text-right">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Staff Recognition Card -->
            <div class="bg-white dark:bg-slate-900 border border-[#E6EAF0] dark:border-slate-800 rounded-2xl shadow-sm p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="award" class="w-5 h-5 text-indigo-500"></i>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Staff Recognition</h3>
                </div>
                <p class="text-xs text-slate-500">Most positively reviewed staff based on event feedback.</p>

                <div class="space-y-3.5">
                    @forelse($topStaff as $staff)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-slate-200 transition-all">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="relative shrink-0">
                                <x-avatar :user="$staff" class="w-9 h-9 rounded-full" />
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full flex items-center justify-center border border-white dark:border-slate-950">
                                    <i data-lucide="check" class="w-2.5 h-2.5 text-white"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-black text-slate-800 dark:text-white truncate">{{ $staff->name }}</p>
                                <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $staff->role ?? 'Staff' }}</p>
                            </div>
                        </div>
                        
                        <div class="text-right shrink-0">
                            <div class="flex items-center gap-0.5 justify-end">
                                <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400"></i>
                                <span class="text-xs font-black text-slate-800 dark:text-white">{{ number_format($staff->avg_rating, 1) }}</span>
                            </div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mt-0.5">{{ $staff->events_count }} Event{{ $staff->events_count > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-xs text-slate-400 italic">
                        No rated staff members yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.ratingsTable = function ratingsTable() {
    return {
        searchQuery: '',
        ratingFilter: '',

        matchRating(partner, eventName, rating) {
            const q = this.searchQuery.toLowerCase();
            const matchText = !q ||
                partner.toLowerCase().includes(q) ||
                eventName.toLowerCase().includes(q);
            const matchRating = !this.ratingFilter || rating === this.ratingFilter;
            return matchText && matchRating;
        }
    }
}
</script>
@endsection
