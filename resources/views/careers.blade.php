@extends('layouts.app')

@section('title', 'Careers - Kingdom Recruitments')

@section('content')
    {{-- Hero Section --}}
    <div class="relative w-[95%] mx-auto rounded-3xl overflow-hidden shadow-2xl bg-kingdom-navy pt-24 pb-12 md:pt-36 md:pb-20 mt-0 mb-8 lg:mb-16">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#E60026 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#B89955_1px,transparent_1px)] bg-[size:24px_24px] animate-pulse"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-kingdom-red text-xs font-bold uppercase tracking-wider mb-4 border border-white/20 backdrop-blur-sm">Careers</span>
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-4 max-w-none leading-tight">
                Build Your Future with <span class="text-transparent bg-clip-text bg-gradient-to-r from-kingdom-red to-rose-400">Kingdom Recruitments</span>
            </h1>
            <p class="text-slate-200 text-lg md:text-xl max-w-2xl font-medium">
                Join a dynamic team dedicated to connecting exceptional talent with global organizations.
            </p>
        </div>
    </div>

    {{-- Why Work With Us Section --}}
    <section class="py-16 bg-kingdom-bg dark:bg-background-dark overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-kingdom-gold font-bold uppercase tracking-widest text-xs mb-2 block">Our Culture</span>
                <h2 class="text-3xl md:text-4xl font-bold text-kingdom-navy dark:text-white">Why Join Kingdom Recruitments?</h2>
                <div class="w-20 h-1 bg-kingdom-gold rounded-full mx-auto mt-4"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Value 1 --}}
                <div class="bg-white dark:bg-surface-dark border border-slate-200/60 dark:border-gray-700 rounded-2xl p-8 hover:shadow-md hover:border-slate-300 dark:hover:bg-white/5 transition-all group text-center shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-6 bg-transparent border-2 border-kingdom-gold rounded-full flex items-center justify-center group-hover:bg-kingdom-gold group-hover:text-kingdom-navy transition-all duration-300 text-kingdom-gold">
                        <i data-lucide="trending-up" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-kingdom-navy dark:text-white">Professional Growth</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                        We invest in our people. From day one, you will have access to training, mentorship, and clear career progression pathways.
                    </p>
                </div>

                {{-- Value 2 --}}
                <div class="bg-white dark:bg-surface-dark border border-slate-200/60 dark:border-gray-700 rounded-2xl p-8 hover:shadow-md hover:border-slate-300 dark:hover:bg-white/5 transition-all group text-center shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-6 bg-transparent border-2 border-kingdom-gold rounded-full flex items-center justify-center group-hover:bg-kingdom-gold group-hover:text-kingdom-navy transition-all duration-300 text-kingdom-gold">
                        <i data-lucide="sparkles" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-kingdom-navy dark:text-white">Inclusive Environment</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                        We celebrate diversity and foster a supportive, collaborative workspace where every team member's voice is valued.
                    </p>
                </div>

                {{-- Value 3 --}}
                <div class="bg-white dark:bg-surface-dark border border-slate-200/60 dark:border-gray-700 rounded-2xl p-8 hover:shadow-md hover:border-slate-300 dark:hover:bg-white/5 transition-all group text-center shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-6 bg-transparent border-2 border-kingdom-gold rounded-full flex items-center justify-center group-hover:bg-kingdom-gold group-hover:text-kingdom-navy transition-all duration-300 text-kingdom-gold">
                        <i data-lucide="award" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-kingdom-navy dark:text-white">Competitive Rewards</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                        Enjoy a competitive base salary, an industry-leading commission structure, health benefits, and regular team milestones.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Open Positions & Call to Action --}}
    <section class="py-20 bg-kingdom-navy text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'0 0 2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="container mx-auto px-6 relative z-10 text-center max-w-4xl">
            <h2 class="text-3xl md:text-5xl font-extrabold mb-6">Ready to Make an Impact?</h2>
            <p class="text-slate-300 text-lg md:text-xl font-medium mb-10 max-w-2xl mx-auto">
                We are always seeking passionate recruitment consultants, talent acquisition specialists, and operations experts to join our growing global offices.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('applicants.upload') }}" class="h-14 px-10 rounded-lg bg-kingdom-gold hover:bg-yellow-500 text-kingdom-navy text-lg font-bold transition-all shadow-lg hover:shadow-kingdom-gold/20 flex items-center justify-center gap-2">
                    <i data-lucide="upload-cloud" class="w-5 h-5"></i> Submit Your CV
                </a>
                <a href="{{ route('contact') }}" class="h-14 px-10 rounded-lg bg-transparent border border-gray-400 text-white text-lg font-bold hover:bg-white hover:text-kingdom-navy transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="mail" class="w-5 h-5"></i> Contact HR
                </a>
            </div>
        </div>
    </section>

    <script>
        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
             if (window.lucide) {
                 lucide.createIcons();
             }
        });
    </script>
@endsection
