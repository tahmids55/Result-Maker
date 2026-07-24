@extends('layouts.app')
@section('title', 'Teachers')

@section('content')
<div class="py-4 space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $teachers->total() }} teachers</p>
        <a href="{{ route('teachers.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Add Teacher
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($teachers->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">👨‍🏫</p>
                <p class="text-sm">No teachers yet. <a href="{{ route('teachers.create') }}" class="text-blue-600 hover:underline">Add one →</a></p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Username</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Assigned Subjects</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($teachers as $teacher)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $teacher->name }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $teacher->username }}</code>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse($teacher->assignedSubjects as $subject)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        {{ $subject->name }}
                                        <span class="ml-1 text-blue-400">({{ $subject->schoolClass->name ?? '' }})</span>
                                    </span>
                                @empty
                                    <span class="text-gray-400 text-xs">No subjects assigned</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('teachers.edit', $teacher) }}"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded transition-colors">Edit</a>
                                <form method="POST" action="{{ route('teachers.destroy', $teacher) }}"
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
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
