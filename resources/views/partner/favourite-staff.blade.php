@extends('layouts.partner')

@section('title', 'Favourite Staff | Kingdom Partner')

@section('content')
<div class="flex flex-col h-full max-h-full gap-6" x-data="{ 
    favouriteIds: @json($favouriteIds ?? []),
    async toggleFav(id) {
        try {
            const res = await fetch('{{ route('partner.favourite-staff.toggle') }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ applicant_id: id })
            });
            const data = await res.json();
            
            if (!res.ok) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Authentication Required',
                        text: data.error || 'You must be logged in to favourite staff.',
                        confirmButtonColor: '#0F1D33'
                    });
                }
                return;
            }

            if (data.status === 'added') {
                this.favouriteIds.push(id);
            } else {
                this.favouriteIds = this.favouriteIds.filter(fid => fid !== id);
            }
        } catch(e) { console.error(e); }
    },
    isFav(id) {
        return this.favouriteIds.includes(id);
    }
}">
    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Favourite Staff</h1>
            <p class="text-sm text-slate-500 mt-2">Manage your preferred staff members. Favourited staff will appear in your booking form.</p>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar pr-2 pb-4 space-y-10">
    
    @if(isset($workedApplicants) && count($workedApplicants) > 0)
        <!-- Favourite Staff Section -->
        <div class="mb-10" x-show="favouriteIds.length > 0">
            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i data-lucide="star" class="text-kingdom-gold w-5 h-5"></i>
                Favourite Staff
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($favourites as $member)
                <div class="group bg-white dark:bg-slate-900 rounded-[14px] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden relative"
                     x-show="isFav({{ $member->id }})" x-transition>
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-kingdom-gold/5 rounded-full blur-2xl group-hover:opacity-15 transition-opacity duration-500"></div>
                    
                    <div class="p-5 relative z-10 flex flex-col h-full justify-between">
                        <div>
                            <!-- Avatar + Info -->
                            <div class="flex items-center gap-4 mb-4">
                                <x-avatar :image="$member->image" :name="$member->name" class="h-14 w-14 rounded-full shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300" />
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-kingdom-gold transition-colors">{{ $member->name }}</h3>
                                    <p class="text-xs text-slate-400 font-semibold truncate">{{ $member->role }}</p>
                                    @if($member->location)
                                    <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                        {{ $member->location }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action buttons row -->
                        <div class="flex gap-2">
                            <button @click="toggleFav({{ $member->id }})"
                                    class="flex-1 py-2.5 rounded-xl text-xs font-bold transition-all bg-red-50 hover:bg-red-100 text-red-500 border border-red-100 hover:border-red-200 flex items-center justify-center gap-1">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                Remove
                            </button>
                            <a href="{{ route('partner.book-staff') }}" class="flex-1 py-2.5 bg-kingdom-gold hover:bg-yellow-600 text-white rounded-xl font-bold text-xs transition-all shadow-md shadow-kingdom-gold/15 hover:shadow-kingdom-gold/30 flex items-center justify-center gap-1">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                Book Staff
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Previously Worked Staff Section -->
        <div>
            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5"></i>
                Previously Worked Staff ({{ count($workedApplicants) }})
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($workedApplicants as $staff)
                <div class="bg-white dark:bg-slate-900 rounded-[14px] border border-slate-100 dark:border-slate-800 p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-4">
                            <x-avatar :image="$staff->image" :name="$staff->name" class="w-14 h-14 rounded-full flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-bold text-slate-900 dark:text-white truncate">{{ $staff->name }}</h4>
                                <p class="text-xs text-slate-400 font-semibold truncate">{{ $staff->role }}</p>
                                @if($staff->location)
                                <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-slate-400"></i>
                                    {{ $staff->location }}
                                </p>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-2 mb-6 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Total Events Worked</span>
                                <span class="font-black text-slate-900 dark:text-white bg-slate-200 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ $staff->total_events_worked }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Last Event Worked</span>
                                <span class="font-semibold text-slate-700 dark:text-gray-300 truncate max-w-[150px]" title="{{ $staff->last_event_worked }}">{{ $staff->last_event_worked }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Toggle Favourite Button -->
                    <button @click="toggleFav({{ $staff->id }})"
                            class="w-full py-2.5 rounded-xl text-xs font-bold transition-all duration-200 border flex items-center justify-center gap-2"
                            :class="isFav({{ $staff->id }})
                                ? 'bg-red-50 hover:bg-red-100 text-red-500 border-red-200 hover:border-red-300'
                                : 'bg-kingdom-navy hover:bg-slate-800 text-white border-transparent shadow-md shadow-kingdom-navy/10'">
                        <template x-if="isFav({{ $staff->id }})">
                            <span class="flex items-center gap-1.5"><i data-lucide="star-off" class="w-4 h-4"></i> Remove Favourite</span>
                        </template>
                        <template x-if="!isFav({{ $staff->id }})">
                            <span class="flex items-center gap-1.5"><i data-lucide="star" class="w-4 h-4"></i> Add to Favourites</span>
                        </template>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-20 text-slate-400">
            <div class="p-4 rounded-full bg-slate-100 dark:bg-slate-800 mb-4 text-slate-500">
                <i data-lucide="users" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700 dark:text-white mb-2">📋 No Staff History Yet</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center max-w-md">Staff members will appear here after they complete work on your events.</p>
        </div>
    @endif
    </div>
</div>
@endsection
