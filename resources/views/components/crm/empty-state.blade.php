@props([
    'title',
    'description',
    'tone' => 'slate',
])

@php
    $tones = [
        'slate' => 'border-slate-300 bg-slate-50 text-crm-mute',
        'blue' => 'border-blue-200 bg-blue-50/80 text-blue-800',
        'rose' => 'border-rose-200 bg-rose-50/80 text-rose-800',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-[28px] border border-dashed px-6 py-12 text-center ' . ($tones[$tone] ?? $tones['slate'])]) }}>
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
        {{ $icon ?? '' }}
    </div>
    <p class="mt-4 text-base font-semibold text-crm-ink">{{ $title }}</p>
    <p class="mt-2 text-sm">{{ $description }}</p>
</div>
