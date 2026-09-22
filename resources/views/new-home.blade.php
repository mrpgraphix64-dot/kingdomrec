@extends('layouts.app')

@section('title', 'Kingdom Recruitments - Next Gen')

@section('content')
<div class="bg-gray-50 dark:bg-slate-950 font-sans text-slate-800 dark:text-slate-200 selection:bg-kingdom-gold selection:text-kingdom-navy overflow-x-hidden transition-colors duration-500">

    <!-- Hero Section: Adaptive (Light: Soft Minimal | Dark: Command Center) -->
    <header class="relative min-h-screen flex items-center justify-center overflow-hidden">
        
        <!-- Animated Background Mesh -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <!-- Adaptive Base -->
            <div class="absolute inset-0 bg-white dark:bg-slate-950"></div>
            
            <!-- Abstract Gradients -->
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-100/50 dark:bg-kingdom-navy/30 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-gold-100/40 dark:bg-kingdom-gold/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
            
            <!-- Grid Pattern (Adaptive) -->
             <div class="absolute inset-0 bg-[linear-gradient(rgba(0,0,0,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(0,0,0,0.03)_1px,transparent_1px)] dark:bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_60%_at_50%_50%,#000_70%,transparent_100%)]"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 z-20 mt-20">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <!-- Text Content -->
                <div class="lg:w-1/2 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-kingdom-gold/30 bg-kingdom-gold/10 text-kingdom-gold dark:text-kingdom-gold text-xs font-bold tracking-widest uppercase mb-8 backdrop-blur-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-kingdom-gold animate-pulse"></span>
                        Decades of Excellence
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-display font-bold leading-tight text-slate-900 dark:text-white mb-6" data-aos="fade-up">
                        Bridging Talent <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-navy to-kingdom-gold dark:from-kingdom-gold dark:to-yellow-200">& Opportunity</span>
                    </h1>
                    
                    <p class="text-lg text-slate-800 dark:text-slate-400 mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed font-medium" data-aos="fade-up" data-aos-delay="100">
                        Connecting world-class professionals with industry-leading organizations across the UK. Your future starts with a conversation.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start" data-aos="fade-up" data-aos-delay="200">
                        <a href="{{ route('applicants.upload') }}" class="group relative px-8 py-4 bg-kingdom-navy dark:bg-kingdom-gold text-white dark:text-kingdom-navy font-bold rounded-lg overflow-hidden transition-all hover:scale-105 hover:shadow-lg hover:shadow-kingdom-navy/20 dark:hover:shadow-[0_0_40px_-10px_rgba(234,179,8,0.5)]">
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            <span class="relative flex items-center gap-2">
                                UPLOAD CV <span class="material-symbols-outlined text-lg">upload_file</span>
                            </span>
                        </a>
                        <a href="{{ route('jobs.index') }}" class="px-8 py-4 bg-white/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-kingdom-navy dark:text-white font-bold rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 transition-all backdrop-blur-md flex items-center gap-2 justify-center shadow-lg shadow-gray-200/50 dark:shadow-none">
                            BROWSE JOBS <span class="material-symbols-outlined text-lg">search</span>
                        </a>
                    </div>
                </div>

                <!-- Glass Card Visual -->
                <div class="lg:w-1/2 relative lg:translate-x-12" data-aos="zoom-in" data-aos-delay="300">
                    <!-- Floater 1 -->
                    <div class="absolute -top-12 -left-12 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-white/40 dark:border-white/10 p-4 rounded-2xl shadow-xl dark:shadow-2xl animate-float-slow">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-500/20 flex items-center justify-center text-green-600 dark:text-green-400">
                                <span class="material-symbols-outlined">check_circle</span>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Success Rate</div>
                                <div class="text-xl font-bold text-slate-900 dark:text-white">98.5%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Hero Image Wrapper -->
                    <div class="relative rounded-[2.5rem] overflow-hidden border border-white/40 dark:border-white/10 shadow-2xl shadow-blue-900/10 dark:shadow-[0_0_100px_-20px_rgba(8,145,178,0.2)] group bg-white dark:bg-slate-800">
                        <img src="{{ asset('images/hero_v3.jpg') }}" alt="Hero" class="w-full object-cover aspect-[4/3] group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-transparent to-transparent dark:from-slate-950 dark:via-transparent dark:to-transparent"></div>
                        
                        <!-- Search Bar Embedded -->
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <div class="bg-white/80 dark:bg-slate-900/90 backdrop-blur-xl border border-white/50 dark:border-white/10 p-4 rounded-2xl flex flex-col md:flex-row gap-4 shadow-lg">
                                <div class="flex-1 relative">
                                    <span class="material-symbols-outlined absolute left-3 top-3 text-slate-400 dark:text-slate-500">search</span>
                                    <input type="text" placeholder="Job Title" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg py-3 pl-10 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-kingdom-gold">
                                </div>
                                <div class="flex-1 relative">
                                    <span class="material-symbols-outlined absolute left-3 top-3 text-slate-400 dark:text-slate-500">location_on</span>
                                    <input type="text" placeholder="Location" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg py-3 pl-10 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-kingdom-gold">
                                </div>
                                <button class="bg-kingdom-navy text-white px-6 py-3 rounded-lg font-bold hover:bg-kingdom-navy/90 border border-transparent">
                                    FIND
                                </button>
                            </div>
                        </div>
                    </div>

                     <!-- Floater 2 -->
                     <div class="absolute -bottom-8 -right-8 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-white/40 dark:border-white/10 p-4 rounded-2xl shadow-xl dark:shadow-2xl animate-float-medium">
                        <div class="flex items-center gap-4">
                            <div class="flex -space-x-2">
                                <img src="https://i.pravatar.cc/100?img=1" class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-900">
                                <img src="https://i.pravatar.cc/100?img=2" class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-900">
                                <img src="https://i.pravatar.cc/100?img=3" class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-900">
                            </div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">4k+ Hired</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Realm/Categories: Grid Style -->
    <section class="py-24 relative overflow-hidden bg-slate-100 dark:bg-slate-900/50 border-t border-slate-200 dark:border-white/5">
         <div class="container mx-auto px-6 relative z-10">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-2">Explore Realms</h2>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">Discover opportunities in specialized sectors.</p>
                </div>
                <button class="text-kingdom-navy dark:text-kingdom-gold hover:text-kingdom-gold dark:hover:text-white transition-colors text-sm font-bold uppercase tracking-widest flex items-center gap-2">
                    View All <span class="material-symbols-outlined text-base">arrow_forward</span>
                </button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($categories as $cat)
                @php 
                    $gradient = str_replace('text-', 'bg-', $cat['color']); 
                @endphp
                <a href="#" class="group relative p-6 bg-kingdom-navy dark:bg-slate-800 border border-kingdom-navy dark:border-white/5 hover:border-kingdom-gold dark:hover:border-kingdom-gold/30 rounded-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden shadow-lg shadow-kingdom-navy/20 hover:shadow-xl dark:shadow-none"
                   data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="absolute inset-0 bg-gradient-to-br from-kingdom-gold/10 dark:from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="mb-4 w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300 group-hover:bg-kingdom-gold group-hover:text-kingdom-navy shadow-inner">
                         <i data-lucide="{{ $cat['icon'] ?? 'briefcase' }}" class="w-6 h-6"></i>
                    </div>
                    
                    <h3 class="text-white font-bold mb-1 tracking-wide">{{ $cat['name'] }}</h3>
                    <p class="text-xs text-white/60 group-hover:text-kingdom-gold transition-colors font-medium">{{ $cat['jobs'] }} Open Roles</p>
                </a>
                @endforeach
            </div>
         </div>
    </section>

    <!-- Why Choose Us: Bento Grid / Editorial -->
    <section class="py-24 bg-white dark:bg-slate-900 relative border-y border-slate-200 dark:border-white/5 transition-colors duration-500">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-5xl font-display font-bold text-slate-900 dark:text-white mb-6">Master Your Craft</h2>
                <p class="text-slate-500 dark:text-slate-400">We don't just fill positions. We architect careers and build legacies for businesses.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Large Card -->
                <div class="md:col-span-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-white/5 rounded-3xl p-8 relative overflow-hidden group shadow-2xl shadow-slate-200/50 dark:shadow-none hover:border-kingdom-gold/30 transition-colors"
                     data-aos="fade-up">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-kingdom-gold/10 dark:bg-kingdom-gold/5 rounded-full blur-[80px]"></div>
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-kingdom-navy flex items-center justify-center text-kingdom-gold mb-6 border border-white/10 shadow-lg shadow-kingdom-navy/20">
                            <span class="material-symbols-outlined text-2xl">campaign</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Advertise a Job</h3>
                            <p class="text-slate-500 dark:text-slate-400 mb-8 max-w-md">Reach thousands of qualified professionals in the hospitality and security sectors with our premium listing service.</p>
                            <button onclick="openModal('advertise')" class="text-kingdom-navy dark:text-white font-bold group-hover:text-kingdom-gold transition-colors flex items-center gap-2">
                                START HIRING <span class="material-symbols-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tall Card -->
                <div class="bg-gradient-to-b from-kingdom-navy to-slate-800 dark:from-kingdom-navy dark:to-slate-900 border border-slate-300 dark:border-white/5 rounded-3xl p-8 relative overflow-hidden group shadow-2xl shadow-slate-200/50 dark:shadow-none hover:border-kingdom-gold/30 transition-colors"
                     data-aos="fade-up" data-aos-delay="200">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 dark:opacity-5"></div>
                    <div class="relative z-10">
                         <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-white mb-6 backdrop-blur-md border border-white/20">
                            <span class="material-symbols-outlined text-2xl">person_search</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Recruiter Profiles</h3>
                        <p class="text-slate-300 text-sm mb-8">Connect with our elite recruitment specialists.</p>
                        <button onclick="openModal('recruiter')" class="w-full py-3 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold border border-white/10 transition-all">
                            VIEW EXPERTS
                        </button>
                    </div>
                </div>

                <!-- Wide Card -->
                <div class="md:col-span-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-white/5 rounded-3xl p-8 md:p-12 flex flex-col md:flex-row items-center gap-12 relative overflow-hidden shadow-2xl shadow-slate-200/50 dark:shadow-none hover:border-kingdom-gold/30 transition-colors"
                     data-aos="fade-up" data-aos-delay="300">
                    <div class="absolute right-0 bottom-0 w-96 h-96 bg-kingdom-navy/5 dark:bg-kingdom-navy/20 rounded-full blur-[100px]"></div>
                    
                    <div class="flex-1 z-10">
                        <h3 class="text-3xl font-display font-bold text-slate-900 dark:text-white mb-4">Find Your Dream Job</h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-8">Access exclusive listings not available on other platforms. Your next career move awaits.</p>
                        <button onclick="openModal('dream_job')" class="px-8 py-4 bg-kingdom-gold text-kingdom-navy font-bold rounded-lg hover:shadow-[0_0_30px_rgba(234,179,8,0.4)] transition-shadow">
                            SEARCH OPPORTUNITIES
                        </button>
                    </div>
                    <div class="flex-1 w-full relative z-10">
                        <!-- UI Mockup -->
                        <div class="relative bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-4 transform rotate-2 shadow-2xl">
                             <div class="flex items-center gap-3 mb-4">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                             </div>
                             <div class="space-y-3">
                                <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-3/4 animate-pulse"></div>
                                <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/2 animate-pulse" style="animation-delay: 0.2s"></div>
                                <div class="h-24 bg-slate-200 dark:bg-slate-800 rounded w-full mt-4 animate-pulse" style="animation-delay: 0.4s"></div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Marquee -->
    <div class="py-12 border-y border-slate-200 dark:border-white/5 bg-white dark:bg-slate-950 shadow-sm relative z-10" data-aos="fade-in">
        <div class="relative flex overflow-x-hidden group">
            <div class="animate-marquee whitespace-nowrap flex gap-12 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest text-lg">
                <span>Top Look</span>
                <span>•</span>
                <span>Trout IT</span>
                <span>•</span>
                <span>Sussex Ltd</span>
                <span>•</span>
                <span>Lawn Hopper</span>
                <span>•</span>
                <span>Top Look</span>
                <span>•</span>
                <span>Trout IT</span>
                 <span>•</span>
                <span>Sussex Ltd</span>
                <span>•</span>
                <span>Lawn Hopper</span>
                 <span>•</span>
                <span>Top Look</span>
                <span>•</span>
                <span>Trout IT</span>
                 <span>•</span>
                <span>Sussex Ltd</span>
                <span>•</span>
                <span>Lawn Hopper</span>
            </div>

            <div class="animate-marquee whitespace-nowrap flex gap-12 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest text-lg pl-12" aria-hidden="true">
                <span>Top Look</span>
                <span>•</span>
                <span>Trout IT</span>
                <span>•</span>
                <span>Sussex Ltd</span>
                <span>•</span>
                <span>Lawn Hopper</span>
                <span>•</span>
                <span>Top Look</span>
                <span>•</span>
                <span>Trout IT</span>
                <span>•</span>
                <span>Sussex Ltd</span>
                <span>•</span>
                <span>Lawn Hopper</span>
                <span>•</span>
                <span>Top Look</span>
                <span>•</span>
                <span>Trout IT</span>
                <span>•</span>
                <span>Sussex Ltd</span>
                <span>•</span>
                <span>Lawn Hopper</span>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <section class="py-24 relative bg-kingdom-navy text-white border-y border-white/10 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-kingdom-gold/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-white/10" data-aos="zoom-in">
                <div class="group">
                     <div class="text-4xl md:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300 counter" data-target="1225">0</div>
                     <div class="text-kingdom-gold text-xs uppercase tracking-widest font-bold">Jobs Posted</div>
                </div>
                 <div class="group">
                     <div class="text-4xl md:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300 counter" data-target="145">0</div>
                     <div class="text-kingdom-gold text-xs uppercase tracking-widest font-bold">Jobs Filled</div>
                </div>
                 <div class="group">
                     <div class="text-4xl md:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300 counter" data-target="170">0</div>
                     <div class="text-kingdom-gold text-xs uppercase tracking-widest font-bold">Partners</div>
                </div>
                 <div class="group">
                     <div class="text-4xl md:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300 counter" data-target="12000" data-suffix="k+">0</div>
                     <div class="text-kingdom-gold text-xs uppercase tracking-widest font-bold">Members</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Split CTA -->
    <section class="grid md:grid-cols-2 min-h-[500px]">
        <!-- Applicants -->
        <div class="relative group overflow-hidden bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-white/5 p-12 flex flex-col justify-center">
            <div class="absolute inset-0 bg-kingdom-navy/5 dark:bg-kingdom-navy/20 translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-700"></div>
            <div class="relative z-10">
                <span class="material-symbols-outlined text-5xl text-kingdom-navy dark:text-kingdom-gold mb-6">person</span>
                <h3 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-4">Applicants</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-8 max-w-sm">Unlock your potential with roles tailored to your expertise.</p>
                <a href="{{ route('applicants.upload') }}" class="inline-block text-kingdom-navy dark:text-white border-b border-kingdom-gold pb-1 hover:text-kingdom-gold transition-colors font-bold">
                    Upload CV
                </a>
            </div>
        </div>

        <!-- Employers -->
         <div class="relative group overflow-hidden bg-slate-50 dark:bg-slate-950 p-12 flex flex-col justify-center">
            <div class="absolute inset-0 bg-kingdom-gold/10 translate-x-[100%] group-hover:translate-x-0 transition-transform duration-700"></div>
            <div class="relative z-10">
                <span class="material-symbols-outlined text-5xl text-white bg-kingdom-navy rounded-full p-2 mb-6 shadow-lg">business</span>
                <h3 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-4">Employers</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-8 max-w-sm">Find the perfect match for your company's culture and needs.</p>
                <a href="{{ route('portal') }}" class="inline-block text-kingdom-navy dark:text-white border-b border-kingdom-gold pb-1 hover:text-kingdom-gold transition-colors font-bold">
                    Post a Job
                </a>
            </div>
        </div>
    </section>

</div>

    <!-- Parallax Script & CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@1.25.0"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800,
        offset: 50,
    });
    lucide.createIcons();

    // Stats Counter Animation
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // The lower the slower

    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText.replace(/[^\d]/g, ''); // Remove non-digits
                const suffix = counter.getAttribute('data-suffix') || '';
                
                // Lower increment to slow and higher to fast
                const inc = target / speed;

                if (count < target) {
                    // Add inc to count and output in counter
                    counter.innerText = Math.ceil(count + inc) + suffix;
                    // Call function every ms
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target.toLocaleString() + suffix;
                }
            };
            updateCount();
        });
    };

    // Trigger Counter on Scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.disconnect(); // Run once
            }
        });
    });

    const statsSection = document.querySelector('.counter');
    if(statsSection) {
        observer.observe(statsSection);
    }

    // Hero Parallax Effect
    document.addEventListener('mousemove', (e) => {
        const blobs = document.querySelectorAll('.animate-pulse');
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;

        blobs.forEach((blob, index) => {
            const speed = (index + 1) * 20;
            const xOffset = (window.innerWidth / 2 - e.clientX) / speed;
            const yOffset = (window.innerHeight / 2 - e.clientY) / speed;

            blob.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
        });
    });
</script>
@endpush
