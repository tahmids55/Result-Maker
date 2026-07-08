@extends('layouts.app')
@section('title', 'Students')

@section('content')
<div class="py-4 space-y-4" x-data="{ 
    selectedIds: [], 
    selectAll: false, 
    toggleAll() { this.selectedIds = this.selectAll ? {{ json_encode($students->pluck('id')->toArray()) }} : [] },
    showModal: false,
    viewStudent: null,
    openViewModal(student) {
        this.viewStudent = student;
        this.showModal = true;
    }
}">

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('students.index') }}" class="flex flex-col gap-3">
            <!-- Top Row: Search & Actions -->
            <div class="flex gap-2">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search name, roll, reg. no..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit" title="Search" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <a href="{{ route('students.index') }}" title="Clear Filters" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                    ✕
                </a>
            </div>

            <!-- Bottom Row: Dropdown Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <select name="class_id" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>

                <select name="section_id" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                    @endforeach
                </select>

                <select name="per_page" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="30" {{ request('per_page', 30) == 30 ? 'selected' : '' }}>30 per page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="70" {{ request('per_page') == 70 ? 'selected' : '' }}>70 per page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <p class="text-sm text-gray-500">{{ $students->total() }} students found</p>
        <div class="flex gap-2">
            {{-- Bulk Import --}}
            <div x-data="{ open: false }">
                <button @click="open=!open" class="border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    📥 Import CSV
                </button>
                <div x-show="open" x-cloak @click.away="open=false"
                     class="absolute z-20 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg p-4 w-80">
                    <p class="text-xs font-semibold text-gray-700 mb-2">Import Students via CSV</p>
                    <p class="text-xs text-gray-500 mb-3">Format: name, roll, father_name, mother_name, class_name, section_name, session</p>
                    <form method="POST" action="{{ route('students.bulk-import') }}" enctype="multipart/form-data" class="space-y-2">
                        @csrf
                        <input type="file" name="csv_file" accept=".csv,.txt" required class="w-full text-xs border border-gray-300 rounded px-2 py-1.5">
                        <button type="submit" class="w-full bg-green-600 text-white text-xs font-medium py-2 rounded-lg">Import</button>
                    </form>
                </div>
            </div>
            <form method="POST" action="{{ route('students.bulk-delete') }}" 
                  x-show="selectedIds.length > 0" x-cloak
                  @submit.prevent="deleteForm = $event.target; confirmDelete = true;">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="student_ids[]" :value="id">
                </template>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    🗑 Delete Selected (<span x-text="selectedIds.length"></span>)
                </button>
            </form>

            <a href="{{ route('students.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Add Student
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($students->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">👥</p>
                <p class="text-sm">No students found. <a href="{{ route('students.create') }}" class="text-blue-600 hover:underline">Add one →</a></p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 w-10">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Roll</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Class / Section</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Father</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Session</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Reg. No.</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $student->id }}" x-model="selectedIds" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-700 font-medium">{{ $student->roll }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700 flex-shrink-0">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $student->schoolClass->name }} / {{ $student->section->name }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $student->father_name ?? '–' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $student->session ?? '–' }}</td>
                            <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $student->registration_no ?? '–' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click='openViewModal({{ json_encode([
                                        "name" => $student->name,
                                        "roll" => $student->roll,
                                        "class" => $student->schoolClass->name,
                                        "section" => $student->section->name,
                                        "father_name" => $student->father_name ?? "–",
                                        "mother_name" => $student->mother_name ?? "–",
                                        "session" => $student->session ?? "–",
                                        "registration_no" => $student->registration_no ?? "–",
                                        "dob" => $student->dob ? $student->dob->format("d M, Y") : "–",
                                        "phone" => $student->phone ?? "–",
                                        "address" => $student->address ?? "–",
                                        "gender" => $student->gender ?? "–",
                                        "photo_url" => $student->photo_url,
                                        "edit_url" => route("students.edit", $student)
                                    ], JSON_HEX_APOS) }})'
                                       class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded transition-colors cursor-pointer">
                                        View
                                    </button>
                                    <a href="{{ route('students.edit', $student) }}"
                                       class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('students.destroy', $student) }}"
                                          @submit.prevent="deleteForm = $event.target; confirmDelete = true;">
                                        @csrf @method('DELETE')
                                        <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    {{-- View Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.away="showModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <!-- Header -->
            <div class="flex justify-between items-center p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Student Profile</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <div class="flex items-center gap-5 mb-6 pb-6 border-b border-gray-100">
                    <template x-if="viewStudent?.photo_url">
                        <img :src="viewStudent.photo_url" class="w-20 h-20 rounded-full object-cover border border-gray-200 shadow-sm" alt="Student Photo">
                    </template>
                    <template x-if="!viewStudent?.photo_url">
                        <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-2xl font-bold text-blue-700 shadow-sm border border-blue-200" x-text="viewStudent?.name?.substring(0, 1).toUpperCase()"></div>
                    </template>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900" x-text="viewStudent?.name"></h4>
                        <p class="text-sm text-gray-500 font-medium mt-1">
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded" x-text="viewStudent?.class + ' / ' + viewStudent?.section"></span>
                            <span class="ml-2">Roll: <span class="font-mono text-gray-700" x-text="viewStudent?.roll"></span></span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Gender</p>
                        <p class="text-sm text-gray-800 font-medium" x-text="viewStudent?.gender"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Reg. No</p>
                        <p class="text-sm text-gray-800 font-medium font-mono" x-text="viewStudent?.registration_no"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Session</p>
                        <p class="text-sm text-gray-800 font-medium" x-text="viewStudent?.session"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date of Birth</p>
                        <p class="text-sm text-gray-800 font-medium" x-text="viewStudent?.dob"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Father's Name</p>
                        <p class="text-sm text-gray-800 font-medium" x-text="viewStudent?.father_name"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mother's Name</p>
                        <p class="text-sm text-gray-800 font-medium" x-text="viewStudent?.mother_name"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Phone</p>
                        <p class="text-sm text-gray-800 font-medium" x-text="viewStudent?.phone"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Address</p>
                        <p class="text-sm text-gray-800 font-medium" x-text="viewStudent?.address"></p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Close
                </button>
                <a :href="viewStudent?.edit_url" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Edit Student
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
