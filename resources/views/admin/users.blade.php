@extends('layouts.admin')

@section('title', 'Users | Kingdom Admin')

@section('content')
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<div x-data="usersTable()" @keydown.escape.window="editingId = null" class="h-full flex flex-col space-y-3">

    <!-- Page Header & Stats -->
    <div class="dashboard-header flex flex-col sm:flex-row sm:items-center justify-between gap-2 shrink-0">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">System Users</h1>
            <p class="text-slate-500 text-xs">Manage administrators, partners & applicants</p>
        </div>
        
        <div class="flex gap-3">


             <div class="relative" x-data="{ openExport: false }" @keydown.escape.window="openExport = false">
                <div class="flex gap-2">
                    <button @click="if(selectMode) { selectedRows = []; selectAll = false; } selectMode = !selectMode" 
                            class="flex items-center gap-2 border border-slate-200 font-bold py-2.5 px-4 rounded-xl transition-all shadow-sm active:scale-95"
                            :class="selectMode ? 'bg-kingdom-gold text-white border-kingdom-gold hover:bg-kingdom-gold/90' : 'bg-white text-kingdom-navy hover:bg-slate-50 hover:text-kingdom-gold'">
                        <i data-lucide="check-square" class="w-4 h-4"></i>
                        <span class="text-sm" x-text="selectMode ? 'Done' : 'Select'"></span>
                    </button>

                    <button @click="openExport = true" class="flex items-center gap-2 bg-white border border-slate-200 text-kingdom-navy hover:bg-slate-50 hover:text-kingdom-gold font-bold py-2.5 px-4 rounded-xl transition-all shadow-sm active:scale-95">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span class="text-sm">Export</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 ml-1 transition-transform duration-200" :class="{'rotate-180': openExport}"></i>
                    </button>
                </div>
                
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

                    <div class="flex min-h-full items-center justify-center p-4 text-center" @click="openExport = false">
                        <div x-show="openExport"
                             @click.stop
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
                                        <!-- Date Range -->
                                        <label class="cursor-pointer relative col-span-2">
                                            <input type="radio" name="exportType" value="date_range" x-model="exportType" class="peer sr-only">
                                            <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-kingdom-gold peer-checked:bg-kingdom-gold/5 transition-all text-center hover:bg-slate-50 flex items-center justify-center gap-2">
                                                <i data-lucide="calendar-range" class="w-4 h-4 text-slate-400 peer-checked:text-kingdom-gold"></i>
                                                <span class="block text-sm font-bold text-slate-700 peer-checked:text-kingdom-gold">Date Range</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Limit Input (Show only for Last/First) -->
                                <div x-show="exportType === 'last' || exportType === 'first'" x-transition class="space-y-2">
                                     <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Quantity</label>
                                     <input type="number" x-model="exportLimit" min="1" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none">
                                </div>

                                <!-- Date Range Inputs -->
                                <div x-show="exportType === 'date_range'" x-transition class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Start Date</label>
                                        <input type="date" x-model="startDate" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none text-xs">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">End Date</label>
                                        <input type="date" x-model="endDate" class="w-full px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 font-bold text-kingdom-navy outline-none text-xs">
                                    </div>
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
        </div>
    </div>

    <!-- 3D Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 shrink-0">
        <!-- Total Users -->
        <div @click="filters.role = ''" 
             class="bg-white px-3 py-2.5 rounded-xl border border-blue-100 hover:border-blue-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group flex items-center justify-between relative cursor-pointer"
             :class="filters.role === '' ? 'ring-1 ring-blue-500 ring-offset-1' : ''">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-blue-100/30"></div>
            <div class="relative z-10 h-8 w-8 shrink-0 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-sm flex items-center justify-center group-hover:scale-105 transition-transform">
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
            <div class="relative z-10 text-right min-w-0 flex-1 pl-2">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Total Users</p>
                <p class="text-lg font-extrabold text-slate-800 leading-tight group-hover:text-blue-600 transition-colors">{{ $stats['total'] }}</p>
            </div>
        </div>

        <!-- Applicants / Applicants -->
        <div @click="filters.role = 'applicant'"
             class="bg-white px-3 py-2.5 rounded-xl border border-emerald-100 hover:border-emerald-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group flex items-center justify-between relative cursor-pointer"
             :class="filters.role === 'applicant' ? 'ring-1 ring-emerald-500 ring-offset-1' : ''">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/30 to-emerald-100/30"></div>
            <div class="relative z-10 h-8 w-8 shrink-0 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-sm flex items-center justify-center group-hover:scale-105 transition-transform">
                <i data-lucide="user-check" class="w-4 h-4"></i>
            </div>
            <div class="relative z-10 text-right min-w-0 flex-1 pl-2">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Applicants</p>
                <p class="text-lg font-extrabold text-slate-800 leading-tight group-hover:text-emerald-600 transition-colors">{{ $stats['applicants'] }}</p>
            </div>
        </div>

        <!-- Partners -->
        <div @click="filters.role = 'partner'"
             class="bg-white px-3 py-2.5 rounded-xl border border-violet-100 hover:border-violet-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group flex items-center justify-between relative cursor-pointer"
             :class="filters.role === 'partner' ? 'ring-1 ring-violet-500 ring-offset-1' : ''">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-50/30 to-violet-100/30"></div>
            <div class="relative z-10 h-8 w-8 shrink-0 rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 text-white shadow-sm flex items-center justify-center group-hover:scale-105 transition-transform">
                <i data-lucide="handshake" class="w-4 h-4"></i>
            </div>
            <div class="relative z-10 text-right min-w-0 flex-1 pl-2">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Partners</p>
                <p class="text-lg font-extrabold text-slate-800 leading-tight group-hover:text-violet-600 transition-colors">{{ $stats['partners'] }}</p>
            </div>
        </div>

        <!-- Team -->
        <div @click="filters.role = 'staff'"
             class="bg-white px-3 py-2.5 rounded-xl border border-amber-100 hover:border-amber-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group flex items-center justify-between relative cursor-pointer"
             :class="filters.role === 'staff' ? 'ring-1 ring-kingdom-gold ring-offset-1' : ''">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50/30 to-amber-100/30"></div>
            <div class="relative z-10 h-8 w-8 shrink-0 rounded-lg bg-gradient-to-br from-kingdom-gold to-amber-500 text-white shadow-sm flex items-center justify-center group-hover:scale-105 transition-transform">
                <i data-lucide="briefcase" class="w-4 h-4"></i>
            </div>
            <div class="relative z-10 text-right min-w-0 flex-1 pl-2">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Team</p>
                <p class="text-lg font-extrabold text-slate-800 leading-tight group-hover:text-kingdom-gold transition-colors">{{ $stats['staff'] }}</p>
            </div>
        </div>

        <!-- Admins -->
         <div @click="filters.role = 'admin'"
              class="bg-white px-3 py-2.5 rounded-xl border border-indigo-100 hover:border-indigo-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group flex items-center justify-between relative cursor-pointer"
              :class="filters.role === 'admin' ? 'ring-1 ring-indigo-500 ring-offset-1' : ''">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/30 to-indigo-100/30"></div>
            <div class="relative z-10 h-8 w-8 shrink-0 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-sm flex items-center justify-center group-hover:scale-105 transition-transform">
                <i data-lucide="shield" class="w-4 h-4"></i>
            </div>
            <div class="relative z-10 text-right min-w-0 flex-1 pl-2">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Admins</p>
                <p class="text-lg font-extrabold text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $stats['admins'] }}</p>
            </div>
        </div>
    </div>

    <!-- Users Content -->
    <div class="anim-fade-in-up bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col w-full self-start relative">

        <!-- Standard Filters above table -->
        <div class="p-4 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 bg-slate-50/50 shrink-0">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" x-model="filters.search" @keyup.enter="applyFilters()" placeholder="Search users..." class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                </div>

                <!-- Status Filter -->
                <select x-model="filters.status" @change="applyFilters()" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                <!-- Role Filter -->
                <select x-model="filters.role" @change="applyFilters()" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                    <option value="">All Roles</option>
                    <option value="applicant">Applicant</option>
                    <option value="partner">Partner</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Team</option>
                </select>

                <!-- Clear Filters Button -->
                <button @click="clearFilters()" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-kingdom-navy transition-colors flex items-center gap-1" x-show="hasActiveFilters">
                    <i data-lucide="x-circle" class="w-4 h-4"></i> Clear
                </button>
            </div>

            {{-- Select / Export live in the page header above — kept here once, not duplicated. --}}
        </div>

        <!-- Scrollable Content Area -->
        <div class="overflow-auto w-full max-h-[calc(100vh-210px)] admin-table-container flex-1 p-0 relative">
            <!-- LIST VIEW -->
            <div class="w-full">
                <table class="w-full min-w-[1000px] text-left border-collapse admin-table">
                    <thead class="sticky top-0 z-20 bg-[#0f1f3d] shadow-sm">
                        <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-[11px] border-b border-slate-700">
                            <template x-if="selectMode">
                                 <th class="py-2.5 px-3 text-center w-10 border-r border-slate-700/50 bg-[#0f1f3d]">
                                     <input type="checkbox" @change="toggleSelectAll()" x-model="selectAll" class="rounded border-slate-300 text-kingdom-gold focus:ring-kingdom-gold w-3.5 h-3.5 cursor-pointer">
                                 </th>
                            </template>
                            <th class="py-2.5 px-3 text-[11px] border-r border-slate-700/50 text-center w-16 bg-[#0f1f3d]">ID</th>
                            <th class="py-2.5 px-3 text-[11px] border-r border-slate-700/50 bg-[#0f1f3d]">User Identity</th>
                            <th class="py-2.5 px-3 text-[11px] border-r border-slate-700/50 w-[26%] bg-[#0f1f3d]">Contact</th>
                            <th class="py-2.5 px-3 text-[11px] border-r border-slate-700/50 w-[15%] bg-[#0f1f3d]">Role & Access</th>
                            <th class="py-2.5 px-3 text-[11px] border-r border-slate-700/50 text-center w-28 bg-[#0f1f3d]">Status</th>
                            <th class="py-2.5 px-3 text-[11px] text-right w-36 bg-[#0f1f3d]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(user, index) in filteredUsers" :key="user.id">
                            <tr @click="selectMode ? toggleRowSelection(user.id) : null" 
                                class="border-b border-slate-100 hover:bg-slate-50 transition-colors"
                                :class="isSelected(user.id) ? 'bg-indigo-50/50' : ''">
                                <template x-if="selectMode">
                                    <td class="py-4 px-4 text-center border-r border-slate-100" @click.stop>
                                        <input type="checkbox" :value="user.id" x-model="selectedRows" class="rounded border-slate-300 text-kingdom-gold focus:ring-kingdom-gold w-3.5 h-3.5 cursor-pointer">
                                    </td>
                                </template>
                                <td class="py-4 px-4 text-sm text-slate-900 font-medium border-r border-slate-100 text-center" x-text="user.id"></td>
                                <td class="py-4 px-4 text-sm border-r border-slate-100">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="h-8 w-8 shrink-0 rounded-full flex items-center justify-center font-bold text-xs ring-2 ring-offset-1 ring-white shadow-sm"
                                             :class="getRoleStyles(user.role).ring">
                                            <span x-text="user.name.substring(0, 2)"></span>
                                        </div>
                                        <span class="font-semibold text-slate-800 text-sm truncate max-w-[150px] inline-block" :title="user.name" x-text="user.name"></span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm border-r border-slate-100">
                                    <a :href="'mailto:' + user.email" class="text-indigo-600 hover:text-indigo-800 hover:underline truncate max-w-[220px] block" :title="user.email" x-text="user.email"></a>
                                    <span class="text-[11px] text-slate-400 font-medium block truncate mt-0.5" x-text="user.phone || 'No phone on file'"></span>
                                </td>
                                <td class="py-4 px-4 text-sm border-r border-slate-100">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border capitalize" 
                                          :class="getRoleStyles(user.role).badge">
                                        <span x-text="user.role"></span>
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-sm text-slate-600 border-r border-slate-100" x-text="formatDate(user.created_at)"></td>
                                <td class="py-4 px-4 text-sm text-right" @click.stop>
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Status Toggle (Standard Status Control) -->
                                        <button @click="toggleStatus(user)"
                                                x-show="user.role !== 'admin' && user.role !== 'Admin'"
                                                class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                                                :class="user.is_active ? 'bg-emerald-500' : 'bg-slate-350'"
                                                title="Toggle Status">
                                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                                                  :class="user.is_active ? 'translate-x-4' : 'translate-x-0'"></span>
                                        </button>
                                        
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-650 transition-colors" @click="openEditUserModal(user)" title="Edit User">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </button>
                                        <template x-if="user.role !== 'admin' && user.id !== {{ auth()->id() }}">
                                            <button @click="$dispatch('open-confirm-modal', { 
                                                        title: 'Delete User', 
                                                        message: 'Are you sure you want to delete this user? This action cannot be undone.', 
                                                        onConfirm: () => submitDeleteForm('/admin/users/' + user.id)
                                                     })" 
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" 
                                                    title="Delete User">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <!-- Empty State (Common) -->
            <div x-show="filteredUsers.length === 0" class="h-full flex flex-col items-center justify-center p-12 text-center text-slate-400">
                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 ring-4 ring-white shadow-sm">
                    <i data-lucide="search" class="w-8 h-8 text-slate-300"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">No users found</h3>
                <p class="text-slate-500 max-w-sm mx-auto mt-2 text-sm">We couldn't find any users matching your filters.</p>
                <button @click="clearFilters()" class="mt-4 text-kingdom-gold font-bold text-sm hover:underline">Clear all filters</button>
            </div>
        </div>
        
        <!-- Pagination Links -->
        <div class="p-4 border-t border-slate-100 bg-white shrink-0">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="editingId" 
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <div x-show="editingId"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="editingId = null"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center" @click="editingId = null">
            <div x-show="editingId"
                 @click.stop
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                    <h3 class="font-bold text-lg text-kingdom-navy">Edit User Config</h3>
                    <button @click="editingId = null" class="bg-white p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="px-6 pb-6 pt-4">
                     <form x-bind:action="'/admin/users/' + editingId" method="POST" id="editUserForm" class="flex flex-col gap-5">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Full Name</label>
                            <input type="text" name="name" x-model="editingForm.name" 
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Email Address</label>
                            <input type="email" name="email" x-model="editingForm.email" 
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none">
                        </div>



                        <div class="flex gap-4 mt-4 pt-2">
                             <button type="button" @click="editingId = null" class="flex-1 px-4 py-3 rounded-xl border-2 border-slate-100 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-800 transition-all">Cancel</button>
                             <button type="submit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 flex items-center justify-center gap-2">
                                <span>Save Changes</span>
                             </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.usersTable = function usersTable() {
            return {
                users: @json($users->items()),
                exportType: 'all',
                exportLimit: 10,
                selectMode: false,
                selectedRows: [],
                selectAll: false,
                filters: {
                    search: '{{ request('search', '') }}',
                    role: '{{ request('role', '') }}',
                    status: '{{ request('status', '') }}'
                },
                roleOptions: [
                    { value: '', label: 'All Roles' },
                    { value: 'applicant', label: 'Applicant' },
                    { value: 'partner', label: 'Partner' },
                    { value: 'admin', label: 'Admin' },
                    { value: 'staff', label: 'Team' }
                ],
                categoryOptions: [
                    { value: '', label: 'All Realms' },
                    ...@json($categories->map(fn($c) => ['value' => $c->name, 'label' => $c->name]))
                ],
                showFilters: true,
                editingId: null,
                editingForm: { name: '', email: '', role: '' },
                startDate: '',
                endDate: '',

                toggleRowSelection(id) {
                    if (this.selectedRows.includes(id)) {
                        this.selectedRows = this.selectedRows.filter(rowId => rowId !== id);
                    } else {
                        this.selectedRows.push(id);
                    }
                },

                isSelected(id) {
                    return this.selectedRows.includes(id);
                },

                getRowClasses(id, index) {
                    if (this.isSelected(id)) {
                        return 'bg-indigo-50/80';
                    }
                    return index % 2 === 0 ? 'bg-slate-100/60 hover:bg-indigo-50' : 'bg-white hover:bg-indigo-50';
                },

                init() {
                    this.$watch('editingId', val => { 
                        document.body.style.overflow = val ? 'hidden' : '';
                        this.$nextTick(() => lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') })); 
                    });

                    this.$watch('filters', () => { 
                         this.$nextTick(() => lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') })); 
                    }, {deep: true});
                    
                    // Initial render for icons
                    this.$nextTick(() => lucide.createIcons({ nodes: this.$el.querySelectorAll('[data-lucide]') }));
                },
                
                get filteredUsers() {
                    return this.users;
                },
                
                applyFilters() {
                    let url = new URL(window.location.href);
                    if (this.filters.search) {
                        url.searchParams.set('search', this.filters.search);
                    } else {
                        url.searchParams.delete('search');
                    }
                    if (this.filters.role) {
                        url.searchParams.set('role', this.filters.role);
                    } else {
                        url.searchParams.delete('role');
                    }
                    if (this.filters.status) {
                        url.searchParams.set('status', this.filters.status);
                    } else {
                        url.searchParams.delete('status');
                    }
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                },

                get selectedRoleLabel() {
                    return this.roleOptions.find(o => o.value === this.filters.role)?.label || 'All Roles';
                },
                
                get selectedRealmLabel() {
                    return this.categoryOptions.find(o => o.value === this.filters.discipline)?.label || 'All Realms';
                },
                
                get hasActiveFilters() {
                    return this.filters.search || this.filters.role || this.filters.status;
                },
                
                clearFilters() {
                    this.filters.search = '';
                    this.filters.role = '';
                    this.filters.status = '';
                    this.applyFilters();
                },

                async toggleStatus(user) {
                    const newStatus = !user.is_active;
                    try {
                        const response = await fetch(`/admin/users/${user.id}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ is_active: newStatus })
                        });
                        
                        if (response.ok) {
                            user.is_active = newStatus;
                        } else {
                            Swal.fire({ icon: 'error', title: 'Update Failed', text: 'Failed to update status', confirmButtonColor: '#0F1D33' });
                        }
                    } catch (error) {
                        console.error('Error updating status:', error);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred', confirmButtonColor: '#0F1D33' });
                    }
                },

                getRoleStyles(role) {
                    const r = (role || '').toLowerCase();
                    if (r === 'admin') {
                        return { 
                            ring: 'ring-kingdom-navy/20 text-kingdom-navy bg-kingdom-navy/5',
                            badge: 'bg-kingdom-navy/10 text-kingdom-navy border-kingdom-navy/20'
                        };
                    } else if (r === 'staff' || r === 'partner') {
                        return {
                            ring: 'ring-kingdom-gold/20 text-kingdom-gold bg-kingdom-gold/5',
                            badge: 'bg-kingdom-gold/10 text-kingdom-gold border-kingdom-gold/20'
                        };
                    } else if (r === 'applicant' || r === 'applicant') {
                        return {
                            ring: 'ring-emerald-200 text-emerald-700 bg-emerald-50',
                            badge: 'bg-white text-emerald-700 border-emerald-200 ring-1 ring-emerald-50'
                        };
                    } else {
                        return {
                            ring: 'ring-slate-200 text-slate-600 bg-slate-50',
                            badge: 'bg-slate-50 text-slate-600 border-slate-200'
                        };
                    }
                },
                
                formatDate(dateString) {
                    if(!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                },
                
                formatTime(dateString) {
                    if(!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                },

                triggerExport(format) {
                    let url = `{{ route('admin.users.export') }}?format=${format}&type=${this.exportType}`;
                    
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
                    if (this.selectAll) {
                        this.selectedRows = this.filteredUsers.map(u => u.id);
                    } else {
                        this.selectedRows = [];
                    }
                },

                openEditUserModal(user) {
                    this.editingId = user.id;
                    this.editingForm.name = user.name;
                    this.editingForm.email = user.email;
                    this.editingForm.role = user.role;
                },
                downloadExcel() {
                    const usersToExport = this.filteredUsers.map(user => ({
                        'User': user.name,
                        'Email ID': user.email,
                        'Mobile No.': user.phone || 'N/A',
                        'Role': (user.role === 'applicant' || user.role === 'applicant') ? 'Applicant' : user.role,
                        'Joined': this.formatDate(user.created_at),
                        'Status': user.is_active ? 'Active' : 'Inactive'
                    }));
                    
                    const ws = XLSX.utils.json_to_sheet(usersToExport);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Users");
                    XLSX.writeFile(wb, "system_users_export.xlsx");
                    this.openExport = false;
                },

                downloadPDF() {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    
                    const tableColumn = ["User", "Email ID", "Mobile No.", "Role", "Joined", "Status"];
                    const tableRows = [];

                    this.filteredUsers.forEach(user => {
                        const userData = [
                            user.name,
                            user.email,
                            user.phone || 'N/A',
                            (user.role === 'applicant' || user.role === 'applicant') ? 'Applicant' : user.role,
                            this.formatDate(user.created_at),
                            user.is_active ? 'Active' : 'Inactive'
                        ];
                        tableRows.push(userData);
                    });

                    doc.autoTable({
                        head: [tableColumn],
                        body: tableRows,
                        startY: 20,
                        theme: 'grid',
                        styles: { fontSize: 8, cellPadding: 2 },
                        headStyles: { fillColor: [15, 29, 51], textColor: [255, 255, 255] },
                    });
                    
                    doc.text("System Users Report", 14, 15);
                    doc.save("system_users_report.pdf");
                    this.openExport = false;
                },

            };
        }
    </script>


</div>
@endsection