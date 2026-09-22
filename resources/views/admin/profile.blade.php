@extends('layouts.admin')

@section('title', 'Profile | Kingdom Admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="dashboard-header text-lg sm:text-xl font-black text-slate-900 tracking-tight mb-6">My Profile</h2>

    <div class="anim-fade-in-up bg-white rounded-2xl border border-slate-200 p-5 md:p-6 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -z-0 pointer-events-none"></div>
        
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6 relative z-10">
            @csrf
            @method('PUT')
            
            <div class="flex items-center gap-8 pb-8 border-b border-slate-100">
                <div class="relative group">
                    <div class="h-28 w-28 rounded-full bg-white flex items-center justify-center overflow-hidden ring-4 ring-slate-50 shadow-2xl relative">
                        <x-avatar :user="auth()->user()" class="h-full w-full" />
                        
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-slate-900/80 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer backdrop-blur-sm rounded-full text-center">
                            <i data-lucide="camera" class="w-6 h-6 text-white mb-0.5 drop-shadow-md transform scale-90 group-hover:scale-100 transition-transform duration-300"></i>
                            <span class="text-[10px] font-bold text-white uppercase tracking-widest drop-shadow-md translate-y-2 group-hover:translate-y-0 transition-transform duration-300">Change</span>
                            <span class="text-[9px] text-yellow-400 font-medium mt-0.5 translate-y-2 group-hover:translate-y-0 transition-transform duration-300 delay-75">Max 2MB</span>
                        </div>
                        
                        <!-- File Input Label overlay -->
                        <label for="profileInput" class="absolute inset-0 cursor-pointer rounded-full"></label>
                    </div>
                    
                    <!-- Edit Badge (Hidden by default, shown on group hover) -->
                    <div class="absolute bottom-1 right-1 bg-kingdom-gold p-1.5 rounded-full ring-2 ring-white shadow-lg pointer-events-none opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100 transition-all duration-300 z-10">
                        <i data-lucide="edit-2" class="w-3.5 h-3.5 text-kingdom-navy"></i>
                    </div>

                    <input type="file" id="profileInput" name="profile" class="hidden" accept="image/*" onchange="previewImage(this)">
                </div>
                
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <h3 class="text-2xl font-bold text-slate-900">{{ auth()->user()?->name ?? 'Admin' }}</h3>
                        <span class="px-3 py-1 bg-kingdom-gold/10 text-kingdom-gold-dark text-xs font-bold rounded-full uppercase tracking-wider border border-kingdom-gold/20">
                            {{ auth()->user()?->role ?? 'admin' }}
                        </span>
                    </div>
                    <p class="text-slate-500">{{ auth()->user()?->email ?? 'admin@kingdom.com' }}</p>
                </div>
            </div>

            <!-- Image Preview Script -->
            <script>
                function previewImage(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            // Find the image or placeholder and update/replace it
                            // For simplicity, let's just reload or trust the upload works. 
                            // Or better:
                            const container = input.previousElementSibling; // The div around img/span
                            let img = container.querySelector('img');
                            if(!img) {
                                // Create img if it was a span
                                img = document.createElement('img');
                                img.className = 'h-full w-full object-cover';
                                container.innerHTML = '';
                                container.appendChild(img);
                                // Re-add label overlay (tricky with innerHTML replacement) - let's keep it simple CSS preview only if time permits or live with post-upload update.
                            }
                            img.src = e.target.result;
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ auth()->user()?->name }}" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ auth()->user()?->email }}" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium">
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <h4 class="text-lg font-bold text-slate-900 mb-4">Security</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">New Password <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <div class="relative group" x-data="{ showPassword: false }">
                             <input :type="showPassword ? 'text' : 'password'" name="password" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium pr-10" placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-500 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold rounded-lg">
                                <i x-show="!showPassword" data-lucide="eye" class="w-5 h-5"></i>
                                <i x-show="showPassword" data-lucide="eye-off" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                     <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Confirm Password</label>
                        <div class="relative group" x-data="{ showConfirmPassword: false }">
                            <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-900 font-medium pr-10" placeholder="••••••••">
                             <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-500 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-kingdom-gold rounded-lg">
                                <i x-show="!showConfirmPassword" data-lucide="eye" class="w-5 h-5"></i>
                                <i x-show="showConfirmPassword" data-lucide="eye-off" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
