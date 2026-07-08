<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarksCraft | Dynamic Result Management</title>
    
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
                    colors: {
                        slate: {
                            850: '#151e2e',
                            900: '#0f172a',
                            950: '#020617',
                        },
                        indigo: {
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                        },
                        violet: {
                            500: '#8b5cf6',
                            600: '#7c3aed',
                        }
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
    
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .text-gradient {
            background: linear-gradient(to right, #818cf8, #c084fc, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .mesh-bg {
            background-color: #020617;
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(56, 189, 248, 0.15) 0px, transparent 50%);
        }
    </style>
</head>
<body class="antialiased text-slate-300 mesh-bg min-h-screen selection:bg-indigo-500/30 selection:text-indigo-200">
    
    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-panel border-b-0 border-white/5 bg-slate-950/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3 group cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:shadow-indigo-500/50 transition-all duration-300 transform group-hover:scale-105">
                        <span class="text-white font-display font-bold text-xl">M</span>
                    </div>
                    <span class="font-display font-bold text-xl text-white tracking-tight">MarksCraft</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8 text-sm font-medium">
                    <a href="#features" class="text-slate-300 hover:text-white transition-colors duration-200">Features</a>
                    <a href="#how-it-works" class="text-slate-300 hover:text-white transition-colors duration-200">How it Works</a>
                    
                    <div class="flex items-center space-x-4 pl-4 border-l border-white/10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-white hover:text-indigo-300 transition-colors">Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>" class="text-slate-300 hover:text-white transition-colors">Log in</a>
                            <a href="<?php echo e(route('register')); ?>" class="px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white border border-white/10 hover:border-white/20 transition-all duration-300 shadow-[0_0_15px_rgba(255,255,255,0.05)] hover:shadow-[0_0_20px_rgba(255,255,255,0.1)]">
                                Get Started
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 sm:pt-40 sm:pb-24 lg:pb-32 overflow-hidden">
        <!-- Animated Background Orbs -->
        <div class="absolute top-0 -left-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-screen filter blur-[128px] opacity-20 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-violet-500 rounded-full mix-blend-screen filter blur-[128px] opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-screen filter blur-[128px] opacity-20 animate-blob animation-delay-4000"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-sm font-medium mb-8 animate-fade-in-up">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                MarksCraft 2.0 is now live
            </div>
            
            <h1 class="text-5xl sm:text-7xl font-display font-extrabold text-white tracking-tight leading-[1.1] mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
                Result management,<br>
                <span class="text-gradient">reimagined for the future.</span>
            </h1>
            
            <p class="mt-4 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s;">
                One system adapts to ANY school's exam pattern. Design your marksheets directly in Microsoft Word, map placeholders with AI, and generate hundreds of results instantly.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                <a href="<?php echo e(route('register')); ?>" class="px-8 py-4 rounded-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-[0_0_30px_rgba(99,102,241,0.3)] hover:shadow-[0_0_40px_rgba(99,102,241,0.5)] hover:scale-105 transition-all duration-300">
                    Start for free
                </a>
                <a href="#features" class="px-8 py-4 rounded-full glass-panel text-white font-semibold hover:bg-white/5 transition-all duration-300 flex items-center justify-center gap-2">
                    Explore features 
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
            </div>
            
            <!-- Dashboard Preview Image -->
            <div class="mt-20 relative max-w-5xl mx-auto animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-pink-500 rounded-2xl blur opacity-20"></div>
                <div class="relative rounded-2xl glass-panel p-2 ring-1 ring-white/10 shadow-2xl">
                    <div class="rounded-xl overflow-hidden bg-slate-900 aspect-video flex items-center justify-center border border-white/5 relative">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                        <div class="text-center z-10">
                            <div class="w-20 h-20 bg-indigo-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-indigo-500/30">
                                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-display font-semibold text-white mb-2">Powerful Dashboard</h3>
                            <p class="text-slate-400">Sign in to experience the real application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-indigo-400 font-semibold tracking-wide uppercase text-sm mb-3">Capabilities</h2>
                <p class="text-3xl sm:text-4xl font-display font-bold text-white">Everything you need to run exams</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20 mb-6 group-hover:bg-blue-500/20 transition-colors">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">MS Word Templates</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Design your marksheets exactly how you want in Microsoft Word. Upload the .docx and let our AI map the placeholders automatically.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center border border-purple-500/20 mb-6 group-hover:bg-purple-500/20 transition-colors">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Live Result Analytics</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Instantly calculate GPAs, grades, and merit positions. View class-wide analytics and generate detailed merit lists in one click.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="glass-panel p-8 rounded-2xl hover:-translate-y-2 transition-transform duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 flex items-center justify-center border border-pink-500/20 mb-6 group-hover:bg-pink-500/20 transition-colors">
                        <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">SMS & WhatsApp</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Notify parents instantly when results are published. Integrated with Twilio to send bulk SMS or WhatsApp messages containing results.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-24 relative z-10 border-t border-white/5 bg-slate-900/50 mt-12">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl sm:text-5xl font-display font-bold text-white mb-6">Ready to transform your school?</h2>
            <p class="text-slate-400 text-lg mb-10">Join schools around the world that use MarksCraft to save hundreds of hours on result processing.</p>
            <a href="<?php echo e(route('register')); ?>" class="px-8 py-4 rounded-full bg-white text-slate-900 font-bold hover:scale-105 transition-transform duration-300 inline-block">
                Create an account
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-white/10 bg-slate-950 py-12 text-center text-slate-500 text-sm">
        <div class="flex items-center justify-center gap-2 mb-4">
            <div class="w-6 h-6 bg-indigo-600 rounded flex items-center justify-center text-white font-bold text-xs">M</div>
            <span class="font-bold text-white">MarksCraft</span>
        </div>
        <p>&copy; <?php echo e(date('Y')); ?> MarksCraft Inc. All rights reserved.</p>
    </footer>

</body>
</html>
<?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/welcome.blade.php ENDPATH**/ ?>