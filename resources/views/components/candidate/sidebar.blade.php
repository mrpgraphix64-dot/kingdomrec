@persist('candidate-sidebar-desktop')
<aside class="w-60 bg-[#0F1D33] flex-shrink-0 hidden lg:flex flex-col sticky top-0 h-screen z-50 select-none border-r border-slate-800/80">
    <style>
        .sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(184, 153, 85, 0.25) transparent;
            overflow-anchor: none;
        }
        .sidebar-scrollbar:hover { scrollbar-color: rgba(184, 153, 85, 0.45) transparent; }
        .sidebar-scrollbar::-webkit-scrollbar { width: 4px; }
        .sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(184, 153, 85, 0.25); border-radius: 10px; }
        .sidebar-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(184, 153, 85, 0.45); }

        .candidate-nav__icon {
            width: 34px; min-width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; border-radius: 9px;
            color: #94a3b8; background-color: rgba(255, 255, 255, 0.03);
            transition: all 180ms ease;
        }
        .candidate-nav__icon svg { width: 17px; height: 17px; flex-shrink: 0; stroke-width: 2; }

        .candidate-nav__item {
            min-height: 44px; padding: 0 10px; border-radius: 10px;
            display: flex; align-items: center; gap: 10px; width: 100%;
            position: relative; cursor: pointer; text-decoration: none;
            color: #cbd5e1; font-size: 13px; font-weight: 600;
            transition: background-color 180ms ease, color 180ms ease, transform 180ms ease;
        }
        .candidate-nav__item:hover { background-color: rgba(255, 255, 255, 0.06); color: #ffffff; transform: translateX(2px); }
        .candidate-nav__item:hover .candidate-nav__icon { color: #B89955; background-color: rgba(184, 153, 85, 0.12); }
        .candidate-nav__item:focus-visible { outline: 2px solid #B89955; outline-offset: 2px; }

        .candidate-nav__item--active { background-color: rgba(255, 255, 255, 0.09) !important; color: #ffffff !important; font-weight: 700; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.20); }
        .candidate-nav__item--active::before {
            content: ""; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3.5px; height: 22px; border-radius: 0 4px 4px 0;
            background-color: #B89955; box-shadow: 0 0 8px rgba(184, 153, 85, 0.6);
        }
        .candidate-nav__item--active .candidate-nav__icon { color: #B89955 !important; background-color: rgba(184, 153, 85, 0.20) !important; }

        .candidate-section-heading {
            font-size: 10px; font-weight: 800; letter-spacing: 0.14em; text-transform: uppercase;
            color: #B89955; padding: 0 10px; margin-top: 20px; margin-bottom: 6px;
        }
    </style>

    <!-- Logo -->
    <div class="p-3.5 flex flex-col items-center justify-center gap-2.5 shrink-0 bg-[#0F1D33] z-10" style="contain: layout style paint;">
        <div class="bg-slate-50 rounded-xl p-2.5 w-full flex items-center justify-center shadow-md border border-white/10">
            <img id="candidate-sidebar-logo" src="{{ asset('logo.png') }}" alt="Kingdom Recruitments" class="w-full max-w-[120px] h-auto object-contain drop-shadow-sm" loading="eager" decoding="sync" width="120" height="42">
        </div>
        <div class="w-full h-px bg-gradient-to-r from-transparent via-[#B89955]/30 to-transparent"></div>
    </div>

    <!-- Navigation -->
    <nav id="candidate-sidebar-nav" class="flex-1 min-h-0 px-2.5 flex flex-col gap-1 overflow-y-auto sidebar-scrollbar relative pb-8">
        @php
            $candidateLinks = [
                ['label' => 'My Profile', 'route' => 'applicant.profile', 'icon' => 'user-circle'],
                ['label' => 'My Schedule', 'route' => 'applicant.schedule', 'icon' => 'calendar'],
                ['label' => 'Timesheets', 'route' => 'applicant.timesheets', 'icon' => 'clock'],
                ['label' => 'My Applications', 'route' => 'jobs.applied', 'icon' => 'briefcase'],
            ];
        @endphp
        @foreach($candidateLinks as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}" wire:navigate
               class="candidate-nav__item {{ $isActive ? 'candidate-nav__item--active' : '' }}">
                <div class="candidate-nav__icon"><i data-lucide="{{ $item['icon'] }}"></i></div>
                <span class="flex-1 truncate">{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="candidate-section-heading">Account</div>
        @php $isEditActive = request()->routeIs('applicant.profile.edit'); @endphp
        <a href="{{ route('applicant.profile.edit') }}" wire:navigate
           class="candidate-nav__item {{ $isEditActive ? 'candidate-nav__item--active' : '' }}">
            <div class="candidate-nav__icon"><i data-lucide="edit-3"></i></div>
            <span class="flex-1 truncate">Edit Profile</span>
        </a>
        <a href="{{ route('jobs.index') }}" wire:navigate class="candidate-nav__item">
            <div class="candidate-nav__icon"><i data-lucide="search"></i></div>
            <span class="flex-1 truncate">Browse Jobs</span>
        </a>
    </nav>

    <!-- Back to Website -->
    <div class="p-3 bg-[#0a1424] border-t border-slate-800/90 shrink-0 z-10 shadow-[0_-4px_12px_rgba(0,0,0,0.15)]">
        <a href="{{ route('home') }}"
           class="group w-full px-3 py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.09] text-slate-300 hover:text-white border border-white/5 hover:border-white/10 transition-all duration-200 flex items-center gap-2.5 hover:-translate-y-0.5 shadow-sm">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-cyan-500/15 text-cyan-400 group-hover:bg-cyan-500/25 group-hover:text-cyan-300 transition-colors shrink-0">
                <i data-lucide="globe" class="w-3.5 h-3.5" stroke-width="2"></i>
            </div>
            <span class="text-xs font-bold truncate">Back to Website</span>
            <i data-lucide="external-link" class="w-3 h-3 ml-auto text-slate-500 group-hover:text-slate-300 transition-colors shrink-0"></i>
        </a>
    </div>

    <!-- Active State Sync (mirrors admin/partner sidebar behavior) -->
    <script data-navigate-once>
        (function() {
            var activeClass = 'candidate-nav__item--active';
            function applyActiveState(nav) {
                var pathUrl = location.href.split('#')[0].split('?')[0];
                var links = nav.querySelectorAll('a.candidate-nav__item');
                var hit = null;
                links.forEach(function(el) {
                    if (el.href.split('#')[0].split('?')[0] === pathUrl) {
                        el.classList.add(activeClass);
                        hit = el;
                    } else {
                        el.classList.remove(activeClass);
                    }
                });
                return hit;
            }
            function initLoad() {
                var nav = document.getElementById('candidate-sidebar-nav');
                if (!nav) return;
                var hit = applyActiveState(nav);
                if (hit) hit.scrollIntoView({ block: 'nearest' });
            }
            document.addEventListener('livewire:navigated', function() {
                var nav = document.getElementById('candidate-sidebar-nav');
                if (nav) applyActiveState(nav);
                if (window.lucide && typeof window.lucide.createIcons === 'function') window.lucide.createIcons();
            });
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLoad);
            } else {
                initLoad();
            }
        })();
    </script>
</aside>
@endpersist
