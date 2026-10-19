{{-- BA.6: band branch me stock aaya (BranchAvailabilityService::warnIfDisabled) — agle poore page pe ek baar dikhao --}}
@php
    $baWarnings = (array) session()->pull('ba_warnings', []);
@endphp
@foreach ($baWarnings as $baWarning)
    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-900 shadow-sm">{{ $baWarning }}</div>
@endforeach
