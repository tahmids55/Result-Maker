@extends('layouts.app')
@section('title', 'Class Result – ' . $class->name . ' ' . $section->name)

@section('content')
<div class="py-4 space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">{{ $class->name }} – Section {{ $section->name }}</h2>
            <p class="text-sm text-gray-500">{{ $exam->name }} {{ $exam->year }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('results.index') }}" class="text-sm border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors">
                ← Back
            </a>
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('results.export') }}" class="inline">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="section_id" value="{{ $section->id }}">
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <button class="text-sm bg-orange-500 hover:bg-orange-600 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                    ⬇ Export CSV
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    @if(auth()->user()->isAdmin())
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $passRate = $totalStudents > 0 ? round(($passedStudents / $totalStudents) * 100, 1) : 0;
            $avgGpa   = $results->avg('gpa');
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-bold text-gray-900">{{ $totalStudents }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Students</div>
        </div>
        <div class="bg-white rounded-xl border border-green-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $passedStudents }}</div>
            <div class="text-xs text-gray-500 mt-1">Passed ({{ $passRate }}%)</div>
        </div>
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-bold text-red-500">{{ $totalStudents - $passedStudents }}</div>
            <div class="text-xs text-gray-500 mt-1">Failed</div>
        </div>
        <div class="bg-white rounded-xl border border-blue-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ number_format($avgGpa, 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">Average GPA</div>
        </div>
    </div>
    @endif

    {{-- Charts --}}
    @if(auth()->user()->isAdmin())
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Pass / Fail Distribution</h3>
            <x-accessible-chart
                id="passChart"
                type="bar"
                title="Pass and Fail Count"
                :labels="['Passed', 'Failed']"
                :values="[$passedStudents, $totalStudents - $passedStudents]"
                :colors="['#059669', '#DC2626']"
                x-label="Status"
                y-label="Students"
                :legend="false"
                height="200"
            />
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Grade Distribution</h3>
            <x-accessible-chart
                id="gradeChart"
                type="doughnut"
                title="Grade Distribution"
                :labels="$gradeDistrib->keys()->toArray()"
                :values="$gradeDistrib->values()->toArray()"
                x-label="Grade"
                y-label="Students"
                height="200"
            />
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Subject Performance (Avg GPA)</h3>
            <x-accessible-chart
                id="subjectChart"
                type="bar"
                title="Subject Average GPA"
                :labels="$subjectAverages->pluck('name')->toArray()"
                :values="$subjectAverages->pluck('avg_gpa')->toArray()"
                :colors="['#4F46E5']"
                x-label="Subject"
                y-label="Avg GPA"
                :legend="false"
                height="200"
            />
        </div>
    </div>
    @endif

    {{-- Result Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Student List</h3>
        </div>
        @if($results->isEmpty())
            <div class="py-12 text-center text-gray-400 text-sm">
                No results found. Make sure marks are entered and click Recalculate.
            </div>
        @else
        @php
            $componentKeys = collect();
            if(isset($teacherSubject)) {
                foreach($results as $r) {
                    $detail = collect($r->subject_details)->firstWhere('subject_id', $teacherSubject->id);
                    if($detail && !empty($detail['components'])) {
                        $componentKeys = collect($detail['components'])->keys();
                        break;
                    }
                }
            }
        @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        @if(auth()->user()->isAdmin())
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Rank</th>
                        @endif
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Roll</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                        @if(auth()->user()->isAdmin())
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Total</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">GPA</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Division</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        @elseif(isset($teacherSubject))
                            @foreach($componentKeys as $comp)
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">{{ $comp }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Total Marks</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                        @endif
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($results as $result)
                    <tr class="hover:bg-gray-50 {{ auth()->user()->isAdmin() && !$result->is_passed ? 'bg-red-50/30' : '' }}">
                        @if(auth()->user()->isAdmin())
                            <td class="px-4 py-3 text-center">
                                @if($result->rank <= 3)
                                    <span class="font-bold text-lg">{{ ['🥇','🥈','🥉'][$result->rank - 1] }}</span>
                                @else
                                    <span class="text-gray-600 font-medium">{{ $result->rank }}</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-4 py-3 font-mono text-gray-700">{{ $result->student->roll }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $result->student->name }}</td>
                        @if(auth()->user()->isAdmin())
                            <td class="px-4 py-3 text-center font-semibold text-gray-800">
                                {{ $result->total_marks }}<span class="text-gray-400 font-normal">/{{ $result->full_marks }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700">{{ number_format($result->percentage, 1) }}%</td>
                            <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ number_format($result->gpa, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $result->grade_badge_color }} text-white">
                                    {{ $result->grade }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600">{{ $result->division }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $result->is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                    {{ $result->is_passed ? 'Pass' : 'Fail' }}
                                </span>
                            </td>
                        @elseif(isset($teacherSubject))
                            @php
                                $detail = collect($result->subject_details)->firstWhere('subject_id', $teacherSubject->id);
                            @endphp
                            @if($detail)
                                @foreach($componentKeys as $comp)
                                    <td class="px-4 py-3 text-center text-gray-700 font-mono">
                                        @if(isset($detail['components'][$comp]))
                                            {{ $detail['components'][$comp]['is_absent'] ? 'AB' : $detail['components'][$comp]['obtained'] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ $detail['obtained'] }} <span class="text-gray-400 font-normal">/ {{ $detail['full'] }}</span></td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ number_format($detail['percentage'], 1) }}%</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold text-white {{ $detail['is_passed'] ? 'bg-green-500' : 'bg-red-500' }}">
                                        {{ $detail['grade'] }}
                                    </span>
                                </td>
                            @else
                                <td class="px-4 py-3 text-center text-gray-400" colspan="{{ $componentKeys->count() + 3 }}">-</td>
                            @endif
                        @endif
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('results.student', [$result->student, $exam]) }}"
                               class="text-xs text-blue-600 hover:underline">View Subjects →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

