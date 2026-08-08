@extends('layouts.app')
@section('title', 'Activity History')

@php
if (!function_exists('formatSubjectDetailsDiff')) {
    function formatSubjectDetailsDiff($oldArr, $newArr) {
        if (!is_array($oldArr) || !is_array($newArr)) return [];
        $oldMap = [];
        foreach ($oldArr as $s) {
            $oldMap[$s['subject_id'] ?? $s['subject_name'] ?? ''] = $s;
        }
        $changes = [];
        foreach ($newArr as $newS) {
            $key = $newS['subject_id'] ?? $newS['subject_name'] ?? '';
            $subjectName = $newS['subject_name'] ?? 'Unknown Subject';
            $oldS = $oldMap[$key] ?? null;
            if (!$oldS) continue;
            
            if (isset($newS['components']) && isset($oldS['components'])) {
                foreach ($newS['components'] as $compName => $compData) {
                    $oldCompData = $oldS['components'][$compName] ?? null;
                    $newMark = $compData['obtained'] ?? 0;
                    $oldMark = $oldCompData['obtained'] ?? 0;
                    if ($oldCompData && $oldMark != $newMark) {
                        $cName = strtoupper($compName);
                        $changes[] = "{$subjectName} ({$cName}): {$oldMark} ➔ {$newMark}";
                    }
                }
            }
            if (isset($newS['sub_subjects']) && isset($oldS['sub_subjects'])) {
                $oldSubMap = [];
                foreach ($oldS['sub_subjects'] as $ss) {
                    $oldSubMap[$ss['sub_subject_id'] ?? $ss['name'] ?? ''] = $ss;
                }
                foreach ($newS['sub_subjects'] as $newSs) {
                    $ssKey = $newSs['sub_subject_id'] ?? $newSs['name'] ?? '';
                    $ssName = $newSs['name'] ?? 'Unknown Paper';
                    $oldSs = $oldSubMap[$ssKey] ?? null;
                    if ($oldSs && isset($newSs['components']) && isset($oldSs['components'])) {
                        foreach ($newSs['components'] as $compName => $compData) {
                            $oldCompData = $oldSs['components'][$compName] ?? null;
                            $newMark = $compData['obtained'] ?? 0;
                            $oldMark = $oldCompData['obtained'] ?? 0;
                            if ($oldCompData && $oldMark != $newMark) {
                                $cName = strtoupper($compName);
                                $changes[] = "{$subjectName} {$ssName} ({$cName}): {$oldMark} ➔ {$newMark}";
                            }
                        }
                    }
                }
            }
        }
        return $changes;
    }
}

if (!function_exists('resolveIdToName')) {
    function resolveIdToName($field, $val) {
        if (!is_numeric($val) || $val <= 0 || !\Illuminate\Support\Str::endsWith($field, '_id')) {
            return null;
        }
        static $modelCache = [];
        $relation = str_replace('_id', '', $field);
        
        $customMap = [
            'class' => 'SchoolClass',
        ];
        
        $modelName = $customMap[$relation] ?? \Illuminate\Support\Str::studly($relation);
        $modelClass = '\\App\\Models\\' . $modelName;
        
        $cacheKey = $modelClass . '_' . $val;
        if (array_key_exists($cacheKey, $modelCache)) {
            return $modelCache[$cacheKey];
        }
        
        if (class_exists($modelClass)) {
            try {
                if (class_basename($modelClass) === 'Student') {
                    $record = $modelClass::with(['schoolClass', 'section'])->find($val);
                } else {
                    $record = $modelClass::find($val);
                }

                if ($record) {
                    $name = $record->name ?? $record->title ?? $record->first_name ?? $record->username ?? null;
                    if ($name) {
                        if (class_basename($modelClass) === 'Student') {
                            $cName = $record->schoolClass->name ?? '-';
                            $sName = $record->section->name ?? '-';
                            $roll = $record->roll ?? '-';
                            $name .= " (Class: $cName | Sec: $sName | Roll: $roll)";
                        }
                        $result = $name . " [#$val]";
                        $modelCache[$cacheKey] = $result;
                        return $result;
                    }
                }
            } catch (\Exception $e) {}
        }
        
        $modelCache[$cacheKey] = null;
        return null;
    }
}
@endphp

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
        <div class="flex items-center gap-3">
            <span class="text-xs font-medium px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full border border-gray-200">
                {{ $activities->total() }} Entries
            </span>
            @if($activities->total() > 0)
                <form method="POST" action="{{ route('activity-log.clear') }}" onsubmit="return confirm('Are you sure you want to completely clear the activity history? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 bg-red-50 text-red-600 rounded-md border border-red-200 hover:bg-red-100 transition-colors flex items-center gap-1 shadow-sm">
                        🗑️ Clear History
                    </button>
                </form>
            @endif
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
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-2.5 px-4 w-24">Event</th>
                            <th class="py-2.5 px-4">Action & Description</th>
                            <th class="py-2.5 px-4 w-36">Target Entity</th>
                            <th class="py-2.5 px-4 w-40">User</th>
                            <th class="py-2.5 px-4 w-44">Date & Time</th>
                            <th class="py-2.5 px-4 w-24 text-right">Details</th>
                        </tr>
                    </thead>
                    @foreach($activities as $activity)
                            @php
                                $eventBadge = [
                                    'created' => ['bg' => 'bg-green-50 text-green-700 border-green-200', 'label' => 'Created', 'icon' => '➕'],
                                    'updated' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Updated', 'icon' => '✏️'],
                                    'deleted' => ['bg' => 'bg-red-50 text-red-700 border-red-200', 'label' => 'Deleted', 'icon' => '🗑️'],
                                ][$activity->event] ?? ['bg' => 'bg-gray-50 text-gray-700 border-gray-200', 'label' => ucfirst($activity->event), 'icon' => '📌'];

                                $modelName = class_basename($activity->subject_type ?? 'Unknown');
                                $oldProps = $activity->properties['old'] ?? [];
                                $newProps = $activity->properties['attributes'] ?? [];
                                $hasChanges = !empty($oldProps) || !empty($newProps);
                                $canExpand = ($hasChanges && $activity->event === 'updated') || ($activity->event === 'created' && !empty($newProps)) || ($activity->event === 'deleted' && !empty($oldProps));
                            @endphp

                            <tbody x-data="{ open: false }" class="border-b border-gray-100 last:border-0">
                                {{-- Main Log Row --}}
                                <tr @if($canExpand) @click="open = !open" class="hover:bg-gray-50 transition-colors cursor-pointer" @else class="hover:bg-gray-50 transition-colors" @endif>
                                    {{-- Event Badge --}}
                                    <td class="py-2 px-4 align-middle">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border {{ $eventBadge['bg'] }}">
                                            <span>{{ $eventBadge['icon'] }}</span>
                                            <span>{{ $eventBadge['label'] }}</span>
                                        </span>
                                    </td>

                                    {{-- Action Description --}}
                                    <td class="py-2 px-4 align-middle font-medium text-gray-800 text-[12px]">
                                        {{ $activity->description }}
                                    </td>

                                    {{-- Target Entity --}}
                                    <td class="py-2 px-4 align-middle">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-mono border border-gray-200">
                                            {{ $modelName }} #{{ $activity->subject_id }}
                                        </span>
                                    </td>

                                    {{-- Performed By User --}}
                                    <td class="py-2 px-4 align-middle">
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
                                    <td class="py-2 px-4 align-middle text-[11px] text-gray-500 whitespace-nowrap">
                                        <span>{{ $activity->created_at->format('d M Y, h:i A') }}</span>
                                        <span class="text-gray-400 text-[10px] ml-1">({{ $activity->created_at->diffForHumans() }})</span>
                                    </td>

                                    {{-- Action / Expand Button --}}
                                    <td class="py-2 px-4 align-middle text-right">
                                        @if($canExpand && $activity->event === 'updated')
                                            <button type="button" 
                                                    class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2 py-0.5 rounded transition-colors">
                                                <span x-text="open ? 'Close' : 'More({{ count($oldProps) }})'"></span>
                                                <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        @elseif($canExpand && $activity->event === 'created')
                                            <button type="button" 
                                                    class="inline-flex items-center gap-1 text-[10px] font-semibold text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 border border-green-200 px-2 py-0.5 rounded transition-colors">
                                                <span x-text="open ? 'Close' : 'More'"></span>
                                                <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        @elseif($canExpand && $activity->event === 'deleted')
                                            <button type="button" 
                                                    class="inline-flex items-center gap-1 text-[10px] font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 border border-red-200 px-2 py-0.5 rounded transition-colors">
                                                <span x-text="open ? 'Close' : 'More'"></span>
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
                                                                <th class="py-1.5 px-3 w-[37.5%] text-green-700 bg-green-50/50">New Value</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100 font-mono">
                                                            @foreach($oldProps as $field => $oldVal)
                                                                @php
                                                                    $newVal = $newProps[$field] ?? '—';
                                                                    if (in_array($field, ['updated_at', 'created_at', 'remember_token', 'password', 'user_id'])) continue;
                                                                    
                                                                    $resolvedOld = resolveIdToName($field, $oldVal);
                                                                    $resolvedNew = resolveIdToName($field, $newVal);
                                                                    
                                                                    $isOldArr = is_array($oldVal);
                                                                    $isNewArr = is_array($newVal);

                                                                    $specialMarksDiff = null;
                                                                    if ($field === 'subject_details' && $isOldArr && $isNewArr) {
                                                                        $specialMarksDiff = formatSubjectDetailsDiff($oldVal, $newVal);
                                                                    }

                                                                    $displayOld = $resolvedOld ?? ($isOldArr ? json_encode($oldVal, JSON_PRETTY_PRINT) : (string) $oldVal);
                                                                    $displayNew = $resolvedNew ?? ($isNewArr ? json_encode($newVal, JSON_PRETTY_PRINT) : (string) $newVal);
                                                                    
                                                                    if (!$isOldArr && !$resolvedOld) $displayOld = \Illuminate\Support\Str::limit($displayOld, 120);
                                                                    if (!$isNewArr && !$resolvedNew) $displayNew = \Illuminate\Support\Str::limit($displayNew, 120);
                                                                @endphp
                                                                <tr>
                                                                    <td class="py-1 px-3 font-sans font-medium text-gray-700 align-top">{{ str_replace('_', ' ', ucfirst($field)) }}</td>
                                                                    @if($specialMarksDiff !== null)
                                                                        <td colspan="2" class="py-1 px-3 text-gray-800 bg-gray-50 break-all align-top">
                                                                            @if(empty($specialMarksDiff))
                                                                                <span class="text-[10px] text-gray-500 italic">Subjects regenerated (no marks changed)</span>
                                                                            @else
                                                                                <ul class="list-disc list-inside text-[11px] font-semibold">
                                                                                    @foreach($specialMarksDiff as $diffChange)
                                                                                        <li>{{ $diffChange }}</li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            @endif
                                                                        </td>
                                                                    @else
                                                                        <td class="py-1 px-3 text-red-600 bg-red-50/30 break-all align-top">
                                                                            @if($isOldArr && !$resolvedOld)
                                                                                <pre class="whitespace-pre-wrap text-[10px] bg-red-100/30 p-2 rounded max-h-48 overflow-y-auto mt-1 mb-1 border border-red-200/50">{{ $displayOld }}</pre>
                                                                            @else
                                                                                {{ $displayOld ?: '—' }}
                                                                            @endif
                                                                        </td>
                                                                        <td class="py-1 px-3 text-green-600 bg-green-50/30 break-all align-top">
                                                                            @if($isNewArr && !$resolvedNew)
                                                                                <pre class="whitespace-pre-wrap text-[10px] bg-green-100/30 p-2 rounded max-h-48 overflow-y-auto mt-1 mb-1 border border-green-200/50">{{ $displayNew }}</pre>
                                                                            @else
                                                                                {{ $displayNew ?: '—' }}
                                                                            @endif
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                            
                                                            {{-- Show Unchanged Context Properties --}}
                                                            @if($activity->subject)
                                                                @php
                                                                    $unchangedCount = 0;
                                                                    $subjectAttrs = $activity->subject->getAttributes();
                                                                @endphp
                                                                @foreach($subjectAttrs as $field => $val)
                                                                    @php
                                                                        if (array_key_exists($field, $oldProps) || !in_array($field, ['student_id', 'subject_id', 'exam_id'])) continue;
                                                                        $unchangedCount++;
                                                                        $resolved = resolveIdToName($field, $val);
                                                                        $isArr = is_array($val);
                                                                        $display = $resolved ?? ($isArr ? json_encode($val, JSON_PRETTY_PRINT) : (string) $val);
                                                                        if (!$isArr && !$resolved) $display = \Illuminate\Support\Str::limit($display, 120);
                                                                    @endphp
                                                                    <tr class="opacity-75 bg-gray-50">
                                                                        <td class="py-1 px-3 font-sans font-medium text-gray-500 align-top">{{ str_replace('_', ' ', ucfirst($field)) }}</td>
                                                                        <td colspan="2" class="py-1 px-3 text-gray-500 break-all align-top text-center italic">
                                                                            {{ $display ?: '—' }} (Unchanged Context)
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                @elseif($activity->event === 'created' && !empty($newProps))
                                                    <table class="w-full text-left text-[11px]">
                                                        <thead>
                                                            <tr class="bg-green-50 text-green-800 font-semibold border-b border-green-100 text-[10px] uppercase">
                                                                <th class="py-1.5 px-3 w-1/3">Field</th>
                                                                <th class="py-1.5 px-3 w-2/3">Value</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-green-50 font-mono">
                                                            @foreach($newProps as $field => $val)
                                                                @php
                                                                    if (in_array($field, ['updated_at', 'created_at', 'remember_token', 'password', 'user_id'])) continue;
                                                                    
                                                                    $resolved = resolveIdToName($field, $val);
                                                                    $isArr = is_array($val);
                                                                    $display = $resolved ?? ($isArr ? json_encode($val, JSON_PRETTY_PRINT) : (string) $val);
                                                                    
                                                                    if (!$isArr && !$resolved) $display = \Illuminate\Support\Str::limit($display, 150);
                                                                @endphp
                                                                <tr>
                                                                    <td class="py-1 px-3 font-sans font-medium text-gray-700 align-top">{{ str_replace('_', ' ', ucfirst($field)) }}</td>
                                                                    <td class="py-1 px-3 text-green-700 break-all align-top">
                                                                        @if($isArr && !$resolved)
                                                                            <pre class="whitespace-pre-wrap text-[10px] bg-green-50/50 p-2 rounded max-h-48 overflow-y-auto mt-1 mb-1 border border-green-100">{{ $display }}</pre>
                                                                        @else
                                                                            {{ $display ?: '—' }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @elseif($activity->event === 'deleted' && !empty($oldProps))
                                                    <table class="w-full text-left text-[11px]">
                                                        <thead>
                                                            <tr class="bg-red-50 text-red-800 font-semibold border-b border-red-100 text-[10px] uppercase">
                                                                <th class="py-1.5 px-3 w-1/3">Field</th>
                                                                <th class="py-1.5 px-3 w-2/3">Deleted Value</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-red-50 font-mono">
                                                            @foreach($oldProps as $field => $val)
                                                                @php
                                                                    if (in_array($field, ['updated_at', 'created_at', 'remember_token', 'password', 'user_id'])) continue;
                                                                    
                                                                    $resolved = resolveIdToName($field, $val);
                                                                    $isArr = is_array($val);
                                                                    $display = $resolved ?? ($isArr ? json_encode($val, JSON_PRETTY_PRINT) : (string) $val);
                                                                    
                                                                    if (!$isArr && !$resolved) $display = \Illuminate\Support\Str::limit($display, 150);
                                                                @endphp
                                                                <tr>
                                                                    <td class="py-1 px-3 font-sans font-medium text-gray-700 align-top">{{ str_replace('_', ' ', ucfirst($field)) }}</td>
                                                                    <td class="py-1 px-3 text-red-700 break-all align-top">
                                                                        @if($isArr && !$resolved)
                                                                            <pre class="whitespace-pre-wrap text-[10px] bg-red-50/50 p-2 rounded max-h-48 overflow-y-auto mt-1 mb-1 border border-red-100">{{ $display }}</pre>
                                                                        @else
                                                                            {{ $display ?: '—' }}
                                                                        @endif
                                                                    </td>
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
