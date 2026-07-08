@extends('layouts.app')
@section('title', 'Merit List – ' . $class->name . ' ' . $section->name)

@section('content')
<div class="py-4 space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Merit List</h2>
            <p class="text-sm text-gray-500">{{ $class->name }} – Section {{ $section->name }} · {{ $exam->name }} {{ $exam->year }}</p>
        </div>
        <a href="{{ route('results.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">← Back</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($results->isEmpty())
            <div class="py-12 text-center text-gray-400 text-sm">No results available. Enter marks and recalculate.</div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 w-16">Position</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Roll</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Marks</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">GPA</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($results as $result)
                <tr class="hover:bg-gray-50 {{ !$result->is_passed ? 'bg-red-50/30' : '' }}">
                    <td class="px-4 py-3 text-center">
                        @if($result->rank === 1) <span class="text-2xl">🥇</span>
                        @elseif($result->rank === 2) <span class="text-2xl">🥈</span>
                        @elseif($result->rank === 3) <span class="text-2xl">🥉</span>
                        @else <span class="font-bold text-gray-600">{{ $result->rank }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-mono text-gray-600 font-medium">{{ $result->student->roll }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $result->student->name }}</td>
                    <td class="px-4 py-3 text-center font-bold text-gray-800">
                        {{ $result->total_marks }}<span class="text-gray-400 font-normal text-xs">/{{ $result->full_marks }}</span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-700">{{ number_format($result->percentage, 1) }}%</td>
                    <td class="px-4 py-3 text-center font-semibold">{{ number_format($result->gpa, 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $result->grade_badge_color }} text-white">{{ $result->grade }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-semibold {{ $result->is_passed ? 'text-green-600' : 'text-red-500' }}">
                            {{ $result->is_passed ? '✓ Pass' : '✗ Fail' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
