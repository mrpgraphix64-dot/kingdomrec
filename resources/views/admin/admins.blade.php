@extends('layouts.admin')

@section('title', 'Admin Management | Kingdom Admin')

@section('content')

<div x-data="adminsTable()" @keydown.escape.window="editingId = null; showAddModal = false; showRolesModal = false;" class="h-full flex flex-col space-y-3">

    <!-- Page Header & Stats -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 shrink-0 mb-1">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Admin Users</h1>
            <p class="text-slate-500 font-medium text-xs">Manage administrator accounts and their assigned roles</p>
        </div>
        
        <div class="flex gap-2.5">
            <button @click="showRolesModal = true; $nextTick(() => lucide.createIcons())" class="flex items-center gap-1.5 bg-white border border-gray-200 text-slate-700 hover:bg-slate-50 font-bold py-2 px-3 rounded-xl transition-all shadow-sm active:scale-95 text-xs">
                <i data-lucide="key" class="w-3.5 h-3.5"></i>
                <span>Access Permission</span>
            </button>
            <button @click="openAddAdminModal()" class="flex items-center gap-1.5 bg-[#0F1D33] text-white hover:bg-slate-800 font-bold py-2 px-3.5 rounded-xl transition-all shadow-sm text-xs">
                <i data-lucide="shield-plus" class="w-3.5 h-3.5"></i>
                <span>+ Add Admin</span>
            </button>
        </div>
    </div>


    <!-- Admins Content Card -->
    <div class="anim-fade-in-up bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden w-full self-start">
        <!-- Filter Bar placed above table -->
        <div class="p-4 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 bg-slate-50/50 shrink-0">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" x-model="searchQuery" @keyup.enter="applyFilters()" placeholder="Search by name or email..." class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                </div>

                <!-- Status Filter -->
                <select x-model="statusFilter" @change="applyFilters()" class="pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <!-- Role Filter -->
                <select x-model="roleFilter" @change="applyFilters()" class="pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-1 focus:ring-kingdom-gold">
                    <option value="">All Roles</option>
                    <template x-for="role in allRoles" :key="role.id">
                        <option :value="role.name" x-text="role.name" :selected="role.name === roleFilter"></option>
                    </template>
                </select>

                <!-- Clear Filters Button -->
                <button @click="clearFilters()" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-kingdom-navy transition-colors flex items-center gap-1" x-show="searchQuery || statusFilter || roleFilter">
                    <i data-lucide="x-circle" class="w-4 h-4"></i> Clear
                </button>
            </div>
        </div>

        <div class="overflow-auto w-full max-h-[calc(100vh-210px)] admin-table-container" x-show="filteredAdmins.length > 0">
            <table class="w-full min-w-[900px] text-left border-collapse admin-table">
                <thead class="sticky top-0 z-20 bg-[#0f1f3d] shadow-sm">
                    <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-[11px] border-b border-slate-700">
                        <th class="py-2.5 px-3 w-16 text-center border-r border-slate-700/50 bg-[#0f1f3d]">ID</th>
                        <th class="py-2.5 px-3 text-left border-r border-slate-700/50 bg-[#0f1f3d]">Name</th>
                        <th class="py-2.5 px-3 text-left border-r border-slate-700/50 bg-[#0f1f3d]">Email</th>
                        <th class="py-2.5 px-3 text-left border-r border-slate-700/50 bg-[#0f1f3d]">Mobile</th>
                        <th class="py-2.5 px-3 text-left w-[20%] border-r border-slate-700/50 bg-[#0f1f3d]">Role</th>
                        <th class="py-2.5 px-3 text-center w-24 border-r border-slate-700/50 bg-[#0f1f3d]">Status</th>
                        <th class="py-2.5 px-3 text-right w-32 bg-[#0f1f3d]">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-transparent">
                    <template x-for="(admin, index) in filteredAdmins" :key="admin.id">
                        <tr class="transition-colors duration-150 border-b border-slate-100 hover:bg-slate-50 bg-white">
                            <!-- ID -->
                            <td class="py-4 px-4 text-center border-r border-slate-100">
                                <span class="inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono text-[10px] font-bold border border-indigo-100 group-hover:bg-[#0f1f3d] group-hover:text-white group-hover:border-[#0f1f3d] transition-colors shadow-sm" x-text="index + 1"></span>
                            </td>
                            <!-- Name -->
                            <td class="py-4 px-4 border-r border-slate-100">
                                <div class="flex items-center gap-3 py-1 min-w-0">
                                    <!-- Avatar initials bubble -->
                                    <div class="h-9 w-9 shrink-0 rounded-full flex items-center justify-center font-bold text-sm bg-indigo-100 text-indigo-700 ring-2 ring-indigo-200 ring-offset-1">
                                        <span x-text="admin.name.substring(0, 2).toUpperCase()"></span>
                                    </div>
                                    <span class="font-semibold text-slate-800 text-sm truncate max-w-[180px]" :title="admin.name" x-text="admin.name"></span>
                                </div>
                            </td>
                            <!-- Email -->
                            <td class="py-4 px-4 border-r border-slate-100">
                                <a :href="'mailto:' + admin.email" class="text-indigo-600 hover:text-indigo-800 hover:underline text-xs font-semibold truncate max-w-[220px] flex items-center gap-1.5" :title="admin.email">
                                    <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                    <span x-text="admin.email"></span>
                                </a>
                            </td>
                            <!-- Mobile -->
                            <td class="py-4 px-4 border-r border-slate-100">
                                <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                    <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                    <template x-if="admin.phone">
                                        <span class="font-medium text-slate-700" x-text="admin.phone"></span>
                                    </template>
                                    <template x-if="!admin.phone">
                                        <span class="text-slate-350 italic">No mobile</span>
                                    </template>
                                </div>
                            </td>
                            <!-- Role -->
                            <td class="py-4 px-4 border-r border-slate-100">
                                <span :class="getRoleBadgeClass(admin)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold capitalize text-center" x-text="getPrimaryRole(admin) || 'No Role'"></span>
                            </td>
                            <!-- Status -->
                            <td class="py-4 px-4 text-center border-r border-slate-100" @click.stop>
                                <button @click="toggleStatus(admin)"
                                        :disabled="admin.id === {{ auth()->id() }}"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold focus-visible:ring-offset-2"
                                        :class="[
                                            admin.is_active ? 'bg-emerald-500' : 'bg-slate-350',
                                            admin.id === {{ auth()->id() }} ? 'opacity-50 cursor-not-allowed' : ''
                                        ]"
                                        role="switch" :aria-checked="admin.is_active"
                                        :title="admin.id === {{ auth()->id() }} ? 'You cannot deactivate your own account' : 'Toggle Status'">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                                          :class="admin.is_active ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                            </td>
                            <!-- Actions -->
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit Button -->
                                    <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-indigo-50 hover:bg-indigo-100 text-indigo-500 hover:text-indigo-700 transition" 
                                            @click="openEditAdminModal(admin)" 
                                            title="Edit admin">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <template x-if="canDeleteAdmin(admin)">
                                        <button @click="$dispatch('open-confirm-modal', { 
                                                     title: 'Delete Admin', 
                                                     message: 'Are you sure you want to completely remove this administrator\'s access?', 
                                                     onConfirm: () => submitDeleteForm('/admin/admins/' + admin.id)
                                                  })" 
                                                class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-650 transition" 
                                                title="Delete admin">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </template>
                                    <template x-if="!canDeleteAdmin(admin)">
                                        <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-50 text-red-400 opacity-30 cursor-not-allowed pointer-events-none" 
                                                disabled 
                                                :title="getDeleteDisabledTitle(admin)">
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

        <!-- Empty State — a real full-width block, not a colspan cell inside the wide scrolling table -->
        <div x-show="filteredAdmins.length === 0" class="py-12 text-center">
            <div class="flex flex-col items-center justify-center space-y-3 bg-white">
                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                    <i data-lucide="users" class="w-10 h-10 text-slate-350"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-500">No admins found</h3>
                <p class="text-slate-400 text-sm">Add your first administrator to get started.</p>
                <button @click="openAddAdminModal()" class="mt-2 bg-[#0F1D33] hover:bg-slate-800 text-white font-bold py-2 px-4 rounded-lg transition-all text-sm shadow-sm">
                    + Add Admin
                </button>
            </div>
        </div>

        <!-- Pagination Links -->
        <div class="p-4 border-t border-slate-100 bg-white shrink-0">
            {{ $admins->links() }}
        </div>
    </div>


    <!-- Add/Edit Admin Modal -->
    <div x-show="showAddModal || editingId" 
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-[100] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             @click="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center" @click="closeModal()">
            <div @click.stop class="relative w-full max-w-lg transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                    <h3 class="font-bold text-lg text-kingdom-navy" x-text="editingId ? 'Edit Administrator' : 'Add New Administrator'"></h3>
                    <button @click="closeModal()" class="bg-white p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="px-6 pb-6 pt-4">
                     <form :action="editingId ? '/admin/admins/' + editingId : '/admin/admins'" method="POST" class="flex flex-col gap-4">
                        @csrf
                        <template x-if="editingId">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Full Name</label>
                            <input type="text" name="name" x-model="form.name" required
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Email Address</label>
                            <input type="email" name="email" x-model="form.email" required autocomplete="new-email"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Mobile Number</label>
                            <input type="text" name="phone" x-model="form.phone"
                                   placeholder="e.g. +44 7700 900077"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none">
                        </div>

                        <div class="space-y-1.5" x-show="!editingId">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Password</label>
                            <input type="password" name="password" x-model="form.password" :required="!editingId" autocomplete="new-password"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none">
                        </div>
                        
                        <div class="space-y-1.5" x-show="editingId">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Reset Password (Optional)</label>
                            <input type="password" name="password" x-model="form.password" autocomplete="new-password" placeholder="Leave blank to keep existing password"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none">
                        </div>

                        <div class="space-y-1.5 relative" x-data="{ openRoleSelect: false }" @click.away="openRoleSelect = false">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Assign Powers (Role)</label>
                            <!-- Hidden input for form submission -->
                            <input type="hidden" name="role_name" x-model="form.role_name" required>
                            
                            <!-- Custom Dropdown Button -->
                            <button type="button" @click="openRoleSelect = !openRoleSelect"
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 text-sm font-bold transition-all outline-none flex justify-between items-center group mt-0.5"
                                    :class="openRoleSelect ? 'border-kingdom-gold bg-white ring-4 ring-kingdom-gold/10' : ''">
                                <span class="text-kingdom-navy truncate pr-4" x-text="form.role_name || 'Select a specific Role...'"></span>
                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center group-hover:bg-slate-200 transition-colors shrink-0">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="openRoleSelect ? 'rotate-180' : ''"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="openRoleSelect" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-2"
                                 class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] py-1.5 max-h-56 overflow-y-auto custom-scrollbar ring-1 ring-black/5"
                                 style="display: none;">
                                @foreach($roles as $role)
                                    @if($role->name !== 'Master Admin')
                                        <button type="button" @click="form.role_name = '{{ $role->name }}'; openRoleSelect = false"
                                                class="w-full text-left px-4 py-3 hover:bg-slate-50 text-sm transition-colors flex items-center justify-between group border-b border-slate-50 last:border-0 relative">
                                                
                                            <!-- Active background highlight -->
                                            <div class="absolute inset-x-2 inset-y-1 rounded-lg bg-kingdom-gold/5 transition-opacity"
                                                 :class="form.role_name === '{{ $role->name }}' ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 scale-95 group-hover:scale-100 transition-all'"></div>
                                                 
                                            <span class="font-bold relative z-10" 
                                                  :class="form.role_name === '{{ $role->name }}' ? 'text-kingdom-gold' : 'text-slate-600 group-hover:text-kingdom-navy'">{{ $role->name }}</span>
                                            
                                            <div class="relative z-10 w-4 h-4 rounded-full border flex items-center justify-center transition-all bg-white"
                                                 :class="form.role_name === '{{ $role->name }}' ? 'border-kingdom-gold shadow-sm shadow-kingdom-gold/20' : 'border-slate-300 group-hover:border-slate-400'">
                                                <div class="w-2 h-2 rounded-full bg-kingdom-gold transition-transform duration-300"
                                                     :class="form.role_name === '{{ $role->name }}' ? 'scale-100' : 'scale-0'"></div>
                                            </div>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-4 mt-4 pt-2">
                             <button type="button" @click="closeModal()" class="flex-1 px-4 py-3 rounded-xl border-2 border-slate-100 font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-800 transition-all">Cancel</button>
                             <button type="submit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg shadow-kingdom-navy/20 flex items-center justify-center gap-2">
                                <span x-text="editingId ? 'Save Admin' : 'Create Admin'"></span>
                             </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Access Permission Modal -->
    <div x-show="showRolesModal" x-cloak style="display: none;"
         class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="roles-modal" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showRolesModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4" @click="showRolesModal = false">
            <div @click.stop class="relative w-full max-w-2xl transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-kingdom-gold/5 to-amber-50 rounded-t-2xl">
                    <div>
                        <h3 class="font-bold text-lg text-kingdom-navy">Access Permission</h3>
                        <p class="text-xs text-slate-500">Define what each role can access in the dashboard</p>
                    </div>
                    <button @click="showRolesModal = false" class="bg-white p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <!-- Existing Roles -->
                    <div class="space-y-3 mb-6">
                        <template x-for="role in allRoles" :key="role.id">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 hover:border-slate-200 transition-colors group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" 
                                             :class="role.name === 'Master Admin' ? 'bg-amber-100 text-amber-600' : 'bg-blue-50 text-blue-500'">
                                            <i data-lucide="shield" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-slate-800" x-text="role.name"></h4>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-wider">
                                                <span x-text="role.permissions ? role.permissions.length : 0"></span> permissions
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <template x-if="role.name !== 'Master Admin'">
                                            <button @click="editRole(role)" class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-colors shadow-sm">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </template>
                                        <template x-if="role.name !== 'Master Admin'">
                                            <form :action="'/admin/roles/' + role.id" method="POST" class="inline" @submit.prevent="Swal.fire({ title: 'Delete Role?', text: 'Are you sure you want to delete \'' + role.name + '\'?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#0F1D33', cancelButtonColor: '#94a3b8', confirmButtonText: 'Yes, delete it' }).then((result) => { if (result.isConfirmed) $el.submit() })">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-500 hover:border-red-200 transition-colors shadow-sm">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </form>
                                        </template>
                                    </div>
                                </div>
                                <!-- Permissions Tags -->
                                <div class="flex flex-wrap gap-1.5 mt-3" x-show="role.permissions && role.permissions.length > 0">
                                    <template x-for="perm in role.permissions" :key="perm.id">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-white border border-slate-200 text-slate-500" x-text="perm.name.replace('_', ' ')"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Add/Edit Role Form -->
                    <div class="border-t border-slate-100 pt-5">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3" x-text="editingRoleId ? 'Edit Role' : 'Create New Role'"></h4>
                        <form :action="editingRoleId ? '/admin/roles/' + editingRoleId : '/admin/roles'" method="POST" class="space-y-4">
                            @csrf
                            <template x-if="editingRoleId">
                                <input type="hidden" name="_method" value="PUT">
                            </template>
                            <div>
                                <input type="text" name="name" x-model="roleForm.name" required placeholder="Role name (e.g. Operations Manager)"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Assign Permissions</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg bg-slate-50 border border-slate-200 cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-colors has-[:checked]:bg-kingdom-gold/10 has-[:checked]:border-kingdom-gold/30">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                   :checked="roleForm.permissions.includes('{{ $permission->name }}')"
                                                   class="rounded border-slate-300 text-kingdom-gold focus:ring-kingdom-gold/30">
                                            <span class="text-xs font-bold text-slate-600">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button x-show="editingRoleId" type="button" @click="cancelEditRole()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-colors">Cancel</button>
                                <button type="submit" class="flex-1 bg-kingdom-gold hover:bg-amber-600 text-white py-2.5 rounded-xl font-bold text-sm transition-colors shadow-lg shadow-kingdom-gold/20 flex items-center justify-center gap-2">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    <span x-text="editingRoleId ? 'Update Role' : 'Create Role'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        window.adminsTable = function adminsTable() {
            return {
                admins: @json($admins->items()),
                showAddModal: false,
                editingId: null,
                form: { name: '', email: '', password: '', role_name: '', phone: '' },
                
                // Roles management
                showRolesModal: false,
                allRoles: @json($roles->load('permissions')),
                editingRoleId: null,
                roleForm: { name: '', permissions: [] },

                searchQuery: '{{ request('search', '') }}',
                roleFilter: '{{ request('role_filter', '') }}',
                statusFilter: '{{ request('status', '') }}',

                get filteredAdmins() {
                    return this.admins;
                },

                applyFilters() {
                    const params = new URLSearchParams();
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.statusFilter) params.set('status', this.statusFilter);
                    if (this.roleFilter) params.set('role_filter', this.roleFilter);
                    window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                },

                clearFilters() {
                    this.searchQuery = '';
                    this.statusFilter = '';
                    this.roleFilter = '';
                    window.location.href = window.location.pathname;
                },

                getRoleBadgeClass(admin) {
                    const role = this.getPrimaryRole(admin);
                    if (role === 'Master Admin') {
                        return 'bg-violet-100 text-violet-700 border border-violet-300';
                    } else if (role === 'Manager') {
                        return 'bg-blue-100 text-blue-700 border border-blue-300';
                    } else if (role === 'Editor') {
                        return 'bg-amber-100 text-amber-700 border border-amber-300';
                    } else if (role === 'Viewer') {
                        return 'bg-slate-100 text-slate-600 border border-slate-300';
                    } else {
                        return 'bg-slate-100 text-slate-600 border border-slate-300';
                    }
                },

                canDeleteAdmin(admin) {
                    // Cannot delete themselves
                    if (admin.id === {{ auth()->id() }}) return false;
                    // Cannot delete Master Admin
                    if (this.getPrimaryRole(admin) === 'Master Admin') return false;
                    return true;
                },

                getDeleteDisabledTitle(admin) {
                    if (admin.id === {{ auth()->id() }}) {
                        return "Cannot delete yourself";
                    }
                    if (this.getPrimaryRole(admin) === 'Master Admin') {
                        return "Cannot delete master admin";
                    }
                    return "";
                },

                init() {
                    this.$watch('showAddModal', val => { 
                        document.body.style.overflow = (val || this.editingId || this.showRolesModal) ? 'hidden' : '';
                        this.$nextTick(() => lucide.createIcons()); 
                    });
                    this.$watch('editingId', val => { 
                        document.body.style.overflow = (val || this.showAddModal || this.showRolesModal) ? 'hidden' : '';
                        this.$nextTick(() => lucide.createIcons()); 
                    });
                    this.$watch('showRolesModal', val => { 
                        document.body.style.overflow = (val || this.showAddModal || this.editingId) ? 'hidden' : '';
                        this.$nextTick(() => lucide.createIcons()); 
                    });
                    this.$watch('searchQuery', () => { this.$nextTick(() => lucide.createIcons()); });
                    this.$watch('roleFilter', () => { this.$nextTick(() => lucide.createIcons()); });
                    this.$watch('statusFilter', () => { this.$nextTick(() => lucide.createIcons()); });
                    this.$nextTick(() => lucide.createIcons());
                },

                getPrimaryRole(admin) {
                    if (admin.roles && admin.roles.length > 0) {
                        return admin.roles[0].name;
                    }
                    return null;
                },

                openAddAdminModal() {
                    this.form = { name: '', email: '', password: '', role_name: '', phone: '' };
                    this.editingId = null;
                    this.showAddModal = true;
                },

                openEditAdminModal(admin) {
                    this.form = { 
                        name: admin.name, 
                        email: admin.email, 
                        password: '',
                        role_name: this.getPrimaryRole(admin) || '',
                        phone: admin.phone || ''
                    };
                    this.editingId = admin.id;
                    this.showAddModal = false;
                },
                
                closeModal() {
                    this.showAddModal = false;
                    this.editingId = null;
                },



                editRole(role) {
                    this.editingRoleId = role.id;
                    this.roleForm.name = role.name;
                    this.roleForm.permissions = role.permissions ? role.permissions.map(p => p.name) : [];
                    this.$nextTick(() => lucide.createIcons());
                },

                async toggleStatus(admin) {
                    if (admin.id === {{ auth()->id() }}) return;
                    const orig = admin.is_active;
                    const newStatus = !orig;
                    admin.is_active = newStatus;

                    try {
                        const response = await fetch(`/admin/users/${admin.id}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ is_active: newStatus })
                        });
                        if (!response.ok) {
                            admin.is_active = orig;
                            const errData = await response.json();
                            Swal.fire({ icon: 'error', title: 'Action Denied', text: errData.error || 'Failed to toggle status.', confirmButtonColor: '#0F1D33' });
                        }
                    } catch {
                        admin.is_active = orig;
                    }
                },

                cancelEditRole() {
                    this.editingRoleId = null;
                    this.roleForm = { name: '', permissions: [] };
                }
            }
        }
    </script>
</div>
@endsection
