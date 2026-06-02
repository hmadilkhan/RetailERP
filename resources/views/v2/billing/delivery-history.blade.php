@extends('layouts.master-tailwind')

@section('title', 'Billing Delivery History')
@section('page_title', 'Billing Delivery History')
@section('page_subtitle', 'Track invoice generation runs, WhatsApp delivery attempts, skipped records, failures, and overdue enforcement.')

@section('content')
    <div class="space-y-6">
        @if($recentRuns->count() > 0)
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($recentRuns as $run)
                    <a href="{{ route('billing.delivery-history', ['run_id' => $run->batch_uuid]) }}" class="rounded-lg border border-erp-line bg-white p-5 shadow-sm transition hover:border-erp hover:shadow-md">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Recent Run</div>
                        <div class="mt-3 text-lg font-black text-erp-ink">{{ \Illuminate\Support\Str::limit($run->batch_uuid, 16, '...') }}</div>
                        <p class="mt-2 text-sm text-erp-mute">{{ date('M d, Y h:i A', strtotime($run->created_at)) }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($run->log_name === 'billing_overdue_run')
                                <span class="rounded-md bg-sky-50 px-2 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-200">Companies {{ $run->affected_company_count ?? 0 }}</span>
                                <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">Locked {{ $run->locked_company_count ?? 0 }}</span>
                            @else
                                <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">Sent {{ $run->whatsapp_sent_count }}</span>
                                <span class="rounded-md bg-amber-50 px-2 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">Skipped {{ $run->total_skipped_count }}</span>
                                <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">Failed {{ $run->total_failed_count }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </section>
        @endif

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-erp-ink">Delivery Log</h2>
                        <p class="mt-1 text-sm text-erp-mute">Filter by company, status, invoice number, run ID, or date.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('billing.invoices.index') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Billing Invoices</a>
                        <a href="{{ route('billing.summary') }}" class="inline-flex h-10 items-center rounded-lg border border-sky-200 bg-sky-50 px-4 text-sm font-bold text-sky-700 transition hover:bg-sky-100">Billing Summary</a>
                    </div>
                </div>
                <form method="GET" action="{{ route('billing.delivery-history') }}" class="mt-4 grid gap-2 md:grid-cols-3 xl:grid-cols-6">
                    <select name="company_id" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="All Companies">
                        <option value="">All Companies</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_id }}" {{ request('company_id') == $company->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="All Statuses">
                        <option value="">All Statuses</option>
                        @foreach (['sent', 'skipped', 'failed', 'processed'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="invoice_no" value="{{ request('invoice_no') }}" placeholder="Invoice #"
                        class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <input type="text" name="run_id" value="{{ request('run_id') }}" placeholder="Run ID"
                        class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <input type="date" name="date" value="{{ request('date') }}"
                        class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <div class="flex flex-wrap gap-2">
                        <button class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Filter</button>
                        <a href="{{ route('billing.delivery-history') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Date</th>
                            <th class="px-5 py-3 text-left font-bold">Stage</th>
                            <th class="px-5 py-3 text-left font-bold">Company</th>
                            <th class="px-5 py-3 text-left font-bold">Invoice</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="px-5 py-3 text-left font-bold">Recipient</th>
                            <th class="px-5 py-3 text-left font-bold">Run ID</th>
                            <th class="px-5 py-3 text-left font-bold">Detail</th>
                            <th class="min-w-36 px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($deliveryLogs as $log)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-erp-mute">{{ date('M d, Y h:i A', strtotime($log->created_at)) }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold uppercase ring-1 {{ $log->stage === 'generation' ? 'bg-sky-50 text-sky-700 ring-sky-200' : ($log->stage === 'overdue_enforcement' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-200') }}">{{ $log->stage === 'overdue_enforcement' ? 'Overdue' : $log->stage }}</span>
                                </td>
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ $log->company_name ?? 'N/A' }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $log->invoice_no ?? 'N/A' }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold uppercase ring-1 {{ $log->status === 'sent' || $log->status === 'processed' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : ($log->status === 'failed' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-amber-50 text-amber-700 ring-amber-200') }}">{{ $log->status }}</span>
                                </td>
                                <td class="px-5 py-4 text-erp-mute">{{ $log->stage === 'whatsapp' ? ($log->to ?? '-') : '-' }}</td>
                                <td class="px-5 py-4">
                                    @if($log->batch_uuid)
                                        <a href="{{ route('billing.delivery-history', ['run_id' => $log->batch_uuid]) }}" class="font-bold text-erp-dark hover:text-erp">{{ \Illuminate\Support\Str::limit($log->batch_uuid, 12, '...') }}</a>
                                    @else
                                        <span class="text-erp-mute">-</span>
                                    @endif
                                </td>
                                <td class="max-w-md px-5 py-4 text-erp-mute">
                                    <div class="line-clamp-3">{{ $log->reason ?? $log->description }}</div>
                                    @if($log->stage === 'overdue_enforcement')
                                        <div class="mt-2 text-xs">Company: {{ $log->company_action ?? '-' }} | Lock: {{ $log->lock_action ?? '-' }}</div>
                                    @endif
                                </td>
                                <td class="min-w-36 whitespace-nowrap px-5 py-4 text-right">
                                    @if($log->invoice_id)
                                        <a href="{{ route('billing.invoices.show', $log->invoice_id) }}" class="inline-flex h-9 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 px-3 text-xs font-bold text-sky-700 transition hover:bg-sky-100">View Invoice</a>
                                    @else
                                        <span class="text-sm text-erp-mute">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No delivery history found</div>
                                    <p class="mt-2 text-sm text-erp-mute">Try changing filters or review recent billing runs.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-erp-line px-5 py-4">
                {{ $deliveryLogs->links('pagination::tailwind') }}
            </div>
        </section>
    </div>
@endsection
