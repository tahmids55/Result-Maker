@extends('layouts.app')
@section('title', 'OCR Result Review')

@section('content')
<div class="py-4 space-y-4">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">OCR Review</h2>
            <p class="text-sm text-gray-500">Image: {{ basename($import->image_path) }} · Status:
                <span class="font-medium {{ $import->isProcessed() ? 'text-green-600' : 'text-red-500' }}">{{ ucfirst($import->status) }}</span>
            </p>
        </div>
        <a href="{{ route('ocr.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg">← Back</a>
    </div>

    @if($import->isFailed())
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-sm text-red-800">
            <p class="font-semibold mb-1">OCR Processing Failed</p>
            <p>{{ $import->error_message }}</p>
            <p class="mt-2 text-xs text-red-600">Make sure Tesseract is installed and the image is clear enough to read.</p>
        </div>
    @elseif($import->isPending())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-sm text-yellow-800">
            <p>⏳ Processing in queue... Please refresh in a moment.</p>
        </div>
    @elseif($import->isProcessed())

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Image Preview --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Original Image</h3>
            <img src="{{ $import->image_url }}" alt="OCR Image"
                 class="w-full rounded-lg border border-gray-200 max-h-96 object-contain">
        </div>

        {{-- Raw Text --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Extracted Raw Text</h3>
            <pre class="text-xs text-gray-600 bg-gray-50 rounded-lg p-3 overflow-auto max-h-96 font-mono whitespace-pre-wrap">{{ $import->extracted_data['raw_text'] ?? 'No text extracted' }}</pre>
        </div>
    </div>

    {{-- Parsed Data + Save to DB --}}
    @if(!empty($import->extracted_data['parsed']))
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5" x-data="{ rows: @json($import->extracted_data['parsed']) }">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Parsed Data — Review & Confirm</h3>

        <form method="POST" action="{{ route('ocr.save-marks', $import) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Exam</label>
                    <select name="exam_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Select Exam --</option>
                        @foreach(\App\Models\Exam::orderByDesc('year')->get() as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->name }} {{ $exam->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                    <select name="subject_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Select Subject --</option>
                        @foreach(\App\Models\Subject::with(['schoolClass','section'])->get() as $subject)
                            <option value="{{ $subject->id }}">
                                {{ $subject->schoolClass->name }}/{{ $subject->section->name }} — {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Roll</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Name (detected)</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Marks (detected)</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Raw Line</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(row, i) in rows" :key="i">
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <input type="number" :name="`rows[${i}][roll]`" x-model="row.roll"
                                           class="w-16 border border-gray-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-blue-400 outline-none">
                                </td>
                                <td class="px-3 py-2 text-gray-600" x-text="row.name ?? '–'"></td>
                                <td class="px-3 py-2">
                                    <template x-for="(mark, j) in row.marks" :key="j">
                                        <input type="number" :name="`rows[${i}][components][comp_${j}]`" x-model="row.marks[j]"
                                               class="w-14 border border-gray-300 rounded px-1 py-1 text-xs mr-1 focus:ring-1 focus:ring-blue-400 outline-none">
                                    </template>
                                </td>
                                <td class="px-3 py-2 font-mono text-gray-400" x-text="row.raw"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-400">
                ⚠ Review the parsed data carefully. Correct any OCR errors before saving. Roll numbers must match existing students.
            </p>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                ✔ Confirm & Save Marks to Database
            </button>
        </form>
    </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-sm text-yellow-800">
            OCR extracted text but couldn't parse structured data. Review the raw text above and enter marks manually.
        </div>
    @endif
    @endif
</div>
@endsection
