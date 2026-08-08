<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing | ResultMaker</title>
    <meta name="description" content="Simple, transparent pricing for ResultMaker.">
    <meta name="theme-color" content="#c0392b">
    
    <!-- Fonts -->
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
<body class="antialiased bg-paper text-ink font-sans min-h-screen flex flex-col">

    {{-- ═══ NAVIGATION ═══ --}}
    <header class="fixed top-0 left-0 right-0 w-full z-50 border-b border-line/80 bg-paper/90 backdrop-blur-md transition-all duration-300" x-data="{ open: false }">
        <div class="mx-auto flex max-w-[1200px] items-center justify-between px-6 py-4">
            {{-- Wordmark --}}
            <a href="{{ route('welcome') }}" class="group flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink text-paper transition-transform duration-300 group-hover:rotate-[18deg]">
                    <svg viewBox="0 0 48 48" class="h-6 w-6" aria-hidden="true">
                        <circle cx="24" cy="24" r="21" fill="none" stroke="currentColor" stroke-width="1.4"/>
                        <circle cx="24" cy="24" r="16.5" fill="none" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1.5 2.5"/>
                        <path d="M17 24.5l4.6 4.6L31 19.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-display text-[1.35rem] font-semibold leading-none tracking-tight text-ink">Result<span class="text-seal">Maker</span></span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-8 md:flex">
                <a href="{{ route('features') }}" class="text-sm font-medium text-ink-soft transition-colors hover:text-ink">Features</a>
                <a href="{{ url('/#workflow') }}" class="text-sm font-medium text-ink-soft transition-colors hover:text-ink">How it works</a>
                <a href="{{ url('/#templates') }}" class="text-sm font-medium text-ink-soft transition-colors hover:text-ink">Templates</a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-ink transition-colors">Pricing</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden text-sm font-medium text-ink-soft transition-colors hover:text-ink sm:block">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-medium text-ink-soft transition-colors hover:text-ink sm:block">Sign in</a>
                @endauth
                <a href="{{ route('register') }}" class="rounded-full bg-ink px-5 py-2.5 text-sm font-semibold text-paper shadow-[0_1px_0_rgba(0,0,0,0.2)] transition-all duration-300 hover:bg-seal">Start free trial</a>
                <button @click="open = !open" class="flex h-9 w-9 items-center justify-center rounded-full border border-line md:hidden" aria-label="Menu">
                    <span class="text-ink text-lg">≡</span>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-cloak x-transition class="border-t border-line/80 bg-paper px-6 py-4 md:hidden shadow-xl">
            <div class="flex flex-col gap-3">
                <a href="{{ route('features') }}" class="text-sm font-medium text-ink-soft">Features</a>
                <a href="{{ url('/#workflow') }}" class="text-sm font-medium text-ink-soft">How it works</a>
                <a href="{{ url('/#templates') }}" class="text-sm font-medium text-ink-soft">Templates</a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-ink">Pricing</a>
                @guest <a href="{{ route('login') }}" class="text-sm font-medium text-ink-soft">Sign in</a> @endguest
            </div>
        </div>
    </header>

    <main class="flex-1 pt-[73px]">
        {{-- Faint ledger grid background for the page --}}
        <div class="fixed inset-0 pointer-events-none opacity-40 z-[-1]" style="background-image:linear-gradient(to right,rgba(27,36,50,0.04) 1px,transparent 1px),linear-gradient(to bottom,rgba(27,36,50,0.04) 1px,transparent 1px);background-size:32px 32px;"></div>

        {{-- ═══ PRICING ═══ --}}
        <section id="pricing" class="py-14 sm:py-24">
            <div class="mx-auto max-w-[1200px] px-4 sm:px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <span class="font-mono text-[0.72rem] uppercase tracking-[0.28em] text-seal">Simple pricing</span>
                    <h1 class="mt-4 font-display text-[2rem] sm:text-[3rem] font-semibold leading-[1.06] tracking-[-0.01em] text-ink">Priced for real school budgets.</h1>
                    <p class="mt-5 text-lg leading-relaxed text-ink-soft">No hidden fees, no complex tiers. Start free, then upgrade when your whole institution is ready to automate results.</p>
                </div>
                <div class="mt-14 grid gap-6 lg:grid-cols-3">
                    @php $plans = [
                        ['name'=>'Starter','price'=>'Free','per'=>'','note'=>'For a single class trial','features'=>['Up to 60 students','1 exam · 1 template','Auto GPA & grading','PDF marksheets'],'cta'=>'Start free','featured'=>false],
                        ['name'=>'School','price'=>'৳2,500','per'=>'/ month','note'=>'For a full institution','features'=>['Unlimited students & classes','Word + PDF bulk generation','OCR mark entry','SMS & WhatsApp notifications','Teacher roles & audit log'],'cta'=>'Start free trial','featured'=>true],
                        ['name'=>'District','price'=>'Custom','per'=>'','note'=>'For groups & boards','features'=>['Multiple institutions','Priority support & training','Database backup & restore','Dedicated onboarding'],'cta'=>'Contact sales','featured'=>false],
                    ]; @endphp
                    @foreach($plans as $plan)
                    <div class="relative flex flex-col rounded-2xl border p-7 transition-transform duration-300 hover:-translate-y-1 bg-paper {{ $plan['featured'] ? 'border-ink bg-ink text-paper shadow-[0_30px_60px_-30px_rgba(27,36,50,0.7)]' : 'border-line bg-[#fdfbf6]' }}">
                        @if($plan['featured'])
                        <span class="absolute -top-3 left-7 rounded-full bg-seal px-3 py-1 font-mono text-[0.6rem] uppercase tracking-[0.15em] text-paper">Most popular</span>
                        @endif
                        <p class="font-mono text-[0.7rem] uppercase tracking-[0.2em] {{ $plan['featured'] ? 'text-gold' : 'text-seal' }}">{{ $plan['name'] }}</p>
                        <div class="mt-4 flex items-end gap-1.5">
                            <span class="font-display text-4xl font-semibold">{{ $plan['price'] }}</span>
                            @if($plan['per'])<span class="pb-1 text-sm {{ $plan['featured'] ? 'text-paper/60' : 'text-ink-soft' }}">{{ $plan['per'] }}</span>@endif
                        </div>
                        <p class="mt-1 text-[0.82rem] {{ $plan['featured'] ? 'text-paper/60' : 'text-ink-soft' }}">{{ $plan['note'] }}</p>
                        <ul class="mt-6 flex-1 space-y-3">
                            @foreach($plan['features'] as $feat)
                            <li class="flex items-start gap-2.5 text-[0.88rem] {{ $plan['featured'] ? 'text-paper/85' : 'text-ink' }}">
                                <span class="{{ $plan['featured'] ? 'text-gold' : 'text-forest' }}">✓</span> {{ $feat }}
                            </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="mt-7 rounded-full px-6 py-3 text-center text-sm font-semibold transition-colors duration-300 {{ $plan['featured'] ? 'bg-seal text-paper hover:bg-seal-deep' : 'border border-ink/25 text-ink hover:border-ink hover:bg-ink hover:text-paper' }}">{{ $plan['cta'] }}</a>
                    </div>
                    @endforeach
                </div>
                
                {{-- FAQs / Common questions --}}
                <div class="mt-24 max-w-3xl mx-auto">
                    <h3 class="font-display text-2xl font-semibold text-ink text-center mb-10">Common Questions</h3>
                    <div class="space-y-6">
                        <div class="border-b border-line pb-6">
                            <h4 class="font-semibold text-ink">Do I need to enter credit card details for the free Starter plan?</h4>
                            <p class="mt-2 text-sm text-ink-soft">No. You can create an account and run your first class entirely for free without adding any payment information.</p>
                        </div>
                        <div class="border-b border-line pb-6">
                            <h4 class="font-semibold text-ink">What happens if we have more than one Word template?</h4>
                            <p class="mt-2 text-sm text-ink-soft">The School plan allows you to upload as many templates as you need. You can use different templates for Class VI vs Class IX, or half-yearly vs annual exams.</p>
                        </div>
                        <div class="border-b border-line pb-6">
                            <h4 class="font-semibold text-ink">Is there a limit on SMS notifications?</h4>
                            <p class="mt-2 text-sm text-ink-soft">SMS credits are billed separately at standard local carrier rates. The platform integration itself is included in your monthly subscription.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="border-t border-line bg-paper z-10 relative">
        <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-10 sm:py-16">
            <div class="grid grid-cols-2 gap-8 sm:gap-10 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
                <div class="col-span-2 md:col-span-1">
                    <a href="{{ route('welcome') }}" class="group flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink text-paper">
                            <svg viewBox="0 0 48 48" class="h-6 w-6"><circle cx="24" cy="24" r="21" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="24" cy="24" r="16.5" fill="none" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1.5 2.5"/><path d="M17 24.5l4.6 4.6L31 19.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="font-display text-[1.35rem] font-semibold leading-none tracking-tight text-ink">Result<span class="text-seal">Maker</span></span>
                    </a>
                    <p class="mt-4 max-w-xs text-[0.86rem] leading-relaxed text-ink-soft">The result & marksheet workflow, automated — built for schools in Bangladesh, South Asia, and beyond.</p>
                </div>
                @php $footerCols = [
                    ['Product', ['Features','Templates','Pricing','OCR entry','Notifications']],
                    ['Institution', ['For headmasters','For teachers','Grading rules','Data backup']],
                    ['Company', ['About','Contact','Support','Privacy']],
                ]; @endphp
                @foreach($footerCols as $col)
                <div>
                    <p class="font-mono text-[0.66rem] uppercase tracking-[0.2em] text-ink-soft">{{ $col[0] }}</p>
                    <ul class="mt-4 space-y-2.5">
                        @foreach($col[1] as $link)
                        <li><a href="#" class="text-[0.88rem] text-ink transition-colors hover:text-seal">{{ $link }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
            <div class="mt-14 flex flex-col items-start justify-between gap-3 border-t border-line pt-6 sm:flex-row sm:items-center">
                <p class="font-mono text-[0.72rem] text-ink-soft">&copy; {{ date('Y') }} ResultMaker · Built for educators</p>
                <p class="font-mono text-[0.72rem] text-ink-soft">Made with care · Laravel · Livewire</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for mobile menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
