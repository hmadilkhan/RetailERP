<div class="text-sm text-erp-mute">
    Showing {{ $details->firstItem() ?? 0 }} to {{ $details->lastItem() ?? 0 }} of {{ $details->total() }} branches
</div>
<div class="flex gap-2">
    @if ($details->onFirstPage())
        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Previous</span>
    @else
        <a href="{{ $details->previousPageUrl() }}" data-page-link
            class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Previous</a>
    @endif

    @if ($details->hasMorePages())
        <a href="{{ $details->nextPageUrl() }}" data-page-link
            class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Next</a>
    @else
        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Next</span>
    @endif
</div>
