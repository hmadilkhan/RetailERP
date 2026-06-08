@php
    $statusText = $status ?? '';
    $statusClass = match ($statusText) {
        'Draft' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'Pending', 'Placed', 'Completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'Approved', 'In-Process' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'Delivered' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'Cancel', 'Cancelled', 'Rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
        default => 'bg-slate-100 text-slate-700 ring-slate-200',
    };
@endphp

<span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $statusClass }}">{{ $statusText }}</span>
