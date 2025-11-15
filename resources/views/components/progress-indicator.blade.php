@props([
    'total',
    'completed',
    'label' => 'Progress'
])

@php
$percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
@endphp

<div>
    <div class="flex items-center justify-between">
        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
        <span class="text-sm font-medium text-gray-700">{{ $completed }} / {{ $total }}</span>
    </div>
    <div class="mt-2">
        <div class="overflow-hidden rounded-full bg-gray-200">
            <div class="h-2 rounded-full bg-indigo-600 transition-all duration-500" style="width: {{ $percentage }}%"></div>
        </div>
    </div>
    <p class="mt-1 text-xs text-gray-500">{{ $percentage }}% complete</p>
</div>
