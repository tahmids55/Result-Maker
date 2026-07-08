{{-- Shared User Info Partial --}}
<div class="px-4 py-4 border-t border-slate-700">
    <div class="flex items-center justify-between">
        <div class="text-sm text-slate-300 truncate">{{ auth()->user()?->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-xs text-slate-400 hover:text-white transition-colors">Logout</button>
        </form>
    </div>
</div>
