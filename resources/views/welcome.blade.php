<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Primary Meta Tags -->
    <title>ResultMaker | Advanced School Result Management System</title>
    <meta name="title" content="ResultMaker | Advanced School Result Management System">
    <meta name="description" content="ResultMaker is a powerful, dynamic result management system for schools. Automate report cards, grade processing, and OCR-based mark entry effortlessly.">
    
    <!-- Comprehensive Keywords -->
    <meta name="keywords" content="school result management, automated report cards, student marks entry, OCR grading, school software, teacher dashboard, education technology, result maker, marksheet generator, school management system, SMS integration for schools, student grade calculator, academic performance tracking, automated grading software, digital report cards, exam management system, class grading, student analytics, education software Bangladesh, school portal, teacher portal, results processing software, dynamic marksheet, MS word marksheet template">
    
    <!-- Authorship and Core Tags -->
    <meta name="author" content="ResultMaker">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    <meta name="rating" content="General">
    <meta name="distribution" content="Global">
    <meta name="google-site-verification" content="SJINiNxzTbNgBluEkns0aTJ2o3CSOtjv7q19eXNW-aY" />
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Mobile / Web App / PWA Tags -->
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ResultMaker">
    <meta name="application-name" content="ResultMaker">
    <meta name="msapplication-TileColor" content="#4f46e5">

    <!-- Open Graph / Facebook / LinkedIn -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="ResultMaker | Advanced School Result Management System">
    <meta property="og:description" content="ResultMaker is a powerful, dynamic result management system for schools. Automate report cards, grade processing, and OCR-based mark entry effortlessly.">
    <meta property="og:site_name" content="ResultMaker">
    <meta property="og:locale" content="en_US">
    <!-- Note: Replace og:image content with an actual URL to your logo/banner later -->
    <meta property="og:image" content="{{ url('/images/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="ResultMaker Dashboard Preview">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="ResultMaker | Advanced School Result Management System">
    <meta property="twitter:description" content="ResultMaker is a powerful, dynamic result management system for schools. Automate report cards, grade processing, and OCR-based mark entry effortlessly.">
    <!-- Note: Replace twitter:image content with an actual URL to your logo/banner later -->
    <meta property="twitter:image" content="{{ url('/images/og-image.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN for simplicity, though local build is available) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js for mobile menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .glass-panel {
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .text-gradient {
            background: linear-gradient(to right, #e2e8f0, #d4c5a9, #f5f0e8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .mesh-bg {
            background-color: #0a0a0a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(80, 60, 30, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(50, 40, 20, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 25, 15, 0.2) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(60, 50, 25, 0.15) 0px, transparent 50%);
        }
    </style>
</head>
<body class="antialiased text-slate-300 mesh-bg min-h-screen selection:bg-white/20 selection:text-white">
    
    <header>
    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-panel border-b-0 border-white/5 bg-slate-950/40" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3 group cursor-pointer">
                    <img src="{{ asset('large.png') }}" alt="ResultMaker" class="h-9 w-auto invert opacity-90 group-hover:opacity-100 transition-opacity">
                </div>
                
                <div class="hidden md:flex items-center space-x-8 text-sm font-medium">
                    <a href="#features" class="text-slate-300 hover:text-white transition-colors duration-200">Features</a>
                    <a href="#how-it-works" class="text-slate-300 hover:text-white transition-colors duration-200">How it Works</a>
                    
                    <div class="flex items-center space-x-4 pl-4 border-l border-white/10">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-white hover:text-stone-300 transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-300 hover:text-white transition-colors">Log in</a>
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white border border-white/10 hover:border-white/20 transition-all duration-300 shadow-[0_0_15px_rgba(255,255,255,0.05)] hover:shadow-[0_0_20px_rgba(255,255,255,0.15)]">
                                Get Started
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile hamburger button -->
                <button @click="mobileOpen = !mobileOpen" aria-label="Toggle mobile menu" class="md:hidden text-slate-300 hover:text-white transition-colors p-2">
                    <svg aria-hidden="true" x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg aria-hidden="true" x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <div x-show="mobileOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden pb-4 pt-2 border-t border-white/10 mt-2">
                <div class="flex flex-col space-y-2">
                    <a href="#features" @click="mobileOpen = false" class="text-slate-300 hover:text-white transition-colors px-2 py-2 rounded-lg hover:bg-white/5 text-sm">Features</a>
                    <a href="#how-it-works" @click="mobileOpen = false" class="text-slate-300 hover:text-white transition-colors px-2 py-2 rounded-lg hover:bg-white/5 text-sm">How it Works</a>
                    <div class="border-t border-white/10 pt-2 mt-2 flex flex-col space-y-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-white hover:text-indigo-300 transition-colors px-2 py-2 text-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-300 hover:text-white transition-colors px-2 py-2 text-sm">Log in</a>
                            <a href="{{ route('register') }}" class="text-center px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white border border-white/10 hover:border-white/20 transition-all duration-300 text-sm font-medium">
                                Get Started
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>
    </header>

    <main>
    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 sm:pt-40 sm:pb-24 lg:pb-32 overflow-hidden">
        <!-- Animated Background Orbs -->
        <div class="absolute top-0 -left-4 w-96 h-96 bg-stone-600 rounded-full mix-blend-screen filter blur-[160px] opacity-10 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-96 h-96 bg-stone-400 rounded-full mix-blend-screen filter blur-[160px] opacity-10 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-neutral-600 rounded-full mix-blend-screen filter blur-[128px] opacity-10 animate-blob animation-delay-4000"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-stone-300 text-sm font-medium mb-8 animate-fade-in-up">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-stone-300 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-stone-200"></span>
                </span>
                ResultMaker 2.0 is now live
            </div>
            
            <h1 class="text-5xl sm:text-7xl font-display font-extrabold text-white tracking-tight leading-[1.1] mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
                Result management,<br>
                <span class="text-gradient">reimagined for the future.</span>
            </h1>
            
            <p class="mt-4 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s;">
                One system adapts to ANY school's exam pattern. Design your marksheets directly in Microsoft Word, map placeholders with AI, and generate hundreds of results instantly.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                <a href="{{ route('register') }}" class="px-8 py-4 rounded-full bg-white text-gray-900 font-bold shadow-[0_0_30px_rgba(255,255,255,0.15)] hover:shadow-[0_0_40px_rgba(255,255,255,0.25)] hover:scale-105 transition-all duration-300">
                    Start for free
                </a>
                <a href="#features" class="px-8 py-4 rounded-full glass-panel text-white font-semibold hover:bg-white/10 transition-all duration-300 flex items-center justify-center gap-2">
                    Explore features 
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
            </div>
            
            <!-- Dashboard Preview Image -->
            <div class="mt-12 sm:mt-20 relative max-w-5xl mx-auto animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="absolute -inset-1 bg-gradient-to-r from-stone-500/30 to-neutral-400/20 rounded-2xl blur opacity-40"></div>
                <div class="relative rounded-2xl glass-panel p-2 ring-1 ring-white/10 shadow-2xl">
                    <div class="rounded-xl overflow-hidden bg-black/60 aspect-video flex items-center justify-center border border-white/5 relative">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                        <div class="text-center z-10">
                            <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/10">
                                <svg aria-hidden="true" class="w-10 h-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-display font-semibold text-white mb-2">Powerful Dashboard</h3>
                            <p class="text-stone-400">Sign in to experience the real application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-stone-300 font-semibold tracking-widest uppercase text-xs mb-3 letter-spacing-wider">Capabilities</h2>
                <p class="text-3xl sm:text-4xl font-display font-bold text-white">Everything you need to run exams</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20 mb-6 group-hover:bg-blue-500/20 transition-colors">
                        <svg aria-hidden="true" class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">MS Word Templates</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Design your marksheets exactly how you want in Microsoft Word. Upload the .docx and let our AI map the placeholders automatically.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center border border-purple-500/20 mb-6 group-hover:bg-purple-500/20 transition-colors">
                        <svg aria-hidden="true" class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Live Result Analytics</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Instantly calculate GPAs, grades, and merit positions. View class-wide analytics and generate detailed merit lists in one click.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 flex items-center justify-center border border-pink-500/20 mb-6 group-hover:bg-pink-500/20 transition-colors">
                        <svg aria-hidden="true" class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">SMS & WhatsApp</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Notify parents instantly when results are published. Integrated with Twilio to send bulk SMS or WhatsApp messages containing results.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 relative z-10 border-t border-white/5 bg-black/30 mt-12">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl sm:text-5xl font-display font-bold text-white mb-6">Ready to transform your school?</h2>
            <p class="text-stone-400 text-lg mb-10">Join schools around the world that use ResultMaker to save hundreds of hours on result processing.</p>
            <a href="{{ route('register') }}" class="px-8 py-4 rounded-full bg-white text-gray-900 font-bold hover:scale-105 transition-transform duration-300 shadow-[0_0_30px_rgba(255,255,255,0.15)] hover:shadow-[0_0_40px_rgba(255,255,255,0.25)] inline-block">
                Create an account
            </a>
        </div>
    </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/10 bg-black/60 py-12 text-center text-stone-500 text-sm">
        <div class="flex items-center justify-center gap-2 mb-4">
            <img src="{{ asset('large.png') }}" alt="ResultMaker" class="h-8 w-auto invert opacity-80">
        </div>
        <p>&copy; {{ date('Y') }} ResultMaker Inc. All rights reserved.</p>
    </footer>

</body>
</html>
