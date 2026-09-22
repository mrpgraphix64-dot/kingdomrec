@extends('layouts.app')

@section('title', 'Contact Us | Kingdom Recruitments')

@section('content')
<main class="relative z-10 pb-20 bg-white font-body transition-colors duration-300">
    {{-- Hero Section --}}
    <div class="relative w-[95%] mx-auto rounded-3xl overflow-hidden shadow-2xl bg-kingdom-navy pt-32 pb-12 md:pt-48 md:pb-20 mt-0 mb-12 lg:mb-24">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#E60026 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-kingdom-red text-xs font-bold uppercase tracking-wider mb-4 border border-white/20 backdrop-blur-sm">24/7 Support</span>
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-4">
                Let's Start a <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-red to-rose-400">Conversation</span>
            </h1>
            <p class="text-slate-200 text-lg md:text-xl max-w-2xl font-medium">
                Whether you're an applicant looking for your next career move or an employer seeking top talent, our team is ready to help.
            </p>
        </div>
    </div>

    <!-- Split Card -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-900/10 overflow-hidden flex flex-col lg:flex-row min-h-[700px] transition-colors duration-300 border border-gray-200">
            
            <!-- Left Panel (Dark) -->
            <div class="lg:w-2/5 bg-slate-900 text-white p-10 lg:p-14 flex flex-col justify-start pt-20 lg:pt-32 relative overflow-hidden group">
                 <!-- Decorative bg elements -->
                 <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 0.2) 1px, transparent 1px); background-size: 20px 20px;"></div>
                 <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-red-600/20 rounded-full blur-3xl group-hover:bg-red-600/30 transition-all duration-700"></div>

                 <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-2">Contact Information</h3>
                    <p class="text-gray-300 text-sm mb-12">Fill up the form and our team will get back to you within 24 hours.</p>
                    
                    <div class="space-y-8">
                        <a href="tel:07411154198" class="flex items-center gap-4 group/item hover:opacity-80 transition-opacity">
                            <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover/item:bg-red-600 group-hover/item:border-red-600 transition-all duration-300">
                                <span class="material-icons text-white text-xl">phone</span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Phone</p>
                                <p class="text-lg font-medium text-white group-hover/item:text-red-500 transition-colors whitespace-pre-line">074 1115 4198</p>
                            </div>
                        </a>

                        <a href="mailto:info@kingdomrecruitments.com" class="flex items-center gap-4 group/item hover:opacity-80 transition-opacity">
                            <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover/item:bg-red-600 group-hover/item:border-red-600 transition-all duration-300">
                                <span class="material-icons text-white text-xl">email</span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Email</p>
                                <p class="text-lg font-medium text-white group-hover/item:text-red-500 transition-colors break-all">info@kingdomrecruitments.com</p>
                            </div>
                        </a>

                        <a href="https://maps.google.com/?q=8-10+Greatorex+Street,+London+E1+5NF,+UK" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 group/item hover:opacity-80 transition-opacity">
                            <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover/item:bg-red-600 group-hover/item:border-red-600 transition-all duration-300">
                                <span class="material-icons text-white text-xl">location_on</span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Headquarters</p>
                                <p class="text-base leading-snug font-medium text-white group-hover/item:text-red-500 transition-colors whitespace-pre-line">Greatorex Business Center
Suite No: 101 (First Floor)
8-10 Greatorex Street, London E1 5NF</p>
                            </div>
                        </a>
                    </div>
                 </div>
            </div>

            <!-- Right Panel (Form) -->
            <div class="lg:w-3/5 p-10 lg:p-14 bg-white transition-colors duration-300">
                @if(session('success'))
                    <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6 text-sm font-semibold border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm font-semibold border border-red-200">
                        Please correct the errors below.
                    </div>
                @endif

                <form class="space-y-8" action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-900">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all placeholder:text-gray-400" placeholder="John Doe" />
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-900">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all placeholder:text-gray-400" placeholder="john@company.com" />
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-900">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all placeholder:text-gray-400" placeholder="+44 ..." />
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-900">I am a...</label>
                            <select name="role" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all text-gray-600">
                                <option value="Employer looking to hire" {{ old('role') === 'Employer looking to hire' ? 'selected' : '' }}>Employer looking to hire</option>
                                <option value="Applicant looking for a job" {{ old('role') === 'Applicant looking for a job' ? 'selected' : '' }}>Applicant looking for a job</option>
                                <option value="Partner / Other" {{ old('role') === 'Partner / Other' ? 'selected' : '' }}>Partner / Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-900">Message</label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all placeholder:text-gray-400 resize-none" placeholder="How can we help you today?">{{ old('message') }}</textarea>
                        @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-start gap-3">
                            <input id="privacy-consent" name="consent" type="checkbox" value="1" {{ old('consent') ? 'checked' : '' }} class="w-5 h-5 border border-gray-300 rounded text-red-600 accent-red-600 focus:ring-red-600/20 bg-gray-50 cursor-pointer mt-0.5" />
                            <label for="privacy-consent" class="text-sm text-gray-500 leading-tight select-none cursor-pointer">
                                I agree to the <a href="{{ route('privacy') }}" class="text-slate-900 font-semibold hover:underline relative z-10">Privacy Policy</a> and consent to being contacted.
                            </label>
                        </div>
                        @error('consent')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button class="group w-full md:w-auto bg-red-600 text-white font-bold py-4 px-10 rounded-lg shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 hover:bg-red-700 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <span>Send Message</span>
                        <span class="material-icons group-hover:translate-x-1 transition-transform text-sm">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Map Section -->
<section class="relative w-full h-[450px] bg-gray-200 group overflow-hidden border-t-4 border-slate-900">
    <div class="absolute inset-0 bg-slate-900/10 pointer-events-none z-10 mix-blend-multiply"></div>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2482.7214470129766!2d-0.06323602336336336!3d51.5183427718151!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48761cb7061d49ab%3A0xe5a3637e19277f28!2s8-10%20Greatorex%20St%2C%20London%20E1%205NF%2C%20UK!5e0!3m2!1sen!2sus!4v1710000000000!5m2!1sen!2sus" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full grayscale opacity-80 hover:grayscale-0 transition-all duration-1000 ease-in-out"></iframe>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20 bg-white p-6 rounded-xl shadow-2xl flex items-center gap-4 max-w-xs w-full">
        <div class="h-12 w-12 bg-red-600/10 rounded-full flex items-center justify-center text-red-600 shrink-0">
            <span class="material-icons">location_on</span>
        </div>
        <div>
            <p class="text-slate-900 font-bold text-lg">Kingdom HQ</p>
            <p class="text-sm text-gray-500">Greatorex St, London</p>
            <a href="https://maps.google.com/?q=8-10+Greatorex+Street,+London+E1+5NF,+UK" target="_blank" rel="noopener noreferrer" class="text-xs text-red-600 font-bold mt-1 inline-block hover:underline">View on Google Maps</a>
        </div>
    </div>
</section>
@endsection
