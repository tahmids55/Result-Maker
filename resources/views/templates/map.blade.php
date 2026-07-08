@extends('layouts.app')
@section('title', 'Map Placeholders – ' . $template->name)

@section('content')
<div class="py-4 max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-gray-800">Map Placeholders: {{ $template->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                Assign each detected placeholder to the corresponding database field.
                Unmapped placeholders will be left blank in generated marksheets.
            </p>
        </div>

        @if(empty($template->placeholders))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                ⚠ No placeholders detected in this template. Make sure your .docx uses <code>${placeholder_name}</code> format.
            </div>
        @else
        <form method="POST" action="{{ route('templates.save-map', $template) }}" class="space-y-4">
            @csrf

            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-b">Placeholder in Template</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-b">Maps To</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($template->placeholders as $placeholder)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <code class="bg-blue-50 text-blue-800 px-2 py-1 rounded text-xs font-mono">{{ '${' . $placeholder . '}' }}</code>
                            </td>
                            <td class="px-4 py-3">
                                <select name="mappings[{{ $placeholder }}]"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">-- Leave blank --</option>
                                    @foreach($availableFields as $group => $fields)
                                        <optgroup label="{{ $group }}">
                                            @foreach($fields as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ (array_key_exists($placeholder, $template->field_mappings ?? []) ? (($template->field_mappings[$placeholder] ?? '') === $key ? 'selected' : '') : ($placeholder === $key ? 'selected' : '')) }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors text-sm">
                    💾 Save Mappings
                </button>
                <a href="{{ route('templates.index') }}"
                   class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-5 py-2 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
