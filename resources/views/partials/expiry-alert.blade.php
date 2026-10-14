{{-- Phase 2.2: expire / jald expire hone wale lots ka alert (ExpiryService::alert, 10 min cache) --}}
@php $expiryAlert = \App\Services\Inventory\ExpiryService::alert(); @endphp
@if ($expiryAlert)
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-900">
        <span>
            @if ($expiryAlert['expired']['lots'])<b>{{ $expiryAlert['expired']['lots'] }} lot(s) have expired</b> and still have stock.@endif
            @if ($expiryAlert['expiring']['lots'])<b>{{ $expiryAlert['expiring']['lots'] }} lot(s)</b> expire in the next {{ \App\Services\Inventory\ExpiryService::ALERT_DAYS }} days.@endif
        </span>
        <a href="{{ url('expiry-report') }}" class="font-bold text-amber-900 underline">View Expiry Report</a>
    </div>
@endif
