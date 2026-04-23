@props([
    'label',
    'value',
    'helper' => null,
    'tone' => 'slate',
])

@php
    $tones = [
        'blue' => 'from-blue-50 to-white text-blue-900 ring-blue-200',
        'cyan' => 'from-cyan-50 to-white text-cyan-900 ring-cyan-200',
        'indigo' => 'from-indigo-50 to-white text-indigo-900 ring-indigo-200',
        'amber' => 'from-amber-50 to-white text-amber-900 ring-amber-200',
        'rose' => 'from-rose-50 to-white text-rose-900 ring-rose-200',
        'emerald' => 'from-emerald-50 to-white text-emerald-900 ring-emerald-200',
        'slate' => 'from-slate-100 to-white text-slate-900 ring-slate-200',
        'violet' => 'from-violet-50 to-white text-violet-900 ring-violet-200',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-[28px] border border-white/70 bg-gradient-to-br p-5 shadow-crm-soft ring-1 ' . ($tones[$tone] ?? $tones['slate'])]) }}>
    <p class="text-xs font-semibold uppercase tracking-[0.22em] opacity-70">{{ $label }}</p>
    <p class="mt-4 text-3xl font-semibold">{{ $value }}</p>
    @if ($helper)
        <p class="mt-2 text-sm opacity-75">{{ $helper }}</p>
    @endif
</div>
