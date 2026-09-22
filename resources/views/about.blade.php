@extends('layouts.app')

@section('title', 'About Us - Kingdom Recruitments')

@section('content')
<!-- Hero Section -->
    {{-- Hero Banner --}}
    <div class="relative w-[95%] mx-auto rounded-3xl overflow-hidden shadow-2xl bg-kingdom-navy pt-24 pb-12 md:pt-36 md:pb-20 mt-0 mb-8 lg:mb-12">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#E60026 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-kingdom-red text-xs font-bold uppercase tracking-wider mb-4 border border-white/20 backdrop-blur-sm">About Us</span>
            <h1 class="text-3xl md:text-5xl lg:text-5xl font-extrabold text-white tracking-tight mb-4 max-w-none leading-tight">
                Connecting Elite Talent with <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-red to-rose-400">Global Opportunity</span>
            </h1>
            <p class="text-slate-200 text-lg md:text-xl max-w-2xl font-medium">
                We pride ourselves on our ability to source and recruit top-tier talent for the individual industry.
            </p>
        </div>
    </div>

    <!-- Main Content Section (Moved from old Hero) -->
    <section class="relative w-full py-24 lg:py-32 bg-white dark:bg-background-dark overflow-hidden transition-colors duration-300" x-data="{ videoOpen: false }">
        <div class="container mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center relative z-10">
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-[2px] bg-primary"></span>
                    <span class="text-primary font-bold uppercase tracking-widest text-xs">Who We Are</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-[#1d0c0f] dark:text-white leading-[1.1] tracking-tight">
                    Kingdom Recruitments
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed mb-2">
                    Our extensive experience working with high-profile organizations has equipped us with a deep understanding of the unique staffing needs and challenges faced by luxury organisations. Our comprehensive recruitment process includes thorough applicant screening, background checks, and skill assessments to ensure that we only provide the highest quality personnel, tailored to your specific requirements.
                </p>
                <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                    As a specialist in Hospitality, Healthcare, Security, IT, Accounting, Legal, Warehousing, Manufacturing, Transport, and Customer Service, we offer both temporary and permanent services.
                </p>
                <div class="pt-4 flex flex-wrap gap-4">
                    <a href="{{ url('contact') }}" class="h-12 px-8 rounded-lg bg-primary text-white font-bold text-base hover:bg-[#cc0022] transition-colors shadow-xl shadow-primary/20 flex items-center justify-center">
                        Get in Touch
                    </a>
                    <button @click="videoOpen = true" class="h-12 px-8 rounded-lg bg-gray-50 dark:bg-surface-dark border border-gray-200 dark:border-gray-600 text-[#1d0c0f] dark:text-white font-bold text-base hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center gap-2">
                        <span class="material-icons text-primary" style="font-size: 20px;">play_circle</span>
                        Watch Our Story
                    </button>
                </div>

                <!-- Video Modal -->
                <div x-show="videoOpen" 
                     class="fixed inset-0 z-[200] overflow-y-auto flex items-center justify-center p-4 sm:p-6" 
                     style="display: none;" 
                     role="dialog" 
                     aria-modal="true">
                    <!-- Backdrop -->
                    <div x-show="videoOpen" 
                         x-transition:enter="transition-opacity ease-out duration-300"
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-in duration-200"
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" 
                         @click="videoOpen = false"></div>

                    <!-- Modal Panel -->
                    <div x-show="videoOpen" 
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200 transform"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
                         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                         class="relative w-full max-w-4xl transform overflow-hidden rounded-2xl bg-black shadow-2xl transition-all aspect-video border border-white/10">
                        
                        <!-- Close Button -->
                        <button @click="videoOpen = false" 
                                class="absolute top-4 right-4 z-30 w-10 h-10 flex items-center justify-center bg-black/60 text-white hover:text-red-500 rounded-full transition-all hover:scale-110">
                            <span class="material-icons">close</span>
                        </button>

                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Kingdom Recruitments Story" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            
            <div class="relative group">
                <div class="absolute -inset-4 bg-primary/5 rounded-2xl rotate-2 group-hover:rotate-1 transition-transform duration-500"></div>
                <div class="aspect-[4/3] rounded-xl overflow-hidden shadow-2xl relative bg-gray-200">
                    <img alt="Modern corporate office interior" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmJR91YkltQ7eNHhU9-pvPpfaLgfeCr2JUiQLwvY2Hu5RnkrlZKYdklziHdOLA2bZxwsMLLM88q2GWQeEsjidzwv_EYsnVHYBUyzt5xR6UFbavmGixZBoobwGJZOqZKZQJDEgwPEB428kpVui5VkFealK9ND7wtiuoQ4jW-FgggelyXMqsD80ipAHjWFA7i5SC-AFX3BJHv1hjxaC3GqT7HtzPPNyMajmQZKHI8xr0ab6zOmEX_v5ojBlCeD0ZICqMvCTdnYp8eJl3"/>
                    <div class="absolute inset-0 bg-navy-dark/10 mix-blend-multiply"></div>
                </div>
                <!-- Stats Overlay -->
                <div class="absolute -bottom-6 -left-6 bg-white dark:bg-surface-dark p-6 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 hidden md:block">
                    <div class="flex gap-8">
                        <div>
                            <p class="text-3xl font-black text-[#1d0c0f] dark:text-white">10k+</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider mt-1">Placements</p>
                        </div>
                        <div class="w-[1px] bg-gray-200 dark:bg-gray-600"></div>
                        <div>
                            <p class="text-3xl font-black text-[#1d0c0f] dark:text-white">98%</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider mt-1">Retention Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Easiest Way To Use Section -->
<section class="py-32 bg-navy-dark relative overflow-hidden min-h-[70vh] flex flex-col justify-center">
    <!-- Parallax Background Overlay -->
    <div class="absolute inset-0 opacity-20 bg-fixed bg-center bg-cover pointer-events-none" 
         style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
    </div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-4">Easiest Way To Use</h2>
            <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>

        <div class="grid md:grid-cols-3 gap-12 text-center max-w-6xl mx-auto">
            <!-- Step 1 -->
            <div class="group flex flex-col items-center">
                <div class="mb-8 transition-transform duration-300 group-hover:-translate-y-2">
                    <div class="w-16 h-16 rounded-full bg-primary/20 backdrop-blur-md border border-primary text-white text-2xl font-bold flex items-center justify-center shadow-lg mx-auto">
                        1
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Browse Job</h3>
                <p class="text-gray-400 leading-relaxed px-4">
                    The immediate goal of job seeking is usually to obtain a job interview with an employer which may lead to getting hired.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="group flex flex-col items-center">
                <div class="mb-8 transition-transform duration-300 group-hover:-translate-y-2">
                    <div class="w-16 h-16 rounded-full bg-primary/20 backdrop-blur-md border border-primary text-white text-2xl font-bold flex items-center justify-center shadow-lg mx-auto">
                        2
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Find Your Vacancy</h3>
                <p class="text-gray-400 leading-relaxed px-4">
                    We offer specialists in SIA Security, Accounting, Event Management, Hospitality Staff, Customer Service, and Waiting Staff.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="group flex flex-col items-center">
                <div class="mb-8 transition-transform duration-300 group-hover:-translate-y-2">
                    <div class="w-16 h-16 rounded-full bg-primary/20 backdrop-blur-md border border-primary text-white text-2xl font-bold flex items-center justify-center shadow-lg mx-auto">
                        3
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Submit Resume</h3>
                <p class="text-gray-400 leading-relaxed px-4">
                    Submit your Resume and Join With Us. Build Your Career as your Dream. We are with You With Trust and Love
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Company Timeline Section -->
<section class="py-20 bg-background-light dark:bg-navy-dark overflow-hidden transition-colors duration-300">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <span class="text-primary font-bold uppercase tracking-widest text-xs mb-2 block">Our History</span>
                <h2 class="text-3xl md:text-4xl font-bold text-[#1d0c0f] dark:text-white">Milestones of Growth</h2>
            </div>
            <div class="w-full md:w-auto">
                <p class="text-gray-600 dark:text-gray-400 max-w-sm text-sm">From a small London office to a global recruitment powerhouse.</p>
            </div>
        </div>
        <div class="relative py-10">
            <!-- Central Line -->
            <div class="absolute left-1/2 top-0 bottom-0 w-[2px] bg-gray-200 dark:bg-gray-700 -translate-x-1/2 hidden md:block"></div>
            
            <!-- Year 1 -->
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 mb-16 items-center">
                <div class="md:text-right order-2 md:order-1">
                    <h3 class="text-5xl font-black text-[#eacdd2] dark:text-gray-600 mb-2">2010</h3>
                    <h4 class="text-xl font-bold text-[#1d0c0f] dark:text-white">Foundation in London</h4>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Kingdom Recruitments was established with a small team of 3 in Shoreditch.</p>
                </div>
                <div class="hidden md:flex justify-center order-1 md:order-2 absolute left-1/2 -translate-x-1/2">
                    <div class="w-4 h-4 rounded-full bg-primary ring-4 ring-white dark:ring-navy-dark"></div>
                </div>
                <div class="order-3 md:order-3 md:pl-8"></div>
            </div>
            <!-- Year 2 -->
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 mb-16 items-center">
                <div class="order-2 md:order-1 md:text-right md:pr-8 hidden md:block"></div>
                <div class="hidden md:flex justify-center order-1 md:order-2 absolute left-1/2 -translate-x-1/2">
                    <div class="w-4 h-4 rounded-full bg-primary ring-4 ring-white dark:ring-navy-dark"></div>
                </div>
                <div class="order-3 md:order-3">
                    <h3 class="text-5xl font-black text-[#eacdd2] dark:text-gray-600 mb-2">2015</h3>
                    <h4 class="text-xl font-bold text-[#1d0c0f] dark:text-white">New York Expansion</h4>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Opened our first international office in Manhattan to serve the US market.</p>
                </div>
            </div>
            <!-- Year 3 -->
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 mb-16 items-center">
                <div class="md:text-right order-2 md:order-1">
                    <h3 class="text-5xl font-black text-[#eacdd2] dark:text-gray-600 mb-2">2020</h3>
                    <h4 class="text-xl font-bold text-[#1d0c0f] dark:text-white">10,000 Placements</h4>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Reached a significant milestone helping professionals find their dream careers.</p>
                </div>
                <div class="hidden md:flex justify-center order-1 md:order-2 absolute left-1/2 -translate-x-1/2">
                    <div class="w-4 h-4 rounded-full bg-primary ring-4 ring-white dark:ring-navy-dark"></div>
                </div>
                <div class="order-3 md:order-3 md:pl-8"></div>
            </div>
            <!-- Year 4 -->
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
                <div class="order-2 md:order-1 md:text-right md:pr-8 hidden md:block"></div>
                <div class="hidden md:flex justify-center order-1 md:order-2 absolute left-1/2 -translate-x-1/2">
                    <div class="w-6 h-6 rounded-full bg-white border-4 border-primary ring-4 ring-white dark:ring-navy-dark animate-pulse"></div>
                </div>
                <div class="order-3 md:order-3">
                    <h3 class="text-5xl font-black text-primary/20 dark:text-primary/40 mb-2">2024</h3>
                    <h4 class="text-xl font-bold text-primary">Industry Award Winner</h4>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Voted "Recruitment Agency of the Year" by Global HR Forum.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Banner -->
<section class="py-20 bg-navy-dark text-center px-6">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Ready to find your next role?</h2>
        <p class="text-lg text-gray-300 mb-10 max-w-2xl mx-auto">Join thousands of professionals who have advanced their careers with Kingdom Recruitments.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('contact') }}" class="h-14 px-10 rounded-lg bg-primary text-white text-lg font-bold hover:bg-[#cc0022] transition-colors shadow-xl shadow-primary/20 flex items-center justify-center">
                Contact Us
            </a>
            <a href="{{ route('careers') }}" class="h-14 px-10 rounded-lg bg-transparent border border-gray-500 text-white text-lg font-bold hover:bg-white hover:text-navy-dark transition-colors flex items-center justify-center">
                Join Our Team
            </a>
        </div>
    </div>
</section>
@endsection
