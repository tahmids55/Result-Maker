@extends('layouts.app')
@section('title', $student->name . ' – ' . $exam->name)

@section('content')
<div class="py-4 space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between print:hidden">
        <div>
            <h2 class="text-lg font-bold text-gray-900">{{ $student->name }}</h2>
            <p class="text-sm text-gray-500">Roll: {{ $student->roll }} · {{ $exam->name }} {{ $exam->year }}</p>
        </div>
        <a href="{{ route('results.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">
            ← Back
        </a>
    </div>

    @if(!$result)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-sm text-yellow-800">
            No result calculated yet. Go to Results → Recalculate for this exam.
        </div>
    @else

    {{-- Summary Card --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $result->total_marks }}</div>
            <div class="text-xs text-gray-500 mt-1">Total / {{ $result->full_marks }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ number_format($result->percentage, 1) }}%</div>
            <div class="text-xs text-gray-500 mt-1">Percentage</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ number_format($result->gpa, 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">GPA</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <span class="text-2xl font-bold px-3 py-1 rounded-lg {{ $result->grade_badge_color }} text-white">{{ $result->grade }}</span>
            <div class="text-xs text-gray-500 mt-2">Grade</div>
        </div>
        <div class="bg-white rounded-xl border border-{{ $result->is_passed ? 'green' : 'red' }}-200 shadow-sm p-4 text-center">
            <div class="text-xl font-bold {{ $result->is_passed ? 'text-green-600' : 'text-red-600' }}">
                {{ $result->is_passed ? '✓ PASSED' : '✗ FAILED' }}
            </div>
            <div class="text-xs text-gray-500 mt-1">{{ $result->division }}</div>
        </div>
    </div>

    {{-- Subject Breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Subject-wise Breakdown</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Subject</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Components</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">GPA</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($result->subject_details ?? [] as $detail)
                <tr class="hover:bg-gray-50 {{ !$detail['is_passed'] ? 'bg-red-50/30' : '' }}">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $detail['subject_name'] }}</div>
                        @if(!empty($detail['subject_code']))<div class="text-xs text-gray-400">{{ $detail['subject_code'] }}</div>@endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1 justify-center">
                            @foreach($detail['components'] as $compName => $cd)
                                <span class="text-xs px-2 py-0.5 rounded
                                    {{ $cd['is_passed'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}"
                                      title="Pass: {{ $cd['pass'] }}">
                                    {{ strtoupper($compName) }}: {{ $cd['obtained'] }}/{{ $cd['full'] }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center font-semibold text-gray-800">
                        {{ $detail['obtained'] }}<span class="text-gray-400 font-normal">/{{ $detail['full'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-700">{{ number_format($detail['percentage'], 1) }}%</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-700">{{ $detail['grade'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-700">{{ number_format($detail['gpa'], 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $detail['is_passed'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            {{ $detail['is_passed'] ? 'Pass' : 'Fail' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 print:hidden">
        @php $generated = $student->generatedMarksheets()->where('exam_id', $exam->id)->latest()->first(); @endphp
        @if($generated)
            <a href="{{ route('marksheets.download', $generated) }}"
               class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                ⬇ Download Marksheet
            </a>
        @else
            <a href="{{ route('marksheets.index') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                🖨️ Generate Marksheet
            </a>
        @endif
        <a href="{{ route('sms.index') }}"
           class="border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium px-5 py-2 rounded-lg transition-colors">
            💬 Send Result SMS
        </a>
        <button onclick="window.print()"
           class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors ml-auto">
            📄 Export to PDF
        </button>
    </div>

    @endif
</div>
@endsection
