@extends('layouts.app')
@section('title', 'Edit Teacher – ' . $teacher->name)

@section('content')
<div class="py-4 max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Edit Teacher: {{ $teacher->name }}</h2>

        <form method="POST" action="{{ route('teachers.update', $teacher) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teacher Name *</label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" value="{{ old('username', $teacher->username) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('username') border-red-400 @enderror">
                    @error('username') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('password') border-red-400 @enderror">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat if changing"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            {{-- Subject Assignment --}}
            <div class="border-t pt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Assign Subjects</label>
                <p class="text-xs text-gray-500 mb-3">Select the subjects this teacher is allowed to enter marks for.</p>

                @php
                    $grouped = $subjects->groupBy(fn($s) => ($s->schoolClass->name ?? 'Unknown') . ' – ' . ($s->section->name ?? ''));
                    $assignedIds = old('subject_ids', $teacher->assignedSubjects->pluck('id')->toArray());
                @endphp

                <div class="space-y-4 max-h-80 overflow-y-auto border rounded-lg p-3 bg-gray-50">
                    @foreach($grouped as $classSection => $subjectGroup)
                        <div>
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ $classSection }}</div>
                            <div class="space-y-1.5 pl-2">
                                @foreach($subjectGroup as $subject)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:bg-blue-50 rounded px-2 py-1 transition-colors">
                                        <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                               {{ in_array($subject->id, $assignedIds) ? 'checked' : '' }}
                                               class="rounded text-blue-600 focus:ring-blue-500">
                                        {{ $subject->name }}
                                        @if($subject->code)
                                            <span class="text-gray-400 text-xs">({{ $subject->code }})</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                    Update Teacher
                </button>
                <a href="{{ route('teachers.index') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-5 py-2 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
