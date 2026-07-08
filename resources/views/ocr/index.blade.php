@extends('layouts.app')
@section('title', 'OCR Import')

@section('content')
<div class="py-4 space-y-4">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Upload --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-1">Upload Image for OCR</h2>
            <p class="text-xs text-gray-500 mb-4">Upload a photo/scan of a student list or mark sheet. Supports English and Bengali.</p>

            <form method="POST" action="{{ route('ocr.upload') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div x-data="{ fileName: '' }">
                    <div @click="$refs.fi.click()"
                         class="border-2 border-dashed border-gray-300 hover:border-blue-400 rounded-xl p-8 text-center cursor-pointer transition-colors">
                        <div class="text-4xl mb-2">🔍</div>
                        <p x-show="!fileName" class="text-sm text-gray-500">Click to upload JPG or PNG</p>
                        <p x-show="fileName" x-text="'✔ ' + fileName" class="text-sm text-green-700 font-medium"></p>
                        <input type="file" x-ref="fi" name="image" accept="image/jpeg,image/png"
                               @change="fileName=$event.target.files[0]?.name" class="hidden" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                    <select name="language" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="eng">English</option>
                        <option value="ben">Bengali</option>
                        <option value="eng+ben">English + Bengali</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                    🚀 Start OCR Processing
                </button>
            </form>
        </div>

        {{-- Bulk ZIP --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-1">Bulk Upload (ZIP)</h2>
            <p class="text-xs text-gray-500 mb-4">Upload a ZIP file containing multiple images. Each will be processed separately.</p>
            <form method="POST" action="{{ route('ocr.bulk-upload') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div x-data="{ fileName: '' }">
                    <div @click="$refs.zf.click()"
                         class="border-2 border-dashed border-purple-300 hover:border-purple-400 rounded-xl p-8 text-center cursor-pointer transition-colors">
                        <div class="text-4xl mb-2">🗜️</div>
                        <p x-show="!fileName" class="text-sm text-gray-500">Click to upload ZIP file (max 50MB)</p>
                        <p x-show="fileName" x-text="'✔ ' + fileName" class="text-sm text-green-700 font-medium"></p>
                        <input type="file" x-ref="zf" name="zip_file" accept=".zip"
                               @change="fileName=$event.target.files[0]?.name" class="hidden" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                    <select name="language" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="eng">English</option>
                        <option value="ben">Bengali</option>
                        <option value="eng+ben">English + Bengali</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                    🚀 Upload & Process All
                </button>
            </form>
        </div>
    </div>

    {{-- History --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">OCR Import History</h3>
        </div>
        @if($imports->isEmpty())
            <div class="py-12 text-center text-gray-400 text-sm">No imports yet.</div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Image</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Language</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($imports as $import)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ basename($import->image_path) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ match($import->status) {
                                    'processed' => 'bg-green-100 text-green-700',
                                    'failed'    => 'bg-red-100 text-red-600',
                                    'processing'=> 'bg-yellow-100 text-yellow-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ ucfirst($import->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ strtoupper($import->language) }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $import->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($import->isProcessed())
                                <a href="{{ route('ocr.show', $import) }}"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1 rounded transition-colors">
                                    Review & Import →
                                </a>
                            @elseif($import->isFailed())
                                <span class="text-xs text-red-500" title="{{ $import->error_message }}">Error</span>
                            @else
                                <span class="text-xs text-gray-400">Processing...</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-200">{{ $imports->links() }}</div>
        @endif
    </div>
</div>
@endsection
