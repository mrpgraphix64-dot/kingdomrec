@extends('layouts.admin')

@section('title', 'Venues | Kingdom Admin')

@section('content')
<div x-data="venueManager()" @keydown.escape.window="modalOpen = false" class="h-full flex flex-col space-y-3">
    <div class="dashboard-header flex flex-col sm:flex-row sm:items-center justify-between gap-2 shrink-0">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Event Venues</h1>
            <p class="text-slate-500 text-xs">Manage event locations & facilities</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto font-sans">
            <!-- Search Bar -->
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" x-model="searchQuery" placeholder="Search venues..."
                    class="pl-9 pr-4 py-1.5 w-60 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all shadow-sm">
            </div>
            <!-- Count Badge -->
            <span class="text-slate-600 text-[11px] font-bold bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl flex items-center gap-1 shadow-sm shrink-0">
                <i data-lucide="building" class="w-3.5 h-3.5 text-slate-400"></i>
                <span x-text="filteredVenuesCount() + ' Venues'"></span>
            </span>
            <button 
              @click="openCreateModal()"
              class="flex items-center gap-1.5 bg-[#0F1D33] hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold text-xs shadow-sm transition-all shrink-0"
            >
              <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Venue
            </button>
        </div>
    </div>

    <!-- Venues Table -->
    <div class="anim-fade-in-up flex flex-col bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Combined Table -->
        <div class="overflow-x-auto w-full" x-show="filteredVenuesCount() > 0">
          <table class="w-full min-w-[1000px] text-left border-collapse">
            <thead>
              <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-xs border-b border-slate-200">
                            <th class="w-24 py-4 px-4 text-center">ID</th>
                            <th class="w-[20%] px-4 py-4 text-left">Venue Name</th>
                            <th class="w-[15%] px-4 py-4 text-left">Contact No.</th>
                            <th class="w-[15%] px-4 py-4 text-left">Email</th>
                            <th class="w-[20%] px-4 py-4 text-left">Location</th>
                            <th class="px-4 py-4 text-left">Description</th>
                            <th class="w-28 px-4 py-4 text-center">Actions</th>
                        </tr>
            </thead>
            <tbody class="bg-transparent">
                        @foreach($venues as $venue)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors group cursor-pointer"
                                x-show="matchesSearch({
                                    name: '{{ addslashes($venue->name) }}',
                                    email: '{{ addslashes($venue->email) }}',
                                    contact_info: '{{ addslashes($venue->contact_info) }}',
                                    address: '{{ addslashes($venue->address) }}',
                                    city: '{{ addslashes($venue->city) }}',
                                    postcode: '{{ addslashes($venue->postcode) }}',
                                    country: '{{ addslashes($venue->country) }}',
                                    description: '{{ addslashes($venue->description) }}'
                                })">
                                
                                <!-- ID -->
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono text-[10px] font-bold border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-colors shadow-sm">{{ $loop->iteration }}</span>
                                </td>
    
                                <!-- Venue Name -->
                                <td class="px-4 py-4">
                                    <span class="font-bold text-slate-800 text-xs group-hover:text-indigo-600 transition-colors truncate">{{ $venue->name }}</span>
                                </td>
    
                                <!-- Contact No. -->
                                <td class="px-4 py-4">
                                    @if($venue->contact_info)
                                        <div class="flex items-center justify-start gap-1.5 text-[11px] font-medium text-slate-600 truncate">
                                            <div class="w-4 h-4 rounded bg-slate-100 flex items-center justify-center shrink-0">
                                                <i data-lucide="phone" class="w-[10px] h-[10px] text-slate-400"></i>
                                            </div>
                                            <span class="truncate">{{ $venue->contact_info }}</span>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Not provided</span>
                                    @endif
                                </td>
    
                                <!-- Email -->
                                <td class="px-4 py-4">
                                    @if($venue->email)
                                        <div class="flex items-center justify-start gap-1.5 text-[11px] font-medium text-slate-600 truncate">
                                            <div class="w-4 h-4 rounded bg-slate-100 flex items-center justify-center shrink-0">
                                                <i data-lucide="mail" class="w-[10px] h-[10px] text-slate-400"></i>
                                            </div>
                                            <a href="mailto:{{ $venue->email }}" class="truncate hover:text-indigo-600 hover:underline transition-colors">{{ $venue->email }}</a>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Not provided</span>
                                    @endif
                                </td>
    
                                <!-- Location -->
                                <td class="px-4 py-4">
                                    <div class="flex flex-col items-start gap-0.5">
                                        <div class="flex items-start gap-1.5">
                                            <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 mt-0.5 shrink-0"></i>
                                            <span class="text-[11px] font-bold text-slate-600 line-clamp-1">{{ $venue->address }}</span>
                                        </div>
                                        @if($venue->city || $venue->postcode)
                                            <div class="text-[9px] text-slate-500 font-medium truncate ml-4.5">
                                                {{ collect([$venue->city, $venue->postcode, $venue->country])->filter()->join(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
    
                                 <!-- Description -->
                                <td class="px-4 py-4">
                                    <p class="text-[11px] text-slate-500 line-clamp-1 leading-relaxed" title="{{ $venue->description }}">
                                        {{ $venue->description ?? 'No description' }}
                                    </p>
                                </td>
    
                                <!-- Actions -->
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click.stop="openEditModal({{ json_encode($venue) }})" 
                                                class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-650 transition-colors" 
                                                title="Edit Venue">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </button>
                                        <form action="{{ route('admin.venue.destroy', $venue->id) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { title: 'Delete Venue', message: 'Are you sure you want to delete this venue? This action cannot be undone.', onConfirm: () => submitDeleteForm('{{ route('admin.venue.destroy', $venue->id) }}') })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" 
                                                    title="Delete Venue">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Empty States — real full-width blocks, not colspan cells inside the wide scrolling table -->
            @if($venues->count() === 0)
                <div class="px-4 py-12 text-center text-slate-400 bg-white">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 shadow-sm mb-1">
                            <i data-lucide="building-2" class="w-6 h-6 text-slate-350"></i>
                        </div>
                        <span class="text-xs font-bold text-kingdom-navy">No Venues Found</span>
                        <p class="text-[11px] text-slate-400">Click "Add Venue" to create your first event location.</p>
                    </div>
                </div>
            @else
                <div x-show="filteredVenuesCount() === 0" style="display: none;" class="px-4 py-12 text-center text-slate-400 bg-white">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 shadow-sm mb-1">
                            <i data-lucide="search-code" class="w-6 h-6 text-slate-355"></i>
                        </div>
                        <span class="text-xs font-bold text-kingdom-navy">No Matching Venues</span>
                        <p class="text-[11px] text-slate-400">Try adjusting your search keywords to find the venue.</p>
                    </div>
                </div>
            @endif

            <!-- Pagination Links -->
            <div class="p-4 border-t border-slate-100 bg-white shrink-0">
                {{ $venues->links() }}
            </div>
        </div>

    <!-- Create/Edit Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>
        
        <div class="flex min-h-full items-center justify-center p-4 text-center" @click="modalOpen = false">
            <div x-show="modalOpen"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                
                <div class="px-6 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                    <h3 class="font-bold text-lg text-kingdom-navy" x-text="isEditing ? 'Edit Venue' : 'Add New Venue'"></h3>
                    <button @click="modalOpen = false" class="bg-white p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="px-6 pb-6 pt-2">
                    <form :action="formAction" method="POST" class="flex flex-col gap-4">
                        @csrf
                        <div x-html="methodField" class="hidden"></div>

                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Venue Name <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="building-2" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                </div>
                                <input type="text" name="name" x-model="formData.name"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                    placeholder="e.g. Innovation Lab">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Email</label>
                                 <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="mail" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" x-model="formData.email"
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                        placeholder="email@venue.com">
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Contact No.</label>
                                 <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="phone" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <input type="text" name="contact_info" x-model="formData.contact_info"
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                        placeholder="+44 123 456 7890">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Address <span class="text-red-500">*</span></label>
                             <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="map-pin" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                </div>
                                <input type="text" name="address" x-model="formData.address"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                    placeholder="e.g. Building D, Ground Floor">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">City</label>
                                <div class="relative group">
                                    <input type="text" name="city" x-model="formData.city"
                                        class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                        placeholder="City">
                                </div>
                            </div>
                             <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Postcode</label>
                                <div class="relative group">
                                    <input type="text" name="postcode" x-model="formData.postcode"
                                        class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                        placeholder="Postcode">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Country</label>
                             <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="globe" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                </div>
                                <input type="text" name="country" x-model="formData.country"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                    placeholder="Country">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Description</label>
                            <div class="relative group">
                                <textarea name="description" x-model="formData.description" rows="3"
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none resize-none" 
                                    placeholder="Brief description about the venue, location guide, etc."></textarea>
                            </div>
                        </div>

                        <div class="flex gap-4 mt-4 pt-4 border-t border-slate-100">
                            <button type="button" @click="modalOpen = false" class="flex-1 px-4 py-3.5 rounded-xl border-2 border-slate-100 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-800 transition-all">
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 hover:shadow-xl hover:shadow-kingdom-navy/30 active:scale-95 flex items-center justify-center gap-2">
                                <span x-text="isEditing ? 'Save Changes' : 'Save Venue'"></span>
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        window.venueManager = function venueManager() {
            return {
                modalOpen: false,
                init() {
                    this.$watch('modalOpen', val => document.body.style.overflow = val ? 'hidden' : '');
                },
                isEditing: false,
                searchQuery: '',
                venues: @json($venues->items()),
                formData: {
                    id: null,
                    name: '',
                    email: '',
                    contact_info: '',
                    address: '',
                    city: '',
                    postcode: '',
                    country: '',
                    description: ''
                },
                formAction: '',
                methodField: '',

                openCreateModal() {
                    this.isEditing = false;
                    this.formData = { id: null, name: '', email: '', contact_info: '', address: '', city: '', postcode: '', country: '', description: '' };
                    this.formAction = "{{ route('admin.venue.store') }}";
                    this.methodField = '';
                    this.modalOpen = true;
                },

                openEditModal(venue) {
                    this.isEditing = true;
                    this.formData = {
                        id: venue.id,
                        name: venue.name,
                        email: venue.email || '',
                        contact_info: venue.contact_info || '',
                        address: venue.address,
                        city: venue.city || '',
                        postcode: venue.postcode || '',
                        country: venue.country || '',
                        description: venue.description || ''
                    };
                    
                    this.formAction = `/admin/venue/${venue.id}`;
                    this.methodField = '<input type="hidden" name="_method" value="PUT">';
                    this.modalOpen = true;
                },

                filteredVenuesCount() {
                    if (!this.searchQuery) return this.venues.length;
                    const q = this.searchQuery.toLowerCase();
                    return this.venues.filter(v => 
                        (v.name || '').toLowerCase().includes(q) ||
                        (v.email || '').toLowerCase().includes(q) ||
                        (v.contact_info || '').toLowerCase().includes(q) ||
                        (v.address || '').toLowerCase().includes(q) ||
                        (v.city || '').toLowerCase().includes(q) ||
                        (v.postcode || '').toLowerCase().includes(q) ||
                        (v.country || '').toLowerCase().includes(q) ||
                        (v.description || '').toLowerCase().includes(q)
                    ).length;
                },

                matchesSearch(v) {
                    if (!this.searchQuery) return true;
                    const q = this.searchQuery.toLowerCase();
                    return (v.name || '').toLowerCase().includes(q) ||
                           (v.email || '').toLowerCase().includes(q) ||
                           (v.contact_info || '').toLowerCase().includes(q) ||
                           (v.address || '').toLowerCase().includes(q) ||
                           (v.city || '').toLowerCase().includes(q) ||
                           (v.postcode || '').toLowerCase().includes(q) ||
                           (v.country || '').toLowerCase().includes(q) ||
                           (v.description || '').toLowerCase().includes(q);
                }
            }
        }
    </script>
</div>
@endsection
