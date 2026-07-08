@extends('layouts.app')
@section('title', 'SMS / WhatsApp Logs')

@section('content')
<div class="py-4 space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $logs->total() }} messages total</p>
        <a href="{{ route('sms.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">← Back</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($logs->isEmpty())
            <div class="py-12 text-center text-gray-400 text-sm">No messages sent yet.</div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Student</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Phone</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Channel</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Message</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Sent At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $log->student?->name ?? 'Manual' }}</td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $log->phone_number }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                {{ $log->channel === 'whatsapp' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ strtoupper($log->channel) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate" title="{{ $log->message }}">
                            {{ Str::limit($log->message, 60) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ match($log->status) { 'sent' => 'bg-green-100 text-green-700', 'failed' => 'bg-red-100 text-red-600', default => 'bg-gray-100 text-gray-500' } }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">
                            {{ $log->sent_at?->format('d M Y H:i') ?? ($log->scheduled_at?->format('d M Y H:i (scheduled)') ?? '–') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
