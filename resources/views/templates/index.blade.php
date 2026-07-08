@extends('layouts.app')
@section('title', 'Marksheet Templates')

@section('content')
<div class="py-4 space-y-4" x-data="{ editModalOpen: false, editForm: { id: '', name: '', description: '', url: '' }, openEditModal(template, editUrl) { this.editForm.id = template.id; this.editForm.name = template.name; this.editForm.description = template.description || ''; this.editForm.url = editUrl; this.editModalOpen = true; } }">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">Upload a .docx file designed in MS Word. The system will detect placeholders like <code class="bg-gray-100 px-1 rounded">${student_name}</code>.</p>
        <a href="{{ route('templates.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Upload Template
        </a>
    </div>

    @if($templates->isEmpty())
        <div class="bg-white rounded-xl border border-dashed border-gray-300 p-12 text-center">
            <p class="text-4xl mb-3">📄</p>
            <p class="text-gray-500 mb-4">No templates yet. Upload your first marksheet template.</p>
            <a href="{{ route('templates.create') }}" class="text-blue-600 font-medium hover:underline">Upload Template →</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $template)
            <div class="bg-white rounded-xl border {{ $template->is_default ? 'border-blue-400 ring-2 ring-blue-200' : 'border-gray-200' }} shadow-sm p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="font-semibold text-gray-900 flex items-center gap-2">
                            {{ $template->name }}
                            <button type="button" @click='openEditModal(@json(["id" => $template->id, "name" => $template->name, "description" => $template->description]), "{{ route("templates.update", $template) }}")' class="text-gray-400 hover:text-blue-600 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </div>
                        @if($template->is_default)
                            <span class="inline-block mt-1 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Default</span>
                        @endif
                    </div>
                    <span class="text-2xl">📄</span>
                </div>

                @if($template->description)
                    <p class="text-xs text-gray-500 mb-3">{{ $template->description }}</p>
                @endif

                <div class="mb-3">
                    <p class="text-xs font-medium text-gray-600 mb-1">Detected Placeholders ({{ count($template->placeholders ?? []) }})</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach(array_slice($template->placeholders ?? [], 0, 6) as $ph)
                            <code class="text-xs bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded">{{ '${' . $ph . '}' }}</code>
                        @endforeach
                        @if(count($template->placeholders ?? []) > 6)
                            <span class="text-xs text-gray-400">+{{ count($template->placeholders) - 6 }} more</span>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('templates.map', $template) }}"
                       class="flex-1 text-center text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-1.5 rounded-lg transition-colors">
                        Map Fields
                    </a>
                    <a href="{{ route('onlyoffice.edit', $template->id) }}"
                       class="flex-1 text-center text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium py-1.5 rounded-lg transition-colors" title="Edit Template Visually">
                        Visual Edit
                    </a>
                    @if(!$template->is_default)
                    <form method="POST" action="{{ route('templates.set-default', $template) }}" class="flex-1">
                        @csrf
                        <button class="w-full text-xs bg-green-50 hover:bg-green-100 text-green-700 font-medium py-1.5 rounded-lg transition-colors">
                            Set Default
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('templates.destroy', $template) }}"
                          @submit.prevent="deleteForm = $event.target; confirmDelete = true;">
                        @csrf @method('DELETE')
                        <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-medium px-3 py-1.5 rounded-lg transition-colors">
                            🗑
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Edit Template Modal --}}
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4">
        <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="editModalOpen = false">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
        </div>
        
        <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-md mx-auto z-50">
                <form :action="editForm.url" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="sm:flex sm:items-start w-full">
                            <div class="text-center sm:text-left w-full">
                                <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                    Edit Template Details
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Template Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" id="name" x-model="editForm.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border" required>
                                    </div>
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea name="description" id="description" x-model="editForm.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full sm:w-1/2 inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Save Changes
                        </button>
                        <button type="button" @click="editModalOpen = false" class="w-full sm:w-1/2 inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
