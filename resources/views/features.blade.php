<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Features | ResultMaker</title>
    <meta name="description" content="Discover how ResultMaker automates the entire school result process.">
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
                <a href="{{ route('features') }}" class="text-sm font-medium text-ink transition-colors">Features</a>
                <a href="{{ url('/#workflow') }}" class="text-sm font-medium text-ink-soft transition-colors hover:text-ink">How it works</a>
                <a href="{{ url('/#templates') }}" class="text-sm font-medium text-ink-soft transition-colors hover:text-ink">Templates</a>
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
                <a href="{{ route('features') }}" class="text-sm font-medium text-ink">Features</a>
                <a href="{{ url('/#workflow') }}" class="text-sm font-medium text-ink-soft">How it works</a>
                <a href="{{ url('/#templates') }}" class="text-sm font-medium text-ink-soft">Templates</a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-ink-soft">Pricing</a>
                @guest <a href="{{ route('login') }}" class="text-sm font-medium text-ink-soft">Sign in</a> @endguest
            </div>
        </div>
    </header>

    <main class="flex-1 pt-[73px]">
        {{-- Faint ledger grid background for the page --}}
        <div class="fixed inset-0 pointer-events-none opacity-40 z-[-1]" style="background-image:linear-gradient(to right,rgba(27,36,50,0.04) 1px,transparent 1px),linear-gradient(to bottom,rgba(27,36,50,0.04) 1px,transparent 1px);background-size:32px 32px;"></div>

        {{-- ═══ HEADER ═══ --}}
        <section class="border-b border-line bg-paper-deep/40 py-14 sm:py-24">
            <div class="mx-auto max-w-[1200px] px-4 sm:px-6 text-center">
                <span class="font-mono text-[0.72rem] uppercase tracking-[0.28em] text-seal">Platform Features</span>
                <h1 class="mx-auto mt-4 max-w-3xl font-display text-[2rem] sm:text-[3.2rem] font-semibold leading-[1.06] tracking-[-0.01em] text-ink">Everything you need to automate results.</h1>
                <p class="mx-auto mt-5 max-w-2xl text-lg leading-relaxed text-ink-soft">ResultMaker replaces scattered spreadsheets and manual grading with a single, streamlined system that adapts to your school's unique rules and formats.</p>
            </div>
        </section>

        {{-- ═══ FEATURE LIST ═══ --}}
        <section class="py-16 sm:py-24">
            <div class="mx-auto max-w-[900px] px-4 sm:px-6">
                
                @php $detailedFeatures = [
                    ['id' => 'documents', 'tag' => 'Documents', 'title' => 'Your Word template, mapped by AI.', 'desc' => 'Most systems force you to use their ugly, generic marksheet designs. ResultMaker is different. You upload the exact Microsoft Word document your school currently uses. Our system automatically detects your placeholders (like [Name], [Roll], [GPA], [Bangla_Marks]) and maps them to our database. When you generate results, they come out looking exactly like your official school format.', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['id' => 'entry', 'tag' => 'Entry', 'title' => 'Live spreadsheet marks entry.', 'desc' => 'Teachers don\'t want to learn complex software. That\'s why our marks entry screen looks and acts exactly like Microsoft Excel. It features a real-time, responsive grid where every keystroke is saved instantly. Multiple teachers can be logged in simultaneously, entering marks for their assigned subjects without ever overwriting each other\'s work.', 'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    ['id' => 'calculation', 'tag' => 'Calculation', 'title' => 'Grades, GPA & merit, automatically.', 'desc' => 'Say goodbye to dragged formulas and manual errors. You define your school\'s grading boundaries once (e.g., 80-100 = A+, 70-79 = A) and ResultMaker handles the rest. It automatically computes subject totals, letter grades, grade points, overall GPA, pass/fail status, and generates accurate class merit rankings in real-time as marks are entered.', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['id' => 'ai-ocr', 'tag' => 'AI / OCR', 'title' => 'Snap a photo, skip the typing.', 'desc' => 'Still have teachers submitting handwritten mark slips? With our mobile-friendly OCR (Optical Character Recognition) tool, you can simply take a photo of a handwritten marksheet. ResultMaker\'s AI reads the table, extracts the roll numbers and marks, and drops them into the digital spreadsheet for you to review and confirm. It saves hours of manual data entry.', 'icon' => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z m8 9a3 3 0 100-6 3 3 0 000 6z'],
                    ['id' => 'output', 'tag' => 'Output', 'title' => 'Bulk marksheets & tabulation.', 'desc' => 'When marks are finalized, printing shouldn\'t take a week. With a single click, ResultMaker generates an individual Word or PDF marksheet for every student in a class, packaged neatly into a ZIP file. It also produces full-class tabulation sheets, comprehensive merit lists, and failure reports ready for the headmaster\'s review.', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
                    ['id' => 'notify', 'tag' => 'Notify', 'title' => 'SMS & WhatsApp to parents.', 'desc' => 'Stop fielding anxious phone calls from parents on results day. Once the headmaster approves the results, you can push a button to instantly send an SMS or WhatsApp message to every parent. The message can include their child\'s GPA, pass/fail status, and merit position, keeping families informed securely and instantly.', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                ]; @endphp

                <div class="space-y-24">
                    @foreach($detailedFeatures as $feature)
                    <div id="{{ $feature['id'] }}" class="scroll-mt-32 group relative">
                        <div class="absolute -inset-x-8 -inset-y-8 z-[-1] rounded-3xl bg-[#fdfbf6] opacity-0 transition-opacity duration-300 group-hover:opacity-100 hidden md:block"></div>
                        <div class="flex items-start gap-6 sm:gap-10">
                            <div class="hidden sm:flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-paper-deep/60 border border-line text-ink">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}" />
                                </svg>
                            </div>
                            <div>
                                <span class="font-mono text-[0.66rem] uppercase tracking-[0.2em] text-seal">{{ $feature['tag'] }}</span>
                                <h2 class="mt-3 font-display text-2xl sm:text-3xl font-semibold leading-tight text-ink">{{ $feature['title'] }}</h2>
                                <p class="mt-4 text-[0.95rem] leading-relaxed text-ink-soft">{{ $feature['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </section>

        {{-- ═══ CTA ═══ --}}
        <section class="border-t border-line bg-ink text-paper py-20 text-center">
            <h2 class="font-display text-3xl font-semibold text-paper">Ready to upgrade your workflow?</h2>
            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('register') }}" class="rounded-full bg-seal px-8 py-3.5 text-[0.95rem] font-semibold text-paper transition-colors duration-300 hover:bg-seal-deep">Start free trial</a>
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
