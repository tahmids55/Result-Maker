<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ResultMaker') – Dynamic Result Management</title>

    {{-- Google Fonts: Inter, Outfit, JetBrains Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js (bundled with Livewire 3) --}}

    {{-- Livewire --}}
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-thin::-webkit-scrollbar { height: 6px; width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: #f1f5f9; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }

        /* Toast animation */
        .toast-enter { animation: slideInRight 0.3s ease-out; }
        .toast-leave { animation: slideOutRight 0.3s ease-in forwards; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }

        /* Mobile sidebar animation */
        .sidebar-enter { animation: slideInLeft 0.2s ease-out; }
        @keyframes slideInLeft { from { transform: translateX(-100%); } to { transform: translateX(0); } }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false, confirmDelete: false, deleteForm: null }">

<div class="flex h-full">

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
        {{-- Backdrop --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-gray-900/50"></div>

        {{-- Mobile Sidebar Panel --}}
        <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-200 transform"
             x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-200 transform"
             x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="relative flex flex-col w-72 bg-slate-800 h-full">

            {{-- Close button --}}
            <div class="absolute top-4 right-4">
                <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
                <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center font-bold text-lg text-white">M</div>
                <span class="text-xl font-semibold tracking-tight text-white">ResultMaker</span>
            </div>

            {{-- Nav --}}
            @include('layouts._nav')

            {{-- User --}}
            @include('layouts._user')
        </div>
    </div>

    {{-- Desktop Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-64 bg-slate-800 text-white flex-shrink-0 print:hidden">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
            <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center font-bold text-lg">M</div>
            <span class="text-xl font-semibold tracking-tight">ResultMaker</span>
        </div>

        {{-- Nav --}}
        @include('layouts._nav')

        {{-- User --}}
        @include('layouts._user')
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-4 lg:px-6 py-3 flex items-center justify-between print:hidden">
            <div class="flex items-center gap-3">
                {{-- Hamburger button for mobile --}}
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span class="hidden sm:inline">{{ now()->format('d M Y') }}</span>
                @php
                    $activeExam = cache()->remember('active_exam_' . auth()->id(), 300, function () {
                        return \App\Models\Exam::active()->latest()->first();
                    });
                @endphp
                @if($activeExam)
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                        Active: {{ $activeExam->name }} {{ $activeExam->year }}
                    </span>
                @endif
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto px-4 lg:px-6 pb-6 pt-4 print:p-0 print:overflow-visible">
            {{-- Breadcrumbs --}}
            @if(!request()->routeIs('dashboard'))
                <nav class="flex text-xs text-gray-500 mb-4 print:hidden" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                                🏠 Home
                            </a>
                        </li>
                        @php
                            $segments = request()->segments();
                            $url = '';
                        @endphp
                        @foreach($segments as $segment)
                            @php $url .= '/' . $segment; @endphp
                            <li>
                                <div class="flex items-center">
                                    <span class="mx-2 text-gray-400">/</span>
                                    <a href="{{ url($url) }}" class="capitalize hover:text-blue-600 transition-colors">{{ str_replace('-', ' ', $segment) }}</a>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Toast Notifications --}}
<div class="fixed top-4 right-4 z-50 space-y-2 print:hidden" style="max-width: 380px;">
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="toast-enter" x-transition:leave="toast-leave"
             class="flex items-center gap-3 p-4 bg-green-600 text-white rounded-xl shadow-lg">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-medium flex-1">{{ session('success') }}</span>
            <button @click="show = false" class="text-green-200 hover:text-white"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             x-transition:enter="toast-enter" x-transition:leave="toast-leave"
             class="flex items-center gap-3 p-4 bg-red-600 text-white rounded-xl shadow-lg">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium flex-1">{{ session('error') }}</span>
            <button @click="show = false" class="text-red-200 hover:text-white"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    @endif
    @if(session('warning'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="toast-enter" x-transition:leave="toast-leave"
             class="flex items-center gap-3 p-4 bg-yellow-500 text-white rounded-xl shadow-lg">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <span class="text-sm font-medium flex-1">{{ session('warning') }}</span>
            <button @click="show = false" class="text-yellow-200 hover:text-white"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div x-show="confirmDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div x-show="confirmDelete" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="confirmDelete = false" class="fixed inset-0 bg-gray-900/50"></div>
    <div x-show="confirmDelete" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative bg-white rounded-2xl shadow-xl p-6 max-w-sm mx-4 z-10">
        <div class="text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Delete</h3>
            <p class="text-sm text-gray-500 mb-6">This action cannot be undone. Are you sure you want to delete this item?</p>
            <div class="flex gap-3 justify-center">
                <button @click="confirmDelete = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                <button @click="if(deleteForm) deleteForm.submit(); confirmDelete = false;"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
