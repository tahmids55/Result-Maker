@extends('layouts.app')
@section('title', 'SMS / WhatsApp')

@section('content')
<div class="py-4 space-y-4">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Bulk Send --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-1">Bulk Send Results</h2>
            <p class="text-xs text-gray-500 mb-4">Send result notification to all parents in a class-section.</p>

            <form method="POST" action="{{ route('sms.send-bulk') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Class</label>
                        <select name="class_id" required onchange="fetchSections(this.value)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Section</label>
                        <select name="section_id" id="section_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Section --</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam</label>
                    <select name="exam_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Exam --</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->name }} {{ $exam->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Channel</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="channel" value="sms" checked class="text-blue-600"> SMS
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="channel" value="whatsapp" class="text-green-600"> WhatsApp
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Message Template</label>
                    <textarea name="message_template" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-mono"
                              >Dear Parent, {student_name} (Roll: {roll}) has obtained Grade {grade} (GPA: {gpa}) in {exam_name} {exam_year}. Result: {status}.</textarea>
                    <p class="text-xs text-gray-400 mt-1">
                        Placeholders: {student_name}, {roll}, {exam_name}, {exam_year}, {grade}, {gpa}, {total}, {percentage}, {status}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Schedule (optional)</label>
                    <input type="datetime-local" name="scheduled_at"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                    💬 Send to All Parents
                </button>
            </form>
        </div>

        {{-- Single Send + Logs --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-800 mb-4">Send to Single Number</h2>
                <form method="POST" action="{{ route('sms.send-single') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" required placeholder="+8801700000000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Message</label>
                        <textarea name="message" rows="3" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 text-sm"><input type="radio" name="channel" value="sms" checked> SMS</label>
                        <label class="flex items-center gap-2 text-sm"><input type="radio" name="channel" value="whatsapp"> WhatsApp</label>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg text-sm transition-colors">
                        Send Message
                    </button>
                </form>
            </div>

            {{-- Recent Logs --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Recent Messages</h3>
                    <a href="{{ route('sms.logs') }}" class="text-xs text-blue-600 hover:underline">View All →</a>
                </div>
                @forelse($logs->take(5) as $log)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 text-xs">
                    <div>
                        <span class="font-medium text-gray-800">{{ $log->student?->name ?? 'Manual' }}</span>
                        <span class="text-gray-400 ml-1">{{ $log->phone_number }}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ match($log->status) { 'sent' => 'bg-green-100 text-green-700', 'failed' => 'bg-red-100 text-red-600', default => 'bg-gray-100 text-gray-500' } }}">
                        {{ ucfirst($log->status) }}
                    </span>
                </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-4">No messages sent yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function fetchSections(classId) {
    const sel = document.getElementById('section_id');
    sel.innerHTML = '<option value="">Loading...</option>';
    if (!classId) { sel.innerHTML = '<option value="">-- Section --</option>'; return; }
    fetch(`/api/sections-by-class?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Section --</option>';
            data.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
        });
}
</script>
@endpush
