@extends('layouts.app')
@section('title', 'Classes')

@section('content')
<div class="py-4 space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('classes.index') }}" class="flex flex-wrap md:flex-nowrap gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search class name or code..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Search
                </button>
                <a href="{{ route('classes.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-3 py-2 rounded-lg transition-colors flex items-center">
                    ✕
                </a>
            </div>
        </form>
    </div>

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $classes->total() }} classes found</p>
        <a href="{{ route('classes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Add Class
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($classes->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">🏫</p>
                <p class="text-sm">No classes yet. <a href="{{ route('classes.create') }}" class="text-blue-600 hover:underline">Add one →</a></p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Class Name</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Sections</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Students</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($classes as $class)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $class->name }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $class->sections_count }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $class->students_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('classes.show', $class) }}"
                                   class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded transition-colors">View</a>
                                <a href="{{ route('classes.edit', $class) }}"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded transition-colors">Edit</a>
                                <form method="POST" action="{{ route('classes.destroy', $class) }}"
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
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
