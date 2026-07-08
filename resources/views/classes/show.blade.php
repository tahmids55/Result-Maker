@extends('layouts.app')
@section('title', $class->name . ' – Details')

@section('content')
<div class="py-4 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">{{ $class->name }}</h2>
            <p class="text-sm text-gray-500">{{ $class->sections->count() }} sections · {{ $class->student_count }} students</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sections.create') }}?class_id={{ $class->id }}"
               class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Add Section
            </a>
            <a href="{{ route('classes.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">
                ← Back
            </a>
        </div>
    </div>

    @forelse($class->sections as $section)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-200">
            <div class="font-semibold text-gray-800">Section {{ $section->name }}</div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500">{{ $section->student_count }} students</span>
                <a href="{{ route('students.create') }}" class="text-xs text-blue-600 hover:underline">+ Student</a>
                <a href="{{ route('sections.edit', $section) }}" class="text-xs text-gray-500 hover:text-gray-700">Edit</a>
            </div>
        </div>

        @if($section->students->isEmpty())
            <div class="px-5 py-6 text-center text-sm text-gray-400">No students in this section yet.</div>
        @else
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Roll</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Father</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Session</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($section->students->sortBy('roll') as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono text-gray-600 text-xs">{{ $student->roll }}</td>
                    <td class="px-4 py-2 font-medium text-gray-800">
                        <a href="{{ route('students.show', $student) }}" class="hover:text-blue-600">{{ $student->name }}</a>
                    </td>
                    <td class="px-4 py-2 text-gray-500 text-xs">{{ $student->father_name ?? '–' }}</td>
                    <td class="px-4 py-2 text-gray-500 text-xs">{{ $student->session ?? '–' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @empty
        <div class="bg-white rounded-xl border border-dashed border-gray-300 p-12 text-center text-gray-400">
            <p class="text-3xl mb-2">📂</p>
            <p class="text-sm">No sections yet. <a href="{{ route('sections.create') }}" class="text-blue-600 hover:underline">Add a section →</a></p>
        </div>
    @endforelse
</div>
@endsection
