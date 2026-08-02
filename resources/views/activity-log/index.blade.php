@extends('layouts.app')
@section('title', 'Activity History')

@section('content')
<div class="py-4 space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span>📋</span> Activity History
            </h2>
            <p class="text-[13px] text-gray-500 mt-0.5">Audit log of system actions and data changes</p>
        </div>
        <div>
            <span class="text-xs font-medium px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full border border-gray-200">
                {{ $activities->total() }} Entries
            </span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
        <form method="GET" action="{{ route('activity-log.index') }}" class="flex flex-col gap-3">
            <!-- Top Row: Search & Actions -->
            <div class="flex gap-2">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search activity by description or ID..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit" title="Search" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <a href="{{ route('activity-log.index') }}" title="Clear Filters" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                    ✕
                </a>
            </div>

            <!-- Bottom Row: Dropdown Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <select name="event" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Events</option>
                    <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>✅ Created</option>
                    <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>✏️ Updated</option>
                    <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>🗑️ Deleted</option>
                </select>

                <select name="model" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Models</option>
                    @foreach($modelTypes as $type)
                        <option value="{{ $type['full'] }}" {{ request('model') === $type['full'] ? 'selected' : '' }}>{{ $type['short'] }}</option>
                    @endforeach
                </select>

                <select name="user_id" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Activity Audit Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($activities->isEmpty())
            <div class="py-12 text-center text-gray-400">
                <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-2 text-xl">📭</div>
                <p class="text-xs font-medium text-gray-600">No activity logs found</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Try clearing filters or performing actions in the app.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-500 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-2.5 px-3.5 w-24">Event</th>
                            <th class="py-2.5 px-3.5">Action & Description</th>
                            <th class="py-2.5 px-3.5 w-36">Target Entity</th>
                            <th class="py-2.5 px-3.5 w-40">User</th>
                            <th class="py-2.5 px-3.5 w-44">Date & Time</th>
                            <th class="py-2.5 px-3.5 w-24 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($activities as $activity)
                            @php
                                $eventBadge = [
                                    'created' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Created', 'icon' => '➕'],
                                    'updated' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Updated', 'icon' => '✏️'],
                                    'deleted' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Deleted', 'icon' => '🗑️'],
                                ][$activity->event] ?? ['bg' => 'bg-gray-50 text-gray-700 border-gray-200', 'label' => ucfirst($activity->event), 'icon' => '📌'];

                                $modelName = class_basename($activity->subject_type ?? 'Unknown');
                                $oldProps = $activity->properties['old'] ?? [];
                                $newProps = $activity->properties['attributes'] ?? [];
                                $hasChanges = !empty($oldProps) || !empty($newProps);
                            @endphp

                            <tbody x-data="{ open: false }" class="border-b border-gray-100 last:border-0">
                                {{-- Main Log Row --}}
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    {{-- Event Badge --}}
                                    <td class="py-2 px-3.5 align-middle">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border {{ $eventBadge['bg'] }}">
                                            <span>{{ $eventBadge['icon'] }}</span>
                                            <span>{{ $eventBadge['label'] }}</span>
                                        </span>
                                    </td>

                                    {{-- Action Description --}}
                                    <td class="py-2 px-3.5 align-middle font-medium text-gray-800 text-[12px]">
                                        {{ $activity->description }}
                                    </td>

                                    {{-- Target Entity --}}
                                    <td class="py-2 px-3.5 align-middle">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-mono border border-gray-200">
                                            {{ $modelName }} #{{ $activity->subject_id }}
                                        </span>
                                    </td>

                                    {{-- Performed By User --}}
                                    <td class="py-2 px-3.5 align-middle">
                                        @if($activity->causer)
                                            <div class="flex items-center gap-1.5 text-[11px]">
                                                <span class="font-medium text-gray-700 truncate max-w-[100px]">{{ $activity->causer->name }}</span>
                                                <span class="px-1 py-0.2 rounded text-[9px] font-bold uppercase tracking-wider {{ $activity->causer->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ $activity->causer->role }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-[11px]">System</span>
                                        @endif
                                    </td>

                                    {{-- Date & Time --}}
                                    <td class="py-2 px-3.5 align-middle text-[11px] text-gray-500 whitespace-nowrap">
                                        <span>{{ $activity->created_at->format('d M Y, h:i A') }}</span>
                                        <span class="text-gray-400 text-[10px] ml-1">({{ $activity->created_at->diffForHumans() }})</span>
                                    </td>

                                    {{-- Action / Expand Button --}}
                                    <td class="py-2 px-3.5 align-middle text-right">
                                        @if($hasChanges && $activity->event === 'updated')
                                            <button @click="open = !open" 
                                                    class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2 py-0.5 rounded transition-colors">
                                                <span x-text="open ? 'Close' : 'Diff ({{ count($oldProps) }})'"></span>
                                                <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        @elseif($activity->event === 'created' && !empty($newProps))
                                            <button @click="open = !open" 
                                                    class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded transition-colors">
                                                <span x-text="open ? 'Close' : 'Details'"></span>
                                                <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        @else
                                            <span class="text-gray-300 text-[10px]">—</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Expandable Details Row --}}
                                @if($hasChanges)
                                    <tr x-show="open" x-cloak class="bg-gray-50/90">
                                        <td colspan="6" class="p-3">
                                            <div class="rounded-lg border border-gray-200 bg-white shadow-inner overflow-hidden">
                                                @if($activity->event === 'updated' && !empty($oldProps))
                                                    <table class="w-full text-left text-[11px]">
                                                        <thead>
                                                            <tr class="bg-gray-100 text-gray-500 font-semibold border-b border-gray-200 text-[10px] uppercase">
                                                                <th class="py-1.5 px-3 w-1/4">Field</th>
                                                                <th class="py-1.5 px-3 w-[37.5%] text-red-700 bg-red-50/50">Old Value</th>
                                                                <th class="py-1.5 px-3 w-[37.5%] text-emerald-700 bg-emerald-50/50">New Value</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100 font-mono">
                                                            @foreach($oldProps as $field => $oldVal)
                                                                @php
                                                                    $newVal = $newProps[$field] ?? '—';
                                                                    if (in_array($field, ['updated_at', 'created_at', 'remember_token', 'password'])) continue;
                                                                    $displayOld = is_array($oldVal) ? json_encode($oldVal, JSON_PRETTY_PRINT) : (string) $oldVal;
                                                                    $displayNew = is_array($newVal) ? json_encode($newVal, JSON_PRETTY_PRINT) : (string) $newVal;
                                                                    $displayOld = \Illuminate\Support\Str::limit($displayOld, 120);
                                                                    $displayNew = \Illuminate\Support\Str::limit($displayNew, 120);
                                                                @endphp
                                                                <tr>
                                                                    <td class="py-1 px-3 font-sans font-medium text-gray-700">{{ str_replace('_', ' ', ucfirst($field)) }}</td>
                                                                    <td class="py-1 px-3 text-red-600 bg-red-50/30 break-all">{{ $displayOld ?: '—' }}</td>
                                                                    <td class="py-1 px-3 text-emerald-600 bg-emerald-50/30 break-all">{{ $displayNew ?: '—' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @elseif($activity->event === 'created' && !empty($newProps))
                                                    <table class="w-full text-left text-[11px]">
                                                        <thead>
                                                            <tr class="bg-emerald-50 text-emerald-800 font-semibold border-b border-emerald-100 text-[10px] uppercase">
                                                                <th class="py-1.5 px-3 w-1/3">Field</th>
                                                                <th class="py-1.5 px-3 w-2/3">Value</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-emerald-50 font-mono">
                                                            @foreach($newProps as $field => $val)
                                                                @php
                                                                    if (in_array($field, ['updated_at', 'created_at', 'remember_token', 'password'])) continue;
                                                                    $display = is_array($val) ? json_encode($val, JSON_PRETTY_PRINT) : (string) $val;
                                                                    $display = \Illuminate\Support\Str::limit($display, 150);
                                                                @endphp
                                                                <tr>
                                                                    <td class="py-1 px-3 font-sans font-medium text-gray-700">{{ str_replace('_', ' ', ucfirst($field)) }}</td>
                                                                    <td class="py-1 px-3 text-emerald-700 break-all">{{ $display ?: '—' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
