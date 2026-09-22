<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        // Force light theme
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    </script>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Kingdom Recruitments — V4</title>
    <meta name="description" content="Kingdom Recruitments — premium recruitment agency connecting talent with opportunity."/>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ time() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&display=block" rel="stylesheet"/>
    <script src="https://unpkg.com/lucide@1.25.0"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--gold:#f2b90d;--dark:#0a0c1a;--light-bg:#f8f9fc;--light-surface:#ffffff;--light-text:#1e293b;--light-muted:#64748b}

        /* ── V4 Navbar / Dropdown Overrides ──
           The shared navbar uses bg-white / dark:bg-surface-dark (#1E293B)
           which is visible against V4's #0a0c1a. Override everything. */

        /* Target dropdown panels by their structural classes */
        nav .absolute.top-full,
        nav [x-show] {
            background: #0a0c1a !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border: 1px solid rgba(242, 185, 13, 0.25) !important;
            border-top: 2px solid #f2b90d !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.8) !important;
        }
        /* Override dropdown link colors */
        nav .absolute.top-full a,
        nav [x-show] a {
            color: #cbd5e1 !important;
        }
        nav .absolute.top-full a:hover,
        nav [x-show] a:hover {
            color: #f2b90d !important;
            background: rgba(242, 185, 13, 0.08) !important;
        }
        /* Override the .glass class for dark mode since app.css sets it to slate */
        .dark .glass {
            background: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid rgba(242, 185, 13, 0.15) !important;
            box-shadow: none !important;
        }
        /* Top contact bar override */
        .bg-white\/95 {
            background: rgba(10, 12, 26, 0.95) !important;
            border-color: rgba(242, 185, 13, 0.1) !important;
        }
        /* Mobile drawer button colors - gold instead of red */
        .bg-primary, .hover\:bg-primary-dark:hover {
            background: #f2b90d !important;
            color: #0a0c1a !important;
        }
        .hover\:border-primary:hover {
            border-color: #f2b90d !important;
        }
        .contact-block {
            background: #f2b90d !important;
            color: #0a0c1a !important;
        }
        /* Nav link hover — gold instead of red */
        .nav-link:hover, .nav-link.text-kingdom-gold {
            color: #f2b90d !important;
        }


        body{overscroll-behavior:none;font-family:'Space Grotesk',sans-serif}
        .v4-text{color:var(--gold)}.v4-bg{background:var(--gold)}.v4-border{border-color:var(--gold)}
        .glass{background:rgba(255,255,255,.04);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(242,185,13,.15)}
        .glass-heavy{background:rgba(10,12,26,.7);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(242,185,13,.25)}
        .gold-glow{box-shadow:0 0 30px rgba(242,185,13,.2),0 0 60px rgba(242,185,13,.06)}
        .text-glow{text-shadow:0 0 20px rgba(242,185,13,.4),0 0 60px rgba(242,185,13,.15)}
        html.lenis,html.lenis body{height:auto}.lenis.lenis-smooth{scroll-behavior:auto!important}
        @keyframes spin-slow{to{transform:rotate(360deg)}}.spin-slow{animation:spin-slow 12s linear infinite}
        @keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}.animate-marquee{animation:marquee 25s linear infinite}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}.animate-float{animation:float 4s ease-in-out infinite}
        @keyframes pulse-ring{0%{transform:scale(.8);opacity:.6}50%{transform:scale(1);opacity:1}100%{transform:scale(.8);opacity:.6}}.pulse-ring{animation:pulse-ring 3s ease-in-out infinite}
        @keyframes gradient-x{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}.animate-gradient{animation:gradient-x 6s ease infinite;background-size:200% auto}
        #canvas-bg{position:fixed;inset:0;z-index:0;pointer-events:none;opacity:.45}
        .icon-blue{color:#60a5fa}.icon-green{color:#4ade80}.icon-purple{color:#a78bfa}.icon-orange{color:#fb923c}.icon-pink{color:#f472b6}.icon-yellow{color:#facc15}.icon-slate{color:#94a3b8}
        .section-label{display:inline-flex;align-items:center;gap:.5rem;padding:.4rem 1.2rem;border-radius:999px;background:rgba(242,185,13,.08);border:1px solid rgba(242,185,13,.3);font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#f2b90d}
        .hover-fill-card{position:relative;overflow:hidden}
        .hover-fill-card .hover-fill{position:absolute;inset:0;background:#f2b90d;z-index:0;pointer-events:none;transition:clip-path 1s cubic-bezier(.4,0,.2,1);clip-path:circle(0% at var(--mx,50%) var(--my,50%))}
        .hover-fill-card:hover .hover-fill{clip-path:circle(200% at var(--mx,50%) var(--my,50%))}
        .card-shine{position:relative;overflow:hidden}.card-shine::after{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:linear-gradient(45deg,transparent 40%,rgba(242,185,13,.06) 50%,transparent 60%);transform:translateX(-100%);transition:transform .8s;z-index:1;pointer-events:none}.card-shine:hover::after{transform:translateX(100%)}
        .border-glow{position:relative}.border-glow::before{content:'';position:absolute;inset:-1px;border-radius:inherit;background:linear-gradient(135deg,rgba(242,185,13,.4),transparent 40%,transparent 60%,rgba(242,185,13,.2));z-index:-1;opacity:0;transition:opacity .5s;pointer-events:none}.border-glow:hover::before{opacity:1}
        .gold-btn{background:#f2b90d;color:#0a0c1a;font-weight:700}
        .gold-btn:hover{box-shadow:0 0 40px rgba(242,185,13,.45)}
        .gold-outline-btn{background:rgba(242,185,13,.08);border:1px solid rgba(242,185,13,.3);color:#f2b90d}
        .gold-outline-btn:hover{background:#f2b90d;color:#0a0c1a}
        .gold-gradient{background:linear-gradient(to right,#f2b90d,#f59e0b)}
        .gold-text-gradient{background:linear-gradient(to right,#f2b90d,#fde68a,#d97706);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

        /* ════ LIGHT MODE OVERRIDES ════ */
        html:not(.dark) body.v4-body{background:var(--light-bg)!important;color:var(--light-text)!important}

        /* Premium Section Background Alternation for Light Mode */
        html:not(.dark) section { background: #f8fafc; border-color: #f1f5f9; }
        html:not(.dark) .search-section { background: #ffffff; }
        html:not(.dark) .categories-section { background: #f8fafc; }
        html:not(.dark) .why-choose-section { background: #ffffff; }
        html:not(.dark) .companies-section { background: #f8fafc; border-color: #e2e8f0; }
        /* Fix the dark gradients explicitly for the companies marquee in light mode */
        html:not(.dark) .companies-section .from-\[\#0a0c1a\] { --tw-gradient-from: #f8fafc; --tw-gradient-stops: var(--tw-gradient-from), transparent; }
        html:not(.dark) .split-section { background: #ffffff; }
        html:not(.dark) .applicants-section { background: #f8fafc; }
        html:not(.dark) .stats-section { background: #0a0c1a !important; color: #fff; } /* Keep stats dark for a cinematic break */
        html:not(.dark) .cta-section { background: #f8fafc; }
        html:not(.dark) .testimonials-section { background: #ffffff; }
        html:not(.dark) .articles-section { background: #f8fafc; }
        html:not(.dark) footer { background: #0a0c1a !important; border-top-color: rgba(255,255,255,0.05); }

        /* Global white text → dark in light mode */
        html:not(.dark) .text-white{color:var(--light-text)!important}
        html:not(.dark) .text-slate-100{color:var(--light-text)!important}
        html:not(.dark) .text-slate-200{color:var(--light-muted)!important}
        html:not(.dark) .text-slate-300{color:var(--light-muted)!important}
        html:not(.dark) .text-slate-400{color:#94a3b8!important}
        html:not(.dark) .text-white\/50{color:var(--light-muted)!important}
        html:not(.dark) .text-white\/70{color:var(--light-text)!important}

        /* Glass panels → white cards */
        html:not(.dark) .glass{background:rgba(255,255,255,.75);border-color:rgba(0,0,0,.08);box-shadow:0 1px 3px rgba(0,0,0,.06)}
        html:not(.dark) .glass-heavy{background:rgba(255,255,255,.9);border-color:rgba(0,0,0,.1);box-shadow:0 4px 20px rgba(0,0,0,.06)}

        /* Section labels */
        html:not(.dark) .section-label{background:rgba(242,185,13,.12);border-color:rgba(242,185,13,.35)}

        /* Canvas dimmed in light mode */
        html:not(.dark) #canvas-bg{opacity:.08}

        /* Outline buttons */
        html:not(.dark) .gold-outline-btn{background:rgba(242,185,13,.08);border-color:rgba(242,185,13,.4);color:#b8860b}

        /* Hero always dark, including the navbar at the top */
        html:not(.dark) #hero-section, html:not(.dark) header#hero-section{background:#0a0c1a!important}
        html:not(.dark) header#hero-section .text-white{color:#fff!important}
        html:not(.dark) header#hero-section .text-slate-200{color:#cbd5e1!important}
        html:not(.dark) header#hero-section .text-slate-300{color:#94a3b8!important}
        
        /* Ensure navbar and contact bar stay dark/glassy on the dark hero in light mode */
        html:not(.dark) .v4-body nav .nav-link{color:#fff!important}
        html:not(.dark) .v4-body nav .nav-link:hover{color:var(--gold)!important}
        html:not(.dark) .v4-body nav.fixed{background:rgba(10,12,26,.95)!important; border-bottom:1px solid rgba(255,255,255,0.05)!important}
        html:not(.dark) .v4-body .bg-white\/95{background:rgba(10,12,26,.5)!important; border-bottom:1px solid rgba(255,255,255,0.05)!important}
        html:not(.dark) .v4-body .text-gray-600{color:#e2e8f0!important}
        
        /* Mobile menu button in light mode over dark hero */
        html:not(.dark) .v4-body .bg-black\/50{background:rgba(255,255,255,0.1)!important; color:#fff!important}
        
        /* Search bar */
        html:not(.dark) .glass input,html:not(.dark) .glass select{color:var(--light-text)!important;background:rgba(0,0,0,.03)!important}
        html:not(.dark) .glass input::placeholder{color:#94a3b8!important}

        /* Company marquee */
        html:not(.dark) .brightness-200{filter:brightness(.4) !important}
        html:not(.dark) .grayscale{filter:grayscale(1) brightness(.4)!important}

        /* Split cards */
        html:not(.dark) .border-glow{background:var(--light-surface);box-shadow:0 4px 24px rgba(0,0,0,.06)}

        /* Stats section: keep dark bg */
        html:not(.dark) section:has(.stat-ring){background:#0a0c1a!important}
        html:not(.dark) section:has(.stat-ring) .text-white{color:#fff!important}
        html:not(.dark) section:has(.stat-ring) .section-label{background:rgba(242,185,13,.08)!important;border-color:rgba(242,185,13,.3)!important}

        /* CTA avatars: light border */
        html:not(.dark) .border-\[3px\]{border-color:var(--light-bg)!important}
        html:not(.dark) .text-white\/80{color:var(--light-text)!important}

        /* Testimonials: visible text */
        html:not(.dark) .testimonial-slide .text-white\/90{color:var(--light-text)!important}
        html:not(.dark) .testimonial-slide .text-white{color:var(--light-text)!important}

        /* Company marquee: readable logos */
        html:not(.dark) .brightness-200{filter:none!important}
        html:not(.dark) .grayscale{filter:grayscale(1)!important}
        html:not(.dark) .text-white\/50{color:#64748b!important}

        /* Search Bar Inputs */
        html:not(.dark) .glass-heavy input, html:not(.dark) .glass-heavy select{background:#fff!important;border-color:rgba(0,0,0,.1)!important;color:#334155!important}
        html:not(.dark) .glass-heavy input::placeholder{color:#94a3b8!important}
        html:not(.dark) .glass-heavy .text-slate-500{color:#64748b!important}

        /* Decorative Background Edge Gradients (causing blue edges) */
        html:not(.dark) .bg-gradient-to-r.from-\[\#0a0c1a\]{background:none!important}
        html:not(.dark) .bg-gradient-to-t.from-\[\#0a0c1a\]{background:none!important}

        /* Gold Text Gradients on Light Backgrounds */
        html:not(.dark) .gold-text-gradient{background:linear-gradient(to right,#b45309,#d97706,#b45309)!important;-webkit-background-clip:text!important;-webkit-text-fill-color:transparent!important;background-clip:text!important}
        html:not(.dark) .text-glow{text-shadow:none!important}
        
        /* Your Path Split Cards (Applicant vs Employer) */
        html:not(.dark) .gs-split .text-white{color:#0f172a!important}
        html:not(.dark) .gs-split .text-slate-400{color:#475569!important}
        html:not(.dark) .gs-split .text-slate-300{color:#64748b!important}
        html:not(.dark) .gs-split .bg-yellow-500\/10{background:rgba(245,158,11,.15)!important}
        html:not(.dark) .gs-split .border-yellow-500\/20{border-color:rgba(245,158,11,.3)!important}
        html:not(.dark) .gs-split .bg-white\/5{background:#f1f5f9!important}
        html:not(.dark) .gs-split .border-white\/10{border-color:#e2e8f0!important}
        
        /* Buttons inside cards */
        html:not(.dark) .gs-split a.glass{background:#0f172a!important;color:#fff!important;border:none!important}
        html:not(.dark) .gs-split a.glass:hover{background:#1e293b!important}
        html:not(.dark) .gs-split a.gold-outline-btn{border-color:#d97706!important;color:#d97706!important}
        html:not(.dark) .gs-split a.gold-outline-btn:hover{background:#d97706!important;color:#fff!important}

        /* Our Advantage Numbers */
        html:not(.dark) .v4-text.opacity-10{color:#cbd5e1!important;opacity:1!important}

        /* Articles border & text */
        html:not(.dark) .border-white\/5{border-color:rgba(0,0,0,.08)!important}
        html:not(.dark) .border-white\/10{border-color:rgba(0,0,0,.1)!important}

        /* Decorative blur orbs: invisible in light mode */
        html:not(.dark) .fixed .blur-\[100px\],
        html:not(.dark) .fixed .blur-\[120px\],
        html:not(.dark) .fixed .blur-\[200px\]{opacity:0!important}
        html:not(.dark) .absolute .blur-\[150px\]{opacity:0!important}
        html:not(.dark) .absolute .blur-\[120px\]{opacity:0!important}
        html:not(.dark) .absolute .blur-3xl{opacity:0!important}

        /* Feature cards text */
        html:not(.dark) .hover-fill-card .text-white{color:var(--light-text)!important}
        html:not(.dark) .hover-fill-card .text-slate-400{color:#64748b!important}
        html:not(.dark) .hover-fill-card:hover .text-white{color:var(--light-text)!important}

        /* Voice of Industry (Stats) section text */
        html:not(.dark) section:has(.stat-ring) .font-black.text-white{color:#0f172a!important}
        html:not(.dark) section:has(.stat-ring) .font-extrabold{color:#0f172a!important}
        html:not(.dark) section:has(.stat-ring) h2.text-white{color:#0f172a!important}
        html:not(.dark) section:has(.stat-ring) p.text-slate-400{color:#475569!important}
        html:not(.dark) section:has(.stat-ring) .text-slate-400{color:#64748b!important}
        html:not(.dark) section:has(.stat-ring){background:#f8fafc!important}
        html:not(.dark) section:has(.stat-ring) .section-label{background:rgba(217,119,6,.1)!important;border-color:rgba(217,119,6,.2)!important;color:#b45309!important}

        /* CTA avatar section text */
        html:not(.dark) section:has(.fa-user) h2.text-white{color:#0f172a!important}
        html:not(.dark) section:has(.fa-user) p.text-slate-400{color:#475569!important}
        html:not(.dark) section:has(.fa-user) p.text-white\/80{color:#475569!important}
        html:not(.dark) section:has(.fa-user) .border-\[\#0a0c1a\]{border-color:#fff!important}

        /* Applicant carousel */
        html:not(.dark) .gs-applicant .text-white{color:#0f172a!important}
        html:not(.dark) .gs-applicant .text-slate-300{color:#475569!important}
        html:not(.dark) .gs-applicant .text-slate-400{color:#64748b!important}
        html:not(.dark) .gs-applicant .bg-white\/5{background:#ffffff!important;box-shadow:0 10px 30px -10px rgba(0,0,0,0.1)!important}
        html:not(.dark) .gs-applicant .border-white\/10{border-color:rgba(0,0,0,.08)!important}

        /* Feature Cards */
        html:not(.dark) .hover-fill-card .text-white{color:#0f172a!important}
        html:not(.dark) .hover-fill-card .text-slate-400{color:#475569!important}

        /* Articles border & text */
        html:not(.dark) .border-white\/5{border-color:rgba(0,0,0,.08)!important}
        html:not(.dark) .border-white\/10{border-color:rgba(0,0,0,.1)!important}

        /* Decorative blur orbs: invisible in light mode */
        html:not(.dark) .fixed .blur-\[100px\],
        html:not(.dark) .fixed .blur-\[120px\],
        html:not(.dark) .fixed .blur-\[200px\]{opacity:0!important}
        html:not(.dark) .absolute .blur-\[150px\]{opacity:0!important}
        html:not(.dark) .absolute .blur-\[120px\]{opacity:0!important}
        html:not(.dark) .absolute .blur-3xl{opacity:0!important}

        /* Feature cards text */
        html:not(.dark) .hover-fill-card .text-white{color:var(--light-text)!important}
        html:not(.dark) .hover-fill-card .text-slate-400{color:#64748b!important}
        html:not(.dark) .hover-fill-card:hover .text-white{color:var(--light-text)!important}

        /* Article cards */
        html:not(.dark) article .text-white{color:var(--light-text)!important}
        html:not(.dark) article .text-slate-500{color:#64748b!important}

        /* Applicant carousel */
        html:not(.dark) .gs-applicant .text-white{color:var(--light-text)!important}
        html:not(.dark) .gs-applicant .text-slate-300{color:#64748b!important}
        html:not(.dark) .gs-applicant .text-slate-400{color:#94a3b8!important}
        html:not(.dark) .gs-applicant .bg-white\/5{background:rgba(0,0,0,.04)!important}
        html:not(.dark) .gs-applicant .border-white\/10{border-color:rgba(0,0,0,.1)!important}

        /* Hardcoded dark BGs in sections */
        html:not(.dark) .bg-gradient-to-br.from-\[\#0a0c1a\]{background:var(--light-bg)!important}
        html:not(.dark) .bg-gradient-to-b{background:transparent!important}

        /* dot pattern bg in stats */
        html:not(.dark) section:has(.stat-ring) .opacity-\[\.04\]{opacity:.04!important}

        /* Footer */
        html:not(.dark) footer{background:#0f1128!important}
        html:not(.dark) footer .text-white{color:#fff!important}
        html:not(.dark) footer .text-slate-300{color:#94a3b8!important}
        html:not(.dark) footer .text-slate-400{color:#64748b!important}
        html:not(.dark) footer .text-white\/50{color:rgba(255,255,255,.5)!important}

        /* Testimonials bg */
        html:not(.dark) .bg-gradient-to-br{background:linear-gradient(to bottom right,#f8f9fc,#eef1f8)!important}

        /* ── Mobile Responsive Overrides ── */
        @media(max-width:767px){
            .section-label{font-size:.6rem;padding:.35rem 1rem}
            .glass-heavy{padding:1.25rem !important}
            #canvas-bg{display:none}
        }
        @media(max-width:480px){
            h1{font-size:2.2rem !important;line-height:1.1 !important}
            h2{font-size:1.6rem !important}
        }
    </style>
</head>
<body class="v4-body bg-[#0a0c1a] dark:bg-[#0a0c1a] bg-[var(--light-bg)] text-slate-800 dark:text-slate-100 selection:bg-yellow-500/30 overflow-x-hidden transition-colors duration-500" style="font-family:'Space Grotesk',sans-serif">

@include('partials.navbar')

<div id="canvas-bg"></div>
<div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
    <div class="absolute top-[5%] left-[3%] w-80 h-80 rounded-full bg-yellow-500/10 blur-[100px] opacity-50"></div>
    <div class="absolute bottom-[10%] right-[5%] w-[30rem] h-[30rem] rounded-full bg-purple-900/10 blur-[120px] opacity-30"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] rounded-full bg-yellow-500/5 blur-[200px] opacity-20"></div>
</div>

<div class="relative z-10 flex min-h-screen flex-col">

{{-- ═══════════════════════ 1. HERO ═══════════════════════ --}}
<header class="relative w-full min-h-[70vh] md:min-h-[90vh] flex flex-col justify-start overflow-hidden pt-28 md:pt-32 pb-20 bg-[#0a0c1a]" id="hero-section">
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/hero_v3.jpg') }}" alt="Recruitment" class="w-full h-full object-cover object-[75%_50%]"/>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0a0c1a] via-[#0a0c1a]/85 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0a0c1a] via-transparent to-transparent"></div>
    </div>
    <div class="absolute top-0 right-0 w-2/5 h-full z-[1] pointer-events-none opacity-30">
        <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
            <polygon fill="#f2b90d" opacity="0.5" points="20,0 100,0 100,100 60,100"></polygon>
            <polygon fill="#b45309" opacity="0.3" points="50,0 100,0 100,60"></polygon>
        </svg>
    </div>
    <div class="absolute top-1/4 right-[15%] w-3 h-3 rounded-full bg-yellow-500/60 animate-float z-[2]"></div>
    <div class="absolute top-1/3 right-[25%] w-2 h-2 rounded-full bg-yellow-500/40 animate-float z-[2]" style="animation-delay:1s"></div>
    <div class="absolute bottom-1/4 right-[20%] w-4 h-4 rounded-full bg-yellow-500/30 animate-float z-[2]" style="animation-delay:2s"></div>
    <div class="relative z-10 container mx-auto px-4 text-white" id="hero-content">
        <div class="max-w-3xl">
            <div class="section-label mb-6 hero-badge" style="opacity:0;transform:translateY(20px)"><i class="fa-solid fa-crown text-xs"></i> Over Two Decades of Excellence</div>
            <h1 class="text-3xl sm:text-5xl md:text-7xl lg:text-8xl font-extrabold leading-[1.08] mb-6 md:mb-8 tracking-tight">
                <span class="hero-h1" style="display:inline-block;opacity:0;transform:translateY(40px)">Bridging</span><br/>
                <span class="gold-text-gradient text-glow animate-gradient hero-gold" style="display:inline-block;opacity:0;transform:translateY(40px)">Talent &amp; Opportunity</span>
            </h1>
            <p class="text-base md:text-xl text-slate-300/90 mb-8 md:mb-12 max-w-xl leading-relaxed hero-desc" style="opacity:0;transform:translateY(30px)">
                Connecting world-class professionals with industry-leading organisations across the UK. Your future starts with a conversation.
            </p>
            <div class="flex flex-wrap gap-4 hero-btns" style="opacity:0;transform:translateY(30px)">
                <a href="{{ route('applicants.upload') }}" class="anime-btn group inline-flex items-center gap-2 sm:gap-3 gold-btn px-5 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-2xl text-sm sm:text-lg gold-glow transition-all duration-500">
                    <i class="fa-solid fa-cloud-arrow-up group-hover:animate-bounce"></i> UPLOAD YOUR CV <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('jobs.index') }}" class="anime-btn inline-flex items-center gap-2 sm:gap-3 glass text-white px-5 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-bold text-sm sm:text-lg hover:border-yellow-500/60 transition-all duration-500">
                    <i class="fa-solid fa-compass"></i> EXPLORE JOBS
                </a>
            </div>
        </div>
    </div>
</header>

{{-- ═══════════════════════ 2. SEARCH BAR ═══════════════════════ --}}
<section class="relative z-30 -mt-10 md:-mt-14 pb-16 px-4 search-section">
    <div class="glass-heavy gold-glow p-4 sm:p-6 md:p-8 rounded-2xl md:rounded-3xl max-w-5xl mx-auto gs-reveal-up border-glow">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
            <div class="md:col-span-4">
                <label class="block text-[11px] font-bold mb-1.5 uppercase tracking-widest" style="color:rgba(242,185,13,.6)">Keywords</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                    <input class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-slate-500 outline-none focus:border-yellow-500/60 transition-all duration-300" placeholder="Job Title or Keyword" type="text"/>
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-[11px] font-bold mb-1.5 uppercase tracking-widest" style="color:rgba(242,185,13,.6)">Location</label>
                <div class="relative">
                    <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                    <input class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-slate-500 outline-none focus:border-yellow-500/60 transition-all duration-300" placeholder="City or Postcode" type="text"/>
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-[11px] font-bold mb-1.5 uppercase tracking-widest" style="color:rgba(242,185,13,.6)">Category</label>
                <select class="w-full px-4 py-3.5 rounded-xl bg-white/5 border border-white/10 text-slate-400 text-sm outline-none appearance-none focus:border-yellow-500/60 transition-all">
                    <option value="" class="bg-[#0a0c1a]">All Categories</option>
                    @foreach($categories as $cat)<option value="{{ $cat['name'] }}" class="bg-[#0a0c1a]">{{ $cat['name'] }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <a href="{{ route('jobs.index') }}" class="anime-btn w-full gold-gradient hover:shadow-[0_0_30px_rgba(242,185,13,.5)] text-[#0a0c1a] font-bold py-3.5 px-4 rounded-xl transition-all flex items-center justify-center gap-2 text-sm">
                    SEARCH <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════ 3. CATEGORIES ═══════════════════════ --}}
<section class="max-w-[1400px] mx-auto px-4 sm:px-6 py-8 md:py-12 mb-4 md:mb-8 categories-section">
    <div class="text-center mb-12 gs-reveal-up">
        <div class="section-label mx-auto mb-5"><i class="fa-solid fa-compass"></i> Explore Realms</div>
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Choose Your <span class="v4-text text-glow">Realm</span></h2>
        <p class="text-slate-400 max-w-xl mx-auto">Explore opportunities across different disciplines and find where you belong.</p>
    </div>
    <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
        @foreach($categories as $cat)
        @php
            $iconMap=['shield'=>'fa-shield-halved','line-chart'=>'fa-chart-line','calendar'=>'fa-calendar-days','users'=>'fa-users','headphones'=>'fa-headphones','utensils'=>'fa-utensils','briefcase'=>'fa-briefcase'];
            $colMap=['text-blue-500'=>'icon-blue','text-green-500'=>'icon-green','text-purple-500'=>'icon-purple','text-orange-500'=>'icon-orange','text-pink-500'=>'icon-pink','text-yellow-500'=>'icon-yellow','text-slate-500'=>'icon-slate'];
            $faIcon=$iconMap[$cat['icon']]??'fa-briefcase'; $colClass=$colMap[$cat['color']]??'icon-slate';
        @endphp
        <a href="{{ route('jobs.index', ['category'=>$cat['name']]) }}"
           class="gs-cat group glass card-shine border-glow p-6 rounded-2xl flex flex-col items-center text-center hover:-translate-y-3 transition-all duration-500 relative overflow-hidden"
           data-tilt data-tilt-max="12" data-tilt-glare="true" data-tilt-max-glare="0.15" data-tilt-perspective="800">
            <div class="absolute -right-6 -bottom-6 opacity-[.04] group-hover:opacity-[.12] transition-opacity duration-700 pointer-events-none">
                <i class="fa-solid {{ $faIcon }} text-[7rem] {{ $colClass }}"></i>
            </div>
            <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 group-hover:border-yellow-500/50 flex items-center justify-center mb-4 transition-all duration-500 group-hover:scale-110 group-hover:bg-yellow-500/10 group-hover:shadow-[0_0_20px_rgba(242,185,13,.15)]">
                <i class="fa-solid {{ $faIcon }} text-2xl {{ $colClass }} group-hover:scale-110 transition-transform duration-500"></i>
            </div>
            <h3 class="text-white font-bold text-sm md:text-base mb-1 group-hover:text-yellow-400 transition-colors duration-300">{{ $cat['name'] }}</h3>
            <p class="text-slate-500 text-xs group-hover:text-slate-400 transition-colors">{{ $cat['jobs'] }} Openings</p>
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════ 4. WHY CHOOSE US ═══════════════════════ --}}
<section class="py-16 md:py-28 relative overflow-hidden px-4 why-choose-section">
    <div class="absolute inset-0 z-0 opacity-[.03]" style="background-image:radial-gradient(#f2b90d 1px,transparent 1px);background-size:40px 40px"></div>
    <div class="container mx-auto relative z-10">
        <div class="text-center mb-20 gs-reveal-up">
            <div class="section-label mx-auto mb-5"><i class="fa-solid fa-trophy"></i> Our Advantage</div>
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 leading-tight">
                Why Choose<br/><span class="gold-text-gradient text-glow">Kingdom Recruitments?</span>
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed max-w-2xl mx-auto">We go beyond simple matchmaking. We build careers and empower businesses with the right talent.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 md:gap-8">
            @foreach([
                ['icon'=>'fa-bullhorn','title'=>'Advertise Job','desc'=>'Kingdom Recruitments is a UK-based job site that specializes in recruitment for the hospitality, leisure, and tourism industries.','num'=>'01'],
                ['icon'=>'fa-person-chalkboard','title'=>'Recruiter Profiles','desc'=>'As Chairman & CEO of Kingdom Recruitments, Mr. Chowdhury has transformed the organization into a high-performing entity.','num'=>'02'],
                ['icon'=>'fa-magnifying-glass-chart','title'=>'Find Dream Job','desc'=>'Finding your dream job can be a challenging but rewarding process. By utilizing Kingdom Recruitments, you can increase your chances.','num'=>'03'],
            ] as $card)
            <div class="gs-feature hover-fill-card group glass-heavy card-shine p-6 sm:p-8 md:p-10 rounded-2xl md:rounded-[2rem] flex flex-col h-full transition-all duration-700 hover:-translate-y-3 hover:shadow-[0_0_40px_rgba(242,185,13,.15)] cursor-pointer"
                 data-tilt data-tilt-max="8" data-tilt-glare="true" data-tilt-max-glare="0.1">
                <div class="hover-fill"></div>
                <div class="absolute top-6 right-8 text-7xl font-black text-white/[.03] group-hover:text-[#0a0c1a]/10 transition-colors duration-700 z-0 pointer-events-none">{{ $card['num'] }}</div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="mb-8 inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 group-hover:bg-white/20 group-hover:border-white/30 transition-all duration-700 group-hover:shadow-lg v4-text">
                        <i class="fa-solid {{ $card['icon'] }} text-2xl group-hover:text-[#0a0c1a] transition-colors duration-700"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white group-hover:text-[#0a0c1a] mb-4 transition-colors duration-700">{{ $card['title'] }}</h3>
                    <p class="text-slate-400 group-hover:text-[#0a0c1a]/70 text-sm leading-relaxed mb-8 flex-grow transition-colors duration-700">{{ $card['desc'] }}</p>
                    <span class="inline-flex items-center v4-text font-bold text-xs tracking-widest uppercase gap-2 group-hover:gap-4 group-hover:bg-[#0a0c1a] group-hover:text-white group-hover:px-4 group-hover:py-2 group-hover:rounded-lg transition-all duration-700 mt-auto pt-4 relative z-20">
                        LEARN MORE <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-1"></i>
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════ 5. TOP COMPANIES MARQUEE ═══════════════════════ --}}
<section class="py-20 border-y border-white/5 relative overflow-hidden companies-section">
    <div class="absolute left-0 top-0 bottom-0 w-24 md:w-48 bg-gradient-to-r from-[#0a0c1a] to-transparent z-10 pointer-events-none"></div>
    <div class="absolute right-0 top-0 bottom-0 w-24 md:w-48 bg-gradient-to-l from-[#0a0c1a] to-transparent z-10 pointer-events-none"></div>
    <div class="container mx-auto px-4 mb-12 text-center relative z-20 gs-reveal-up">
        <div class="section-label mx-auto mb-5"><i class="fa-solid fa-handshake"></i> Trusted Partners</div>
        <h2 class="text-3xl md:text-5xl font-bold text-white tracking-tight">Top <span class="v4-text">Companies</span></h2>
    </div>
    <div class="flex overflow-hidden group select-none">
        <div class="flex animate-marquee whitespace-nowrap min-w-full shrink-0 items-center gap-20 md:gap-36 pr-20 md:pr-36 group-hover:[animation-play-state:paused]">
            @foreach($companies as $company)
            <div class="group/co flex items-center gap-4 hover:scale-110 transition-all duration-300 cursor-pointer opacity-60 hover:opacity-100">
                <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-10 md:h-14 w-auto object-contain brightness-200 grayscale group-hover/co:grayscale-0 transition-all duration-500"/>
                <span class="text-lg md:text-2xl font-bold tracking-tight text-white/50 group-hover/co:text-yellow-400 transition-colors duration-500">{{ $company['name'] }}</span>
            </div>
            <div class="flex items-center justify-center" aria-hidden="true"><div class="w-2 h-2 rotate-45 bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-[0_0_8px_rgba(242,185,13,.5)] rounded-[1px]"></div></div>
            @endforeach
        </div>
        <div class="flex animate-marquee whitespace-nowrap min-w-full shrink-0 items-center gap-20 md:gap-36 pr-20 md:pr-36 group-hover:[animation-play-state:paused]" aria-hidden="true">
            @foreach($companies as $company)
            <div class="group/co flex items-center gap-4 hover:scale-110 transition-all duration-300 cursor-pointer opacity-60 hover:opacity-100">
                <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-10 md:h-14 w-auto object-contain brightness-200 grayscale group-hover/co:grayscale-0 transition-all duration-500"/>
                <span class="text-lg md:text-2xl font-bold tracking-tight text-white/50 group-hover/co:text-yellow-400 transition-colors duration-500">{{ $company['name'] }}</span>
            </div>
            <div class="flex items-center justify-center" aria-hidden="true"><div class="w-2 h-2 rotate-45 bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-[0_0_8px_rgba(242,185,13,.5)] rounded-[1px]"></div></div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════ 6. EMPLOYER vs APPLICANT SPLIT ═══════════════════════ --}}
<section class="py-16 md:py-24 relative overflow-hidden split-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 gs-reveal-up">
            <div class="section-label mx-auto mb-5"><i class="fa-solid fa-route"></i> Your Path</div>
            <h2 class="text-3xl md:text-5xl font-bold text-white">Choose Your <span class="v4-text text-glow">Journey</span></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="gs-split group glass-heavy card-shine rounded-3xl overflow-hidden hover:border-yellow-500/50 transition-all duration-700 hover:-translate-y-2 relative" data-tilt data-tilt-max="5">
                <div class="absolute top-0 left-0 w-full h-1.5 gold-gradient transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-700"></div>
                <div class="absolute -bottom-20 -right-20 w-60 h-60 bg-yellow-500/5 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                <div class="p-6 sm:p-8 lg:p-14 relative z-10 flex flex-col h-full">
                    <div class="w-16 h-16 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center mb-8 group-hover:bg-[#f2b90d] transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(242,185,13,.3)]">
                        <i class="fa-solid fa-briefcase text-2xl v4-text group-hover:text-[#0a0c1a] transition-colors duration-500"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-white mb-3 group-hover:text-yellow-400 transition-colors duration-500">Looking For a Job</h3>
                    <p class="text-slate-400 mb-8 text-sm leading-relaxed">Your next role could be with one of these top leading organizations.</p>
                    <ul class="space-y-4 mb-10 flex-grow">
                        @foreach(['Exclusive Listings','Career Coaching','Confidential Search'] as $item)
                        <li class="flex items-center text-slate-300 text-sm"><div class="w-6 h-6 rounded-full bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center mr-3 shrink-0"><i class="fa-solid fa-check v4-text text-[10px]"></i></div>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('portal') }}#applicant-login" class="anime-btn inline-flex items-center gap-3 gold-outline-btn px-6 py-3.5 rounded-xl text-sm transition-all duration-500 self-start">
                        Apply Now <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="gs-split group glass-heavy card-shine rounded-3xl overflow-hidden hover:border-yellow-500/50 transition-all duration-700 hover:-translate-y-2 relative" data-tilt data-tilt-max="5">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-slate-400 via-white to-slate-300 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-700"></div>
                <div class="absolute -bottom-20 -right-20 w-60 h-60 bg-white/5 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                <div class="p-6 sm:p-8 lg:p-14 relative z-10 flex flex-col h-full">
                    <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-8 group-hover:bg-white transition-all duration-500 group-hover:shadow-[0_0_25px_rgba(255,255,255,.15)]">
                        <i class="fa-solid fa-building text-2xl text-slate-300 group-hover:text-[#0a0c1a] transition-colors duration-500"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-white mb-3">Are You Recruiting?</h3>
                    <p class="text-slate-400 mb-8 text-sm leading-relaxed">Find the perfect applicants for your organization with our premium service.</p>
                    <ul class="space-y-4 mb-10 flex-grow">
                        @foreach(['48h Shortlist Guarantee','AI-Powered Matching','Premium Support'] as $item)
                        <li class="flex items-center text-slate-300 text-sm"><div class="w-6 h-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mr-3 shrink-0"><i class="fa-solid fa-check v4-text text-[10px]"></i></div>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('portal') }}" class="anime-btn inline-flex items-center gap-3 glass text-white px-6 py-3.5 rounded-xl font-bold text-sm hover:bg-white hover:text-[#0a0c1a] hover:border-white transition-all duration-500 self-start">
                        Post a Job <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════ 7. FEATURED APPLICANTS CAROUSEL ═══════════════════════ --}}
<section class="py-16 md:py-28 px-4 relative applicants-section" x-data="{current:0,total:{{ count($applicants) }},next(){this.current=(this.current+1)%this.total},prev(){this.current=(this.current-1+this.total)%this.total}}">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-yellow-500/5 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="container mx-auto relative z-10">
        <div class="text-center mb-16 gs-reveal-up">
            <div class="section-label mb-5"><i class="fa-solid fa-users-rays"></i> Featured Talent</div>
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Featured <span class="v4-text text-glow">Applicants</span></h2>
            <p class="text-slate-400 max-w-2xl mx-auto">Discover top talent ready to make an impact in your organization.</p>
        </div>
        <div class="relative w-full max-w-7xl mx-auto flex flex-col items-center justify-center min-h-[420px] sm:min-h-[520px]">
            <button @click="prev()" class="absolute left-0 sm:left-2 lg:left-6 z-20 w-10 h-10 md:w-14 md:h-14 glass-heavy rounded-full flex items-center justify-center v4-text hover:bg-[#f2b90d] hover:text-[#0a0c1a] hover:scale-110 transition-all duration-300 cursor-pointer gold-glow" type="button"><i class="fa-solid fa-chevron-left text-sm md:text-lg"></i></button>
            <div class="w-full flex-1 flex items-center justify-center relative">
                @foreach($applicants as $index => $applicant)
                <div class="gs-applicant w-full max-w-[1000px] glass-heavy rounded-2xl sm:rounded-3xl overflow-hidden group absolute inset-0 m-auto transition-all duration-600 ease-out" :class="current==={{ $index }}?'opacity-100 scale-100 translate-y-0 relative pointer-events-auto z-10':'opacity-0 scale-95 translate-y-6 pointer-events-none z-0'">
                    <div class="flex flex-col md:flex-row h-auto md:h-[500px]">
                        <div class="w-full md:w-1/2 relative overflow-hidden bg-slate-800">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0a0c1a]/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-10"></div>
                            <div class="w-full h-full bg-cover bg-center bg-no-repeat transition-transform duration-1000 group-hover:scale-110 min-h-[200px] sm:min-h-[320px]" style="background-image:url('{{ $applicant->image }}')" role="img"></div>
                            @if($applicant->available)<div class="absolute top-6 left-6 z-20 bg-black/70 backdrop-blur-lg px-4 py-2 rounded-full border border-green-500/30"><span class="text-green-400 text-xs font-bold tracking-widest flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span> AVAILABLE</span></div>@endif
                        </div>
                        <div class="w-full md:w-1/2 p-5 sm:p-8 md:p-12 flex flex-col justify-between">
                            <div>
                                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-2">{{ $applicant->name }}</h2>
                                <p class="v4-text font-semibold text-sm uppercase tracking-[.2em] mb-6">{{ $applicant->title }}</p>
                                <div class="w-16 h-0.5 bg-gradient-to-r from-[#f2b90d] to-transparent rounded-full mb-6"></div>
                                <p class="text-slate-400 leading-relaxed text-[15px] mb-8">{{ $applicant->bio }}</p>
                                <div class="flex flex-wrap gap-2 mb-8">
                                    @foreach($applicant->skills as $skill)<span class="px-3 py-1.5 bg-white/5 border border-white/10 text-slate-300 rounded-lg text-xs font-bold uppercase tracking-wide hover:border-yellow-500/50 hover:text-yellow-400 hover:bg-yellow-500/5 transition-all duration-300">{{ $skill }}</span>@endforeach
                                    @if(isset($applicant->moreSkillsCount) && $applicant->moreSkillsCount > 0)<span class="px-3 py-1.5 bg-yellow-500/10 border border-yellow-500/20 v4-text text-xs font-bold uppercase tracking-wide rounded-lg">+{{ $applicant->moreSkillsCount }}</span>@endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3 pt-6 border-t border-white/10">
                                <button class="anime-btn flex-1 h-12 gold-gradient hover:shadow-[0_0_25px_rgba(242,185,13,.4)] text-[#0a0c1a] font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2">Request Interview <i class="fa-solid fa-paper-plane"></i></button>
                                <button class="h-12 w-12 flex items-center justify-center rounded-xl glass hover:border-yellow-500/50 hover:bg-yellow-500/10 transition-all"><i class="fa-regular fa-heart text-slate-400 hover:text-yellow-400 transition-colors"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button @click="next()" class="absolute right-0 sm:right-2 lg:right-6 z-20 w-10 h-10 md:w-14 md:h-14 glass-heavy rounded-full flex items-center justify-center v4-text hover:bg-[#f2b90d] hover:text-[#0a0c1a] hover:scale-110 transition-all duration-300 cursor-pointer gold-glow" type="button"><i class="fa-solid fa-chevron-right text-sm md:text-lg"></i></button>
            <div class="flex items-center justify-center gap-3 mt-10">
                @foreach($applicants as $index => $applicant)<button @click="current={{ $index }}" class="transition-all duration-300" :class="current==={{ $index }}?'w-8 h-2.5 bg-[#f2b90d] rounded-full':'w-2.5 h-2.5 bg-slate-600 hover:bg-yellow-500/50 rounded-full'" type="button"></button>@endforeach
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════ 8. STATS COUNTERS ═══════════════════════ --}}
<section class="py-16 md:py-28 relative overflow-hidden stats-section">
    <div class="absolute inset-0 bg-gradient-to-br from-[#0a0c1a] via-[#0f1228] to-[#0a0c1a] z-0"></div>
    <div class="absolute inset-0 z-0 opacity-[.04]" style="background-image:radial-gradient(#f2b90d 1px,transparent 1px);background-size:30px 30px"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16 gs-reveal-up">
            <div class="section-label mx-auto mb-5"><i class="fa-solid fa-chart-pie"></i> Our Impact</div>
            <h2 class="text-3xl md:text-4xl font-bold text-white">Numbers That <span class="v4-text">Speak</span></h2>
        </div>
        <div class="flex flex-wrap justify-center gap-6 sm:gap-10 md:gap-16 max-w-5xl mx-auto">
            @foreach([['val'=>'1225','label'=>'Jobs Posted','icon'=>'fa-briefcase','color'=>'#f2b90d'],['val'=>'145','label'=>'Jobs Filled','icon'=>'fa-folder-open','color'=>'#60a5fa'],['val'=>'170','label'=>'Companies','icon'=>'fa-building','color'=>'#4ade80'],['val'=>'125','label'=>'Members','icon'=>'fa-users','color'=>'#a78bfa']] as $stat)
            <div class="gs-stat relative group text-center">
                <div class="relative w-32 h-32 sm:w-44 sm:h-44 md:w-52 md:h-52 flex justify-center items-center">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120"><circle cx="60" cy="60" r="54" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="3"/><circle class="stat-ring" cx="60" cy="60" r="54" fill="none" stroke="{{ $stat['color'] }}" stroke-width="3" stroke-linecap="round" stroke-dasharray="339.29" stroke-dashoffset="339.29"/></svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <i class="fa-solid {{ $stat['icon'] }} text-xl mb-2" style="color:{{ $stat['color'] }}"></i>
                        <div class="text-2xl sm:text-3xl md:text-4xl font-black text-white count-up" data-target="{{ $stat['val'] }}">0</div>
                        <div class="text-xs font-bold uppercase tracking-widest mt-1" style="color:{{ $stat['color'] }}">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════ 9. JOBS CTA ═══════════════════════ --}}
<section class="relative flex min-h-[50vh] md:min-h-[65vh] flex-col items-center justify-center overflow-hidden px-4 py-16 md:py-24 cta-section">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-yellow-500/[.03] to-transparent pointer-events-none"></div>
    <div class="relative z-10 flex max-w-4xl flex-col items-center text-center gs-reveal-up">
        <div class="section-label mb-8"><i class="fa-solid fa-sparkles"></i> Discover</div>
        <h2 class="mb-6 md:mb-8 text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-extrabold leading-[1.08] tracking-tight text-white">Jobs You May<br/>Be <span class="gold-text-gradient text-glow animate-gradient">Interested In</span></h2>
        <p class="mb-8 md:mb-12 max-w-2xl text-base sm:text-lg leading-relaxed text-slate-400">A stable career is out there. We will help you get it!</p>
        <div class="relative flex flex-col items-center">
            <div class="flex -space-x-3">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full border-[3px] border-[#0a0c1a] shadow-lg flex items-center justify-center" style="background:#f59e0b"><i class="fa-solid fa-user text-white text-xs sm:text-base"></i></div>
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full border-[3px] border-[#0a0c1a] shadow-lg flex items-center justify-center" style="background:#60a5fa"><i class="fa-solid fa-user text-white text-xs sm:text-base"></i></div>
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full border-[3px] border-[#0a0c1a] shadow-lg flex items-center justify-center" style="background:#4ade80"><i class="fa-solid fa-user text-white text-xs sm:text-base"></i></div>
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full border-[3px] border-[#0a0c1a] shadow-lg flex items-center justify-center" style="background:#a78bfa"><i class="fa-solid fa-user text-white text-xs sm:text-base"></i></div>
                <div class="flex w-10 h-10 sm:w-14 sm:h-14 items-center justify-center rounded-full border-[3px] border-[#0a0c1a] gold-gradient text-xs sm:text-sm font-black text-[#0a0c1a] shadow-lg">+2k</div>
            </div>
            <p class="mt-5 text-lg font-medium text-white/80">Joined the Kingdom this month</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="anime-btn mt-8 md:mt-12 inline-flex items-center gap-3 gold-btn px-6 sm:px-10 py-3 sm:py-4 rounded-xl sm:rounded-2xl text-sm sm:text-lg gold-glow hover:shadow-[0_0_50px_rgba(242,185,13,.5)] transition-all duration-500">Browse All Jobs <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</section>

{{-- ═══════════════════════ 10. TESTIMONIALS ═══════════════════════ --}}
<section class="py-16 md:py-24 px-4 sm:px-6 relative overflow-hidden testimonials-section">
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/5 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-purple-900/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="flex flex-col md:flex-row items-end justify-between mb-14 gap-6 gs-reveal-up">
            <div>
                <div class="section-label mb-5"><i class="fa-solid fa-quote-left"></i> Endorsements</div>
                <h2 class="text-3xl md:text-5xl font-bold text-white tracking-tight">Voice of the <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-300 to-slate-500">Industry</span></h2>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="prevTestimonial()" class="w-12 h-12 rounded-full glass-heavy flex items-center justify-center text-slate-400 hover:bg-[#f2b90d] hover:text-[#0a0c1a] transition-all duration-300 cursor-pointer"><i class="fa-solid fa-chevron-left"></i></button>
                <button onclick="nextTestimonial()" class="w-12 h-12 rounded-full glass-heavy flex items-center justify-center text-slate-400 hover:bg-[#f2b90d] hover:text-[#0a0c1a] transition-all duration-300 cursor-pointer"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
        <div class="relative glass-heavy rounded-2xl sm:rounded-3xl p-5 sm:p-8 md:p-12 grid grid-cols-1 items-center overflow-hidden">
            <div class="absolute top-8 left-10 md:top-10 md:left-14 opacity-[.06] pointer-events-none z-0"><span class="font-serif text-[12rem] leading-none v4-text">"</span></div>
            <div class="contents">
                @foreach($testimonials as $index => $t)
                <div class="testimonial-slide col-start-1 row-start-1 w-full transition-all duration-600 ease-out opacity-0 translate-y-6 pointer-events-none z-0" data-index="{{ $index }}">
                    <div class="grid md:grid-cols-12 gap-8 md:gap-14 items-center relative">
                        <div class="md:col-span-8 space-y-8">
                            <p class="text-base sm:text-xl md:text-2xl lg:text-3xl font-light leading-relaxed text-white/90 relative z-10 italic">{{ $t['quote'] }}</p>
                            <div class="flex flex-col gap-1 relative z-10">
                                <h4 class="text-xl font-bold text-white">{{ $t['author'] }}</h4>
                                <div class="flex items-center gap-3 text-sm text-slate-400"><span>{{ $t['role'] }}</span><span class="w-1.5 h-1.5 rounded-full" style="background:#f2b90d"></span><span class="v4-text font-medium">{{ $t['company'] }}</span></div>
                            </div>
                        </div>
                        <div class="md:col-span-4 flex justify-center md:justify-end">
                            <div class="relative w-28 h-28 sm:w-40 sm:h-40 md:w-52 md:h-52">
                                <div class="absolute inset-0 rounded-full spin-slow" style="border:1px solid rgba(242,185,13,.15)"></div>
                                <div class="absolute inset-3 rounded-full spin-slow" style="border:1px solid rgba(255,255,255,.05);animation-direction:reverse;animation-duration:18s"></div>
                                <div class="absolute inset-5 rounded-full overflow-hidden grayscale hover:grayscale-0 transition-all duration-700" style="border:2px solid rgba(242,185,13,.2)"><img src="{{ $t['image'] }}" alt="{{ $t['author'] }}" class="w-full h-full object-cover"/></div>
                                <div class="absolute -top-1 left-1/2 w-2.5 h-2.5 rounded-full shadow-[0_0_12px_rgba(242,185,13,.8)] pulse-ring" style="background:#f2b90d"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div id="testimonial-progress" class="absolute bottom-0 left-0 h-1 gold-gradient transition-all duration-500 ease-linear z-30 rounded-full" style="width:0%"></div>
        </div>
    </div>
</section>

{{-- ═══════════════════════ 11. ARTICLES ═══════════════════════ --}}
<section class="py-24 border-t border-white/5 px-4 articles-section">
    <div class="container mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end mb-14 gap-6 gs-reveal-up">
            <div class="max-w-2xl">
                <div class="section-label mb-5"><i class="fa-solid fa-newspaper"></i> Latest</div>
                <h2 class="text-white text-3xl md:text-5xl font-bold">News, Tips &amp; <span class="v4-text">Articles</span></h2>
            </div>
            <button class="hidden md:flex items-center gap-2 v4-text font-bold hover:text-yellow-300 transition-colors text-sm tracking-wide">View all articles <i class="fa-solid fa-arrow-right"></i></button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($articles as $article)
            <article class="gs-article group cursor-pointer flex flex-col h-full glass card-shine border-glow rounded-2xl overflow-hidden hover:-translate-y-3 transition-all duration-500">
                <div class="relative overflow-hidden aspect-[4/3] bg-slate-800">
                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-1000" loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0a0c1a]/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute top-4 left-4"><span class="gold-gradient px-3 py-1.5 rounded-lg text-xs font-bold text-[#0a0c1a] uppercase tracking-wide">{{ $article['category'] }}</span></div>
                </div>
                <div class="flex-1 flex flex-col p-7">
                    <div class="flex items-center gap-4 text-xs text-slate-500 mb-4 font-medium"><span><i class="fa-regular fa-clock mr-1"></i>{{ $article['readTime'] }}</span><span class="w-1 h-1 bg-slate-600 rounded-full"></span><span>{{ $article['date'] }}</span></div>
                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-yellow-400 transition-colors duration-300 line-clamp-2">{{ $article['title'] }}</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-5 line-clamp-2">{{ $article['excerpt'] }}</p>
                    <div class="mt-auto pt-4 border-t border-white/5"><span class="inline-flex items-center v4-text font-bold text-sm gap-2 group-hover:gap-3 transition-all duration-300">Read More <i class="fa-solid fa-arrow-right text-xs"></i></span></div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════ FOOTER ═══════════════════════ --}}
<footer class="px-4 py-16 border-t border-white/5 glass">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
        <div class="sm:col-span-2 md:col-span-1">
            <div class="flex items-center gap-3 mb-6"><div class="p-2 gold-gradient rounded-xl shadow-[0_0_15px_rgba(242,185,13,.3)]"><i class="fa-solid fa-crown text-[#0a0c1a] text-lg"></i></div><span class="text-lg font-bold tracking-tight text-white">KINGDOM</span></div>
            <p class="text-slate-400 text-sm leading-relaxed mb-6">Redefining elite talent acquisition through technology and tradition.</p>
            <div class="flex gap-3">@foreach(['twitter'=>'fa-twitter','linkedin'=>'fa-linkedin','instagram'=>'fa-instagram','facebook'=>'fa-facebook'] as $name => $icon)<a href="#" class="w-10 h-10 rounded-xl glass flex items-center justify-center text-slate-400 hover:text-yellow-400 hover:border-yellow-500/50 transition-all duration-300 text-sm"><i class="fa-brands {{ $icon }}"></i></a>@endforeach</div>
        </div>
        @foreach([['Platform',[['How it works','#'],['Pricing','#'],['Partner Program','#']]],['Company',[['About Us',route('about')],['The Hub','#'],['Careers','#']]],['Legal',[['Privacy Policy','#'],['Terms of Service','#'],['Cookie Policy','#']]]] as [$heading,$links])
        <div><h4 class="text-white font-bold mb-6 text-sm uppercase tracking-widest">{{ $heading }}</h4><ul class="space-y-3 text-sm text-slate-400">@foreach($links as [$label,$href])<li><a href="{{ $href }}" class="hover:text-yellow-400 hover:translate-x-1 transition-all duration-300 inline-block">{{ $label }}</a></li>@endforeach</ul></div>
        @endforeach
    </div>
    <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-white/5 flex flex-wrap justify-between items-center text-xs text-slate-600 uppercase tracking-widest gap-4">
        <p>&copy; {{ date('Y') }} Kingdom Recruitments. All rights reserved.</p>
        <p>Built with <i class="fa-solid fa-heart v4-text"></i> for elite talent</p>
    </div>
</footer>

</div>{{-- /z-10 wrapper --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://unpkg.com/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
<script>
const lenis=new Lenis({duration:1.2,easing:t=>Math.min(1,1.001-Math.pow(2,-10*t)),smooth:true});(function raf(t){lenis.raf(t);requestAnimationFrame(raf)})(0);
(function(){const c=document.getElementById('canvas-bg'),s=new THREE.Scene(),cam=new THREE.PerspectiveCamera(75,innerWidth/innerHeight,.1,1000),r=new THREE.WebGLRenderer({alpha:true,antialias:true});r.setSize(innerWidth,innerHeight);r.setPixelRatio(Math.min(devicePixelRatio,2));c.appendChild(r.domElement);const n=900,p=new Float32Array(n*3);for(let i=0;i<n*3;i++)p[i]=(Math.random()-.5)*25;const g=new THREE.BufferGeometry();g.setAttribute('position',new THREE.BufferAttribute(p,3));const m=new THREE.PointsMaterial({size:.02,color:0xf2b90d,transparent:true,opacity:.7});const pts=new THREE.Points(g,m);s.add(pts);cam.position.z=2;let mx=0,my=0;document.addEventListener('mousemove',e=>{mx=(e.clientX/innerWidth-.5)*2;my=(e.clientY/innerHeight-.5)*2});(function a(){const t=performance.now()*.001;pts.rotation.y=t*.03;pts.rotation.x+=.03*(my*.5-pts.rotation.x);pts.rotation.y+=.03*(mx*.5-pts.rotation.y);r.render(s,cam);requestAnimationFrame(a)})();window.addEventListener('resize',()=>{cam.aspect=innerWidth/innerHeight;cam.updateProjectionMatrix();r.setSize(innerWidth,innerHeight)})})();
gsap.registerPlugin(ScrollTrigger);
gsap.from('.gs-reveal-down',{y:-50,autoAlpha:0,duration:1,ease:'power3.out',delay:.1});
gsap.utils.toArray('.gs-reveal-up').forEach((el,i)=>{gsap.from(el,{y:50,autoAlpha:0,duration:.9,delay:.3+i*.1,ease:'power3.out'})});

/* ── Hero entrance timeline ── */
(function(){
    const tl=gsap.timeline({defaults:{ease:'power4.out'}});
    
    // Reset any inline text split interference and setup smooth Y entry
    gsap.set(['.hero-h1', '.hero-gold'], {opacity: 0, y: 40});

    tl.to('.hero-badge',{opacity:1,y:0,duration:.8,delay:.2})
      .to('.hero-h1', {opacity:1, y:0, duration:1.2, ease:'back.out(1.5)'}, '-=.4')
      .to('.hero-gold', {opacity:1, y:0, duration:1.4, ease:'back.out(1.5)'}, '-=.8')
      .to('.hero-desc',{opacity:1,y:0,duration:.8},'-=.6')
      .to('.hero-btns',{opacity:1,y:0,duration:.8,ease:'back.out(1.7)'},'-=.6');
      
    /* Subtle glow pulse on the gold text */
    gsap.to('.hero-gold',{textShadow:'0 0 40px rgba(242,185,13,.6), 0 0 80px rgba(242,185,13,.2)',duration:2,repeat:-1,yoyo:true,ease:'sine.inOut',delay:2});
})();

/* ── Hero scroll parallax ── */
gsap.to('#hero-content',{y:-80,ease:'none',scrollTrigger:{trigger:'#hero-section',start:'top top',end:'bottom top',scrub:true}});
gsap.to('#hero-section img',{scale:1.15,ease:'none',scrollTrigger:{trigger:'#hero-section',start:'top top',end:'bottom top',scrub:true}});

/* ── Section heading reveals (alternating left/right) ── */
gsap.utils.toArray('section').forEach((sec,i)=>{
    const h=sec.querySelector('h2');if(!h)return;
    const fromX=i%2===0?-60:60;
    gsap.from(h,{x:fromX,autoAlpha:0,duration:1,ease:'power3.out',scrollTrigger:{trigger:h,start:'top 88%',once:true}});
    const sub=sec.querySelector('.section-label');
    if(sub)gsap.from(sub,{x:fromX*.5,autoAlpha:0,duration:.8,ease:'power3.out',scrollTrigger:{trigger:sub,start:'top 90%',once:true}});
    const p=h.parentElement?.querySelector('p');
    if(p){
        if(p.children.length===0 && p.innerText.trim().length>0){
            const text=p.innerText.trim();
            p.innerHTML='';
            text.split(' ').forEach(word=>{
                const out=document.createElement('span');
                out.style.cssText='display:inline-block;overflow:hidden;padding-right:0.3em;vertical-align:bottom;';
                const i=document.createElement('span');
                i.innerText=word;
                i.style.cssText='display:inline-block;transform:translateY(100%);opacity:0;';
                out.appendChild(i);
                p.appendChild(out);
            });
            gsap.to(p.querySelectorAll('span > span'),{y:'0%',opacity:1,duration:0.8,stagger:0.02,ease:'back.out(1.5)',delay:0.2,scrollTrigger:{trigger:p,start:'top 90%',once:true}});
        }else{
            gsap.from(p,{y:30,autoAlpha:0,duration:.8,delay:.15,ease:'power2.out',scrollTrigger:{trigger:p,start:'top 90%',once:true}});
        }
    }
});

/* ── Category cards: staggered scale + fade ── */
gsap.utils.toArray('.gs-cat').forEach((el,i)=>{
    gsap.from(el,{scale:.85,autoAlpha:0,y:30,duration:.7,delay:i*.08,ease:'back.out(1.5)',scrollTrigger:{trigger:el.closest('section')||el,start:'top 85%',once:true}});
});

/* ── Feature cards: slide up + rotate slightly ── */
gsap.utils.toArray('.gs-feature').forEach((el,i)=>{
    gsap.from(el,{y:60,rotateX:8,autoAlpha:0,duration:.9,delay:i*.15,ease:'power3.out',scrollTrigger:{trigger:el.closest('section')||el,start:'top 85%',once:true}});
});

/* ── Split cards: slide in from left & right ── */
gsap.utils.toArray('.gs-split').forEach((el,i)=>{
    gsap.from(el,{x:i%2===0?-80:80,autoAlpha:0,duration:1,ease:'power3.out',scrollTrigger:{trigger:el,start:'top 85%',once:true}});
});

/* ── Applicant carousel cards ── */
gsap.utils.toArray('.gs-applicant').forEach((el,i)=>{
    gsap.from(el,{scale:.9,autoAlpha:0,duration:1,ease:'power2.out',scrollTrigger:{trigger:el.closest('section')||el,start:'top 80%',once:true}});
});

/* ── Stats: scale + rotate in ── */
gsap.utils.toArray('.gs-stat').forEach((el,i)=>{
    gsap.from(el,{scale:.6,rotation:-15,autoAlpha:0,duration:.8,delay:i*.12,ease:'back.out(2)',scrollTrigger:{trigger:el.closest('section')||el,start:'top 85%',once:true}});
});

/* ── Article cards: staggered slide up ── */
gsap.utils.toArray('.gs-article').forEach((el,i)=>{
    gsap.from(el,{y:50,autoAlpha:0,duration:.7,delay:i*.1,ease:'power2.out',scrollTrigger:{trigger:el.closest('section')||el,start:'top 85%',once:true}});
});

/* ── Company marquee: scale + fade on scroll ── */
(function(){
    const mq=document.querySelector('.animate-marquee');
    if(mq){
        const sec=mq.closest('section');
        gsap.from(sec,{autoAlpha:0,y:40,duration:1,ease:'power2.out',scrollTrigger:{trigger:sec,start:'top 90%',once:true}});
    }
})();

/* ── CTA section: scrubbed zoom ── */
(function(){
    const cta=document.querySelector('section:has(.gold-text-gradient.animate-gradient):not(header)');
    if(cta){
        const h2=cta.querySelector('h2');
        if(h2){
            ScrollTrigger.create({trigger:cta,start:'top 85%',once:true,onEnter:()=>{gsap.fromTo(h2,{scale:.9,opacity:0},{scale:1,opacity:1,duration:.8,ease:'power2.out'})}});
        }
        ScrollTrigger.create({trigger:cta,start:'top 75%',once:true,onEnter:()=>{gsap.fromTo(cta.querySelectorAll('.flex.-space-x-3 > div'),{scale:0,opacity:0},{scale:1,opacity:1,stagger:.08,duration:.5,ease:'back.out(2.5)'})}});
    }
})();

/* ── Decorative blur orbs: parallax ── */
gsap.utils.toArray('section > .absolute.rounded-full[class*="blur"]').forEach(orb=>{
    gsap.to(orb,{y:-60,ease:'none',scrollTrigger:{trigger:orb.closest('section'),start:'top bottom',end:'bottom top',scrub:true}});
});

/* ── Footer items stagger ── */
(function(){
    const footer=document.querySelector('footer');
    if(footer){
        const items=footer.querySelectorAll('footer > div > div > div');
        gsap.from(items,{y:40,autoAlpha:0,stagger:.1,duration:.7,ease:'power2.out',scrollTrigger:{trigger:footer,start:'top 90%',once:true}});
    }
})();

/* ── Count-up & stat rings ── */
gsap.utils.toArray('.count-up').forEach(el=>{gsap.to(el,{innerHTML:el.dataset.target,duration:2.5,snap:{innerHTML:1},ease:'power2.out',scrollTrigger:{trigger:el,start:'top 95%',once:true}})});
gsap.utils.toArray('.stat-ring').forEach(ring=>{gsap.to(ring,{strokeDashoffset:80,duration:2.5,ease:'power2.out',scrollTrigger:{trigger:ring,start:'top 90%',once:true}})});
VanillaTilt.init(document.querySelectorAll('[data-tilt]'),{max:10,speed:400,glare:true,'max-glare':.12,perspective:1000});
document.querySelectorAll('.anime-btn').forEach(b=>{b.addEventListener('mouseenter',()=>anime({targets:b,scale:1.05,duration:500,easing:'easeOutElastic(1,.6)'}));b.addEventListener('mouseleave',()=>anime({targets:b,scale:1,duration:400,easing:'easeOutElastic(1,.6)'}))});
document.querySelectorAll('.hover-fill-card').forEach(card=>{card.addEventListener('mousemove',e=>{const r=card.getBoundingClientRect();card.style.setProperty('--mx',`${e.clientX-r.left}px`);card.style.setProperty('--my',`${e.clientY-r.top}px`)});card.addEventListener('mouseenter',e=>{const r=card.getBoundingClientRect();card.style.setProperty('--mx',`${e.clientX-r.left}px`);card.style.setProperty('--my',`${e.clientY-r.top}px`);const f=card.querySelector('.hover-fill');if(f)f.style.clipPath='circle(200% at var(--mx,50%) var(--my,50%))'});card.addEventListener('mouseleave',()=>{const f=card.querySelector('.hover-fill');if(f)f.style.clipPath='circle(0% at var(--mx,50%) var(--my,50%))'})});
(function(){let ci=0;const slides=document.querySelectorAll('.testimonial-slide'),total=slides.length,bar=document.getElementById('testimonial-progress');let interval,anim=false;function show(i){if(anim||!total)return;anim=true;slides.forEach(s=>{s.classList.remove('opacity-100','translate-y-0','z-20','pointer-events-auto');s.classList.add('opacity-0','translate-y-6','z-0','pointer-events-none')});const a=slides[i];if(a){a.classList.remove('opacity-0','translate-y-6','z-0','pointer-events-none');a.classList.add('opacity-100','translate-y-0','z-20','pointer-events-auto')}if(bar)bar.style.width=((i+1)/total*100)+'%';setTimeout(()=>anim=false,600)}window.nextTestimonial=function(){ci=(ci+1)%total;show(ci);reset()};window.prevTestimonial=function(){ci=(ci-1+total)%total;show(ci);reset()};function start(){interval=setInterval(window.nextTestimonial,6000)}function reset(){clearInterval(interval);start()}if(total>0){show(0);start()}})();

</script>
</body>
</html>
