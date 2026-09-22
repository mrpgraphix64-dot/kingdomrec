@extends('layouts.app')

@section('title', 'Upload CV - Kingdom Recruitments')

@section('content')
<div class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-body transition-colors duration-300 text-navy-dark dark:text-white">
    
    {{-- Hero Section --}}
    <div class="relative w-[95%] mx-auto rounded-3xl overflow-hidden shadow-2xl bg-kingdom-navy pt-32 pb-12 md:pt-48 md:pb-20 mt-0">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#E60026 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-4">
                Upload your <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-red to-rose-400">CV</span>
            </h1>
            <p class="text-slate-200 text-lg md:text-xl max-w-2xl font-medium">
                Complete your profile to get matched with top employers.
            </p>
        </div>
    </div>
    
    <main class="flex-grow flex flex-col items-center justify-start pb-16 px-4 md:px-6 pt-12">
        <div class="w-full max-w-[800px] flex flex-col gap-8" 
             x-data="{ 
                 step: 1,
                 categories: {{ json_encode($categories) }},
                 formData: {
                     category: '{{ old('category', '') }}',
                     sub_category: '{{ old('sub_category', '') }}',
                     other_category_description: '{{ old('other_category_description', '') }}'
                 },
                 education: [
                     { id: 1, institution: '', degree: '', start_date: '', end_date: '', is_current: false, description: '' }
                 ],
                 experience: [
                     { id: 1, company: '', job_title: '', start_date: '', end_date: '', is_current: false, description: '' }
                 ],
                 errors: {},
                 fileName: null,
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
                 scrollToFirstError(firstErrorField, container) {
                     this.$nextTick(() => {
                         let targetEl = null;
                         if (firstErrorField === 'cv') {
                             targetEl = container.querySelector('.border-dashed') || container.querySelector('[name=\'cv\']');
                         } else if (firstErrorField === 'category') {
                             targetEl = container.querySelector('#upload-category-select-trigger') || container.querySelector('[name=\'category\']')?.parentElement;
                         } else if (firstErrorField === 'other_category_description') {
                             targetEl = container.querySelector('[name=\'other_category_description\']');
                         } else {
                             targetEl = container.querySelector(`[name='${firstErrorField}']`);
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
                 validateStep(step) {
                     this.errors = {};
                     let isValid = true;
                     const container = this.$refs['step' + step];
                     
                     if (step === 1) {
                        const name = container.querySelector('input[name=name]');
                        if (!name || !name.value.trim()) {
                            this.errors.name = 'Please enter your full name.';
                            isValid = false;
                        }
                        const email = container.querySelector('input[name=email]');
                        if (!email || !email.value.trim()) {
                             this.errors.email = 'Please enter your email address.';
                             isValid = false;
                        }
                        const phone = container.querySelector('input[name=phone]');
                        if (!phone || !phone.value.trim() || phone.value.trim() === 'Not provided') {
                             this.errors.phone = 'Please enter your phone number.';
                             isValid = false;
                        }
                         const location = container.querySelector('input[name=location]');
                         if (!location || !location.value.trim() || location.value.trim() === 'Not provided') {
                              this.errors.location = 'Please enter your location.';
                              isValid = false;
                         }
                     }
                     
                     if (step === 2) {
                         if (!this.formData.category) {
                             this.errors.category = 'Please select a job category.';
                             isValid = false;
                         } else if (this.formData.category === 'Other / Not Listed') {
                             if (!this.formData.other_category_description || !this.formData.other_category_description.trim()) {
                                 this.errors.other_category_description = 'Please tell us what type of work you are looking for.';
                                 isValid = false;
                             }
                         }
                         const photo = container.querySelector('input[name=photo]');
                         if (photo && photo.files.length === 0 && !photo.hasAttribute('data-has-file')) { 
                              if(!photo.value) {
                                  this.errors.photo = 'Please upload a photo';
                                  isValid = false;
                              }
                         }
                     }

                     if (step === 3) {
                         if (!this.fileName) {
                             this.errors.cv = 'Please upload your CV (PDF only)';
                             isValid = false;
                         }
                     }

                     if (!isValid) {
                         const firstErrorField = Object.keys(this.errors)[0];
                         this.scrollToFirstError(firstErrorField, container);
                     }
                     
                     return isValid;
                 },
                 clearError(field) {
                     if (this.errors[field]) {
                         delete this.errors[field];
                     }
                 },
                 handleEnter(e) {
                     if (e.target.tagName === 'TEXTAREA') return; 
                     if (e.target.tagName === 'BUTTON') return;
                     
                     e.preventDefault();

                     if (this.step === 1) {
                         if (this.validateStep(1)) this.step = 2;
                     } else if (this.step === 2) {
                         if (this.validateStep(2)) this.step = 3;
                     } else if (this.step === 3) {
                         this.submitForm();
                     }
                 },
                 submitForm() {
                     if (this.validateStep(3) && Object.keys(this.errors).length === 0) {
                        this.$refs.form.submit();
                     }
                 }
             }">
            
            <!-- Progress Bar -->
            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-end">
                    <span class="text-primary font-bold text-sm uppercase tracking-wider" x-text="'Step ' + step + ' of 3'"></span>
                    <span class="text-gray-400 text-xs font-medium" x-show="step === 3">Almost done!</span>
                </div>
                <div class="h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-primary transition-all duration-500 ease-out rounded-full" :style="'width: ' + (step / 3 * 100) + '%'"></div>
                </div>
            </div>

            <form x-ref="form" novalidate @keydown.enter="handleEnter($event)" action="{{ route('applicants.storeCv') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-surface-dark p-6 md:p-8 rounded-2xl shadow-xl dark:shadow-none border border-gray-100 dark:border-gray-700">
                @csrf
                
                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-8 p-6 bg-red-500/10 border border-red-500/20 text-red-500 rounded-2xl flex flex-col gap-2">
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
                
                <!-- Step 1: Basic Information -->
                <div x-show="step === 1" x-ref="step1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-xl font-bold mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">Basic Information</h2>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">Your Name<span class="text-red-500">*</span></label>
                            <input type="text" name="name" required 
                                value="{{ old('name', $existingApplicant->name ?? (Auth::check() ? Auth::user()->name : '')) }}"
                                @input="clearError('name')"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600" 
                                :class="{'border-red-500 focus:ring-red-500': errors.name}"
                                placeholder="Your Name">
                             <span x-show="errors.name" x-text="errors.name" class="text-xs text-red-500 font-bold mt-1 block"></span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">Email Address<span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="material-icons text-gray-400">email</span>
                                    </div>
                                    <input type="email" name="email" required 
                                        value="{{ old('email', $existingApplicant->email ?? (Auth::check() ? Auth::user()->email : '')) }}"
                                        @if(Auth::check()) readonly @endif
                                        @input="clearError('email')"
                                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600 @if(Auth::check()) cursor-not-allowed opacity-75 @endif" 
                                        :class="{'border-red-500 focus:ring-red-500': errors.email}"
                                        placeholder="you@example.com">
                                </div>
                                <span x-show="errors.email" x-text="errors.email" class="text-xs text-red-500 font-bold mt-1 block"></span>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">Phone Number<span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="material-icons text-gray-400">phone</span>
                                    </div>
                                    <input type="tel" name="phone" required 
                                        value="{{ old('phone', ($existingApplicant->phone === 'Not provided' ? '' : ($existingApplicant->phone ?? '')) ) }}"
                                        @input="clearError('phone')"
                                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600" 
                                        :class="{'border-red-500 focus:ring-red-500': errors.phone}"
                                        placeholder="+44 7123 456789">
                                </div>
                                <span x-show="errors.phone" x-text="errors.phone" class="text-xs text-red-500 font-bold mt-1 block"></span>
                            </div>
                        </div>

                        <div class="space-y-2 mt-4">
                            <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">Location (City / Town)<span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-icons text-gray-400">place</span>
                                </div>
                                <input type="text" name="location" required 
                                    value="{{ old('location', ($existingApplicant->location === 'Not provided' ? '' : ($existingApplicant->location ?? '')) ) }}"
                                    @input="clearError('location')"
                                    class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none placeholder-gray-400 dark:placeholder-gray-600" 
                                    :class="{'border-red-500 focus:ring-red-500': errors.location}"
                                    placeholder="e.g. London, Birmingham, Manchester">
                            </div>
                            <span x-show="errors.location" x-text="errors.location" class="text-xs text-red-500 font-bold mt-1 block"></span>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="button" @click="if(validateStep(1)) step = 2" class="bg-primary hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                                Next Step <span class="material-icons text-sm">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Job Details & Photo -->
                <div x-show="step === 2" x-ref="step2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-xl font-bold mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">Job Details</h2>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Job Category Custom Dropdown -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">Job Category<span class="text-red-500">*</span></label>
                                <div class="relative" x-data="{ open: false }">
                                    <input type="hidden" name="category" :value="formData.category">

                                    <button type="button" id="upload-category-select-trigger" @click="open = !open" @click.outside="open = false"
                                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none text-left flex items-center justify-between"
                                        :class="errors.category ? 'border-red-500 ring-1 ring-red-500' : (formData.category ? 'text-gray-700 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400')">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-icons text-gray-400">work</span>
                                        </div>
                                        <span x-text="formData.category || 'Select Category'"></span>
                                        <span class="material-icons text-gray-500 transition-transform duration-200" :class="{'rotate-180': open}">expand_more</span>
                                    </button>
                                    
                                    <div x-show="open" x-cloak class="absolute z-50 w-full mt-2 bg-white dark:bg-navy-dark rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden max-h-64 overflow-y-auto">
                                        <template x-for="cat in categories" :key="cat.id">
                                            <button type="button" @click="selectCategory(cat.name); open = false" 
                                                class="w-full text-left px-4 py-3 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center justify-between transition-colors">
                                                <span x-text="cat.name"></span>
                                                <span x-show="formData.category === cat.name" class="material-icons text-primary text-sm">check</span>
                                            </button>
                                        </template>

                                        <!-- Other / Not Listed Option -->
                                        <button type="button" @click="selectCategory('Other / Not Listed'); open = false"
                                            class="w-full text-left px-4 py-3 text-sm font-medium hover:bg-amber-50 dark:hover:bg-amber-900/20 text-amber-600 dark:text-amber-400 flex items-center justify-between transition-colors border-t border-gray-100 dark:border-gray-700">
                                            <span class="flex items-center gap-2">
                                                <span class="material-icons text-sm">add_circle_outline</span>
                                                <span>Other / Not Listed</span>
                                            </span>
                                            <span x-show="formData.category === 'Other / Not Listed'" class="material-icons text-primary text-sm">check</span>
                                        </button>
                                    </div>
                                </div>
                                <span x-show="errors.category" x-text="errors.category" class="text-xs text-red-500 font-bold mt-1 block"></span>
                            </div>

                            <!-- Conditional "Other / Not Listed" Work Description Field -->
                            <div x-show="formData.category === 'Other / Not Listed'" x-cloak class="space-y-2 animate-fade-in-up">
                                <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">
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
                                           class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark focus:ring-2 focus:ring-primary focus:border-transparent outline-none text-sm font-medium transition-all"
                                           :class="errors.other_category_description ? 'border-red-500 ring-1 ring-red-500' : ''">
                                </div>
                                <span x-show="errors.other_category_description" x-text="errors.other_category_description" class="text-xs text-red-500 font-bold mt-1 block"></span>
                            </div>

                            <!-- Job Sub Category Custom Dropdown (Only for standard categories with sub categories) -->
                            <div x-show="formData.category && formData.category !== 'Other / Not Listed' && filteredSubCategories.length > 0" x-cloak class="space-y-2">
                                <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">Specialisation / Sub Category</label>
                                <div class="relative" x-data="{ openSub: false }">
                                    <input type="hidden" name="sub_category" :value="formData.sub_category">
                                    <button type="button" @click="openSub = !openSub" @click.outside="openSub = false"
                                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none text-left flex items-center justify-between">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-icons text-gray-400">badge</span>
                                        </div>
                                        <span :class="formData.sub_category ? 'text-gray-700 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400'" x-text="formData.sub_category || 'Select Sub Category (Optional)'"></span>
                                        <span class="material-icons text-gray-500 transition-transform duration-200" :class="{'rotate-180': openSub}">expand_more</span>
                                    </button>
                                    
                                    <div x-show="openSub" x-cloak class="absolute z-50 w-full mt-2 bg-white dark:bg-navy-dark rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden max-h-60 overflow-y-auto">
                                        <button type="button" @click="formData.sub_category = ''; openSub = false"
                                            class="w-full text-left px-4 py-3 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-400 dark:text-gray-500 flex items-center justify-between transition-colors border-b border-gray-100 dark:border-gray-700">
                                            <span>None / General</span>
                                            <span x-show="!formData.sub_category" class="material-icons text-primary text-sm">check</span>
                                        </button>
                                        <template x-for="sub in filteredSubCategories" :key="sub.id">
                                            <button type="button" @click="formData.sub_category = sub.name; openSub = false" 
                                                class="w-full text-left px-4 py-3 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center justify-between transition-colors">
                                                <span x-text="sub.name"></span>
                                                <span x-show="formData.sub_category === sub.name" class="material-icons text-primary text-sm">check</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- Photo Upload -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">Photo Upload<span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <input type="file" name="photo" accept="image/*" required
                                    @change="
                                        const file = $event.target.files[0];
                                        if(file) {
                                            if(file.size > 2097152) {
                                                errors.photo = 'File is too large. Max size is 2MB.';
                                                $event.target.value = '';
                                            } else {
                                                clearError('photo');
                                            }
                                        }
                                    "
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all text-sm text-gray-500 dark:text-gray-400 cursor-pointer"
                                    :class="{'border-red-500 focus:ring-red-500': errors.photo}">
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Max size: 2MB. Formats: JPEG, PNG</span>
                                <span x-show="errors.photo" x-text="errors.photo" class="text-xs text-red-500 font-bold"></span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-navy-dark dark:text-gray-200">Photo Focus Point (Optional)</label>
                            <select name="image_position" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-navy-dark focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none text-sm text-gray-700 dark:text-gray-300">
                                <option value="object-center" {{ old('image_position', $existingApplicant->image_position ?? 'object-center') == 'object-center' ? 'selected' : '' }}>Center (Default)</option>
                                <option value="object-top" {{ old('image_position', $existingApplicant->image_position ?? '') == 'object-top' ? 'selected' : '' }}>Top</option>
                                <option value="object-bottom" {{ old('image_position', $existingApplicant->image_position ?? '') == 'object-bottom' ? 'selected' : '' }}>Bottom</option>
                                <option value="object-left" {{ old('image_position', $existingApplicant->image_position ?? '') == 'object-left' ? 'selected' : '' }}>Left</option>
                                <option value="object-right" {{ old('image_position', $existingApplicant->image_position ?? '') == 'object-right' ? 'selected' : '' }}>Right</option>
                            </select>
                            <span class="text-xs text-gray-500">Choose how the image is positioned if it needs cropping.</span>
                        </div>
                    </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6"></div>

                        <!-- Experience Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-navy-dark dark:text-gray-200">Work Experience</h3>
                                <button type="button" @click="addExperience()" class="text-sm font-bold text-primary hover:text-red-700 flex items-center gap-1 transition-colors">
                                    <span class="material-icons text-base">add_circle</span> Add Position
                                </button>
                            </div>
                            
                            <div class="space-y-6">
                                <template x-for="(exp, index) in experience" :key="exp.id">
                                    <div class="bg-gray-50 dark:bg-navy-dark/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 relative group animate-fade-in-up">
                                        <button type="button" @click="removeExperience(index)" x-show="experience.length > 0" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors p-1" title="Remove">
                                            <span class="material-icons text-sm">close</span>
                                        </button>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div class="space-y-1">
                                                <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">Company Name</label>
                                                <input type="text" :name="'experience[' + index + '][company]'" x-model="exp.company" placeholder="e.g. Kingdom Events"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-gray-400 dark:placeholder-gray-600">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">Job Title</label>
                                                <input type="text" :name="'experience[' + index + '][job_title]'" x-model="exp.job_title" placeholder="e.g. Security Officer"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-gray-400 dark:placeholder-gray-600">
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div class="space-y-1">
                                                <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">Start Date</label>
                                                <input type="date" :name="'experience[' + index + '][start_date]'" x-model="exp.start_date"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all text-gray-500 placeholder-gray-400 dark:placeholder-gray-600">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">End Date</label>
                                                <input type="date" :name="'experience[' + index + '][end_date]'" x-model="exp.end_date" :disabled="exp.is_current"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed placeholder-gray-400 dark:placeholder-gray-600">
                                                <div class="flex items-center gap-2 mt-1">
                                                    <input type="checkbox" :name="'experience[' + index + '][is_current]'" x-model="exp.is_current" :id="'current_exp_' + index" 
                                                        class="rounded text-primary focus:ring-primary bg-gray-100 border-gray-300">
                                                    <label :for="'current_exp_' + index" class="text-xs font-bold text-gray-500 cursor-pointer select-none">I currently work here</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-1">
                                            <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">Description (Optional)</label>
                                            <textarea :name="'experience[' + index + '][description]'" x-model="exp.description" rows="2" placeholder="Briefly describe your responsibilities..."
                                                class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all resize-none placeholder-gray-400 dark:placeholder-gray-600"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-2"></div>

                        <!-- Education Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-navy-dark dark:text-gray-200">Education</h3>
                                <button type="button" @click="addEducation()" class="text-sm font-bold text-primary hover:text-red-700 flex items-center gap-1 transition-colors">
                                    <span class="material-icons text-base">add_circle</span> Add Education
                                </button>
                            </div>
                            
                            <div class="space-y-6">
                                <template x-for="(edu, index) in education" :key="edu.id">
                                    <div class="bg-gray-50 dark:bg-navy-dark/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 relative group animate-fade-in-up">
                                        <button type="button" @click="removeEducation(index)" x-show="education.length > 0" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors p-1" title="Remove">
                                            <span class="material-icons text-sm">close</span>
                                        </button>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div class="space-y-1">
                                                <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">Institution / School</label>
                                                <input type="text" :name="'education[' + index + '][institution]'" x-model="edu.institution" placeholder="e.g. University of London"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-gray-400 dark:placeholder-gray-600">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">Degree / Qualification</label>
                                                <input type="text" :name="'education[' + index + '][degree]'" x-model="edu.degree" placeholder="e.g. BSc Computer Science"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all placeholder-gray-400 dark:placeholder-gray-600">
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div class="space-y-1">
                                                <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">Start Date</label>
                                                <input type="date" :name="'education[' + index + '][start_date]'" x-model="edu.start_date"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all text-gray-500 placeholder-gray-400 dark:placeholder-gray-600">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">End Date</label>
                                                <input type="date" :name="'education[' + index + '][end_date]'" x-model="edu.end_date" :disabled="edu.is_current"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-navy-dark focus:ring-2 focus:ring-primary/50 outline-none text-sm font-medium transition-all text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed placeholder-gray-400 dark:placeholder-gray-600">
                                                <div class="flex items-center gap-2 mt-1">
                                                    <input type="checkbox" :name="'education[' + index + '][is_current]'" x-model="edu.is_current" :id="'current_edu_' + index" 
                                                        class="rounded text-primary focus:ring-primary bg-gray-100 border-gray-300">
                                                    <label :for="'current_edu_' + index" class="text-xs font-bold text-gray-500 cursor-pointer select-none">Currently studying</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>



                    <div class="mt-8 flex items-center justify-between gap-4">
                        <button type="button" @click="step = 1" class="text-gray-500 dark:text-gray-400 font-semibold hover:text-navy-dark dark:hover:text-white transition-colors px-4">
                            Back
                        </button>
                        <button type="button" @click="if(validateStep(2)) step = 3" class="bg-primary hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                            Next Step <span class="material-icons text-sm">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- Step 3: CV Upload (Original Design) -->
                <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-xl font-bold mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">Upload Resume</h2>

                    <!-- Upload Area -->
                    <div class="flex flex-col gap-4">
                        <div class="relative group cursor-pointer" x-data="{ dragging: false }">
                            <!-- Custom Dash Border Container -->
                            <div 
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false"
                                :class="{ 'border-primary bg-red-50/30 dark:bg-red-900/10': dragging, 'border-gray-300 dark:border-gray-600': !dragging }"
                                class="border-2 border-dashed hover:border-primary hover:bg-red-50/30 dark:hover:bg-red-900/10 transition-all duration-300 w-full min-h-[280px] flex flex-col items-center justify-center p-8 gap-6 rounded-xl relative"
                                style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); background-opacity: 0.05;"
                            >
                                <input type="file" name="cv" required accept=".pdf"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    x-on:change="
                                        const file = $event.target.files[0];
                                        if(file) {
                                            const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                                            if(!isPdf) {
                                                errors.cv = 'Only PDF files are allowed.';
                                                fileName = null;
                                                $event.target.value = '';
                                                return;
                                            }
                                            if(file.size < 1024) {
                                                errors.cv = 'File is too small. Minimum size is 1KB.';
                                                fileName = null;
                                                $event.target.value = '';
                                            } else if(file.size > 5242880) {
                                                errors.cv = 'File is too large. Maximum size is 5MB.';
                                                fileName = null;
                                                $event.target.value = '';
                                            } else {
                                                clearError('cv');
                                                fileName = file.name;
                                            }
                                        }
                                    " 
                                />
                                
                                <!-- Upload State -->
                                <div x-show="!fileName" class="flex flex-col items-center justify-center transition-all duration-300">
                                    <div class="size-16 rounded-full bg-slate-50 dark:bg-navy-dark flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                                        <span class="material-icons text-primary text-3xl">cloud_upload</span>
                                    </div>
                                    <div class="text-center space-y-2 max-w-xs px-4">
                                        <p class="text-navy-dark dark:text-white text-lg font-bold">
                                            Drag & drop your resume here
                                        </p>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                                            or <span class="text-primary font-bold underline decoration-2 decoration-primary/30 underline-offset-4 hover:decoration-primary">browse PDF file</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Success/Preview State -->
                                <div x-show="fileName" x-cloak class="flex flex-col items-center justify-center transition-all duration-300 relative z-20 pointer-events-none">
                                    <div class="size-20 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mb-4 shadow-sm border border-green-100 dark:border-green-800">
                                        <span class="material-icons text-green-500 text-4xl">description</span>
                                    </div>
                                    <div class="text-center space-y-1">
                                        <p class="text-navy-dark dark:text-white text-lg font-bold truncate max-w-[250px]" x-text="fileName"></p>
                                        <div class="flex items-center justify-center gap-1 text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/10 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                            <span class="material-icons text-sm">check_circle</span>
                                            <span>Ready to Submit</span>
                                        </div>
                                    </div>
                                    <button type="button" 
                                        @click.prevent.stop="fileName = null; $el.closest('.group').querySelector('input').value = ''"
                                        class="mt-6 text-gray-400 hover:text-red-500 text-sm font-semibold flex items-center gap-1 transition-colors px-4 py-2 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg pointer-events-auto">
                                        <span class="material-icons text-sm">delete</span> Remove & Upload New
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Meta Text -->
                        <!-- Meta Text -->
                        <div class="flex flex-col items-center justify-center gap-1 text-gray-500 dark:text-gray-400 text-sm">
                             <div class="flex items-center gap-2">
                                <span class="material-icons text-base">info</span>
                                <span>Supported format: <strong>PDF</strong> only (Max 5MB)</span>
                            </div>
                            <!-- Error Message Display -->
                            <div x-show="errors.cv" x-transition.opacity class="flex items-center gap-2 text-red-500 bg-red-50 dark:bg-red-900/10 px-3 py-1 rounded-full text-xs font-bold mt-2">
                                <span class="material-icons text-sm">error</span>
                                <span x-text="errors.cv"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="relative flex py-8 items-center">
                        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
                        <span class="flex-shrink-0 mx-4 text-gray-400 text-sm font-medium uppercase tracking-wide">Or</span>
                        <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
                    </div>

                    <!-- LinkedIn -->
                    <div class="flex justify-center mb-8">
                        <button type="button" class="w-full md:w-auto flex items-center justify-center gap-3 py-3 px-6 rounded-lg border-2 border-primary/20 hover:border-primary/50 hover:bg-primary/5 dark:hover:bg-primary/10 transition-all group bg-transparent">
                            <span class="material-icons text-primary group-hover:scale-110 transition-transform">link</span>
                            <span class="text-navy-dark dark:text-white font-bold text-sm">Link LinkedIn Profile</span>
                        </button>
                    </div>

                    <div class="mt-8 flex items-center justify-between gap-4">
                        <button type="button" @click="step = 2" class="text-gray-500 dark:text-gray-400 font-semibold hover:text-navy-dark dark:hover:text-white transition-colors px-4">
                            Back
                        </button>
                        <button type="button" @click="submitForm()" class="bg-primary hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                            Submit Application <span class="material-icons text-sm">check</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </main>
</div>
@endsection
