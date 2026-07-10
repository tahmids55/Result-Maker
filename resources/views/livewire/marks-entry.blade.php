<div class="py-4 space-y-4">

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
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm" wire:poll.60s="saveMarksSilent">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-700">
                {{ count($students) }} Students ·
                {{ count($subjects) }} Subjects
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="saveMarks" wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveMarks">💾 Save Marks</span>
                    <span wire:loading wire:target="saveMarks">Saving...</span>
                </button>
                <button wire:click="saveAndCalculateMarks" wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveAndCalculateMarks">⚡ Save & Calculate</span>
                    <span wire:loading wire:target="saveAndCalculateMarks">Processing...</span>
                </button>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if(!empty($errors_))
        <div class="mx-5 mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-xs font-semibold text-yellow-800 mb-1">⚠ Validation Issues:</p>
            @foreach($errors_ as $err)
                <p class="text-xs text-yellow-700">• {{ $err }}</p>
            @endforeach
        </div>
        @endif

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
                                    @php
                                        $val      = $marks[$sid][$subject['id']][$sub['id']][$compName] ?? '';
                                        $obtained = (float) $val;
                                        $full     = (float) $config['full'];
                                        $pass     = (float) $config['pass'];
                                        $isOver   = $val !== '' && $obtained > $full;
                                        $isFail   = $val !== '' && $obtained < $pass && $val !== '';
                                    @endphp
                                    <td class="border-b border-r border-gray-100 p-0">
                                        <input type="number"
                                               wire:model.lazy="marks.{{ $sid }}.{{ $subject['id'] }}.{{ $sub['id'] }}.{{ $compName }}"
                                               min="0" max="{{ $config['full'] }}" step="0.5"
                                               data-row="{{ $r }}" data-col="{{ $c }}"
                                               @keydown="handleKey($event)"
                                               class="mark-input w-full px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 rounded transition-colors
                                                      {{ $isOver ? 'bg-red-100 text-red-700 ring-1 ring-red-400'
                                                         : ($isFail ? 'bg-orange-50 text-orange-700'
                                                            : 'bg-transparent') }}"
                                               placeholder="–">
                                    </td>
                                    @php $c++; @endphp
                                    @endforeach
                                @endforeach
                            @else
                                @foreach($subject['exam_components'] as $compName => $config)
                                @php
                                    $val      = $marks[$sid][$subject['id']][0][$compName] ?? '';
                                    $obtained = (float) $val;
                                    $full     = (float) $config['full'];
                                    $pass     = (float) $config['pass'];
                                    $isOver   = $val !== '' && $obtained > $full;
                                    $isFail   = $val !== '' && $obtained < $pass && $val !== '';
                                @endphp
                                <td class="border-b border-r border-gray-100 p-0">
                                    <input type="number"
                                           wire:model.lazy="marks.{{ $sid }}.{{ $subject['id'] }}.0.{{ $compName }}"
                                           min="0" max="{{ $config['full'] }}" step="0.5"
                                           data-row="{{ $r }}" data-col="{{ $c }}"
                                           @keydown="handleKey($event)"
                                           class="mark-input w-full px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 rounded transition-colors
                                                  {{ $isOver ? 'bg-red-100 text-red-700 ring-1 ring-red-400'
                                                     : ($isFail ? 'bg-orange-50 text-orange-700'
                                                        : 'bg-transparent') }}"
                                           placeholder="–">
                                </td>
                                @php $c++; @endphp
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Computed columns --}}
                        <td class="px-2 py-1.5 text-center font-semibold text-gray-800 border-b border-r border-gray-100 bg-blue-50/40">
                            {{ isset($rowTotals[$sid]) ? number_format($rowTotals[$sid], 1) : '–' }}
                        </td>
                        <td class="px-2 py-1.5 text-center text-gray-700 border-b border-r border-gray-100 bg-blue-50/40">
                            {{ isset($rowPercentages[$sid]) ? number_format($rowPercentages[$sid], 1) . '%' : '–' }}
                        </td>
                        <td class="px-2 py-1.5 text-center text-gray-700 border-b border-r border-gray-100 bg-blue-50/40">
                            {{ isset($rowGpas[$sid]) ? number_format($rowGpas[$sid], 2) : '–' }}
                        </td>
                        <td class="px-2 py-1.5 text-center border-b border-gray-100 bg-blue-50/40">
                            @if(isset($rowGrades[$sid]))
                                <span class="px-1.5 py-0.5 rounded text-xs font-bold
                                    {{ ($rowPassed[$sid] ?? false) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                    {{ $rowGrades[$sid] }}
                                </span>
                            @else
                                <span class="text-gray-400">–</span>
                            @endif
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
                <span class="text-red-600">red = exceeds full marks</span>
            </p>
            <div class="flex items-center gap-2">
                <button wire:click="saveMarks" wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveMarks">💾 Save Marks</span>
                    <span wire:loading wire:target="saveMarks">Saving...</span>
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

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('marksGrid', () => ({
            handleKey(e) {
                const input = e.target;
                const row = parseInt(input.dataset.row);
                const col = parseInt(input.dataset.col);
                
                let nextRow = row;
                let nextCol = col;

                switch (e.key) {
                    case 'ArrowUp':
                        nextRow = row - 1;
                        break;
                    case 'ArrowDown':
                    case 'Enter':
                        nextRow = row + 1;
                        e.preventDefault();
                        break;
                    case 'ArrowLeft':
                        if (input.selectionStart === 0) {
                            nextCol = col - 1;
                        }
                        break;
                    case 'ArrowRight':
                        if (input.selectionStart === input.value.length) {
                            nextCol = col + 1;
                        }
                        break;
                    default:
                        return; // Let default behavior happen
                }

                // If moving left/right triggered a column change
                if (nextCol !== col || nextRow !== row) {
                    const nextInput = document.querySelector(`.mark-input[data-row="${nextRow}"][data-col="${nextCol}"]`);
                    if (nextInput) {
                        nextInput.focus();
                        nextInput.select();
                    }
                }
            }
        }))
    });
</script>
@endpush
