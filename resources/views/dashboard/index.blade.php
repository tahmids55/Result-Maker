@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="py-4 space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label' => 'Classes',  'value' => $stats['total_classes'],   'icon' => '🏫', 'color' => 'blue'],
                ['label' => 'Students', 'value' => $stats['total_students'],  'icon' => '👥', 'color' => 'green'],
                ['label' => 'Subjects', 'value' => $stats['total_subjects'],  'icon' => '📚', 'color' => 'purple'],
                ['label' => 'Exams',    'value' => $stats['total_exams'],     'icon' => '📝', 'color' => 'yellow'],
            ];
            $colorMap = [
                'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
                'green'  => 'bg-green-50 border-green-200 text-green-700',
                'purple' => 'bg-purple-50 border-purple-200 text-purple-700',
                'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
            ];
        @endphp
        @foreach($cards as $card)
        <div class="bg-white rounded-xl border {{ $colorMap[$card['color']] }} p-5 shadow-sm">
            <div class="text-3xl mb-2">{{ $card['icon'] }}</div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($card['value']) }}</div>
            <div class="text-sm text-gray-500 mt-1">Total {{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('marks.index') }}"
               class="flex flex-col items-center gap-2 p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors text-center">
                <span class="text-2xl">✏️</span>
                <span class="text-sm font-medium">Enter Marks</span>
            </a>
            <a href="{{ route('marksheets.index') }}"
               class="flex flex-col items-center gap-2 p-4 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors text-center">
                <span class="text-2xl">🖨️</span>
                <span class="text-sm font-medium">Generate Marksheets</span>
            </a>
            <a href="{{ route('templates.create') }}"
               class="flex flex-col items-center gap-2 p-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-colors text-center">
                <span class="text-2xl">📄</span>
                <span class="text-sm font-medium">Upload Template</span>
            </a>
            <a href="{{ route('ocr.index') }}"
               class="flex flex-col items-center gap-2 p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl transition-colors text-center">
                <span class="text-2xl">🔍</span>
                <span class="text-sm font-medium">OCR Import</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Class Chart --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Students per Class</h2>
            <x-accessible-chart
                id="classChart"
                type="bar"
                title="Students per Class"
                :labels="$classStats->pluck('name')->toArray()"
                :values="$classStats->pluck('count')->toArray()"
                :colors="['#3b82f6']"
                x-label="Class"
                y-label="Students"
                :legend="false"
                height="180"
            />
        </div>

        {{-- Recent Results --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Recent Results</h2>
            @if($recentResults->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">No results calculated yet.</p>
            @else
                <div class="space-y-2">
                @foreach($recentResults as $result)
                    <div class="flex items-center justify-between text-sm py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="font-medium text-gray-800">{{ $result->student->name }}</span>
                            <span class="text-gray-400 ml-2 text-xs">{{ $result->exam->name }} {{ $result->exam->year }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                {{ $result->is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $result->grade }} / {{ number_format($result->gpa, 2) }}
                            </span>
                        </div>
                    </div>
                @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Active Exam Banner --}}
    @if($stats['active_exam'])
    <div class="bg-blue-600 text-white rounded-xl p-5 flex items-center justify-between shadow">
        <div>
            <div class="font-semibold">Active Exam: {{ $stats['active_exam']->name }} {{ $stats['active_exam']->year }}</div>
            @if($stats['active_exam']->end_date)
                <div class="text-blue-200 text-sm mt-1">Ends: {{ $stats['active_exam']->end_date->format('d M Y') }}</div>
            @endif
        </div>
        <a href="{{ route('marks.index') }}"
           class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium text-sm hover:bg-blue-50 transition-colors">
            Enter Marks →
        </a>
    </div>
    @endif
</div>
@endsection

