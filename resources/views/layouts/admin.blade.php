<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kingdom Recruitments - Admin')</title>
    <meta name="description" content="Kingdom Recruitments Admin Dashboard — Manage jobs, applicants, staff, events, and payroll.">
    <meta name="theme-color" content="#0F1D33">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=2">
    <link rel="preload" as="image" href="{{ asset('logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    
    <!-- Google Material Symbols (CDN based for reliable loading) -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" data-navigate-once></script>

    <!-- Alpine Plugins (Core Alpine is provided by Livewire) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" data-navigate-once></script>

    <style>
        /* STEP 1: Remove default Livewire flicker */
        [wire\:loading] {
            display: none !important;
        }

        /* FOUT handling moved to css/material-symbols.css */

        /* Custom Scrollbar moved to app.css */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* ULTRA SMOOTH: GPU acceleration for content transitions */
        #page-content {
            will-change: opacity, transform;
        }

        /* Sidebar is FIXED — never animates, never re-renders */
        [x-persist] aside {
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        [x-persist] aside img {
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: translateZ(0);
        }
        /* GLOBAL ADMIN COMPACT CSS TOKENS & STYLES */
        :root {
            --admin-space-xs: 6px;
            --admin-space-sm: 10px;
            --admin-space-md: 14px;
            --admin-space-lg: 18px;
            --admin-space-xl: 24px;
        }

        /* Sticky Table Headers - Solid Opaque Dark Navy Background & White Text */
        .admin-table-container table thead th,
        table.sticky-header thead th,
        .sticky-thead thead th {
            position: sticky;
            top: 0;
            z-index: 25;
            background-color: #0f1f3d !important;
            color: #ffffff !important;
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.15);
        }
        .dark .admin-table-container table thead th,
        .dark table.sticky-header thead th,
        .dark .sticky-thead thead th {
            background-color: #0f1f3d !important;
            color: #ffffff !important;
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.15);
        }

        /* Compact Table Cells */
        .admin-table td, .admin-table th {
            padding-top: 0.625rem !important;
            padding-bottom: 0.625rem !important;
        }

        /* Ultra-smooth horizontal & vertical scrolling for table containers */
        .admin-table-container,
        .table-responsive,
        .custom-scrollbar {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }
        .admin-table-container::-webkit-scrollbar,
        .table-responsive::-webkit-scrollbar,
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .admin-table-container::-webkit-scrollbar-track,
        .table-responsive::-webkit-scrollbar-track,
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(241, 245, 249, 0.5);
            border-radius: 4px;
        }
        .admin-table-container::-webkit-scrollbar-thumb,
        .table-responsive::-webkit-scrollbar-thumb,
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.5);
            border-radius: 4px;
        }
        .admin-table-container::-webkit-scrollbar-thumb:hover,
        .table-responsive::-webkit-scrollbar-thumb:hover,
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.8);
        }
    </style>
</head>
<body class="admin-panel bg-[#f4f7fb] dark:bg-[#0b1329] min-h-screen text-slate-900 font-sans relative overflow-x-hidden" 
      x-data="{ mobileSidebarOpen: false }"
      :class="{ 'overflow-hidden': mobileSidebarOpen }">

    @include('components.admin.mobile-sidebar')

    <style>
        /* Fallback: if JS fails or takes too long, reveal icons after 1s */
        @keyframes revealIconsFallback {
            to { color: inherit; }
        }
        .material-symbols-outlined {
            animation: revealIconsFallback 0.1s linear 1s forwards;
        }
    </style>
    <script>
        // Use Font Loading API if available to immediately show icons once ready
        if (document.fonts) {
            document.fonts.load('1rem "Material Symbols Outlined"').then(function() {
                document.body.classList.add('fonts-loaded');
            });
        } else {
            // Fallback for older browsers
            setTimeout(() => document.body.classList.add('fonts-loaded'), 200);
        }
    </script>

    <div class="flex min-h-screen w-full bg-[#f4f7fb] dark:bg-[#0b1329]">
        <!-- Sidebar (NEVER re-renders — persisted) -->
        @include('components.admin.sidebar')

        <!-- Main Content Wrapper -->
        <main class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden bg-[#f4f7fb] dark:bg-[#0b1329]">
            <!-- Header (stays fixed) -->
            @include('components.admin.header')

            <!-- ONLY THIS PART NAVIGATES -->
            <div id="page-content" class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-5 lg:p-6 custom-scrollbar relative">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Page Transition Overlay (premium wipe) -->
    <div id="page-transition-overlay"
         class="fixed inset-0 bg-white/80 backdrop-blur-sm z-[200] pointer-events-none"
         style="opacity: 0; display: none;">
    </div>

    <!-- Logo blob-cache: convert to in-memory URL to prevent reload flash -->
    <script data-navigate-once>
        (function() {
            const logo = document.getElementById('admin-sidebar-logo');
            if (logo && logo.src && !logo.src.startsWith('blob:')) {
                fetch(logo.src)
                    .then(r => r.blob())
                    .then(blob => {
                        const blobUrl = URL.createObjectURL(blob);
                        logo.src = blobUrl;
                    })
                    .catch(() => {});
            }
        })();
    </script>

    <!-- STEP 2 + 3: Professional GSAP Page Transitions -->
    <script data-navigate-once>
        // NAVIGATE OUT: fade out content + subtle slide
        document.addEventListener("livewire:navigate", () => {
            const content = document.getElementById("page-content");
            gsap.to(content, {
                opacity: 0,
                y: 10,
                duration: 0.2,
                ease: "power2.out"
            });
        });

        // HIDDEN BEFORE RENDER: The crucial step to prevent flashes
        document.addEventListener("livewire:navigating", () => {
            const content = document.getElementById("page-content");
            if(content) content.style.opacity = '0';
        });

        // NAVIGATED IN: fade in content + slide up
        document.addEventListener("livewire:navigated", () => {
            const content = document.getElementById("page-content");
            
            // Animate content IN
            gsap.fromTo(content,
                { opacity: 0, y: 10 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.25,
                    ease: "power2.out",
                    clearProps: "all"
                }
            );

            // STEP 4: Prevent scroll jump
            content.scrollTo({ top: 0, behavior: "instant" });
        });
    </script>

    <!-- Global Confirmation Modal -->
    <div x-data="{ 
            open: false, 
            title: 'Confirm Action', 
            message: 'Are you sure you want to proceed?', 
            confirmCallback: null 
         }" 
         @keydown.escape.window="open = false" 
         @open-confirm-modal.window="
            open = true; 
            title = $event.detail.title || 'Confirm Action'; 
            message = $event.detail.message || 'Are you sure you want to proceed?'; 
            confirmCallback = $event.detail.onConfirm;
         "
         x-show="open" 
         class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div role="alertdialog" aria-modal="true" aria-labelledby="confirm-modal-title" class="bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8" aria-hidden="true"></i>
                </div>
                <h3 id="confirm-modal-title" class="text-xl font-bold text-slate-900 mb-2" x-text="title"></h3>
                <p class="text-slate-600 mb-6" x-text="message"></p>

                <div class="flex gap-3">
                    <button @click="open = false" class="flex-1 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-50 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                        Cancel
                    </button>
                    <button @click="open = false; if(confirmCallback) confirmCallback()" class="flex-1 py-3 rounded-xl font-bold text-white bg-red-600 hover:bg-red-700 transition-colors shadow-lg shadow-red-600/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@1.25.0" data-navigate-once></script>
    <script data-navigate-once>
        // Init all icons once on first load
        lucide.createIcons();
        // On navigation: re-init all unrendered icons on the page
        document.addEventListener('livewire:navigated', () => {
            lucide.createIcons({ nodes: document.querySelectorAll('i[data-lucide]') });
        });
    </script>

    <!-- Scripts -->
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function submitDeleteForm(actionUrl) {
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = actionUrl;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            f.appendChild(csrf);
            f.appendChild(method);
            document.body.appendChild(f);
            f.submit();
        }
    </script>
    <!-- Toast Notifications -->
    @include('components.toast')
    @include('components.chatbot-widget')
    @include('components.unified-support-widget')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" data-navigate-once></script>
    @stack('scripts')
    @livewireScripts
</body>

</html>