@extends('layouts.app')
@section('title', 'Marksheet Generation History')

@section('content')
<div class="py-4 space-y-4">
    <div class="flex items-center justify-between">
        <form method="GET" action="{{ route('marksheets.history') }}" class="flex gap-3">
            <select name="exam_id" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Exams</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                        {{ $exam->name }} {{ $exam->year }}
                    </option>
                @endforeach
            </select>
        </form>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('bulk-delete-form').submit()"
                    class="border border-red-300 text-red-600 hover:bg-red-50 text-sm px-4 py-2 rounded-lg transition-colors">
                🗑 Delete Selected
            </button>
            <a href="{{ route('marksheets.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">
                ← Generate More
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($marksheets->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">🖨️</p>
                <p class="text-sm">No marksheets generated yet.</p>
            </div>
        @else
            <form id="bulk-delete-form" method="POST" action="{{ route('marksheets.bulk-delete') }}">
            @csrf
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox" onchange="document.querySelectorAll('.delete-checkbox').forEach(cb => cb.checked = this.checked)" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Student</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Exam</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Template</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Generated</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($marksheets as $sheet)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="marksheet_ids[]" value="{{ $sheet->id }}" class="delete-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </td>
                        <td class="px-4 py-3">
                            @if($sheet->student_id)
                                <div class="font-medium text-gray-900">{{ $sheet->student->name }}</div>
                                <div class="text-xs text-gray-400">Roll: {{ $sheet->student->roll }}</div>
                            @else
                                <div class="font-medium text-gray-900">Batch Generation</div>
                                <div class="text-xs text-blue-500">{{ $sheet->batch_name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $sheet->exam->name }} {{ $sheet->exam->year }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $sheet->template->name }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 uppercase">
                                {{ $sheet->file_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $sheet->generated_at?->format('d M Y H:i') ?? '–' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('marksheets.download', $sheet) }}"
                               class="text-xs bg-green-50 hover:bg-green-100 text-green-700 font-medium px-3 py-1.5 rounded-lg transition-colors">
                                ⬇ Download
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </form>
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $marksheets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
