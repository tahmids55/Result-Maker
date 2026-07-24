<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResultMaker – Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center">
<div class="w-full max-w-md px-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        {{-- Logo --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">M</div>
            <div>
                <div class="text-2xl font-bold text-gray-900">ResultMaker</div>
                <div class="text-xs text-gray-500">Dynamic Result Management</div>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email or Username</label>
                <input type="text" name="login" value="{{ old('login') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm
                              @error('login') border-red-400 @enderror"
                       placeholder="you@example.com or username" required autofocus>
                @error('login')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                       placeholder="••••••••" required>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="rounded text-blue-600">
                <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors shadow-sm">
                Sign In
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-500">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">Sign up</a>
        </p>
    </div>
</div>
</body>
</html>
