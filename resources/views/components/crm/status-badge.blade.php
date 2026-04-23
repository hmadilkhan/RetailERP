@props([
    'label',
    'color' => '#114a8f',
])

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-3 py-1 text-xs font-semibold']) }}
    style="background-color: {{ $color }}15; color: {{ $color }};">
    {{ $label }}
</span>
