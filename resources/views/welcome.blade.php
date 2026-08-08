<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ResultMaker | Advanced School Result Management System</title>
    <meta name="title" content="ResultMaker | Advanced School Result Management System">
    <meta name="description" content="ResultMaker is a powerful, dynamic result management system for schools. Automate report cards, grade processing, and OCR-based mark entry effortlessly.">
    <meta name="keywords" content="school result management, automated report cards, student marks entry, OCR grading, school software, teacher dashboard, education technology, result maker, marksheet generator, school management system, SMS integration for schools, student grade calculator, academic performance tracking, automated grading software, digital report cards, exam management system, class grading, student analytics, education software Bangladesh, school portal, teacher portal, results processing software, dynamic marksheet, MS word marksheet template">
    <meta name="author" content="ResultMaker">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    <meta name="rating" content="General">
    <meta name="distribution" content="Global">
    <meta name="google-site-verification" content="SJINiNxzTbNgBluEkns0aTJ2o3CSOtjv7q19eXNW-aY" />
    <link rel="canonical" href="{{ url('/') }}">
    <meta name="theme-color" content="#c0392b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ResultMaker">
    <meta name="application-name" content="ResultMaker">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="ResultMaker | Advanced School Result Management System">
    <meta property="og:description" content="ResultMaker is a powerful, dynamic result management system for schools. Automate report cards, grade processing, and OCR-based mark entry effortlessly.">
    <meta property="og:site_name" content="ResultMaker">
    <meta property="og:locale" content="en_US">
    <meta property="og:image" content="{{ url('/images/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="ResultMaker | Advanced School Result Management System">
    <meta property="twitter:description" content="ResultMaker is a powerful, dynamic result management system for schools.">
    <meta property="twitter:image" content="{{ url('/images/og-image.png') }}">

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
                    animation: {
                        'rm-rise': 'rmRise 0.7s cubic-bezier(0.22,1,0.36,1) both',
                    },
                    keyframes: {
                        rmRise: {
                            '0%': { opacity: '0', transform: 'translateY(14px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    }
                }
            }
        }
    </script>

    <style>
        html { scroll-behavior: smooth; scrollbar-width: thin; scrollbar-color: transparent transparent; }
        html:hover { scrollbar-color: #d8cdb8 transparent; }
        body { -webkit-font-smoothing: antialiased; font-feature-settings: 'ss01'; }
        ::selection { background-color: #1b2432; color: #f6f1e7; }
        * { border-color: #d8cdb8; }
        .rm-rise { animation: rmRise 0.7s cubic-bezier(0.22,1,0.36,1) both; }
        @keyframes rmRise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="antialiased bg-paper text-ink font-sans min-h-screen">

    {{-- ═══ NAVIGATION ═══ --}}
    <header class="fixed top-0 left-0 right-0 w-full z-50 border-b border-line/80 bg-paper/90 backdrop-blur-md transition-all duration-300" x-data="{ open: false }">
        <div class="mx-auto flex max-w-[1200px] items-center justify-between px-6 py-4">
            {{-- Wordmark --}}
            <a href="/" class="group flex items-center gap-2.5">
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
                <a href="#workflow" class="text-sm font-medium text-ink-soft transition-colors hover:text-ink">How it works</a>
                <a href="#templates" class="text-sm font-medium text-ink-soft transition-colors hover:text-ink">Templates</a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-ink-soft transition-colors hover:text-ink">Pricing</a>
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
                <a href="{{ route('features') }}" @click="open=false" class="text-sm font-medium text-ink-soft">Features</a>
                <a href="#workflow" @click="open=false" class="text-sm font-medium text-ink-soft">How it works</a>
                <a href="#templates" @click="open=false" class="text-sm font-medium text-ink-soft">Templates</a>
                <a href="{{ route('pricing') }}" @click="open=false" class="text-sm font-medium text-ink-soft">Pricing</a>
                @guest <a href="{{ route('login') }}" class="text-sm font-medium text-ink-soft">Sign in</a> @endguest
            </div>
        </div>
    </header>

    <main class="pt-[73px]">
    {{-- ═══ HERO ═══ --}}
    <section id="top" class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 opacity-50" style="background-image:linear-gradient(to right,rgba(27,36,50,0.04) 1px,transparent 1px),linear-gradient(to bottom,rgba(27,36,50,0.04) 1px,transparent 1px);background-size:32px 32px;mask-image:radial-gradient(ellipse 80% 70% at 50% 0%,#000 40%,transparent 100%);"></div>
        <div class="mx-auto grid max-w-[1200px] items-center gap-10 sm:gap-14 px-4 sm:px-6 pb-14 pt-12 sm:pb-20 sm:pt-16 lg:grid-cols-[1.05fr_0.95fr] lg:pb-28 lg:pt-24">
            <div class="rm-rise">
                <span class="inline-flex items-center gap-2 rounded-full border border-line bg-[#fdfbf6] px-3.5 py-1.5 font-mono text-[0.68rem] uppercase tracking-[0.18em] text-ink-soft">
                    <span class="h-1.5 w-1.5 rounded-full bg-seal"></span>
                    Built for schools across South Asia
                </span>
                <h1 class="mt-6 font-display text-[2rem] xs:text-[2.4rem] font-semibold leading-[1.08] tracking-[-0.02em] text-ink sm:text-[3rem] lg:text-[3.6rem]">
                    From raw marks to a
                    <span class="relative text-seal sm:whitespace-nowrap">
                        finished marksheet
                        <svg class="absolute -bottom-1 sm:-bottom-2 left-0 w-full" height="10" viewBox="0 0 300 10" preserveAspectRatio="none" aria-hidden="true">
                            <path d="M2 7C60 2 240 2 298 6" stroke="#c0392b" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                        </svg>
                    </span>
                    — in one afternoon.
                </h1>
                <p class="mt-7 max-w-xl text-lg leading-relaxed text-ink-soft">
                    ResultMaker automates the entire exam workflow: live marks entry, automatic GPA and grading, merit positions, and hundreds of individual marksheets generated from <span class="font-semibold text-ink">your own Word template</span> — not a generic one.
                </p>
                <div class="mt-7 sm:mt-9 flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 sm:gap-4">
                    <a href="{{ route('register') }}" class="group rounded-full bg-seal px-7 py-3 sm:py-3.5 text-sm sm:text-[0.95rem] font-semibold text-paper shadow-[0_10px_24px_-8px_rgba(192,57,43,0.6)] transition-all duration-300 hover:bg-seal-deep text-center">
                        Start free trial <span class="ml-1.5 inline-block transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                    </a>
                    <a href="#workflow" class="rounded-full border border-ink/25 px-7 py-3 sm:py-3.5 text-sm sm:text-[0.95rem] font-semibold text-ink transition-colors duration-300 hover:border-ink hover:bg-ink hover:text-paper text-center">See how it works</a>
                </div>
                <p class="mt-6 font-mono text-[0.72rem] text-ink-soft">No card required · Set up your first exam in minutes · Bangla + English support</p>
            </div>

            {{-- Marksheet Card --}}
            <div class="rm-rise" style="animation-delay:120ms">
                <div class="relative">
                    <div class="absolute -right-3 top-4 h-full w-full rounded-[10px] border border-line bg-paper-deep"></div>
                    <div class="absolute -right-1.5 top-2 h-full w-full rounded-[10px] border border-line bg-[#fbf8f1]"></div>
                    <div class="relative overflow-hidden rounded-[10px] border border-line bg-[#fdfbf6] shadow-[0_30px_60px_-24px_rgba(27,36,50,0.4)]">
                        <div class="border-b-2 border-double border-ink/70 px-4 sm:px-7 pb-4 sm:pb-5 pt-5 sm:pt-6 text-center">
                            <p class="font-mono text-[0.55rem] sm:text-[0.62rem] uppercase tracking-[0.35em] text-seal">Progress Report</p>
                            <h3 class="mt-1.5 font-display text-base sm:text-xl font-semibold text-ink">Shurjomukhi High School</h3>
                            <p class="mt-0.5 text-[0.62rem] sm:text-[0.7rem] text-ink-soft">Annual Examination 2026 · Class IX · Section A</p>
                        </div>
                        <div class="grid grid-cols-2 gap-x-4 sm:gap-x-6 gap-y-1 px-4 sm:px-7 py-3 sm:py-4 text-[0.65rem] sm:text-[0.72rem]">
                            <p class="text-ink-soft">Student: <span class="font-semibold text-ink">Anika Tabassum</span></p>
                            <p class="text-ink-soft">Roll: <span class="font-mono font-semibold text-ink">14</span></p>
                            <p class="text-ink-soft">ID: <span class="font-mono font-semibold text-ink">2026-0914</span></p>
                            <p class="text-ink-soft">Group: <span class="font-semibold text-ink">Science</span></p>
                        </div>
                        <div class="overflow-x-auto">
                        <table class="w-full border-t border-line text-[0.65rem] sm:text-[0.72rem]">
                            <thead>
                                <tr class="bg-paper-deep/70 text-left font-mono text-[0.52rem] sm:text-[0.6rem] uppercase tracking-wider text-ink-soft">
                                    <th class="px-3 sm:px-7 py-2 font-medium">Subject</th>
                                    <th class="px-1 sm:px-2 py-2 text-center font-medium">Marks</th>
                                    <th class="px-1 sm:px-2 py-2 text-center font-medium">Grade</th>
                                    <th class="px-3 sm:px-7 py-2 text-right font-medium">GP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $subjects = [['Bangla 1st Paper','92','A+','5.00'],['English 1st Paper','86','A+','5.00'],['Mathematics','95','A+','5.00'],['Physics','78','A','4.00'],['Chemistry','81','A+','5.00'],['ICT','89','A+','5.00']]; @endphp
                                @foreach($subjects as $s)
                                <tr class="border-t border-line/70">
                                    <td class="px-3 sm:px-7 py-1.5 text-ink">{{ $s[0] }}</td>
                                    <td class="px-1 sm:px-2 py-1.5 text-center font-mono text-ink">{{ $s[1] }}</td>
                                    <td class="px-1 sm:px-2 py-1.5 text-center font-mono font-semibold text-forest">{{ $s[2] }}</td>
                                    <td class="px-3 sm:px-7 py-1.5 text-right font-mono text-ink">{{ $s[3] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="flex items-center justify-between border-t-2 border-double border-ink/70 bg-paper-deep/40 px-4 sm:px-7 py-3 sm:py-4">
                            <div>
                                <p class="font-mono text-[0.58rem] uppercase tracking-[0.2em] text-ink-soft">Merit Position</p>
                                <p class="font-display text-lg font-semibold text-ink">3rd <span class="text-[0.7rem] font-normal text-ink-soft">of 128</span></p>
                            </div>
                            <div class="text-right">
                                <p class="font-mono text-[0.58rem] uppercase tracking-[0.2em] text-ink-soft">Final GPA</p>
                                <p class="font-display text-lg font-semibold text-seal">4.83</p>
                            </div>
                            <span class="flex h-12 w-12 items-center justify-center rounded-full border border-seal/40 text-seal">
                                <svg viewBox="0 0 48 48" class="h-9 w-9"><circle cx="24" cy="24" r="21" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="24" cy="24" r="16.5" fill="none" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1.5 2.5"/><path d="M17 24.5l4.6 4.6L31 19.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ STATS ═══ --}}
    <section class="border-y border-line bg-ink text-paper">
        <div class="mx-auto grid max-w-[1200px] grid-cols-2 divide-x divide-white/10 px-4 sm:px-6 lg:grid-cols-4">
            @php $stats = [['3,200+','Marksheets generated per school, per exam'],['92%','Less time spent versus Excel + Word'],['11','Subjects processed simultaneously'],['0','Manual GPA calculations']]; @endphp
            @foreach($stats as $st)
            <div class="px-3 sm:px-5 py-6 sm:py-8 text-center lg:py-10">
                <p class="font-display text-2xl sm:text-4xl font-semibold tracking-tight text-paper">{{ $st[0] }}</p>
                <p class="mx-auto mt-1.5 sm:mt-2 max-w-[15ch] text-[0.68rem] sm:text-[0.78rem] leading-snug text-paper/60">{{ $st[1] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ═══ PROBLEM: OLD WAY vs NEW WAY ═══ --}}
    <section class="mx-auto max-w-[1200px] px-4 sm:px-6 py-14 sm:py-24">
        <div class="grid gap-14 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
            <div>
                <span class="font-mono text-[0.72rem] uppercase tracking-[0.28em] text-seal">The old way</span>
                <h2 class="mt-4 font-display text-[1.6rem] sm:text-[2.3rem] font-semibold leading-[1.08] tracking-[-0.01em] text-ink">Results season shouldn't cost you a month of nights.</h2>
                <p class="mt-5 text-lg leading-relaxed text-ink-soft">Copy marks into a spreadsheet. Calculate GPA by hand. Fix a wrong formula. Retype every marksheet in Word. Multiply by hundreds of students. ResultMaker replaces all of it.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @php $problems = [
                    ['Fragile spreadsheets','One dragged formula and a whole class ranks wrong.','Auto-calculated GPA, grades & merit'],
                    ['Retyping marksheets','Hand-typing hundreds of Word documents, one by one.','Bulk generation in a single click'],
                    ['Scattered mark slips','Handwritten sheets pile up on the staff-room desk.','OCR reads them from a photo'],
                    ['Anxious parents','Phones ringing the moment results are due.','Bulk SMS & WhatsApp on publish'],
                ]; @endphp
                @foreach($problems as $p)
                <div class="rounded-xl border border-line bg-[#fdfbf6] p-5 transition-shadow duration-300 hover:shadow-[0_16px_32px_-20px_rgba(27,36,50,0.4)]">
                    <p class="font-display text-lg font-semibold text-ink line-through decoration-seal/60 decoration-2">{{ $p[0] }}</p>
                    <p class="mt-1.5 text-[0.82rem] leading-snug text-ink-soft">{{ $p[1] }}</p>
                    <p class="mt-4 flex items-start gap-2 border-t border-line pt-3 text-[0.82rem] font-semibold text-forest">
                        <span class="mt-px">&rarr;</span> {{ $p[2] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ FEATURES ═══ --}}
    <section id="features" class="border-y border-line bg-paper-deep/40">
        <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-14 sm:py-24">
            <div class="max-w-2xl">
                <span class="font-mono text-[0.72rem] uppercase tracking-[0.28em] text-seal">Everything the result process needs</span>
                <h2 class="mt-4 font-display text-[1.7rem] sm:text-[2.4rem] font-semibold leading-[1.06] tracking-[-0.01em] text-ink">One system, laser-focused on marksheets.</h2>
            </div>
            <div class="mt-14 grid gap-px overflow-hidden rounded-2xl border border-line bg-line sm:grid-cols-2 lg:grid-cols-3">
                @php $features = [
                    ['Documents','Your Word template, mapped by AI','Upload the marksheet you already use. ResultMaker detects placeholders — name, roll, marks, GPA — and maps them to real data automatically. Every school keeps its own format.'],
                    ['Entry','Live spreadsheet marks entry','A real-time, Excel-like grid. Every keystroke saves instantly, and several teachers can enter marks for their assigned subjects at the same time.'],
                    ['Calculation','Grades, GPA & merit, automatically','Totals, letter grades, grade points, pass/fail and class rankings — all computed from your own configurable grading rules.'],
                    ['AI / OCR','Snap a photo, skip the typing','Photograph a handwritten mark sheet and OCR extracts the numbers for you. Review, confirm, done.'],
                    ['Output','Bulk marksheets, merit lists & tabulation','Generate every student marksheet as Word or PDF in one click, download as a ZIP, plus class merit lists and full tabulation sheets.'],
                    ['Notify','SMS & WhatsApp to parents','Publish results and send each family their child\'s outcome instantly through integrated Twilio bulk messaging.'],
                ]; @endphp
                @foreach($features as $f)
                <a href="{{ route('features') }}#{{ Str::slug($f[0]) }}" class="group flex flex-col bg-[#fdfbf6] p-5 sm:p-7 transition-colors duration-300 hover:bg-paper focus:outline-none focus:ring-2 focus:ring-seal/50">
                    <span class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-seal">{{ $f[0] }}</span>
                    <h3 class="mt-3 font-display text-xl font-semibold leading-snug text-ink">{{ $f[1] }}</h3>
                    <p class="mt-3 text-[0.9rem] leading-relaxed text-ink-soft">{{ $f[2] }}</p>
                    <span class="mt-5 inline-block text-sm font-semibold text-ink opacity-0 transition-opacity duration-300 group-hover:opacity-100">Learn more &rarr;</span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ SHOWCASE (Tabbed: Marks Entry / Grading / Merit) ═══ --}}
    <section id="templates" class="mx-auto max-w-[1200px] px-4 sm:px-6 py-14 sm:py-24" x-data="{ tab: 'entry' }">
        <div class="grid gap-12 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
            <div class="lg:sticky lg:top-28">
                <span class="font-mono text-[0.72rem] uppercase tracking-[0.28em] text-seal">Inside the workspace</span>
                <h2 class="mt-4 font-display text-[1.6rem] sm:text-[2.3rem] font-semibold leading-[1.08] tracking-[-0.01em] text-ink">Precision where it counts, calm everywhere else.</h2>
                <p class="mt-5 text-lg leading-relaxed text-ink-soft">The parts of the workflow teachers touch every day — mark entry, grade rules, and merit lists — designed to be fast, forgiving, and impossible to get wrong.</p>
                <div class="mt-8 flex flex-wrap gap-2">
                    <button @click="tab='entry'" :class="tab==='entry' ? 'border-ink bg-ink text-paper' : 'border-line bg-[#fdfbf6] text-ink-soft hover:border-ink/40 hover:text-ink'" class="rounded-full border px-4 py-2 text-sm font-semibold transition-all duration-300">Marks Entry</button>
                    <button @click="tab='grading'" :class="tab==='grading' ? 'border-ink bg-ink text-paper' : 'border-line bg-[#fdfbf6] text-ink-soft hover:border-ink/40 hover:text-ink'" class="rounded-full border px-4 py-2 text-sm font-semibold transition-all duration-300">Grading Rules</button>
                    <button @click="tab='merit'" :class="tab==='merit' ? 'border-ink bg-ink text-paper' : 'border-line bg-[#fdfbf6] text-ink-soft hover:border-ink/40 hover:text-ink'" class="rounded-full border px-4 py-2 text-sm font-semibold transition-all duration-300">Merit List</button>
                </div>
            </div>

            {{-- Tab: Marks Entry --}}
            <div x-show="tab==='entry'" x-transition class="rm-rise">
                <div class="overflow-hidden rounded-lg border border-line bg-[#fdfbf6]">
                    <div class="flex items-center justify-between border-b border-line bg-paper-deep/60 px-4 py-2.5">
                        <p class="font-mono text-[0.7rem] uppercase tracking-wider text-ink-soft">Class IX-A · Annual 2026</p>
                        <span class="flex items-center gap-1.5 font-mono text-[0.68rem] text-forest"><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-forest"></span> Saved live</span>
                    </div>
                    <div class="overflow-x-auto">
                    <table class="w-full text-[0.72rem] sm:text-[0.8rem] min-w-[420px]">
                        <thead><tr class="border-b border-line font-mono text-[0.55rem] sm:text-[0.62rem] uppercase tracking-wider text-ink-soft"><th class="px-3 sm:px-4 py-2 text-left font-medium">Roll</th><th class="px-3 sm:px-4 py-2 text-left font-medium">Name</th><th class="px-2 sm:px-3 py-2 text-center font-medium">Bangla</th><th class="px-2 sm:px-3 py-2 text-center font-medium">English</th><th class="px-2 sm:px-3 py-2 text-center font-medium">Math</th></tr></thead>
                        <tbody>
                            @php $students = [['02','Rifat Hasan','88','79','91'],['07','Mst. Salma','95','84','88'],['14','Anika Tabassum','92','86','95'],['21','Tanvir Ahmed','73','68','80']]; @endphp
                            @foreach($students as $i => $s)
                            <tr class="border-b border-line/70 {{ $i === 2 ? 'bg-seal/5' : '' }}">
                                <td class="px-4 py-2.5 font-mono text-ink-soft">{{ $s[0] }}</td>
                                <td class="px-4 py-2.5 text-ink">{{ $s[1] }}</td>
                                @foreach(array_slice($s, 2) as $j => $m)
                                <td class="px-3 py-2.5 text-center"><span class="inline-block min-w-[2.25rem] rounded border px-2 py-0.5 font-mono {{ $i === 2 && $j === 2 ? 'border-seal bg-seal/10 text-seal' : 'border-line bg-paper text-ink' }}">{{ $m }}</span></td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            {{-- Tab: Grading Rules --}}
            <div x-show="tab==='grading'" x-cloak x-transition class="rm-rise">
                <div class="overflow-hidden rounded-lg border border-line bg-[#fdfbf6]">
                    <div class="border-b border-line bg-paper-deep/60 px-4 py-2.5">
                        <p class="font-mono text-[0.7rem] uppercase tracking-wider text-ink-soft">Grading scheme · fully configurable</p>
                    </div>
                    <div class="grid grid-cols-3 gap-px bg-line sm:grid-cols-6">
                        @php $grades = [['A+','80 – 100','5.00','text-forest'],['A','70 – 79','4.00','text-forest'],['A-','60 – 69','3.50','text-ink'],['B','50 – 59','3.00','text-ink'],['C','40 – 49','2.00','text-ink-soft'],['F','00 – 32','0.00','text-seal']]; @endphp
                        @foreach($grades as $g)
                        <div class="bg-[#fdfbf6] p-4 text-center">
                            <p class="font-display text-2xl font-semibold {{ $g[3] }}">{{ $g[0] }}</p>
                            <p class="mt-1 font-mono text-[0.62rem] text-ink-soft">{{ $g[1] }}</p>
                            <p class="mt-2 font-mono text-[0.72rem] font-semibold text-ink">GP {{ $g[2] }}</p>
                        </div>
                        @endforeach
                    </div>
                    <p class="border-t border-line px-4 py-3 text-[0.78rem] text-ink-soft">Set your own boundaries and grade points — every calculation across the school follows them.</p>
                </div>
            </div>

            {{-- Tab: Merit List --}}
            <div x-show="tab==='merit'" x-cloak x-transition class="rm-rise">
                <div class="overflow-hidden rounded-lg border border-line bg-[#fdfbf6]">
                    <div class="border-b border-line bg-paper-deep/60 px-4 py-2.5">
                        <p class="font-mono text-[0.7rem] uppercase tracking-wider text-ink-soft">Merit list · Class IX · sorted by GPA</p>
                    </div>
                    <table class="w-full text-[0.82rem]"><tbody>
                        @php $meritRows = [['1','Nusrat Jahan','588','4.92'],['2','Sadia Islam','571','4.88'],['3','Anika Tabassum','564','4.83'],['4','Rifat Hasan','552','4.75'],['5','Mahin Chowdhury','540','4.67']]; @endphp
                        @foreach($meritRows as $i => $r)
                        <tr class="border-b border-line/70 last:border-0">
                            <td class="w-14 px-4 py-3"><span class="flex h-7 w-7 items-center justify-center rounded-full font-mono text-[0.72rem] font-semibold {{ $i < 3 ? 'bg-ink text-paper' : 'bg-paper-deep text-ink-soft' }}">{{ $r[0] }}</span></td>
                            <td class="px-2 py-3 text-ink">{{ $r[1] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-ink-soft">{{ $r[2] }}</td>
                            <td class="w-20 px-4 py-3 text-right font-mono font-semibold text-seal">{{ $r[3] }}</td>
                        </tr>
                        @endforeach
                    </tbody></table>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ WORKFLOW ═══ --}}
    <section id="workflow" class="border-y border-line bg-ink text-paper">
        <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-14 sm:py-24">
            <div class="max-w-2xl">
                <span class="font-mono text-[0.72rem] uppercase tracking-[0.28em] text-gold">The workflow</span>
                <h2 class="mt-4 font-display text-[1.7rem] sm:text-[2.4rem] font-semibold leading-[1.06] tracking-[-0.01em] text-paper">Four steps from empty class list to published results.</h2>
            </div>
            <div class="mt-14 grid gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10 md:grid-cols-2 lg:grid-cols-4">
                @php $steps = [
                    ['01','Set up your school','Add classes, sections, subjects and your grading rules. Import students from Excel in seconds.'],
                    ['02','Upload your template','Drop in your existing Word marksheet. AI maps the placeholders to data fields for you.'],
                    ['03','Enter the marks','Teachers fill the live spreadsheet — or snap a photo and let OCR read handwritten slips.'],
                    ['04','Generate & notify','One click produces every marksheet, merit list and tabulation sheet — then message parents.'],
                ]; @endphp
                @foreach($steps as $step)
                <div class="group bg-ink p-5 sm:p-7 transition-colors duration-300 hover:bg-[#232f41]">
                    <p class="font-mono text-3xl font-semibold text-gold">{{ $step[0] }}</p>
                    <h3 class="mt-5 font-display text-lg font-semibold text-paper">{{ $step[1] }}</h3>
                    <p class="mt-2.5 text-[0.86rem] leading-relaxed text-paper/60">{{ $step[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ TESTIMONIAL ═══ --}}
    <section class="mx-auto max-w-[1000px] px-4 sm:px-6 py-14 sm:py-24 text-center">
        <svg viewBox="0 0 48 48" class="mx-auto h-10 w-10 text-seal" aria-hidden="true"><circle cx="24" cy="24" r="21" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="24" cy="24" r="16.5" fill="none" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1.5 2.5"/><path d="M17 24.5l4.6 4.6L31 19.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <blockquote class="mt-6 sm:mt-8 font-display text-[1.25rem] font-medium leading-[1.35] tracking-[-0.01em] text-ink sm:text-[1.7rem] lg:text-[2.1rem]">&ldquo;Our annual results used to take three teachers a full week. With ResultMaker we published the same 400 marksheets — in our own school format — before lunch.&rdquo;</blockquote>
        <div class="mt-8 flex items-center justify-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-ink font-display text-paper">MR</span>
            <div class="text-left">
                <p class="font-semibold text-ink">Md. Mizanur Rahman</p>
                <p class="text-sm text-ink-soft">Headmaster, Shurjomukhi High School · Rajshahi</p>
            </div>
        </div>
    </section>



    {{-- ═══ FINAL CTA ═══ --}}
    <section class="mx-auto max-w-[1200px] px-4 sm:px-6 py-14 sm:py-24">
        <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-line bg-ink px-5 py-12 text-center text-paper sm:px-16 sm:py-16">
            <div class="pointer-events-none absolute inset-0 opacity-20" style="background-image:radial-gradient(circle at center,rgba(255,255,255,0.6) 1px,transparent 1.4px);background-size:22px 22px;mask-image:radial-gradient(ellipse 70% 90% at 50% 0%,#000,transparent 75%);"></div>
            <div class="relative">
                <h2 class="mx-auto max-w-2xl font-display text-[1.6rem] sm:text-[2.4rem] font-semibold leading-[1.08] tracking-[-0.01em] text-paper lg:text-[3rem]">Give your teachers their evenings back.</h2>
                <p class="mx-auto mt-5 max-w-xl text-lg text-paper/70">Set up your school, upload your marksheet, and publish your next exam with ResultMaker.</p>
                <div class="mt-7 sm:mt-9 flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center justify-center gap-3 sm:gap-4">
                    <a href="{{ route('register') }}" class="rounded-full bg-seal px-8 py-3 sm:py-3.5 text-sm sm:text-[0.95rem] font-semibold text-paper transition-colors duration-300 hover:bg-seal-deep text-center">Start free trial</a>
                    <a href="{{ route('login') }}" class="rounded-full border border-white/25 px-8 py-3 sm:py-3.5 text-sm sm:text-[0.95rem] font-semibold text-paper transition-colors duration-300 hover:bg-white/10 text-center">Sign in</a>
                </div>
            </div>
        </div>
    </section>
    </main>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="border-t border-line bg-paper">
        <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-10 sm:py-16">
            <div class="grid grid-cols-2 gap-8 sm:gap-10 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
                <div class="col-span-2 md:col-span-1">
                    <a href="/" class="group flex items-center gap-2.5">
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

    <!-- Alpine.js for tabs & mobile menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
