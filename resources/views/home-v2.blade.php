@extends('layouts.app')

@section('title', 'Kingdom Recruitments')

@section('content')
    <div class="relative">
        {{-- ═══════════════════════════════════════════════════════════════
            HERO SECTION (Aster Style - Dark overlay, bold type, CTA)
        ═══════════════════════════════════════════════════════════════ --}}
        <header class="relative w-full min-h-[80vh] md:min-h-[90vh] overflow-hidden flex flex-col justify-center items-start" style="padding-top: 5rem;">
            {{-- Background Image --}}
            <div class="absolute inset-0 z-0">
                <img alt="Recruitment" class="w-full h-full object-cover object-[75%_50%] scale-105 animate-[heroZoom_20s_ease-in-out_infinite_alternate]" src="{{ asset('images/hero_v3.jpg') }}" />
                <div class="absolute inset-0 bg-gradient-to-r from-kingdom-navy/95 via-kingdom-navy/70 to-kingdom-navy/30"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-kingdom-navy/60 via-transparent to-transparent"></div>
            </div>

            {{-- Particle Canvas --}}
            <canvas id="hero-particles" class="absolute inset-0 z-[1] pointer-events-none"></canvas>

            {{-- Decorative Gold Triangle --}}
            <div class="absolute top-0 left-0 z-[2] pointer-events-none">
                <div class="w-0 h-0 border-t-[140px] border-r-[140px] border-t-kingdom-gold border-r-transparent opacity-90"></div>
            </div>

            {{-- Vertical Text --}}
            <div class="absolute left-4 top-1/2 -translate-y-1/2 z-[2] hidden lg:block">
                <span class="text-white/20 text-[10px] font-bold tracking-[0.4em] uppercase" style="writing-mode: vertical-rl; transform: rotate(180deg);">BRIDGING TALENT & OPPORTUNITY</span>
            </div>

            {{-- Floating Service Icons --}}
            <div class="absolute inset-0 z-[2] pointer-events-none hidden lg:block">
                <div class="absolute top-[18%] right-[10%] animate-[floatSlow_6s_ease-in-out_infinite]">
                    <div class="w-16 h-16 rounded-2xl bg-kingdom-gold/15 backdrop-blur-md border border-kingdom-gold/20 flex items-center justify-center shadow-lg shadow-kingdom-gold/10">
                        <span class="material-symbols-outlined text-kingdom-gold text-2xl">security</span>
                    </div>
                </div>
                <div class="absolute top-[35%] right-[22%] animate-[floatMed_5s_ease-in-out_infinite_0.5s]">
                    <div class="w-14 h-14 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 flex items-center justify-center shadow-lg">
                        <span class="material-symbols-outlined text-white text-xl">restaurant</span>
                    </div>
                </div>
                <div class="absolute bottom-[25%] right-[8%] animate-[floatSlow_7s_ease-in-out_infinite_1s]">
                    <div class="w-14 h-14 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 flex items-center justify-center shadow-lg">
                        <span class="material-symbols-outlined text-white text-xl">groups</span>
                    </div>
                </div>
                <div class="absolute bottom-[35%] right-[28%] animate-[floatMed_6s_ease-in-out_infinite_2s]">
                    <div class="w-12 h-12 rounded-lg bg-kingdom-gold/15 backdrop-blur-md border border-kingdom-gold/20 flex items-center justify-center shadow-lg shadow-kingdom-gold/10">
                        <span class="material-symbols-outlined text-kingdom-gold text-lg">work</span>
                    </div>
                </div>
                <div class="absolute top-[55%] right-[15%] animate-[floatSlow_5s_ease-in-out_infinite_1.5s]">
                    <div class="w-10 h-10 rounded-lg bg-white/8 backdrop-blur-md border border-white/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white/70 text-lg">event</span>
                    </div>
                </div>
            </div>

            {{-- Stats Floaters --}}
            <div class="absolute bottom-8 right-8 z-[3] hidden lg:flex gap-4">
                <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-2xl px-5 py-3 flex items-center gap-3 animate-[fadeSlideUp_0.8s_ease-out_0.5s_both]">
                    <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-400 text-lg">trending_up</span>
                    </div>
                    <div>
                        <div class="text-white font-bold text-lg">98.5%</div>
                        <div class="text-white/50 text-[10px] uppercase tracking-wider">Success Rate</div>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-2xl px-5 py-3 flex items-center gap-3 animate-[fadeSlideUp_0.8s_ease-out_0.8s_both]">
                    <div class="flex -space-x-2">
                        <div class="w-7 h-7 rounded-full bg-kingdom-gold/80 border-2 border-kingdom-navy flex items-center justify-center text-[10px] font-bold text-kingdom-navy">4k+</div>
                    </div>
                    <div>
                        <div class="text-white font-bold text-lg">Hired</div>
                        <div class="text-white/50 text-[10px] uppercase tracking-wider">This Year</div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="relative z-[3] container mx-auto px-6 md:px-12">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-kingdom-gold/30 bg-kingdom-gold/10 backdrop-blur-md mb-6 animate-[fadeSlideUp_0.6s_ease-out_both]">
                        <span class="w-2 h-2 rounded-full bg-kingdom-gold animate-pulse"></span>
                        <span class="text-kingdom-gold text-xs font-bold tracking-[0.15em] uppercase">Decades of Excellence</span>
                    </div>
                    <h1 class="text-5xl md:text-6xl lg:text-8xl font-display font-extrabold text-white leading-[0.95] mb-4 animate-[fadeSlideUp_0.7s_ease-out_0.15s_both]">
                        DECADES<br/>OF <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-gold to-yellow-300">EXPERTISE</span>
                    </h1>
                    <p class="text-white/60 text-lg md:text-xl mb-8 max-w-lg leading-relaxed animate-[fadeSlideUp_0.7s_ease-out_0.3s_both]">
                        Connecting world-class professionals with industry-leading organizations across the UK.
                    </p>
                    <div class="flex flex-wrap gap-4 animate-[fadeSlideUp_0.7s_ease-out_0.45s_both]">
                        <a href="{{ route('applicants.upload') }}" class="group inline-flex items-center gap-3 bg-kingdom-gold hover:bg-yellow-400 text-kingdom-navy font-bold px-8 py-4 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg shadow-kingdom-gold/30 hover:shadow-xl hover:shadow-kingdom-gold/40">
                            <span class="material-icons group-hover:rotate-12 transition-transform">cloud_upload</span>
                            <span class="tracking-wide">UPLOAD YOUR CV</span>
                            <span class="material-icons group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                        <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-bold px-8 py-4 rounded-xl border border-white/20 hover:border-white/40 transition-all duration-300">
                            <span class="material-icons">search</span>
                            <span class="tracking-wide">BROWSE JOBS</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Bottom Gradient Fade --}}
            <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-gray-50 to-transparent z-[2]"></div>
        </header>

        {{-- ═══════════════════════════════════════════════════════════════
            SERVICE CARDS (Aster Style - 4 minimal white cards)
        ═══════════════════════════════════════════════════════════════ --}}
        <section class="py-20 bg-gray-50 relative overflow-hidden">
            {{-- Decorative dots --}}
            <div class="absolute top-12 left-12 w-32 h-32 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle, #F8B803 1.5px, transparent 1.5px); background-size: 16px 16px;"></div>
            <div class="absolute bottom-12 right-12 w-32 h-32 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle, #0F1D33 1.5px, transparent 1.5px); background-size: 16px 16px;"></div>
            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center mb-14">
                    <span class="text-kingdom-gold text-xs font-bold tracking-[0.2em] uppercase">What We Do</span>
                    <h2 class="text-3xl md:text-4xl font-display font-bold text-kingdom-navy mt-2">Our Services</h2>
                    <div class="w-16 h-1 bg-kingdom-gold rounded-full mx-auto mt-4"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $services = [
                            ['icon' => 'campaign', 'title' => 'Advertise Job', 'desc' => 'Kingdom Recruitments is a UK-based job site that specializes in recruitment for the hospitality, leisure, and tourism industries.', 'modal' => 'advertise'],
                            ['icon' => 'person_search', 'title' => 'Recruiter Profiles', 'desc' => 'As Chairman & CEO of Kingdom Recruitments, Mr. Chowdhury has transformed the organization into a high-performing entity.', 'modal' => 'recruiter'],
                            ['icon' => 'restaurant', 'title' => 'Hospitality & Staffing', 'desc' => 'Kingdom Recruitments hospitality and staffing service is the perfect blend of experience and enthusiasm in the industry.', 'modal' => 'advertise'],
                            ['icon' => 'manage_search', 'title' => 'Find Dream Job', 'desc' => 'Finding your dream job can be a challenging but rewarding process. By utilizing Kingdom Recruitments, you can increase your chances.', 'modal' => 'dream_job'],
                        ];
                    @endphp

                    <style>
                        .service-card:hover {
                            background-color: #E60026 !important;
                            border-color: #E60026 !important;
                        }
                    </style>

                    @foreach($services as $svc)
                    <div class="service-card bg-white border border-gray-100 rounded-none p-10 flex flex-col group transition-all duration-500 relative overflow-hidden shadow-[0_0_40px_rgba(0,0,0,0.03)] hover:shadow-2xl">
                        {{-- Decorative background shape top-left --}}
                        <div class="absolute -top-16 -left-16 w-40 h-40 bg-gray-50 group-hover:bg-white/10 rounded-[40%] transition-colors duration-500 pointer-events-none -rotate-12 group-hover:rotate-12"></div>
                        
                        <h3 class="text-xl font-bold text-kingdom-navy group-hover:text-white mb-4 font-display transition-colors duration-500 relative z-10">{{ $svc['title'] }}</h3>
                        <p class="text-gray-500 group-hover:text-white/90 text-sm leading-relaxed mb-8 flex-grow transition-colors duration-500 relative z-10 line-clamp-4">{{ $svc['desc'] }}</p>
                        
                        <button onclick="openModal('{{ $svc['modal'] }}')" class="inline-flex items-center gap-2 text-[#E60026] group-hover:text-white font-bold text-sm tracking-wide transition-colors duration-500 relative z-10">
                            KNOW MORE <span class="material-icons text-sm transition-transform group-hover:translate-x-1">arrow_forward</span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════
            TEAM SECTION (Aster Algarve Style - Dark BG, profile pills)
        ═══════════════════════════════════════════════════════════════ --}}
        <section class="py-20 bg-kingdom-navy relative overflow-hidden">
            {{-- Decorative elements --}}
            <div class="absolute top-16 right-16 w-40 h-40 border border-kingdom-gold/10 rounded-full pointer-events-none"></div>
            <div class="absolute bottom-16 left-16 w-60 h-60 border border-white/5 rounded-full pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-kingdom-gold/5 rounded-full blur-[150px] pointer-events-none"></div>
            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center mb-16">
                    <span class="text-kingdom-gold text-xs font-bold tracking-[0.2em] uppercase">Meet The Team</span>
                    <h2 class="text-3xl md:text-4xl font-display font-bold text-white mt-2">Kingdom Recruitments</h2>
                    <div class="w-16 h-1 bg-kingdom-gold rounded-full mx-auto mt-4"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                    @php
                        $team = [
                            ['role' => 'The Recruitment Expert', 'name' => 'Mr. Chowdhury', 'desc' => 'With decades of leadership in the recruitment industry, Mr. Chowdhury drives Kingdom Recruitments forward.', 'img' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=200&q=80'],
                            ['role' => 'Hospitality Specialist', 'name' => 'Sarah Jenkins', 'desc' => 'Sarah specializes in placing top talent across the UK hospitality and events industry.', 'img' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80'],
                            ['role' => 'Security Operations', 'name' => 'James Wilson', 'desc' => 'James leads our SIA security staffing division with over 10 years of industry experience.', 'img' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80'],
                            ['role' => 'Client Relations', 'name' => 'Emily Davis', 'desc' => 'Emily ensures seamless communication between our partners and the Kingdom recruitment team.', 'img' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80'],
                        ];
                    @endphp
                    @foreach($team as $member)
                    <div class="flex items-start gap-5 bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 hover:border-kingdom-gold/30 hover:scale-[1.02] transition-all duration-500 group cursor-pointer">
                        <div class="relative flex-shrink-0">
                            <img src="{{ $member['img'] }}" alt="{{ $member['name'] }}" class="w-20 h-20 rounded-full object-cover border-2 border-kingdom-gold/30 group-hover:border-kingdom-gold transition-colors duration-500">
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-green-500 border-2 border-kingdom-navy"></div>
                        </div>
                        <div>
                            <span class="text-kingdom-gold text-[10px] font-bold uppercase tracking-[0.15em]">{{ $member['role'] }}</span>
                            <h3 class="text-white font-bold text-lg mb-1 group-hover:text-kingdom-gold transition-colors">{{ $member['name'] }}</h3>
                            <p class="text-gray-400 text-sm leading-relaxed">{{ $member['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════
            GALLERY / PAST EVENTS (Aster Style)
        ═══════════════════════════════════════════════════════════════ --}}
        <section class="py-20 bg-white relative">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-12">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-display font-bold text-kingdom-navy mt-2">Our Past Events</h2>
                    </div>
                    <a href="#" class="group inline-flex items-center justify-center gap-2 bg-[#FF3344] hover:bg-[#E60026] text-white font-bold px-8 py-3.5 transition-all mt-4 md:mt-0 shadow-lg hover:shadow-xl text-sm" style="clip-path: polygon(0 0, 92% 0, 100% 50%, 92% 100%, 0 100%); padding-right: 2.5rem;">
                        SEE ALL PHOTO <span class="material-icons text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    @php
                        $gallery = [
                            ['img' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=600&q=80', 'title' => 'Corporate Gala 2024', 'cat' => 'Events'],
                            ['img' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80', 'title' => 'Hospitality Awards', 'cat' => 'Awards'],
                            ['img' => 'https://images.unsplash.com/photo-1505236858219-8359eb29e329?auto=format&fit=crop&w=600&q=80', 'title' => 'Annual Conference', 'cat' => 'Conference'],
                            ['img' => 'https://images.unsplash.com/photo-1560439513-74b037a25d84?auto=format&fit=crop&w=600&q=80', 'title' => 'Team Building Day', 'cat' => 'Corporate'],
                            ['img' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&w=600&q=80', 'title' => 'Recruitment Fair', 'cat' => 'Recruitment'],
                        ];
                    @endphp
                    @foreach($gallery as $i => $item)
                    <div class="relative group overflow-hidden rounded-2xl {{ $i === 0 ? 'md:col-span-2 md:row-span-2' : '' }} aspect-[4/3] cursor-pointer shadow-md hover:shadow-xl transition-shadow duration-300 border-4 border-transparent hover:border-[#FF3344]">
                        <img src="{{ $item['img'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-kingdom-navy/90 via-kingdom-navy/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-400"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                            <span class="text-kingdom-gold text-[10px] font-bold uppercase tracking-wider">{{ $item['cat'] }}</span>
                            <h4 class="text-white font-bold text-sm md:text-base">{{ $item['title'] }}</h4>
                        </div>
                        <div class="absolute top-4 right-4 w-9 h-9 rounded-full bg-[#FF3344] text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 scale-75 group-hover:scale-100">
                            <span class="material-icons text-lg">arrow_outward</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════
            AFFILIATES / TOP COMPANIES MARQUEE
        ═══════════════════════════════════════════════════════════════ --}}
        <section class="py-16 bg-gray-50 border-y border-gray-200 relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-20 md:w-40 bg-gradient-to-r from-gray-50 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-20 md:w-40 bg-gradient-to-l from-gray-50 to-transparent z-10 pointer-events-none"></div>
            <div class="container mx-auto px-6 mb-10 text-center">
                <span class="text-kingdom-gold text-xs font-bold tracking-[0.2em] uppercase">Kingdom Recruitments</span>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-kingdom-navy mt-2">Our Affiliates</h2>
            </div>
            @php
                $companies = [
                    ["name" => "TOP LOOK", "logo" => asset('images/top-company/1.png')],
                    ["name" => "TROUT IT", "logo" => asset('images/top-company/2.png')],
                    ["name" => "SUSSEX LTD", "logo" => asset('images/top-company/3.png')],
                    ["name" => "LAWN HOPPER", "logo" => asset('images/top-company/4.png')],
                    ["name" => "TOP LOOK", "logo" => asset('images/top-company/1.png')],
                    ["name" => "TROUT IT", "logo" => asset('images/top-company/2.png')],
                    ["name" => "SUSSEX LTD", "logo" => asset('images/top-company/3.png')],
                    ["name" => "LAWN HOPPER", "logo" => asset('images/top-company/4.png')],
                ];
            @endphp
            <div class="flex overflow-hidden group select-none py-4">
                <div class="flex animate-marquee whitespace-nowrap min-w-full shrink-0 items-center gap-16 md:gap-32 pr-16 md:pr-32 group-hover:[animation-play-state:paused]">
                    @foreach($companies as $company)
                    <div class="flex items-center gap-4 hover:scale-105 transition-transform cursor-pointer">
                        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-10 md:h-12 w-auto object-contain" />
                        <span class="text-lg md:text-2xl font-display font-bold text-kingdom-navy">{{ $company['name'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex animate-marquee whitespace-nowrap min-w-full shrink-0 items-center gap-16 md:gap-32 pr-16 md:pr-32 group-hover:[animation-play-state:paused]" aria-hidden="true">
                    @foreach($companies as $company)
                    <div class="flex items-center gap-4 hover:scale-105 transition-transform cursor-pointer">
                        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-10 md:h-12 w-auto object-contain" />
                        <span class="text-lg md:text-2xl font-display font-bold text-kingdom-navy">{{ $company['name'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════
            DASHED-CIRCLE STATISTICS (Aster Style)
        ═══════════════════════════════════════════════════════════════ --}}
        <section class="py-20 bg-white relative overflow-hidden">
            <div class="container mx-auto px-6">
                <div class="text-center mb-4">
                    <span class="text-kingdom-gold text-xs font-bold tracking-[0.2em] uppercase">KINGDOM RECRUITMENTS</span>
                </div>
                <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-4">
                    {{-- Stat 1 --}}
                    <div class="flex flex-col items-center group">
                        <div class="relative w-36 h-36 md:w-40 md:h-40 flex items-center justify-center">
                            <div class="absolute inset-0 rounded-full border-[3px] border-dashed border-kingdom-gold animate-[spin_20s_linear_infinite] group-hover:[animation-play-state:paused]"></div>
                            <div class="text-center">
                                <div class="text-4xl md:text-5xl font-extrabold text-kingdom-navy counter" data-target="1225">0</div>
                                <div class="text-kingdom-gold text-xs font-bold uppercase tracking-wider mt-1">Jobs Posted</div>
                            </div>
                        </div>
                    </div>
                    {{-- Arrow 1 --}}

                    {{-- Stat 2 --}}
                    <div class="flex flex-col items-center group">
                        <div class="relative w-36 h-36 md:w-40 md:h-40 flex items-center justify-center">
                            <div class="absolute inset-0 rounded-full border-[3px] border-dashed border-kingdom-gold animate-[spin_25s_linear_infinite_reverse] group-hover:[animation-play-state:paused]"></div>
                            <div class="text-center">
                                <div class="text-4xl md:text-5xl font-extrabold text-kingdom-navy counter" data-target="145">0</div>
                                <div class="text-kingdom-gold text-xs font-bold uppercase tracking-wider mt-1">Jobs Filled</div>
                            </div>
                        </div>
                    </div>
                    {{-- Arrow 2 --}}

                    {{-- Stat 3 --}}
                    <div class="flex flex-col items-center group">
                        <div class="relative w-36 h-36 md:w-40 md:h-40 flex items-center justify-center">
                            <div class="absolute inset-0 rounded-full border-[3px] border-dashed border-kingdom-gold animate-[spin_22s_linear_infinite] group-hover:[animation-play-state:paused]"></div>
                            <div class="text-center">
                                <div class="text-4xl md:text-5xl font-extrabold text-kingdom-navy counter" data-target="170">0</div>
                                <div class="text-kingdom-gold text-xs font-bold uppercase tracking-wider mt-1">+Companies</div>
                            </div>
                        </div>
                    </div>
                    {{-- Arrow 3 --}}

                    {{-- Stat 4 --}}
                    <div class="flex flex-col items-center group">
                        <div class="relative w-36 h-36 md:w-40 md:h-40 flex items-center justify-center">
                            <div class="absolute inset-0 rounded-full border-[3px] border-dashed border-kingdom-gold animate-[spin_18s_linear_infinite_reverse] group-hover:[animation-play-state:paused]"></div>
                            <div class="text-center">
                                <div class="text-4xl md:text-5xl font-extrabold text-kingdom-navy counter" data-target="12000">0</div>
                                <div class="text-kingdom-gold text-xs font-bold uppercase tracking-wider mt-1">+Members</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════
            SPLIT CTA - APPLICANTS & EMPLOYERS
        ═══════════════════════════════════════════════════════════════ --}}
        <section class="grid md:grid-cols-2">
            <!-- Applicants -->
            <div class="relative group overflow-hidden bg-kingdom-navy md:p-24 p-12 flex flex-col justify-center min-h-[450px]">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=1200&auto=format&fit=crop')] bg-cover bg-center opacity-0 group-hover:opacity-20 transform scale-100 group-hover:scale-105 transition-all duration-700"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-kingdom-navy/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                
                <div class="relative z-10 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                    <span class="material-symbols-outlined text-6xl text-kingdom-gold mb-6 opacity-80 group-hover:opacity-100 transition-opacity">person</span>
                    <h3 class="text-4xl md:text-5xl font-display font-bold text-white mb-6">Applicants</h3>
                    <p class="text-gray-300 md:text-lg mb-10 max-w-sm leading-relaxed">Unlock your potential with roles tailored to your expertise across the UK.</p>
                    <a href="{{ route('applicants.upload') }}" class="inline-flex items-center gap-3 bg-kingdom-gold text-kingdom-navy font-bold px-8 py-4 rounded-full hover:bg-white hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                        Upload CV <span class="material-icons text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>

            <!-- Employers -->
            <div class="relative group overflow-hidden bg-[#F8FAFC] border-t md:border-t-0 md:border-l border-gray-200 md:p-24 p-12 flex flex-col justify-center min-h-[450px]">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1200&auto=format&fit=crop')] bg-cover bg-center opacity-0 group-hover:opacity-10 transform scale-100 group-hover:scale-105 transition-all duration-700 mix-blend-multiply"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#F8FAFC]/90 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                
                <div class="relative z-10 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                    <span class="material-symbols-outlined text-6xl text-[#E60026] mb-6 opacity-80 group-hover:opacity-100 transition-opacity">business</span>
                    <h3 class="text-4xl md:text-5xl font-display font-bold text-kingdom-navy mb-6">Employers</h3>
                    <p class="text-gray-500 md:text-lg mb-10 max-w-sm leading-relaxed">Find the perfect match for your company's culture and hiring needs.</p>
                    <a href="{{ route('portal') }}" class="inline-flex items-center gap-3 bg-kingdom-navy text-white font-bold px-8 py-4 rounded-full hover:bg-[#E60026] hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                        Post a Job <span class="material-icons text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════
            TESTIMONIALS (Aster Style)
        ═══════════════════════════════════════════════════════════════ --}}
        @php
            $testimonials = [
                ["quote" => "Kingdom recruitment's has been helping with our recruitment for years, placing some great applicants with us. They know the sort of people we look for & ensure they only send the right ones over.", "author" => "Mr Sayed", "role" => "Event Manager", "company" => "Intercontinental Park Lane", "image" => "https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=200&auto=format&fit=crop"],
                ["quote" => "Whoever I speak to at Kingdom recruitments they are always incredibly supportive & attentive. They always go over & above to provide great applicants who match the brief & are quick with responses.", "author" => "Event & Banqueting Manager", "role" => "Manager", "company" => "The Tower Hotel London", "image" => "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=200&auto=format&fit=crop"],
            ];
        @endphp
        <section id="testimonials-section" class="py-20 px-6 bg-[#020c1b] relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-kingdom-gold/5 rounded-full blur-[120px] pointer-events-none"></div>
            <div class="max-w-6xl mx-auto relative z-10">
                <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-12 h-px bg-kingdom-gold"></span>
                            <span class="text-kingdom-gold text-xs font-bold tracking-[0.2em] uppercase">Endorsements</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-white">Voice of the <span class="text-gray-400">Industry</span></h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="prevTestimonial()" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-gray-400 hover:bg-white/5 hover:text-white transition-all cursor-pointer"><span class="material-icons text-sm">arrow_back</span></button>
                        <button onclick="nextTestimonial()" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-gray-400 hover:bg-white/5 hover:text-white transition-all cursor-pointer"><span class="material-icons text-sm">arrow_forward</span></button>
                    </div>
                </div>
                <div class="relative bg-white/5 backdrop-blur-sm border border-kingdom-gold/20 rounded-2xl p-8 md:p-12 overflow-hidden">
                    <div class="absolute top-6 left-8 opacity-10 pointer-events-none"><span class="font-serif text-[8rem] leading-none text-kingdom-gold">"</span></div>
                    <div class="contents">
                        @foreach($testimonials as $i => $t)
                        <div class="test-slide col-start-1 row-start-1 w-full transition-all duration-500 {{ $i === 0 ? 'opacity-100' : 'opacity-0 absolute' }}" data-index="{{ $i }}">
                            <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
                                <div class="flex-1">
                                    <p class="text-xl md:text-2xl text-white font-light leading-relaxed mb-8">{{ $t['quote'] }}</p>
                                    <h4 class="text-lg font-bold text-white">{{ $t['author'] }}</h4>
                                    <div class="flex items-center gap-2 text-sm text-gray-400">
                                        <span>{{ $t['role'] }}</span>
                                        <span class="w-1 h-1 bg-kingdom-gold rounded-full"></span>
                                        <span class="text-kingdom-gold">{{ $t['company'] }}</span>
                                    </div>
                                </div>
                                <div class="w-32 h-32 md:w-44 md:h-44 rounded-full overflow-hidden border-2 border-kingdom-gold/30 flex-shrink-0">
                                    <img src="{{ $t['image'] }}" alt="{{ $t['author'] }}" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div id="test-progress" class="absolute bottom-0 left-0 h-1 bg-kingdom-gold transition-all duration-500" style="width:50%"></div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════════
            NEWS & ARTICLES
        ═══════════════════════════════════════════════════════════════ --}}
        @php
            $articles = [
                ['category' => "Career Advice", 'title' => "How to Introduce Yourself in a Job Interview?", 'excerpt' => "First impressions matter. Learn the proven framework for answering 'Tell me about yourself' with confidence.", 'readTime' => "5 min read", 'date' => "Nov 24, 2023", 'image' => "https://images.unsplash.com/photo-1565688534245-05d6b5be184a?q=80&w=800&auto=format&fit=crop"],
                ['category' => "Management", 'title' => "Building Highly Motivated Product Teams", 'excerpt' => "Why culture fit and intrinsic motivation outweigh raw technical skills when scaling.", 'readTime' => "4 min read", 'date' => "Nov 22, 2023", 'image' => "https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop"],
                ['category' => "Industry Insights", 'title' => "Why Hospitality Careers Are Booming in the UK", 'excerpt' => "Analyzing salary trends, growth opportunities, and career flexibility in the hospitality sector.", 'readTime' => "6 min read", 'date' => "Nov 15, 2023", 'image' => "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=800&auto=format&fit=crop"],
            ];
        @endphp
        <section class="py-20 bg-gray-50 border-t border-gray-200">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
                    <div>
                        <span class="text-kingdom-gold font-bold tracking-wider text-xs uppercase mb-2 block">Latest Insights</span>
                        <h2 class="text-kingdom-navy text-3xl md:text-4xl font-display font-bold">News, Tips & Articles</h2>
                    </div>
                    <button class="hidden md:flex items-center gap-2 text-kingdom-navy font-bold hover:text-kingdom-gold transition-colors">
                        View all articles <span class="material-icons">arrow_forward</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($articles as $article)
                    <article class="group cursor-pointer flex flex-col bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-300">
                        <div class="relative overflow-hidden aspect-[4/3]">
                            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            <div class="absolute top-4 left-4">
                                <span class="bg-kingdom-navy px-3 py-1 rounded-full text-xs font-bold text-white uppercase tracking-wide">{{ $article['category'] }}</span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                                <span>{{ $article['readTime'] }}</span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span>{{ $article['date'] }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-kingdom-navy mb-3 group-hover:text-kingdom-gold transition-colors line-clamp-2">{{ $article['title'] }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-2">{{ $article['excerpt'] }}</p>
                            <div class="mt-auto">
                                <span class="inline-flex items-center text-kingdom-gold font-bold text-sm group-hover:translate-x-1 transition-transform">Read More <span class="material-icons text-[16px] ml-1">arrow_forward</span></span>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <style>
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-100%); } }
        .animate-marquee { animation: marquee 30s linear infinite; }
        @keyframes heroZoom { 0% { transform: scale(1.05); } 100% { transform: scale(1.12); } }
        @keyframes floatSlow { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-18px); } }
        @keyframes floatMed { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-14px) rotate(3deg); } }
        @keyframes fadeSlideUp { 0% { opacity: 0; transform: translateY(30px); } 100% { opacity: 1; transform: translateY(0); } }
        /* Scroll reveal */
        .reveal-section { opacity: 0; transform: translateY(40px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal-section.revealed { opacity: 1; transform: translateY(0); }
    </style>

    @push('scripts')
        @include('partials.modal_script')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // ── Hero Particle Canvas ──
                const canvas = document.getElementById('hero-particles');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    let particles = [];
                    function resizeCanvas() { canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight; }
                    resizeCanvas(); window.addEventListener('resize', resizeCanvas);
                    class Particle {
                        constructor() { this.reset(); }
                        reset() {
                            this.x = Math.random() * canvas.width;
                            this.y = Math.random() * canvas.height;
                            this.size = Math.random() * 2 + 0.5;
                            this.speedX = (Math.random() - 0.5) * 0.4;
                            this.speedY = (Math.random() - 0.5) * 0.4;
                            this.opacity = Math.random() * 0.4 + 0.1;
                            this.gold = Math.random() > 0.7;
                        }
                        update() {
                            this.x += this.speedX; this.y += this.speedY;
                            if (this.x < 0 || this.x > canvas.width || this.y < 0 || this.y > canvas.height) this.reset();
                        }
                        draw() {
                            ctx.beginPath();
                            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                            ctx.fillStyle = this.gold ? `rgba(248,184,3,${this.opacity})` : `rgba(255,255,255,${this.opacity})`;
                            ctx.fill();
                        }
                    }
                    for (let i = 0; i < 60; i++) particles.push(new Particle());
                    function animate() {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        particles.forEach(p => { p.update(); p.draw(); });
                        requestAnimationFrame(animate);
                    }
                    animate();
                }

                // ── Counter Animation ──
                const counters = document.querySelectorAll('.counter');
                const speed = 200;
                const animateCounters = () => {
                    counters.forEach(counter => {
                        const target = +counter.getAttribute('data-target');
                        const update = () => {
                            const current = +counter.innerText.replace(/[^\d]/g, '');
                            const inc = target / speed;
                            if (current < target) {
                                counter.innerText = Math.ceil(current + inc).toLocaleString();
                                setTimeout(update, 20);
                            } else {
                                counter.innerText = target.toLocaleString();
                            }
                        };
                        update();
                    });
                };
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach(e => { if (e.isIntersecting) { animateCounters(); obs.disconnect(); } });
                });
                if (counters.length) obs.observe(counters[0]);

                // ── Testimonial Slider ──
                let currentTest = 0;
                const slides = document.querySelectorAll('.test-slide');
                const totalSlides = slides.length;
                const progress = document.getElementById('test-progress');
                let testInterval;
                function showTest(idx) {
                    slides.forEach(s => { s.classList.add('opacity-0', 'absolute'); s.classList.remove('opacity-100'); });
                    slides[idx]?.classList.remove('opacity-0', 'absolute');
                    slides[idx]?.classList.add('opacity-100');
                    if (progress) progress.style.width = `${((idx + 1) / totalSlides) * 100}%`;
                }
                window.nextTestimonial = () => { currentTest = (currentTest + 1) % totalSlides; showTest(currentTest); resetTest(); };
                window.prevTestimonial = () => { currentTest = (currentTest - 1 + totalSlides) % totalSlides; showTest(currentTest); resetTest(); };
                function startTest() { testInterval = setInterval(window.nextTestimonial, 6000); }
                function resetTest() { clearInterval(testInterval); startTest(); }
                if (totalSlides > 0) { showTest(0); startTest(); }

                // ── Scroll Reveal ──
                const revealSections = document.querySelectorAll('section');
                const revealObs = new IntersectionObserver((entries) => {
                    entries.forEach(e => {
                        if (e.isIntersecting) { e.target.classList.add('revealed'); revealObs.unobserve(e.target); }
                    });
                }, { threshold: 0.1 });
                revealSections.forEach(s => { s.classList.add('reveal-section'); revealObs.observe(s); });
            });
        </script>
    @endpush
@endsection