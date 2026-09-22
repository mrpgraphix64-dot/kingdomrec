@extends('layouts.admin')

@section('title', 'Team | Kingdom Admin')

@section('content')
<div class="h-full flex flex-col space-y-3">
    <div class="dashboard-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 shrink-0">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Team Directory</h1>
            <p class="text-slate-500 text-xs">Manage team members, roles & dashboard access</p>
        </div>
        <button 
          onclick="toggleModal('newStaffModal')"
          class="bg-[#0F1D33] hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold text-xs transition-all shadow-sm flex items-center gap-1.5"
        >
          <i data-lucide="plus" class="w-3.5 h-3.5"></i>
          Add Member
        </button>
    </div>

    <div class="flex-1 overflow-y-auto min-h-0 space-y-4 pr-2">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($staffMembers as $member)
        @php
            $dashboardRole = null;
            if ($member->linkedUser && $member->linkedUser->roles->count() > 0) {
                $dashboardRole = $member->linkedUser->roles->first()->name;
            }
        @endphp
        <div class="bg-white p-4 rounded-2xl shadow-sm flex flex-col items-center text-center hover:-translate-y-1 hover:shadow-lg transition-all duration-300 relative group border border-slate-100">
            <!-- Background Glow -->
            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-kingdom-gold/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            
            <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                 <button onclick='openEditModal(@json($member))' class="p-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                </button>
                <form action="{{ route('admin.team.destroy', $member->id) }}" method="POST" @submit.prevent="$dispatch('open-confirm-modal', { title: 'Delete Team Member', message: 'Are you sure you want to delete this team member?', onConfirm: () => submitDeleteForm('{{ route('admin.team.destroy', $member->id) }}') })">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-full bg-red-50 hover:bg-red-100 text-red-500 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
            <div 
                class="w-20 h-20 rounded-full bg-cover bg-center mb-3 shadow-lg ring-3 ring-white relative flex items-center justify-center bg-slate-200 group-hover:scale-105 transition-transform duration-300"
                style="{{ $member->image ? 'background-image: url(' . $member->image . ')' : '' }}"
            >
                @if(!$member->image)
                    <span class="text-2xl font-bold text-slate-400">{{ substr($member->name, 0, 1) }}</span>
                @endif
                <div class="absolute bottom-0.5 right-0.5 w-5 h-5 rounded-full bg-emerald-500 border-3 border-white" title="Active"></div>
            </div>
            <h3 class="text-base font-bold text-slate-900 group-hover:text-kingdom-gold transition-colors">{{ $member->name }}</h3>
            <p class="text-xs font-bold text-slate-400 mb-1 uppercase tracking-wider">{{ $member->role }}</p>
            
            {{-- Dashboard Access Badge --}}
            @if($dashboardRole)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider mb-3
                    {{ $dashboardRole === 'Master Admin' ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                    <i data-lucide="shield-check" class="w-3 h-3"></i>
                    {{ $dashboardRole }}
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-medium text-slate-400 bg-slate-50 border border-slate-100 mb-3">
                    Staff Only
                </span>
            @endif
            
            <div class="flex gap-3 w-full mt-auto">
                <a href="mailto:{{ $member->email }}" class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-slate-50 text-slate-600 hover:bg-blue-50 hover:border-blue-100 hover:text-blue-600 text-sm font-bold transition-all">
                    <i data-lucide="mail" class="w-4 h-4"></i> Email
                </a>
                <a href="tel:{{ $member->phone }}" class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-slate-50 text-slate-600 hover:bg-emerald-50 hover:border-emerald-100 hover:text-emerald-600 text-sm font-bold transition-all {{ !$member->phone ? 'opacity-50 pointer-events-none' : '' }}">
                    <i data-lucide="phone" class="w-4 h-4"></i> Call
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center text-center py-16 px-4 bg-white rounded-2xl border border-dashed border-slate-200">
            <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                <i data-lucide="users" class="w-6 h-6 text-slate-300"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-700 mb-1">No team members yet</h3>
            <p class="text-xs text-slate-400 max-w-xs mb-5">Add internal staff here to give them a profile, contact details, and optional dashboard access.</p>
            <button
              onclick="toggleModal('newStaffModal')"
              class="bg-[#0F1D33] hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold text-xs transition-all shadow-sm flex items-center gap-1.5"
            >
              <i data-lucide="plus" class="w-3.5 h-3.5"></i>
              Add Member
            </button>
        </div>
        @endforelse
    </div>

    <!-- Add Modal -->
    <div id="newStaffModal" onclick="toggleModal('newStaffModal')" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div onclick="event.stopPropagation()" class="relative bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between px-6 py-3 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Add Team Member</h3>
                <button onclick="toggleModal('newStaffModal')" class="p-2 rounded-full text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="px-6 pb-6 pt-2 max-h-[80vh] overflow-y-auto">
                <form action="{{ route('admin.team.store') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium" placeholder="e.g. Alex Johnson">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Job Title <span class="text-red-500">*</span></label>
                        <input type="text" name="role" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium" placeholder="e.g. Logistics Manager">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium" placeholder="email@company.com">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mobile Number</label>
                        <input type="text" name="phone" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium" placeholder="e.g. +44 7700 900077">
                    </div>
                     <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <div class="relative group" x-data="{ showPassword: false }">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium pr-10" placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-500 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold rounded-lg">
                                <i x-show="!showPassword" data-lucide="eye" class="w-5 h-5"></i>
                                <i x-show="showPassword" data-lucide="eye-off" class="w-5 h-5"></i>
                            </button>
                            <p class="text-xs text-slate-500 mt-1">Default password for team member</p>
                        </div>
                    </div>

                    {{-- Dashboard Access Section --}}
                    <div class="border-t border-slate-100 pt-4 mt-1" x-data="{ grantAccess: false }">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Dashboard Access</label>
                                <p class="text-xs text-slate-400">Allow this member to log into the admin dashboard</p>
                            </div>
                            <button type="button" @click="grantAccess = !grantAccess"
                                :class="grantAccess ? 'bg-kingdom-gold' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-kingdom-gold/30">
                                <span :class="grantAccess ? 'translate-x-6' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"></span>
                            </button>
                        </div>
                        <div x-show="grantAccess" x-transition class="mt-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Assign Role</label>
                            <select name="dashboard_role" x-bind:disabled="!grantAccess"
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium">
                                <option value="">— Select a role —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="mt-2 w-full bg-slate-900 text-white py-3.5 rounded-xl font-bold hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20">
                        Create Team Member & Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editStaffModal" onclick="toggleModal('editStaffModal')" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div onclick="event.stopPropagation()" class="relative bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between px-6 py-3 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Edit Team Member</h3>
                <button onclick="toggleModal('editStaffModal')" class="p-2 rounded-full text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="px-6 pb-6 pt-2 max-h-[80vh] overflow-y-auto">
                <form id="editStaffForm" method="POST" class="flex flex-col gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Job Title <span class="text-red-500">*</span></label>
                        <input type="text" name="role" id="edit_role" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="edit_email" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mobile Number</label>
                        <input type="text" name="phone" id="edit_phone" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium">
                    </div>
                     <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">New Password <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <div class="relative group" x-data="{ showPassword: false }">
                            <input :type="showPassword ? 'text' : 'password'" name="password" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium pr-10" placeholder="Leave blank to keep current">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-500 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold rounded-lg">
                                <i x-show="!showPassword" data-lucide="eye" class="w-5 h-5"></i>
                                <i x-show="showPassword" data-lucide="eye-off" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Dashboard Access Section --}}
                    <div class="border-t border-slate-100 pt-4 mt-1" x-data="{ grantAccess: false }" id="edit_dashboard_section">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Dashboard Access</label>
                                <p class="text-xs text-slate-400">Allow this member to log into the admin dashboard</p>
                            </div>
                            <button type="button" @click="grantAccess = !grantAccess"
                                :class="grantAccess ? 'bg-kingdom-gold' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-kingdom-gold/30">
                                <span :class="grantAccess ? 'translate-x-6' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"></span>
                            </button>
                        </div>
                        <div x-show="grantAccess" x-transition class="mt-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Assign Role</label>
                            <select name="dashboard_role" id="edit_dashboard_role" x-bind:disabled="!grantAccess"
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium">
                                <option value="">— Select a role —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="mt-2 w-full bg-slate-900 text-white py-3.5 rounded-xl font-bold hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20">
                        Update Team Member
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity Section -->
    <div class="anim-fade-in-up mt-3 bg-white rounded-2xl shadow-sm p-4 border border-slate-100">
        <h3 class="text-sm font-bold text-kingdom-navy mb-3">Recent Team Activity</h3>
        <div class="space-y-4">
            @forelse($activities as $log)
            <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100/50">
                <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0 font-bold text-slate-500">
                    {{ substr($log->user->name ?? '?', 0, 1) }}
                </div>
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                        <h4 class="font-bold text-slate-900 text-sm">{{ $log->action }} <span class="text-slate-400 font-normal">by {{ $log->user->name ?? 'Unknown' }}</span></h4>
                        <span class="text-xs text-slate-400 font-bold">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    @if($log->description)
                    <p class="text-sm text-slate-600 mt-1">{{ $log->description }}</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-slate-400 font-medium">No recent activity found.</div>
            @endforelse
        </div>
    </div>
    </div>

</div>
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function openEditModal(member) {
            const form = document.getElementById('editStaffForm');
            form.action = `/admin/team/${member.id}`;
            
            document.getElementById('edit_name').value = member.name;
            document.getElementById('edit_role').value = member.role;
            document.getElementById('edit_email').value = member.email;
            document.getElementById('edit_phone').value = member.phone || '';

            // Handle dashboard access toggle and role dropdown
            const section = document.getElementById('edit_dashboard_section');
            const select = document.getElementById('edit_dashboard_role');
            
            // Check if member has a linked user with a dashboard role
            const linkedUser = member.linked_user || member.linkedUser;
            let currentRole = '';
            if (linkedUser && linkedUser.roles && linkedUser.roles.length > 0) {
                currentRole = linkedUser.roles[0].name;
            }

            // Set the Alpine.js state and select value
            if (currentRole) {
                select.value = currentRole;
                // Trigger Alpine reactivity by dispatching a custom event
                section.__x.$data.grantAccess = true;
            } else {
                select.value = '';
                section.__x.$data.grantAccess = false;
            }
            
            toggleModal('editStaffModal');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                ['newStaffModal', 'editStaffModal'].forEach(id => {
                    const modal = document.getElementById(id);
                    if (modal && !modal.classList.contains('hidden')) {
                        toggleModal(id);
                    }
                });
            }
        });
    </script>
@endsection
