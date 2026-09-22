@extends('layouts.admin')

@section('title', 'Sub Categories | Kingdom Admin')

@section('content')
<div x-data="subCategoryManager(@js($subCategories), @js($parentCategories))" @keydown.escape.window="modalOpen = false; quickCategoryModalOpen = false" class="flex flex-col gap-6 h-full min-h-0">

    <!-- Header -->
    <div class="dashboard-header flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Sub Categories</h1>
            <p class="text-slate-500 text-sm mt-1">Refine job classifications with specific sub-groups</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Status Filter -->
            <div class="flex items-center bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <button @click="statusFilter = ''" 
                        class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wide transition-all"
                        :class="statusFilter === '' ? 'bg-kingdom-navy text-white' : 'text-slate-500 hover:bg-slate-50'">All</button>
                <button @click="statusFilter = 'active'" 
                        class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wide transition-all border-l border-slate-200"
                        :class="statusFilter === 'active' ? 'bg-emerald-500 text-white' : 'text-slate-500 hover:bg-slate-50'">Active</button>
                <button @click="statusFilter = 'inactive'" 
                        class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wide transition-all border-l border-slate-200"
                        :class="statusFilter === 'inactive' ? 'bg-rose-500 text-white' : 'text-slate-500 hover:bg-slate-50'">Inactive</button>
            </div>

            <button @click="openCreateModal()" 
                class="bg-[#0F1D33] hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Sub-Category
            </button>
        </div>
    </div>

    @php
        function getCategoryIcon($parent) {
            return match($parent) {
                'SIA Security' => 'shield',
                'Security' => 'shield',
                'Event Management' => 'calendar-days',
                'Events' => 'calendar-days',
                'Hospitality Staff' => 'coffee',
                'Hospitality' => 'coffee',
                'Waiting Staff' => 'utensils',
                'Customer Service' => 'headphones',
                'Accounting' => 'calculator',
                'Cleaning' => 'spray-can',
                'Kitchen Porter' => 'chef-hat',
                'Construction' => 'hard-hat',
                default => 'briefcase',
            };
        }
    @endphp

    <!-- Sub Categories Table -->
    <div class="anim-fade-in-up flex-1 min-h-0 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto flex-1 relative custom-scrollbar">
          <table class="w-full min-w-[1000px] text-left border-collapse">
            <colgroup>
                <col style="width: 80px;">
                <col style="width: 20%;">
                <col style="width: 20%;">
                <col>
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 112px;">
            </colgroup>
            <thead class="bg-[#0f1f3d] sticky top-0 z-50">
              <tr class="text-xs font-semibold tracking-wide uppercase text-white bg-[#0f1f3d]">
                    <th class="py-4 px-4 text-center">ID</th>
                    <th class="px-4 py-4 text-left">Sub-Category</th>
                    <th class="px-4 py-4 text-left">Parent Category</th>
                    <th class="px-4 py-4 text-left">Description</th>
                    <th class="px-4 py-4 text-center">Job Roles</th>
                    <th class="px-4 py-4 text-center">Rate Cards</th>
                    <th class="px-4 py-4 text-center">Status</th>
                    <th class="px-4 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-transparent">
                    <!-- Alpine Dynamic Empty State -->
                    <tr x-show="allSubCategories.filter(s => statusFilter === '' || (statusFilter === 'active' && s.status === 'Active') || (statusFilter === 'inactive' && (s.status === 'Inactive' || s.status === 'Archived'))).length === 0" x-cloak>
                        <td colspan="8" class="px-6 py-12 text-center bg-transparent border-b-2 border-slate-200">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-14 w-14 bg-slate-50 rounded-full flex items-center justify-center mb-3 ring-4 ring-white shadow-sm border border-slate-100">
                                    <i data-lucide="layers" class="w-6 h-6 text-slate-350"></i>
                                </div>
                                <h3 class="text-sm font-bold text-kingdom-navy">No Sub-Categories Found</h3>
                                <p class="text-slate-400 text-xs mt-1">No sub-categories match the selected status.</p>
                            </div>
                        </td>
                    </tr>
                    @forelse($subCategories as $sub)
                    @php
                        $isInactive = $sub->status === 'Archived' || $sub->status === 'Inactive';
                    @endphp
                    <tr x-data="{ 
                        status: '{{ $sub->status }}', 
                        id: {{ $sub->id }},
                        isLoading: false,
                        toggle() {
                            if(this.isLoading) return;
                            this.isLoading = true;
                            const newStatus = this.status === 'Active' ? 'Inactive' : 'Active';
                            const originalStatus = this.status;
                            this.status = newStatus;
 
                            axios.put(`/admin/sub-category/${this.id}/status`, { status: newStatus })
                                .then(() => {
                                    const parentItem = $data.allSubCategories.find(i => i.id === this.id);
                                    if (parentItem) parentItem.status = newStatus;
                                    window.dispatchEvent(new CustomEvent('toast-show', {
                                        detail: { message: `Status updated to ${newStatus}`, type: 'success' }
                                    }));
                                })
                                .catch((err) => {
                                    console.error('Toggle Error:', err);
                                    this.status = originalStatus;
                                    window.dispatchEvent(new CustomEvent('toast-show', {
                                        detail: { message: 'Failed to update status', type: 'error' }
                                    }));
                                })
                                .finally(() => {
                                    this.isLoading = false;
                                });
                        },
                        init() {
                            this.$nextTick(() => lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }));
                        }
                    }" 
                    class="transition-colors duration-150 group cursor-pointer border-b border-slate-100 hover:bg-slate-50 bg-white"
                    :class="(status === 'Archived' || status === 'Inactive') ? 'opacity-60 grayscale-[0.8]' : ''"
                    x-show="statusFilter === '' || (statusFilter === 'active' && status === 'Active') || (statusFilter === 'inactive' && (status === 'Inactive' || status === 'Archived'))"
                    x-cloak>
                        <!-- ID -->
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono text-[10px] font-bold border border-indigo-100 group-hover:bg-[#0f1f3d] group-hover:text-white group-hover:border-[#0f1f3d] transition-colors shadow-sm">{{ $loop->iteration }}</span>
                        </td>
                        <!-- Sub-Category -->
                        <td class="px-4 py-4 overflow-hidden">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-10 w-10 shrink-0 rounded-lg bg-indigo-50/50 flex items-center justify-center border border-indigo-100/50 group-hover:bg-white group-hover:shadow-sm transition-all text-indigo-600 group-hover:text-indigo-700">
                                    @if($sub->parent)
                                         @if($sub->parent->icon)
                                             <img src="{{ asset('media/' . $sub->parent->icon) }}" class="w-5 h-5 object-contain" alt="{{ $sub->parent->name }}">
                                         @else
                                             <i data-lucide="{{ getCategoryIcon($sub->parent->name) }}" class="w-5 h-5 transition-colors"></i>
                                         @endif
                                    @else
                                         <i data-lucide="{{ getCategoryIcon($sub->parent_category) }}" class="w-5 h-5 transition-colors"></i>
                                    @endif
                                </div>
                                <span class="font-bold text-sm text-slate-800 group-hover:text-indigo-600 transition-colors truncate" title="{{ $sub->name }}">{{ $sub->name }}</span>
                            </div>
                        </td>
                        <!-- Parent Category -->
                        <td class="px-4 py-4">
                            @if($sub->parent)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold border {{ ($sub->parent->status === 'Inactive' || $sub->parent->status === 'Archived') ? 'bg-slate-100 text-slate-500 border-slate-200 line-through decoration-slate-400' : 'bg-slate-50 text-slate-600 border-slate-100' }}">
                                    {{ $sub->parent_category }}
                                     @if($sub->parent->status === 'Inactive' || $sub->parent->status === 'Archived')
                                         <span class="text-[10px] text-orange-500 font-bold no-underline inline-block">(Inactive)</span>
                                     @endif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-500 border border-red-100">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $sub->parent_category }} (Deleted)
                                </span>
                            @endif
                        </td>
                        <!-- Description -->
                        <td class="px-4 py-4 text-xs font-medium text-slate-500 truncate">
                            {{ $sub->description ?? '-' }}
                        </td>
                        <!-- Roles -->
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-1.5 text-[10px] font-bold text-slate-600 bg-slate-100 rounded border border-slate-200 group-hover:scale-110 transition-transform">
                                {{ $sub->roles_count ?? 0 }}
                             </span>
                        </td>
                        <!-- Rate Cards Count -->
                        <td class="px-4 py-4 text-center">
                            @if(($sub->rate_cards_count ?? 0) > 0)
                            <span class="inline-flex items-center gap-1.5 justify-center h-6 min-w-[1.5rem] px-2.5 text-[10px] font-bold text-kingdom-gold bg-kingdom-gold/10 rounded border border-kingdom-gold/20 group-hover:scale-110 transition-transform cursor-help shadow-sm" title="{{ implode(', ', $sub->linked_rate_cards ?? []) }}">
                                <i data-lucide="credit-card" class="w-3 h-3"></i>
                                {{ $sub->rate_cards_count }}
                            </span>
                            @else
                            <span class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-1.5 text-[10px] font-bold text-slate-400 bg-slate-50 rounded border border-slate-100">
                                0
                            </span>
                            @endif
                        </td>
                        <!-- Status -->
                        <td class="px-4 py-4 text-center">
                           <button @click.stop.prevent="toggle()" 
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold border border-opacity-20 uppercase tracking-wide transition-all hover:scale-105 active:scale-95 cursor-pointer shadow-sm"
                               :class="status === 'Active' ? 'bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200'"
                               :disabled="isLoading"
                           >
                               <span x-show="status === 'Active'" style="display: none">
                                   <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                               </span>
                               <span x-show="status !== 'Active'" style="display: none">
                                   <i data-lucide="x-circle" class="w-3 h-3"></i>
                               </span>
                               <span x-text="status === 'Archived' ? 'Inactive' : status"></span>
                           </button>
                        </td>
                        <!-- Actions -->
                        <td class="px-4 py-4 text-center">
                             <div class="flex items-center justify-center gap-1 transition-opacity">
                                <button @click.stop="openEditModal({ ...{{ json_encode($sub) }}, status: status })" class="p-1 rounded text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button @click.stop="
                                            const count = {{ $sub->rate_cards_count ?? 0 }};
                                            const names = {{ json_encode($sub->linked_rate_cards ?? []) }};
                                            let msg = 'Are you sure you want to delete this sub-category? This action cannot be undone.';
                                            if (count > 0) {
                                                msg = `Warning: This sub-category has ${count} linked rate card${count > 1 ? 's' : ''}: ${names.join(', ')}. The server will block deletion. ` + msg;
                                            }
                                            $dispatch('open-confirm-modal', { 
                                                title: 'Delete Sub-Category', 
                                                message: msg, 
                                                onConfirm: () => submitDeleteForm('{{ route('admin.sub-category.destroy', $sub->id) }}')
                                            });
                                        " 
                                        class="p-1 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center bg-transparent border-b-2 border-slate-200">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-14 w-14 bg-slate-50 rounded-full flex items-center justify-center mb-3 ring-4 ring-white shadow-sm border border-slate-100">
                                    <i data-lucide="layers" class="w-6 h-6 text-slate-300"></i>
                                </div>
                                <h3 class="text-sm font-bold text-kingdom-navy">No Sub-Categories Found</h3>
                                <p class="text-slate-400 text-xs mt-1">Get started by creating your first classification.</p>
                                <button @click="openCreateModal()" class="mt-3 text-kingdom-gold font-bold text-xs hover:underline">
                                    Create Now
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
         </div>
    </div>

    <!-- Create/Edit Sub Category Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="modalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="modalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="modalOpen"
                 @click.stop
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-md transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                
                <div class="px-6 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                    <h3 x-text="isEditing ? 'Edit Sub-Category' : 'Add Sub-Category'" class="font-bold text-xl text-kingdom-navy"></h3>
                    <button @click="modalOpen = false" class="bg-white p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form :action="formAction" method="POST" class="px-6 pb-6 pt-2 flex flex-col gap-4">
                    @csrf
                    <div x-html="methodField" class="hidden"></div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-kingdom-navy pl-1">Parent Category <span class="text-red-500">*</span></label>
                        <div class="relative group" x-data="{ open: false }">
                            <input type="hidden" name="parent_category" x-model="formData.parent_category">
                            
                            <button type="button" @click="open = !open" @click.outside="open = false"
                                class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-left flex items-center justify-between transition-all outline-none group hover:border-kingdom-gold/30">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="folder" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                </div>
                                <span x-text="formData.parent_category || 'Select Category'" :class="formData.parent_category ? 'text-kingdom-navy' : 'text-slate-400'"></span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                            </button>
        
                            <div x-show="open" 
                                 x-collapse
                                 x-cloak
                                 class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5 max-h-72 flex flex-col"
                                 style="display: none;">
                                 <div class="overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-slate-100 [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-slate-400 max-h-56">
                                     <template x-for="cat in parentCategories" :key="cat.id || cat.name">
                                        <button type="button" @click="formData.parent_category = cat.name; open = false" 
                                            class="w-full text-left px-4 py-2.5 text-sm font-bold flex items-center justify-between group transition-colors border-b border-slate-50 last:border-0"
                                            :class="formData.parent_category === cat.name ? 'bg-kingdom-gold/5 text-kingdom-navy' : 'text-slate-600 hover:bg-slate-50 hover:text-kingdom-navy'">
                                            <span x-text="cat.name"></span>
                                            <i data-lucide="check" class="w-4 h-4 text-kingdom-gold opacity-0 group-hover:opacity-100 transition-opacity" :class="formData.parent_category === cat.name ? 'opacity-100' : ''"></i>
                                        </button>
                                    </template>
                                 </div>
                                 <div class="px-2 py-2 border-t border-slate-100 bg-slate-50 shrink-0 shadow-[0_-4px_10px_-4px_rgba(0,0,0,0.05)]">
                                     <button type="button" @click="openQuickCategoryModal(); open = false" 
                                         class="w-full py-2.5 rounded-lg bg-white border border-slate-200 text-kingdom-gold font-bold text-sm hover:border-kingdom-gold hover:bg-kingdom-gold/5 hover:shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95 duration-150">
                                         <i data-lucide="plus-circle" class="w-4 h-4"></i> Create New Category
                                     </button>
                                 </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5" x-data="{ open: false }">
                        <label class="block text-sm font-bold text-kingdom-navy pl-1">Sub-Category Name <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="tag" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                            </div>
                            <input type="text" name="name" x-model="formData.name"
                                class="w-full pl-11 pr-10 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none"
                                placeholder="e.g. Door Supervision" required>

                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button type="button" @click="open = !open" @click.outside="open = false" class="p-1 rounded-full text-slate-400 hover:text-kingdom-navy hover:bg-slate-100 transition-colors">
                                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                </button>
                            </div>
                            
                            <div x-show="open" x-transition x-cloak class="absolute z-50 w-full mt-1 bg-white border border-slate-100 rounded-xl shadow-xl max-h-60 overflow-auto">
                                <ul class="py-1">
                                    <template x-for="sub in allSubCategories" :key="sub.id">
                                        <li>
                                            <button type="button" @click="openEditModal(sub); open = false" 
                                            class="w-full text-left px-4 py-2 hover:bg-slate-50 text-sm text-kingdom-navy font-medium group flex items-center justify-between">
                                                <span x-text="sub.name"></span>
                                                <i x-show="formData.id === sub.id" data-lucide="check" class="w-3 h-3 text-kingdom-gold"></i>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-kingdom-navy pl-1">Description</label>
                        <div class="relative group">
                            <div class="absolute top-3.5 left-0 pl-4 pointer-events-none">
                                <i data-lucide="align-left" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                            </div>
                            <textarea name="description" x-model="formData.description"
                                class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-medium text-kingdom-navy transition-all outline-none min-h-[100px] resize-none" 
                                placeholder="Brief description of the sub-category..."></textarea>
                        </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-kingdom-navy pl-1">Status <span class="text-red-500">*</span></label>
                         <div class="relative group" x-data="{ open: false }">
                             <input type="hidden" name="status" x-model="formData.status">
                             <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                 <div class="relative w-5 h-5 flex items-center justify-center">
                                     <i data-lucide="activity" x-show="formData.status === 'Active'" class="w-5 h-5 text-green-500 transition-colors absolute inset-0"></i>
                                     <i data-lucide="archive" x-show="formData.status !== 'Active'" class="w-5 h-5 text-orange-500 transition-colors absolute inset-0" style="display: none;"></i>
                                 </div>
                             </div>
                             
                             <button type="button" @click="open = !open" @click.outside="open = false"
                                 class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-left flex items-center justify-between transition-all outline-none group hover:border-kingdom-gold/30"
                                 :class="formData.status === 'Active' ? 'text-green-600 bg-green-50/50' : 'text-orange-600 bg-orange-50/50'">
                                 <span x-text="formData.status"></span>
                                 <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                             </button>

                             <div x-show="open" 
                                  x-collapse
                                  x-cloak
                                  class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-50 overflow-hidden py-1">
                                 <button type="button" @click="formData.status = 'Active'; open = false" 
                                     class="w-full text-left px-4 py-2.5 text-sm font-bold flex items-center justify-between group transition-colors border-b border-slate-50 last:border-0 hover:bg-green-50 text-slate-600 hover:text-green-700">
                                     <span>Active</span>
                                     <i data-lucide="check" class="w-4 h-4 text-green-600 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" :class="formData.status === 'Active' ? 'opacity-100' : ''"></i>
                                 </button>

                                 <button type="button" @click="formData.status = 'Inactive'; open = false" 
                                     class="w-full text-left px-4 py-2.5 text-sm font-bold flex items-center justify-between group transition-colors hover:bg-slate-100 text-slate-600 hover:text-slate-700">
                                     <span>Inactive</span>
                                     <i data-lucide="check" class="w-4 h-4 text-slate-600 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" :class="formData.status === 'Inactive' ? 'opacity-100' : ''"></i>
                                 </button>
                             </div>
                         </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex gap-4">
                        <button type="button" @click="modalOpen = false" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all text-sm">
                             Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 flex items-center justify-center gap-2 text-sm group/btn">
                            <span x-text="isEditing ? 'Update Sub-Category' : 'Create Sub-Category'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 transition-transform duration-200 group-hover/btn:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Quick Create Parent Category Modal -->
    <div x-show="quickCategoryModalOpen" class="fixed inset-0 z-[110] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <!-- Backdrop -->
        <div x-show="quickCategoryModalOpen"
             x-transition.opacity
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="quickCategoryModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center" @click="quickCategoryModalOpen = false">
            <div x-show="quickCategoryModalOpen"
                 @click.stop
                 x-transition
                 class="relative w-full max-w-lg transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                    <h3 class="font-bold text-lg text-kingdom-navy">Quick Add Category</h3>
                    <button type="button" @click="quickCategoryModalOpen = false" class="bg-white p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="px-6 py-5">
                    <form @submit.prevent="submitQuickCategory" enctype="multipart/form-data" class="flex flex-col gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Category Name <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="type" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                </div>
                                <input type="text" x-model="quickCategoryForm.name" required
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                    placeholder="e.g. VIP Protection">
                            </div>
                        </div>

                        <div class="space-y-1.5 pt-1">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Description</label>
                            <div class="relative group">
                                <div class="absolute top-3.5 left-0 pl-4 pointer-events-none">
                                    <i data-lucide="align-left" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                </div>
                                <textarea x-model="quickCategoryForm.description"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-medium text-kingdom-navy transition-all outline-none min-h-[100px] resize-none placeholder:text-slate-400" 
                                    placeholder="Brief overview..."></textarea>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-2 mb-2">
                             <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Status <span class="text-red-500">*</span></label>
                                <div class="relative group" x-data="{ open: false }">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                        <div class="relative w-5 h-5 flex items-center justify-center">
                                            <i data-lucide="activity" x-show="quickCategoryForm.status === 'Active'" class="w-5 h-5 text-green-500 transition-colors absolute inset-0"></i>
                                            <i data-lucide="archive" x-show="quickCategoryForm.status !== 'Active'" class="w-5 h-5 text-orange-500 transition-colors absolute inset-0" style="display: none;"></i>
                                        </div>
                                    </div>
                                    
                                    <button type="button" @click="open = !open" @click.outside="open = false"
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-left flex items-center justify-between transition-all outline-none group hover:border-kingdom-gold/30"
                                        :class="quickCategoryForm.status === 'Active' ? 'text-green-600 bg-green-50/50' : 'text-orange-600 bg-orange-50/50'">
                                        <span x-text="quickCategoryForm.status"></span>
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                    </button>

                                    <div x-show="open" 
                                         x-collapse
                                         x-cloak
                                         class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-50 overflow-hidden py-1">
                                         <button type="button" @click="quickCategoryForm.status = 'Active'; open = false" 
                                             class="w-full text-left px-4 py-2.5 text-sm font-bold flex items-center justify-between group transition-colors hover:bg-slate-50 text-slate-600 hover:text-kingdom-navy">
                                             <span>Active</span>
                                             <i data-lucide="check" class="w-4 h-4 text-green-500 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" :class="quickCategoryForm.status === 'Active' ? 'opacity-100' : ''"></i>
                                         </button>
                                         <button type="button" @click="quickCategoryForm.status = 'Inactive'; open = false" 
                                             class="w-full text-left px-4 py-2.5 text-sm font-bold flex items-center justify-between group transition-colors hover:bg-slate-50 text-slate-600 hover:text-kingdom-navy">
                                             <span>Inactive</span>
                                             <i data-lucide="check" class="w-4 h-4 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" :class="quickCategoryForm.status === 'Inactive' ? 'opacity-100' : ''"></i>
                                         </button>
                                         <button type="button" @click="quickCategoryForm.status = 'Archived'; open = false" 
                                             class="w-full text-left px-4 py-2.5 text-sm font-bold flex items-center justify-between group transition-colors hover:bg-slate-50 text-slate-600 hover:text-kingdom-navy">
                                             <span>Archived</span>
                                             <i data-lucide="check" class="w-4 h-4 text-orange-500 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" :class="quickCategoryForm.status === 'Archived' ? 'opacity-100' : ''"></i>
                                         </button>
                                    </div>
                                </div>
                             </div>
                             
                             <div class="space-y-1.5" x-data="{ fileName: '' }">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Icon <span class="text-[10px] text-slate-400 font-normal uppercase">(Optional)</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                        <i data-lucide="image" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <input type="file" x-ref="quickCatIcon" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                      @change="fileName = $event.target.files[0].name">
                                    <div class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 group-hover:bg-white focus-within:bg-white focus-within:border-kingdom-gold focus-within:ring-4 focus-within:ring-kingdom-gold/10 flex items-center justify-between transition-all">
                                        <span class="text-sm font-medium text-slate-500 truncate max-w-[150px]" x-text="fileName || 'No file chosen'"></span>
                                        <span class="bg-white border border-slate-200 text-kingdom-gold-dark text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm group-hover:border-kingdom-gold/30 transition-colors">Choose File</span>
                                    </div>
                                </div>
                             </div>
                        </div>

                        <div class="flex gap-4 mt-4 pt-4 border-t border-slate-100">
                            <button type="button" @click="quickCategoryModalOpen = false" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all text-sm">
                                Cancel
                            </button>
                            <button type="submit" :disabled="isQuickSubmitting" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg flex items-center justify-center gap-2 text-sm disabled:opacity-70 disabled:grayscale active:scale-95 duration-150">
                                <span x-text="isQuickSubmitting ? 'Saving...' : 'Create & Select'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    window.subCategoryManager = function subCategoryManager(subCategories, parentCategories) {
        return {
            modalOpen: false,
            isEditing: false,
            statusFilter: '',
            allSubCategories: subCategories,
            parentCategories: parentCategories,
            init() {
                this.$watch('modalOpen', val => document.body.style.overflow = val ? 'hidden' : '');
                this.$watch('statusFilter', () => this.$nextTick(() => { if (window.lucide) window.lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }); }));
            },
            formData: {
                id: null,
                name: '',
                description: '',
                parent_category: ''
            },
            formAction: '',
            methodField: '',

            // Quick Category Context
            quickCategoryModalOpen: false,
            isQuickSubmitting: false,
            quickCategoryForm: {
                name: '',
                description: '',
                status: 'Active'
            },

            openCreateModal() {
                this.isEditing = false;
                this.formData = { id: null, name: '', description: '', parent_category: '', status: 'Active' };
                this.formAction = "{{ route('admin.sub-category.store') }}";
                this.methodField = '';
                this.modalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            openEditModal(sub) {
                this.isEditing = true;
                this.formData = {
                    id: sub.id,
                    name: sub.name,
                    description: sub.description || '',
                    parent_category: sub.parent_category,
                    status: sub.status === 'Archived' ? 'Inactive' : sub.status
                };
                this.formAction = `/admin/sub-category/${sub.id}`;
                this.methodField = '<input type="hidden" name="_method" value="PUT">';
                this.modalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            openQuickCategoryModal() {
                this.quickCategoryForm = { name: '', description: '', status: 'Active' };
                if (this.$refs.quickCatIcon) this.$refs.quickCatIcon.value = '';
                this.quickCategoryModalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            async submitQuickCategory() {
                if (this.isQuickSubmitting || !this.quickCategoryForm.name) return;
                this.isQuickSubmitting = true;

                const formDataToSend = new FormData();
                formDataToSend.append('name', this.quickCategoryForm.name);
                formDataToSend.append('description', this.quickCategoryForm.description);
                formDataToSend.append('status', this.quickCategoryForm.status);
                formDataToSend.append('_token', document.querySelector('meta[name=csrf-token]').content);
                
                if (this.$refs.quickCatIcon.files.length > 0) {
                    formDataToSend.append('icon', this.$refs.quickCatIcon.files[0]);
                }

                try {
                    const response = await fetch("{{ route('admin.job-category.store') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formDataToSend
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.parentCategories.push(data.category);
                        this.formData.parent_category = data.category.name; // Auto-select it
                        this.quickCategoryModalOpen = false;
                        
                        // Show success toast via Alpine/global dispatch event (adjust if you use a different toast system)
                        window.dispatchEvent(new CustomEvent('toast-show', {
                            detail: { message: `Category ${data.category.name} created!`, type: 'success' }
                        }));
                    }
                } catch (error) {
                    console.error('Error creating category:', error);
                    window.dispatchEvent(new CustomEvent('toast-show', {
                        detail: { message: `Failed to create category`, type: 'error' }
                    }));
                } finally {
                    this.isQuickSubmitting = false;
                }
            }
        }
    }


</script>
@endsection
