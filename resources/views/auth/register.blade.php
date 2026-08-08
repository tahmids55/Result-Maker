<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResultMaker – Create Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Fraunces', 'Georgia', 'serif'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        paper: '#f6f1e7',
                        'paper-deep': '#efe7d7',
                        ink: '#1b2432',
                        'ink-soft': '#47536a',
                        seal: '#c0392b',
                        'seal-deep': '#9c2b20',
                        gold: '#b58a3a',
                        forest: '#2f5d50',
                        line: '#d8cdb8',
                    },
                }
            }
        }
    </script>
    <style>
        body { -webkit-font-smoothing: antialiased; font-feature-settings: 'ss01'; }
        ::selection { background-color: #1b2432; color: #f6f1e7; }
        * { border-color: #d8cdb8; }
    </style>
</head>
<body class="h-full bg-paper font-sans text-ink">

    {{-- Faint ledger grid background --}}
    <div class="fixed inset-0 pointer-events-none opacity-40" style="background-image:linear-gradient(to right,rgba(27,36,50,0.04) 1px,transparent 1px),linear-gradient(to bottom,rgba(27,36,50,0.04) 1px,transparent 1px);background-size:32px 32px;"></div>

    <div class="relative min-h-full flex">
        {{-- Left Panel: Branding --}}
        <div class="hidden lg:flex lg:w-[45%] bg-ink text-paper flex-col justify-between p-12 relative overflow-hidden">
            {{-- Dot pattern --}}
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image:radial-gradient(circle at center,rgba(255,255,255,0.5) 1px,transparent 1.4px);background-size:20px 20px;"></div>

            <div class="relative">
                <a href="/" class="flex items-center gap-2.5 group">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-paper text-ink transition-transform duration-300 group-hover:rotate-[18deg]">
                        <svg viewBox="0 0 48 48" class="h-6 w-6"><circle cx="24" cy="24" r="21" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="24" cy="24" r="16.5" fill="none" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1.5 2.5"/><path d="M17 24.5l4.6 4.6L31 19.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="font-display text-[1.35rem] font-semibold leading-none tracking-tight">Result<span class="text-gold">Maker</span></span>
                </a>
            </div>

            <div class="relative">
                <h2 class="font-display text-[2.4rem] font-semibold leading-[1.08] tracking-[-0.01em]">Set up your first exam in under five minutes.</h2>
                <p class="mt-6 text-paper/60 text-lg leading-relaxed">No credit card required. Free plan available for single-class trials.</p>
                <div class="mt-10 space-y-4">
                    @php $perks = ['Upload your own Word marksheet template','Auto-calculate GPA, grades & merit','Bulk-generate marksheets in one click','Notify parents via SMS & WhatsApp']; @endphp
                    @foreach($perks as $perk)
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-forest/30 text-forest text-[0.65rem] font-bold">✓</span>
                        <span class="text-paper/70 text-[0.92rem]">{{ $perk }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="relative">
                <p class="font-mono text-[0.72rem] text-paper/40">&copy; {{ date('Y') }} ResultMaker · Built for educators</p>
            </div>
        </div>

        {{-- Right Panel: Registration Form --}}
        <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                {{-- Mobile wordmark --}}
                <div class="lg:hidden mb-10 text-center">
                    <a href="/" class="inline-flex items-center gap-2.5 group">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink text-paper transition-transform duration-300 group-hover:rotate-[18deg]">
                            <svg viewBox="0 0 48 48" class="h-6 w-6"><circle cx="24" cy="24" r="21" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="24" cy="24" r="16.5" fill="none" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1.5 2.5"/><path d="M17 24.5l4.6 4.6L31 19.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="font-display text-[1.35rem] font-semibold leading-none tracking-tight text-ink">Result<span class="text-seal">Maker</span></span>
                    </a>
                </div>

                {{-- Form header --}}
                <div class="mb-8">
                    <h1 class="font-display text-[1.8rem] font-semibold text-ink tracking-[-0.01em]">Create your account</h1>
                    <p class="mt-2 text-ink-soft">Get started with ResultMaker for free.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-ink mb-1.5">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="w-full py-2.5 px-4 bg-[#fdfbf6] text-ink border border-line rounded-lg focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink/20 text-sm transition-all placeholder:text-ink-soft/50
                                      @error('name') border-seal @enderror"
                               required autofocus placeholder="Md. Abdul Karim">
                        @error('name')
                            <p class="text-xs text-seal pl-1 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-ink mb-1.5">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="w-full py-2.5 px-4 bg-[#fdfbf6] text-ink border border-line rounded-lg focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink/20 text-sm transition-all placeholder:text-ink-soft/50
                                      @error('email') border-seal @enderror"
                               required placeholder="headmaster@school.edu.bd">
                        @error('email')
                            <p class="text-xs text-seal pl-1 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-ink mb-1.5">Password</label>
                        <input type="password" name="password" id="password"
                               class="w-full py-2.5 px-4 bg-[#fdfbf6] text-ink border border-line rounded-lg focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink/20 text-sm transition-all placeholder:text-ink-soft/50
                                      @error('password') border-seal @enderror"
                               required placeholder="Minimum 8 characters">
                        @error('password')
                            <p class="text-xs text-seal pl-1 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-ink mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full py-2.5 px-4 bg-[#fdfbf6] text-ink border border-line rounded-lg focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink/20 text-sm transition-all placeholder:text-ink-soft/50"
                               required placeholder="••••••••">
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full bg-seal hover:bg-seal-deep text-paper font-semibold py-3 px-6 rounded-full text-sm transition-all duration-300 shadow-[0_10px_24px_-8px_rgba(192,57,43,0.5)] mt-2">
                        Create account &rarr;
                    </button>

                    <p class="text-center font-mono text-[0.68rem] text-ink-soft/60 mt-3">No credit card required · Free starter plan included</p>
                </form>

                {{-- Divider --}}
                <div class="mt-8 flex items-center gap-4">
                    <div class="flex-1 h-px bg-line"></div>
                    <span class="font-mono text-[0.68rem] uppercase tracking-[0.15em] text-ink-soft/60">or</span>
                    <div class="flex-1 h-px bg-line"></div>
                </div>

                <p class="mt-6 text-center text-sm text-ink-soft">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-ink hover:text-seal transition-colors">Sign in &rarr;</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
