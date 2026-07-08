@extends('layouts.app')
@section('title', 'Subjects')

@section('content')
<div class="py-4 space-y-4">

    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" action="{{ route('subjects.index') }}" class="flex flex-wrap gap-3 w-full md:w-auto flex-1 mr-4">
            <select name="class_id" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
            <select name="section_id" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Sections</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                @endforeach
            </select>
            <div class="flex flex-1 md:flex-none gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search subject..."
                       class="w-full md:w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Search
                </button>
            </div>
        </form>
        <a href="{{ route('subjects.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Add Subject
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($subjects->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">📚</p>
                <p class="text-sm">No subjects yet. <a href="{{ route('subjects.create') }}" class="text-blue-600 hover:underline">Add one →</a></p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Subject</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Class / Section</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Components</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Total Marks</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($subjects as $subject)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $subject->name }}</div>
                            @if($subject->code)<div class="text-xs text-gray-400">{{ $subject->code }}</div>@endif
                            @if($subject->is_optional)<span class="text-xs bg-purple-100 text-purple-600 px-1.5 py-0.5 rounded">Optional</span>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $subject->schoolClass->name }} / {{ $subject->section->name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @if($subject->has_sub_subjects)
                                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-medium">
                                        {{ $subject->subSubjects->count() }} Papers
                                    </span>
                                @else
                                    @foreach($subject->exam_components as $compName => $config)
                                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                            {{ strtoupper($compName) }}: {{ $config['full'] }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            {{ $subject->total_full_marks }}
                            <span class="text-xs text-gray-400 font-normal">(pass: {{ $subject->total_pass_marks }})</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('subjects.copy', $subject) }}">
                                    @csrf
                                    <button class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-2 py-1 rounded transition-colors">Copy</button>
                                </form>
                                <a href="{{ route('subjects.edit', $subject) }}"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded transition-colors">Edit</a>
                                <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                                      @submit.prevent="deleteForm = $event.target; confirmDelete = true;">
                                    @csrf @method('DELETE')
                                    <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
