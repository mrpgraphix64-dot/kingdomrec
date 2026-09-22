@extends('layouts.app')

@section('title', 'Our Team - Kingdom Recruitments')

@section('content')
    {{-- Hero Section --}}
    <div class="relative w-[95%] mx-auto rounded-3xl overflow-hidden shadow-2xl bg-kingdom-navy pt-24 pb-12 md:pt-36 md:pb-20 mt-0 mb-8 lg:mb-16">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#E60026 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#B89955_1px,transparent_1px)] bg-[size:24px_24px] animate-pulse"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-kingdom-red text-xs font-bold uppercase tracking-wider mb-4 border border-white/20 backdrop-blur-sm">Kingdom Recruitments</span>
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-4 max-w-none leading-tight">
                Meet <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-red to-rose-400">Our Team</span>
            </h1>
            <p class="text-slate-200 text-lg md:text-xl max-w-2xl font-medium">
                The experts behind our global recruitment success.
            </p>
        </div>
    </div>

    {{-- Founder Section --}}
    <section class="py-16 bg-white dark:bg-background-dark overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-center">
                {{-- Founder Image (Placeholder) --}}
                <div class="relative group max-w-sm mx-auto w-full">
                     {{-- Gold Border Accent --}}
                    <div class="absolute -top-4 -left-4 w-20 h-20 border-t-4 border-l-4 border-kingdom-gold rounded-tl-3xl z-0"></div>
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 border-b-4 border-r-4 border-kingdom-gold rounded-br-3xl z-0"></div>
                    
                    <div class="relative z-10 aspect-[3/4] rounded-xl overflow-hidden bg-slate-100 shadow-2xl border border-slate-200 flex items-center justify-center">
                        <img src="{{ asset('images/team/chowdhury.jpg') }}" 
                             alt="Mr. Chowdhury" 
                             class="w-full h-full object-cover" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                        <div class="hidden w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-kingdom-navy to-slate-800 text-kingdom-gold">
                            <span class="text-6xl font-black font-display tracking-wider">MC</span>
                            <span class="mt-4 font-bold text-xs uppercase tracking-widest opacity-80">Founder & CEO</span>
                        </div>
                    </div>
                    
                    {{-- Name Tag --}}
                    <div class="absolute bottom-6 -right-4 bg-kingdom-gold shadow-lg px-6 py-3 rounded-l-lg z-20">
                        <h3 class="text-kingdom-navy font-bold text-lg">Mr. Chowdhury</h3>
                        <p class="text-kingdom-navy/80 text-[10px] font-bold uppercase tracking-widest">Founder & CEO</p>
                    </div>
                </div>

                {{-- Founder Text --}}
                <div class="relative">
                    <h2 class="text-3xl md:text-4xl font-bold text-kingdom-navy dark:text-white mb-6">A Message from our Founder</h2>
                    <div class="w-20 h-1 bg-kingdom-gold rounded-full mb-8"></div>
                    
                    <div class="space-y-6 text-slate-600 dark:text-slate-400 text-lg leading-relaxed">
                        <p>
                            At Kingdom Recruitments, our journey has always been about more than just filling positions. It's about crafting the future of organizations and empowering the careers of individuals who drive the world forward.
                        </p>
                        <p>
                            We believe that every placement is a partnership. Since our inception, we have remained steadfast in our commitment to integrity, performance, and a personal touch that larger agencies often lose. Our team is the bedrock of this commitment.
                        </p>
                        <blockquote class="border-l-4 border-kingdom-gold pl-6 py-2 italic text-slate-800 dark:text-slate-200 font-medium my-8 bg-slate-50 dark:bg-white/5 rounded-r-lg">
                            "Excellence is not an act, but a habit. We have made it our mission to ensure every client and applicant experiences the 'Kingdom' standard of quality."
                        </blockquote>
                    </div>

                    <div class="mt-8 font-cursive text-3xl text-kingdom-gold">
                        Mr. Chowdhury
                    </div>
                    <p class="text-xs font-bold text-kingdom-navy dark:text-white uppercase tracking-widest mt-1">Chairman & CEO</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Values Section --}}
    <section class="py-20 bg-kingdom-navy text-white relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'0 0 2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid md:grid-cols-3 gap-8">
                {{-- Value 1 --}}
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-colors group text-center">
                    <div class="w-16 h-16 mx-auto mb-6 bg-transparent border-2 border-kingdom-gold rounded-full flex items-center justify-center group-hover:bg-kingdom-gold group-hover:text-kingdom-navy transition-all duration-300 text-kingdom-gold">
                        <i data-lucide="shield-check" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Trust</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Built through transparency and consistent delivery of our promises to both applicants and clients.
                    </p>
                </div>

                {{-- Value 2 --}}
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-colors group text-center">
                    <div class="w-16 h-16 mx-auto mb-6 bg-transparent border-2 border-kingdom-gold rounded-full flex items-center justify-center group-hover:bg-kingdom-gold group-hover:text-kingdom-navy transition-all duration-300 text-kingdom-gold">
                        <i data-lucide="gem" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Integrity</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Adhering to the highest ethical standards in recruitment, ensuring fair and quality placements.
                    </p>
                </div>

                {{-- Value 3 --}}
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-colors group text-center">
                    <div class="w-16 h-16 mx-auto mb-6 bg-transparent border-2 border-kingdom-gold rounded-full flex items-center justify-center group-hover:bg-kingdom-gold group-hover:text-kingdom-navy transition-all duration-300 text-kingdom-gold">
                        <i data-lucide="gauge" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Performance</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Driving results with precision, speed, and an unwavering focus on high-impact talent acquisition.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @php
        $team = \App\Models\Team::all()->map(function($t) {
            $initials = collect(explode(' ', $t->name))->map(fn($n) => mb_substr($n, 0, 1))->join('');
            return [
                'name' => $t->name,
                'role' => $t->role ?? 'Consultant',
                'desc' => 'Expert recruitment consultant at Kingdom Recruitments.',
                'icon' => 'users',
                'image' => $t->image ?? '',
                'linkedin' => null,
                'initials' => $initials
            ];
        });
    @endphp

    @if($team->count() > 0)
    {{-- Team Grid --}}
    <section class="py-24 bg-white dark:bg-background-dark">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-kingdom-gold font-bold uppercase tracking-widest text-xs mb-2 block">Meet The Specialists</span>
                <h2 class="text-3xl md:text-5xl font-extrabold text-kingdom-navy dark:text-white">Expert Leadership Team</h2>
                <p class="text-slate-500 max-w-2xl mx-auto mt-4">
                    Our consultants bring years of specialized knowledge from the hospitality, event, and corporate sectors.
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-10">
                @foreach($team as $member)
                    <div class="flex flex-col items-center text-center group w-full sm:w-64 md:w-72">
                        <div class="relative w-40 h-40 mb-6 rounded-full overflow-hidden border-4 border-slate-100 group-hover:border-kingdom-gold/30 transition-all duration-500 shadow-xl bg-slate-50 flex items-center justify-center">
                             @if(!empty($member['image']))
                             <img src="{{ $member['image'] }}" 
                                  alt="{{ $member['name'] }}" 
                                  class="w-full h-full object-cover" 
                                  onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');" />
                             <div class="hidden w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-kingdom-navy to-slate-800 text-kingdom-gold">
                                <span class="text-3xl font-black font-display">{{ $member['initials'] }}</span>
                             </div>
                             @else
                             <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-kingdom-navy to-slate-800 text-kingdom-gold">
                                <span class="text-3xl font-black font-display">{{ $member['initials'] }}</span>
                             </div>
                             @endif
                        </div>
                        
                        <h3 class="text-xl font-bold text-kingdom-navy dark:text-white mb-1 group-hover:text-kingdom-gold transition-colors">{{ $member['name'] }}</h3>
                        <p class="text-kingdom-gold text-xs font-bold uppercase tracking-widest mb-3">{{ $member['role'] }}</p>
                        <p class="text-slate-500 text-sm leading-relaxed mb-4 px-2">
                             {{ $member['desc'] }}
                        </p>
                        
                        @if(!empty($member['linkedin']))
                            <a href="{{ $member['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-slate-300 text-slate-400 hover:border-kingdom-gold hover:text-kingdom-gold hover:bg-kingdom-gold/5 transition-all">
                                <i data-lucide="linkedin" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Banner --}}
    <section class="py-20 bg-kingdom-navy text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-5xl font-extrabold mb-4">Want to Join the Kingdom?</h2>
            <p class="text-kingdom-gold text-lg md:text-xl font-bold mb-8">We are always looking for exceptional recruitment consultants.</p>
            
            <a href="{{ route('contact') }}" class="inline-block px-10 py-4 bg-kingdom-gold hover:bg-yellow-500 text-kingdom-navy font-extrabold rounded-lg uppercase tracking-widest transition-all shadow-lg hover:shadow-kingdom-gold/20 transform hover:-translate-y-1">
                Send Your Resume
            </a>
        </div>
    </section>

    <script>
        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
             lucide.createIcons();
        });
    </script>
@endsection
