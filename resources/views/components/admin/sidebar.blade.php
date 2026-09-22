@persist('admin-sidebar-desktop')
<aside class="w-60 bg-[#0F1D33] flex-shrink-0 hidden lg:flex flex-col sticky top-0 h-screen z-50 select-none border-r border-slate-800/80">
    <style>
        /* Modern Subtle Sidebar Scrollbar */
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
        .sidebar-nav__icon {
            width: 34px;
            min-width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 9px;
            color: #94a3b8;
            background-color: rgba(255, 255, 255, 0.03);
            transition: all 180ms ease;
        }
        .sidebar-nav__icon svg,
        .sidebar-nav__icon i {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            stroke-width: 2;
        }

        /* Top-Level Navigation Item */
        .sidebar-nav__item {
            min-height: 44px;
            padding: 0 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            position: relative;
            cursor: pointer;
            text-decoration: none;
            color: #cbd5e1;
            font-size: 13px;
            font-weight: 600;
            transition: background-color 180ms ease, color 180ms ease, transform 180ms ease, box-shadow 180ms ease;
        }
        .sidebar-nav__item:hover {
            background-color: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            transform: translateX(2px);
        }
        .sidebar-nav__item:hover .sidebar-nav__icon {
            color: #B89955;
            background-color: rgba(184, 153, 85, 0.12);
        }
        .sidebar-nav__item:focus-visible {
            outline: 2px solid #B89955;
            outline-offset: 2px;
        }

        /* Active Navigation Item */
        .sidebar-nav__item--active {
            background-color: rgba(255, 255, 255, 0.09) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.20);
        }
        .sidebar-nav__item--active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3.5px;
            height: 22px;
            border-radius: 0 4px 4px 0;
            background-color: #B89955;
            box-shadow: 0 0 8px rgba(184, 153, 85, 0.6);
        }
        .sidebar-nav__item--active .sidebar-nav__icon {
            color: #B89955 !important;
            background-color: rgba(184, 153, 85, 0.20) !important;
        }
        .sidebar-nav__item--active .sidebar-nav__icon svg,
        .sidebar-nav__item--active .sidebar-nav__icon i {
            stroke-width: 2.5;
        }

        /* Chevron Container & Animation */
        .sidebar-nav__chevron {
            margin-left: auto;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 200ms ease;
            color: #94a3b8;
        }
        .sidebar-nav__item:hover .sidebar-nav__chevron {
            color: #ffffff;
        }

        /* Submenu Layout & Visual Hierarchy */
        .sidebar-submenu {
            position: relative;
            margin-left: 18px;
            padding-left: 12px;
            margin-top: 3px;
            margin-bottom: 5px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .sidebar-submenu::before {
            content: "";
            position: absolute;
            left: 0;
            top: 4px;
            bottom: 4px;
            width: 1.5px;
            background-color: rgba(184, 153, 85, 0.25);
            border-radius: 2px;
        }

        /* Submenu Item */
        .sidebar-submenu__item {
            min-height: 38px;
            padding: 0 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 180ms ease, color 180ms ease, transform 180ms ease;
            position: relative;
        }
        .sidebar-submenu__item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            transform: translateX(2px);
        }
        .sidebar-submenu__item:hover .sidebar-submenu__icon {
            color: #B89955;
        }
        .sidebar-submenu__icon {
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #94a3b8;
            transition: color 180ms ease;
        }
        .sidebar-submenu__icon svg,
        .sidebar-submenu__icon i {
            width: 14px;
            height: 14px;
            stroke-width: 2;
        }

        /* Submenu Active State */
        .sidebar-submenu__item--active {
            background-color: rgba(184, 153, 85, 0.14) !important;
            color: #ffffff !important;
            font-weight: 700;
        }
        .sidebar-submenu__item--active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background-color: #B89955;
            border-radius: 0 4px 4px 0;
        }
        .sidebar-submenu__item--active .sidebar-submenu__icon {
            color: #B89955 !important;
        }
        .sidebar-submenu__item--active .sidebar-submenu__icon svg,
        .sidebar-submenu__item--active .sidebar-submenu__icon i {
            stroke-width: 2.5;
        }

        /* Section Headings */
        .sidebar-section-heading {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #B89955;
            padding: 0 10px;
            margin-top: 20px;
            margin-bottom: 6px;
        }
    </style>

    <!-- Top: Logo / Branding Area (Fixed, never scrolls) -->
    <div class="p-3.5 flex flex-col items-center justify-center gap-2.5 shrink-0 bg-[#0F1D33] z-10" style="contain: layout style paint;">
        <div class="bg-slate-50 rounded-xl p-2.5 w-full flex items-center justify-center shadow-md border border-white/10">
            <img id="admin-sidebar-logo" src="{{ asset('logo.png') }}" alt="Kingdom Recruitments" class="w-full max-w-[120px] h-auto object-contain drop-shadow-sm" loading="eager" decoding="sync" width="120" height="42">
        </div>
        <div class="w-full h-px bg-gradient-to-r from-transparent via-[#B89955]/30 to-transparent"></div>
    </div>

    <!-- Middle: Navigation Area (Flex 1, Independent Scroll, Natural Breathing Room) -->
    <nav id="admin-sidebar-nav" class="flex-1 min-h-0 px-2.5 flex flex-col gap-1 overflow-y-auto sidebar-scrollbar relative pb-8">
        @php
            $navGroups = config('admin-nav.groups');
        @endphp

        @foreach($navGroups as $groupName => $items)
            @php
                // Sidebar visibility only — a UX layer on top of the route-level
                // permission middleware in routes/web.php, which remains the real
                // enforcement. An item with no 'permission' key is shown to every
                // admin, matching its route(s) carrying no permission middleware.
                $visibleItems = collect($items)->filter(fn($item) => empty($item['permission']) || auth()->user()->can($item['permission']))->values()->all();
            @endphp
            @continue(empty($visibleItems))

            @if($groupName !== 'Overview')
                <div class="sidebar-section-heading">{{ $groupName }}</div>
            @endif

            @foreach($visibleItems as $item)
                @if(!empty($item['children']))
                    @php
                        $isGroupActive = false;
                        foreach ($item['children'] as $child) {
                            if (request()->routeIs($child['route']) && empty(array_diff_assoc($child['params'] ?? [], request()->query()))) {
                                $isGroupActive = true;
                                break;
                            }
                        }
                    @endphp
                    <div x-data="{ open: {{ $isGroupActive ? 'true' : 'false' }} }" class="w-full">
                        <button type="button" @click="open = !open"
                            class="sidebar-nav__item {{ $isGroupActive ? 'sidebar-nav__item--active' : '' }}">
                            <div class="sidebar-nav__icon">
                                <i data-lucide="{{ $item['icon'] }}"></i>
                            </div>
                            <span class="sidebar-nav__label flex-1 truncate text-left">{{ $item['label'] }}</span>
                            <div class="sidebar-nav__chevron" :class="{ 'rotate-180': open }">
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                            </div>
                        </button>
                        
                        <div x-show="open" x-collapse.duration.200ms x-cloak class="overflow-hidden">
                            <div class="sidebar-submenu">
                                @foreach($item['children'] as $child)
                                    @php
                                        $childHref = route($child['route'], $child['params'] ?? []);
                                        $isChildActive = request()->routeIs($child['route']) && empty(array_diff_assoc($child['params'] ?? [], request()->query()));
                                    @endphp
                                    <a href="{{ $childHref }}" wire:navigate
                                       class="sidebar-submenu__item {{ $isChildActive ? 'sidebar-submenu__item--active' : '' }}">
                                        <div class="sidebar-submenu__icon">
                                            <i data-lucide="{{ $child['icon'] }}"></i>
                                        </div>
                                        <span class="truncate">{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        $href = $item['route'] === '#' ? '#' : route($item['route']);
                        $isActive = request()->routeIs($item['route']);
                    @endphp
                    <a href="{{ $href }}" wire:navigate
                       class="sidebar-nav__item {{ $isActive ? 'sidebar-nav__item--active' : '' }}">
                        <div class="sidebar-nav__icon">
                            <i data-lucide="{{ $item['icon'] }}"></i>
                        </div>
                        <span class="sidebar-nav__label flex-1 truncate">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        @endforeach
    </nav>

    <!-- Bottom: Fixed Utility Area (Anchored, Stable, Distinct Surface) -->
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

    <!-- Active State & Scroll Sync via Livewire -->
    <script data-navigate-once>
        (function() {
            var _saved = 0;
            var _lock = null;
            var _didNav = false;

            var activeClass = 'sidebar-nav__item--active';
            var subActiveClass = 'sidebar-submenu__item--active';

            function applyActiveState(nav) {
                var fullUrl = location.href.split('#')[0];
                var pathUrl = fullUrl.split('?')[0];

                // Reset all parent buttons
                var buttons = nav.querySelectorAll('button.sidebar-nav__item');
                for (var b = 0; b < buttons.length; b++) {
                    buttons[b].classList.remove(activeClass);
                }

                var links = nav.querySelectorAll('a.sidebar-nav__item, a.sidebar-submenu__item');
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
                    var isSub = el.classList.contains('sidebar-submenu__item');
                    
                    var isMatch = exactHit
                        ? (el === exactHit)
                        : (el.href.indexOf('?') === -1 && el.href.split('#')[0] === pathUrl);
                        
                    if (isMatch) {
                        hit = el;
                        if (isSub) {
                            el.classList.add(subActiveClass);
                            // Highlight and open parent if collapsed
                            var parentGroup = el.closest('[x-data]');
                            if (parentGroup) {
                                var pBtn = parentGroup.querySelector('button.sidebar-nav__item');
                                if (pBtn) pBtn.classList.add(activeClass);
                            }
                        } else {
                            el.classList.add(activeClass);
                        }
                    } else {
                        if (isSub) {
                            el.classList.remove(subActiveClass);
                        } else {
                            el.classList.remove(activeClass);
                        }
                    }
                }
                return hit;
            }

            function isVisible(nav, el) {
                var nR = nav.getBoundingClientRect();
                var eR = el.getBoundingClientRect();
                return eR.top >= nR.top && eR.bottom <= nR.bottom;
            }

            // Save scroll position before navigation
            document.addEventListener('livewire:navigate', function() {
                _didNav = true;
                var nav = document.getElementById('admin-sidebar-nav');
                if (nav) _saved = nav.scrollTop;
            });

            // Prevent visible jump during navigation
            document.addEventListener('livewire:navigating', function() {
                if (_lock) clearInterval(_lock);
                _lock = setInterval(function() {
                    var nav = document.getElementById('admin-sidebar-nav');
                    if (nav) nav.scrollTop = _saved;
                }, 5);
                setTimeout(function() { if (_lock) { clearInterval(_lock); _lock = null; } }, 800);
            });

            // Restore scroll and apply active style after navigation
            document.addEventListener('livewire:navigated', function() {
                if (_lock) { clearInterval(_lock); _lock = null; }

                var nav = document.getElementById('admin-sidebar-nav');
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
                
                // Re-initialize Lucide icons if available
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });

            // Initial DOM boot
            function initLoad() {
                var nav = document.getElementById('admin-sidebar-nav');
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
