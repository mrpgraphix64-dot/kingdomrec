<footer class="bg-[#050A18] text-white pt-16 pb-0 mt-0 border-t border-gray-900 font-body relative overflow-hidden">
    <!-- Subtle Background Glow -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-kingdom-navy/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-kingdom-gold/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="container mx-auto px-6 lg:px-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-5 mb-16 items-start relative z-10">
        <!-- Col 1: Brand -->
        <div class="flex flex-col items-center md:items-start text-center md:text-left space-y-6">
            <a href="{{ route('home') }}" class="block">
                 <img src="{{ asset('logo.png') }}?v={{ time() }}_final" class="h-20 w-auto mb-6" alt="Kingdom Recruitments">
            </a>
            <p class="text-gray-400 text-sm leading-7 max-w-xs">
                We are Kingdom Recruitments, we pride ourselves on our ability to source and recruit top-tier talent for the individual industry.
            </p>
            <div class="flex gap-4">
                <a href="https://www.facebook.com/Kingdom.Recruitments/" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-[#1877F2] hover:bg-white/10 hover:text-[#1460c4] transition-all border border-white/10 hover:border-[#1877F2]/50">
                    <span class="font-bold text-lg">f</span>
                </a>
                <a href="https://wa.me/447411154198" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-[#25D366] hover:bg-white/10 hover:text-[#1fba58] transition-all border border-white/10 hover:border-[#25D366]/50">
                    <span class="font-bold text-lg">WA</span>
                </a>
            </div>
        </div>

        <!-- Col 2: Quick Links -->
        <div>
            <h4 class="text-white font-bold text-lg mb-8 relative inline-block tracking-wide">
                Quick Links
                <span class="absolute -bottom-3 left-0 w-12 h-[3px] bg-kingdom-gold rounded-full"></span>
            </h4>
            <ul class="space-y-4">
                <li><a href="{{ route('jobs.index') }}" class="text-gray-400 hover:text-white hover:pl-2 transition-all duration-300 text-sm flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-kingdom-gold"></span> Job List</a></li>
                <li><a href="{{ route('applicants.upload') }}" class="text-gray-400 hover:text-white hover:pl-2 transition-all duration-300 text-sm flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-kingdom-gold"></span> Upload CV</a></li>
                <li><a href="{{ route('portal') }}" class="text-gray-400 hover:text-white hover:pl-2 transition-all duration-300 text-sm flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-kingdom-gold"></span> Business Partner</a></li>
                <li><a href="{{ route('portal') }}#partner-login" class="text-gray-400 hover:text-white hover:pl-2 transition-all duration-300 text-sm flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-kingdom-gold"></span> Post a Job</a></li>
                <li><a href="{{ route('applicants.index') }}" class="text-gray-400 hover:text-white hover:pl-2 transition-all duration-300 text-sm flex items-center gap-3"><span class="w-1.5 h-1.5 rounded-full bg-kingdom-gold"></span> Applicants</a></li>
            </ul>
        </div>

        <!-- Col 3: Quick Find -->
        <div>
            <h4 class="text-white font-bold text-lg mb-8 relative inline-block tracking-wide">
                Contact Us
                <span class="absolute -bottom-3 left-0 w-12 h-[3px] bg-kingdom-gold rounded-full"></span>
            </h4>
            <ul class="space-y-4">
                <li class="flex items-start gap-4">
                    <span class="material-symbols-outlined text-kingdom-gold mt-1">call</span>
                    <div class="flex flex-col gap-1">
                        <span class="text-gray-500 text-xs uppercase font-bold tracking-wider">Call Us</span>
                        <a href="tel:07411154198" class="text-gray-300 text-sm hover:text-white transition-colors cursor-pointer">074 1115 4198</a>
                    </div>
                </li>
                <li class="w-full h-px bg-white/5"></li>
                <li class="flex items-start gap-4">
                     <span class="material-symbols-outlined text-kingdom-gold mt-1">mail</span>
                     <div class="flex flex-col gap-1">
                        <span class="text-gray-500 text-xs uppercase font-bold tracking-wider">Email Us</span>
                        <a href="mailto:info@kingdomrecruitments.com" class="text-gray-300 text-sm hover:text-white transition-colors">info@kingdomrecruitments.com</a>
                     </div>
                </li>
            </ul>
        </div>

        <!-- Col 4: Address -->
        <div>
            <h4 class="text-white font-bold text-lg mb-8 relative inline-block tracking-wide">
                Location
                <span class="absolute -bottom-3 left-0 w-12 h-[3px] bg-kingdom-gold rounded-full"></span>
            </h4>
            <div class="flex items-start gap-4">
                <span class="material-symbols-outlined text-kingdom-gold mt-1">location_on</span>
                <div class="flex flex-col gap-2">
                    <span class="text-gray-500 text-xs uppercase font-bold tracking-wider">Visit Us</span>
                    <address class="text-gray-300 text-sm leading-7 not-italic">
                        Greatorex Business Center<br>
                        Suite No: 101 (First Floor)<br>
                        8-10 Greatorex Street,<br>
                        London E1 5NF, UK
                    </address>
                    <a href="https://maps.google.com/?q=8-10+Greatorex+Street,+London+E1+5NF,+UK" target="_blank" class="text-kingdom-gold text-xs font-bold uppercase tracking-wide hover:text-yellow-400 flex items-center gap-1 mt-2">
                        Get Directions <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="bg-[#020611] py-8 pb-24 md:pb-8 border-t border-white/5 relative z-10">
        <div class="container mx-auto px-6 lg:px-12 flex flex-col md:flex-row justify-between items-center gap-6 text-center md:text-left">
             <p class="text-gray-500 text-sm font-medium order-2 md:order-1">
                &copy; {{ date('Y') }} Kingdom Recruitments. All rights reserved.
             </p>
             <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6 text-sm text-gray-500 order-1 md:order-2">
                <div class="flex items-center gap-6">
                    <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
                <!-- <span class="hidden md:inline text-gray-700">|</span> -->
                    <span class="flex items-center gap-1">
                    Designed and Developed by 
                    <a href="https://reworkzone.com" target="_blank" class="text-white font-bold hover:text-red-500 transition-colors">Reworkzone</a>
                </span>
             </div>
        </div>
    </div>
</footer>
