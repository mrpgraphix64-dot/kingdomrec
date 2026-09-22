@extends('layouts.candidate')

@section('title', 'Edit Profile - Kingdom Recruitments')

@section('content')
<div x-data="photoCropper()" x-init="init()" class="flex flex-col font-body relative">

    <div class="max-w-[800px] w-full mx-auto mb-2">
        <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Edit Your Profile</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">
            Update your details to stay matched with the best opportunities.
        </p>
    </div>

    <main class="flex-grow flex flex-col items-center justify-start relative">
        @php
            $currentRole = old('category', $applicant->role ?? '');
            $isOtherCategory = ($currentRole === 'Other / Not Listed' || $currentRole === 'Other');
            $otherCategoryDesc = old('other_category_description', $applicant->other_category_description ?? '');
            $isKnownCategory = collect($categories->pluck('name'))->contains($currentRole);
            $initialCategory = $isKnownCategory ? $currentRole : ($isOtherCategory ? 'Other / Not Listed' : '');
            if (!$isKnownCategory && !$isOtherCategory && !empty($currentRole) && !in_array(strtolower($currentRole), ['applicant', 'general applicant'])) {
                $initialCategory = 'Other / Not Listed';
                if (empty($otherCategoryDesc)) {
                    $otherCategoryDesc = $currentRole;
                }
            }
        @endphp
        <!-- Safe JSON Data Storage (to prevent double-quote conflict inside HTML x-data attribute) -->
        <script id="categories-data" type="application/json">
            @json($categories)
        </script>
        <script id="educations-data" type="application/json">
            @json($applicant->educations ?? [])
        </script>
        <script id="experiences-data" type="application/json">
            @json($applicant->experiences ?? [])
        </script>

        <div class="w-full max-w-[800px] flex flex-col gap-8" 
             x-data="{ 
                 categories: [],
                 formData: { 
                     name: '{{ addslashes(old('name', $applicant->name ?? Auth::user()->name)) }}',
                     phone: '{{ addslashes(old('phone', ($applicant->phone === 'Not provided' ? '' : $applicant->phone) ?? '')) }}',
                     location: '{{ addslashes(old('location', ($applicant->location === 'Not provided' ? '' : $applicant->location) ?? '')) }}',
                     category: '{{ addslashes($initialCategory) }}',
                     sub_category: '{{ addslashes(old('sub_category', $applicant->sub_category ?? '')) }}',
                     other_category_description: '{{ addslashes($otherCategoryDesc) }}'
                 },
                 education: [],
                 experience: [],
                 errors: {},
                 init() {
                     const categoriesEl = document.getElementById('categories-data');
                     if (categoriesEl) this.categories = JSON.parse(categoriesEl.textContent);
                     
                     const edusEl = document.getElementById('educations-data');
                     if (edusEl) {
                         const edus = JSON.parse(edusEl.textContent);
                         this.education = edus.map(edu => ({
                             ...edu,
                             start_date: edu.start_date ? edu.start_date.substring(0, 10) : '',
                             end_date: edu.end_date ? edu.end_date.substring(0, 10) : ''
                         }));
                     }
                     
                     const expsEl = document.getElementById('experiences-data');
                     if (expsEl) {
                         const exps = JSON.parse(expsEl.textContent);
                         this.experience = exps.map(exp => ({
                             ...exp,
                             start_date: exp.start_date ? exp.start_date.substring(0, 10) : '',
                             end_date: exp.end_date ? exp.end_date.substring(0, 10) : ''
                         }));
                     }
                 },
                 get filteredSubCategories() {
                     if (this.formData.category === 'Other / Not Listed' || !this.formData.category) return [];
                     const cat = this.categories.find(c => c.name === this.formData.category);
                     return cat ? (cat.sub_categories || []) : [];
                 },
                 selectCategory(catName) {
                     this.formData.category = catName;
                     this.formData.sub_category = '';
                     if (catName !== 'Other / Not Listed') {
                         this.clearError('other_category_description');
                     }
                     this.clearError('category');
                 },
                 addEducation() {
                     this.education.push({ id: Date.now(), institution: '', degree: '', start_date: '', end_date: '', is_current: false, description: '' });
                 },
                 removeEducation(index) {
                     this.education.splice(index, 1);
                 },
                 addExperience() {
                     this.experience.push({ id: Date.now(), company: '', job_title: '', start_date: '', end_date: '', is_current: false, description: '' });
                 },
                 removeExperience(index) {
                     this.experience.splice(index, 1);
                 },
                 clearError(field) {
                     if (this.errors[field]) {
                         delete this.errors[field];
                     }
                 },
                 scrollToField(fieldKey) {
                     this.$nextTick(() => {
                         const container = this.$refs.formContainer;
                         let targetEl = null;

                         if (fieldKey === 'category') {
                             targetEl = container.querySelector('#category-select-trigger') || container.querySelector('[name=\'category\']')?.parentElement;
                         } else if (fieldKey === 'other_category_description') {
                             targetEl = container.querySelector('[name=\'other_category_description\']');
                         } else if (fieldKey === 'name') {
                             targetEl = container.querySelector('[name=\'name\']');
                         } else if (fieldKey === 'phone') {
                             targetEl = container.querySelector('[name=\'phone\']');
                         } else if (fieldKey === 'location') {
                             targetEl = container.querySelector('[name=\'location\']');
                         } else {
                             targetEl = container.querySelector(`[name='${fieldKey}']`);
                         }

                         if (!targetEl) {
                             targetEl = container.querySelector(`[name*='${fieldKey}']`);
                         }

                         if (targetEl) {
                             const header = document.querySelector('header') || document.querySelector('nav') || document.querySelector('.sticky, .fixed');
                             const headerOffset = (header ? header.getBoundingClientRect().height : 80) + 24;
                             const elementPosition = targetEl.getBoundingClientRect().top;
                             const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                             window.scrollTo({
                                 top: Math.max(0, offsetPosition),
                                 behavior: 'smooth'
                             });

                             if (targetEl.tagName === 'INPUT' || targetEl.tagName === 'SELECT' || targetEl.tagName === 'TEXTAREA' || targetEl.tagName === 'BUTTON') {
                                 targetEl.focus({ preventScroll: true });
                             } else {
                                 const focusable = targetEl.querySelector('input, select, textarea, button');
                                 if (focusable) focusable.focus({ preventScroll: true });
                             }
                         }
                     });
                 },
                 validateForm(e) {
                     this.errors = {};
                     const container = this.$refs.formContainer;
                     let isValid = true;
                     let firstInvalidField = null;

                     if (!this.formData.name || !this.formData.name.trim()) {
                         this.errors.name = 'Please enter your full name.';
                         isValid = false;
                         if (!firstInvalidField) firstInvalidField = 'name';
                     }

                     if (!this.formData.phone || !this.formData.phone.trim() || this.formData.phone.trim() === 'Not provided') {
                         this.errors.phone = 'Please enter your phone number.';
                         isValid = false;
                         if (!firstInvalidField) firstInvalidField = 'phone';
                     }

                     if (!this.formData.location || !this.formData.location.trim() || this.formData.location.trim() === 'Not provided') {
                         this.errors.location = 'Please enter your location.';
                         isValid = false;
                         if (!firstInvalidField) firstInvalidField = 'location';
                     }

                     if (!this.formData.category) {
                         this.errors.category = 'Please select a job category.';
                         isValid = false;
                         if (!firstInvalidField) firstInvalidField = 'category';
                     } else if (this.formData.category === 'Other / Not Listed') {
                         if (!this.formData.other_category_description || !this.formData.other_category_description.trim()) {
                             this.errors.other_category_description = 'Please tell us what type of work you are looking for.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = 'other_category_description';
                         }
                     }

                     this.experience.forEach((exp, idx) => {
                         if (!exp.company || !exp.company.trim()) {
                             this.errors[`experience[${idx}][company]`] = 'Please enter company name.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = `experience[${idx}][company]`;
                         }
                         if (!exp.job_title || !exp.job_title.trim()) {
                             this.errors[`experience[${idx}][job_title]`] = 'Please enter job title.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = `experience[${idx}][job_title]`;
                         }
                         if (!exp.start_date) {
                             this.errors[`experience[${idx}][start_date]`] = 'Please enter start date.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = `experience[${idx}][start_date]`;
                         }
                         if (!exp.is_current && !exp.end_date) {
                             this.errors[`experience[${idx}][end_date]`] = 'Please enter end date or mark as current.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = `experience[${idx}][end_date]`;
                         }
                     });

                     this.education.forEach((edu, idx) => {
                         if (!edu.institution || !edu.institution.trim()) {
                             this.errors[`education[${idx}][institution]`] = 'Please enter institution name.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = `education[${idx}][institution]`;
                         }
                         if (!edu.degree || !edu.degree.trim()) {
                             this.errors[`education[${idx}][degree]`] = 'Please enter qualification/degree.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = `education[${idx}][degree]`;
                         }
                         if (!edu.start_date) {
                             this.errors[`education[${idx}][start_date]`] = 'Please enter start date.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = `education[${idx}][start_date]`;
                         }
                         if (!edu.is_current && !edu.end_date) {
                             this.errors[`education[${idx}][end_date]`] = 'Please enter end date or mark as current.';
                             isValid = false;
                             if (!firstInvalidField) firstInvalidField = `education[${idx}][end_date]`;
                         }
                     });

                     if (!isValid) {
                         e.preventDefault();
                         this.scrollToField(firstInvalidField);
                     }
                 }
             }">
            
            <form x-ref="formContainer" @submit="validateForm($event)" novalidate action="{{ route('applicant.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-[#0B1120] dark:bg-white/5 backdrop-blur-xl p-8 md:p-10 rounded-3xl shadow-2xl border border-white/10 relative z-10">
                @csrf
                @method('PUT')
                <input type="hidden" name="redirect_back" value="{{ request()->query('redirect_back') }}">
                
                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-8 p-6 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-red-500 font-bold">
                            <span class="material-icons">error_outline</span>
                            <span>Please fix the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1 opacity-90 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="space-y-12">
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-2 border-b border-white/10">
                            <span class="material-icons text-amber-500">person</span>
                            <h2 class="text-xl font-bold text-white">Basic Information</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-300 uppercase tracking-wide opacity-80">Full Name<span class="text-red-500">*</span></label>
                                <input type="text" name="name" 
                                    x-model="formData.name"
                                    @input="clearError('name')"
                                    class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 focus:border-transparent transition-all outline-none placeholder-slate-500 border"
                                    :class="errors.name ? 'border-red-500 ring-1 ring-red-500' : 'border-white/10'" 
                                    placeholder="Enter your full name">
                                <span x-show="errors.name" x-text="errors.name" class="text-xs text-red-500 font-bold mt-1.5 flex items-center gap-1"></span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-slate-300 uppercase tracking-wide opacity-80">Email Address</label>
                                    <div class="relative">
                                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-icons text-gray-400 text-lg">email</span>
                                        </div>
                                        <input type="email" name="email" readonly
                                            value="{{ $applicant->email ?? Auth::user()->email }}"
                                            class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/5 border border-white/10 text-slate-400 cursor-not-allowed transition-all outline-none shadow-inner opacity-70">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-slate-300 uppercase tracking-wide opacity-80">Phone Number<span class="text-red-500">*</span></label>
                                    <div class="relative">
                                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-icons text-gray-400 text-lg">phone</span>
                                        </div>
                                        <input type="tel" name="phone" 
                                            x-model="formData.phone"
                                            @input="clearError('phone')"
                                            class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 focus:border-transparent transition-all outline-none placeholder-slate-500 border"
                                            :class="errors.phone ? 'border-red-500 ring-1 ring-red-500' : 'border-white/10'" 
                                            placeholder="Enter your phone number">
                                    </div>
                                    <span x-show="errors.phone" x-text="errors.phone" class="text-xs text-red-500 font-bold mt-1.5 flex items-center gap-1"></span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-300 uppercase tracking-wide opacity-80">Location (City / Town)<span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="material-icons text-gray-400 text-lg">place</span>
                                    </div>
                                    <input type="text" name="location" 
                                        x-model="formData.location"
                                        @input="clearError('location')"
                                        class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 focus:border-transparent transition-all outline-none placeholder-slate-500 border"
                                        :class="errors.location ? 'border-red-500 ring-1 ring-red-500' : 'border-white/10'" 
                                        placeholder="e.g. London, Birmingham, Manchester">
                                </div>
                                <span x-show="errors.location" x-text="errors.location" class="text-xs text-red-500 font-bold mt-1.5 flex items-center gap-1"></span>
                            </div>

                            <!-- Profile Photo Cropper Section -->
                            <div class="space-y-4 mt-2">
                                <label class="block text-sm font-bold text-slate-300 uppercase tracking-wide opacity-80">Profile Photo</label>
                                
                                <div class="flex flex-col sm:flex-row items-start gap-6">
                                    <!-- Photo Preview Thumbnail -->
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="relative group cursor-pointer" @click="$refs.cropperFileInput.click()">
                                            <!-- Circular Preview -->
                                            <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white dark:border-gray-700 shadow-lg ring-2 ring-gray-200 dark:ring-gray-600 bg-slate-900 flex items-center justify-center shrink-0 relative">
                                                <!-- Silhouette fallback -->
                                                <span class="material-icons text-5xl text-slate-500 select-none pointer-events-none">person</span>
                                                
                                                <!-- Cropped image preview -->
                                                <template x-if="croppedPreview">
                                                    <img :src="croppedPreview" alt="Profile Photo" class="absolute inset-0 w-full h-full object-cover rounded-full">
                                                </template>
                                                
                                                <!-- Original image preview -->
                                                <template x-if="!croppedPreview && originalUrl">
                                                    <img :src="originalUrl" alt="Profile Photo" class="absolute inset-0 w-full h-full object-cover rounded-full" style="object-position: center" onerror="this.style.display='none'">
                                                </template>
                                            </div>
                                            
                                            <!-- Hover Overlay -->
                                            <div class="absolute inset-0 rounded-full bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200">
                                                <span class="material-icons text-white text-xl mb-0.5">photo_camera</span>
                                                <span class="text-[10px] font-bold text-white uppercase tracking-wider">Change</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Hidden file input -->
                                        <input type="file" x-ref="cropperFileInput" accept="image/jpeg,image/png,image/webp" class="hidden"
                                               @change="onFileSelect($event)">
                                        <!-- Hidden input for the cropped blob -->
                                        <input type="file" name="photo" id="croppedFileInput" x-ref="croppedFileInput" class="hidden">
                                        <input type="hidden" name="image_position" value="center">
                                        
                                        <!-- Action Buttons -->
                                        <button type="button" @click="$refs.cropperFileInput.click()" 
                                                class="text-xs font-bold text-amber-500 hover:text-amber-400 cursor-pointer flex items-center gap-1 transition-colors">
                                            <span class="material-icons text-sm">edit</span>
                                            <span x-text="croppedPreview || originalUrl ? 'Change Photo' : 'Upload Photo'"></span>
                                        </button>
                                        
                                        <!-- Cropped indicator -->
                                        <div x-show="croppedPreview" x-cloak class="flex items-center gap-1 text-[10px] text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full font-bold">
                                            <span class="material-icons text-xs">check_circle</span>
                                            <span>Photo cropped & ready</span>
                                        </div>
                                    </div>

                                    <!-- Instructions -->
                                    <div class="flex-1 space-y-2 pt-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Upload a professional headshot for your profile.</p>
                                        <ul class="text-[11px] text-gray-400 space-y-1">
                                            <li class="flex items-center gap-1.5"><span class="material-icons text-xs text-amber-500/60">check</span> Drag to reposition your photo</li>
                                            <li class="flex items-center gap-1.5"><span class="material-icons text-xs text-amber-500/60">check</span> Max 5MB · JPEG, PNG, WebP</li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-2 border-b border-white/10">
                            <span class="material-icons text-amber-500">work_outline</span>
                            <h2 class="text-xl font-bold text-white">Job Details</h2>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Job Category Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-300 uppercase tracking-wide opacity-80">
                                    Job Category<span class="text-red-500">*</span>
                                </label>
                                <div class="relative" x-data="{ open: false }">
                                    <input type="hidden" name="category" :value="formData.category">
                                    
                                    <button type="button" id="category-select-trigger" @click="open = !open" @click.outside="open = false"
                                        class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 transition-all outline-none text-left flex items-center justify-between border"
                                        :class="errors.category ? 'border-red-500 ring-1 ring-red-500' : 'border-white/10'">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-icons text-gray-400">cases</span>
                                        </div>
                                        <span :class="formData.category ? 'text-white font-medium' : 'text-slate-400'" x-text="formData.category || 'Select Category'"></span>
                                        <span class="material-icons text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}">expand_more</span>
                                    </button>
                                    
                                    <div x-show="open" x-cloak class="absolute z-50 w-full mt-2 bg-[#1E293B] rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.5)] border border-white/15 overflow-hidden max-h-64 overflow-y-auto">
                                        <template x-for="cat in categories" :key="cat.id">
                                            <button type="button" @click="selectCategory(cat.name); open = false" 
                                                class="w-full text-left px-4 py-3 text-sm font-medium hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-between transition-colors border-b border-white/5 last:border-0">
                                                <span x-text="cat.name"></span>
                                                <span x-show="formData.category === cat.name" class="material-icons text-amber-500 text-sm">check</span>
                                            </button>
                                        </template>

                                        <!-- Other / Not Listed Option -->
                                        <button type="button" @click="selectCategory('Other / Not Listed'); open = false"
                                            class="w-full text-left px-4 py-3 text-sm font-medium hover:bg-white/10 text-amber-400 hover:text-amber-300 flex items-center justify-between transition-colors border-t border-white/10 bg-white/[0.02]">
                                            <span class="flex items-center gap-2">
                                                <span class="material-icons text-sm">add_circle_outline</span>
                                                <span>Other / Not Listed</span>
                                            </span>
                                            <span x-show="formData.category === 'Other / Not Listed'" class="material-icons text-amber-500 text-sm">check</span>
                                        </button>
                                    </div>
                                </div>
                                <span x-show="errors.category" x-text="errors.category" class="text-xs text-red-500 font-bold mt-1.5 flex items-center gap-1"></span>
                            </div>

                            <!-- Conditional "Other / Not Listed" Work Description Field -->
                            <div x-show="formData.category === 'Other / Not Listed'" x-cloak class="space-y-2 animate-fade-in-up">
                                <label class="block text-sm font-bold text-slate-300 uppercase tracking-wide opacity-80">
                                    What type of work are you looking for?<span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="material-icons text-gray-400">edit_note</span>
                                    </div>
                                    <input type="text" 
                                           name="other_category_description" 
                                           x-model="formData.other_category_description" 
                                           @input="clearError('other_category_description')"
                                           placeholder="Example: Event Photographer, Driver, Chef"
                                           class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-slate-500 border"
                                           :class="errors.other_category_description ? 'border-red-500 ring-1 ring-red-500' : 'border-white/10'">
                                </div>
                                <span x-show="errors.other_category_description" x-text="errors.other_category_description" class="text-xs text-red-500 font-bold mt-1.5 flex items-center gap-1"></span>
                            </div>

                            <!-- Optional Sub Category (Only for standard categories with sub categories) -->
                            <div x-show="formData.category && formData.category !== 'Other / Not Listed' && filteredSubCategories.length > 0" x-cloak class="space-y-2">
                                <label class="block text-sm font-bold text-slate-300 uppercase tracking-wide opacity-80">
                                    Specialisation / Sub Category
                                </label>
                                <div class="relative" x-data="{ openSub: false }">
                                    <input type="hidden" name="sub_category" :value="formData.sub_category">
                                    <button type="button" @click="openSub = !openSub" @click.outside="openSub = false"
                                        class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 transition-all outline-none text-left flex items-center justify-between border border-white/10">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-icons text-gray-400">badge</span>
                                        </div>
                                        <span :class="formData.sub_category ? 'text-white font-medium' : 'text-slate-400'" x-text="formData.sub_category || 'Select Sub Category (Optional)'"></span>
                                        <span class="material-icons text-gray-400 transition-transform duration-200" :class="{'rotate-180': openSub}">expand_more</span>
                                    </button>
                                    
                                    <div x-show="openSub" x-cloak class="absolute z-50 w-full mt-2 bg-[#1E293B] rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.5)] border border-white/15 overflow-hidden max-h-60 overflow-y-auto">
                                        <button type="button" @click="formData.sub_category = ''; openSub = false"
                                            class="w-full text-left px-4 py-3 text-sm font-medium hover:bg-white/10 text-slate-400 hover:text-white flex items-center justify-between transition-colors border-b border-white/5">
                                            <span>None / General</span>
                                            <span x-show="!formData.sub_category" class="material-icons text-amber-500 text-sm">check</span>
                                        </button>
                                        <template x-for="sub in filteredSubCategories" :key="sub.id">
                                            <button type="button" @click="formData.sub_category = sub.name; openSub = false" 
                                                class="w-full text-left px-4 py-3 text-sm font-medium hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-between transition-colors border-b border-white/5 last:border-0">
                                                <span x-text="sub.name"></span>
                                                <span x-show="formData.sub_category === sub.name" class="material-icons text-amber-500 text-sm">check</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-white/10"></div>

                    <!-- Work Experience -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between pb-2 border-b border-white/10">
                             <div class="flex items-center gap-3">
                                <span class="material-icons text-amber-500">business_center</span>
                                <h2 class="text-xl font-bold text-white">Work Experience</h2>
                            </div>
                            <button type="button" @click="addExperience()" class="text-sm font-bold text-amber-500 hover:text-amber-400 flex items-center gap-1 transition-colors">
                                <span class="material-icons text-base">add_circle</span> Add Position
                            </button>
                        </div>
                        
                        <div class="space-y-6">
                            <template x-for="(exp, index) in experience" :key="exp.id || index">
                                <div class="bg-white/5 hover:bg-white/10 shadow-lg p-6 rounded-xl border border-white/10 relative group animate-fade-in-up">
                                    <button type="button" @click="removeExperience(index)" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-400 transition-colors bg-white/5 hover:bg-white/10 rounded-full shadow-sm border border-white/10" title="Remove">
                                        <span class="material-icons text-sm">close</span>
                                    </button>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold uppercase text-slate-300">Company Name</label>
                                            <input type="text" :name="'experience[' + index + '][company]'" x-model="exp.company" placeholder="e.g. Kingdom Events" required
                                                class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-slate-500 border border-white/10">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold uppercase text-slate-300">Job Title</label>
                                            <input type="text" :name="'experience[' + index + '][job_title]'" x-model="exp.job_title" placeholder="e.g. Security Officer" required
                                                class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-slate-500 border border-white/10">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold uppercase text-slate-300">Start Date<span class="text-red-500">*</span></label>
                                            <input type="date" :name="'experience[' + index + '][start_date]'" x-model="exp.start_date" required
                                                class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all text-slate-300 placeholder-slate-500 border border-white/10">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold uppercase text-slate-300">End Date<span x-show="!exp.is_current" class="text-red-500">*</span></label>
                                            <div class="flex flex-col gap-2">
                                                <input type="date" :name="'experience[' + index + '][end_date]'" x-model="exp.end_date" :disabled="exp.is_current" :required="!exp.is_current"
                                                    class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all text-slate-300 disabled:opacity-50 disabled:cursor-not-allowed placeholder-slate-500 border border-white/10">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" :name="'experience[' + index + '][is_current]'" x-model="exp.is_current" :id="`current_exp_${index}`" 
                                                        class="rounded accent-[#E60026] focus:ring-red-500 bg-white/5 border-white/10 size-4 cursor-pointer">
                                                    <label :for="`current_exp_${index}`" class="text-xs font-bold text-slate-400 hover:text-white cursor-pointer select-none transition-colors">I currently work here</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <label class="block text-xs font-bold uppercase text-slate-300">Description (Optional)</label>
                                        <textarea :name="'experience[' + index + '][description]'" x-model="exp.description" rows="3" placeholder="Briefly describe your responsibilities..."
                                            class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all resize-none placeholder-slate-600 border border-white/10"></textarea>
                                    </div>
                                </div>
                            </template>
                            
                            <div x-show="experience.length === 0" class="text-center py-8 bg-white/5 hover:bg-white/10 shadow-lg rounded-xl border border-dashed border-white/20">
                                <p class="text-slate-500 font-medium">No work experience added yet.</p>
                                <button type="button" @click="addExperience()" class="text-amber-500 font-bold text-sm hover:underline mt-2">Add your first role</button>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-white/10"></div>

                    <!-- Education -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between pb-2 border-b border-white/10">
                             <div class="flex items-center gap-3">
                                <span class="material-icons text-[#3B82F6]">school</span>
                                <h2 class="text-xl font-bold text-white">Education</h2>
                            </div>
                            <button type="button" @click="addEducation()" class="text-sm font-bold text-[#3B82F6] hover:text-blue-400 flex items-center gap-1 transition-colors">
                                <span class="material-icons text-base">add_circle</span> Add Education
                            </button>
                        </div>
                        
                        <div class="space-y-6">
                            <template x-for="(edu, index) in education" :key="edu.id || index">
                                <div class="bg-white/5 hover:bg-white/10 shadow-lg p-6 rounded-xl border border-white/10 relative group animate-fade-in-up">
                                    <button type="button" @click="removeEducation(index)" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-400 transition-colors bg-white/5 hover:bg-white/10 rounded-full shadow-sm border border-white/10" title="Remove">
                                        <span class="material-icons text-sm">close</span>
                                    </button>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold uppercase text-slate-400">Institution / School</label>
                                            <input type="text" :name="'education[' + index + '][institution]'" x-model="edu.institution" placeholder="e.g. University of London" required
                                                class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-slate-600 border border-white/10">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold uppercase text-slate-400">Degree / Qualification</label>
                                            <input type="text" :name="'education[' + index + '][degree]'" x-model="edu.degree" placeholder="e.g. BSc Computer Science" required
                                                class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-slate-600 border border-white/10">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold uppercase text-slate-400">Start Date<span class="text-red-500">*</span></label>
                                            <input type="date" :name="'education[' + index + '][start_date]'" x-model="edu.start_date" required
                                                class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all text-slate-300 placeholder-slate-600 border border-white/10">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold uppercase text-slate-400">End Date<span x-show="!edu.is_current" class="text-red-500">*</span></label>
                                            <div class="flex flex-col gap-2">
                                                <input type="date" :name="'education[' + index + '][end_date]'" x-model="edu.end_date" :disabled="edu.is_current" :required="!edu.is_current"
                                                    class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 focus:bg-white/10 text-white shadow-inner focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all text-slate-300 disabled:opacity-50 disabled:cursor-not-allowed placeholder-slate-600 border border-white/10">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" :name="'education[' + index + '][is_current]'" x-model="edu.is_current" :id="`current_edu_${index}`" 
                                                        class="rounded accent-[#E60026] focus:ring-red-500 bg-white/5 border-white/10 size-4 cursor-pointer">
                                                    <label :for="`current_edu_${index}`" class="text-xs font-bold text-slate-400 hover:text-white cursor-pointer select-none transition-colors">Currently studying</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <div x-show="education.length === 0" class="text-center py-8 bg-white/5 hover:bg-white/10 shadow-lg rounded-xl border border-dashed border-white/20">
                                <p class="text-slate-500 font-medium">No education history added yet.</p>
                                <button type="button" @click="addEducation()" class="text-[#3B82F6] font-bold text-sm hover:underline mt-2">Add your first qualification</button>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-2 border-b border-white/10">
                            <span class="material-icons text-emerald-400">description</span>
                            <h2 class="text-xl font-bold text-white">Resume / CV</h2>
                        </div>

                        <div class="bg-gradient-to-b from-white/5 to-transparent rounded-2xl p-5 border border-white/5 backdrop-blur-sm relative overflow-hidden">
                            <!-- Subtle Background Glow -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

                            @if($applicant->cv_link)
                                @if($applicant->cv_exists)
                                    <div class="flex items-center justify-between mb-5 bg-[#0B1120]/50 p-3 rounded-xl border border-white/5 shadow-inner">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="size-10 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center shrink-0 shadow-sm">
                                                <span class="material-icons text-red-400 text-xl">picture_as_pdf</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-white text-sm truncate">{{ $applicant->cv_name ?? 'Attached Resume' }}</p>
                                                <p class="text-xs text-emerald-400 font-medium flex items-center gap-1 truncate"><span class="material-icons text-[10px]">check_circle</span> Active Document</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('applicant.view-resume') }}" target="_blank" class="ml-3 px-3 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-semibold text-white transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm shrink-0">
                                            <span class="material-icons text-[14px]">visibility</span> View PDF
                                        </a>
                                    </div>
                                @else
                                    <div class="flex items-center justify-between mb-5 bg-[#0B1120]/50 p-3 rounded-xl border border-white/5 shadow-inner animate-pulse-subtle">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="size-10 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-sm animate-bounce-subtle">
                                                <span class="material-icons text-amber-400 text-xl">warning</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-white text-sm truncate">{{ $applicant->cv_name ?? 'Attached Resume' }}</p>
                                                <p class="text-xs text-amber-400 font-medium flex items-center gap-1 truncate"><span class="material-icons text-[10px]">warning</span> File Unavailable on Server</p>
                                            </div>
                                        </div>
                                        <span class="ml-3 px-3 py-1.5 bg-white/5 border border-white/5 rounded-lg text-xs font-semibold text-slate-500 cursor-not-allowed flex items-center gap-1.5 shrink-0" title="File is missing on server. Please upload it again.">
                                            <span class="material-icons text-[14px]">visibility_off</span> Unavailable
                                        </span>
                                    </div>
                                @endif
                            @else
                                <div class="flex items-center gap-3 mb-5 bg-amber-500/10 p-3 rounded-xl border border-amber-500/20 shadow-inner">
                                    <div class="size-10 rounded-lg bg-amber-500/20 flex items-center justify-center shrink-0">
                                        <span class="material-icons text-amber-400">error_outline</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-amber-400 text-sm truncate">Action Required</p>
                                        <p class="text-xs text-amber-400/80 truncate">Upload your resume in PDF format to apply for jobs.</p>
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-2 relative z-10">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    {{ $applicant->cv_link ? 'Update Resume' : 'Upload Document' }}
                                </label>
                                <label for="cv-upload" class="flex items-center justify-between w-full p-3.5 border border-dashed border-white/15 rounded-xl cursor-pointer bg-white/5 hover:border-emerald-400/40 hover:bg-white/10 transition-all duration-200 group shadow-sm">
                                    <div class="flex items-center gap-3 min-w-0" id="cv-label-content">
                                        <div class="size-9 rounded-lg bg-white/5 border border-white/5 flex items-center justify-center group-hover:bg-emerald-500/20 group-hover:border-emerald-500/30 transition-colors shrink-0">
                                            <span class="material-icons text-slate-400 group-hover:text-emerald-400 transition-colors text-[18px]">cloud_upload</span>
                                        </div>
                                        <div class="min-w-0 pr-2">
                                            <p class="text-sm font-semibold text-slate-200 group-hover:text-white transition-colors truncate">Browse files</p>
                                            <p class="text-xs text-slate-500 truncate">PDF up to 5MB</p>
                                        </div>
                                    </div>
                                    <input id="cv-upload" type="file" name="cv" accept=".pdf" class="hidden" 
                                        onchange="
                                            const file = this.files[0];
                                            if (file) {
                                                const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                                                if (!isPdf) {
                                                    alert('Only PDF files are allowed.');
                                                    this.value = '';
                                                    return;
                                                }
                                                if (file.size > 5242880) {
                                                    alert('File is too large. Maximum size is 5MB.');
                                                    this.value = '';
                                                    return;
                                                }
                                                document.getElementById('cv-label-content').innerHTML = '<div class=\'size-9 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0\'><span class=\'material-icons text-emerald-400 text-[18px]\'>task_alt</span></div><div class=\'min-w-0 pr-2\'><p class=\'text-sm font-bold text-emerald-400 truncate\'>' + file.name + '</p><p class=\'text-xs text-slate-500 truncate\'>Ready to save</p></div>';
                                            }
                                        " />
                                    <div class="size-6 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors opacity-50 group-hover:opacity-100 shrink-0">
                                        <span class="material-icons text-slate-300 group-hover:text-emerald-400 transition-colors text-[12px]">arrow_forward_ios</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-6 pt-8 mt-4 border-t border-white/10">
                    <a href="{{ route('portal') }}" class="text-slate-400 font-bold hover:text-white transition-colors px-4 py-2">
                        Cancel Changes
                    </a>
                    <button type="submit" class="bg-kingdom-red hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span class="material-icons">save</span> Update Profile
                    </button>
                </div>

            </form>
        </div>
    </main>

    <!-- ═══ CROPPER MODAL (Positioned outside filter container to prevent square blur and allow alignment/scroll lock) ═══ -->
    <div x-show="showCropper" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4"
         @keydown.escape.window="cancelCrop()">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="cancelCrop()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-[#0B1120] rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-white/10"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                <div class="flex items-center gap-2">
                    <span class="material-icons text-amber-500">crop</span>
                    <h3 class="text-lg font-bold text-white">Adjust Profile Photo</h3>
                </div>
                <button type="button" @click="cancelCrop()" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white/10 transition-colors">
                    <span class="material-icons text-gray-400">close</span>
                </button>
            </div>
            
            <!-- Cropper Area -->
            <div x-ref="cropContainer" class="relative overflow-hidden" style="height: 340px; cursor: grab;"
                 @mousedown.prevent="startDrag($event)"
                 @touchstart.prevent="startDrag($event)">
                <!-- Image fills container via object-fit:cover -->
                <img x-ref="cropImage" :src="rawImageUrl" 
                     class="absolute inset-0 w-full h-full object-cover select-none pointer-events-none"
                     :style="`object-position: ${posX}% ${posY}%;`"
                     draggable="false">
                
                <!-- Circular mask overlay (r=130 fits perfectly on mobile screens) -->
                <svg class="absolute inset-0 w-full h-full pointer-events-none" preserveAspectRatio="none">
                    <defs>
                        <mask id="cropMask">
                            <rect width="100%" height="100%" fill="white"/>
                            <circle cx="50%" cy="50%" r="130" fill="black"/>
                        </mask>
                    </defs>
                    <rect width="100%" height="100%" fill="rgba(0,0,0,0.6)" mask="url(#cropMask)"/>
                    <circle cx="50%" cy="50%" r="130" fill="none" stroke="white" stroke-width="2" opacity="0.8"/>
                </svg>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-white/10">
                <p class="text-[11px] text-gray-400 flex items-center gap-1">
                    <span class="material-icons text-xs">open_with</span>
                    Drag to reposition
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" @click="cancelCrop()" 
                            class="px-5 py-2.5 text-sm font-bold text-gray-400 hover:text-white transition-colors rounded-lg hover:bg-white/10">
                        Cancel
                    </button>
                    <button type="button" @click="applyCrop()" 
                            class="px-6 py-2.5 text-sm font-bold text-white bg-kingdom-red hover:bg-red-700 rounded-xl shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span class="material-icons text-sm">check</span>
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.photoCropper = function photoCropper() {
    return {
        showCropper: false,
        originalUrl: '{{ $applicant->image ? $applicant->profile_photo_url : "" }}',
        rawImageUrl: '',
        croppedPreview: null,
        croppedBlob: null,

        // Position as percentage (50% = centered)
        posX: 50,
        posY: 50,
        imgNaturalW: 0,
        imgNaturalH: 0,

        // Drag state
        dragging: false,
        dragStartX: 0,
        dragStartY: 0,
        dragOriginPosX: 50,
        dragOriginPosY: 50,

        // Crop circle
        cropRadius: 130,
        containerH: 340,

        init() {
            this._onMove = (e) => this.onDrag(e);
            this._onEnd = () => this.endDrag();
            document.addEventListener('mousemove', this._onMove);
            document.addEventListener('mouseup', this._onEnd);
            document.addEventListener('touchmove', this._onMove, { passive: false });
            document.addEventListener('touchend', this._onEnd);

            // Prevent background scrolling when modal is open
            this.$watch('showCropper', (value) => {
                document.body.style.overflow = value ? 'hidden' : '';
            });
        },

        onFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 5242880) {
                alert('Image is too large. Max size is 5MB.');
                e.target.value = '';
                return;
            }

            const url = URL.createObjectURL(file);
            const img = new Image();
            img.onload = () => {
                this.imgNaturalW = img.naturalWidth;
                this.imgNaturalH = img.naturalHeight;
                this.rawImageUrl = url;
                this.posX = 50;
                this.posY = 50;
                this.showCropper = true;
            };
            img.src = url;
            e.target.value = '';
        },

        startDrag(e) {
            this.dragging = true;
            const pt = e.touches ? e.touches[0] : e;
            this.dragStartX = pt.clientX;
            this.dragStartY = pt.clientY;
            this.dragOriginPosX = this.posX;
            this.dragOriginPosY = this.posY;
        },

        onDrag(e) {
            if (!this.dragging) return;
            e.preventDefault();
            const pt = e.touches ? e.touches[0] : e;
            const dx = pt.clientX - this.dragStartX;
            const dy = pt.clientY - this.dragStartY;

            // Convert pixel drag to percentage (negative because dragging right should move position left)
            // Sensitivity: ~200px drag = full 0-100% range
            this.posX = Math.max(0, Math.min(100, this.dragOriginPosX - (dx / 2)));
            this.posY = Math.max(0, Math.min(100, this.dragOriginPosY - (dy / 2)));
        },

        endDrag() {
            this.dragging = false;
        },

        cancelCrop() {
            this.showCropper = false;
            if (this.rawImageUrl && this.rawImageUrl.startsWith('blob:')) {
                URL.revokeObjectURL(this.rawImageUrl);
            }
            this.rawImageUrl = '';
        },

        applyCrop() {
            const container = this.$refs.cropContainer;
            const cw = container ? container.offsetWidth : 400;
            const ch = this.containerH;
            const natW = this.imgNaturalW;
            const natH = this.imgNaturalH;
            const imgRatio = natW / natH;
            const containerRatio = cw / ch;

            // Figure out displayed dimensions (object-fit: cover logic)
            let dispW, dispH;
            if (imgRatio > containerRatio) {
                dispH = ch;
                dispW = ch * imgRatio;
            } else {
                dispW = cw;
                dispH = cw / imgRatio;
            }

            // How much the image overflows the container
            const overflowX = dispW - cw;
            const overflowY = dispH - ch;

            // object-position converts % to pixel offset of the overflow
            const offsetX = (this.posX / 100) * overflowX;
            const offsetY = (this.posY / 100) * overflowY;

            // Circle center on screen
            const cx = cw / 2;
            const cy = ch / 2;
            const circleLeft = cx - this.cropRadius;
            const circleTop = cy - this.cropRadius;
            const circleDia = this.cropRadius * 2;

            // Convert screen coords to image natural coords
            const scale = natW / dispW;
            const srcX = (circleLeft + offsetX) * scale;
            const srcY = (circleTop + offsetY) * scale;
            const srcSize = circleDia * scale;

            // Draw cropped circle
            const canvas = document.createElement('canvas');
            const size = 512;
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');

            ctx.beginPath();
            ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
            ctx.closePath();
            ctx.clip();

            const img = this.$refs.cropImage;
            ctx.drawImage(img, srcX, srcY, srcSize, srcSize, 0, 0, size, size);

            canvas.toBlob((blob) => {
                if (!blob) return;
                this.croppedBlob = blob;
                this.croppedPreview = URL.createObjectURL(blob);
                this.showCropper = false;

                const dt = new DataTransfer();
                const file = new File([blob], 'profile-photo.jpg', { type: 'image/jpeg' });
                dt.items.add(file);
                const fileInput = document.getElementById('croppedFileInput') || this.$refs.croppedFileInput;
                if (fileInput) {
                    fileInput.files = dt.files;
                }
            }, 'image/jpeg', 0.92);
        }
    };
}
</script>
@endpush

@endsection
