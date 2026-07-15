{{--
    Accessible Chart Component
    
    Usage:
    <x-accessible-chart
        id="myChart"
        type="doughnut"
        title="Grade Distribution"
        :labels="['A+', 'A', 'B']"
        :values="[10, 15, 8]"
        :colors="['#16a34a', '#3b82f6', '#f97316']"
        x-label="Grade"
        y-label="Students"
        height="220"
    />
--}}

@props([
    'id',
    'type' => 'bar',
    'title' => 'Chart',
    'labels' => [],
    'values' => [],
    'colors' => [],
    'xLabel' => 'Category',
    'yLabel' => 'Value',
    'height' => 220,
    'legend' => true,
    'borderRadius' => 8,
])

@php
    $dataPoints = collect($labels)->zip($values)->map(fn($pair) => ['label' => $pair[0], 'value' => $pair[1]])->toArray();
    $chartId = $id ?? 'chart_' . uniqid();
@endphp

<div x-data="accessibleChart({
        chartId: '{{ $chartId }}',
        type: '{{ $type }}',
        title: @js($title),
        labels: @js($labels),
        values: @js($values),
        colors: @js($colors),
        xLabel: @js($xLabel),
        yLabel: @js($yLabel),
        legend: {{ $legend ? 'true' : 'false' }},
        borderRadius: {{ $borderRadius }},
        dataPoints: @js($dataPoints),
     })"
     role="figure"
     :aria-label="ariaLabel"
     tabindex="0"
     @keydown.left.prevent="prevDatapoint"
     @keydown.right.prevent="nextDatapoint"
     @keydown.enter.prevent="announceValue"
     @focus="showFocusHint = true"
     @blur="showFocusHint = false"
     class="relative outline-none focus:ring-2 focus:ring-accent ring-offset-2 rounded-lg">

    {{-- Canvas --}}
    <canvas x-ref="canvas" height="{{ $height }}"></canvas>

    {{-- Keyboard focus hint --}}
    <div x-show="showFocusHint" x-cloak
         class="absolute top-2 right-2 text-[10px] text-ink-faint bg-surface-raised px-2 py-1 rounded shadow-sm animate-fade-in">
        ← → to navigate, Enter to announce
    </div>

    {{-- Screen-reader fallback data table --}}
    <table class="sr-only" role="table" :aria-label="'Data table for ' + title">
        <caption x-text="title"></caption>
        <thead>
            <tr>
                <th scope="col" x-text="xLabel"></th>
                <th scope="col" x-text="yLabel"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataPoints as $i => $point)
            <tr>
                <td>{{ $point['label'] }}</td>
                <td>{{ $point['value'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Live region for screen reader announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" x-text="announcement"></div>
</div>
