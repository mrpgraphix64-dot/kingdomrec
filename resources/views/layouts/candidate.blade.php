<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kingdom Recruitments - My Portal')</title>
    <meta name="description" content="Kingdom Recruitments Candidate Portal — Your schedule, timesheets, applications, and profile.">
    <meta name="theme-color" content="#0F1D33">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=2">
    <link rel="preload" as="image" href="{{ asset('logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <!-- Material Icons (used by profile/resume/experience cards) -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/icon?family=Material+Icons&display=block" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&display=block" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" data-navigate-once></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" data-navigate-once></script>

    <style>
        [wire\:loading] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        #page-content { will-change: opacity, transform; }

        .candidate-table-container,
        .custom-scrollbar {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }
        .candidate-table-container::-webkit-scrollbar,
        .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .candidate-table-container::-webkit-scrollbar-track,
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(241, 245, 249, 0.5); border-radius: 4px; }
        .candidate-table-container::-webkit-scrollbar-thumb,
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); border-radius: 4px; }
        .candidate-table-container::-webkit-scrollbar-thumb:hover,
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.8); }
    </style>
</head>
<body class="candidate-panel bg-slate-50 dark:bg-slate-900 min-h-screen text-slate-900 font-sans relative overflow-x-hidden"
      x-data="{ mobileSidebarOpen: false }"
      :class="{ 'overflow-hidden': mobileSidebarOpen }">

    @include('components.candidate.mobile-sidebar')

    <div class="flex min-h-screen w-full bg-slate-50 dark:bg-slate-900">
        @include('components.candidate.sidebar')

        <main class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900">
            @include('components.candidate.header')

            <div id="page-content" class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-5 custom-scrollbar relative">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Global Confirmation Modal (Sign Out, etc.) -->
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
        <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2" x-text="title"></h3>
                <p class="text-slate-600 mb-6" x-text="message"></p>
                <div class="flex gap-3">
                    <button @click="open = false" class="flex-1 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button @click="open = false; if(confirmCallback) confirmCallback()" class="flex-1 py-3 rounded-xl font-bold text-white bg-red-600 hover:bg-red-700 transition-colors shadow-lg shadow-red-600/20">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@1.25.0" data-navigate-once></script>
    <script data-navigate-once>
        lucide.createIcons();
        document.addEventListener('livewire:navigated', () => {
            lucide.createIcons({ nodes: document.querySelectorAll('i[data-lucide]') });
        });
    </script>

    <!-- Toast Notifications -->
    @include('components.toast')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" data-navigate-once></script>
    @stack('scripts')
    @livewireScripts
</body>
</html>
