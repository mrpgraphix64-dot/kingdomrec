@extends('layouts.admin')

@section('title', 'Rate Card | Kingdom Admin')

@section('content')
<div x-data="rateCardManager()" @keydown.escape.window="modalOpen = false" class="flex flex-col gap-3 h-full min-h-0">
  {{-- Duplicate Rate Card Warning Banner --}}
  @if(isset($duplicateWarnings) && $duplicateWarnings->count() > 0)
  <div class="bg-amber-50 border border-amber-300 rounded-xl px-5 py-3 flex items-start gap-3 shadow-sm shrink-0">
      <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
      <div class="flex-1">
          <h4 class="text-sm font-bold text-amber-800">Duplicate Active Rate Cards Detected</h4>
          <ul class="mt-1.5 space-y-0.5">
              @foreach($duplicateWarnings as $dup)
                  @php $venueName = $dup->venue_id ? (\App\Models\Venue::find($dup->venue_id)?->name ?? 'ID:'.$dup->venue_id) : 'Any Venue'; @endphp
                  <li class="text-xs text-amber-700 font-medium">
                      <span class="font-bold">{{ $dup->sub_category }}</span> —
                      {{ $dup->company_name ?: 'No Company' }} —
                      {{ $venueName }}
                      <span class="inline-flex px-1.5 py-0.5 ml-1 rounded bg-amber-200 text-amber-800 text-[9px] font-black uppercase">{{ $dup->cnt }} active rows</span>
                  </li>
              @endforeach
          </ul>
          <p class="text-[10px] text-amber-600 mt-1.5">Resolve duplicates by deactivating or deleting extra rows. The system uses the newest active record.</p>
      </div>
  </div>
  @endif
  <div class="dashboard-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 relative z-60">
    <div>
      <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Standard Rate Cards</h1>
      <p class="text-slate-500 text-xs">Approved billing rates for 2026 fiscal year</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <!-- Search Input -->
        <div class="relative w-64">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/></svg>
            </span>
            <input type="text" x-model="searchQuery" @keyup.enter="applyFilters()" placeholder="Search partner, venue, role..." 
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-xs font-bold text-kingdom-navy bg-white shadow-sm transition-all outline-none">
        </div>

        <!-- Status Filter -->
        <div class="flex items-center bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <button @click="statusFilter = ''; applyFilters()" 
                    class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wide transition-all"
                    :class="(statusFilter === '' || !statusFilter) ? 'bg-kingdom-navy text-white' : 'text-slate-500 hover:bg-slate-50'">All</button>
            <button @click="statusFilter = 'Active'; applyFilters()" 
                    class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wide transition-all border-l border-slate-200"
                    :class="(statusFilter === 'Active' || statusFilter === 'active') ? 'bg-emerald-500 text-white' : 'text-slate-500 hover:bg-slate-50'">Active</button>
            <button @click="statusFilter = 'Inactive'; applyFilters()" 
                    class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wide transition-all border-l border-slate-200"
                    :class="(statusFilter === 'Inactive' || statusFilter === 'inactive') ? 'bg-rose-500 text-white' : 'text-slate-500 hover:bg-slate-50'">Inactive</button>
        </div>

        <!-- Clear Filters Button -->
        <button @click="clearFilters()" class="px-3 py-2.5 text-xs font-semibold text-slate-500 hover:text-kingdom-navy transition-colors flex items-center gap-1 bg-white border border-slate-200 rounded-xl shadow-sm" x-show="searchQuery || statusFilter">
          <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
          <span>Clear</span>
        </button>

        <button @click="if(selectMode) { selectedRows = []; selectAll = false; } selectMode = !selectMode"
                class="flex items-center gap-2 border border-slate-200 font-bold py-2.5 px-4 rounded-xl transition-all shadow-sm active:scale-95"
                :class="selectMode ? 'bg-kingdom-gold text-white border-kingdom-gold hover:bg-kingdom-gold/90' : 'bg-white text-kingdom-navy hover:bg-slate-50 hover:text-kingdom-gold'">
            <i data-lucide="check-square" class="w-4 h-4"></i>
            <span class="text-sm" x-text="selectMode ? 'Done' : 'Select'"></span>
        </button>

        <div class="relative" x-data="{ openExport: false }">
            <button @click="openExport = true" class="flex items-center gap-2 px-4 py-2.5 bg-white rounded-xl shadow-sm text-slate-600 font-bold text-sm border border-slate-100 hover:bg-slate-50 transition-colors">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export</span>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="openExport ? 'rotate-180' : ''"></i>
            </button>
            
            <!-- Export Modal -->
            <div x-show="openExport" 
                 x-cloak
                 style="display: none;"
                 class="fixed inset-0 z-[100] overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                
                <div x-show="openExport"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                     @click="openExport = false"></div>

                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div x-show="openExport"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="relative w-full max-w-md transform rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-100">
                        
                        <div class="px-6 py-4 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                            <h3 class="font-bold text-lg text-kingdom-navy">Export Options</h3>
                            <button @click="openExport = false" class="bg-white p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Export Type Selection -->
                            <div class="space-y-3">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Export Mode</label>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- All -->
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="exportType" value="all" x-model="exportType" class="peer sr-only">
                                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50">
                                            <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">All Records</span>
                                        </div>
                                    </label>

                                    <!-- Selected -->
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="exportType" value="selected" x-model="exportType" class="peer sr-only" :disabled="selectedRows.length === 0">
                                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50"
                                             :class="selectedRows.length === 0 ? 'opacity-50 cursor-not-allowed bg-slate-50' : ''">
                                            <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">Selected</span>
                                            <span class="text-[10px] text-slate-400" x-text="'(' + selectedRows.length + ')'"></span>
                                        </div>
                                    </label>

                                    <!-- Last N -->
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="exportType" value="last" x-model="exportType" class="peer sr-only">
                                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50">
                                            <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">Last (Newest)</span>
                                        </div>
                                    </label>

                                    <!-- First N -->
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="exportType" value="first" x-model="exportType" class="peer sr-only">
                                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50">
                                            <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">First (Oldest)</span>
                                        </div>
                                    </label>
                                </div>
                                
                                <!-- Date Range Option (New) -->
                                <div class="mt-3">
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="exportType" value="date_range" x-model="exportType" class="peer sr-only">
                                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50 flex items-center justify-center gap-2">
                                            <i data-lucide="calendar-range" class="w-4 h-4 text-slate-400 peer-checked:text-kingdom-gold"></i>
                                            <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">Date Range</span>
                                        </div>
                                    </label>
                                </div>
                                
                                <!-- Date Range Inputs -->
                                <div x-show="exportType === 'date_range'" x-transition class="grid grid-cols-2 gap-3 mt-3">
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Start Date</label>
                                        <input type="date" x-model="startDate" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none text-xs">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">End Date</label>
                                        <input type="date" x-model="endDate" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none text-xs">
                                    </div>
                                </div>
                            </div>

                            <!-- Limit Input (Show only for Last/First) -->
                            <div x-show="exportType === 'last' || exportType === 'first'" x-transition class="space-y-2">
                                 <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Quantity</label>
                                 <input type="number" x-model="exportLimit" min="1" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none">
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3 pt-2">
                                <button @click="triggerExport('csv')" class="flex-1 py-3 bg-green-50 text-green-600 hover:bg-green-100 rounded-xl font-bold transition-colors flex items-center justify-center gap-2 border border-green-100">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> CSV
                                </button>
                                <button @click="triggerExport('pdf')" class="flex-1 py-3 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-bold transition-colors flex items-center justify-center gap-2 border border-red-100">
                                    <i data-lucide="file-text" class="w-4 h-4"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button 
          @click="openCreateModal()"
          class="flex items-center gap-2 px-5 py-2.5 bg-[#0F1D33] hover:bg-slate-800 text-white rounded-xl shadow-sm font-bold text-sm transition-all"
        >
            <i data-lucide="plus" class="w-4 h-4"></i> Add Rate
        </button>
    </div>
  </div>  <!-- Rate Cards Table -->
  <div class="anim-fade-in-up flex flex-col flex-1 min-h-0 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      
      <!-- Combined Table -->
      <div class="overflow-auto w-full max-h-[calc(100vh-210px)] admin-table-container">
        <table class="w-full min-w-[1000px] text-left border-collapse admin-table">
          <thead class="sticky top-0 z-20 bg-[#0f1f3d] shadow-sm">
            <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-[11px] border-b border-slate-700">
                          <template x-if="selectMode">
                              <th style="width: 45px;" class="py-2.5 px-3 text-center text-white bg-[#0f1f3d]">
                                   <input type="checkbox" @change="toggleSelectAll()" x-model="selectAll" class="rounded border-slate-650 bg-slate-750 text-indigo-600 focus:ring-0 w-3.5 h-3.5 cursor-pointer">
                              </th>
                          </template>
                          <th style="width: 70px;" class="py-2.5 px-3 text-center text-white bg-[#0f1f3d]">ID</th>
                          <th class="px-3 py-2.5 text-left text-white bg-[#0f1f3d]">Staff Type</th>
                          <th style="width: 12%;" class="px-3 py-2.5 text-right text-white bg-[#0f1f3d]">Gross Rate</th>
                          <th style="width: 12%;" class="px-3 py-2.5 text-right text-white bg-[#0f1f3d]">Net Rate</th>
                          <th style="width: 22%;" class="px-3 py-2.5 text-center text-white bg-[#0f1f3d]">Venue</th>
                          <th style="width: 12%;" class="px-3 py-2.5 text-center text-white bg-[#0f1f3d]">Status</th>
                          <th style="width: 12%;" class="px-3 py-2.5 text-center text-white bg-[#0f1f3d]">Actions</th>
                      </tr>
          </thead>
              <template x-for="(partner, pIndex) in groupedPartners" :key="partner.name">
                  <!-- Partner Group Section -->
                  <tbody class="border-b border-slate-200 bg-white">
                      <!-- Partner Parent Row -->
                      <tr class="bg-slate-50 cursor-pointer hover:bg-slate-100/90 transition-colors border-b border-slate-200" @click="togglePartner(partner.name)">
                          <td :colspan="selectMode ? 8 : 7" class="px-5 py-4 bg-slate-50 border-l-4 border-kingdom-gold border-y border-slate-200">
                              <div class="flex items-center justify-between">
                                  <div class="flex items-center gap-4">
                                      <!-- Expand/Collapse Chevron inline -->
                                      <span class="text-slate-500 transition-transform duration-200 shrink-0" :class="isPartnerExpanded(partner.name) ? 'rotate-180' : ''">
                                          <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                      </span>

                                      <!-- Partner Initial Badge -->
                                      <div class="w-9 h-9 rounded-full bg-[#0f1f3d] text-white font-extrabold flex items-center justify-center text-sm ring-2 ring-kingdom-gold/25 shadow-sm shrink-0" x-text="partner.name.charAt(0).toUpperCase()"></div>

                                      <!-- Partner Name & Venue -->
                                      <div class="flex flex-col">
                                          <div class="flex items-center gap-2 flex-wrap">
                                              <span class="font-extrabold text-kingdom-navy text-sm md:text-base tracking-tight" x-text="partner.name"></span>
                                              <template x-if="!isPartnerActive(partner.name)">
                                                  <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-650 border border-red-200">
                                                      <svg class="w-2.5 h-2.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                                      Deleted Partner
                                                  </span>
                                              </template>
                                          </div>
                                          <div class="flex items-center gap-1.5 text-xs text-slate-500 font-semibold mt-0.5">
                                              <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                              <span>Venue: <strong class="text-slate-700 font-bold" x-text="partner.primaryVenue"></strong></span>
                                          </div>
                                      </div>
                                  </div>
                                  
                                  <!-- Rate Summary / Status Pills -->
                                  <div class="flex items-center gap-3">
                                      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                          <span x-text="partner.totalActive"></span> Active
                                      </span>
                                      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-650 border border-slate-200 shadow-sm">
                                          <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                          <span x-text="partner.totalInactive"></span> Inactive
                                      </span>
                                      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white text-kingdom-navy border border-slate-200 shadow-sm">
                                          <span x-text="partner.rates.length + (partner.rates.length === 1 ? ' Rate' : ' Rates')"></span>
                                      </span>
                                  </div>
                              </div>
                          </td>
                      </tr>
                      
                      <!-- Child Rate Card Rows -->
                      <template x-for="(rate, rIndex) in partner.rates" :key="rate.id">
                          <tr x-show="isPartnerExpanded(partner.name)" 
                              x-transition
                              class="transition-colors duration-150 group cursor-pointer border-b border-slate-100 bg-white hover:bg-slate-50/50"
                              :class="{'ring-2 ring-kingdom-gold/50 bg-kingdom-gold/5': isSelected(rate.id)}"
                              @click="selectMode ? toggleRowSelection(rate.id) : null">
                              
                              <template x-if="selectMode">
                                  <td class="px-4 py-4 text-center border-l-4 border-slate-200/40" @click.stop>
                                      <input type="checkbox" :value="rate.id" x-model="selectedRows" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5 cursor-pointer">
                                  </td>
                              </template>
 
                              <td class="px-4 py-4 text-center" :class="!selectMode ? 'border-l-4 border-slate-200/40' : ''">
                                  <span class="inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono text-[10px] font-bold border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-colors shadow-sm" x-text="rIndex + 1"></span>
                              </td>
 
                              <td class="px-4 py-4">
                                  <div class="flex flex-col gap-0.5">
                                      <p class="font-bold text-xs text-slate-800 group-hover:text-indigo-600" x-text="rate.sub_category"></p>
                                      <div class="flex items-center gap-1">
                                          <span class="inline-flex self-start items-center px-1.5 py-0.5 rounded text-[9px] font-bold border"
                                                :class="isCategoryActive(rate.type) ? getRateTypeColorClass(rate.type) : 'bg-red-50 text-red-400 border-red-100'"
                                                x-text="rate.type">
                                          </span>
                                          <template x-if="!isCategoryActive(rate.type)">
                                              <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-650 border border-red-200">
                                                  <svg class="w-2.5 h-2.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                                  Deleted
                                              </span>
                                          </template>
                                      </div>
                                  </div>
                              </td>
 
                              <td class="px-4 py-4 text-right">
                                  <p class="text-[11px] font-bold text-slate-500" x-text="'£' + parseFloat(rate.overtime_rate).toFixed(2)"></p>
                              </td>
 
                              <td class="px-4 py-4 text-right">
                                  <p class="text-xs font-extrabold text-indigo-650" x-text="'£' + parseFloat(rate.hourly_rate).toFixed(2)"></p>
                              </td>
 
                              <td class="px-4 py-4 text-center">
                                  <div class="flex items-center justify-center gap-1.5" :title="rate.venue ? rate.venue.name : 'N/A'">
                                      <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                      <span class="text-[11px] truncate"
                                            :class="rate.venue && rate.venue.name === partner.primaryVenue ? 'text-slate-350 font-medium' : 'text-slate-600 font-bold'"
                                            x-text="rate.venue ? rate.venue.name : 'N/A'">
                                      </span>
                                  </div>
                              </td>
 
                              <td class="px-4 py-4 text-center" @click.stop>
                                  <button @click="toggleStatus(rate)"
                                          class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                                          :class="rateStatusMap[rate.id] === 'Active' ? 'bg-emerald-500' : 'bg-slate-350'"
                                          role="switch" :aria-checked="rateStatusMap[rate.id] === 'Active'">
                                      <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                                            :class="rateStatusMap[rate.id] === 'Active' ? 'translate-x-4' : 'translate-x-0'"></span>
                                  </button>
                              </td>
 
                              <td class="px-4 py-4 text-center">
                                  <div class="flex items-center justify-center gap-1.5">
                                      <button @click.stop="openEditModal(rate)" 
                                              class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-650 transition-colors" 
                                              title="Edit Rate">
                                          <i data-lucide="pencil" class="w-4 h-4"></i>
                                      </button>
                                      <button @click.stop="deleteRateCard('/admin/rate-card/' + rate.id)" 
                                              class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" 
                                              title="Delete Rate">
                                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                                      </button>
                                  </div>
                              </td>
                          </tr>
                      </template>
                      
                      <!-- Spacer Row to separate partner groups -->
                      <tr class="h-4 bg-slate-50/10"><td :colspan="selectMode ? 8 : 7" class="p-0 border-none"></td></tr>
                  </tbody>
              </template>
              
              <!-- Empty State Row -->
              <template x-if="groupedPartners.length === 0">
                  <tbody class="bg-white">
                      <tr>
                          <td :colspan="selectMode ? 8 : 7" class="px-4 py-12 text-center text-slate-400 bg-white">
                              <div class="flex flex-col items-center justify-center gap-2">
                                  <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 shadow-sm mb-1">
                                      <svg class="w-6 h-6 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                  </div>
                                  <span class="text-xs font-bold text-kingdom-navy">No Rate Cards Found</span>
                                  <p class="text-[11px] text-slate-400">There are no rate cards matching the selected status or search query.</p>
                              </div>
                          </td>
                      </tr>
                  </tbody>
              </template>
        </table>
      </div>
      <div class="px-4 py-4 border-t border-slate-200 bg-slate-50">
          {{ $rates->links() }}
      </div>
  </div>

  <!-- Create/Edit Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>
        
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="modalOpen"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                
                <div class="flex items-center justify-between px-6 py-3 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                    <h3 class="text-xl font-bold text-kingdom-navy" x-text="isEditing ? 'Edit Rate Card' : 'Add Rate Card'"></h3>
                    <button @click="modalOpen = false" class="p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="px-6 pb-6 pt-2 overflow-y-auto custom-scrollbar max-h-[80vh]">
                    <form :action="formAction" method="POST" class="flex flex-col gap-4" id="rateCardForm">
                        @csrf
                        <div x-html="methodField" class="hidden"></div>

                        <!-- 1. Category -->
                        <div class="space-y-1.5">
                             <label class="block text-sm font-bold text-kingdom-navy pl-1">Category <span class="text-red-500">*</span></label>
                             <div class="relative group" x-data="{ open: false }">
                                <input type="hidden" name="type" x-model="formData.type" required>
                                 <button type="button" @click="open = !open" @click.outside="open = false"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy text-left flex items-center justify-between transition-all outline-none">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="layers" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <span x-text="formData.type || 'Select Category'" :class="formData.type ? 'text-kingdom-navy' : 'text-slate-400'"></span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                 </button>
                                
                                <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5 max-h-72 flex flex-col">
                                     <div class="overflow-y-auto max-h-56">
                                    @foreach($categories as $cat)
                                    <button type="button" @click="formData.type = '{{ $cat->name }}'; formData.sub_category = ''; open = false" 
                                        class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-50 text-slate-700 hover:text-kingdom-navy transition-colors flex items-center justify-between">
                                        {{ $cat->name }}
                                        <i x-show="formData.type === '{{ $cat->name }}'" data-lucide="check" class="w-4 h-4 text-kingdom-gold"></i>
                                    </button>
                                    @endforeach
                                     </div>
                                     <div class="px-2 py-2 border-t border-slate-100 bg-slate-50 shrink-0">
                                         <a href="/admin/job-post" target="_blank"
                                            class="w-full py-2 rounded-lg bg-white border border-slate-200 text-kingdom-gold font-bold text-xs hover:border-kingdom-gold hover:bg-kingdom-gold/5 transition-all flex items-center justify-center gap-1.5">
                                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Add New Category
                                         </a>
                                     </div>
                                </div>
                            </div>
                            <span x-show="errors.type" x-text="errors.type" class="text-xs text-red-500 font-bold pl-1 block mt-1"></span>
                        </div>

                        <!-- 2. Sub Category -->
                        <div class="space-y-1.5">
                             <label class="block text-sm font-bold text-kingdom-navy pl-1">Sub Category <span class="text-red-500">*</span></label>
                             <div class="relative group" x-data="{ open: false }">
                                <input type="hidden" name="sub_category" x-model="formData.sub_category" required>
                                 <button type="button" @click="open = !open" @click.outside="open = false"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy text-left flex items-center justify-between transition-all outline-none"
                                    :class="!formData.type ? 'opacity-50 cursor-not-allowed' : ''"
                                    :disabled="!formData.type">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="git-branch" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <span x-text="formData.sub_category || 'Select Sub Category'" :class="formData.sub_category ? 'text-kingdom-navy' : 'text-slate-400'"></span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                 </button>
                                
                                <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5 max-h-72 flex flex-col">
                                     <div class="overflow-y-auto max-h-56">
                                    <template x-for="sub in filteredSubCategories" :key="sub.id">
                                        <button type="button" @click="formData.sub_category = sub.name; open = false" 
                                            class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-50 text-slate-700 hover:text-kingdom-navy transition-colors flex items-center justify-between">
                                            <span x-text="sub.name"></span>
                                            <i x-show="formData.sub_category === sub.name" data-lucide="check" class="w-4 h-4 text-kingdom-gold"></i>
                                        </button>
                                    </template>
                                    <div x-show="filteredSubCategories.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center">
                                        No sub-categories found
                                    </div>
                                     </div>
                                     <div class="px-2 py-2 border-t border-slate-100 bg-slate-50 shrink-0">
                                         <a href="/admin/sub-category" target="_blank"
                                            class="w-full py-2 rounded-lg bg-white border border-slate-200 text-kingdom-gold font-bold text-xs hover:border-kingdom-gold hover:bg-kingdom-gold/5 transition-all flex items-center justify-center gap-1.5">
                                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Add New Sub-Category
                                         </a>
                                     </div>
                                </div>
                            </div>
                            <span x-show="errors.sub_category" x-text="errors.sub_category" class="text-xs text-red-500 font-bold pl-1 block mt-1"></span>
                        </div>

                        <!-- 3. Select Staff -->
                        <!-- Removed Staff Role (Title) -> replaced essentially by sub_category directly -->
                        <!-- 4. Company Name -->
                         <div class="space-y-1.5">
                             <label class="block text-sm font-bold text-kingdom-navy pl-1">Company Name <span class="text-red-500">*</span></label>
                             <div class="relative group" x-data="{ open: false, search: '' }" @click.outside="open = false">
                                <input type="hidden" name="company_name" x-model="formData.company_name">
                                 <button type="button" @click="open = !open; $nextTick(() => { if(open) $refs.partnerSearch.focus() })"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy text-left flex items-center justify-between transition-all outline-none">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="building-2" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <span x-text="formData.company_name || 'Select Partner Company'" :class="formData.company_name ? 'text-kingdom-navy' : 'text-slate-400'"></span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                 </button>
                                
                                <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5">
                                    <!-- Search -->
                                    <div class="p-2 border-b border-slate-100">
                                        <input type="text" x-ref="partnerSearch" x-model="search" placeholder="Search partners..." 
                                            class="w-full px-3 py-2 text-sm rounded-lg bg-slate-50 border border-slate-200 focus:outline-none focus:border-kingdom-gold placeholder:text-slate-400 font-medium">
                                    </div>
                                    <div class="max-h-48 overflow-y-auto">
                                        @foreach($partners as $partner)
                                        @php $displayName = $partner->company_name ?: $partner->name; @endphp
                                        <button type="button" 
                                            x-show="!search || '{{ strtolower($displayName) }}'.includes(search.toLowerCase())"
                                            @click="formData.company_name = '{{ addslashes($displayName) }}'; open = false; search = ''" 
                                            class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-50 text-slate-700 hover:text-kingdom-navy transition-colors flex items-center justify-between">
                                            <div class="flex flex-col">
                                                <span>{{ $displayName }}</span>
                                                @if($partner->company_name && $partner->company_name !== $partner->name)
                                                <span class="text-[10px] text-slate-400 font-medium">{{ $partner->name }}</span>
                                                @endif
                                            </div>
                                            <i x-show="formData.company_name === '{{ addslashes($displayName) }}'" data-lucide="check" class="w-4 h-4 text-kingdom-gold"></i>
                                        </button>
                                        @endforeach
                                        <div x-show="search && ![...document.querySelectorAll('[x-show*=search]')].some(el => el.offsetParent)" class="px-4 py-3 text-sm text-slate-400 text-center">
                                            No partners found
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <span x-show="errors.company_name" x-text="errors.company_name" class="text-xs text-red-500 font-bold pl-1 block mt-1"></span>
                        </div>

                        <!-- 4. Venue -->
                         <div class="space-y-1.5">
                             <label class="block text-sm font-bold text-kingdom-navy pl-1">Venue <span class="text-red-500">*</span></label>
                             <div class="relative group" x-data="{ open: false }">
                                <input type="hidden" name="venue_id" x-model="formData.venue_id" required>
                                 <button type="button" @click="open = !open" @click.outside="open = false"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold text-kingdom-navy text-left flex items-center justify-between transition-all outline-none">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="map-pin" class="w-5 h-5 text-slate-400 group-focus-within:text-kingdom-gold transition-colors"></i>
                                    </div>
                                    <span x-text="getSelectedVenueName() || 'Select Venue'" :class="formData.venue_id ? 'text-kingdom-navy' : 'text-slate-400'"></span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                 </button>
                                
                                <div x-show="open" x-collapse x-cloak class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden ring-1 ring-black/5 max-h-60 overflow-y-auto">
                                    @foreach($venues as $venue)
                                    <button type="button" @click="formData.venue_id = '{{ $venue->id }}'; open = false" 
                                        class="w-full text-left px-4 py-2.5 text-sm font-bold hover:bg-slate-50 text-slate-700 hover:text-kingdom-navy transition-colors flex items-center justify-between">
                                        {{ $venue->name }}
                                        <i x-show="formData.venue_id == '{{ $venue->id }}'" data-lucide="check" class="w-4 h-4 text-kingdom-gold"></i>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                             <span x-show="errors.venue_id" x-text="errors.venue_id" class="text-xs text-red-500 font-bold pl-1 block mt-1"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- 6. Gross Rate -->
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Gross Rate (GBP/hr) <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold text-lg">£</span>
                                    </div>
                                    <input type="number" step="0.01" name="overtime" x-model="formData.overtime" @input="autoCalcTotal()" class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" placeholder="0.00" required>
                                </div>
                                <span x-show="errors.overtime" x-text="errors.overtime" class="text-xs text-red-500 font-bold pl-1 block mt-1"></span>
                            </div>

                            <!-- 5. Net Rate -->
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Net Rate (GBP/hr) <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold text-lg">£</span>
                                    </div>
                                    <input type="number" step="0.01" name="hourly" x-model="formData.hourly" class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" placeholder="0.00" required>
                                </div>
                                <span x-show="errors.hourly" x-text="errors.hourly" class="text-xs text-red-500 font-bold pl-1 block mt-1"></span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- 7. Hours -->
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Hours</label>
                                <div class="relative group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="clock" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <input type="number" step="0.5" name="hours" x-model="formData.hours" @input="autoCalcTotal()" class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none" placeholder="0.0">
                                </div>
                            </div>
                            
                            <!-- 8. Total Amount (Auto-calculated: Gross × Hours) -->
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-kingdom-navy pl-1">Total Amount <span class="text-[10px] text-slate-400 font-normal">(Gross × Hours)</span></label>
                                <div class="relative group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold text-lg">£</span>
                                    </div>
                                    <input type="number" step="0.01" name="total_amount" x-model="formData.total_amount" class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-slate-100 border border-slate-200 text-sm font-bold text-kingdom-navy transition-all outline-none cursor-not-allowed" placeholder="0.00" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Status Input -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-kingdom-navy pl-1">Status</label>
                            <div class="relative">
                                <select name="status" x-model="formData.status"
                                    class="w-full h-11 px-3 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium text-kingdom-navy outline-none appearance-none cursor-pointer focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <i data-lucide="chevron-down"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="flex gap-4 mt-4 pt-4 border-t border-slate-100">
                            <button type="button" @click="modalOpen = false" class="flex-1 px-4 py-3.5 rounded-xl border-2 border-slate-100 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-800 transition-all">Cancel</button>
                            <button type="button" @click="validateAndSubmit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 flex items-center justify-center gap-2">
                                 <span x-text="isEditing ? 'Save Changes' : 'Save Rate Card'"></span>
                                 <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    
    <script>
        window.rateCardManager = function rateCardManager() {
            return {
                modalOpen: false,
                isEditing: false,
                allSubCategories: @json($subCategories),
                allVenues: @json($venues),
                // Export State
                exportType: 'all',
                exportLimit: 10,
                startDate: '',
                endDate: '',
                selectMode: false,
                selectedRows: [],
                selectAll: false,
                // End Export State

                searchQuery: '{{ request('search', '') }}',
                expandedPartners: {},
                ratesList: @json($rates->items()),
                activeCategories: @json($categories->pluck('name')),
                activePartners: @json($partners->map(function($p) { return $p->company_name ?: $p->name; })->toArray()),

                togglePartner(name) {
                    this.expandedPartners[name] = !this.expandedPartners[name];
                },
                isPartnerExpanded(name) {
                    return !!this.expandedPartners[name];
                },
                isSelected(id) {
                    return this.selectedRows.includes(id);
                },
                getRateTypeColorClass(type) {
                    switch(type) {
                        case 'Security': return 'bg-blue-50 text-blue-600 border-blue-100';
                        case 'Hospitality': return 'bg-orange-50 text-orange-600 border-orange-100';
                        case 'Management': return 'bg-purple-50 text-purple-600 border-purple-100';
                        default: return 'bg-slate-100 text-slate-600 border-slate-200';
                    }
                },
                isCategoryActive(type) {
                    return this.activeCategories.includes(type);
                },
                isPartnerActive(name) {
                    if (name === 'Global Rates') return true;
                    return this.activePartners.includes(name);
                },

                toggleRowSelection(id) {
                    if (this.selectedRows.includes(id)) {
                        this.selectedRows = this.selectedRows.filter(rowId => rowId !== id);
                    } else {
                        this.selectedRows.push(id);
                    }
                },
                get filteredSubCategories() {
                    // Handle aliasing: If type is 'Security', also match 'SIA Security'
                    const type = this.formData.type;
                    if (!type) return [];
                    
                    return this.allSubCategories.filter(sub => {
                        if (sub.parent_category === type) return true;
                        if (type === 'Security' && sub.parent_category === 'SIA Security') return true;
                        if (type === 'SIA Security' && sub.parent_category === 'Security') return true;
                        return false;
                    });
                },
                getSelectedVenueName() {
                     const venue = this.allVenues.find(v => v.id == this.formData.venue_id);
                     return venue ? venue.name : '';
                },
                rateStatusMap: @json($rates->pluck('status', 'id')),
                statusFilter: '{{ request('status', '') }}',
                isRowVisible(rateId) {
                    if (this.statusFilter === '') return true;
                    if (this.statusFilter === 'active') return this.rateStatusMap[rateId] === 'Active';
                    if (this.statusFilter === 'inactive') return this.rateStatusMap[rateId] === 'Inactive';
                    return true;
                },
                get groupedPartners() {
                    const groups = {};
                    
                    this.ratesList.forEach(rate => {
                        const company = rate.company_name || 'Global Rates';
                        if (!groups[company]) {
                            groups[company] = {
                                name: company,
                                rates: []
                            };
                        }
                        groups[company].rates.push(rate);
                    });
                    
                    const result = [];
                    
                    Object.keys(groups).forEach(companyName => {
                        const group = groups[companyName];
                        
                        // Filter rates in the group
                        const filteredRates = group.rates.filter(rate => {
                            // 1. Status Filter
                            const rateStatus = this.rateStatusMap[rate.id] || rate.status || 'Active';
                            if (this.statusFilter === 'active' && rateStatus !== 'Active') return false;
                            if (this.statusFilter === 'inactive' && rateStatus !== 'Inactive') return false;
                            
                            // 2. Search Query Filter
                            if (this.searchQuery.trim() !== '') {
                                const q = this.searchQuery.toLowerCase();
                                const partner = companyName.toLowerCase();
                                const venue = (rate.venue ? rate.venue.name : (this.allVenues.find(v => v.id == rate.venue_id)?.name || 'n/a')).toLowerCase();
                                const role = (rate.sub_category || '').toLowerCase();
                                const cat = (rate.type || '').toLowerCase();
                                
                                const matches = partner.includes(q) || 
                                                venue.includes(q) || 
                                                role.includes(q) || 
                                                cat.includes(q);
                                if (!matches) return false;
                            }
                            
                            return true;
                        });
                        
                        // Overall counts (not affected by search query or status filters)
                        const totalActive = group.rates.filter(r => (this.rateStatusMap[r.id] || r.status || 'Active') === 'Active').length;
                        const totalInactive = group.rates.filter(r => (this.rateStatusMap[r.id] || r.status || 'Active') === 'Inactive').length;
                        
                        // Calculate primary venue inside raw list for this partner
                        const venueCounts = {};
                        group.rates.forEach(r => {
                            const vObj = r.venue || this.allVenues.find(v => v.id == r.venue_id);
                            const vName = vObj ? vObj.name : 'N/A';
                            venueCounts[vName] = (venueCounts[vName] || 0) + 1;
                        });
                        
                        let primaryVenue = 'N/A';
                        let maxCount = 0;
                        Object.keys(venueCounts).forEach(vName => {
                            if (venueCounts[vName] > maxCount) {
                                maxCount = venueCounts[vName];
                                primaryVenue = vName;
                            }
                        });
                        
                        if (filteredRates.length > 0) {
                            result.push({
                                name: companyName,
                                rates: filteredRates,
                                primaryVenue: primaryVenue,
                                totalActive: totalActive,
                                totalInactive: totalInactive,
                                totalRates: group.rates.length
                            });
                        }
                    });
                    
                    // Sort partners: Global Rates always at bottom, others alphabetical
                    return result.sort((a, b) => {
                        if (a.name === 'Global Rates') return 1;
                        if (b.name === 'Global Rates') return -1;
                        return a.name.localeCompare(b.name);
                    });
                },
                applyFilters() {
                    let url = new URL(window.location.href);
                    if (this.searchQuery) {
                        url.searchParams.set('search', this.searchQuery);
                    } else {
                        url.searchParams.delete('search');
                    }
                    if (this.statusFilter) {
                        url.searchParams.set('status', this.statusFilter);
                    } else {
                        url.searchParams.delete('status');
                    }
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                },
                clearFilters() {
                    this.searchQuery = '';
                    this.statusFilter = '';
                    this.applyFilters();
                },
                init() {
                    this.$watch('modalOpen', val => document.body.style.overflow = val ? 'hidden' : '');
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }); });
                    this.$watch('statusFilter', () => this.$nextTick(() => { if (window.lucide) window.lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }); }));
                    
                    // Expand all partners by default on load
                    this.ratesList.forEach(rate => {
                        const company = rate.company_name || 'Global Rates';
                        this.expandedPartners[company] = true;
                    });
                },
                formData: {
                    id: null,
                    type: '',
                    sub_category: '', 
                    company_name: '',
                    venue_id: '',
                    hourly: '',
                    overtime: '',
                    hours: '',
                    total_amount: '',
                    status: 'Active'
                },
                formAction: '',
                methodField: '',
                errors: {},

                // Auto-calculate total: Gross Rate × Hours
                autoCalcTotal() {
                    const gross = parseFloat(this.formData.overtime) || 0;
                    const hours = parseFloat(this.formData.hours) || 0;
                    this.formData.total_amount = (gross * hours).toFixed(2);
                },

                openCreateModal() {
                    this.isEditing = false;
                    this.formData = {
                         id: null, type: '', sub_category: '', company_name: '', venue_id: '', hourly: '', overtime: '', hours: '', total_amount: '', status: 'Active'
                    };
                    this.errors = {};
                    this.formAction = "{{ route('admin.rate-card.store') }}"; 
                    this.methodField = '';
                    this.modalOpen = true;
                },

                openEditModal(rate) {
                    this.isEditing = true;
                    // Map existing rate data to form.
                    this.formData = {
                        id: rate.id,
                        type: rate.type,
                        sub_category: rate.sub_category || '',
                        company_name: rate.company_name || '',
                        venue_id: rate.venue_id || '',
                        hourly: rate.hourly_rate,
                        overtime: rate.overtime_rate,
                        hours: rate.hours,
                        total_amount: rate.total_amount,
                        status: rate.status || 'Active'
                    };
                    this.errors = {};
                    this.formAction = `/admin/rate-card/${rate.id}`;
                    this.methodField = '<input type="hidden" name="_method" value="PUT">';
                    this.autoCalcTotal();
                    this.modalOpen = true;
                },

                async toggleStatus(rate) {
                    const currentStatus = this.rateStatusMap[rate.id];
                    const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
                    
                    // Optimistic update
                    this.rateStatusMap[rate.id] = newStatus;

                    try {
                        const response = await fetch(`/admin/rate-card/${rate.id}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ status: newStatus })
                        });
                        if (!response.ok) {
                            this.rateStatusMap[rate.id] = currentStatus;
                            const data = await response.json().catch(() => null);
                            window.dispatchEvent(new CustomEvent('toast-show', { detail: { message: data?.message || 'Could not update status.', type: 'error' } }));
                        }
                    } catch {
                        this.rateStatusMap[rate.id] = currentStatus;
                        window.dispatchEvent(new CustomEvent('toast-show', { detail: { message: 'Could not update status.', type: 'error' } }));
                    }
                },
                
                validateAndSubmit(event) {
                    this.errors = {}; // Reset errors
                    let valid = true;

                    if (!this.formData.type) {
                        this.errors.type = 'Please select a Category.';
                        valid = false;
                    }
                    if (!this.formData.sub_category) {
                         this.errors.sub_category = 'Please select a Sub-Category.';
                        valid = false;
                    }
                    if (!this.formData.company_name) {
                        this.errors.company_name = 'Company Name is required.';
                        valid = false;
                    }

                     if (!this.formData.venue_id) {
                        this.errors.venue_id = 'Please select a Venue.';
                        valid = false;
                    }
                    if (!this.formData.hourly) {
                        this.errors.hourly = 'Rate Nett is required.';
                        valid = false;
                    }
                    if (!this.formData.overtime) {
                        this.errors.overtime = 'Rate Gross is required.';
                        valid = false;
                    }
                    
                    if (valid) {
                         document.querySelector('#rateCardForm').submit();
                    }
                },

                triggerExport(format) {
                    let url = `{{ route('admin.rate-card.export') }}?format=${format}&type=${this.exportType}`;
                    
                    if (this.exportType === 'last' || this.exportType === 'first') {
                        url += `&limit=${this.exportLimit}`;
                    }
                    
                    if (this.exportType === 'selected') {
                        if (this.selectedRows.length === 0) {
                            Swal.fire({ icon: 'warning', title: 'No Selection', text: 'Please select at least one row.', confirmButtonColor: '#0F1D33' });
                            return;
                        }
                        url += `&ids=${this.selectedRows.join(',')}`;
                    }

                    if (this.exportType === 'date_range') {
                        if (!this.startDate || !this.endDate) {
                            Swal.fire({ icon: 'warning', title: 'Missing Dates', text: 'Please select both start and end dates.', confirmButtonColor: '#0F1D33' });
                            return;
                        }
                        url += `&start_date=${this.startDate}&end_date=${this.endDate}`;
                    }
                    
                    window.location.href = url;
                    this.openExport = false;
                },

                toggleSelectAll() {
                    const visibleIds = [];
                    this.groupedPartners.forEach(group => {
                        group.rates.forEach(r => {
                            visibleIds.push(r.id);
                        });
                    });
                    
                    if (this.selectAll) {
                        this.selectedRows = visibleIds;
                    } else {
                        this.selectedRows = [];
                    }
                },

                deleteRateCard(actionUrl) {
                     this.$dispatch('open-confirm-modal', {
                         title: 'Delete Rate Card',
                         message: 'Are you sure you want to delete this rate card? This action cannot be undone.',
                         onConfirm: () => submitDeleteForm(actionUrl)
                     });
                 }
            }
        }
        
        
    </script>
</div>
@endsection
