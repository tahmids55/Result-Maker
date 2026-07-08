@extends('layouts.app')
@section('title', 'Generate Marksheets')

@section('content')
<div class="py-4 space-y-4">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Queued Generation --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-1">Generate (Queue – Large Classes)</h2>
            <p class="text-xs text-gray-500 mb-5">Jobs will run in background via Laravel Horizon. Best for 50+ students.</p>

            <form method="POST" action="{{ route('marksheets.generate') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Class</label>
                        <select name="class_id" required onchange="fetchSections(this.value, 'section_id')"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Section</label>
                        <select name="section_id" id="section_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Section --</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Exam</label>
                    <select name="exam_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Exam --</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->name }} {{ $exam->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Roll Start (Optional)</label>
                        <input type="number" name="roll_start" min="1" placeholder="e.g. 1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Roll End (Optional)</label>
                        <input type="number" name="roll_end" min="1" placeholder="e.g. 20" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Template</label>
                    <select name="template_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Template --</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}" {{ $tpl->is_default ? 'selected' : '' }}>
                                {{ $tpl->name }} {{ $tpl->is_default ? '(Default)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                    🚀 Queue Generation
                </button>
            </form>
        </div>

        {{-- Sync Generation --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-1">Generate & Download (Sync – Small Classes)</h2>
            <p class="text-xs text-gray-500 mb-5">Generates immediately and downloads a ZIP. Best for ≤30 students.</p>

            <form method="POST" action="{{ route('marksheets.generate-sync') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Class</label>
                        <select name="class_id" required onchange="fetchSections(this.value, 'section_id_sync')"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Section</label>
                        <select name="section_id" id="section_id_sync" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Section --</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Exam</label>
                    <select name="exam_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Exam --</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->name }} {{ $exam->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Roll Start (Optional)</label>
                        <input type="number" name="roll_start" min="1" placeholder="e.g. 1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Roll End (Optional)</label>
                        <input type="number" name="roll_end" min="1" placeholder="e.g. 20" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Template</label>
                    <select name="template_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Template --</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}" {{ $tpl->is_default ? 'selected' : '' }}>
                                {{ $tpl->name }} {{ $tpl->is_default ? '(Default)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                    ⬇ Generate & Download ZIP
                </button>
            </form>
        </div>
    </div>

    {{-- History link --}}
    <div class="text-right">
        <a href="{{ route('marksheets.history') }}" class="text-sm text-blue-600 hover:underline">View Generation History →</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function fetchSections(classId, targetId) {
    const sel = document.getElementById(targetId);
    sel.innerHTML = '<option value="">Loading...</option>';
    if (!classId) { sel.innerHTML = '<option value="">-- Section --</option>'; return; }

    fetch(`/api/sections-by-class?class_id=${classId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Section --</option>';
            data.forEach(s => {
                sel.innerHTML += `<option value="${s.id}">${s.name}</option>`;
            });
        });
}
</script>
@endpush
