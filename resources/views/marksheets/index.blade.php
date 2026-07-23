@extends('layouts.app')
@section('title', 'Generate Marksheets')

@section('content')
<div class="py-4 space-y-4">

    {{-- Batch Progress Tracker --}}
    @if(!empty($activeBatchId))
    <div x-data="batchTracker('{{ $activeBatchId }}')" x-init="startPolling()" class="relative">
        {{-- Progress Card --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden"
             :class="status === 'failed' ? 'border-red-300' : status === 'done' ? 'border-green-300' : 'border-blue-300'">
            
            {{-- Progress Bar --}}
            <div class="h-1.5 bg-gray-100">
                <div class="h-full transition-all duration-500 ease-out rounded-r"
                     :class="status === 'failed' ? 'bg-red-500' : status === 'done' ? 'bg-green-500' : 'bg-blue-500'"
                     :style="'width: ' + pct + '%'"></div>
            </div>

            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        {{-- Animated Icon --}}
                        <template x-if="status === 'queued'">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center animate-pulse">
                                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </template>
                        <template x-if="status === 'processing'">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </div>
                        </template>
                        <template x-if="status === 'done'">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </template>
                        <template x-if="status === 'failed'">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                        </template>

                        <div>
                            <div class="text-sm font-semibold text-gray-800" x-text="stage"></div>
                            <div class="text-xs text-gray-500" x-text="detail"></div>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-2xl font-bold tabular-nums"
                             :class="status === 'failed' ? 'text-red-600' : status === 'done' ? 'text-green-600' : 'text-blue-600'"
                             x-text="pct + '%'"></div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider" x-text="status"></div>
                    </div>
                </div>

                {{-- Download button when done --}}
                <template x-if="status === 'done' && downloadUrl">
                    <a :href="downloadUrl"
                       class="flex items-center justify-center gap-2 w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg transition-colors text-sm mt-2">
                        ⬇ Download Ready – Click to Save
                    </a>
                </template>

                {{-- Dismiss button --}}
                <button @click="dismiss()" x-show="status === 'done' || status === 'failed'"
                        class="text-xs text-gray-400 hover:text-gray-600 mt-2 block mx-auto">
                    Dismiss
                </button>
            </div>
        </div>
    </div>
    @endif

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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Output Mode</label>
                        <select name="output_mode" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="individual">Individual Files (ZIP)</option>
                            <option value="combined">All-in-One (Single File)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Output Format</label>
                        <select name="output_format" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="docx">.docx (Word)</option>
                            <option value="pdf">.pdf (PDF)</option>
                        </select>
                    </div>
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Output Mode</label>
                        <select name="output_mode" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="individual">Individual Files (ZIP)</option>
                            <option value="combined">All-in-One (Single File)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Output Format</label>
                        <select name="output_format" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="docx">.docx (Word)</option>
                            <option value="pdf">.pdf (PDF)</option>
                        </select>
                    </div>
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

/**
 * Alpine.js component for real-time batch progress tracking.
 * Polls /marksheets/batch-progress every 2 seconds.
 */
function batchTracker(batchId) {
    return {
        batchId: batchId,
        stage: 'Queued',
        detail: 'Waiting for worker to pick up...',
        pct: 0,
        status: 'queued',
        downloadUrl: null,
        timer: null,

        startPolling() {
            this.poll(); // immediate first check
            this.timer = setInterval(() => this.poll(), 2000);
        },

        async poll() {
            try {
                const res = await fetch(`/marksheets/batch-progress?batch_id=${this.batchId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.status === 'unknown') return;

                this.stage  = data.stage || 'Processing';
                this.detail = data.detail || '';
                this.pct    = data.pct || 0;
                this.status = data.status || 'processing';

                if (data.download_url) {
                    this.downloadUrl = data.download_url;
                }

                // Stop polling when terminal state
                if (data.status === 'done' || data.status === 'failed') {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            } catch (e) {
                // Network error, keep trying
            }
        },

        async dismiss() {
            if (this.timer) clearInterval(this.timer);
            // Clear server-side active batch cache
            try {
                await fetch(`/marksheets/batch-dismiss?batch_id=${this.batchId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
            } catch(e) {}
            this.$el.remove();
        }
    };
}
</script>
@endpush
