@extends('layouts.partner')

@section('title', 'Company Profile | Kingdom Partner')

@section('content')
<div class="flex flex-col h-full max-h-full gap-6 overflow-hidden">
    <!-- Header -->
    <div class="flex-none flex-shrink-0">
        <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Company Profile</h1>
        <p class="text-sm text-slate-500 mt-2">Manage your account details and business information.</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="flex-none p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400 flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <p class="font-medium text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Profile Form -->
    <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 pb-8">
        <div class="bg-white dark:bg-[#0F1D33] rounded-[14px] p-5 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-800/50">
            <form action="{{ route('partner.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Photo Upload Area -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-6 pb-8 border-b border-slate-200 dark:border-slate-800/50">
                <div class="relative shrink-0">
                    <div class="h-24 w-24 rounded-full bg-slate-100 dark:bg-white/5 border-4 border-white dark:border-[#0F1D33] shadow-md flex items-center justify-center overflow-hidden">
                        <x-avatar :user="$user" class="h-full w-full" />
                    </div>
                    <label for="profile_photo" class="absolute bottom-0 right-0 h-8 w-8 bg-white dark:bg-[#1E2E46] border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center cursor-pointer shadow-sm hover:text-[#B89955] transition-colors text-slate-500">
                        <i data-lucide="camera" class="text-[16px] w-5 h-5"></i>
                        <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Company Logo</h3>
                    <p class="text-sm text-slate-500 mt-1">Update your company logo or profile picture. Recommended size: 256x256px.</p>
                    @error('profile_photo')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contact Name (swapped to left) -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Contact Person (Name) <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-[13px] focus:outline-none focus:ring-2 focus:ring-[#B89955] focus:bg-white dark:bg-white/5 dark:border-white/10 dark:text-white dark:focus:ring-[#B89955] dark:focus:bg-[#1E2E46] transition-all"
                        placeholder="John Doe">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Name (swapped to right) -->
                <div class="space-y-2">
                    <label for="company_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Company Name</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-[13px] focus:outline-none focus:ring-2 focus:ring-[#B89955] focus:bg-white dark:bg-white/5 dark:border-white/10 dark:text-white dark:focus:ring-[#B89955] dark:focus:bg-[#1E2E46] transition-all"
                        placeholder="e.g. Acme Corp">
                    @error('company_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Readonly) -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Email Address</label>
                    <div class="relative">
                        <input type="email" id="email" value="{{ $user->email }}" readonly disabled
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 text-[13px] opacity-70 cursor-not-allowed dark:bg-black/20 dark:border-white/5 dark:text-slate-400">
                        <i data-lucide="lock" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Email cannot be changed directly. Contact support if needed.</p>
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <label for="phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-[13px] focus:outline-none focus:ring-2 focus:ring-[#B89955] focus:bg-white dark:bg-white/5 dark:border-white/10 dark:text-white dark:focus:ring-[#B89955] dark:focus:bg-[#1E2E46] transition-all"
                        placeholder="+44 20 7123 4567">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address (full width) -->
                <div class="space-y-2 md:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Address</label>
                    <textarea id="address" name="address" rows="2"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-[13px] focus:outline-none focus:ring-2 focus:ring-[#B89955] focus:bg-white dark:bg-white/5 dark:border-white/10 dark:text-white dark:focus:ring-[#B89955] dark:focus:bg-[#1E2E46] transition-all resize-none"
                        placeholder="123 Business Street, London, UK">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Website Address -->
                <div class="space-y-2 md:col-span-2">
                    <label for="website" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Website Address</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[13px] font-medium">https://</span>
                        <input type="url" id="website" name="website" value="{{ old('website', $user->website) }}" 
                            class="w-full pl-[68px] pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-[13px] focus:outline-none focus:ring-2 focus:ring-[#B89955] focus:bg-white dark:bg-white/5 dark:border-white/10 dark:text-white dark:focus:ring-[#B89955] dark:focus:bg-[#1E2E46] transition-all"
                            placeholder="www.yourcompany.com">
                    </div>
                    @error('website')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800/50 flex flex-col sm:flex-row items-center justify-end gap-3">
                <button type="reset" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-slate-500 text-[13px] hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/5 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-white text-[13px] bg-gradient-to-r from-[#1f5d50] to-[#124b3f] hover:from-[#164a3f] hover:to-[#0f3d32] shadow-lg shadow-emerald-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Find the image container and replace its contents or update the img src
                const container = input.closest('.relative').firstElementChild;
                container.innerHTML = `<img src="${e.target.result}" alt="Preview" class="h-full w-full object-cover">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
