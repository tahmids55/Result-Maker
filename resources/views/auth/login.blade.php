<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResultMaker – Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center relative overflow-hidden">
@php
    $bgFiles = \Illuminate\Support\Facades\File::exists(public_path('login_backgrounds')) 
                ? \Illuminate\Support\Facades\File::files(public_path('login_backgrounds')) 
                : [];
    $bgUrls = array_map(function($file) {
        return asset('login_backgrounds/' . $file->getFilename());
    }, $bgFiles);
@endphp
    <div id="bg-slider-container" class="absolute inset-0 z-[-1]">
        @forelse($bgUrls as $index => $url)
            <div class="absolute inset-0 bg-cover bg-center blur-md scale-105 transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" 
                 style="background-image: url('{{ $url }}');">
            </div>
        @empty
            <div class="absolute inset-0 bg-slate-900"></div>
        @endforelse
    </div>
<div class="w-full max-w-lg px-4 relative mt-10">
    <div class="bg-black/50 backdrop-blur-2xl rounded-[32px] shadow-[0_20px_50px_rgba(0,0,0,0.5)] p-8 pt-0 border border-white/10">
        {{-- Floating Top Icon --}}
        <div class="flex justify-center mb-6" style="transform: translateY(-32px);">
            <div class="w-20 h-20 bg-black/60 rounded-2xl flex items-center justify-center shadow-2xl border border-white/10 p-2">
                <img src="{{ asset('large.png') }}" alt="ResultMaker Logo" class="w-full h-full object-contain invert">
            </div>
        </div>

        {{-- Text --}}
        <div class="text-center mb-8 -mt-2">
            <h1 class="text-[28px] font-bold text-white mb-2 tracking-tight">Sign in with <br> email or username</h1>
            <p class="text-sm text-gray-400 px-4 leading-relaxed">Login to manage marks, results, and student data together. For free</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            
            {{-- Email Input --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                      <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                </div>
                <input type="text" name="login" value="{{ old('login') }}"
                       class="w-full pl-11 pr-4 py-3.5 bg-black/40 text-white border-none rounded-2xl focus:ring-1 focus:ring-gray-400 focus:bg-black/60 outline-none transition text-sm placeholder-gray-500
                              @error('login') ring-1 ring-red-500/50 @enderror"
                       placeholder="Email or username" required autofocus>
            </div>
            @error('login')
                <p class="mt-1 text-xs text-red-400 pl-1">{{ $message }}</p>
            @enderror

            {{-- Password Input --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="password" name="password" id="password"
                       class="w-full pl-11 pr-11 py-3.5 bg-black/40 text-white border-none rounded-2xl focus:ring-1 focus:ring-gray-400 focus:bg-black/60 outline-none transition text-sm placeholder-gray-500"
                       placeholder="Password" required>
                {{-- Visibility Toggle --}}
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-gray-500 hover:text-gray-300" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'; this.innerHTML = p.type === 'password' ? '<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z\' clip-rule=\'evenodd\' /><path d=\'M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z\' /></svg>' : '<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path d=\'M10 12a2 2 0 100-4 2 2 0 000 4z\' /><path fill-rule=\'evenodd\' d=\'M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z\' clip-rule=\'evenodd\' /></svg>'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                      <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                    </svg>
                </div>
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex justify-between items-center mt-2 px-1">
                <label class="flex items-center space-x-2 cursor-pointer group">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded bg-black/40 border-none text-blue-500 focus:ring-0 focus:ring-offset-0">
                    <span class="text-sm text-gray-400 group-hover:text-gray-300 transition">Remember me</span>
                </label>
                <a href="#" class="text-sm font-medium text-gray-300 hover:text-white transition">Forgot password?</a>
            </div>

            <button type="submit"
                    class="w-full mt-2 bg-white hover:bg-gray-100 text-gray-900 font-bold py-3.5 rounded-2xl transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)] hover:shadow-[0_0_20px_rgba(255,255,255,0.2)]">
                Get Started
            </button>
        </form>

        {{-- Divider --}}
        <div class="mt-8 flex items-center justify-center space-x-4">
            <div class="h-px w-full bg-white/10"></div>
            <span class="text-xs text-gray-400 font-medium tracking-wide whitespace-nowrap">Or sign in with</span>
            <div class="h-px w-full bg-white/10"></div>
        </div>

        {{-- Social Placeholders --}}
        <div class="mt-6 grid grid-cols-3 gap-3">
            <button type="button" class="flex justify-center items-center py-2.5 bg-black/40 border border-white/5 rounded-2xl hover:bg-black/60 transition group">
                <svg class="h-5 w-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            </button>
            <button type="button" class="flex justify-center items-center py-2.5 bg-black/40 border border-white/5 rounded-2xl hover:bg-black/60 transition group">
                <svg class="h-5 w-5 text-[#1877F2] group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </button>
            <button type="button" class="flex justify-center items-center py-2.5 bg-black/40 border border-white/5 rounded-2xl hover:bg-black/60 transition group">
                <svg class="h-5 w-5 text-white group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.04 2.26-.79 3.59-.76 1.56.04 2.87.73 3.65 1.86-3.13 1.86-2.61 5.98.53 7.21-.71 1.73-1.63 3.39-2.85 4.14V20.28zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>
            </button>
        </div>
    </div>
</div>
<script>
    const backgrounds = document.querySelectorAll('#bg-slider-container > div');
    if (backgrounds.length > 1) {
        let currentIndex = 0;
        setInterval(() => {
            backgrounds[currentIndex].classList.remove('opacity-100');
            backgrounds[currentIndex].classList.add('opacity-0');
            
            currentIndex = (currentIndex + 1) % backgrounds.length;
            
            backgrounds[currentIndex].classList.remove('opacity-0');
            backgrounds[currentIndex].classList.add('opacity-100');
        }, 5000);
    }
</script>
</body>
</html>
