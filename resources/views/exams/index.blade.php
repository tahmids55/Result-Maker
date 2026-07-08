@extends('layouts.app')
@section('title', 'Exams')

@section('content')
<div class="py-4 space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('exams.index') }}" class="flex flex-wrap md:flex-nowrap gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search exam name or year..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Search
                </button>
                <a href="{{ route('exams.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-3 py-2 rounded-lg transition-colors flex items-center">
                    ✕
                </a>
            </div>
        </form>
    </div>

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $exams->total() }} exams found</p>
        <a href="{{ route('exams.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Create Exam
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($exams->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">📝</p>
                <p class="text-sm">No exams yet. <a href="{{ route('exams.create') }}" class="text-blue-600 hover:underline">Create one →</a></p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Exam</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Year</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Period</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $exam->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $exam->year }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            @if($exam->start_date)
                                {{ $exam->start_date->format('d M Y') }} – {{ $exam->end_date?->format('d M Y') ?? '...' }}
                            @else
                                –
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('exams.toggle-active', $exam) }}">
                                @csrf
                                <button type="submit"
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors
                                               {{ $exam->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $exam->is_active ? '● Active' : '○ Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('marks.index') }}?exam_id={{ $exam->id }}"
                                   class="text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 px-2 py-1 rounded transition-colors">Enter Marks</a>
                                <a href="{{ route('exams.edit', $exam) }}"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded transition-colors">Edit</a>
                                <form method="POST" action="{{ route('exams.destroy', $exam) }}"
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
                {{ $exams->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
