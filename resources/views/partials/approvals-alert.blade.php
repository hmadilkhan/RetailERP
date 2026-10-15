{{-- Phase 2.4: approver ko pending approvals ki ginti (ApprovalService::pendingCountFor, 1 min cache). Approvals page pe khud nahi. --}}
@php
    $pendingApprovals = (session('company_id') && !request()->is('approvals'))
        ? app(\App\Services\Approvals\ApprovalService::class)->pendingCountFor((int) session('company_id'), (int) session('roleId'), session('userid'), session('branch'))
        : 0;
@endphp
@if ($pendingApprovals > 0)
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-sky-200 bg-sky-50 px-5 py-3 text-sm text-sky-900 shadow-sm">
        <span><b>{{ $pendingApprovals }}</b> request(s) are waiting for your approval.</span>
        <a href="{{ url('approvals') }}" class="font-bold text-sky-900 underline">Open Approvals</a>
    </div>
@endif
