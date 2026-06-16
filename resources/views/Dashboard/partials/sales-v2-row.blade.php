@php
    $currency = session('currency');
    $rowClass = 'flex items-center justify-between gap-4 px-5 py-3 text-sm';
    $clickable = !empty($action);
@endphp

<div class="{{ $rowClass }} {{ $clickable ? 'cursor-pointer transition hover:bg-green-50' : '' }}" @if ($clickable) onclick="{{ $action }}" @endif>
    <div class="font-bold text-erp-text">{{ $label }}</div>
    <div class="shrink-0 font-black text-erp-ink">{{ $currency }} {{ number_format($amount ?? 0, 0) }}</div>
</div>
