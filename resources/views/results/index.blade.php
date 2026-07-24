@extends('layouts.app')
@section('title', 'Results')

@section('content')
<div class="py-4 space-y-4">

    {{-- Selector --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">View Class Results</h2>
        <form method="POST" action="{{ route('results.class') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            @csrf
            <select name="class_id" required onchange="fetchSections(this.value, this)"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">-- Class --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
            <select name="section_id" id="section_id" required onchange="filterSubjects(this.value, this)"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">-- Section --</option>
            </select>
            @if(auth()->user()->isTeacher())
            <select name="subject_id"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">-- Subject (All) --</option>
                @foreach($teacherSubjects as $sub)
                    <option value="{{ $sub->id }}" data-class-id="{{ $sub->class_id }}" data-section-id="{{ $sub->section_id }}">
                        {{ $sub->name }}
                    </option>
                @endforeach
            </select>
            @endif
            <select name="exam_id" required
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">-- Exam --</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}">{{ $exam->name }} {{ $exam->year }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                📊 View Results
            </button>
            @if(auth()->user()->isAdmin())
                <button type="submit" formaction="{{ route('results.recalculate') }}"
                        class="border border-blue-300 text-blue-700 hover:bg-blue-50 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    🔄 Recalculate
                </button>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Merit List --}}
        @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Merit List</h3>
            <form method="POST" action="{{ route('results.merit') }}" class="space-y-3">
                @csrf
                <select name="class_id" required onchange="fetchSections(this.value, this)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                <select name="section_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Section --</option>
                </select>
                <select name="exam_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Exam --</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->name }} {{ $exam->year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                    🏅 View Merit List
                </button>
            </form>
        </div>
        @endif

        {{-- Export --}}
        @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Export to CSV</h3>
            <form method="POST" action="{{ route('results.export') }}" class="space-y-3">
                @csrf
                <select name="class_id" required onchange="fetchSections(this.value, this)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                <select name="section_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Section --</option>
                </select>
                <select name="exam_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Exam --</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->name }} {{ $exam->year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                    ⬇ Download CSV
                </button>
            </form>
        </div>
        @endif

        {{-- Info --}}
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-5">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">💡 How Results Work</h3>
            <ul class="text-xs text-blue-700 space-y-1 leading-relaxed">
                @if(auth()->user()->isAdmin())
                    <li>• Results are calculated automatically from marks</li>
                    <li>• GPA follows Bangladesh SSC/HSC scale (5.0)</li>
                    <li>• Click <strong>Recalculate</strong> after editing marks</li>
                    <li>• Merit ranks are assigned within each class-section</li>
                    <li>• Failed subjects result in overall Fail status</li>
                @else
                    <li>• You can preview the marks you entered for your assigned subjects here.</li>
                    <li>• Overall calculation is handled by the school administration.</li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function fetchSections(classId, element) {
    const form = element.closest('form');
    if (!form) return;
    const sel = form.querySelector('[name="section_id"]');
    if (!sel) return;

    sel.innerHTML = '<option value="">Loading...</option>';
    if (!classId) { 
        sel.innerHTML = '<option value="">-- Section --</option>'; 
        return; 
    }
    fetch(`/api/sections-by-class?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Section --</option>';
            data.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
            
            // Also trigger subject filter if subject dropdown exists
            filterSubjects('', sel);
        });
}

function filterSubjects(sectionId, element) {
    const form = element.closest('form');
    if (!form) return;
    const subjSel = form.querySelector('[name="subject_id"]');
    if (!subjSel) return;
    
    subjSel.value = '';
    const classId = form.querySelector('[name="class_id"]').value;
    
    Array.from(subjSel.options).forEach(opt => {
        if (opt.value === '') return;
        const optClassId = opt.getAttribute('data-class-id');
        const optSectionId = opt.getAttribute('data-section-id');
        
        let show = true;
        if (classId && optClassId !== classId) show = false;
        if (sectionId && optSectionId !== String(sectionId)) show = false;
        
        opt.hidden = !show;
        opt.disabled = !show; // helps with browser compatibility
    });
}
</script>
@endpush
