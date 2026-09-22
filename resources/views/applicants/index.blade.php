@extends('layouts.app')

@section('title', 'Applicants - Kingdom Recruitments')

@section('content')
    {{-- Hero Section --}}
    {{-- Hero Section --}}
    <div class="relative w-[95%] mx-auto rounded-3xl overflow-hidden shadow-2xl bg-kingdom-navy mt-0">
        {{-- Background Image with Overlay --}}
        <div class="absolute inset-0 z-0">
            <img alt="Diverse professional team working together in a modern office" class="h-full w-full object-cover object-center opacity-40" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBDeydMUZNEKE_OikUEoKPUxkXwPreziuKNCGd_5bpoiFsC9tOwrMa6fU5N0wnaYLV9mKA2uVCQ96jFtv1PbfGL5JUPcazr9L9dm0QpcyhQEbLe0RCODYIiRMeUqotKcs7Juqla52lyvN34mOdAd6KeYiFBv0UM-o9mUioDkda4r8lLyNHRa4joWKwyKyLfuBGXIiLd0G9cdDzdwiJpwZx8j18MmZnQwftPqtWc3EJQ_RUd8TgW4GnnpO7PpwssmHPoxCX5W56TIxxl"/>
            <div class="absolute inset-0 bg-gradient-to-r from-kingdom-navy via-kingdom-navy/80 to-kingdom-navy/30 dark:from-navy-dark dark:via-navy-dark/80 dark:to-navy-dark/30"></div>
        </div>
        <div class="relative z-10 mx-auto max-w-7xl px-6 py-32 sm:py-40 lg:px-8 lg:py-48 mt-12 md:mt-0">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full bg-kingdom-red/10 border border-kingdom-red/20 px-3 py-1 mb-6 backdrop-blur-sm">
                    <span class="flex h-2 w-2 rounded-full bg-kingdom-red"></span>
                    <span class="text-xs font-bold text-white tracking-wide uppercase">New Roles Added Daily</span>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-6xl mb-6 leading-[1.1]">
                    Elevate Your Career <br class="hidden sm:block"/> with Kingdom
                </h1>
                <p class="text-lg leading-8 text-gray-300 mb-10 max-w-xl">
                    Connect with top-tier employers across the UK. We bridge the gap between your ambition and the world's leading companies.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('applicants.upload') }}" class="flex items-center justify-center gap-2 bg-kingdom-red hover:bg-red-700 text-white text-base font-bold px-8 py-4 rounded-lg transition-all shadow-xl shadow-kingdom-red/25 hover:shadow-kingdom-red/40 hover:-translate-y-0.5">
                        <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                        Upload CV
                    </a>
                    <a href="{{ route('jobs.index') }}" class="flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-sm text-base font-bold px-8 py-4 rounded-lg transition-all">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        Search Jobs
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Banner --}}
    <section class="relative z-20 -mt-8 mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-px bg-gray-200/50 dark:bg-gray-700/50 overflow-hidden rounded-2xl shadow-xl lg:grid-cols-4">
            <div class="bg-white dark:bg-navy-dark p-6 sm:p-8 text-center transition-colors">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jobs Placed</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-kingdom-navy dark:text-white">15k+</dd>
            </div>
            <div class="bg-white dark:bg-navy-dark p-6 sm:p-8 text-center transition-colors">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Roles</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-kingdom-navy dark:text-white">2,400</dd>
            </div>
            <div class="bg-white dark:bg-navy-dark p-6 sm:p-8 text-center transition-colors">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employers</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-kingdom-navy dark:text-white">500+</dd>
            </div>
            <div class="bg-white dark:bg-navy-dark p-6 sm:p-8 text-center transition-colors">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Time to Hire</dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-kingdom-navy dark:text-white">14 Days</dd>
            </div>
        </div>
    </section>

    {{-- Featured Sectors --}}
    <section class="py-24 bg-slate-50 dark:bg-background-dark transition-colors">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-kingdom-navy dark:text-white sm:text-4xl">Featured Sectors</h2>
                    <p class="mt-4 text-lg text-slate-600 dark:text-gray-400">Explore opportunities in high-growth industries.</p>
                </div>
                <a href="{{ route('jobs.index') }}" class="text-kingdom-red font-bold flex items-center gap-1 hover:gap-2 transition-all">
                    View all sectors <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $sector)
                <a href="{{ route('jobs.index', ['sector' => $sector['name']]) }}" class="group flex flex-col items-center gap-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-navy-dark p-6 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-kingdom-red/30 hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-kingdom-red/5 dark:bg-kingdom-red/10 text-kingdom-red group-hover:bg-kingdom-red group-hover:text-white transition-colors">
                        <i data-lucide="{{ $sector['icon'] }}" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-sm font-bold text-kingdom-navy dark:text-white">{{ $sector['name'] }}</h3>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Career Resources --}}
    <section class="py-24 bg-white dark:bg-navy-dark/50 transition-colors">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-kingdom-navy dark:text-white sm:text-4xl">Tools for Your Success</h2>
                <p class="mt-4 text-lg text-slate-600 dark:text-gray-400">Expert advice to help you land your dream job, from CV writing to interview prep.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Resource Card 1 -->
                <article class="flex flex-col overflow-hidden rounded-2xl bg-white dark:bg-navy-dark shadow-lg ring-1 ring-gray-200 dark:ring-gray-700 transition-all hover:shadow-xl hover:ring-kingdom-red/20">
                    <div class="h-48 overflow-hidden">
                        <img alt="Person writing on a resume with a pen" class="h-full w-full object-cover transition-transform duration-500 hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCO8roQ6xsyYjhQfMVyoGZYywrauNpfaDQhCIBgG2RSa7fWzd2-vHRFaAOIZHZTSohqXATocR_cUvs4MFeVGG3VpujF3_mTlgwfhlHzjAd021lLx3QDq7E4O5VfIwUGqpvvFr58bwJyqnlD-0an3qRGn2hmbNKQ4XSArqUjA41R7tKOeLhbgB__1jVkdKLwZURlHFYu7UlKeL1EH20wz045Y8oVkcmHc5nmfTIi_nMOcDR-cpAaopAQWeQaa7hGJsGg3b4gATaPraa-"/>
                    </div>
                    <div class="flex flex-1 flex-col p-8">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-kingdom-red mb-3">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Guides
                        </div>
                        <h3 class="text-xl font-bold text-kingdom-navy dark:text-white mb-3">Mastering the Modern CV</h3>
                        <p class="flex-1 text-slate-600 dark:text-gray-400 mb-6">Learn the key elements that make a CV stand out to recruiters in {{ date('Y') }}. Format, tone, and keywords.</p>
                        <a href="#" onclick="Swal.fire({title: 'Coming Soon', text: 'This guide will be available shortly.', icon: 'info', confirmButtonColor: '#e60026'}); event.preventDefault();" class="text-sm font-bold text-kingdom-navy dark:text-gray-300 hover:text-kingdom-red dark:hover:text-kingdom-red flex items-center gap-1 group transition-colors">
                            Read Guide <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </article>
                <!-- Resource Card 2 -->
                <article class="flex flex-col overflow-hidden rounded-2xl bg-white dark:bg-navy-dark shadow-lg ring-1 ring-gray-200 dark:ring-gray-700 transition-all hover:shadow-xl hover:ring-kingdom-red/20">
                    <div class="h-48 overflow-hidden">
                        <img alt="Two people shaking hands during an interview" class="h-full w-full object-cover transition-transform duration-500 hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuADRhbBULPNLYWfG2bu1Il4Z5ZGKtSeI4QRECVw52N5hArtjFCGEpdx3Yyy1U65heOvI1tltrNd2xWZJsPbOWtS9AQuSFP1YBzPcLntU3hTRX8WjCYxexk7AcqilxhTq5tgZ4Hqf8OIe5l2xcr9jX5R5rR5BIIlG9ERC8-bsygsUmIIfttTVj11ZRX2JTBzwonDxmj29GANhN-RqODxl7V6TAfO4Dflnf-ihQhs4hhueeftxEBaKIc9djQ0j4ZdKSrhsAq2wxY9RCEI"/>
                    </div>
                    <div class="flex flex-1 flex-col p-8">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-kingdom-red mb-3">
                            <i data-lucide="mic-2" class="w-4 h-4"></i>
                            Interview Prep
                        </div>
                        <h3 class="text-xl font-bold text-kingdom-navy dark:text-white mb-3">Acing the Technical Interview</h3>
                        <p class="flex-1 text-slate-600 dark:text-gray-400 mb-6">From coding challenges to behavioral questions, get prepared for every stage of the process.</p>
                        <a href="#" onclick="Swal.fire({title: 'Coming Soon', text: 'This guide will be available shortly.', icon: 'info', confirmButtonColor: '#e60026'}); event.preventDefault();" class="text-sm font-bold text-kingdom-navy dark:text-gray-300 hover:text-kingdom-red dark:hover:text-kingdom-red flex items-center gap-1 group transition-colors">
                            Read Article <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </article>
                <!-- Resource Card 3 -->
                <article class="flex flex-col overflow-hidden rounded-2xl bg-white dark:bg-navy-dark shadow-lg ring-1 ring-gray-200 dark:ring-gray-700 transition-all hover:shadow-xl hover:ring-kingdom-red/20">
                    <div class="h-48 overflow-hidden">
                        <img alt="Graph chart showing salary growth trends" class="h-full w-full object-cover transition-transform duration-500 hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC-MeCltgnugMIsArgrbt2lKJlCXUhpLQenk31oCKWEI-HP90XpVcRBi5Bf2cfM7SPvYscfzZHz9UOtV_DrnFZWfulliz51bZf9dDkdLpzznvCqwi_GjEHPi0o-bvf5e6Ok4IyT6nzRd949r9aX6DOtdBT1BFg_caeDlA069H4x8uy964R6hXYZRqUCxRKDNJnX4CJMEFZgT5UT3brS7GXFnDjJQ1BsGwD75cncqqyZxxGXGuBhjta6FYJJ0dJXF0w7TYpiVxd4zwNW"/>
                    </div>
                    <div class="flex flex-1 flex-col p-8">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-kingdom-red mb-3">
                            <i data-lucide="trending-up" class="w-4 h-4"></i>
                            Insights
                        </div>
                        <h3 class="text-xl font-bold text-kingdom-navy dark:text-white mb-3">{{ date('Y') }} Salary Benchmarking</h3>
                        <p class="flex-1 text-slate-600 dark:text-gray-400 mb-6">Know your worth. We analyze data across thousands of placements to give you accurate ranges.</p>
                        <a href="#" onclick="Swal.fire({title: 'Coming Soon', text: 'This report will be available shortly.', icon: 'info', confirmButtonColor: '#e60026'}); event.preventDefault();" class="text-sm font-bold text-kingdom-navy dark:text-gray-300 hover:text-kingdom-red dark:hover:text-kingdom-red flex items-center gap-1 group transition-colors">
                            View Report <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- Testimonials (Dark Section) --}}
    <section class="py-24 bg-kingdom-navy dark:bg-navy-dark/95 text-white relative overflow-hidden transition-colors">
        {{-- Abstract bg elements --}}
        <div class="absolute top-0 right-0 -mt-20 -mr-20 h-96 w-96 rounded-full bg-kingdom-red/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 mb-6">
                        <span class="text-xs font-bold tracking-wide uppercase text-kingdom-red">Success Stories</span>
                    </div>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl mb-6">Applicants who found their path.</h2>
                    <p class="text-gray-300 text-lg mb-8">We don't just fill positions; we build careers. See what our applicants have to say about the Kingdom experience.</p>
                    <div class="flex gap-4">
                        <button class="h-12 w-12 flex items-center justify-center rounded-full border border-white/20 hover:bg-white/10 transition-colors">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </button>
                        <button class="h-12 w-12 flex items-center justify-center rounded-full bg-kingdom-red hover:bg-red-700 transition-colors shadow-lg shadow-kingdom-red/30">
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                <div class="relative">
                    {{-- Quote Card --}}
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-8 sm:p-10 relative">
                        <i data-lucide="quote" class="absolute top-8 left-8 w-12 h-12 text-kingdom-red/20 rotate-180 fill-current"></i>
                        <div class="relative z-10 pt-6">
                            <p class="text-xl sm:text-2xl font-medium leading-relaxed mb-8">
                                "Kingdom Recruitments found me a role that perfectly matched my skills. The team was supportive, professional, and transparent throughout the entire process."
                            </p>
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-gray-600 overflow-hidden">
                                    <img alt="Sarah Jenkins" class="h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAjdxhpMziuSFXarJgTDLG7YXHe_rx0EvJeGfl3PkCtj6DJze-uLqPfHjZvT8FDgSau71KH-RGEnqyrLUWDOW2ohho4W879fYoVRgmX0htjY7_0Gk1XwsrSHfnNUaRDF09kFlJFIyKzjTk8FFCoH9j8-G_ECM8LReU0v4EOYpx-Zbkqg-QkRO1ZGRNSgTL6vr2qEdWEXTDgkH8eNSFo5GguyqxvzSryVoH5OstennoDoQJSQdB47ih7qshSrjONK5WCMzmRu65bVR82"/>
                                </div>
                                <div>
                                    <div class="font-bold text-white">Sarah Jenkins</div>
                                    <div class="text-sm text-gray-400">Senior Financial Analyst</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Decorative card stack effect --}}
                    <div class="absolute inset-0 bg-white/5 rounded-2xl rotate-3 -z-10 translate-y-4 scale-[0.98]"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Strip --}}
    <section class="bg-kingdom-red py-16 relative overflow-hidden">
         <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-2xl font-bold text-white sm:text-3xl mb-4">Ready to take the next step?</h2>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-8">
                <a href="{{ route('portal') }}#register" class="w-full sm:w-auto bg-white text-kingdom-red hover:bg-gray-50 font-bold px-8 py-3 rounded-lg shadow-lg transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Register Now
                </a>
                <a href="{{ route('contact') }}" class="w-full sm:w-auto bg-kingdom-red text-white border border-white/30 hover:bg-white/10 font-bold px-8 py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    Contact a Consultant
                </a>
            </div>
        </div>
    </section>

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', () => {
             if(window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
@endsection
