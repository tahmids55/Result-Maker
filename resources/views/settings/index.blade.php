@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div class="py-4 space-y-6" x-data="{ tab: 'school' }">

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
        @foreach([['school','🏫 School Info'],['grades','📊 Grading System'],['backup','💾 Backup'],['general','⚙️ General Settings']] as [$key, $label])
        <button @click="tab='{{ $key }}'"
                :class="tab==='{{ $key }}' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- School Info --}}
    <div x-show="tab==='school'" x-cloak>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-2xl">
            <h2 class="text-base font-semibold text-gray-800 mb-5">School Information</h2>
            <form method="POST" action="{{ route('settings.school') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Name *</label>
                        <input type="text" name="name" value="{{ old('name', $school?->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $school?->phone) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $school?->email) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old('address', $school?->address) }}</textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text (on Marksheets)</label>
                        <textarea name="footer_text" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old('footer_text', $school?->footer_text) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Format</label>
                        <select name="date_format" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="d/m/Y" {{ ($school?->date_format ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            <option value="m/d/Y" {{ ($school?->date_format) === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            <option value="Y-m-d" {{ ($school?->date_format) === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">GPA Scale</label>
                        <select name="gpa_scale" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="5.0" {{ ($school?->gpa_scale ?? '5.0') === '5.0' ? 'selected' : '' }}>5.0 Scale</option>
                            <option value="4.0" {{ ($school?->gpa_scale) === '4.0' ? 'selected' : '' }}>4.0 Scale</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Logo</label>
                        @if($school?->logo)
                            <img src="{{ $school->logo_url }}" alt="Logo" class="w-16 h-16 object-contain border rounded mb-1">
                        @endif
                        <input type="file" name="logo" accept="image/*"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Principal Signature</label>
                        @if($school?->signature)
                            <img src="{{ $school->signature_url }}" alt="Signature" class="w-24 h-12 object-contain border rounded mb-1">
                        @endif
                        <input type="file" name="signature" accept="image/*"
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-5 mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMS API Key</label>
                        <input type="text" name="sms_api_key" value="{{ old('sms_api_key', $school?->sms_api_key) }}"
                               placeholder="Enter SMS API Key"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp API Key</label>
                        <input type="text" name="whatsapp_api_key" value="{{ old('whatsapp_api_key', $school?->whatsapp_api_key) }}"
                               placeholder="Enter WhatsApp API Key"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Grading System --}}
    <div x-show="tab==='grades'" x-cloak>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-3xl" x-data="gradeEditor()">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold text-gray-800">Grading System</h2>
                <button type="button" @click="addRow()"
                        class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium px-3 py-1.5 rounded-lg">
                    + Add Grade
                </button>
            </div>
            <form method="POST" action="{{ route('settings.grades') }}" class="space-y-3">
                @csrf
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Grade</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">GPA</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Min %</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Max %</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Label</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(g, i) in grades" :key="i">
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="text" :name="`grades[${i}][grade]`" x-model="g.grade"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="`grades[${i}][gpa]`" x-model="g.gpa" step="0.01" min="0" max="5"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="`grades[${i}][min_percentage]`" x-model="g.min" step="0.01"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="`grades[${i}][max_percentage]`" x-model="g.max" step="0.01"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" :name="`grades[${i}][label]`" x-model="g.label"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @click="grades.splice(i,1)" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                    Save Grading System
                </button>
            </form>
        </div>
    </div>

    {{-- General Settings --}}
    <div x-show="tab==='general'" x-cloak>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-lg space-y-5">
            <h2 class="text-base font-semibold text-gray-800">General Settings</h2>
            
            <form method="POST" action="{{ route('settings.general') }}">
                @csrf
                <div class="flex items-center">
                    <input type="checkbox" name="auto_save_marks" id="auto_save_marks" 
                           value="1" {{ ($school?->auto_save_marks) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    <label for="auto_save_marks" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none">Auto save marks</label>
                    
                    <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative ml-1 flex items-center">
                        <svg class="w-4 h-4 text-gray-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div x-show="open" style="display: none; background-color: rgba(0, 0, 0, 0.75); color: #ffffff; width: 220px;" class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 p-2.5 text-xs rounded-md z-50 text-center shadow-lg leading-relaxed">
                            Auto save will silently save your work in the marks entry grid every 1 minute.
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-[5px] border-transparent" style="border-top-color: rgba(0, 0, 0, 0.75);"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Backup --}}
    <div x-show="tab==='backup'" x-cloak>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-lg space-y-5">
            <h2 class="text-base font-semibold text-gray-800">Database Backup & Restore</h2>
            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                <p class="text-sm font-medium text-green-800 mb-2">Create Backup</p>
                <p class="text-xs text-green-700 mb-3">Downloads a complete SQL dump of the database.</p>
                <a href="{{ route('settings.backup') }}"
                   class="inline-block bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
                    ⬇ Download Backup
                </a>
            </div>
            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                <p class="text-sm font-medium text-red-800 mb-2">⚠ Restore from Backup</p>
                <p class="text-xs text-red-700 mb-3">This will overwrite the current database. Use with caution.</p>
                <form method="POST" action="{{ route('settings.restore') }}" enctype="multipart/form-data"
                      @submit.prevent="deleteForm = $event.target; confirmDelete = true;">
                    @csrf
                    <input type="file" name="backup_file" accept=".sql" required class="w-full text-xs border border-red-300 rounded px-2 py-1.5 mb-2 bg-white">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
                        🔄 Restore Database
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function gradeEditor() {
    return {
        grades: {!! json_encode($grades->map(fn($g) => ['grade' => $g->grade, 'gpa' => $g->gpa, 'min' => $g->min_percentage, 'max' => $g->max_percentage, 'label' => $g->label])->toArray()) !!},
        addRow() {
            this.grades.push({ grade: '', gpa: 0, min: 0, max: 0, label: '' });
        }
    };
}
</script>
@endpush
