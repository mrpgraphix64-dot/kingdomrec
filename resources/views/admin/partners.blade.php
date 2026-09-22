@extends('layouts.admin')

@section('title', 'Partners | Kingdom Admin')

@section('content')
<div x-data="partnersManager()" @keydown.escape.window="detailModal = false" class="flex flex-col gap-3">

  <!-- Header -->
  <div class="dashboard-header flex flex-col sm:flex-row sm:items-center justify-between gap-2 shrink-0">
    <div>
      <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Partners</h1>
      <p class="text-slate-500 text-xs">Manage partner accounts & access</p>
    </div>
  </div>

  <!-- Stats Overview -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 shrink-0">
    <div class="bg-white rounded-xl border border-slate-200 px-3 py-2.5 flex items-center gap-2.5 shadow-sm">
        <div class="h-8 w-8 shrink-0 rounded-lg bg-violet-50 flex items-center justify-center">
            <i data-lucide="handshake" class="w-4 h-4 text-violet-500"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-lg font-extrabold text-violet-600 leading-tight">{{ $stats['total'] }}</p>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Total</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-3 py-2.5 flex items-center gap-2.5 shadow-sm">
        <div class="h-8 w-8 shrink-0 rounded-lg bg-emerald-50 flex items-center justify-center">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-lg font-extrabold text-emerald-600 leading-tight">{{ $stats['active'] }}</p>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Active</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-3 py-2.5 flex items-center gap-2.5 shadow-sm">
        <div class="h-8 w-8 shrink-0 rounded-lg bg-rose-50 flex items-center justify-center">
            <i data-lucide="x-circle" class="w-4 h-4 text-rose-400"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-lg font-extrabold text-rose-500 leading-tight">{{ $stats['inactive'] }}</p>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Inactive</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-3 py-2.5 flex items-center gap-2.5 shadow-sm">
        <div class="h-8 w-8 shrink-0 rounded-lg bg-blue-50 flex items-center justify-center">
            <i data-lucide="calendar-plus" class="w-4 h-4 text-blue-500"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-lg font-extrabold text-blue-600 leading-tight">{{ $stats['recent'] }}</p>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">This Month</p>
        </div>
    </div>
  </div>

  <!-- Partners Content Container -->
  <div class="anim-fade-in-up bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col flex-1 min-h-0 relative">
    
    <!-- Filter Bar placed above table -->
    <div class="p-4 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 bg-slate-50/50 shrink-0">
      <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
        <!-- Search Input -->
        <div class="relative flex-1 max-w-xs">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
          </span>
          <input type="text" x-model="searchQuery" @keyup.enter="applyFilters()" placeholder="Search partners..." class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
        </div>
        
        <!-- Status Filter -->
        <select x-model="statusFilter" @change="applyFilters()" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        
        <!-- Clear Filters Button -->
        <button @click="clearFilters()" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-kingdom-navy transition-colors flex items-center gap-1" x-show="searchQuery || statusFilter">
          <i data-lucide="x-circle" class="w-4 h-4"></i> Clear
        </button>
      </div>
      
      <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide" x-text="filteredPartners.length + ' records on this page'"></span>
    </div>

    <!-- Scrollable Content Area -->
    <div class="overflow-auto w-full max-h-[calc(100vh-210px)] admin-table-container flex-1 p-0 relative">
      <div class="w-full">
        <table class="w-full min-w-[1000px] text-left border-collapse admin-table">
          <thead class="sticky top-0 z-20 bg-[#0f1f3d] shadow-sm">
            <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-[11px] border-b border-slate-700">
              <th class="py-2.5 px-3 text-white border-r border-slate-700/50 bg-[#0f1f3d]">Partner</th>
              <th class="py-2.5 px-3 w-[22%] text-white border-r border-slate-700/50 bg-[#0f1f3d]">Contact</th>
              <th class="py-2.5 px-3 w-[16%] text-white border-r border-slate-700/50 bg-[#0f1f3d]">Company</th>
              <th class="py-2.5 px-3 text-center w-28 text-white border-r border-slate-700/50 bg-[#0f1f3d]">Active Events</th>
              <th class="py-2.5 px-3 text-center w-24 text-white border-r border-slate-700/50 bg-[#0f1f3d]">Status</th>
              <th class="py-2.5 px-3 text-right w-36 text-white bg-[#0f1f3d]">Actions</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(partner, idx) in filteredPartners" :key="partner.id">
              <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors" @click="openDetail(partner)">
                <!-- Partner -->
                <td class="py-4 px-4 text-sm border-r border-slate-100">
                  <div class="flex items-center gap-2.5 min-w-0">
                    <div class="h-8 w-8 shrink-0 rounded-full flex items-center justify-center text-[10px] font-extrabold ring-2 ring-offset-1 ring-slate-200 bg-violet-50 text-violet-600">
                      <span x-text="partner.name ? partner.name.substring(0,2).toUpperCase() : '??'"></span>
                    </div>
                    <div class="min-w-0">
                      <span class="font-semibold text-slate-800 text-sm block truncate max-w-[150px]" :title="partner.name" x-text="partner.name"></span>
                      <span class="text-[10px] text-slate-400 block" x-text="'Joined ' + formatDate(partner.created_at)"></span>
                    </div>
                  </div>
                </td>
                <!-- Contact (email + phone stacked, one column) -->
                <td class="py-4 px-4 text-sm border-r border-slate-100">
                  <a :href="'mailto:' + partner.email" class="text-indigo-600 hover:text-indigo-800 hover:underline block truncate max-w-[220px]" :title="partner.email" x-text="partner.email"></a>
                  <span class="text-[11px] text-slate-400 font-medium block truncate mt-0.5" x-text="partner.phone || 'No phone on file'"></span>
                </td>
                <!-- Company -->
                <td class="py-4 px-4 text-slate-600 text-sm truncate border-r border-slate-100 max-w-[150px]" :title="partner.company_name" x-text="partner.company_name || '—'"></td>
                <!-- Active Events (the business-relationship signal — how live this partner is right now) -->
                <td class="py-4 px-4 text-center border-r border-slate-100">
                  <template x-if="partner.active_events_count > 0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                      <span x-text="partner.active_events_count"></span> active
                    </span>
                  </template>
                  <template x-if="!partner.active_events_count">
                    <span class="text-xs text-slate-400 font-medium" x-text="(partner.total_events_count || 0) + ' total'"></span>
                  </template>
                </td>
                <!-- Status Toggle -->
                <td class="py-4 px-4 text-center border-r border-slate-100" @click.stop>
                  <button @click="toggleStatus(partner)"
                          class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                          :class="partner.is_active ? 'bg-emerald-500' : 'bg-slate-350'"
                          role="switch" :aria-checked="partner.is_active">
                      <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                            :class="partner.is_active ? 'translate-x-4' : 'translate-x-0'"></span>
                  </button>
                </td>
                <!-- Actions -->
                <td class="py-4 px-4 text-right" @click.stop>
                  <div class="flex items-center justify-end gap-1.5">
                    <button @click="openDetail(partner)" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="View">
                      <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    <button @click="openEditModal(partner)" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-650 transition-colors" title="Edit">
                      <i data-lucide="pencil" class="w-4 h-4"></i>
                    </button>
                    <button @click.prevent="copyEmail(partner.email, $event.currentTarget)" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Copy Email">
                      <i data-lucide="copy" class="w-4 h-4"></i>
                    </button>
                    <button @click="$dispatch('open-confirm-modal', { 
                                title: 'Delete Partner', 
                                message: 'Are you sure you want to delete this partner account? This action cannot be undone.', 
                                onConfirm: () => submitDeleteForm('/admin/partners/' + partner.id)
                             })" 
                            class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" 
                            title="Delete Partner">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
      <!-- Empty State -->
      <div x-show="filteredPartners.length === 0" class="py-12 text-center text-slate-400">
        <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 ring-4 ring-white shadow-sm">
          <i data-lucide="search" class="w-8 h-8 text-slate-300"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900">No partners found</h3>
        <p class="text-slate-500 max-w-sm mx-auto mt-2 text-sm">Try adjusting your search or filters</p>
        <button @click="clearFilters()" class="mt-4 text-kingdom-gold font-bold text-sm hover:underline">Clear all filters</button>
      </div>
    </div>
    
    <!-- Pagination Links -->
    <div class="p-4 border-t border-slate-100 bg-white shrink-0">
      {{ $partners->links() }}
    </div>
  </div>

  <!-- Detail Modal -->
  <div x-show="detailModal" x-cloak style="display:none"
       class="fixed inset-0 z-[100] overflow-y-auto" aria-modal="true">
    <div x-show="detailModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="detailModal = false"></div>

    <div class="flex min-h-full items-center justify-center p-4" @click="detailModal = false">
      <div x-show="detailModal"
           @click.stop
           x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
           x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
           class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">

        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 px-5 py-4 text-white">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="h-11 w-11 rounded-full bg-white/20 flex items-center justify-center text-sm font-extrabold backdrop-blur-sm">
                <span x-text="selectedPartner?.name ? selectedPartner.name.substring(0,2).toUpperCase() : ''"></span>
              </div>
              <div>
                <h3 class="font-bold text-sm" x-text="selectedPartner?.name"></h3>
                <p class="text-[10px] text-white/70" x-text="selectedPartner?.company_name || 'No company'"></p>
              </div>
            </div>
            <button @click="detailModal = false" class="p-1.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors">
              <i data-lucide="x" class="w-4 h-4"></i>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-5 space-y-3">
          <div class="grid grid-cols-1 gap-2.5">
            <!-- Phone -->
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 rounded-xl">
              <div class="h-7 w-7 rounded-lg bg-white flex items-center justify-center border border-slate-100">
                <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Phone</p>
                <p class="text-xs font-medium text-kingdom-navy" x-text="selectedPartner?.phone || 'Not provided'"></p>
              </div>
            </div>
            <!-- Email -->
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 rounded-xl">
              <div class="h-7 w-7 rounded-lg bg-white flex items-center justify-center border border-slate-100">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Email</p>
                <p class="text-xs font-medium text-kingdom-navy truncate" x-text="selectedPartner?.email"></p>
              </div>
            </div>
            <!-- Company -->
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 rounded-xl">
              <div class="h-7 w-7 rounded-lg bg-white flex items-center justify-center border border-slate-100">
                <i data-lucide="building-2" class="w-3.5 h-3.5 text-slate-400"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Company</p>
                <p class="text-xs font-medium text-kingdom-navy" x-text="selectedPartner?.company_name || 'Not provided'"></p>
              </div>
            </div>
            <!-- Events -->
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 rounded-xl">
              <div class="h-7 w-7 rounded-lg bg-white flex items-center justify-center border border-slate-100">
                <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-slate-400"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Events</p>
                <p class="text-xs font-medium text-kingdom-navy">
                  <span class="font-bold" :class="selectedPartner?.active_events_count > 0 ? 'text-emerald-600' : 'text-slate-500'" x-text="selectedPartner?.active_events_count || 0"></span> active ·
                  <span x-text="selectedPartner?.total_events_count || 0"></span> total bookings
                </p>
              </div>
            </div>
            <!-- Website -->
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 rounded-xl">
              <div class="h-7 w-7 rounded-lg bg-white flex items-center justify-center border border-slate-100">
                <i data-lucide="globe" class="w-3.5 h-3.5 text-slate-400"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Website</p>
                <template x-if="selectedPartner?.website">
                  <a :href="selectedPartner.website" target="_blank" class="text-xs font-medium text-blue-600 hover:underline truncate block" x-text="selectedPartner.website"></a>
                </template>
                <template x-if="!selectedPartner?.website">
                  <p class="text-xs font-medium text-slate-400">Not provided</p>
                </template>
              </div>
            </div>
            <!-- Address -->
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 rounded-xl">
              <div class="h-7 w-7 rounded-lg bg-white flex items-center justify-center border border-slate-100">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Address</p>
                <p class="text-xs font-medium text-kingdom-navy" x-text="selectedPartner?.address || 'Not provided'"></p>
              </div>
            </div>
            <!-- Joined -->
            <div class="flex items-center gap-3 px-3 py-2.5 bg-slate-50 rounded-xl">
              <div class="h-7 w-7 rounded-lg bg-white flex items-center justify-center border border-slate-100">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Joined</p>
                <p class="text-xs font-medium text-kingdom-navy" x-text="selectedPartner ? formatDate(selectedPartner.created_at) : ''"></p>
              </div>
            </div>
          </div>

          <!-- Status & Actions -->
          <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <div class="flex-1 flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase text-slate-400">Status:</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                      :class="selectedPartner?.is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-500 border-rose-200'"
                      x-text="selectedPartner?.is_active ? 'Active' : 'Inactive'"></span>
            </div>
            <button @click="copyEmail(selectedPartner?.email, $event.currentTarget)" 
               class="px-3 py-1.5 bg-kingdom-navy text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition-colors flex items-center gap-1.5">
              <i data-lucide="copy" class="w-3 h-3"></i> Copy Email
            </button>
            <button @click="detailModal = false; openEditModal(selectedPartner)" 
               class="px-3 py-1.5 bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold rounded-lg hover:bg-slate-200 transition-colors flex items-center gap-1.5">
              <i data-lucide="pencil" class="w-3 h-3"></i> Edit
            </button>
            <button @click="detailModal = false; $dispatch('open-confirm-modal', { 
                              title: 'Delete Partner', 
                              message: 'Are you sure you want to delete this partner account? This action cannot be undone.', 
                              onConfirm: () => submitDeleteForm('/admin/partners/' + selectedPartner.id)
                           })" 
                class="px-3 py-1.5 bg-red-50 text-red-500 border border-red-200 text-xs font-bold rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1.5"
                title="Delete Partner">
              <i data-lucide="trash-2" class="w-3 h-3"></i> Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div x-show="editModal" x-cloak style="display:none"
       class="fixed inset-0 z-[100] overflow-y-auto" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="editModal = false"></div>
     <div class="flex min-h-full items-center justify-center p-4" @click="editModal = false">
      <div @click.stop class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
        <div class="bg-slate-50 px-5 py-4 border-b border-slate-100 flex items-center justify-between">
          <h3 class="font-bold text-kingdom-navy">Edit Partner Details</h3>
          <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <div class="p-5">
          <form :action="'/admin/partners/' + editForm.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                   <label class="block text-xs font-bold text-slate-600 mb-1">Full Name</label>
                   <input type="text" name="name" x-model="editForm.name" required class="w-full rounded-lg border-slate-200 text-sm focus:border-kingdom-gold focus:ring focus:ring-kingdom-gold/20">
                </div>
                <div>
                   <label class="block text-xs font-bold text-slate-600 mb-1">Email</label>
                   <input type="email" name="email" x-model="editForm.email" required class="w-full rounded-lg border-slate-200 text-sm focus:border-kingdom-gold focus:ring focus:ring-kingdom-gold/20">
                </div>
                <div>
                   <label class="block text-xs font-bold text-slate-600 mb-1">Phone</label>
                   <input type="text" name="phone" x-model="editForm.phone" class="w-full rounded-lg border-slate-200 text-sm focus:border-kingdom-gold focus:ring focus:ring-kingdom-gold/20">
                </div>
                <div>
                   <label class="block text-xs font-bold text-slate-600 mb-1">Company</label>
                   <input type="text" name="company_name" x-model="editForm.company_name" class="w-full rounded-lg border-slate-200 text-sm focus:border-kingdom-gold focus:ring focus:ring-kingdom-gold/20">
                </div>
            </div>
            
            <div>
               <label class="block text-xs font-bold text-slate-600 mb-1">Website</label>
               <input type="text" name="website" x-model="editForm.website" class="w-full rounded-lg border-slate-200 text-sm focus:border-kingdom-gold focus:ring focus:ring-kingdom-gold/20">
            </div>

            <div>
               <label class="block text-xs font-bold text-slate-600 mb-1">Address</label>
               <input type="text" name="address" x-model="editForm.address" class="w-full rounded-lg border-slate-200 text-sm focus:border-kingdom-gold focus:ring focus:ring-kingdom-gold/20">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
               <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-lg hover:bg-slate-200">Cancel</button>
               <button type="submit" class="px-4 py-2 bg-kingdom-navy text-white font-bold rounded-lg hover:bg-slate-800">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
window.partnersManager = function partnersManager() {
    return {
        allPartners: @json($partners->items()),
        searchQuery: '{{ request('search', '') }}',
        statusFilter: '{{ request('status', '') }}',
        detailModal: false,
        editModal: false,
        editForm: {},
        selectedPartner: null,

        init() {
            this.$nextTick(() => lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }));
        },

        get filteredPartners() {
            return this.allPartners;
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

        async toggleStatus(partner) {
            const newStatus = !partner.is_active;
            const orig = partner.is_active;
            partner.is_active = newStatus;

            try {
                const response = await fetch(`/admin/users/${partner.id}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ is_active: newStatus })
                });
                if (!response.ok) partner.is_active = orig;
            } catch {
                partner.is_active = orig;
            }
        },

        openDetail(partner) {
            this.selectedPartner = partner;
            this.detailModal = true;
        },

        openEditModal(partner) {
            this.editForm = { ...partner };
            this.editModal = true;
        },

        copyEmail(email, btn) {
            if (!email) return;
            navigator.clipboard.writeText(email);
            const icon = btn.querySelector('i');
            if (icon) {
                const oldIcon = icon.getAttribute('data-lucide');
                icon.setAttribute('data-lucide', 'check');
                icon.classList.add('text-emerald-500');
                lucide.createIcons({ nodes: [icon] });
                setTimeout(() => {
                    icon.setAttribute('data-lucide', oldIcon);
                    icon.classList.remove('text-emerald-500');
                    lucide.createIcons({ nodes: [icon] });
                }, 1500);
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    };
}
</script>
@endsection
