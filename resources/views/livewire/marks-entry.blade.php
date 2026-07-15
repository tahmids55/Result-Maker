<div class="py-4 space-y-4"
     x-data
     @marks-hydrate.window="$store.marks.hydrate($event.detail[0] || $event.detail)">

    {{-- Selector Row --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Select Class / Section / Exam / Subject</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Class --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Class</label>
                <select wire:model.live="classId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Section --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Section</label>
                <select wire:model.live="sectionId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        {{ empty($classId) ? 'disabled' : '' }}>
                    <option value="">-- Select Section --</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Exam --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Exam</label>
                <select wire:model.live="examId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Select Exam --</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->name }} {{ $exam->year }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Subject --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Subject (Optional)</label>
                <select wire:model.live="subjectId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        {{ empty($sectionId) ? 'disabled' : '' }}>
                    <option value="">-- All Subjects --</option>
                    @foreach($availableSubjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Load Button --}}
            <div class="flex items-end">
                <button wire:click="loadMarks" wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="loadMarks">📋 Load Marks</span>
                    <span wire:loading wire:target="loadMarks">Loading...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Marks Grid --}}
    @if($loaded)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-700">
                {{ count($students) }} Students ·
                {{ count($subjects) }} Subjects
            </div>
            <div class="flex items-center gap-3">
                {{-- Save Status Indicator --}}
                <div class="flex items-center gap-2 text-xs">
                    <template x-if="$store.marks.saveState === 'saving'">
                        <span class="flex items-center gap-1.5 text-blue-600 animate-pulse-save">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Saving...
                        </span>
                    </template>
                    <template x-if="$store.marks.saveState === 'saved'">
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Saved
                        </span>
                    </template>
                    <template x-if="$store.marks.saveState === 'error'">
                        <span class="flex items-center gap-1 text-red-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                            Retrying...
                        </span>
                    </template>
                    <template x-if="$store.marks.dirty.length > 0 && $store.marks.saveState === 'idle'">
                        <span class="text-amber-600">
                            <span x-text="$store.marks.dirty.length"></span> unsaved
                        </span>
                    </template>
                </div>

                <button @click="$store.marks.forceSave()"
                        :disabled="$store.marks.dirty.length === 0"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    💾 Save Marks
                </button>
                <button wire:click="saveAndCalculateMarks" wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveAndCalculateMarks">⚡ Save & Calculate</span>
                    <span wire:loading wire:target="saveAndCalculateMarks">Processing...</span>
                </button>
            </div>
        </div>

        {{-- Spreadsheet Table --}}
        <div class="overflow-x-auto scrollbar-thin" x-data="marksGrid()">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b border-r border-gray-200 sticky left-0 bg-slate-50 w-8">#</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b border-r border-gray-200 sticky left-8 bg-slate-50 min-w-[60px]">Roll</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b border-r border-gray-200 sticky left-20 bg-slate-50 min-w-[140px]">Name</th>
                        @foreach($subjects as $subject)
                            @if($subject['has_sub_subjects'])
                                @foreach($subject['sub_subjects'] as $sub)
                                    @foreach($sub['exam_components'] as $compName => $config)
                                        <th class="px-2 py-1 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[70px]"
                                            title="{{ $subject['name'] }} - {{ $sub['name'] }} - {{ strtoupper($compName) }} (Full: {{ $config['full'] }})">
                                            <div class="text-gray-500 font-normal truncate max-w-[70px]">{{ Str::limit($subject['name'], 8) }}</div>
                                            <div class="text-indigo-500 font-medium text-[10px] truncate max-w-[70px]">{{ $sub['name'] }}</div>
                                            <div class="text-blue-600 uppercase">{{ $compName }}</div>
                                            <div class="text-gray-400 font-normal">/{{ $config['full'] }}</div>
                                        </th>
                                    @endforeach
                                @endforeach
                            @else
                                @foreach($subject['exam_components'] as $compName => $config)
                                    <th class="px-2 py-1 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[70px]"
                                        title="{{ $subject['name'] }} - {{ strtoupper($compName) }} (Full: {{ $config['full'] }})">
                                        <div class="text-gray-500 font-normal truncate max-w-[70px]">{{ Str::limit($subject['name'], 8) }}</div>
                                        <div class="text-blue-600 uppercase">{{ $compName }}</div>
                                        <div class="text-gray-400 font-normal">/{{ $config['full'] }}</div>
                                    </th>
                                @endforeach
                            @endif
                        @endforeach
                        <th class="px-3 py-2 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[60px] bg-blue-50">Total</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[55px] bg-blue-50">%</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[50px] bg-blue-50">GPA</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-600 border-b border-gray-200 min-w-[50px] bg-blue-50">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $r => $student)
                    @php $sid = $student['id']; @endphp
                    <tr class="hover:bg-gray-50 {{ ($r % 2 === 0) ? 'bg-white' : 'bg-slate-50/50' }}">
                        <td class="px-3 py-1.5 text-gray-400 border-b border-r border-gray-100 sticky left-0 {{ ($r % 2 === 0) ? 'bg-white' : 'bg-slate-50' }}">{{ $r + 1 }}</td>
                        <td class="px-3 py-1.5 font-mono text-gray-700 border-b border-r border-gray-100 sticky left-8 {{ ($r % 2 === 0) ? 'bg-white' : 'bg-slate-50' }}">{{ $student['roll'] }}</td>
                        <td class="px-3 py-1.5 font-medium text-gray-800 border-b border-r border-gray-100 sticky left-20 {{ ($r % 2 === 0) ? 'bg-white' : 'bg-slate-50' }}">{{ $student['name'] }}</td>

                        @php $c = 0; @endphp
                        @foreach($subjects as $subject)
                            @if($subject['has_sub_subjects'])
                                @foreach($subject['sub_subjects'] as $sub)
                                    @foreach($sub['exam_components'] as $compName => $config)
                                    <td class="border-b border-r border-gray-100 p-0">
                                        <input type="number"
                                               x-model.lazy="$store.marks.cells[{{ $sid }}][{{ $subject['id'] }}][{{ $sub['id'] }}]['{{ $compName }}']"
                                               @change="$store.marks.setCell({{ $sid }}, {{ $subject['id'] }}, {{ $sub['id'] }}, '{{ $compName }}', $event.target.value)"
                                               min="0" max="{{ $config['full'] }}" step="0.5"
                                               data-row="{{ $r }}" data-col="{{ $c }}"
                                               @keydown="handleKey($event)"
                                               class="mark-input w-full px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 rounded transition-colors bg-transparent"
                                               :class="{
                                                   'bg-red-100 text-red-700 ring-1 ring-red-400': $store.marks.isOver({{ $sid }}, {{ $subject['id'] }}, {{ $sub['id'] }}, '{{ $compName }}', {{ $config['full'] }}),
                                                   'bg-orange-50 text-orange-700': !$store.marks.isOver({{ $sid }}, {{ $subject['id'] }}, {{ $sub['id'] }}, '{{ $compName }}', {{ $config['full'] }}) && $store.marks.isFail({{ $sid }}, {{ $subject['id'] }}, {{ $sub['id'] }}, '{{ $compName }}', {{ $config['pass'] }})
                                               }"
                                               placeholder="–">
                                    </td>
                                    @php $c++; @endphp
                                    @endforeach
                                @endforeach
                            @else
                                @foreach($subject['exam_components'] as $compName => $config)
                                <td class="border-b border-r border-gray-100 p-0">
                                    <input type="number"
                                           x-model.lazy="$store.marks.cells[{{ $sid }}][{{ $subject['id'] }}][0]['{{ $compName }}']"
                                           @change="$store.marks.setCell({{ $sid }}, {{ $subject['id'] }}, 0, '{{ $compName }}', $event.target.value)"
                                           min="0" max="{{ $config['full'] }}" step="0.5"
                                           data-row="{{ $r }}" data-col="{{ $c }}"
                                           @keydown="handleKey($event)"
                                           class="mark-input w-full px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 rounded transition-colors bg-transparent"
                                           :class="{
                                               'bg-red-100 text-red-700 ring-1 ring-red-400': $store.marks.isOver({{ $sid }}, {{ $subject['id'] }}, 0, '{{ $compName }}', {{ $config['full'] }}),
                                               'bg-orange-50 text-orange-700': !$store.marks.isOver({{ $sid }}, {{ $subject['id'] }}, 0, '{{ $compName }}', {{ $config['full'] }}) && $store.marks.isFail({{ $sid }}, {{ $subject['id'] }}, 0, '{{ $compName }}', {{ $config['pass'] }})
                                           }"
                                           placeholder="–">
                                </td>
                                @php $c++; @endphp
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Computed columns from Alpine store --}}
                        <td class="px-2 py-1.5 text-center font-semibold text-gray-800 border-b border-r border-gray-100 bg-blue-50/40"
                            x-text="$store.marks.rowResults[{{ $sid }}]?.total ?? '–'"></td>
                        <td class="px-2 py-1.5 text-center text-gray-700 border-b border-r border-gray-100 bg-blue-50/40"
                            x-text="($store.marks.rowResults[{{ $sid }}]?.pct ?? '–') + ($store.marks.rowResults[{{ $sid }}] ? '%' : '')"></td>
                        <td class="px-2 py-1.5 text-center text-gray-700 border-b border-r border-gray-100 bg-blue-50/40"
                            x-text="$store.marks.rowResults[{{ $sid }}]?.gpa?.toFixed(2) ?? '–'"></td>
                        <td class="px-2 py-1.5 text-center border-b border-gray-100 bg-blue-50/40">
                            <span class="px-1.5 py-0.5 rounded text-xs font-bold"
                                  :class="$store.marks.rowResults[{{ $sid }}]?.passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                                  x-text="$store.marks.rowResults[{{ $sid }}]?.grade ?? '–'"></span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer Save --}}
        <div class="px-5 py-4 border-t border-gray-200 flex justify-between items-center">
            <p class="text-xs text-gray-500">
                💡 Marks are color-coded: <span class="text-orange-600">orange = below pass mark</span>,
                <span class="text-red-600">red = exceeds full marks</span>.
                Auto-saves 2s after last edit.
            </p>
            <div class="flex items-center gap-2">
                <button @click="$store.marks.forceSave()"
                        :disabled="$store.marks.dirty.length === 0"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    💾 Save Marks
                </button>
                <button wire:click="saveAndCalculateMarks" wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveAndCalculateMarks">⚡ Save & Calculate</span>
                    <span wire:loading wire:target="saveAndCalculateMarks">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    @elseif($classId && $sectionId && $examId)
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-400 shadow-sm">
        <p class="text-4xl mb-3">📋</p>
        <p class="text-sm">Click <strong>Load Marks</strong> to display the spreadsheet.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-400 shadow-sm">
        <p class="text-4xl mb-3">✏️</p>
        <p class="text-sm">Select a class, section, and exam above to begin entering marks.</p>
    </div>
    @endif

</div>
