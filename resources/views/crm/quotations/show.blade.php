@extends('crm.layouts.app')

@section('title', 'Quotation Details')
@section('page_title', 'Quotation Workspace')
@section('page_subtitle', 'A premium CRM quotation view for commercial review, internal tracking, and polished client-ready output.')

@php
    $statusClasses = [
        'draft' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'sent' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'accepted' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'expired' => 'bg-amber-50 text-amber-700 ring-amber-200',
    ];
@endphp

@section('content')
    <section class="overflow-hidden rounded-[36px] border border-white/70 bg-white/90 shadow-crm">
        <div class="bg-gradient-to-r from-crm-deep via-crm-blue to-erp-accent px-6 py-8 text-white sm:px-8">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/90">
                            {{ $quotation->quotation_no }}
                        </span>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$quotation->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                            {{ $quotation->statusLabel() }}
                        </span>
                    </div>
                    <h2 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $lead->company_name ?: $lead->contact_person_name }}</h2>
                    <p class="mt-2 text-base text-slate-200">Proposal linked to lead {{ $lead->lead_code }} for {{ $lead->contact_person_name }}.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if ($canEditQuotation)
                        <a href="{{ route('crm.quotations.edit', $quotation) }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-crm-deep transition hover:bg-slate-100">
                            Edit Quotation
                        </a>
                    @endif
                    <a href="{{ route('crm.quotations.pdf', $quotation) }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                        Download PDF
                    </a>
                    <a href="{{ route('crm.leads.show', $lead) }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                        Back to Lead
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 border-t border-white/60 bg-slate-50/70 px-6 py-5 sm:grid-cols-2 xl:grid-cols-5 sm:px-8">
            @foreach ([
                'Quotation Date' => optional($quotation->quotation_date)->format('d M Y'),
                'Valid Until' => optional($quotation->valid_until)->format('d M Y') ?: 'Open ended',
                'Prepared By' => $quotation->creator->fullname ?? 'System',
                'Lead Status' => $lead->status->name ?? 'Unknown',
                'Grand Total' => number_format((float) $quotation->total, 2),
            ] as $label => $value)
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">{{ $label }}</p>
                    <p class="mt-2 text-sm font-semibold text-crm-ink">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-12">
        <div class="space-y-6 xl:col-span-8">
            <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Quotation Items</h3>
                        <p class="mt-1 text-sm text-crm-mute">Line-item commercial breakdown for this proposal.</p>
                    </div>
                    <div class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                        {{ $quotation->items->count() }} items
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-[24px] border border-crm-line">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50/90">
                                <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">
                                    <th class="px-5 py-4">Item</th>
                                    <th class="px-5 py-4">Description</th>
                                    <th class="px-5 py-4">Qty</th>
                                    <th class="px-5 py-4">Unit Price</th>
                                    <th class="px-5 py-4">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($quotation->items as $item)
                                    <tr>
                                        <td class="px-5 py-4 font-semibold text-crm-ink">{{ $item->item_name }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ $item->description ?: 'N/A' }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format((float) $item->quantity, 2) }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format((float) $item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Proposal Notes</h3>
                <p class="mt-1 text-sm text-crm-mute">Internal and client-facing narrative attached to the quotation.</p>
                <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50/80 p-5">
                    <div class="whitespace-pre-line text-sm leading-7 text-crm-text">{{ $quotation->notes ?: 'No quotation notes added.' }}</div>
                </div>
            </div>
        </div>

        <div class="space-y-6 xl:col-span-4">
            <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Totals Summary</h3>
                <p class="mt-1 text-sm text-crm-mute">A professional summary card for commercial approval and printing.</p>
                <div class="mt-6 rounded-[28px] border border-blue-200 bg-blue-50 p-5">
                    <div class="flex items-center justify-between text-sm text-blue-900">
                        <span>Subtotal</span>
                        <span>{{ number_format((float) $quotation->subtotal, 2) }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm text-blue-900">
                        <span>Discount</span>
                        <span>{{ number_format((float) $quotation->discount, 2) }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm text-blue-900">
                        <span>Tax</span>
                        <span>{{ number_format((float) $quotation->tax, 2) }}</span>
                    </div>
                    <div class="mt-4 border-t border-blue-200 pt-4">
                        <div class="flex items-center justify-between text-base font-semibold text-blue-950">
                            <span>Total</span>
                            <span>{{ number_format((float) $quotation->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Lead Context</h3>
                <p class="mt-1 text-sm text-crm-mute">Commercial context from the linked lead.</p>
                <div class="mt-6 grid gap-4">
                    @foreach ([
                        'Lead Code' => $lead->lead_code,
                        'Lead Contact' => $lead->contact_person_name,
                        'Company' => $lead->company_name ?: 'N/A',
                        'Source' => $lead->leadSource->name ?? 'N/A',
                        'Assigned To' => $lead->assignedUser->fullname ?? 'Unassigned',
                    ] as $label => $value)
                        <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">{{ $label }}</div>
                            <div class="mt-2 text-sm font-medium text-crm-text">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
