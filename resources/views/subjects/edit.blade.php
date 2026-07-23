@extends('layouts.app')
@section('title', 'Edit Subject – ' . $subject->name)

@section('content')
<div class="py-4 max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Edit Subject: {{ $subject->name }}</h2>

        <form method="POST" action="{{ route('subjects.update', $subject) }}" x-data="subjectEditForm()" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Name *</label>
                    <input type="text" name="name" value="{{ old('name', $subject->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" value="{{ old('code', $subject->code) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $subject->sort_order) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                    <select name="class_id" required onchange="fetchSections(this.value)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $subject->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section *</label>
                    <select name="section_id" id="section_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ $subject->section_id == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" name="is_optional" value="1" {{ $subject->is_optional ? 'checked' : '' }} class="rounded">
                        Optional Subject
                    </label>
                </div>
            </div>

            <div class="mb-4 space-y-3">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="checkbox" name="has_sub_subjects" value="1" x-model="hasSubSubjects" class="rounded text-blue-600 focus:ring-blue-500">
                    <span class="font-bold">This Subject has Sub-Subjects (Papers)</span>
                </label>

                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="checkbox" name="accumulated_pass_marks" value="1" {{ old('accumulated_pass_marks', $subject->accumulated_pass_marks) ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="font-bold">Use Accumulated Pass Marks</span>
                        <p class="text-xs text-gray-500 mt-0.5">If checked, a student passes the subject if their total obtained marks ≥ total pass marks, regardless of failing individual components.</p>
                    </div>
                </label>
            </div>

            {{-- Dynamic Components (No Sub-Subjects) --}}
            <div x-show="!hasSubSubjects" class="mt-4 border-t pt-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-700">Exam Components</label>
                    <button type="button" @click="addComponent()"
                            class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium px-3 py-1.5 rounded-lg">
                        + Add Component
                    </button>
                </div>
                <div class="space-y-2">
                    <template x-for="(comp, index) in components" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-center bg-gray-50 p-3 rounded-lg border">
                            <div class="col-span-4">
                                <input type="text" :name="`components[${index}][name]`" x-model="comp.name" x-bind:required="!hasSubSubjects"
                                       placeholder="Component name"
                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                            </div>
                            <div class="col-span-3">
                                <input type="number" :name="`components[${index}][full]`" x-model="comp.full" x-bind:required="!hasSubSubjects"
                                       placeholder="Full marks" min="1" step="0.01"
                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                            </div>
                            <div class="col-span-3">
                                <input type="number" :name="`components[${index}][pass]`" x-model="comp.pass" x-bind:required="!hasSubSubjects"
                                       placeholder="Pass marks" min="1" step="0.01"
                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                            </div>
                            <div class="col-span-2 text-right">
                                <button type="button" @click="components.splice(index,1)" x-show="components.length > 1"
                                        class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50">✕</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Sub-Subjects --}}
            <div x-show="hasSubSubjects" class="mt-4 border-t pt-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-700">Sub-Subjects (Papers)</label>
                    <button type="button" @click="addSubSubject()"
                            class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium px-3 py-1.5 rounded-lg transition-colors">
                        + Add Paper
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(sub, sIndex) in subSubjects" :key="sIndex">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <input type="text" :name="`sub_subjects[${sIndex}][name]`" x-bind:required="hasSubSubjects"
                                       x-model="sub.name" placeholder="Paper Name (e.g. Bangla 1st Paper)"
                                       class="w-2/3 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none font-medium">
                                
                                <button type="button" @click="removeSubSubject(sIndex)" x-show="subSubjects.length > 1"
                                        class="text-red-500 hover:text-red-700 text-sm font-medium px-2 py-1">Remove Paper</button>
                            </div>

                            <div class="pl-4 border-l-2 border-indigo-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Components for this Paper</span>
                                    <button type="button" @click="addSubComponent(sIndex)"
                                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        + Add Component
                                    </button>
                                </div>
                                
                                <div class="space-y-2">
                                    <template x-for="(comp, cIndex) in sub.components" :key="cIndex">
                                        <div class="grid grid-cols-12 gap-2 items-center">
                                            <div class="col-span-4">
                                                <input type="text" :name="`sub_subjects[${sIndex}][components][${cIndex}][name]`" x-bind:required="hasSubSubjects"
                                                       x-model="comp.name" placeholder="Component Name"
                                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs focus:ring-1 focus:ring-indigo-400 outline-none">
                                            </div>
                                            <div class="col-span-3">
                                                <input type="number" :name="`sub_subjects[${sIndex}][components][${cIndex}][full]`" x-bind:required="hasSubSubjects"
                                                       x-model="comp.full" placeholder="Full Marks" min="1" step="0.01"
                                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs focus:ring-1 focus:ring-indigo-400 outline-none">
                                            </div>
                                            <div class="col-span-3">
                                                <input type="number" :name="`sub_subjects[${sIndex}][components][${cIndex}][pass]`" x-bind:required="hasSubSubjects"
                                                       x-model="comp.pass" placeholder="Pass Marks" min="1" step="0.01"
                                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs focus:ring-1 focus:ring-indigo-400 outline-none">
                                            </div>
                                            <div class="col-span-2 text-right">
                                                <button type="button" @click="removeSubComponent(sIndex, cIndex)" x-show="sub.components.length > 1"
                                                        class="text-red-500 hover:text-red-700 text-xs px-2 py-1">✕</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex gap-3 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                    Update Subject
                </button>
                <a href="{{ route('subjects.index') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-5 py-2 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function subjectEditForm() {
    @php
        $existingSubSubjects = $subject->subSubjects->map(function($sub) {
            return [
                'name' => $sub->name,
                'components' => collect($sub->exam_components)->map(fn($v, $k) => ['name' => $k, 'full' => $v['full'], 'pass' => $v['pass']])->values()->all()
            ];
        })->toArray();
        if (empty($existingSubSubjects)) {
            $existingSubSubjects = [
                ['name' => '1st Paper', 'components' => [['name' => 'MCQ', 'full' => 40, 'pass' => 16], ['name' => 'CQ', 'full' => 60, 'pass' => 24]]],
                ['name' => '2nd Paper', 'components' => [['name' => 'MCQ', 'full' => 40, 'pass' => 16], ['name' => 'CQ', 'full' => 60, 'pass' => 24]]]
            ];
        }
        
        $existingComponents = collect($subject->exam_components)->map(fn($v, $k) => ['name' => $k, 'full' => $v['full'], 'pass' => $v['pass']])->values()->all();
        if (empty($existingComponents)) {
            $existingComponents = [['name' => 'MCQ', 'full' => 40, 'pass' => 16], ['name' => 'CQ', 'full' => 60, 'pass' => 24]];
        }
    @endphp

    return {
        hasSubSubjects: {{ old('has_sub_subjects', $subject->has_sub_subjects ? 'true' : 'false') }},
        components: {!! json_encode($existingComponents) !!},
        subSubjects: {!! json_encode($existingSubSubjects) !!},
        
        addComponent() {
            this.components.push({ name: '', full: '', pass: '' });
        },
        addSubSubject() {
            this.subSubjects.push({ 
                name: '', 
                components: [{ name: 'Written', full: 100, pass: 33 }] 
            });
        },
        removeSubSubject(index) {
            if (this.subSubjects.length > 1) this.subSubjects.splice(index, 1);
        },
        addSubComponent(sIndex) {
            this.subSubjects[sIndex].components.push({ name: '', full: '', pass: '' });
        },
        removeSubComponent(sIndex, cIndex) {
            if (this.subSubjects[sIndex].components.length > 1) {
                this.subSubjects[sIndex].components.splice(cIndex, 1);
            }
        }
    };
}

function fetchSections(classId) {
    const sel = document.getElementById('section_id');
    sel.innerHTML = '<option value="">Loading...</option>';
    if (!classId) { sel.innerHTML = '<option value="">-- Section --</option>'; return; }
    fetch(`/api/sections-by-class?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Select Section --</option>';
            data.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
        });
}
</script>
@endpush
