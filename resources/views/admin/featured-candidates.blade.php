@extends('layouts.admin')

@section('title', 'Featured Candidates Management | Kingdom Admin')

@section('content')
<div class="max-w-7xl mx-auto flex flex-col space-y-6" x-data="{
    search: '{{ request('search') }}',
    filter: '{{ request('filter', '') }}',
    saving: false,
    toggleFeatured(id, el) {
        fetch('{{ url('/admin/featured-candidates/toggle') }}/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Update UI state
                let card = document.getElementById('candidate-card-' + id);
                let badge = document.getElementById('badge-' + id);
                let checkbox = document.getElementById('checkbox-' + id);
                
                if (data.is_featured) {
                    if (card) {
                        card.classList.add('border-amber-300', 'bg-amber-50/25', 'ring-2', 'ring-amber-400/40');
                        card.classList.remove('bg-white', 'border-slate-200');
                    }
                    if (badge) {
                        badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 shadow-sm';
                        badge.innerHTML = '<i data-lucide=\'star\' class=\'w-3.5 h-3.5 fill-amber-500 text-amber-500\'></i> Featured on Homepage';
                    }
                    if (checkbox) checkbox.checked = true;
                } else {
                    if (card) {
                        card.classList.remove('border-amber-300', 'bg-amber-50/25', 'ring-2', 'ring-amber-400/40');
                        card.classList.add('bg-white', 'border-slate-200');
                    }
                    if (badge) {
                        badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200';
                        badge.innerHTML = '<i data-lucide=\'eye-off\' class=\'w-3.5 h-3.5 text-slate-400\'></i> Not Featured';
                    }
                    if (checkbox) checkbox.checked = false;
                }
                if (window.lucide) window.lucide.createIcons();

                // Keep the header's total-featured count in sync with each toggle
                const counter = document.getElementById('total-featured-count');
                if (counter) {
                    const current = parseInt(counter.textContent, 10) || 0;
                    counter.textContent = data.is_featured ? current + 1 : Math.max(0, current - 1);
                }
            }
        })
        .catch(err => console.error(err));
    }
}">

    <!-- Page Header -->
    <div class="dashboard-header flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10 border-b border-slate-200/80 pb-5">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold uppercase tracking-wider mb-2">
                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-500 text-amber-500"></i>
                <span>Homepage Showcase Control</span>
            </div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                Featured Candidates Management
            </h1>
            <p class="text-slate-600 text-sm mt-1">Curate and feature outstanding candidate profiles on the public website homepage.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2 flex items-center gap-4 text-xs font-bold text-slate-700 shadow-sm">
                <div class="flex items-center gap-1.5 text-amber-700">
                    <i data-lucide="star" class="w-4 h-4 fill-amber-500 text-amber-500"></i>
                    <span><strong id="total-featured-count" class="text-slate-900 text-sm font-extrabold">{{ $totalFeaturedCount }}</strong> Featured</span>
                </div>
                <div class="h-4 w-px bg-slate-200"></div>
                <div class="text-slate-600">
                    <span><strong class="text-slate-900 text-sm font-extrabold">{{ $totalCandidatesCount }}</strong> Total Talent</span>
                </div>
            </div>

            <a href="{{ route('admin.settings') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs flex items-center gap-2 transition-all shadow-sm">
                <i data-lucide="settings" class="w-4 h-4 text-slate-500"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Alert Banner -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-bold flex items-center gap-3 animate-in fade-in slide-in-from-top-3 duration-300">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters & Search Toolbar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.featured-candidates.index') }}" method="GET" class="w-full flex flex-col md:flex-row items-center gap-3">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 hover:bg-white focus:bg-white rounded-xl border border-slate-200 text-sm font-medium text-slate-900 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all"
                       placeholder="Search name, role, email...">
            </div>

            <!-- Filter Status -->
            <div class="w-full md:w-52">
                <select name="filter" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2 bg-slate-50 hover:bg-white focus:bg-white rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:outline-none focus:border-amber-500 transition-all">
                    <option value="">All Candidates</option>
                    <option value="featured" {{ request('filter') === 'featured' ? 'selected' : '' }}>⭐ Featured Only</option>
                    <option value="standard" {{ request('filter') === 'standard' ? 'selected' : '' }}>👤 Standard Only</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition-colors shrink-0 shadow-sm">
                Filter Results
            </button>

            @if(request('search') || request('filter'))
                <a href="{{ route('admin.featured-candidates.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 underline shrink-0 px-2">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Candidates Grid Container -->
    <div class="w-full">
        @if($applicants->count() > 0)
            <div>
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-4">
                    Candidates Catalogue ({{ $applicants->total() }} Total) · toggling Featured saves instantly
                </p>

                <!-- Responsive Grid: 1 col (mobile), 2 cols (tablet), 3 cols (desktop) -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($applicants as $c)
                        <div id="candidate-card-{{ $c->id }}" 
                             class="group relative rounded-2xl p-5 border transition-all duration-200 flex flex-col justify-between {{ $c->is_featured ? 'bg-amber-50/25 border-amber-300 ring-2 ring-amber-400/40 shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-md' }}">
                            
                            <div class="space-y-4">
                                <!-- Top Row: Avatar + Info (Left) and Checkbox (Right) -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $c->profile_photo_url }}" alt="{{ $c->name }}" 
                                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($c->name) }}&background=0F1D33&color=fff'"
                                             class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm shrink-0 bg-slate-100">
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-amber-800 transition-colors truncate">
                                                {{ $c->name }}
                                            </h3>
                                            <div class="inline-flex items-center gap-1.5 mt-0.5">
                                                <span class="text-xs font-semibold text-slate-700 truncate">
                                                    {{ $c->sub_category ?? $c->role ?? 'Applicant' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Featured Toggle (saves instantly via AJAX — the only control needed) -->
                                    <label class="relative inline-flex items-center cursor-pointer p-1 shrink-0" title="Feature on homepage">
                                        <input type="checkbox"
                                               id="checkbox-{{ $c->id }}"
                                               {{ $c->is_featured ? 'checked' : '' }}
                                               @change="toggleFeatured({{ $c->id }}, $el)"
                                               class="w-5 h-5 rounded border-slate-300 text-amber-600 focus:ring-amber-500 cursor-pointer">
                                    </label>
                                </div>

                                <!-- Contact Details Group -->
                                <div class="space-y-1.5 text-xs text-slate-700 bg-slate-50/80 rounded-xl p-2.5 border border-slate-100">
                                    <div class="flex items-center gap-2 truncate">
                                        <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>
                                        <span class="truncate font-medium">{{ $c->email }}</span>
                                    </div>
                                    @if($c->location)
                                        <div class="flex items-center gap-2 truncate">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>
                                            <span class="truncate font-medium">{{ $c->location }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Summary / Bio Snippet -->
                                <div class="text-xs text-slate-600 leading-relaxed italic line-clamp-2 px-1">
                                    "{{ $c->bio ?? 'Highly skilled professional ready for immediate dispatch.' }}"
                                </div>
                            </div>

                            <!-- Footer: Dynamic Status Badge & Direct Toggle Action -->
                            <div class="pt-3.5 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                <span id="badge-{{ $c->id }}"
                                      class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $c->is_featured ? 'bg-amber-100 text-amber-900 border border-amber-300 shadow-sm' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    @if($c->is_featured)
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-500 text-amber-500"></i>
                                        <span>Featured on Homepage</span>
                                    @else
                                        <i data-lucide="eye-off" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span>Not Featured</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($applicants->hasPages())
                <!-- Pagination -->
                <div class="mt-6 pt-4 border-t border-slate-200/80">
                    {{ $applicants->links() }}
                </div>
                @endif
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-200 shadow-sm">
                <i data-lucide="user-x" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <h3 class="text-base font-bold text-slate-800">No candidates found</h3>
                <p class="text-xs text-slate-500 mt-1">Try adjusting your search query or filter selection.</p>
            </div>
        @endif
    </div>
</div>
@endsection
