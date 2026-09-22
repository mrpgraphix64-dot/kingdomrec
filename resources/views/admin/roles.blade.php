@extends('layouts.admin')

@section('title', 'Access Permission | Kingdom Admin')

@section('content')

<div x-data="rolesTable()" @keydown.escape.window="editingId = null; showAddModal = false;" class="h-full flex flex-col space-y-6">

    <!-- Page Header & Stats -->
    <div class="dashboard-header flex flex-col md:flex-row md:items-end justify-between gap-4 shrink-0">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Access Permission</h1>
            <p class="text-slate-500 font-medium text-sm">Create and modify dynamic permission sets for your administrators</p>
        </div>
        
        <div class="flex gap-3">
            <button @click="openAddRoleModal()" class="flex items-center gap-2 bg-[#0F1D33] text-white hover:bg-slate-800 font-bold py-2.5 px-4 rounded-xl transition-all shadow-sm">
                <i data-lucide="key-round" class="w-4 h-4"></i>
                <span class="text-sm">Create Role</span>
            </button>
        </div>
    </div>


    <!-- Content -->
    <div class="anim-fade-in-up bg-white rounded-3xl border border-slate-200/60 shadow-2xl shadow-blue-900/5 overflow-hidden flex flex-col flex-1 min-h-0 relative mt-6">
        <div class="overflow-y-auto overflow-x-auto section-scroll flex-1 relative bg-slate-50/50 p-6 custom-scrollbar">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Role Card -->
                <template x-for="(role, index) in roles" :key="role.id">
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden relative group">
                        <!-- Top Accent -->
                        <div class="h-1.5 w-full transition-colors" 
                             :class="index === 0 ? 'bg-kingdom-gold' : 'bg-kingdom-navy group-hover:bg-kingdom-gold'"></div>
                        
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-black text-slate-800" x-text="role.name"></h3>
                                    <span class="inline-flex mt-2 bg-slate-100 text-slate-500 text-[10px] uppercase font-bold tracking-wider px-2 py-1 rounded">
                                        <span x-text="role.permissions.length"></span>&nbsp;POWERS ATTACHED
                                    </span>
                                </div>
                                <div class="h-12 w-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:text-kingdom-gold transition-colors">
                                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                                </div>
                            </div>
                            
                            <div class="mt-2 space-y-2 flex-1">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Granted Powers:</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="perm in role.permissions" :key="perm.id">
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-kingdom-navy/5 text-kingdom-navy border border-kingdom-navy/10 text-[10px] font-medium" x-text="formatPermissionName(perm.name)"></span>
                                    </template>
                                    <div x-show="role.permissions.length === 0" class="text-xs text-slate-400 italic">No specific powers completely assigned.</div>
                                </div>
                            </div>
                            
                            <div class="mt-8 pt-4 border-t border-slate-100 flex gap-2">
                                <button @click="openEditRoleModal(role)" class="flex-1 py-2.5 text-sm font-bold text-kingdom-navy hover:bg-slate-50 rounded-lg transition-colors border border-slate-200 hover:border-kingdom-navy text-center">
                                    Edit Role
                                </button>
                                <template x-if="role.name !== 'Master Admin'">
                                    <button @click="$dispatch('open-confirm-modal', { 
                                                title: 'Delete Role', 
                                                message: 'This will destroy the role. Any admin mapped to this role remains, but they will lose these defined powers. This action cannot be undone.', 
                                                onConfirm: () => submitDeleteForm('/admin/roles/' + role.id)
                                             })" 
                                            class="px-3.5 py-2.5 text-red-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-200" 
                                            title="Delete Role">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <div x-show="roles.length === 0" class="flex flex-col items-center justify-center p-12 text-center text-slate-400 h-64">
                <h3 class="text-lg font-bold text-slate-900">No roles found</h3>
                <p class="text-sm">Run the permission seeder to establish defaults.</p>
            </div>
        </div>
    </div>


    <!-- Add/Edit Modal -->
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
            <div @click.stop class="relative w-full max-w-xl transform rounded-2xl bg-white text-left shadow-2xl transition-all">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                    <h3 class="font-bold text-lg text-kingdom-navy" x-text="editingId ? 'Edit Configuration' : 'Create Custom Role'"></h3>
                    <button @click="closeModal()" class="bg-white p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors shadow-sm border border-slate-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="px-6 pb-6 pt-4">
                     <form :action="editingId ? `/admin/roles/${editingId}` : '/admin/roles'" method="POST" class="flex flex-col gap-6">
                        @csrf
                        <template x-if="editingId">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Role Title</label>
                            <input type="text" name="name" x-model="form.name" required
                                   :disabled="form.name === 'Master Admin' && editingId"
                                   placeholder="e.g. Content Manager"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/10 placeholder:text-slate-400 text-sm font-bold text-kingdom-navy transition-all outline-none disabled:opacity-50">
                        </div>

                        <!-- Permissions Checklist -->
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Assign Powers</label>
                            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto">
                                @foreach($permissions as $perm)
                                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-white hover:bg-kingdom-gold/5 cursor-pointer transition-colors has-[:checked]:border-kingdom-gold has-[:checked]:ring-1 has-[:checked]:ring-kingdom-gold">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" x-model="form.permissions"
                                               class="w-4 h-4 text-kingdom-gold border-slate-300 rounded focus:ring-kingdom-gold">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700 capitalize">{{ str_replace('_', ' ', $perm->name) }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-4 mt-4 pt-4 border-t border-slate-100">
                             <button type="button" @click="closeModal()" class="flex-1 px-4 py-3 rounded-xl border-2 border-slate-100 font-bold text-slate-600 hover:bg-slate-50 transition-all">Cancel</button>
                             <button type="submit" class="flex-1 bg-kingdom-navy hover:bg-slate-800 text-white py-3 rounded-xl font-bold transition-all shadow-lg flex items-center justify-center gap-2">
                                <span x-text="editingId ? 'Save Changes' : 'Publish Role'"></span>
                             </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        window.rolesTable = function rolesTable() {
            return {
                roles: @json($roles),
                showAddModal: false,
                editingId: null,
                form: { name: '', permissions: [] },

                init() {
                    this.$watch('showAddModal', () => { this.$nextTick(() => lucide.createIcons()); });
                    this.$watch('editingId', () => { this.$nextTick(() => lucide.createIcons()); });
                    this.$nextTick(() => lucide.createIcons());
                },

                formatPermissionName(name) {
                    return name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                },

                openAddRoleModal() {
                    this.form = { name: '', permissions: [] };
                    this.editingId = null;
                    this.showAddModal = true;
                },

                openEditRoleModal(role) {
                    // Extract permission names into an array
                    const perms = role.permissions.map(p => p.name);
                    this.form = { 
                        name: role.name, 
                        permissions: perms
                    };
                    this.editingId = role.id;
                    this.showAddModal = false;
                },
                
                closeModal() {
                    this.showAddModal = false;
                    this.editingId = null;
                },


            }
        }
    </script>
</div>
@endsection
