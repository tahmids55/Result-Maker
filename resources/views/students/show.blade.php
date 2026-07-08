@extends('layouts.app')
@section('title', $student->name)

@section('content')
<div class="py-4 space-y-4">

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start gap-5">
            {{-- Photo --}}
            <div class="flex-shrink-0">
                @if($student->profile_photo)
                    <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                         class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-3xl font-bold text-blue-700">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900">{{ $student->name }}</h2>
                <p class="text-sm text-gray-500">Roll: <strong>{{ $student->roll }}</strong>
                    @if($student->registration_no) · Reg: <strong>{{ $student->registration_no }}</strong>@endif
                </p>
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Class</span>
                        <div class="font-medium text-gray-800">{{ $student->schoolClass->name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Section</span>
                        <div class="font-medium text-gray-800">{{ $student->section->name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Session</span>
                        <div class="font-medium text-gray-800">{{ $student->session ?? '–' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Father</span>
                        <div class="font-medium text-gray-800">{{ $student->father_name ?? '–' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500">Mother</span>
                        <div class="font-medium text-gray-800">{{ $student->mother_name ?? '–' }}</div>
                    </div>
                    @if($student->dob)
                    <div>
                        <span class="text-gray-500">Date of Birth</span>
                        <div class="font-medium text-gray-800">{{ $student->dob->format('d M Y') }}</div>
                    </div>
                    @endif
                    @if($student->phone)
                    <div>
                        <span class="text-gray-500">Phone</span>
                        <div class="font-medium text-gray-800">{{ $student->phone }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('students.edit', $student) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('students.index') }}"
                   class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">
                    ← Back
                </a>
            </div>
        </div>
    </div>

    {{-- Result History --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Result History</h3>
        </div>
        @if($student->results->isEmpty())
            <div class="py-10 text-center text-gray-400 text-sm">
                No results calculated yet for this student.
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Exam</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">GPA</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Rank</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Marksheet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($student->results as $result)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $result->exam->name }} {{ $result->exam->year }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">
                            {{ $result->total_marks }}<span class="text-gray-400">/{{ $result->full_marks }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ number_format($result->percentage, 1) }}%</td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ number_format($result->gpa, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $result->grade_badge_color }} text-white">
                                {{ $result->grade }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $result->rank ?? '–' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $result->is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $result->is_passed ? 'Pass' : 'Fail' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('results.student', [$student, $result->exam]) }}"
                               class="text-xs text-blue-600 hover:underline">View →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
