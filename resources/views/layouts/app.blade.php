<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'Kingdom Recruitments')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=2">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&amp;family=Roboto:wght@300;400;500;700&amp;display=swap"
        rel="stylesheet" />
    <!-- Preload Icons to prevent FOUC -->
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=block" />
    <link rel="preload" as="style" href="https://fonts.googleapis.com/icon?family=Material+Icons&amp;display=block" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=block"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&amp;display=block" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@1.25.0"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        // Force light theme
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    </script>
</head>

<body class="font-body text-gray-700 bg-background-light min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-kingdom-gold selection:text-kingdom-navy">

    @include('partials.navbar')

    <main class="flex-1 w-full">
        @yield('content')
    </main>

    @include('partials.footer')

    @livewireScripts
    <!-- Back to Top Button -->
    <button id="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="fixed bottom-8 left-8 bg-kingdom-navy hover:bg-kingdom-gold text-white p-3 rounded-full shadow-lg transition-all duration-300 opacity-0 translate-y-10 invisible z-50 group hover:shadow-xl hover:scale-110"
        aria-label="Back to top">
        <span
            class="material-symbols-outlined text-2xl group-hover:-translate-y-1 transition-transform">arrow_upward</span>
    </button>

    <script>
        // Back to Top Visibility
        window.addEventListener('scroll', () => {
            const backToTopBtn = document.getElementById('back-to-top');
            if (backToTopBtn) {
                if (window.scrollY > 300) {
                    backToTopBtn.classList.remove('opacity-0', 'translate-y-10', 'invisible');
                    backToTopBtn.classList.add('opacity-100', 'translate-y-0', 'visible');
                } else {
                    backToTopBtn.classList.add('opacity-0', 'translate-y-10', 'invisible');
                    backToTopBtn.classList.remove('opacity-100', 'translate-y-0', 'visible');
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
    @include('components.toast')
    @include('components.chatbot-widget')
    @include('components.unified-support-widget')
</body>

</html>