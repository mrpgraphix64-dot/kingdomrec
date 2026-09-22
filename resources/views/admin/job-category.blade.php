@extends('layouts.admin')

@section('title', 'Categories | Kingdom Admin')

@section('content')
<div x-data="categoryManager(@js($categories), @js($subCategories), @js($parentCategories))" 
     @keydown.escape.window="modalOpen = false; subModalOpen = false; quickCategoryModalOpen = false" 
     class="flex flex-col gap-3">

  <!-- Header -->
  <div class="dashboard-header flex flex-col sm:flex-row sm:items-center justify-between gap-2 shrink-0">
    <div>
      <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Categories</h1>
      <p class="text-slate-500 text-xs">Manage job categories & sub-categories</p>
    </div>
    <div class="flex items-center gap-2">
        <button @click="openSubCreateModal()" 
            class="bg-white border border-slate-200 hover:border-kingdom-gold text-kingdom-navy px-3 py-2 rounded-lg font-bold text-xs shadow-sm transition-all flex items-center gap-1.5 hover:shadow-md active:scale-95">
            <i data-lucide="layers" class="w-3.5 h-3.5 text-kingdom-gold"></i>
            <span class="hidden sm:inline">Add</span> Sub-Category
        </button>
        <button @click="openCreateModal()" 
            class="bg-[#0F1D33] hover:bg-slate-800 text-white px-3 py-2 rounded-lg font-bold text-xs shadow-sm transition-all flex items-center gap-1.5">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span class="hidden sm:inline">Add</span> Category
        </button>
    </div>
  </div>

  <!-- Stats Overview -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4 shrink-0">
    <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
        <div class="h-10 w-10 shrink-0 rounded-xl bg-kingdom-navy/5 flex items-center justify-center shadow-inner">
            <i data-lucide="briefcase" class="w-5 h-5 text-kingdom-navy"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-0.5">Categories</h4>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-black text-kingdom-navy leading-none" x-text="allCategories.length"></span>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 mb-0.5">
                    <span x-text="allCategories.filter(c => c.status === 'Active').length"></span> Active
                </span>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
        <div class="h-10 w-10 shrink-0 rounded-xl bg-kingdom-gold/10 flex items-center justify-center shadow-inner">
            <i data-lucide="layers" class="w-5 h-5 text-kingdom-gold-dark"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-0.5">Sub-Categories</h4>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-black text-kingdom-gold-dark leading-none" x-text="allSubCategories.length"></span>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 mb-0.5">
                    <span x-text="allSubCategories.filter(s => s.status === 'Active').length"></span> Active
                </span>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
        <div class="h-10 w-10 shrink-0 rounded-xl bg-blue-50 flex items-center justify-center shadow-inner text-blue-500">
            <i data-lucide="credit-card" class="w-5 h-5"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-0.5">Rate Cards</h4>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-black text-blue-600 leading-none" x-text="allSubCategories.reduce((sum, s) => sum + (s.rate_cards_count || 0), 0)"></span>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-0.5">Linked Rates</span>
            </div>
        </div>
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <div class="flex items-center gap-2 shrink-0">
    <div class="relative flex-1 max-w-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
        </div>
        <input type="text" x-model="searchQuery" placeholder="Search..." 
               class="w-full pl-9 pr-8 py-2 rounded-lg bg-white border border-slate-200 focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 text-xs font-medium text-kingdom-navy transition-all outline-none placeholder:text-slate-400">
        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>
    <!-- Status Filter -->
    <div class="flex items-center bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
        <button @click="statusFilter = ''" 
                class="px-2.5 py-2 text-[10px] font-bold uppercase tracking-wide transition-all"
                :class="statusFilter === '' ? 'bg-kingdom-navy text-white' : 'text-slate-500 hover:bg-slate-50'">All</button>
        <button @click="statusFilter = 'active'" 
                class="px-2.5 py-2 text-[10px] font-bold uppercase tracking-wide transition-all border-l border-slate-200"
                :class="statusFilter === 'active' ? 'bg-emerald-500 text-white' : 'text-slate-500 hover:bg-slate-50'">Active</button>
        <button @click="statusFilter = 'inactive'" 
                class="px-2.5 py-2 text-[10px] font-bold uppercase tracking-wide transition-all border-l border-slate-200"
                :class="statusFilter === 'inactive' ? 'bg-rose-500 text-white' : 'text-slate-500 hover:bg-slate-50'">Inactive</button>
    </div>

    <button @click="expandAll = !expandAll" 
            class="px-2.5 py-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-1.5 shadow-sm">
        <i data-lucide="chevrons-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="expandAll ? 'rotate-180' : ''"></i>
        <span x-text="expandAll ? 'Collapse' : 'Expand'"></span>
    </button>
  </div>

  <hr class="border-slate-200 dark:border-slate-800 my-2 shrink-0">

  <!-- Categories Grid List -->
  <div class="anim-fade-in-up grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pb-4 items-start">
    
    <template x-for="(cat, catIdx) in filteredCategories" :key="cat.id">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-1 group/card flex flex-col relative"
           x-data="{ 
               expanded: expandAll || searchQuery.length > 0, 
               status: cat.status, 
               isLoading: false,
               toggleStatus() {
                   if(this.isLoading) return;
                   this.isLoading = true;
                   const newStatus = this.status === 'Active' ? 'Inactive' : 'Active';
                   const orig = this.status;
                   this.status = newStatus;
                   
                   fetch(`/admin/job-category/${cat.id}/status`, {
                       method: 'PUT',
                       headers: {
                           'Content-Type': 'application/json',
                           'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                       },
                       body: JSON.stringify({ status: newStatus })
                   })
                   .then(r => r.json())
                   .then(data => {
                       if (data.success) {
                           cat.status = newStatus;
                           if (newStatus === 'Inactive') {
                               $data.allSubCategories.forEach(s => {
                                   if (s.parent_category === cat.name && s.status === 'Active') {
                                       s.status = 'Inactive';
                                   }
                               });
                           }
                       } else { this.status = orig; }
                   })
                   .catch(() => { this.status = orig; })
                   .finally(() => { this.isLoading = false; });
               }
           }"
           x-effect="expanded = expandAll || searchQuery.length > 0"
           :class="(status === 'Archived' || status === 'Inactive') ? 'opacity-70 hover:opacity-100 grayscale-[0.2]' : ''">
        
        <!-- Toggle Switch Top Right -->
        <div class="absolute top-4 right-4 z-10" @click.stop>
            <button @click="toggleStatus()" :disabled="isLoading"
                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                    :class="status === 'Active' ? 'bg-emerald-500' : 'bg-slate-300'"
                    role="switch" :aria-checked="status === 'Active'" title="Toggle Status">
                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                      :class="status === 'Active' ? 'translate-x-4' : 'translate-x-0'"></span>
            </button>
        </div>

        <!-- Main Card Body -->
        <div class="p-5 flex flex-col gap-3">
            <!-- Header: Icon & Title -->
            <div class="flex items-start gap-3 pr-10">
                <div class="h-10 w-10 shrink-0 rounded-xl bg-slate-50 text-kingdom-gold-dark flex items-center justify-center ring-1 ring-slate-200 group-hover/card:scale-110 transition-transform shadow-sm">
                    <template x-if="cat.icon">
                        <img :src="'/media/' + cat.icon" class="w-5 h-5 object-contain" alt="Icon">
                    </template>
                    <template x-if="!cat.icon">
                        <i data-lucide="briefcase" class="w-5 h-5"></i>
                    </template>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 class="text-base font-bold text-kingdom-navy group-hover/card:text-kingdom-gold transition-colors truncate" x-text="cat.name"></h3>
                    <span class="inline-flex mt-0.5 text-[9px] font-bold px-1.5 py-0.5 rounded border"
                          :class="(status === 'Active') ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'"
                          x-text="status === 'Archived' ? 'Inactive' : status"></span>
                </div>
            </div>
            
            <!-- Description -->
            <p class="text-xs text-slate-500 line-clamp-2 mt-1 min-h-[32px]" x-text="cat.description || 'No description provided.'"></p>
            
            <!-- Quick Meta & Actions -->
            <div class="flex items-center justify-between mt-2 pt-4 border-t border-slate-100">
                <button @click="expanded = !expanded" class="flex items-center gap-1.5 px-2 py-1 -ml-2 rounded-lg hover:bg-slate-50 text-xs font-bold transition-colors" :class="expanded ? 'text-kingdom-navy' : 'text-slate-500'">
                    <div class="transition-transform duration-200" :class="expanded ? 'rotate-90' : ''">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </div>
                    <span class="flex items-center gap-1">
                        <i data-lucide="layers" class="w-3.5 h-3.5" :class="expanded ? 'text-kingdom-gold' : 'text-slate-400'"></i>
                        <span x-text="getSubsForCategory(cat.name).length"></span> Subcategories
                    </span>
                </button>
                
                <div class="flex items-center gap-1 shrink-0">
                    <button @click="openEditModal(cat)" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100" title="Edit Category">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </button>
                    <button @click="openDeleteModal('category', cat)" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all border border-transparent hover:border-red-100" title="Delete Category">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Accordion Sub-Categories -->
        <div x-show="expanded" x-collapse x-cloak>
            <div class="bg-slate-50/80 border-t border-slate-100 p-3 flex flex-col gap-1.5 shadow-inner">
                <!-- Sub-category list -->
                <template x-if="getSubsForCategory(cat.name).length > 0">
                    <div class="space-y-1.5">
                        <template x-for="(sub, subIdx) in getSubsForCategory(cat.name)" :key="sub.id">
                            <div x-data="{ 
                                subStatus: sub.status, 
                                subLoading: false,
                                toggleSubStatus() {
                                    if(this.subLoading) return;
                                    this.subLoading = true;
                                    const newStatus = this.subStatus === 'Active' ? 'Inactive' : 'Active';
                                    const orig = this.subStatus;
                                    this.subStatus = newStatus;
                                    
                                    fetch(`/admin/sub-category/${sub.id}/status`, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        },
                                        body: JSON.stringify({ status: newStatus })
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.success) {
                                            sub.status = newStatus;
                                        } else { this.subStatus = orig; }
                                    })
                                    .catch(() => { this.subStatus = orig; })
                                    .finally(() => { this.subLoading = false; });
                                }
                            }"
                            x-effect="if(sub.status !== subStatus) subStatus = sub.status"
                            class="flex items-center gap-2 px-2.5 py-2 bg-white rounded-lg border border-slate-200/60 shadow-sm group hover:border-slate-300 transition-all"
                            :class="(subStatus === 'Archived' || subStatus === 'Inactive') ? 'opacity-60 hover:opacity-100' : ''">
                                
                                <div class="w-5 h-5 shrink-0 rounded bg-slate-50 flex items-center justify-center border border-slate-100 text-slate-400 group-hover:text-kingdom-gold transition-colors">
                                    <i data-lucide="tag" class="w-3 h-3"></i>
                                </div>
                                
                                <div class="flex-1 min-w-0 pr-1">
                                    <h4 class="text-xs font-bold text-kingdom-navy group-hover:text-kingdom-gold transition-colors truncate" x-text="sub.name"></h4>
                                </div>
                                
                                <!-- Rate cards badge -->
                                <template x-if="(sub.rate_cards_count || 0) > 0">
                                    <span class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-px text-[9px] font-bold text-kingdom-gold bg-kingdom-gold/10 rounded border border-kingdom-gold/20" title="Rate Cards">
                                        <i data-lucide="credit-card" class="w-2.5 h-2.5"></i>
                                        <span x-text="sub.rate_cards_count"></span>
                                    </span>
                                </template>
                                
                                <!-- Status badge -->
                                <button @click="toggleSubStatus()" :disabled="subLoading" 
                                        class="shrink-0 relative inline-flex h-3 w-6 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                                        :class="subStatus === 'Active' ? 'bg-emerald-500' : 'bg-slate-300'"
                                        title="Toggle Status">
                                    <span class="pointer-events-none inline-block h-2 w-2 transform rounded-full bg-white shadow transition-transform duration-200 ease-in-out"
                                          :class="subStatus === 'Active' ? 'translate-x-3' : 'translate-x-0'"></span>
                                </button>
                                
                                <div class="shrink-0 flex items-center ml-1 border-l border-slate-100 pl-1">
                                    <button @click="openSubEditModal(sub)" class="p-1 rounded text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button @click="openDeleteModal('sub', sub)" class="p-1 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                
                <!-- Add Sub Button -->
                <button @click="openSubCreateModal(cat.name)" 
                        class="w-full mt-1 py-1.5 rounded-lg border border-dashed border-slate-300 bg-white/50 text-slate-500 hover:text-kingdom-gold hover:border-kingdom-gold text-xs font-bold flex items-center justify-center gap-1.5 transition-all hover:bg-white shadow-sm active:scale-[0.99] group/add">
                    <i data-lucide="plus" class="w-3.5 h-3.5 text-slate-400 group-hover/add:text-kingdom-gold"></i>
                    Add Subcategory
                </button>
            </div>
        </div>
      </div>
    </template>
    
    <!-- Empty State -->
    <template x-if="filteredCategories.length === 0">
        <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-3xl border border-slate-200 border-dashed shadow-sm">
            <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 ring-8 ring-slate-50/50 shadow-sm border border-slate-100">
                <i data-lucide="layers" class="w-8 h-8 text-slate-300"></i>
            </div>
            <h3 class="text-base font-bold text-kingdom-navy" x-text="searchQuery ? 'No matching categories' : 'No categories found'"></h3>
            <p class="text-slate-400 text-sm mt-1 max-w-sm text-center" x-text="searchQuery ? 'Try adjusting your search terms.' : 'Get started by creating your first job category.'"></p>
            <button x-show="searchQuery" @click="searchQuery = ''" class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-xs transition-colors">Clear Search</button>
            <button x-show="!searchQuery" @click="openCreateModal()" class="mt-4 px-4 py-2 bg-kingdom-navy hover:bg-slate-800 text-white rounded-lg font-bold text-xs shadow-md transition-colors">Add Category</button>
        </div>
    </template>
  </div>

  <!-- ════════════════════════════════════ -->
  <!-- CATEGORY CREATE/EDIT MODAL          -->
  <!-- ════════════════════════════════════ -->
  <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
      <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalOpen = false"></div>
      <div class="flex min-h-full items-center justify-center p-4 text-center">
          <div x-show="modalOpen" x-transition @click.stop class="relative w-full max-w-lg transform rounded-2xl bg-white text-left shadow-2xl transition-all">
              <div class="px-6 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                  <h3 x-text="isEditing ? 'Edit Category' : 'Add New Category'" class="font-bold text-lg text-kingdom-navy"></h3>
                  <button @click="modalOpen = false" class="bg-white p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                      <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
              </div>
              <div class="px-6 pb-6 pt-2">
                  <form :action="formAction" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                      @csrf
                      <div x-html="methodField" class="hidden"></div>
                      
                      <div class="space-y-1.5">
                          <label class="block text-sm font-bold text-kingdom-navy pl-1">Category Name <span class="text-red-500">*</span></label>
                          <div class="relative group">
                              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                  <i data-lucide="type" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                              </div>
                              <input type="text" name="name" x-model="formData.name"
                                  class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                  placeholder="e.g. Event Security" required>
                          </div>
                      </div>

                      <div class="space-y-1.5">
                          <label class="block text-sm font-bold text-kingdom-navy pl-1">Description</label>
                          <div class="relative group">
                              <div class="absolute top-3.5 left-0 pl-4 pointer-events-none">
                                  <i data-lucide="align-left" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                              </div>
                              <textarea name="description" x-model="formData.description"
                                  class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-medium text-kingdom-navy transition-all outline-none min-h-[80px] resize-none" 
                                  placeholder="Brief description of the category..."></textarea>
                          </div>
                      </div>

                      <!-- Dynamic Sub-Categories -->
                      <div class="space-y-1.5 pt-3 border-t border-slate-100 mt-2" x-show="formData.sub_categories">
                           <div class="flex items-center justify-between">
                               <label class="block text-sm font-bold text-kingdom-navy pl-1">Sub-Categories <span class="text-xs text-slate-400 font-normal ml-1">(Optional)</span></label>
                               <button type="button" @click="formData.sub_categories.push('')" class="text-xs font-bold text-kingdom-gold hover:text-kingdom-gold-dark flex items-center gap-1 transition-colors bg-kingdom-gold/10 hover:bg-kingdom-gold/20 px-2.5 py-1.5 rounded-lg active:scale-95 duration-150">
                                   <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Row
                               </button>
                           </div>
                           <template x-for="(sub, index) in formData.sub_categories" :key="index">
                               <div class="relative group mt-2 flex items-center gap-2">
                                   <div class="relative flex-1">
                                       <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                           <i data-lucide="tag" class="w-4 h-4 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                       </div>
                                       <input type="text" x-model="formData.sub_categories[index]" :name="'sub_categories[' + index + ']'"
                                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-300 text-sm font-bold text-kingdom-navy transition-all outline-none" 
                                           placeholder="e.g. Door Supervisor, CCTV Operator">
                                   </div>
                                   <button type="button" @click="formData.sub_categories.length > 1 ? formData.sub_categories.splice(index, 1) : formData.sub_categories[0] = ''" 
                                           class="p-3 bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-colors border border-red-100 shadow-sm active:scale-95 duration-150">
                                       <i data-lucide="trash-2" class="w-4 h-4"></i>
                                   </button>
                               </div>
                           </template>
                           <p class="text-[11px] text-slate-500 mt-2 pl-1"><i data-lucide="info" class="w-3 h-3 inline mr-1 text-kingdom-gold"></i>These sub-categories will be auto-created and linked to this category upon saving.</p>
                      </div>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-2">
                           <div class="space-y-1.5" x-data="{ fileName: '' }">
                              <label class="block text-sm font-bold text-kingdom-navy pl-1">Icon (Optional)</label>
                              <div class="relative group">
                                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                      <i data-lucide="image" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                  </div>
                                  <input type="file" name="icon" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    @change="fileName = $event.target.files[0].name">
                                  <div class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 group-hover:bg-white focus-within:bg-white focus-within:border-kingdom-gold focus-within:ring-4 focus-within:ring-kingdom-gold/10 flex items-center justify-between transition-all">
                                      <span class="text-sm font-medium text-slate-500 truncate max-w-[150px]" x-text="fileName || 'No file chosen'"></span>
                                      <span class="bg-white border border-slate-200 text-kingdom-gold-dark text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm group-hover:border-kingdom-gold/30 transition-colors">Choose</span>
                                  </div>
                              </div>
                          </div>

                          <div class="space-y-1.5">
                              <label class="block text-sm font-bold text-kingdom-navy pl-1">Status <span class="text-red-500">*</span></label>
                              <div class="relative group" x-data="{ open: false }">
                                  <input type="hidden" name="status" x-model="formData.status">
                                  <button type="button" @click="open = !open" @click.outside="open = false"
                                      class="w-full pl-4 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-left flex items-center justify-between transition-all outline-none group hover:border-kingdom-gold/30"
                                      :class="formData.status === 'Active' ? 'text-green-600 bg-green-50/50' : 'text-orange-600 bg-orange-50/50'">
                                      <span x-text="formData.status"></span>
                                      <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                  </button>
                                  <div x-show="open" x-collapse x-cloak class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-50 overflow-hidden py-1">
                                      <button type="button" @click="formData.status = 'Active'; open = false" class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-green-50 text-slate-600 hover:text-green-700 flex items-center justify-between">
                                          <span>Active</span>
                                          <i data-lucide="check" class="w-4 h-4 text-green-600 opacity-0" :class="formData.status === 'Active' ? 'opacity-100' : ''"></i>
                                      </button>
                                      <button type="button" @click="formData.status = 'Inactive'; open = false" class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-100 text-slate-600 hover:text-slate-700 flex items-center justify-between">
                                          <span>Inactive</span>
                                          <i data-lucide="check" class="w-4 h-4 text-slate-600 opacity-0" :class="formData.status === 'Inactive' ? 'opacity-100' : ''"></i>
                                      </button>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="flex gap-4 mt-4 pt-4 border-t border-slate-100">
                          <button type="button" @click="modalOpen = false" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all text-sm">
                              Cancel
                          </button>
                          <button type="submit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 flex items-center justify-center gap-2 text-sm group/btn">
                              <span x-text="isEditing ? 'Update Category' : 'Create Category'"></span>
                              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 transition-transform duration-200 group-hover/btn:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                          </button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <!-- ════════════════════════════════════ -->
  <!-- SUB-CATEGORY CREATE/EDIT MODAL      -->
  <!-- ════════════════════════════════════ -->
  <div x-show="subModalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
      <div x-show="subModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="subModalOpen = false"></div>
      <div class="flex min-h-full items-center justify-center p-4 text-center">
          <div x-show="subModalOpen" x-transition @click.stop class="relative w-full max-w-md transform rounded-2xl bg-white text-left shadow-2xl transition-all">
              <div class="px-6 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                  <h3 x-text="isSubEditing ? 'Edit Sub-Category' : 'Add Sub-Category'" class="font-bold text-lg text-kingdom-navy"></h3>
                  <button @click="subModalOpen = false" class="bg-white p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                      <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
              </div>
              <form :action="subFormAction" method="POST" class="px-6 pb-6 pt-2 flex flex-col gap-4">
                  @csrf
                  <div x-html="subMethodField"></div>

                  <div class="space-y-1.5">
                      <label class="block text-sm font-bold text-kingdom-navy pl-1">Parent Category <span class="text-red-500">*</span></label>
                      <div class="relative group" x-data="{ open: false }">
                          <input type="hidden" name="parent_category" x-model="subFormData.parent_category">
                          <button type="button" @click="open = !open" @click.outside="open = false"
                              class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-left flex items-center justify-between transition-all outline-none group hover:border-kingdom-gold/30">
                              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                  <i data-lucide="folder" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                              </div>
                              <span x-text="subFormData.parent_category || 'Select Category'" :class="subFormData.parent_category ? 'text-kingdom-navy' : 'text-slate-400'"></span>
                              <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                          </button>
                          <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5 max-h-56 overflow-y-auto" style="display: none;">
                              <template x-for="pc in parentCategories" :key="pc.id || pc.name">
                                  <button type="button" @click="subFormData.parent_category = pc.name; open = false" 
                                      class="w-full text-left px-4 py-2.5 text-sm font-bold flex items-center justify-between transition-colors border-b border-slate-50 last:border-0"
                                      :class="subFormData.parent_category === pc.name ? 'bg-kingdom-gold/5 text-kingdom-navy' : 'text-slate-600 hover:bg-slate-50 hover:text-kingdom-navy'">
                                      <span x-text="pc.name"></span>
                                      <i data-lucide="check" class="w-4 h-4 text-kingdom-gold" :class="subFormData.parent_category === pc.name ? 'opacity-100' : 'opacity-0'"></i>
                                  </button>
                              </template>
                          </div>
                      </div>
                  </div>

                  <div class="space-y-1.5">
                      <label class="block text-sm font-bold text-kingdom-navy pl-1">Sub-Category Name <span class="text-red-500">*</span></label>
                      <div class="relative group">
                          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                              <i data-lucide="tag" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                          </div>
                          <input type="text" name="name" x-model="subFormData.name"
                              class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none"
                              placeholder="e.g. Door Supervision" required>
                      </div>
                  </div>

                  <div class="space-y-1.5">
                      <label class="block text-sm font-bold text-kingdom-navy pl-1">Description</label>
                      <div class="relative group">
                          <div class="absolute top-3.5 left-0 pl-4 pointer-events-none">
                              <i data-lucide="align-left" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                          </div>
                          <textarea name="description" x-model="subFormData.description"
                              class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-medium text-kingdom-navy transition-all outline-none min-h-[80px] resize-none" 
                              placeholder="Brief description..."></textarea>
                      </div>
                  </div>

                  <div class="space-y-1.5">
                      <label class="block text-sm font-bold text-kingdom-navy pl-1">Status <span class="text-red-500">*</span></label>
                      <div class="relative group" x-data="{ open: false }">
                          <input type="hidden" name="status" x-model="subFormData.status">
                          <button type="button" @click="open = !open" @click.outside="open = false"
                              class="w-full pl-4 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-bold text-left flex items-center justify-between transition-all outline-none hover:border-kingdom-gold/30"
                              :class="subFormData.status === 'Active' ? 'text-green-600 bg-green-50/50' : 'text-orange-600 bg-orange-50/50'">
                              <span x-text="subFormData.status"></span>
                              <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                          </button>
                          <div x-show="open" x-collapse x-cloak class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-50 overflow-hidden py-1">
                              <button type="button" @click="subFormData.status = 'Active'; open = false" class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-green-50 text-slate-600 hover:text-green-700 flex items-center justify-between">
                                  <span>Active</span>
                                  <i data-lucide="check" class="w-4 h-4 text-green-600" :class="subFormData.status === 'Active' ? 'opacity-100' : 'opacity-0'"></i>
                              </button>
                              <button type="button" @click="subFormData.status = 'Inactive'; open = false" class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-100 text-slate-600 hover:text-slate-700 flex items-center justify-between">
                                  <span>Inactive</span>
                                  <i data-lucide="check" class="w-4 h-4 text-slate-600" :class="subFormData.status === 'Inactive' ? 'opacity-100' : 'opacity-0'"></i>
                              </button>
                          </div>
                      </div>
                  </div>

                  <div class="flex gap-4 mt-4 pt-4 border-t border-slate-100">
                      <button type="button" @click="subModalOpen = false" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all text-sm">
                           Cancel
                      </button>
                      <button type="submit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 flex items-center justify-center gap-2 text-sm group/btn">
                          <span x-text="isSubEditing ? 'Update Sub-Category' : 'Create Sub-Category'"></span>
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 transition-transform duration-200 group-hover/btn:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>



  <script>
    window.categoryManager = function categoryManager(categories, subCategories, parentCategories) {
        return {
            // State
            searchQuery: '',
            statusFilter: '',
            expandAll: false,
            allCategories: categories,
            allSubCategories: subCategories,
            parentCategories: parentCategories,
            
            
            // Category Modal
            modalOpen: false,
            isEditing: false,
            formData: { id: null, name: '', description: '', status: 'Active', sub_categories: [''] },
            formAction: '',
            methodField: '',
            
            // Sub-Category Modal
            subModalOpen: false,
            isSubEditing: false,
            subFormData: { id: null, name: '', description: '', parent_category: '', status: 'Active' },
            subFormAction: '',
            subMethodField: '',
            
            

            // Computed
            get filteredCategories() {
                let list = this.allCategories;
                
                if (this.statusFilter === 'active') {
                    list = list.filter(c => c.status === 'Active');
                } else if (this.statusFilter === 'inactive') {
                    list = list.filter(c => c.status === 'Inactive' || c.status === 'Archived');
                }

                if (!this.searchQuery) return list;
                const q = this.searchQuery.toLowerCase();
                return list.filter(cat => {
                    const catMatch = cat.name.toLowerCase().includes(q) || 
                                     (cat.description || '').toLowerCase().includes(q);
                    const subMatch = this.getSubsForCategory(cat.name).some(s => 
                        s.name.toLowerCase().includes(q) || (s.description || '').toLowerCase().includes(q)
                    );
                    return catMatch || subMatch;
                });
            },
            
            getSubsForCategory(catName) {
                let subs = this.allSubCategories.filter(s => s.parent_category === catName);
                if (this.statusFilter === 'active') {
                    subs = subs.filter(s => s.status === 'Active');
                } else if (this.statusFilter === 'inactive') {
                    subs = subs.filter(s => s.status === 'Inactive' || s.status === 'Archived');
                }
                return subs;
            },
            
            // Category CRUD
            openCreateModal() {
                this.isEditing = false;
                this.formData = { id: null, name: '', description: '', status: 'Active', sub_categories: [''] };
                this.formAction = "{{ route('admin.job-category.store') }}";
                this.methodField = '';
                this.modalOpen = true;
                this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            },
            
            openEditModal(cat) {
                this.isEditing = true;
                this.formData = {
                    id: cat.id,
                    name: cat.name,
                    description: cat.description || '',
                    status: cat.status === 'Archived' ? 'Inactive' : cat.status,
                    sub_categories: ['']
                };
                this.formAction = `/admin/job-category/${cat.id}`;
                this.methodField = '<input type="hidden" name="_method" value="PUT">';
                this.modalOpen = true;
                this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            },
            
            // Sub-Category CRUD
            openSubCreateModal(parentName) {
                this.isSubEditing = false;
                this.subFormData = { id: null, name: '', description: '', parent_category: parentName || '', status: 'Active' };
                this.subFormAction = "{{ route('admin.sub-category.store') }}";
                this.subMethodField = '';
                this.subModalOpen = true;
                this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            },
            
            openSubEditModal(sub) {
                this.isSubEditing = true;
                this.subFormData = {
                    id: sub.id,
                    name: sub.name,
                    description: sub.description || '',
                    parent_category: sub.parent_category,
                    status: sub.status === 'Archived' ? 'Inactive' : sub.status
                };
                this.subFormAction = `/admin/sub-category/${sub.id}`;
                this.subMethodField = '<input type="hidden" name="_method" value="PUT">';
                this.subModalOpen = true;
                this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            },
            
            // Delete
            openDeleteModal(type, item) {
                let deleteWarning = '';
                let title = type === 'category' ? 'Delete Category' : 'Delete Sub-Category';
                let message = 'Are you sure you want to delete this ' + (type === 'category' ? 'category' : 'sub-category') + '? This action cannot be undone.';
                let actionUrl = '';
                
                if (type === 'category') {
                    actionUrl = `/admin/job-category/${item.id}`;
                    const subCount = this.getSubsForCategory(item.name).length;
                    if (subCount > 0) {
                        deleteWarning = `Warning: This category has ${subCount} linked sub-categor${subCount === 1 ? 'y' : 'ies'}. The server may block deletion.`;
                    }
                } else {
                    actionUrl = `/admin/sub-category/${item.id}`;
                    if (item.rate_cards_count && item.rate_cards_count > 0) {
                        deleteWarning = `Warning: This sub-category has ${item.rate_cards_count} linked rate card${item.rate_cards_count > 1 ? 's' : ''}. The server will block deletion.`;
                    }
                }
                
                if (deleteWarning) {
                    message += '\n\n' + deleteWarning;
                }
                
                this.$dispatch('open-confirm-modal', {
                    title: title,
                    message: message,
                    onConfirm: () => submitDeleteForm(actionUrl)
                });
            },
            
            init() {
                this.$watch('modalOpen', val => { if(!val && !this.subModalOpen) document.body.style.overflow = ''; else if(val) document.body.style.overflow = 'hidden'; });
                this.$watch('subModalOpen', val => { if(!val && !this.modalOpen) document.body.style.overflow = ''; else if(val) document.body.style.overflow = 'hidden'; });
                this.$watch('statusFilter', () => this.$nextTick(() => { if (window.lucide) window.lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }); }));
                this.$watch('searchQuery', () => this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); }));
                this.$watch('expandAll', () => this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); }));

                this.$nextTick(() => { 
                    if (window.lucide) window.lucide.createIcons(); 
                    
                    const urlParams = new URLSearchParams(window.location.search);
                    const newName = urlParams.get('new_name');
                    const asSub = urlParams.get('as_sub');
                    const parentCat = urlParams.get('parent_category');

                    if (newName) {
                        if (asSub === '1' || parentCat) {
                            this.openSubCreateModal(parentCat || '');
                            this.subFormData.name = newName;
                        } else {
                            this.openCreateModal();
                            this.formData.name = newName;
                        }
                    }
                });
            }
        }
    }
  </script>
</div>
@endsection
