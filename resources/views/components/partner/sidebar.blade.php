@persist('partner-sidebar-desktop')
<aside class="w-60 bg-[#0F1D33] flex-shrink-0 hidden lg:flex flex-col sticky top-0 h-screen z-50 select-none border-r border-slate-800/80">
    <style>
        /* Modern Thin Sidebar Scrollbar */
        .sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(184, 153, 85, 0.25) transparent;
            overflow-anchor: none;
        }
        .sidebar-scrollbar:hover {
            scrollbar-color: rgba(184, 153, 85, 0.45) transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar { width: 4px; }
        .sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { 
            background-color: rgba(184, 153, 85, 0.25); 
            border-radius: 10px; 
        }
        .sidebar-scrollbar:hover::-webkit-scrollbar-thumb { 
            background-color: rgba(184, 153, 85, 0.45); 
        }

        /* Fixed Navigation Icon Container */
        .partner-nav__icon {
            width: 38px;
            min-width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 10px;
            color: #94a3b8;
            transition: all 180ms ease;
        }
        .partner-nav__icon svg,
        .partner-nav__icon i {
            width: 19px;
            height: 19px;
            flex-shrink: 0;
            stroke-width: 2;
        }

        /* Top-Level Navigation Item */
        .partner-nav__item {
            min-height: 48px;
            padding: 0 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            position: relative;
            cursor: pointer;
            text-decoration: none;
            color: #cbd5e1;
            font-size: 13px;
            font-weight: 600;
            transition: background-color 180ms ease, color 180ms ease, transform 180ms ease, box-shadow 180ms ease;
        }
        .partner-nav__item:hover {
            background-color: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            transform: translateX(3px);
        }
        .partner-nav__item:hover .partner-nav__icon {
            color: #B89955;
            background-color: rgba(184, 153, 85, 0.10);
        }
        .partner-nav__item:focus-visible {
            outline: 2px solid #B89955;
            outline-offset: 2px;
        }

        /* Active Navigation Item */
        .partner-nav__item--active {
            background-color: rgba(255, 255, 255, 0.09) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.20);
        }
        .partner-nav__item--active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3.5px;
            height: 24px;
            border-radius: 0 4px 4px 0;
            background-color: #B89955;
            box-shadow: 0 0 10px rgba(184, 153, 85, 0.6);
        }
        .partner-nav__item--active .partner-nav__icon {
            color: #B89955 !important;
            background-color: rgba(184, 153, 85, 0.18) !important;
        }
        .partner-nav__item--active .partner-nav__icon svg,
        .partner-nav__item--active .partner-nav__icon i {
            stroke-width: 2.5;
        }

        /* Section Headings */
        .partner-section-heading {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #B89955;
            padding: 0 12px;
            margin-top: 22px;
            margin-bottom: 6px;
        }
    </style>

    <!-- Logo -->
    <div class="p-4 flex flex-col items-center justify-center gap-3 shrink-0 bg-[#0F1D33] z-10" style="contain: layout style paint;">
        <div class="bg-slate-50 rounded-2xl p-3 w-full flex items-center justify-center shadow-lg border border-white/5 shadow-black/20">
            <img id="partner-sidebar-logo" src="{{ asset('logo.png') }}" alt="Kingdom Recruitments" class="w-full max-w-[125px] h-auto object-contain drop-shadow-sm" loading="eager" decoding="sync" width="125" height="45">
        </div>
        <div class="w-full h-px bg-gradient-to-r from-transparent via-[#B89955]/40 to-transparent"></div>
    </div>

    <!-- Navigation Scroll Container -->
    <nav id="partner-sidebar-nav" class="flex-1 min-h-0 px-3 flex flex-col gap-1 overflow-y-auto sidebar-scrollbar relative pb-12">
        
        @php $isActive = request()->routeIs('partner.dashboard'); @endphp
        <a href="{{ route('partner.dashboard') }}" wire:navigate 
           class="partner-nav__item {{ $isActive ? 'partner-nav__item--active' : '' }}">
            <div class="partner-nav__icon">
                <i data-lucide="layout-dashboard"></i>
            </div>
            <span class="flex-1 truncate">Dashboard</span>
        </a>

        <div class="partner-section-heading">Recruitment Hub</div>
        
        @php
            $partnerLinks1 = [
                ['label' => 'My Bookings & Shifts', 'route' => 'partner.event-list', 'icon' => 'calendar'],
                ['label' => 'Timesheets', 'route' => 'partner.timesheets', 'icon' => 'calendar-clock'],
                ['label' => 'Invoices', 'route' => 'partner.billing', 'icon' => 'receipt'],
                ['label' => 'Rate Card', 'route' => 'partner.rate-card', 'icon' => 'banknote'],
                ['label' => 'Quotations', 'route' => 'partner.quotations', 'icon' => 'file-text'],
            ];
            $partnerLinks2 = [
                ['label' => 'Staff Booking', 'route' => 'partner.book-staff', 'icon' => 'user-plus'],
                ['label' => 'Favourite Staff', 'route' => 'partner.favourite-staff', 'icon' => 'star'],
                ['label' => 'Notes & Messages', 'route' => 'partner.messages', 'icon' => 'message-square'],
                ['label' => 'Contacts', 'route' => 'partner.contacts', 'icon' => 'users'],
            ];
            $partnerLinks3 = [
                ['label' => 'News/Alerts', 'route' => 'partner.alerts', 'icon' => 'bell'],
                ['label' => 'Profile', 'route' => 'partner.profile', 'icon' => 'user'],
            ];
        @endphp

        @foreach($partnerLinks1 as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}" wire:navigate 
               class="partner-nav__item {{ $isActive ? 'partner-nav__item--active' : '' }}">
                <div class="partner-nav__icon">
                    <i data-lucide="{{ $item['icon'] }}"></i>
                </div>
                <span class="flex-1 truncate">{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="partner-section-heading">Management</div>
        @foreach($partnerLinks2 as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}" wire:navigate 
               class="partner-nav__item {{ $isActive ? 'partner-nav__item--active' : '' }}">
                <div class="partner-nav__icon">
                    <i data-lucide="{{ $item['icon'] }}"></i>
                </div>
                <span class="flex-1 truncate">{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="partner-section-heading">Account</div>
        @foreach($partnerLinks3 as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}" wire:navigate 
               class="partner-nav__item {{ $isActive ? 'partner-nav__item--active' : '' }}">
                <div class="partner-nav__icon">
                    <i data-lucide="{{ $item['icon'] }}"></i>
                </div>
                <span class="flex-1 truncate">{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="h-6 shrink-0"></div>
    </nav>

    <!-- Back to Website -->
    <div class="p-3 border-t border-slate-800/80 bg-[#0c1729] mt-auto shrink-0">
        <a href="{{ route('home') }}" class="btn-interactive w-full p-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/5 transition-all flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-cyan-500/15 text-cyan-400 shrink-0">
                <i data-lucide="globe" class="w-3.5 h-3.5" stroke-width="2"></i>
            </div>
            <span class="text-xs font-bold truncate">Back to Website</span>
        </a>
    </div>

    <script data-navigate-once>
        (function() {
            var _saved = 0;
            var _lock = null;
            var _didNav = false;

            var activeClass = 'partner-nav__item--active';

            function applyActiveState(nav) {
                var fullUrl = location.href.split('#')[0];
                var pathUrl = fullUrl.split('?')[0];

                var links = nav.querySelectorAll('a.partner-nav__item');
                var exactHit = null;

                for (var j = 0; j < links.length; j++) {
                    if (links[j].href.split('#')[0] === fullUrl && links[j].href.indexOf('?') !== -1) {
                        exactHit = links[j];
                        break;
                    }
                }

                var hit = null;
                for (var k = 0; k < links.length; k++) {
                    var el = links[k];
                    var isMatch = exactHit
                        ? (el === exactHit)
                        : (el.href.indexOf('?') === -1 && el.href.split('#')[0] === pathUrl);
                        
                    if (isMatch) {
                        hit = el;
                        el.classList.add(activeClass);
                    } else {
                        el.classList.remove(activeClass);
                    }
                }
                return hit;
            }

            function isVisible(nav, el) {
                var nR = nav.getBoundingClientRect();
                var eR = el.getBoundingClientRect();
                return eR.top >= nR.top && eR.bottom <= nR.bottom;
            }

            document.addEventListener('livewire:navigate', function() {
                _didNav = true;
                var nav = document.getElementById('partner-sidebar-nav');
                if (nav) _saved = nav.scrollTop;
            });

            document.addEventListener('livewire:navigating', function() {
                if (_lock) clearInterval(_lock);
                _lock = setInterval(function() {
                    var nav = document.getElementById('partner-sidebar-nav');
                    if (nav) nav.scrollTop = _saved;
                }, 5);
                setTimeout(function() { if (_lock) { clearInterval(_lock); _lock = null; } }, 800);
            });

            document.addEventListener('livewire:navigated', function() {
                if (_lock) { clearInterval(_lock); _lock = null; }

                var nav = document.getElementById('partner-sidebar-nav');
                if (!nav) return;

                if (_didNav) {
                    nav.scrollTop = _saved;
                    var hit = applyActiveState(nav);
                    if (hit && !isVisible(nav, hit)) {
                        hit.scrollIntoView({ block: 'nearest' });
                    }
                    _didNav = false;
                } else {
                    var hit = applyActiveState(nav);
                    if (hit) hit.scrollIntoView({ block: 'nearest' });
                }

                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });

            function initLoad() {
                var nav = document.getElementById('partner-sidebar-nav');
                if (!nav) return;
                var hit = applyActiveState(nav);
                if (hit) hit.scrollIntoView({ block: 'nearest' });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLoad);
            } else {
                initLoad();
            }
        })();
    </script>
</aside>
@endpersist