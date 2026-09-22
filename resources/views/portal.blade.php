<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kingdom Recruitments</title>
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=2">

    <!-- Google Fonts (same as home page) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;700;800&display=swap"
        rel="stylesheet" />

    <!-- Material Symbols & Icons (Preloaded) -->
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
    <link rel="preload" as="style" href="https://fonts.googleapis.com/icon?family=Material+Icons&display=block" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&display=block" rel="stylesheet" />

    <!-- Scripts & Core Styling (Tailwind is loaded via Vite below) -->
    <script src="https://unpkg.com/lucide@1.25.0"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Assets (same as home page for consistent navbar styling) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Navbar & Brand Styles */
        .nav-pill {
            display: flex;
            align-items: center;
            background-color: rgba(17, 24, 39, 0.4);
            backdrop-filter: blur(12px);
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .nav-link {
            padding: 1rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #DC2626;
        }

        .contact-block {
            position: relative;
            padding: 1rem 2rem;
            background-color: #DC2626;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: flex;
            align-items: center;
            transition: background-color 0.3s;
            clip-path: polygon(15% 0, 100% 0, 100% 100%, 0% 100%);
            text-decoration: none;
        }

        .contact-block:hover {
            background-color: #991b1b;
        }

        .theme-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            background-color: #DC2626;
            margin-left: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: background-color 0.3s;
        }

        .theme-toggle-btn:hover {
            background-color: #991b1b;
        }

        /* Custom utilities */
        .bg-grid-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes popIn {
            0% {
                opacity: 0;
                transform: scale(0.9) translateY(4px);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.1);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .animate-pop-in {
            animation: popIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .animate-pulse-slow {
            animation: pulse-slow 8s infinite ease-in-out;
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }

        /* Shine Effect */
        .shine-container {
            position: absolute;
            inset: 0;
            overflow: hidden;
            border-radius: inherit;
            pointer-events: none;
            z-index: 20;
        }

        .shine-beam {
            position: absolute;
            top: 0;
            left: -150%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transform: skewX(-25deg);
            transition: left 0s;
        }

        .group:hover .shine-beam {
            left: 150%;
            transition: left 1s ease-in-out;
        }

        .shine-beam-dark {
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
        }

        /* Loading Cursor */
        .cursor-wait {
            cursor: wait;
        }

        /* Custom Styles for Notifications Section */
        .glow-red {
            filter: drop-shadow(0 0 8px rgba(236, 19, 19, 0.6));
        }

        .magnetic-field {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 160px;
            height: 160px;
        }

        .magnetic-trigger {
            position: absolute;
            width: 50%;
            height: 50%;
            z-index: 10;
        }

        .trigger-tl {
            top: 0;
            left: 0;
        }

        .trigger-tr {
            top: 0;
            right: 0;
        }

        .trigger-bl {
            bottom: 0;
            left: 0;
        }

        .trigger-br {
            bottom: 0;
            right: 0;
        }

        .magnetic-icon {
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            pointer-events: none;
        }

        .trigger-tl:hover~.magnetic-icon {
            transform: translate(-12px, -12px) rotate(-8deg);
        }

        .trigger-tr:hover~.magnetic-icon {
            transform: translate(12px, -12px) rotate(8deg);
        }

        .trigger-bl:hover~.magnetic-icon {
            transform: translate(-12px, 12px) rotate(-4deg);
        }

        .trigger-br:hover~.magnetic-icon {
            transform: translate(12px, 12px) rotate(4deg);
        }

        .magnetic-field:hover .magnetic-glow {
            opacity: 0.8;
            transform: scale(1.1);
        }

        .magnetic-glow {
            transition: all 0.5s ease;
            opacity: 0.4;
        }

        @keyframes ring {

            0%,
            100% {
                transform: rotate(0deg);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: rotate(-15deg);
            }

            20%,
            40%,
            60%,
            80% {
                transform: rotate(15deg);
            }
        }

        @keyframes ripple {
            from {
                transform: scale(1);
                opacity: 0.5;
            }

            to {
                transform: scale(2.5);
                opacity: 0;
            }
        }

        @keyframes confetti-fall {
            from {
                transform: translateY(-10vh) rotateZ(0deg);
                opacity: 1;
            }

            to {
                transform: translateY(100vh) rotateZ(720deg);
                opacity: 0;
            }
        }

        .ringing-active .bell-icon {
            animation: ring 1s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        .ringing-active .ripple-effect {
            animation: ripple 1s cubic-bezier(0, 0, 0.2, 1) forwards;
        }

        .confetti-piece {
            position: absolute;
            width: 8px;
            height: 16px;
            border-radius: 4px;
            opacity: 0;
            animation-name: confetti-fall;
            animation-timing-function: linear;
            animation-fill-mode: forwards;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        @keyframes waveMove {
            0% { transform: translateX(0); }
            100% { transform: translateX(200px); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
    </style>
    <script>
        // Force light theme
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display flex flex-col min-h-screen">

    <!-- Preloader -->
    <div id="app-preloader"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-white dark:bg-gray-900 transition-opacity duration-500"
        @if($errors->any()) style="display: none;" @endif>
        <div class="flex flex-col items-center gap-6">
            <div class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-sm font-bold text-gray-400 tracking-widest uppercase">Loading Portal...</span>
            </div>
        </div>
    </div>



    <!-- Main Navbar -->
    @include('partials.navbar')

    <!-- Toast Container -->
    @include('components.toast')

    <!-- Main Content Container -->
    <div id="app-content" class="flex-grow flex flex-col relative w-full pt-24 lg:pt-28">

        <!-- View: Home -->
        <main id="view-home"
            class="view-screen w-full px-4 pb-12 lg:px-12 lg:pb-16 flex flex-col justify-center @if($errors->any()) hidden @endif">
            <div class="mx-auto w-full max-w-[1400px] min-h-[600px] flex flex-col">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6 flex-1">

                    <!-- Applicant Card -->
                    <div
                        class="lg:col-span-7 flex flex-col relative group overflow-hidden rounded-3xl transition-all duration-300 hover:shadow-2xl hover:shadow-red-600/30 hover:scale-[1.01] animate-fade-in-up delay-100"
                        style="background: linear-gradient(135deg, #7a0000, #ff1a1a);">

                        <!-- Animated glow wave -->
                        <div class="absolute pointer-events-none" style="width:200%; height:300px; bottom:-120px; left:-50%; background:radial-gradient(circle,rgba(255,80,80,0.6) 0%,transparent 70%); filter:blur(80px); animation: waveMove 6s infinite linear;"></div>

                        <div class="shine-container">
                            <div class="shine-beam"></div>
                        </div>

                        <!-- Floating icons -->
                        <div class="absolute top-[20%] right-[15%] w-[50px] h-[50px] rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center z-20 pointer-events-none" style="animation: float 4s ease-in-out infinite;">
                            <span class="material-symbols-outlined text-white/80 text-xl">person_search</span>
                        </div>
                        <div class="absolute top-[40%] right-[5%] w-[50px] h-[50px] rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center z-20 pointer-events-none" style="animation: float 4s ease-in-out infinite 1s;">
                            <span class="material-symbols-outlined text-white/80 text-xl">work</span>
                        </div>
                        <div class="absolute top-[60%] right-[20%] w-[50px] h-[50px] rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center z-20 pointer-events-none" style="animation: float 4s ease-in-out infinite 2s;">
                            <span class="material-symbols-outlined text-white/80 text-xl">description</span>
                        </div>

                        <!-- Applicant Image (right side, blended) -->
                        <div class="absolute right-0 bottom-0 top-0 hidden lg:flex items-end pointer-events-none z-[5]"
                             style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 15%, rgba(0,0,0,0.5) 35%, black 55%), linear-gradient(to bottom, transparent 0%, black 20%, black 80%, transparent 100%); -webkit-mask-composite: destination-in; mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 15%, rgba(0,0,0,0.5) 35%, black 55%), linear-gradient(to bottom, transparent 0%, black 20%, black 80%, transparent 100%); mask-composite: intersect;">
                            <img src="{{ asset('images/applicant.jpg') }}" alt="" class="w-[380px] xl:w-[440px] object-cover h-full opacity-35 group-hover:opacity-45 transition-opacity duration-700" />
                        </div>

                        <div class="relative flex-1 flex flex-col justify-between p-8 lg:p-12 z-10">
                            <div class="flex items-start justify-between animate-fade-in-down delay-300">
                                <span
                                    class="inline-flex items-center justify-center size-12 rounded-full bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <span class="material-symbols-outlined text-2xl">person_search</span>
                                </span>
                                <div
                                    class="px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                                    <span class="text-white text-xs font-bold uppercase tracking-wider">Applicant Portal</span>
                                </div>
                            </div>

                            <div class="space-y-8 max-w-md animate-fade-in-up delay-200">
                                <div class="space-y-4">
                                    <h1
                                        class="text-5xl lg:text-7xl font-black text-white leading-[0.9] tracking-tight drop-shadow-sm">
                                        Find Your<br />Kingdom</h1>
                                    <p class="text-lg lg:text-xl text-white/90 font-medium max-w-sm leading-relaxed">
                                        Join thousands of professionals finding their dream roles today.</p>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                                    <a href="{{ route('applicant.profile') }}"
                                        class="h-14 px-8 rounded-xl bg-white text-red-700 font-bold text-lg hover:bg-gray-50 transition-all active:scale-95 shadow-lg shadow-black/10 flex items-center justify-center gap-2 group/btn">
                                        <span class="btn-text">Applicant Login</span>
                                        <span
                                            class="material-symbols-outlined text-[20px] transition-transform group-hover/btn:translate-x-1 icon-arrow">arrow_forward</span>
                                        <svg class="animate-spin h-5 w-5 text-red-700 hidden icon-loading"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="#register" onclick="handleNav(event, '#register', true)"
                                        class="h-14 px-8 rounded-xl font-bold text-lg text-white transition-all active:scale-95 flex items-center justify-center gap-2"
                                        style="background: linear-gradient(135deg, #ff2a2a, #b80000);">
                                        <span class="btn-text">Register Now</span>
                                        <svg class="animate-spin h-5 w-5 text-white hidden icon-loading"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>

                                <!-- Stats bar -->
                                <div class="flex justify-between rounded-2xl p-5 lg:p-6 mt-4 w-full max-w-xl" style="background: rgba(0,0,0,0.4);">
                                    <div>
                                        <h3 class="text-xl lg:text-2xl font-extrabold text-white">10,000+</h3>
                                        <p class="text-[11px] lg:text-xs text-white/60 font-medium">Professionals Placed</p>
                                    </div>
                                    <div>
                                        <h3 class="text-xl lg:text-2xl font-extrabold text-white">5,000+</h3>
                                        <p class="text-[11px] lg:text-xs text-white/60 font-medium">Active Job Roles</p>
                                    </div>
                                    <div>
                                        <h3 class="text-xl lg:text-2xl font-extrabold text-white">50+</h3>
                                        <p class="text-[11px] lg:text-xs text-white/60 font-medium">Countries Worldwide</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="lg:col-span-5 flex flex-col gap-4 lg:gap-6 h-full">
                        <!-- Admin Card -->
                        <div
                            class="flex-grow flex flex-col rounded-3xl p-[1px] relative group transition-all duration-300 hover:shadow-2xl hover:shadow-cyan-500/10 hover:scale-[1.01] animate-fade-in-up delay-300 overflow-hidden bg-gradient-to-br from-white/10 via-white/5 to-transparent">
                            <!-- Inner card -->
                            <div class="flex-1 flex flex-col rounded-[calc(1.5rem-1px)] bg-[#0d1117] relative overflow-hidden">
                                <!-- Noise texture overlay -->
                                <div class="absolute inset-0 opacity-30 pointer-events-none" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iLjc1IiBzdGl0Y2hUaWxlcz0ic3RpdGNoIi8+PC9maWx0ZXI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIzMDAiIGZpbHRlcj0idXJsKCNhKSIgb3BhY2l0eT0iMC4wNCIvPjwvc3ZnPg==');"></div>
                                <!-- Subtle top-edge glow -->
                                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 h-[1px] bg-gradient-to-r from-transparent via-cyan-400/40 to-transparent pointer-events-none"></div>
                                <!-- Bottom center glow flare -->
                                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-48 h-24 bg-cyan-400/8 blur-[60px] rounded-full pointer-events-none group-hover:bg-cyan-400/15 transition-all duration-700"></div>

                                <!-- Admin boss image (blended) -->
                                <div class="absolute right-0 bottom-0 top-0 hidden lg:flex items-end pointer-events-none z-[1]"
                                     style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 20%, rgba(0,0,0,0.5) 45%, black 70%), linear-gradient(to bottom, transparent 0%, black 25%, black 75%, transparent 100%); -webkit-mask-composite: destination-in; mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 20%, rgba(0,0,0,0.5) 45%, black 70%), linear-gradient(to bottom, transparent 0%, black 25%, black 75%, transparent 100%); mask-composite: intersect;">
                                    <img src="{{ asset('images/admin-boss.jpg') }}" alt="" class="w-[220px] xl:w-[260px] object-cover h-full opacity-30 group-hover:opacity-40 transition-opacity duration-700" />
                                </div>

                                <div class="shine-container">
                                    <div class="shine-beam"></div>
                                </div>

                                <div
                                    class="flex-1 w-full flex flex-row lg:flex-col justify-between items-center lg:items-start gap-4 z-10 relative p-8">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-3 mb-2">
                                            <button onclick="toggleAdminSecret(this)"
                                                class="focus:outline-none cursor-pointer active:scale-95 transition-transform"
                                                aria-label="Toggle secret admin mode">
                                                <span
                                                    class="material-symbols-outlined text-3xl transition-all duration-500 text-red-500 group-hover:rotate-12 admin-icon drop-shadow-[0_0_8px_rgba(239,68,68,0.4)]">shield_lock</span>
                                            </button>
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest border transition-colors duration-300 bg-red-500/10 text-red-400 border-red-500/20 admin-badge">
                                                <span class="block animate-pop-in admin-badge-text">Restricted</span>
                                            </span>
                                        </div>
                                        <h3
                                            class="text-2xl font-bold text-white tracking-tight transition-colors">
                                            System Admin</h3>
                                        <div class="relative h-10 w-full hidden lg:block">
                                            <p
                                                class="text-sm text-gray-400 absolute top-0 left-0 transition-all duration-500 opacity-100 translate-y-0 admin-desc">
                                                Secure gateway for internal staff.</p>
                                            <div
                                                class="text-xs font-mono text-green-400 absolute top-0 left-0 transition-all duration-500 opacity-0 -translate-y-4 pointer-events-none admin-status">
                                                <p>STATUS: ONLINE</p>
                                                <p>VER: 2.4.1-RC</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="w-auto lg:w-full h-12 px-6 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 group-hover:translate-x-1 lg:group-hover:translate-x-0 lg:group-hover:-translate-y-1 border border-white/20 backdrop-blur-sm text-white hover:border-white/40 admin-btn"
                                        style="background: linear-gradient(180deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 40%, rgba(0,0,0,0.1) 100%); box-shadow: 0 1px 0 0 rgba(255,255,255,0.1) inset, 0 -1px 0 0 rgba(0,0,0,0.3) inset, 0 4px 20px rgba(0,0,0,0.4);">
                                        <span class="material-symbols-outlined text-[18px] btn-icon text-white/50">lock</span>
                                        <span class="whitespace-nowrap btn-text">Secure Login</span>
                                        <svg class="animate-spin h-4 w-4 text-white hidden icon-loading"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Business Card -->
                        <div
                            class="flex-grow-[1.5] flex flex-col relative group rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-red-500/20 hover:scale-[1.01] animate-fade-in-up delay-200 border border-white/20"
                            style="background-color: #0b0f2a;">

                            <!-- Radial gradient glows (from React component) -->
                            <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(circle at 20% 50%, rgba(255,0,0,0.4), transparent 40%), radial-gradient(circle at 80% 50%, rgba(0,102,255,0.4), transparent 40%);"></div>

                            <!-- Glow border overlay -->
                            <div class="pointer-events-none absolute inset-0 rounded-2xl border border-transparent opacity-40 blur-[2px]" style="background: linear-gradient(to right, #ef4444, #3b82f6); -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0); -webkit-mask-composite: xor; mask-composite: exclude;"></div>

                            <!-- Handshake image (right side, blended) -->
                            <div class="absolute right-0 bottom-0 top-0 hidden lg:flex items-center pointer-events-none z-[1]"
                                 style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.2) 15%, rgba(0,0,0,0.6) 35%, black 55%), linear-gradient(to bottom, transparent 0%, black 20%, black 80%, transparent 100%); -webkit-mask-composite: destination-in; mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.2) 15%, rgba(0,0,0,0.6) 35%, black 55%), linear-gradient(to bottom, transparent 0%, black 20%, black 80%, transparent 100%); mask-composite: intersect;">
                                <img src="{{ asset('images/handshake-bg.jpg') }}" alt="" class="w-[350px] xl:w-[420px] object-cover h-full opacity-40 group-hover:opacity-50 transition-opacity duration-700 group-hover:scale-105 transition-transform" />
                            </div>

                            <!-- Noise texture -->
                            <div class="absolute inset-0 opacity-[0.12] pointer-events-none" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iLjc1IiBzdGl0Y2hUaWxlcz0ic3RpdGNoIi8+PC9maWx0ZXI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIzMDAiIGZpbHRlcj0idXJsKCNhKSIgb3BhY2l0eT0iMC4wNCIvPjwvc3ZnPg==');"></div>

                            <div class="shine-container">
                                <div class="shine-beam"></div>
                            </div>

                            <!-- Content -->
                            <div class="relative flex-1 flex flex-col justify-between p-8 lg:p-10 z-10">
                                <div class="flex justify-between items-start mb-6">
                                    <span
                                        class="inline-flex items-center justify-center size-10 rounded-full bg-white/10 backdrop-blur-md text-white border border-white/20 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                        <span class="material-symbols-outlined text-xl">domain</span>
                                    </span>
                                    <div
                                        class="px-4 py-1 rounded-full border border-white/30 group-hover:bg-white/10 transition-colors">
                                        <span
                                            class="text-white text-xs font-bold uppercase tracking-wider">Business</span>
                                    </div>
                                </div>
                                <h2
                                    class="text-4xl lg:text-5xl font-bold text-white mb-4 tracking-tight">
                                    Hire the Best</h2>
                                <p class="text-white/70 text-lg lg:text-xl mb-8 max-w-xl leading-relaxed">Access top-tier talent pools
                                    and manage diverse teams efficiently.</p>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('partner.dashboard') }}"
                                        class="px-6 py-4 rounded-xl text-lg font-semibold text-white shadow-xl hover:-translate-y-[2px] transition-all active:scale-95 flex items-center justify-center gap-2"
                                        style="background: linear-gradient(to bottom right, #ef4444, #b91c1c); box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.4);">
                                        <span class="btn-text">Partner Login</span>
                                        <svg class="animate-spin h-4 w-4 text-white hidden icon-loading"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="#join-partner" onclick="handleNav(event, '#join-partner', true)"
                                        class="px-6 py-4 rounded-xl text-lg font-semibold text-white shadow-xl hover:-translate-y-[2px] transition-all active:scale-95 flex items-center justify-center gap-2"
                                        style="background: linear-gradient(to bottom right, #2563eb, #1e40af); box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);">
                                        <span class="btn-text">Join as Partner</span>
                                        <svg class="animate-spin h-4 w-4 text-white hidden icon-loading"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- New Job Notifications & Stats Section --}}
            <div
                class="w-full max-w-7xl mx-auto mt-12 lg:mt-20 bg-white dark:bg-white/5 rounded-[2.5rem] p-6 md:p-12 shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 relative overflow-hidden">
                <!-- Decorative Background Pattern -->
                <div class="absolute inset-0 bg-grid-pattern opacity-[0.03] pointer-events-none"></div>
                <div
                    class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[100px] pointer-events-none translate-x-1/3 -translate-y-1/3">
                </div>

                <!-- Notification Card -->
                <div class="w-full max-w-6xl mx-auto mb-16">
                    <div
                        class="w-full flex flex-col md:flex-row rounded-3xl overflow-hidden shadow-2xl bg-gray-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                        <div
                            class="w-full md:w-1/2 bg-kingdom-navy p-10 md:p-16 flex flex-col items-center md:items-start justify-center text-center md:text-left relative overflow-hidden">
                            <!-- Background Shine -->
                            <div
                                class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-kingdom-red/20 blur-[100px] rounded-full pointer-events-none">
                            </div>

                            <div id="bell-container" class="magnetic-field mb-4 -ml-4 relative z-10">
                                <div class="magnetic-trigger trigger-tl"></div>
                                <div class="magnetic-trigger trigger-tr"></div>
                                <div class="magnetic-trigger trigger-bl"></div>
                                <div class="magnetic-trigger trigger-br"></div>
                                <div class="magnetic-icon relative flex items-center justify-center">
                                    <div
                                        class="ripple-effect absolute w-full h-full rounded-full border-2 border-kingdom-red/40 opacity-0 pointer-events-none">
                                    </div>
                                    <div class="ripple-effect absolute w-full h-full rounded-full border-2 border-kingdom-red/20 opacity-0 pointer-events-none delay-100"
                                        style="animation-delay: 100ms"></div>
                                    <div
                                        class="absolute inset-0 rounded-full bg-kingdom-red/20 glow-red magnetic-glow blur-xl scale-150">
                                    </div>
                                    <div
                                        class="relative p-6 rounded-full bg-kingdom-red/10 glow-red flex items-center justify-center border border-kingdom-red/20">
                                        <span
                                            class="bell-icon material-symbols-outlined text-kingdom-red text-6xl font-bold select-none origin-top">notifications_active</span>
                                    </div>
                                </div>
                            </div>
                            <h2
                                class="text-white text-3xl lg:text-4xl font-extrabold leading-tight tracking-tight mb-4 relative z-10">
                                Get New Job <br class="hidden lg:block" /> Notifications
                            </h2>
                            <p class="text-slate-300 text-lg relative z-10">Never miss an opportunity. Be the first to
                                apply to the top positions in the market.</p>
                        </div>
                        <div
                            class="w-full md:w-1/2 bg-white dark:bg-slate-950 p-10 md:p-16 flex flex-col justify-center">
                            <div class="max-w-[420px] mx-auto md:mx-0 w-full">
                                <div id="success-message" class="text-center flex-col items-center hidden">
                                    <span
                                        class="material-symbols-outlined text-green-500 text-6xl mb-4 animate-pop-in opacity-0"
                                        style="animation-fill-mode: forwards;">check_circle</span>
                                    <h2 class="text-kingdom-navy dark:text-slate-100 text-2xl font-bold mb-2 animate-fade-in-up opacity-0"
                                        style="animation-delay: 0.2s; animation-fill-mode: forwards;">Congratulations!
                                    </h2>
                                    <p class="text-slate-500 dark:text-slate-400 text-base font-normal animate-fade-in-up opacity-0"
                                        style="animation-delay: 0.3s; animation-fill-mode: forwards;">
                                        You're on the list! You'll now receive job notifications directly in your inbox.
                                    </p>
                                </div>
                                <div id="form-container">
                                    <h2 class="text-kingdom-navy dark:text-slate-100 text-2xl font-bold mb-2">Join Our
                                        Newsletter</h2>
                                    <p class="text-slate-500 dark:text-slate-400 text-base font-normal mb-8">
                                        Subscribe & get all related jobs notification directly in your inbox.
                                    </p>
                                    <form id="subscribe-form" class="flex flex-col" novalidate>
                                        <div class="group relative">
                                            <label
                                                class="block text-xs font-bold text-slate-400 mb-1 ml-1 uppercase tracking-wider">Email
                                                Address</label>
                                            <div id="email-input-wrapper"
                                                class="flex w-full items-stretch rounded-xl h-14 border-2 border-slate-100 dark:border-slate-800 focus-within:border-kingdom-navy dark:focus-within:border-kingdom-red transition-all overflow-hidden bg-slate-50 dark:bg-slate-900">
                                                <div class="flex items-center justify-center px-4 text-slate-400">
                                                    <span class="material-symbols-outlined">mail</span>
                                                </div>
                                                <input id="email-input"
                                                    class="form-input flex-1 border-none bg-transparent focus:outline-0 focus:ring-0 text-kingdom-navy dark:text-white text-base font-medium placeholder:text-slate-400 px-2"
                                                    placeholder="Enter your email address" required type="email"
                                                    aria-invalid="false" />
                                            </div>
                                            <p id="email-error" class="text-red-500 text-xs mt-1 ml-1 hidden"></p>
                                        </div>
                                        <button type="submit"
                                            class="mt-4 flex w-full cursor-pointer items-center justify-center rounded-xl h-14 bg-kingdom-red text-white text-lg font-bold tracking-wide hover:bg-red-700 hover:scale-[1.01] active:scale-[0.98] transition-all shadow-xl shadow-kingdom-red/30 select-none">
                                            Get Started
                                        </button>
                                        <p class="text-slate-400 text-xs text-center mt-4">
                                            By subscribing, you agree to our Terms of Service and Privacy Policy.
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Section -->
                <div id="stats-section" class="w-full max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            class="stat-card flex flex-col gap-2 rounded-2xl p-8 border border-slate-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50 backdrop-blur-sm transition-all duration-500 ease-out hover:scale-[1.03] hover:shadow-xl dark:hover:shadow-kingdom-red/10 opacity-0 translate-y-4">
                            <span class="material-symbols-outlined text-kingdom-red mb-2 text-4xl">groups</span>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium uppercase tracking-widest">
                                Active Users</p>
                            <p class="stat-value text-kingdom-navy dark:text-white text-4xl font-black" data-value="500"
                                data-suffix="K+">0K+</p>
                        </div>
                        <div class="stat-card flex flex-col gap-2 rounded-2xl p-8 border border-slate-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50 backdrop-blur-sm transition-all duration-500 ease-out hover:scale-[1.03] hover:shadow-xl dark:hover:shadow-kingdom-red/10 opacity-0 translate-y-4"
                            style="transition-delay: 100ms;">
                            <span class="material-symbols-outlined text-kingdom-red mb-2 text-4xl">work</span>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium uppercase tracking-widest">
                                Jobs Posted</p>
                            <p class="stat-value text-kingdom-navy dark:text-white text-4xl font-black" data-value="120"
                                data-suffix="K+">0K+</p>
                        </div>
                        <div class="stat-card flex flex-col gap-2 rounded-2xl p-8 border border-slate-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50 backdrop-blur-sm transition-all duration-500 ease-out hover:scale-[1.03] hover:shadow-xl dark:hover:shadow-kingdom-red/10 opacity-0 translate-y-4"
                            style="transition-delay: 200ms;">
                            <span class="material-symbols-outlined text-kingdom-red mb-2 text-4xl">verified</span>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium uppercase tracking-widest">
                                Success Rate</p>
                            <p class="stat-value text-kingdom-navy dark:text-white text-4xl font-black" data-value="94"
                                data-suffix="%">0%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confetti Container (hidden but globally available) -->
            <div id="confetti-container"
                class="fixed top-0 left-0 w-full h-full pointer-events-none z-[100] overflow-hidden"></div>

        </main>

        <!-- View: Applicant Login -->
        <div id="view-applicant-login"
            class="view-screen flex-grow flex items-center justify-center px-4 pb-12 lg:px-12 lg:pb-16 hidden">
            <div
                class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up border border-gray-100 dark:border-gray-800">
                <div class="h-2 w-full bg-primary"></div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="size-12 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-2xl shadow-sm text-gray-700 dark:text-white">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <a href="#" onclick="handleNav(event, '#')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer"><span
                                class="material-symbols-outlined">close</span></a>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome Back</h1>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">Login to access your applicant portal.</p>
                    <form class="space-y-4" data-action="{{ route('login.post') }}" onsubmit="handleAjaxSubmit(event)"
                        novalidate>
                        @csrf
                        <div id="login-error-container"
                            class="bg-red-50 text-red-500 text-sm p-3 rounded-lg hidden general-error"></div>
                        <input type="hidden" name="login_type" value="applicant">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email
                                Address</label>
                            <input type="email" name="email"
                                class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary h-12"
                                placeholder="you@example.com" required />
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <a href="{{ route('password.request') }}"
                                    class="text-sm text-primary hover:text-red-700 font-semibold transition-colors">Forgot
                                    Password?</a>
                            </div>
                            <div class="relative flex items-center">
                                <input type="password" name="password"
                                    class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary h-12 pr-10"
                                    placeholder="••••••••" required />
                                <button type="button" onclick="togglePassword(this)"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <button type="submit"
                            class="w-full h-12 bg-primary text-white rounded-xl font-bold hover:bg-red-700 transition-colors shadow-lg shadow-primary/30 flex items-center justify-center gap-2">
                            <span class="btn-text">Sign In</span>
                            <svg class="animate-spin h-5 w-5 text-white hidden icon-loading"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">Don't have an account? <a
                                href="#register" onclick="handleNav(event, '#register')"
                                class="text-primary font-bold hover:underline">Register</a></p>
                    </form>
                </div>
            </div>
        </div>

        <!-- View: Register -->
        <div id="view-register"
            class="view-screen flex-grow flex items-center justify-center px-4 pb-12 lg:px-12 lg:pb-16 hidden">
            <div
                class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up border border-gray-100 dark:border-gray-800">
                <div class="h-2 w-full bg-primary"></div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="size-12 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-2xl shadow-sm text-gray-700 dark:text-white">
                            <span class="material-symbols-outlined">person_add</span>
                        </div>
                        <a href="#" onclick="handleNav(event, '#')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer"><span
                                class="material-symbols-outlined">close</span></a>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Join Kingdom</h1>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">Start your journey to your dream career.</p>
                    <form class="space-y-4" data-action="{{ route('register') }}" onsubmit="handleAjaxSubmit(event)"
                        novalidate>
                        @csrf
                        <div class="bg-red-50 text-red-500 text-sm p-3 rounded-lg hidden general-error"
                            id="register-error"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First
                                    Name</label>
                                <input type="text" name="first_name"
                                    class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary h-12"
                                    required />
                                <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last
                                    Name</label>
                                <input type="text" name="last_name"
                                    class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary h-12"
                                    required />
                                <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email
                                Address</label>
                            <input type="email" name="email"
                                class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary h-12"
                                required />
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Create
                                Password</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password" oninput="checkPasswordStrength(this)"
                                    onfocus="toggleMeter(true, this)" onblur="toggleMeter(false, this)"
                                    class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary h-12 pr-10"
                                    required />
                                <button type="button" onclick="togglePassword(this)"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <!-- Strength Meter -->
                            <div class="mt-2 flex gap-1 h-1 hidden" id="strength-bars">
                                <div
                                    class="h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar">
                                </div>
                                <div
                                    class="h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar">
                                </div>
                                <div
                                    class="h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar">
                                </div>
                                <div
                                    class="h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar">
                                </div>
                            </div>
                            <p class="text-xs mt-1 text-gray-500 font-medium text-right hidden" id="strength-text">Weak
                            </p>
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm
                                Password</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password_confirmation"
                                    class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary h-12 pr-10"
                                    required />
                                <button type="button" onclick="togglePassword(this)"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full h-12 bg-gray-900 dark:bg-white dark:text-black text-white rounded-xl font-bold hover:bg-black dark:hover:bg-gray-200 transition-colors shadow-lg flex items-center justify-center gap-2">
                            <span class="btn-text">Create Account</span>
                            <svg class="animate-spin h-5 w-5 text-white dark:text-black hidden icon-loading"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">Already have an account? <a
                                href="#applicant-login" onclick="handleNav(event, '#applicant-login')"
                                class="text-primary font-bold hover:underline">Sign In</a></p>
                    </form>
                </div>
            </div>
        </div>

        <!-- View: Forgot Password -->
        <div id="view-forgot-password"
            class="view-screen flex-grow flex items-center justify-center px-4 pb-12 lg:px-12 lg:pb-16 hidden">
            <div
                class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up border border-gray-100 dark:border-gray-800">
                <div class="h-2 w-full bg-primary"></div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="size-12 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-2xl shadow-sm text-gray-700 dark:text-white">
                            <span class="material-symbols-outlined">lock_reset</span>
                        </div>
                        <a href="#applicant-login" onclick="handleNav(event, '#applicant-login')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer"><span
                                class="material-symbols-outlined">close</span></a>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Reset Password</h1>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">Enter your email to receive reset instructions.</p>
                    <form class="space-y-4" onsubmit="handleFormSubmit(event, '#applicant-login')" novalidate>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email
                                Address</label><input type="email"
                                class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary h-12"
                                placeholder="you@example.com" required /></div>
                        <button type="submit"
                            class="w-full h-12 bg-primary text-white rounded-xl font-bold hover:bg-red-700 transition-colors shadow-lg shadow-primary/30 flex items-center justify-center gap-2">
                            <span class="btn-text">Send Reset Link</span>
                            <svg class="animate-spin h-5 w-5 text-white hidden icon-loading"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">Remember your password? <a
                                href="#applicant-login" onclick="handleNav(event, '#applicant-login')"
                                class="text-primary font-bold hover:underline">Sign In</a></p>
                    </form>
                </div>
            </div>
        </div>

        <!-- View: Partner Login -->
        <div id="view-partner-login"
            class="view-screen flex-grow flex items-center justify-center px-4 pb-12 lg:px-12 lg:pb-16 hidden">
            <div
                class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up border border-gray-100 dark:border-gray-800">
                <div class="h-2 w-full bg-background-dark"></div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="size-12 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-2xl shadow-sm text-gray-700 dark:text-white">
                            <span class="material-symbols-outlined">business_center</span>
                        </div>
                        <a href="#" onclick="handleNav(event, '#')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer"><span
                                class="material-symbols-outlined">close</span></a>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Partner Portal</h1>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">Manage your recruitment pipeline.</p>
                    <form class="space-y-4" data-action="{{ route('login.post') }}" onsubmit="handleAjaxSubmit(event)"
                        novalidate>
                        @csrf
                        <div id="partner-login-error"
                            class="bg-red-50 text-red-500 text-sm p-3 rounded-lg hidden general-error"></div>
                        <input type="hidden" name="login_type" value="partner">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business
                                Email</label>
                            <input type="email" name="email"
                                class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-background-dark focus:ring-background-dark h-12"
                                placeholder="hr@company.com" required />
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password"
                                    class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-background-dark focus:ring-background-dark h-12 pr-10"
                                    required />
                                <button type="button" onclick="togglePassword(this)"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <button type="submit"
                            class="w-full h-12 bg-background-dark text-white rounded-xl font-bold hover:bg-blue-950 transition-colors shadow-lg shadow-blue-900/30 flex items-center justify-center gap-2">
                            <span class="btn-text">Access Dashboard</span>
                            <svg class="animate-spin h-5 w-5 text-white hidden icon-loading"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                        <div class="flex justify-between items-center text-sm mt-4">
                            <a href="#join-partner" onclick="handleNav(event, '#join-partner')"
                                class="text-gray-500 hover:text-background-dark dark:hover:text-white">Become a
                                Partner</a>
                            <a href="{{ route('password.request') }}" class="text-gray-500 hover:text-background-dark dark:hover:text-white">Forgot
                                Password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View: Join Partner -->
        <div id="view-join-partner"
            class="view-screen flex-grow flex items-center justify-center px-4 pb-12 lg:px-12 lg:pb-16 hidden">
            <div
                class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up border border-gray-100 dark:border-gray-800">
                <div class="h-2 w-full bg-background-dark"></div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="size-12 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-2xl shadow-sm text-gray-700 dark:text-white">
                            <span class="material-symbols-outlined">handshake</span>
                        </div>
                        <a href="#" onclick="handleNav(event, '#')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer"><span
                                class="material-symbols-outlined">close</span></a>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Hire with Kingdom</h1>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">Connect with top-tier talent today.</p>
                    <form class="space-y-4" data-action="{{ route('register.partner') }}"
                        onsubmit="handleAjaxSubmit(event)" novalidate>
                        @csrf
                        <div class="bg-red-50 text-red-500 text-sm p-3 rounded-lg hidden general-error"
                            id="join-partner-error"></div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company
                                Name</label>
                            <input type="text" name="company_name"
                                class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-background-dark focus:ring-background-dark h-12"
                                required />
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Work
                                Email</label>
                            <input type="email" name="email"
                                class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-background-dark focus:ring-background-dark h-12"
                                required />
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Create
                                Password</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password" oninput="checkPasswordStrength(this)"
                                    onfocus="toggleMeter(true, this)" onblur="toggleMeter(false, this)"
                                    class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-background-dark focus:ring-background-dark h-12 pr-10"
                                    required />
                                <button type="button" onclick="togglePassword(this)"
                                    class="absolute right-0 pr-3 inset-y-0 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <!-- Strength Meter -->
                            <div class="mt-2 flex gap-1 h-1 hidden" id="partner-strength-bars">
                                <div
                                    class="h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar">
                                </div>
                                <div
                                    class="h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar">
                                </div>
                                <div
                                    class="h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar">
                                </div>
                                <div
                                    class="h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar">
                                </div>
                            </div>
                            <p class="text-xs mt-1 text-gray-500 font-medium text-right hidden"
                                id="partner-strength-text">Weak</p>
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm
                                Password</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password_confirmation"
                                    class="w-full rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-background-dark focus:ring-background-dark h-12 pr-10"
                                    required />
                                <button type="button" onclick="togglePassword(this)"
                                    class="absolute right-0 pr-3 inset-y-0 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full h-12 bg-background-dark text-white rounded-xl font-bold hover:bg-blue-950 transition-colors shadow-lg flex items-center justify-center gap-2">
                            <span class="btn-text">Request Access</span>
                            <svg class="animate-spin h-5 w-5 text-white hidden icon-loading"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">Already a partner? <a
                                href="#partner-login" onclick="handleNav(event, '#partner-login')"
                                class="text-background-dark dark:text-white font-bold hover:underline">Sign In</a></p>
                    </form>
                </div>
            </div>
        </div>

        <!-- View: Admin Login -->
        <div id="view-admin-login"
            class="view-screen flex-grow flex items-center justify-center px-4 pb-12 lg:px-12 lg:pb-16 hidden">
            <div
                class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up border border-gray-100 dark:border-gray-800">
                <div class="h-2 w-full bg-gray-600"></div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="size-12 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-2xl shadow-sm text-gray-700 dark:text-white">
                            <span class="material-symbols-outlined">shield</span>
                        </div>
                        <a href="#" onclick="handleNav(event, '#')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer"><span
                                class="material-symbols-outlined">close</span></a>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">System Access</h1>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">Secure gateway for administrators.</p>
                    <form class="space-y-4" data-action="{{ route('login.post') }}" onsubmit="handleAjaxSubmit(event)"
                        novalidate>
                        @csrf
                        <div id="admin-login-error"
                            class="bg-red-50 text-red-500 text-sm p-3 rounded-lg hidden general-error"></div>
                        <input type="hidden" name="login_type" value="admin">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admin ID
                                (Email)</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-3.5 text-gray-400 material-symbols-outlined text-[20px]">badge</span>
                                <input type="email" name="email"
                                    class="w-full pl-11 rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-gray-600 focus:ring-gray-600 h-12 font-mono"
                                    placeholder="admin@kingdom.com" required />
                            </div>
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Security Key
                                (Password)</label>
                            <div class="relative flex items-center">
                                <span
                                    class="absolute left-4 text-gray-400 material-symbols-outlined text-[20px]">key</span>
                                <input type="password" name="password"
                                    class="w-full pl-11 pr-10 rounded-xl border-gray-300 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-gray-600 focus:ring-gray-600 h-12 font-mono"
                                    required />
                                <button type="button" onclick="togglePassword(this)"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p class="text-red-500 text-xs mt-1 error-msg hidden"></p>
                        </div>
                        <button type="submit"
                            class="w-full h-12 bg-gray-800 text-white rounded-xl font-bold hover:bg-gray-900 transition-colors shadow-lg flex items-center justify-center gap-2">
                            <span class="icon-default material-symbols-outlined text-[18px]">lock_open</span>
                            <span class="btn-text">Verify & Login</span>
                            <svg class="animate-spin h-5 w-5 text-white hidden icon-loading"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- View: Help Center -->
        <div id="view-help" class="view-screen w-full px-4 pt-0 lg:px-12 lg:pt-0 lg:pb-16 hidden">
            <div class="mx-auto w-full max-w-[1000px] animate-fade-in-up">
                <!-- Header -->
                <div class="text-center mb-12 pt-0">
                    <div
                        class="inline-flex items-center justify-center size-16 rounded-full bg-primary/10 text-primary mb-6 animate-pop-in">
                        <span class="material-symbols-outlined text-3xl">support_agent</span>
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6 tracking-tight">
                        How can we <span class="text-primary">help</span>?
                    </h1>
                    <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                        Browse our frequently asked questions or get in touch with our support team. We're here to
                        ensure your recruitment journey is smooth.
                    </p>

                    <div class="mt-10 relative max-w-lg mx-auto group">
                        <span
                            class="absolute left-5 top-4 text-gray-400 material-symbols-outlined group-focus-within:text-primary transition-colors">search</span>
                        <input type="text" placeholder="Search for answers..."
                            class="w-full pl-14 h-14 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-primary focus:ring-primary shadow-sm transition-shadow group-hover:shadow-md" />
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex justify-center gap-4 mb-10" id="help-tabs">
                    <button onclick="switchHelpTab('applicant')"
                        class="px-8 py-4 rounded-2xl font-bold transition-all flex items-center gap-2 bg-primary text-white shadow-lg shadow-primary/30 scale-105 tab-applicant">
                        <span class="material-symbols-outlined">person</span> Applicant Support
                    </button>
                    <button onclick="switchHelpTab('business')"
                        class="px-8 py-4 rounded-2xl font-bold transition-all flex items-center gap-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 tab-business">
                        <span class="material-symbols-outlined">business_center</span> Partner Support
                    </button>
                </div>

                <!-- FAQ Grid: Applicant -->
                <div id="faq-applicant" class="grid gap-4 mb-12">
                    <div
                        class="bg-white dark:bg-gray-900 p-6 lg:p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up delay-100">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">How do I update my resume?</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Go to your dashboard and click on
                            the 'Upload Resume' card. We support PDF and DOCX formats up to 5MB.</p>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-900 p-6 lg:p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up delay-200">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Is this service free for
                            applicants?</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Yes, Kingdom Recruitments is 100%
                            free for job seekers. We monetize by charging businesses for successful placements.</p>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-900 p-6 lg:p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up delay-300">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">How long does the verification
                            process take?</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Typically 24-48 hours after you
                            complete your profile. You'll receive an email notification once approved.</p>
                    </div>
                </div>

                <!-- FAQ Grid: Business -->
                <div id="faq-business" class="grid gap-4 mb-12 hidden">
                    <div
                        class="bg-white dark:bg-gray-900 p-6 lg:p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up delay-100">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">How do I post a job?</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Navigate to your partner dashboard
                            and click 'New Listing'. Our AI will help you draft the description.</p>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-900 p-6 lg:p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up delay-200">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">What is the pricing model?</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">We operate on a success-fee basis.
                            You only pay a percentage of the first year's salary when you make a hire.</p>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-900 p-6 lg:p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition-shadow animate-fade-in-up delay-300">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Can I invite team members?</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Yes, you can add unlimited team
                            members from the settings page with varying permission levels.</p>
                    </div>
                </div>

                <!-- Contact CTA -->
                <div
                    class="p-8 lg:p-12 rounded-[2rem] bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 text-center relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 relative z-10">Still can't find
                        what you're looking for?</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 relative z-10">Our support team is available
                        Mon-Fri, 9am - 5pm EST.</p>
                    <button
                        class="relative z-10 px-8 py-4 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-bold rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors shadow-sm active:scale-95">Contact
                        Support Team</button>
                </div>

                <div class="mt-12 text-center pb-12">
                    <a href="#" onclick="handleNav(event, '#')"
                        class="inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-primary transition-colors py-2 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                        <span class="material-symbols-outlined text-lg">arrow_back</span> Back to Home
                    </a>
                </div>
            </div>
        </div>

        <!-- View: Applicant Profile -->
        <div id="view-applicant-profile" class="view-screen flex-grow w-full hidden">
            <!-- Profile Layout Adapted from Profile Page -->

            <!-- Profile Header - Clean & Centered -->
            <div class="relative w-full bg-white dark:bg-gray-900 pt-12 pb-8 px-4 sm:px-6 lg:px-8 border-b border-gray-100 dark:border-gray-800">
                <div class="max-w-3xl mx-auto flex flex-col items-center text-center">
                    
                    <!-- Avatar -->
                    <div class="relative group mb-6">
                        <x-avatar :user="$applicant ?? Auth::user()" class="w-32 h-32 lg:w-40 lg:h-40 rounded-full border-4 border-white dark:border-gray-800 shadow-xl" />
                        
                        <form action="{{ route('applicant.update.image') }}" method="POST" enctype="multipart/form-data" id="profile-image-form" 
                            class="absolute bottom-0 right-0">
                            @csrf
                            <input type="file" name="image" id="profile-image-input" class="hidden" accept="image/*" onchange="document.getElementById('profile-image-form').submit()">
                            <button type="button" onclick="document.getElementById('profile-image-input').click()"
                                class="bg-primary text-white p-2.5 rounded-full shadow-lg border-2 border-white dark:border-gray-900 hover:bg-red-700 hover:scale-110 transition-transform"
                                title="Update Profile Picture">
                                <span class="material-symbols-outlined text-lg">photo_camera</span>
                            </button>
                        </form>
                    </div>

                    <!-- Name & Role -->
                    <div class="space-y-2 mb-6">
                        <h1 class="text-3xl lg:text-4xl font-black text-navy-dark dark:text-white tracking-tight">
                            {{ optional(Auth::user())->name ?? 'Applicant' }}
                        </h1>
                        <p class="text-xl text-gray-500 dark:text-gray-400 font-medium">
                            {{ $applicant->role ?? (Auth::user()->designation ?? 'Role not specified') }}
                        </p>
                    </div>

                    <!-- Meta Info (Location & Status) -->
                    <div class="flex flex-wrap items-center justify-center gap-4 mb-8 text-sm sm:text-base">
                        @if(optional(Auth::user())->location)
                        <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                            <span class="material-symbols-outlined text-lg text-primary">location_on</span>
                            {{ Auth::user()->location }}
                        </div>
                        <span class="hidden sm:block text-gray-300 dark:text-gray-700">|</span>
                        @endif
                        
                        <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 px-3 py-1 rounded-full border border-green-100 dark:border-green-900/30">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                            </span>
                            <span class="font-bold text-xs uppercase tracking-wide">Open to Opportunities</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                        <a href="{{ route('applicant.profile.edit') }}"
                            class="px-8 py-3 bg-white dark:bg-gray-800 text-navy-dark dark:text-white rounded-xl text-sm font-bold border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">edit_square</span> Edit Profile
                        </a>
                        <button onclick="copyProfileLink()"
                            class="px-8 py-3 bg-primary text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-lg shadow-red-500/20 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">share</span> Share Profile
                        </button>
                    </div>

                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-5xl mx-auto">

                    <!-- Contact Information -->
                    <div
                        class="bg-white dark:bg-gray-900 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 lg:p-8 h-full">
                        <h3
                            class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-8 flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">contact_page</span> Contact Details
                        </h3>
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div
                                    class="size-12 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined">mail</span>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">
                                        Email Address</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white break-all">{{
                                        optional(Auth::user())->email ?? 'applicant@example.com' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div
                                    class="size-12 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined">call</span>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">
                                        Mobile Number</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ optional(Auth::user())->phone ?? 'Not provided' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resume Section -->
                    <div
                        class="bg-white dark:bg-gray-900 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 lg:p-8 h-full flex flex-col">
                        
                        @php
                            $applicant = Auth::check() ? \App\Models\Applicant::where('email', Auth::user()->email)->first() : null;
                        @endphp

                        <div class="flex justify-between items-center mb-6">
                            <h3
                                class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">description</span> Resume / CV
                            </h3>
                            @if($applicant && $applicant->cv_link)
                                @if($applicant->cv_exists)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">Verified</span>
                                @else
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded">Action Required</span>
                                @endif
                            @else
                                <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded">Pending</span>
                            @endif
                        </div>

                        @if($applicant && $applicant->cv_link)
                            @if($applicant->cv_exists)
                                <div
                                    class="flex-1 flex flex-col items-center justify-center text-center p-8 border-2 border-dashed border-gray-100 dark:border-gray-700 rounded-2xl bg-gray-50 dark:bg-gray-800/50 mb-6 group hover:border-primary/30 transition-colors">
                                    <div
                                        class="size-16 bg-white dark:bg-gray-900 rounded-full shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-kingdom-red text-3xl">description</span>
                                    </div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-1">{{ $applicant->cv_name ?? basename($applicant->cv_link) }}</h4>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">Document Uploaded</p>
                                    <p class="text-xs text-gray-400 mt-2">Saved in your profile</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <a href="{{ route('applicant.view-resume') }}" target="_blank"
                                        class="flex items-center justify-center gap-2 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white font-bold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <span class="material-symbols-outlined text-sm">visibility</span> Preview
                                    </a>
                                    <a href="{{ route('applicant.download-resume') }}" download
                                        class="flex items-center justify-center gap-2 py-3 rounded-xl bg-background-dark text-white font-bold hover:bg-gray-800 transition-colors shadow-lg shadow-background-dark/10">
                                        <span class="material-symbols-outlined text-sm">download</span> Download
                                    </a>
                                </div>
                            @else
                                <div
                                    class="flex-1 flex flex-col items-center justify-center text-center p-8 border-2 border-dashed border-amber-300 dark:border-amber-700 rounded-2xl bg-amber-500/5 dark:bg-amber-500/10 mb-6 group hover:border-amber-500/30 transition-colors">
                                    <div
                                        class="size-16 bg-white dark:bg-gray-900 rounded-full shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-amber-500 text-3xl">warning</span>
                                    </div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-1">Resume File Unavailable</h4>
                                    <p class="text-amber-600 dark:text-amber-400 text-sm">The uploaded resume file could not be found on the server.</p>
                                    <p class="text-xs text-gray-400 mt-2">Please upload a new copy.</p>
                                </div>

                                <a href="{{ route('applicant.profile.edit') }}"
                                    class="flex items-center justify-center gap-2 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition-colors shadow-lg shadow-amber-500/20 w-full">
                                    <span class="material-symbols-outlined text-sm font-bold">upload</span> Upload Resume Copy
                                </a>
                            @endif
                        @else
                            <a href="{{ route('applicants.upload') }}"
                                class="flex-1 flex flex-col items-center justify-center text-center p-8 border-2 border-dashed border-gray-100 dark:border-gray-700 rounded-2xl bg-gray-50 dark:bg-gray-800/50 mb-6 group hover:border-primary/30 transition-colors block cursor-pointer">
                                <div
                                    class="size-16 bg-white dark:bg-gray-900 rounded-full shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-gray-400 text-3xl">upload_file</span>
                                </div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-1">No Resume Uploaded</h4>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Click to upload PDF</p>
                            </a>

                            <div class="grid grid-cols-1 gap-3">
                                <a href="{{ route('applicants.upload') }}"
                                    class="flex items-center justify-center gap-2 py-3 rounded-xl bg-background-dark text-white font-bold hover:bg-gray-800 transition-colors shadow-lg shadow-background-dark/10">
                                    <span class="material-symbols-outlined text-sm">cloud_upload</span> Upload Resume
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

                <!-- View: Partner Dashboard -->
        <div id="view-partner-dashboard" class="view-screen hidden fixed inset-0 z-[60] bg-background-light dark:bg-background-dark flex">
<aside class="w-56 bg-[#0F1D33] flex-shrink-0 hidden lg:flex flex-col sticky top-0 h-screen overflow-hidden z-50">
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.4);
        }

        /* NEW - Active Sidebar Item Simple Design */
        .sidebar-item-active-design {
            background-color: #ffffff;
            color: #1f5d50;
            border-radius: 14px;
            position: relative;
            font-weight: 600;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .dark .sidebar-item-active-design {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff;
        }

        .sidebar-item-active-design::before {
            content: "";
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: #ffffff;
            border-radius: 4px;
        }
        
        .dark .sidebar-item-active-design::before {
            background: #B89955;
        }
    </style>

    <!-- Logo -->
    <div class="p-6 flex flex-col items-center justify-center gap-6">
        <img src="{{ asset('logo.png') }}" alt="Kingdom Recruitments" class="w-full max-w-[140px] h-auto object-contain">
        <div class="w-full h-px bg-gradient-to-r from-transparent via-[#B89955]/50 to-transparent"></div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 flex flex-col gap-1 overflow-y-auto custom-scrollbar relative pb-4">
        
       <a href="#partner-dashboard" class="sidebar-item-active sidebar-item-active-design mt-2 relative group flex items-center gap-3 px-3.5 py-3 transition-all duration-300 w-full text-left">
            <div class="h-9 w-9 flex items-center justify-center rounded-xl transition-all duration-300 text-emerald-700 bg-transparent">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            </div>
            <span class="text-sm font-semibold transition-colors text-emerald-700 dark:text-white">Dashboard</span>
        </a>

        <div class="px-2 mb-2 mt-4">
            <p class="text-[10px] uppercase tracking-widest text-[#B89955] font-bold mb-2">Recruitment Hub</p>
        </div>
        
        @php
            $partnerLinks1 = [
                ['label' => 'Event List', 'icon' => 'calendar'],
                ['label' => 'Shift Listings', 'icon' => 'clipboard-list'],
                ['label' => 'Timesheets', 'icon' => 'clock'],
                ['label' => 'Rate Card', 'icon' => 'credit-card'],
                ['label' => 'Quotations', 'icon' => 'file-text'],
            ];
            $partnerLinks2 = [
                ['label' => 'Book Staff', 'icon' => 'user-plus'],
                ['label' => 'Favourite Staff', 'icon' => 'star'],
                ['label' => 'Notes & Messages', 'icon' => 'message-square'],
                ['label' => 'Contacts', 'icon' => 'contact'],
            ];
            $partnerLinks3 = [
                ['label' => 'News/Alerts', 'icon' => 'bell'],
                ['label' => 'Profile', 'icon' => 'user'],
                ['label' => 'Password', 'icon' => 'lock'],
                ['label' => 'Policies & Terms', 'icon' => 'shield'],
            ];
        @endphp

        @foreach($partnerLinks1 as $item)
        <a href="#" class="relative group flex items-center gap-3 px-3.5 py-3 rounded-2xl transition-all duration-300 w-full text-left hover:bg-white/5 hover:translate-x-1">
            <div class="h-9 w-9 flex items-center justify-center rounded-xl transition-all duration-300 text-slate-400 bg-white/5 group-hover:text-white group-hover:bg-[#B89955] group-hover:shadow-[0_0_15px_rgba(184,153,85,0.4)]">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
            </div>
            <span class="text-sm font-bold transition-colors text-slate-400 group-hover:text-white">{{ $item['label'] }}</span>
        </a>
        @endforeach

        <div class="px-2 mb-2 mt-4">
            <p class="text-[10px] uppercase tracking-widest text-[#B89955] font-bold mb-2">Management</p>
        </div>
        @foreach($partnerLinks2 as $item)
        <a href="#" class="relative group flex items-center gap-3 px-3.5 py-3 rounded-2xl transition-all duration-300 w-full text-left hover:bg-white/5 hover:translate-x-1">
            <div class="h-9 w-9 flex items-center justify-center rounded-xl transition-all duration-300 text-slate-400 bg-white/5 group-hover:text-white group-hover:bg-[#B89955] group-hover:shadow-[0_0_15px_rgba(184,153,85,0.4)]">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
            </div>
            <span class="text-sm font-bold transition-colors text-slate-400 group-hover:text-white">{{ $item['label'] }}</span>
        </a>
        @endforeach

        <div class="px-2 mb-2 mt-4">
            <p class="text-[10px] uppercase tracking-widest text-[#B89955] font-bold mb-2">Account</p>
        </div>
        @foreach($partnerLinks3 as $item)
        <a href="#" class="relative group flex items-center gap-3 px-3.5 py-3 rounded-2xl transition-all duration-300 w-full text-left hover:bg-white/5 hover:translate-x-1">
            <div class="h-9 w-9 flex items-center justify-center rounded-xl transition-all duration-300 text-slate-400 bg-white/5 group-hover:text-white group-hover:bg-[#B89955] group-hover:shadow-[0_0_15px_rgba(184,153,85,0.4)]">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
            </div>
            <span class="text-sm font-bold transition-colors text-slate-400 group-hover:text-white">{{ $item['label'] }}</span>
        </a>
        @endforeach
        
        <div class="my-2 border-t border-white/5 mx-3"></div>
        <a href="#" onclick="confirmLogout(event, 'partner')" class="group flex items-center gap-3 px-3.5 py-3 rounded-2xl transition-all duration-200 w-full text-left hover:bg-red-500/10 hover:translate-x-1">
            <div class="h-9 w-9 flex items-center justify-center rounded-xl transition-all duration-200 text-red-500 bg-white/5 group-hover:text-white group-hover:bg-red-500 group-hover:shadow-[0_0_15px_rgba(239,68,68,0.4)]">
                <span class="material-symbols-outlined text-[20px]">logout</span>
            </div>
            <span class="text-sm font-bold transition-colors text-red-500 group-hover:text-red-400">Logout</span>
        </a>

        <!-- Scroll Fade Hint -->
        <div class="sticky bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-[#0F1D33] to-transparent pointer-events-none"></div>
    </nav>
</aside>
<main class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden">
<header class="h-16 flex items-center justify-between px-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/50 dark:border-slate-800/50 z-20 sticky top-0">
<div class="flex items-center gap-4 w-1/3">
<div class="relative w-full max-w-sm">
<i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
<input class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-kingdom-gold/30 outline-none transition-all dark:text-slate-100" placeholder="Search shifts, staff, or quotes..." type="text"/>
</div>
</div>
<div class="flex items-center gap-6">
<div class="relative p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
<i data-lucide="bell" class="w-5 h-5"></i>
<span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
</div>
<div class="flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-slate-800">
<div class="text-right">
<p class="text-sm font-semibold dark:text-white">{{ auth()->user()->name ?? 'Guest' }}</p>
<p class="text-[10px] text-kingdom-gold uppercase font-bold tracking-tighter">{{ auth()->user()->company_name ?? 'Premium Partner' }}</p>
</div>
<img alt="Partner Profile" class="w-10 h-10 rounded-full border-2 border-kingdom-gold/20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCN1C_HikAQI9ue47nV-WseDVObHwfUIyFT47_gkgD4wi0Y_z2Vdsm6E8wO3wQSVZ2KSf5yVd2JXPoARJZuYDaJVEmE7OREGbZFry__U1K5JBq51wlHAg4hG4iaKtoXa-qvbm1-N743VDtL_-YeyqpKlyeO2h6OqBJ090lOMjTpfgKd9uSg99wzkZLKRCUAhmixL5CMRFJ1nTjlmnnVY8OFy27Cxuxj8oVhJlRSas2eJtzP3VCIuZfYYB-EBO2lS0lpbK0T6BlVZwdy"/>
</div>
</div>
</header>
<div class="flex-1 w-full overflow-y-auto overflow-x-hidden p-4 md:p-6 lg:p-8 custom-scrollbar bg-gradient-to-br from-slate-50 to-slate-200 dark:from-slate-900 dark:to-slate-950">
<div class="flex justify-between items-end mb-8">
<div>
<h2 class="text-2xl font-display font-bold text-kingdom-navy dark:text-white">Partner Command Console</h2>
<p class="text-slate-500 dark:text-slate-400">Review your real-time recruitment performance and active staffing status.</p>
</div>
<a href="mailto:info@kingdom.com?subject=Requesting%20New%20Staff" class="bg-kingdom-gold hover:bg-yellow-600 text-slate-900 px-6 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-lg shadow-kingdom-gold/20 flex items-center gap-2">
<i data-lucide="plus-circle" class="w-5 h-5"></i>
                    Request New Staff
                </a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Active Placements -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border-t border-l border-white/60 dark:border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgba(16,185,129,0.15)] hover:-translate-y-2 transition-all duration-500 group">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-colors duration-500"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="w-12 h-12 flex items-center justify-center bg-emerald-500/10 rounded-xl text-emerald-600 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300 border border-emerald-500/20 shadow-sm">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <span class="text-emerald-700 dark:text-emerald-400 text-xs font-bold bg-emerald-100 dark:bg-emerald-900/40 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800 shadow-sm">+12.5%</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider mb-1 relative z-10">Active Placements</p>
        <h3 class="text-3xl font-black text-slate-800 dark:text-white relative z-10 tracking-tight">{{ $partnerStats['active_placements'] ?? 7 }}</h3>
    </div>

    <!-- Card 2: Open Shifts -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border-t border-l border-white/60 dark:border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgba(59,130,246,0.15)] hover:-translate-y-2 transition-all duration-500 group">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-colors duration-500"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="w-12 h-12 flex items-center justify-center bg-blue-500/10 rounded-xl text-blue-600 transform group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-300 border border-blue-500/20 shadow-sm">
                <i data-lucide="calendar-clock" class="w-6 h-6"></i>
            </div>
            <span class="text-blue-700 dark:text-blue-400 text-xs font-bold bg-blue-100 dark:bg-blue-900/40 px-2.5 py-1 rounded-lg border border-blue-200 dark:border-blue-800 shadow-sm">Live</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider mb-1 relative z-10">Open Shifts</p>
        <h3 class="text-3xl font-black text-slate-800 dark:text-white relative z-10 tracking-tight">{{ $partnerStats['open_shifts'] ?? 0 }}</h3>
    </div>

    <!-- Card 3: Monthly Billing -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border-t border-l border-white/60 dark:border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgba(245,158,11,0.15)] hover:-translate-y-2 transition-all duration-500 group">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-colors duration-500"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="w-12 h-12 flex items-center justify-center bg-amber-500/10 rounded-xl text-amber-600 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300 border border-amber-500/20 shadow-sm">
                <i data-lucide="banknote" class="w-6 h-6"></i>
            </div>
            <span class="text-amber-700 dark:text-amber-400 text-xs font-bold bg-amber-100 dark:bg-amber-900/40 px-2.5 py-1 rounded-lg border border-amber-200 dark:border-amber-800 shadow-sm">Stable</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider mb-1 relative z-10">Monthly Billing</p>
        <h3 class="text-3xl font-black text-slate-800 dark:text-white relative z-10 tracking-tight">£{{ $partnerStats['monthly_billing'] ?? '10,500' }}</h3>
    </div>

    <!-- Card 4: Partner Rating -->
    <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border-t border-l border-white/60 dark:border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgba(168,85,247,0.15)] hover:-translate-y-2 transition-all duration-500 group">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-colors duration-500"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div class="w-12 h-12 flex items-center justify-center bg-purple-500/10 rounded-xl text-purple-600 transform group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-300 border border-purple-500/20 shadow-sm">
                <i data-lucide="badge-check" class="w-6 h-6"></i>
            </div>
            <span class="text-purple-700 dark:text-purple-400 text-xs font-bold bg-purple-100 dark:bg-purple-900/40 px-2.5 py-1 rounded-lg border border-purple-200 dark:border-purple-800 shadow-sm">Elite</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider mb-1 relative z-10">Partner Rating</p>
        <h3 class="text-3xl font-black text-slate-800 dark:text-white relative z-10 tracking-tight">{{ $partnerStats['rating'] ?? '4.9/5.0' }}</h3>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 w-full min-w-0">
<div class="lg:col-span-2 space-y-6 lg:space-y-8 min-w-0">
    
    <!-- Upcoming Event Staffing -->
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl border-t border-l border-white/60 dark:border-white/10 overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.12)] transition-all duration-500 group">
        <!-- Glow effect -->
        <div class="absolute -left-32 -top-32 w-64 h-64 bg-kingdom-gold/5 rounded-full blur-3xl group-hover:bg-kingdom-gold/10 transition-colors duration-500 pointer-events-none"></div>
        
        <div class="relative p-6 border-b border-slate-100 dark:border-slate-800/60 flex justify-between items-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10">
            <h4 class="font-extrabold text-kingdom-navy dark:text-white flex items-center gap-3">
                <div class="h-10 w-10 flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-[#B89955] rounded-xl shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 border border-slate-200 dark:border-slate-700">
                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                </div>
                Upcoming Event Staffing
            </h4>
            <a class="text-xs text-[#B89955] font-bold hover:text-[#0F1D33] dark:hover:text-white transition-colors bg-[#B89955]/10 hover:bg-[#B89955]/20 px-3.5 py-2 rounded-lg" href="#">View All Schedule</a>
        </div>
        <div class="w-full overflow-x-auto border-t border-slate-100">
<table class="w-full text-left min-w-[600px] border-collapse">
<thead class="bg-[#0f1f3d] text-white">
<tr class="text-xs font-semibold tracking-wide text-white uppercase bg-[#0f1f3d]">
<th class="px-6 py-4 text-white">Event / Location</th>
<th class="px-6 py-4 text-white">Requirement</th>
<th class="px-6 py-4 text-white">Fulfillment</th>
<th class="px-6 py-4 text-white font-semibold text-center">Status</th>
</tr>
</thead>
<tbody class="bg-transparent">
    @if(isset($partnerJobs) && count($partnerJobs) > 0)
        @foreach($partnerJobs as $job)
            @php
                $filled = rand(50, 100);
                $statusClass = $filled == 100 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                $statusText = $filled == 100 ? 'Ready' : 'In Progress';
            @endphp
            <tr class="transition-colors duration-150 group border-b border-slate-100 hover:bg-slate-50 bg-white">
                <td class="px-6 py-4">
                    <p class="text-sm font-semibold dark:text-slate-200">{{ $job->sub_category }}</p>
                    <p class="text-xs text-slate-400">{{ $job->location ?? 'TBD' }}</p>
                </td>
                <td class="px-6 py-4 text-sm dark:text-slate-300">{{ $job->type }} - {{ $job->dept }}</td>
                <td class="px-6 py-4">
                    <div class="w-full max-w-[100px] h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="bg-kingdom-gold h-full" style="width: {{ $filled }}%"></div>
                    </div>
                    <p class="text-[10px] mt-1 text-slate-500">{{ $filled }}% Filled</p>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $statusClass }}">{{ $statusText }}</span>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="4" class="px-6 py-12 text-center">
    <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
        <i data-lucide="calendar-off" class="w-10 h-10 mb-3 opacity-50"></i>
        <p class="font-medium">No upcoming events scheduled</p>
        <p class="text-xs mt-1 opacity-70">Your upcoming confirmed staffing shifts will appear here.</p>
    </div>
</td>
        </tr>
    @endif
</tbody>
</table>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Pending Quotations -->
    <div class="relative bg-gradient-to-br from-[#0F1D33] to-[#1a2b4c] text-white p-7 rounded-3xl shadow-[0_8px_30px_rgba(15,29,51,0.2)] hover:shadow-[0_20px_40px_rgba(15,29,51,0.4)] overflow-hidden ring-1 ring-white/10 hover:-translate-y-2 transition-all duration-500 group">
        <!-- Animated background elements -->
        <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-[#B89955]/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700 ease-out"></div>
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-colors duration-500"></div>
        
        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity duration-500 group-hover:scale-110 group-hover:-rotate-12 transform origin-bottom-right">
            <i data-lucide="receipt" class="w-32 h-32"></i>
        </div>
        
        <div class="relative z-10 flex items-center gap-3 mb-6">
            <div class="w-10 h-10 flex items-center justify-center bg-[#B89955]/10 rounded-xl backdrop-blur-md border border-[#B89955]/30 text-[#B89955] group-hover:scale-110 transition-transform duration-300">
                <i data-lucide="file-text" class="w-5 h-5"></i>
            </div>
            <h4 class="text-[#B89955] text-xs font-bold uppercase tracking-widest">Pending Quotations</h4>
        </div>

        <div class="space-y-4 relative z-10">
            @if(isset($partnerQuotations) && count($partnerQuotations) > 0)
                @foreach($partnerQuotations as $quote)
                    <div class="flex justify-between items-center border-b border-white/10 pb-3 group/item">
                        <span class="text-sm text-slate-300 group-hover/item:text-white transition-colors">{{ $quote->role ?? 'Quotation' }} - {{ $quote->date }}</span>
                        <span class="font-bold text-white bg-white/5 px-2.5 py-1 rounded-lg border border-white/10">{{ $quote->amount ?? 'TBD' }}</span>
                    </div>
                @endforeach
            @else
                <div class="flex flex-col items-center justify-center text-slate-400 py-8">
                    <i data-lucide="file-text" class="w-10 h-10 mb-3 opacity-40 group-hover:scale-110 transition-transform duration-500"></i>
                    <p class="text-sm font-semibold text-slate-300">All caught up!</p>
                    <p class="text-[11px] opacity-60 mt-1 text-center font-medium">No pending quotes to review at this time.</p>
                </div>
            @endif
            <button class="w-full mt-4 py-3 bg-[#B89955] hover:bg-white text-white hover:text-[#0F1D33] rounded-xl text-xs font-bold tracking-wider transition-all duration-300 shadow-lg shadow-[#B89955]/20 hover:shadow-white/20">REVIEW ALL QUOTES</button>
        </div>
    </div>
    <!-- Timesheet Approval Rate (Animated 3D Bar Chart) -->
    <div class="relative bg-white dark:bg-slate-900 p-7 rounded-3xl border-t border-l border-white/60 dark:border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.12)] hover:-translate-y-2 transition-all duration-500 group overflow-hidden flex flex-col" x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
        <div class="absolute -right-16 top-1/2 -translate-y-1/2 w-48 h-48 bg-teal-500/5 rounded-full blur-3xl group-hover:bg-teal-500/10 transition-colors duration-500 pointer-events-none"></div>
        
        <div class="flex items-center justify-between mb-6 relative z-10 h-[36px]">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl text-teal-500 shadow-inner group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300 border border-slate-100 dark:border-slate-700">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                </div>
                <h4 class="text-slate-500 dark:text-slate-400 text-xs font-extrabold uppercase tracking-widest">Timesheet Approval Rate</h4>
            </div>
            <span class="text-[10px] font-bold bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 px-2 py-1.5 rounded-lg border border-teal-100 dark:border-teal-800 shadow-sm flex items-center gap-1">
                <div class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></div>
                Last 6 Months
            </span>
        </div>

        <!-- 3D Animated Bar Chart -->
        <div class="flex-1 flex items-end justify-between px-2 pt-6 pb-2 relative z-10 min-h-[160px] border-b border-slate-100 dark:border-slate-800">
            <!-- Background Grid Lines -->
            <div class="absolute inset-0 flex flex-col justify-between pt-6 pb-8 pointer-events-none px-2 z-0">
                <div class="w-full border-t border-dashed border-slate-200 dark:border-slate-700/50"></div>
                <div class="w-full border-t border-dashed border-slate-200 dark:border-slate-700/50"></div>
                <div class="w-full border-t border-dashed border-slate-200 dark:border-slate-700/50"></div>
                <div class="w-full border-t border-dashed border-slate-200 dark:border-slate-700/50"></div>
            </div>

            @php
                $chartData = [
                    ['month' => 'Jul', 'val' => 65],
                    ['month' => 'Aug', 'val' => 85],
                    ['month' => 'Sep', 'val' => 45],
                    ['month' => 'Oct', 'val' => 92],
                    ['month' => 'Nov', 'val' => 78],
                    ['month' => 'Dec', 'val' => 100],
                ];
            @endphp

            @foreach($chartData as $index => $data)
            <div class="flex flex-col items-center justify-end gap-2 relative group/bar z-10 w-1/6 h-full">
                <!-- Tooltip -->
                <div class="absolute -top-10 opacity-0 group-hover/bar:opacity-100 group-hover/bar:-translate-y-2 transition-all duration-300 bg-slate-800 dark:bg-white text-white dark:text-slate-900 text-[10px] font-black px-2.5 py-1 rounded-lg shadow-xl pointer-events-none whitespace-nowrap z-20">
                    {{ $data['val'] }}%
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-slate-800 dark:bg-white rotate-45"></div>
                </div>
                
                <!-- 3D Bar Container -->
                <div class="w-6 sm:w-8 max-w-[40px] bg-slate-50 dark:bg-slate-800/50 rounded-t-xl flex items-end justify-center h-full relative border-b-2 border-slate-200 dark:border-slate-700 mx-auto">
                    <!-- Bar Fill animated -->
                    <div class="w-full rounded-t-xl bg-gradient-to-r from-teal-600 via-teal-400 to-teal-700 dark:from-teal-500 dark:via-teal-300 dark:to-teal-600 shadow-[inset_-3px_0_8px_rgba(0,0,0,0.2),inset_3px_0_8px_rgba(255,255,255,0.4)] group-hover/bar:shadow-[0_0_20px_rgba(20,184,166,0.5)] group-hover/bar:brightness-110 transition-all ease-out relative"
                         style="height: 0%; transition-duration: {{ 800 + ($index * 150) }}ms;"
                         :style="show ? 'height: {{ $data['val'] }}%' : 'height: 0%'">
                        <!-- Top "cap" for 3D cylinder effect -->
                        <div class="absolute top-0 left-0 right-0 h-3 bg-gradient-to-b from-white/50 to-transparent rounded-t-xl"></div>
                    </div>
                </div>
                
                <!-- X-axis Label -->
                <span class="text-[10px] font-extrabold text-slate-400 uppercase group-hover/bar:text-teal-600 dark:group-hover/bar:text-teal-400 transition-colors mt-1">{{ $data['month'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>

<div class="space-y-6 min-w-0">
    <!-- Activity Feed -->
    <div class="relative bg-white dark:bg-slate-900 p-7 rounded-3xl border-t border-l border-white/60 dark:border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.12)] transition-all duration-500 group h-full overflow-hidden flex flex-col">
        <div class="absolute -left-20 -top-20 w-64 h-64 bg-purple-500/5 rounded-full blur-3xl group-hover:bg-purple-500/10 transition-colors duration-500 pointer-events-none"></div>
        
        <h4 class="font-extrabold text-[#0F1D33] dark:text-white mb-8 flex items-center gap-3 relative z-10 h-[36px]">
            <div class="p-2.5 bg-slate-50 dark:bg-slate-800 text-purple-600 dark:text-purple-400 rounded-xl shadow-inner group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-300">
                <i data-lucide="history" class="w-5 h-5"></i>
            </div>
            Activity Feed
        </h4>
        <div class="flex-1 flex flex-col items-center justify-center text-slate-400 py-16 relative z-10">
            <div class="w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-500 shadow-sm border border-slate-100 dark:border-slate-700/50">
                <i data-lucide="clock-3" class="w-10 h-10 text-slate-400/50"></i>
            </div>
            <p class="font-bold text-slate-600 dark:text-slate-300">No Recent Activity</p>
            <p class="text-[11px] mt-2 opacity-70 text-center font-medium max-w-[200px]">Your latest actions and system notifications will appear here.</p>
        </div>
    </div>
</div>
</div>
</div>
</main>
        </div>

    </div>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Scripts -->
    <script>
        // Auth State injected from Server
        const authState = {
            check: {{ Auth::check() ? 'true' : 'false' }},
            role: "{{ optional(Auth::user())->role ?? '' }}"
        };

        // --- Theme Management ---
        // Theme toggling is handled by the Navbar's onclick="toggleTheme()" 
        // and the script in <head>, so we don't need event listeners here.

        function initTheme() {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }

        // --- Routing / Navigation Logic ---
        function handleNav(event, hash, showLoading = false) {

            event.preventDefault();

            if (showLoading) {
                const btn = event.currentTarget;
                if (!btn) return;

                // Show loading state
                btn.classList.add('cursor-wait', 'opacity-90');
                const btnText = btn.querySelector('.btn-text');
                const btnIcon = btn.querySelector('.btn-icon'); // For admin lock
                const btnArrow = btn.querySelector('.icon-arrow'); // For arrows
                const loadingIcon = btn.querySelector('.icon-loading');

                if (btnText) {
                    btn.dataset.originalText = btnText.innerText;
                    btnText.innerText = 'Loading...';
                }
                if (btnIcon) btnIcon.style.display = 'none';
                if (btnArrow) btnArrow.style.display = 'none';
                if (loadingIcon) loadingIcon.style.display = 'block';

                setTimeout(() => {
                    window.location.hash = hash;

                    // Reset button state
                    btn.classList.remove('cursor-wait', 'opacity-90');
                    if (btnText) btnText.innerText = btn.dataset.originalText;
                    if (btnIcon) btnIcon.style.display = 'inline-block';
                    if (btnArrow) btnArrow.style.display = 'inline-block';
                    if (loadingIcon) loadingIcon.style.display = 'none';
                }, 800);
            } else {
                window.location.hash = hash;
            }

            // Clear forms on Close (when hash is empty or just '#')
            if (hash === '#' || hash === '') {
                document.querySelectorAll('form').forEach(form => {
                    form.reset();
                    // Also hide any error messages
                    form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
                    form.querySelectorAll('.general-error').forEach(el => el.classList.add('hidden'));
                    form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

                    // Reset password strength meters
                    if (typeof toggleMeter !== 'undefined') {
                        form.querySelectorAll('input[type="password"]').forEach(input => toggleMeter(false, input));
                    }
                });
            }
        }

        // Toast Notification System
        function showToast(title, message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500' : 'bg-green-500';
            const icon = type === 'error' ? 'error' : 'check_circle';

            toast.className = `flex items-center w-full max-w-sm p-4 text-white ${bgColor} rounded-xl shadow-lg mb-4 transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-white/20">
                    <span class="material-symbols-outlined text-xl">${icon}</span>
                </div>
                <div class="ml-3 text-sm font-normal">
                    <span class="block font-bold">${title}</span>
                    <span class="block text-white/90">${message}</span>
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white/20 text-white rounded-lg focus:ring-2 focus:ring-white p-1.5 hover:bg-white/30 inline-flex h-8 w-8 items-center justify-center" onclick="this.parentElement.remove()">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });

            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
        }

        // Validation Logic
        function validateForm(form, targetHash) {
            let isValid = true;

            // Helper to show error
            const showError = (input, msg) => {
                isValid = false;
                input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                showToast('Validation Error', msg, 'error');
            };

            // Remove previous error styles and hide messages
            form.querySelectorAll('input, select').forEach(el => {
                el.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            });
            form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));

            // Generic "Required" Check
            let firstInvalid = null;
            form.querySelectorAll('[required]').forEach(el => {
                if (!el.value.trim()) {
                    el.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

                    // Find error message container
                    // Try direct sibling first, then wrapper sibling (for passwords with relative div)
                    let errorMsg = el.parentElement.querySelector('.error-msg');
                    if (!errorMsg) {
                        // Check if parent is .relative (like password fields) and get next sibling of parent
                        if (el.parentElement.classList.contains('relative')) {
                            errorMsg = el.parentElement.parentElement.querySelector('.error-msg');
                        } else {
                            // Backup: check next sibling
                            errorMsg = el.nextElementSibling;
                            if (errorMsg && !errorMsg.classList.contains('error-msg')) errorMsg = null;
                        }
                    }

                    if (errorMsg) {
                        errorMsg.innerText = 'This field is required.';
                        errorMsg.classList.remove('hidden');
                    }

                    if (!firstInvalid) firstInvalid = el;
                    isValid = false;
                }
            });

            if (firstInvalid) {
                // remove showToast
                firstInvalid.focus();
                return false;
            }

            // Specific Validations based on Target View

            // Applicant Login or Register
            if (targetHash === '#applicant-dashboard') {
                const password = form.querySelector('input[type="password"]');
                const emails = form.querySelectorAll('input[type="email"]');

                // Password Length
                if (password && password.value.length < 8) {
                    showError(password, 'Password must be at least 8 characters long.');
                    return false;
                }

                // Email Format (Basic)
                if (emails.length > 0) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    emails.forEach(email => {
                        if (!emailRegex.test(email.value)) {
                            showError(email, 'Please enter a valid email address.');
                            isValid = false;
                        }
                    });
                    if (!isValid) return false;
                }
            }

            // Partner Registration (Obsolete Industry Check Removed)
            if (form.closest('#view-join-partner')) {
                // Additional partner checks if needed
            }

            return isValid;
        }

        function handleFormSubmit(event, targetHash) {
            event.preventDefault();
            const form = event.target;

            // Run Validation
            if (!validateForm(form, targetHash)) {
                return;
            }

            const btn = form.querySelector('button[type="submit"]');

            // Show loading state
            btn.classList.add('cursor-wait', 'opacity-80');
            const btnText = btn.querySelector('.btn-text');
            const iconDefault = btn.querySelector('.icon-default');
            const loadingIcon = btn.querySelector('.icon-loading');

            if (btnText) btnText.innerText = 'Processing...';
            if (iconDefault) iconDefault.style.display = 'none';
            if (loadingIcon) loadingIcon.style.display = 'block';

            setTimeout(() => {
                window.location.hash = targetHash;

                // Success Toast
                showToast('Success', 'Operation completed successfully!', 'success');

                // Reset (though usually form submits redirect away)
                btn.classList.remove('cursor-wait', 'opacity-80');
                if (btnText) btnText.innerText = 'Submit';
                if (iconDefault) iconDefault.style.display = 'inline-block';
                if (loadingIcon) loadingIcon.style.display = 'none';
            }, 1200);
        }

        function router() {
            let hash = window.location.hash || '#';

            // --- Route Attributes / Guards ---

            // 1. Logged-in User on Guest Guard - Redirect to Dashboard
            const guestRoutes = ['', '#', '#applicant-login', '#register', '#forgot-password', '#partner-login', '#join-partner', '#admin-login'];
            if (authState.check && guestRoutes.includes(hash)) {
                if (authState.role === 'applicant' || authState.role === 'applicant') {
                    window.location.href = "{{ route('applicant.profile') }}";
                    return;
                } else if (authState.role === 'partner') {
                    window.location.href = "{{ route('partner.dashboard') }}";
                    return;
                } else if (authState.role === 'admin') {
                    window.location.href = "{{ route('admin.dashboard') }}";
                    return;
                }
            }

            // 2. Protected Routes (Dashboards) - Redirect to Login if guest or wrong role
            if (hash === '#applicant-profile') {
                if (!authState.check || (authState.role !== 'applicant' && authState.role !== 'applicant')) {
                    window.location.hash = '#applicant-login';
                    return;
                } else {
                    window.location.href = "{{ route('applicant.profile') }}";
                    return;
                }
            }
            else if (hash === '#partner-dashboard') {
                if (!authState.check || authState.role !== 'partner') {
                    window.location.hash = '#partner-login';
                    return;
                } else {
                    window.location.href = "{{ route('partner.dashboard') }}";
                    return;
                }
            }
            else if (hash === '#admin-dashboard') {
                if (!authState.check || authState.role !== 'admin') {
                    window.location.hash = '#admin-login';
                    return;
                } else {
                    window.location.href = "{{ route('admin.dashboard') }}";
                    return;
                }
            }

            // Hide all views
            document.querySelectorAll('.view-screen').forEach(el => el.classList.add('hidden'));

            let viewId = 'view-home';
            if (hash === '#applicant-login') viewId = 'view-applicant-login';
            else if (hash === '#register') viewId = 'view-register';
            else if (hash === '#forgot-password') viewId = 'view-forgot-password';
            else if (hash === '#partner-login') viewId = 'view-partner-login';
            else if (hash === '#join-partner') viewId = 'view-join-partner';
            else if (hash === '#admin-login') viewId = 'view-admin-login';
            else if (hash === '#help') viewId = 'view-help';
            else if (hash === '#applicant-profile') viewId = 'view-applicant-profile';
            else if (hash === '#partner-dashboard') viewId = 'view-partner-dashboard';

            const activeEl = document.getElementById(viewId);
            if (activeEl) {
                activeEl.classList.remove('hidden');
                // Re-trigger animations
                activeEl.classList.remove('animate-fade-in-up');
                void activeEl.offsetWidth; // trigger reflow
                activeEl.classList.add('animate-fade-in-up');
            }

            window.scrollTo(0, 0);
        }

        // AJAX Submission Logic for Auth Forms
        async function handleAjaxSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const action = form.dataset.action;
            const btn = form.querySelector('button[type="submit"]');

            // Clear previous errors
            form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
            form.querySelectorAll('input').forEach(el => el.classList.remove('border-red-500'));

            // Try to find a general error container in this specific form first
            // Try to find a general error container in this specific form first
            const localGeneralError = form.querySelector('.general-error');
            if (localGeneralError) localGeneralError.classList.add('hidden');

            const generalError = document.getElementById('login-error-container');
            if (generalError) generalError.classList.add('hidden');

            // Show loading
            btn.classList.add('cursor-wait', 'opacity-80');
            const btnText = btn.querySelector('.btn-text');
            const loadingIcon = btn.querySelector('.icon-loading');
            const originalText = btnText ? btnText.innerText : '';

            if (btnText) btnText.innerText = 'Processing...';
            if (loadingIcon) loadingIcon.style.display = 'block';

            try {
                const formData = new FormData(form);
                const response = await fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json().catch(() => ({}));
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }
                    // Check if it's a redirect to the dashboard or home
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }

                    showToast('Success', 'Redirecting...', 'success');
                    window.location.reload();
                    return;
                } else if (response.status === 419) {
                    // Seamless Retry for CSRF Mismatch
                    if (!form.dataset.retried) {
                        form.dataset.retried = 'true'; // Prevent infinite loop

                        try {
                            const csrfRes = await fetch('/csrf-token');
                            const csrfData = await csrfRes.json();
                            const newToken = csrfData.token;

                            // Update all forms on page to be safe
                            document.querySelectorAll('input[name="_token"]').forEach(el => el.value = newToken);

                            // Update current payload
                            formData.set('_token', newToken);

                            // Retry request
                            const retryResponse = await fetch(action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            // Recursively handle the retry response by simulating a successful "first" response
                            // But better to just process it here:
                            if (retryResponse.ok) {
                                // Success flow
                                window.location.reload(); // Or redirect if we parse it, but reload is safe success state here for login
                                return;
                            } else if (retryResponse.status === 422 || retryResponse.status === 401) {
                                // Pass through to 422 handler below, but we need to overwrite 'response' variable?
                                // Easier to just RESTART the function?
                                // Let's just manually trigger the logic or recursively call handleAjaxSubmit?
                                // Form data is already updated in DOM inputs, so recursive call works if we reset event?
                                // No, handleAjaxSubmit expects an event.

                                // Let's just handle 422 here manually for the retry to match standard flow
                                const retryData = await retryResponse.json();
                                if (retryData.errors) {
                                    // Use same logic as below
                                    Object.keys(retryData.errors).forEach(key => {
                                        const input = form.querySelector(`[name="${key}"]`);
                                        if (input) {
                                            input.classList.add('border-red-500');
                                            let errorMsg = input.parentElement.querySelector('.error-msg');
                                            if (!errorMsg) errorMsg = input.closest('div').parentElement.querySelector('.error-msg');
                                            if (!errorMsg) errorMsg = input.nextElementSibling;
                                            if (errorMsg && !errorMsg.classList.contains('error-msg')) errorMsg = null;
                                            if (errorMsg) {
                                                errorMsg.innerText = retryData.errors[key][0];
                                                errorMsg.classList.remove('hidden');
                                            }
                                        }
                                    });
                                }
                                return;
                            }
                        } catch (e) {
                            console.error('Retry failed', e);
                            window.location.reload(); // Fallback to reload if retry crashes
                        }
                    } else {
                        window.location.reload(); // Already retried, give up and reload
                    }
                    return;
                } else if (response.status === 422 || response.status === 401) {
                    const data = await response.json();
                    if (data.errors) {

                        Object.keys(data.errors).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.classList.add('border-red-500');

                                // Robust error message finding
                                let errorMsg = input.parentElement.querySelector('.error-msg');
                                if (!errorMsg) {
                                    // Check parent's parent (for .relative wrappers etc)
                                    errorMsg = input.closest('div').parentElement.querySelector('.error-msg');
                                }
                                if (!errorMsg) {
                                    // Fallback: check next sibling regardless of container
                                    errorMsg = input.nextElementSibling;
                                    if (errorMsg && !errorMsg.classList.contains('error-msg')) errorMsg = null;
                                }

                                if (errorMsg) {
                                    errorMsg.innerText = data.errors[key][0];
                                    errorMsg.classList.remove('hidden');
                                }
                            }
                        });
                    }
                } else {
                    throw new Error('Server Error');
                }

            } catch (error) {
                console.error(error);
                // showToast('Error', 'An unexpected error occurred.', 'error');

                // Try form specific error first
                const localGeneralError = form.querySelector('.general-error');
                if (localGeneralError) {
                    localGeneralError.innerText = 'Something went wrong. Please try again.';
                    localGeneralError.classList.remove('hidden');
                } else if (generalError) {
                    // Log warning that we fell back to global
                    console.warn('Falling back to global error container');
                    // Avoid showing it if it's not visible/related? 
                    // No, for now let's just show it but ideally we shouldn't reach here if all forms have .general-error
                    generalError.innerText = 'Something went wrong. Please try again.';
                    generalError.classList.remove('hidden');
                }
            } finally {
                // Reset Loading
                btn.classList.remove('cursor-wait', 'opacity-80');
                if (btnText) btnText.innerText = originalText;
                if (loadingIcon) loadingIcon.style.display = 'none';
            }
        }

        window.handleAjaxSubmit = handleAjaxSubmit;

        // --- Live Validation ---
        function validateInput(input) {
            const form = input.closest('form');
            if (!form) return;

            // Reset styles
            input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

            // Robust error message finding
            let errorMsg = input.parentElement.querySelector('.error-msg');
            if (!errorMsg) {
                errorMsg = input.closest('div').parentElement.querySelector('.error-msg');
            }
            if (!errorMsg) {
                errorMsg = input.nextElementSibling;
                if (errorMsg && !errorMsg.classList.contains('error-msg')) errorMsg = null;
            }

            if (errorMsg) errorMsg.classList.add('hidden');

            // 1. Required Check
            if (input.hasAttribute('required') && !input.value.trim()) {
                input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                if (errorMsg) {
                    errorMsg.innerText = 'This field is required.';
                    errorMsg.classList.remove('hidden');
                }
                return false;
            }

            // 2. Email Format Check (if type email and not empty)
            if (input.type === 'email' && input.value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value.trim())) {
                    input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    if (errorMsg) {
                        errorMsg.innerText = 'Please enter a valid email address.';
                        errorMsg.classList.remove('hidden');
                    }
                    return false;
                }
            }

            return true;
        }

        function initLiveValidation() {
            const inputs = document.querySelectorAll('form[novalidate] input');
            inputs.forEach(input => {
                // Validate on blur
                input.addEventListener('blur', () => {
                    validateInput(input);
                });

                // Clear error on input (typing)
                input.addEventListener('input', () => {
                    // Start checking live only if it was already invalid? 
                    // Or just clear errors for better UX. 
                    // Usually: clear error immediately when typing starts.
                    input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

                    let errorMsg = input.parentElement.querySelector('.error-msg');
                    if (!errorMsg) {
                        errorMsg = input.closest('div').parentElement.querySelector('.error-msg');
                    }
                    if (!errorMsg) {
                        errorMsg = input.nextElementSibling;
                        if (errorMsg && !errorMsg.classList.contains('error-msg')) errorMsg = null;
                    }
                    if (errorMsg) errorMsg.classList.add('hidden');
                });
            });
        }

        // Call init
        // Call init moved to window.onload logic to ensure DOM is ready and handle errors gracefully

        // --- Interactions ---
        function logout() {
            Swal.fire({
                title: 'Sign Out?',
                text: 'Are you sure you want to sign out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0F1D33',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, sign out'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.hash = '#';
                }
            });
        }

        // Admin Secret Toggle
        let adminRevealed = false;
        function toggleAdminSecret(btn) {
            adminRevealed = !adminRevealed;
            const container = btn.closest('.relative.group');
            const icon = btn.querySelector('.admin-icon');
            const badge = container.querySelector('.admin-badge');
            const badgeText = container.querySelector('.admin-badge-text');
            const desc = container.querySelector('.admin-desc');
            const status = container.querySelector('.admin-status');
            const loginBtn = container.querySelector('.admin-btn');
            const btnText = loginBtn.querySelector('.btn-text');
            const btnIcon = loginBtn.querySelector('.btn-icon');

            if (adminRevealed) {
                icon.innerText = 'verified_user';
                icon.classList.remove('text-primary');
                icon.classList.add('text-green-600', 'rotate-[360deg]');

                badge.className = "px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest border transition-colors duration-300 bg-green-50 text-green-700 border-green-200 admin-badge";
                badgeText.innerText = "System OK";

                desc.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                desc.classList.remove('opacity-100', 'translate-y-0');

                status.classList.remove('opacity-0', '-translate-y-4', 'pointer-events-none');
                status.classList.add('opacity-100', 'translate-y-0');

                loginBtn.className = "w-auto lg:w-full h-12 px-6 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2 group-hover:translate-x-1 lg:group-hover:translate-x-0 lg:group-hover:-translate-y-1 shadow-md group-hover:shadow-lg bg-green-600 hover:bg-green-700 text-white admin-btn";
                btnText.innerText = "Console";
                btnIcon.innerText = "terminal";
            } else {
                icon.innerText = 'shield_lock';
                icon.classList.remove('text-green-600', 'rotate-[360deg]');
                icon.classList.add('text-primary');

                badge.className = "px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest border transition-colors duration-300 bg-background-dark/5 dark:bg-white/10 text-background-dark dark:text-white border-background-dark/10 dark:border-white/20 admin-badge";
                badgeText.innerText = "Restricted";

                desc.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
                desc.classList.add('opacity-100', 'translate-y-0');

                status.classList.add('opacity-0', '-translate-y-4', 'pointer-events-none');
                status.classList.remove('opacity-100', 'translate-y-0');

                loginBtn.className = "w-auto lg:w-full h-12 px-6 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2 group-hover:translate-x-1 lg:group-hover:translate-x-0 lg:group-hover:-translate-y-1 shadow-md group-hover:shadow-lg bg-background-dark dark:bg-white dark:text-background-dark text-white hover:bg-background-dark/90 dark:hover:bg-gray-100 admin-btn";
                btnText.innerText = "Secure Login";
                btnIcon.innerText = "lock";
            }
        }

        // Help Center Tabs
        function switchHelpTab(tab) {
            const btnApplicant = document.querySelector('.tab-applicant');
            const btnBusiness = document.querySelector('.tab-business');
            const faqApplicant = document.getElementById('faq-applicant');
            const faqBusiness = document.getElementById('faq-business');

            if (tab === 'applicant') {
                btnApplicant.className = "px-8 py-4 rounded-2xl font-bold transition-all flex items-center gap-2 bg-primary text-white shadow-lg shadow-primary/30 scale-105 tab-applicant";
                btnBusiness.className = "px-8 py-4 rounded-2xl font-bold transition-all flex items-center gap-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 tab-business";
                faqApplicant.classList.remove('hidden');
                faqBusiness.classList.add('hidden');
            } else {
                btnBusiness.className = "px-8 py-4 rounded-2xl font-bold transition-all flex items-center gap-2 bg-background-dark text-white shadow-lg shadow-background-dark/30 scale-105 tab-business";
                btnApplicant.className = "px-8 py-4 rounded-2xl font-bold transition-all flex items-center gap-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 tab-applicant";
                faqBusiness.classList.remove('hidden');
                faqApplicant.classList.add('hidden');
            }
        }

        // Password Visibility Toggle
        function togglePassword(btn) {
            const input = btn.previousElementSibling;
            const icon = btn.querySelector('span');

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerText = 'visibility_off'; // Show "crossed eye" or "hide" state
                icon.classList.add('text-primary');
            } else {
                input.type = 'password';
                icon.innerText = 'visibility'; // Show "eye" state
                icon.classList.remove('text-primary');
            }
        }

        // Toggle Meter Visibility
        // Toggle Meter Visibility
        function toggleMeter(show, input = null) {
            // Find relative elements
            // Structure: div > label + div.relative(input) + div(bars) + p(text)
            // If input is null (from onfocus check? wait, onfocus='toggleMeter(true)' passes undefined input)
            // Ah, the original call was onfocus="toggleMeter(true)". We need 'this' there too.
            // I need to update the HTML calls first? Or handle event.target?

            // Let's assume input is PASSED or available.
            // But wait, existing calls are: onfocus="toggleMeter(true)" (no input param!)
            // I must update the HTML calls to pass 'this' on focus too: onfocus="toggleMeter(true, this)"
            // I will do that in a separate step or assume I can get it.
            // Actually, I can't easily get 'this' if not passed.

            // NOTE: I will update the HTML to pass 'this' in a subsequent step or this same step if possible.
            // But for now, let's write the function to accept it.

            if (!input) return;

            const wrapper = input.closest('.relative').parentNode;
            const bars = wrapper.querySelector('[id*="strength-bars"]');
            const text = wrapper.querySelector('[id*="strength-text"]');

            if (show) {
                if (bars) bars.classList.remove('hidden');
                if (text) text.classList.remove('hidden');
            } else {
                // On blur, only hide if empty
                if (input.value.length === 0) {
                    if (bars) bars.classList.add('hidden');
                    if (text) text.classList.add('hidden');
                }
            }
        }

        // Password Strength Logic
        function checkPasswordStrength(input) {
            const val = input.value;
            const wrapper = input.closest('.relative').parentNode;
            const bars = wrapper.querySelectorAll('.strength-bar');
            const text = wrapper.querySelector('[id*="strength-text"]');

            let score = 0;
            if (val.length > 5) score++;
            if (val.length > 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            // Max score is 5, but we have 4 bars.
            // Map score to 0-4 range roughly

            // Colors
            const colors = ['bg-gray-200', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
            const darkColors = ['dark:bg-gray-700', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500']; // Tailwind classes apply same color for ease

            // Reset
            bars.forEach(bar => {
                bar.className = 'h-full w-full rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-300 strength-bar';
            });

            if (val.length === 0) {
                text.innerText = 'Weak';
                text.className = 'text-xs mt-1 text-gray-500 font-medium text-right hidden'; // Keep hidden if empty? Or just reset style? Text visibility handled by toggleMeter.
                return;
            }

            let activeBars = 0;
            let label = 'Weak';
            let colorClass = 'bg-red-500';

            if (score <= 1) { activeBars = 1; label = 'Weak'; colorClass = 'bg-red-500'; }
            else if (score === 2) { activeBars = 2; label = 'Fair'; colorClass = 'bg-orange-500'; }
            else if (score === 3) { activeBars = 3; label = 'Good'; colorClass = 'bg-yellow-500'; }
            else if (score >= 4) { activeBars = 4; label = 'Strong'; colorClass = 'bg-green-500'; }

            text.innerText = label;

            // Text Color
            text.className = `text-xs mt-1 font-medium text-right transition-colors ${colorClass.replace('bg-', 'text-')}`;

            // Fill bars
            bars.forEach((bar, index) => {
                if (index < activeBars) {
                    bar.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                    bar.classList.add(colorClass);
                }
            });
        }

        // --- NEW SECTION LOGIC ---
        function initNewsletter() {
            // Confetti
            const triggerConfetti = () => {
                const container = document.getElementById('confetti-container');
                if (!container) return;
                const CONFETTI_COUNT = 150;
                const COLORS = ['#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6', '#8b5cf6'];
                const fragment = document.createDocumentFragment();
                for (let i = 0; i < CONFETTI_COUNT; i++) {
                    const el = document.createElement('div');
                    el.className = 'confetti-piece';
                    el.style.left = `${Math.random() * 100}vw`;
                    el.style.backgroundColor = COLORS[Math.floor(Math.random() * COLORS.length)];
                    el.style.transform = `rotate(${Math.random() * 360}deg)`;
                    el.style.animationDuration = `${3 + Math.random() * 2}s`;
                    el.style.animationDelay = `${Math.random()}s`;
                    fragment.appendChild(el);
                }
                container.appendChild(fragment);
                setTimeout(() => { container.innerHTML = ''; }, 6000);
            };

            // Form Logic
            const form = document.getElementById('subscribe-form');
            if (form) {
                const emailInput = document.getElementById('email-input');
                const emailError = document.getElementById('email-error');
                const emailWrapper = document.getElementById('email-input-wrapper');
                const formContainer = document.getElementById('form-container');
                const successMessage = document.getElementById('success-message');
                const bellContainer = document.getElementById('bell-container');

                const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (validateEmail(emailInput.value)) {
                        formContainer.style.display = 'none';
                        successMessage.style.display = 'flex';
                        if (bellContainer) {
                            bellContainer.classList.add('ringing-active');
                            setTimeout(() => bellContainer.classList.remove('ringing-active'), 1000);
                        }
                        triggerConfetti();
                    } else {
                        emailError.textContent = 'Please enter a valid email address.';
                        emailError.classList.remove('hidden');
                        emailInput.setAttribute('aria-invalid', 'true');
                        emailWrapper.classList.add('border-red-500');
                        emailWrapper.classList.remove('border-slate-100', 'dark:border-slate-800');
                    }
                });

                emailInput.addEventListener('input', () => {
                    if (emailError.textContent) {
                        emailError.classList.add('hidden');
                        emailError.textContent = '';
                        emailInput.setAttribute('aria-invalid', 'false');
                        emailWrapper.classList.remove('border-red-500');
                        emailWrapper.classList.add('border-slate-100', 'dark:border-slate-800');
                    }
                });
            }

            // Stats Count-Up
            const statsSection = document.getElementById('stats-section');
            if (statsSection) {
                const statCards = document.querySelectorAll('.stat-card');
                const animateCountUp = (el, end, duration = 1500) => {
                    let startTime = null;
                    const suffix = el.dataset.suffix || '';
                    const step = (timestamp) => {
                        if (!startTime) startTime = timestamp;
                        const progress = timestamp - startTime;
                        const percentage = Math.min(progress / duration, 1);
                        const currentVal = Math.floor(end * percentage);
                        el.textContent = currentVal + suffix;
                        if (progress < duration) requestAnimationFrame(step);
                    };
                    requestAnimationFrame(step);
                };

                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        statCards.forEach(card => {
                            card.classList.remove('opacity-0', 'translate-y-4');
                            card.classList.add('opacity-100', 'translate-y-0');
                            const valueEl = card.querySelector('.stat-value');
                            const endValue = parseInt(valueEl.dataset.value, 10);
                            if (endValue) animateCountUp(valueEl, endValue);
                        });
                        observer.disconnect();
                    }
                }, { threshold: 0.1 });
                observer.observe(statsSection);
            }
        }

        // Init
        window.addEventListener('hashchange', router);

        function hidePreloader() {
            const preloader = document.getElementById('app-preloader');
            if (preloader && !preloader.classList.contains('opacity-0')) {
                setTimeout(() => {
                    preloader.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => {
                        preloader.remove();
                    }, 500);
                }, 800);
            }
        }

        window.addEventListener('load', () => {
            // Check for server-side validation errors and re-open the correct modal
            @if ($errors->any())
                @if ($errors->has('first_name') || $errors->has('last_name'))
                    window.location.hash = '#register';
                @elseif ($errors->has('email') || $errors->has('password'))
                    // Heuristic: If we don't know for sure, assume login unless it was clearly registration
                    // Since 'first_name' is unique to register, its absence suggests login (or partial register fail)
                    // But we can check specifically if old('first_name') exists to be sure.
                    @if(old('first_name'))
                        window.location.hash = '#register';
                    @else
                        window.location.hash = '#applicant-login';
                    @endif
                @endif
            @endif

            try {
                initTheme();
                router();
                initNewsletter();
                initLiveValidation();
            } catch (e) {
                console.error('Portal Init Error:', e);
            } finally {
                // Wait for icon fonts to load before revealing
                document.fonts.ready.then(() => {
                    hidePreloader();
                });
            }
        });

        // BFCache (Back/Forward Cache) Handler: 
        // Forces the router to re-evaluate auth boundaries if the user clicks "Back" in their browser
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                // Page was restored from cache (User clicked back/forward)
                router();
            }
        });

        // Failsafe: Force remove preloader if load hangs (e.g. 5 seconds)
        setTimeout(() => {
            hidePreloader();
        }, 5000);
    </script>
    <!-- Back to Top Button -->
    <button id="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="fixed bottom-8 right-8 bg-primary hover:bg-red-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 opacity-0 translate-y-10 invisible z-50 group hover:shadow-xl hover:scale-110"
        aria-label="Back to top">
        <span
            class="material-symbols-outlined text-2xl group-hover:-translate-y-1 transition-transform">arrow_upward</span>
    </button>

    <script>
        // Back to Top Logic
        const backToTopBtn = document.getElementById('back-to-top');
        if (backToTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backToTopBtn.classList.remove('opacity-0', 'translate-y-10', 'invisible');
                    backToTopBtn.classList.add('opacity-100', 'translate-y-0', 'visible');
                } else {
                    backToTopBtn.classList.add('opacity-0', 'translate-y-10', 'invisible');
                    backToTopBtn.classList.remove('opacity-100', 'translate-y-0', 'visible');
                }
            });
        }
    </script>
    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true"
        aria-labelledby="modal-title">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="logout-backdrop"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <!-- Modal Panel -->
                <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg scale-95 opacity-0"
                    id="logout-panel">
                    <div class="bg-white dark:bg-gray-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <span class="material-symbols-outlined text-red-600 dark:text-red-400">logout</span>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white"
                                    id="modal-title">Sign Out</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to sign
                                        out? You will need to login again to access your dashboard.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" id="confirm-logout-btn" onclick="approveLogout()"
                            class="inline-flex w-full justify-center rounded-xl bg-red-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">Sign
                            Out</button>
                        <button type="button" onclick="cancelLogout()"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-gray-800 px-3 py-2 text-sm font-bold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto transition-colors">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Global Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        // Logout Modal Logic
        let pendingLogoutForm = null;

        function confirmLogout(event, type = null) {
            if (event) event.preventDefault();

            // If explicit admin type OR no parent form found (e.g. navbar button), use global form
            if (type === 'admin' || !event.target.closest('form')) {
                pendingLogoutForm = document.getElementById('logout-form');
            } else {
                pendingLogoutForm = event.target.closest('form');
            }

            const modal = document.getElementById('logout-modal');
            const backdrop = document.getElementById('logout-backdrop');
            const panel = document.getElementById('logout-panel');

            if (modal) {
                modal.classList.remove('hidden');
                // Animate in
                requestAnimationFrame(() => {
                    backdrop.classList.remove('opacity-0');
                    panel.classList.remove('scale-95', 'opacity-0');
                    panel.classList.add('scale-100', 'opacity-100');
                });
            }
        }

        function cancelLogout() {
            const modal = document.getElementById('logout-modal');
            const backdrop = document.getElementById('logout-backdrop');
            const panel = document.getElementById('logout-panel');

            if (modal) {
                // Animate out
                backdrop.classList.add('opacity-0');
                panel.classList.add('scale-95', 'opacity-0');
                panel.classList.remove('scale-100', 'opacity-100');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    pendingLogoutForm = null;
                }, 300);
            }
        }



        function showToast(message, title = 'Notice') {
            const toast = document.createElement('div');
            toast.className = 'bg-white dark:bg-gray-800 border-l-4 border-primary text-gray-800 dark:text-gray-200 p-4 rounded shadow-lg flex items-center gap-3 mb-3 animate-fade-in-up pointer-events-auto';
            toast.innerHTML = `
                <span class="material-symbols-outlined text-primary">info</span>
                <div class="flex-1">
                    <p class="font-bold">${title}</p>
                    <p class="text-sm">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            `;
            
            const container = document.getElementById('toast-container');
            if (container) {
                container.appendChild(toast);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(-10px)';
                        toast.style.transition = 'all 0.3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 5000);
            }
        }

        function copyProfileLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                showToast('Profile link copied to clipboard!', 'Success');
            }).catch(err => {
                console.error('Failed to copy: ', err);
                showToast('Failed to copy link.', 'Error');
            });
        }

        function approveLogout() {
            console.log('approveLogout called. Form:', pendingLogoutForm);
            if (pendingLogoutForm) {
                // Show loading state on button
                const btn = document.getElementById('confirm-logout-btn');
                if (btn) {
                    btn.innerText = 'Signing out...';
                    btn.disabled = true;
                }
                pendingLogoutForm.submit();
            } else {
                console.error('No pending logout form found!');
                // Fallback: try to find the global form if null
                const fallbackForm = document.getElementById('logout-form');
                if (fallbackForm) {
                    console.log('Using fallback global form');
                    fallbackForm.submit();
                } else {
                    Swal.fire({ icon: 'error', title: 'Sign Out Failed', text: 'Could not sign out. Please refresh and try again.', confirmButtonColor: '#0F1D33' });
                }
            }
        }
    </script>
    <script>
        // Check for 'message' query parameter and show toast
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            
            if (message) {
                // Remove the message parameter from URL without reloading
                const newUrl = window.location.pathname + window.location.hash;
                window.history.replaceState({}, document.title, newUrl);
                
                showToast(decodeURIComponent(message));
            }
        });
    </script>
    <!-- Scripts -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>