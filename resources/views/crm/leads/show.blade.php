@extends('crm.layouts.app')

@section('title', 'Lead Details')
@section('page_title', 'Lead Profile')
@section('page_subtitle', 'A premium CRM view for lead intelligence, follow-up activity, commercial context, next-action planning, and controlled documents.')

@php
    $priorityClasses = [
        'low' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'medium' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
        'high' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        'urgent' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
    ];

    $activityTypeStyles = [
        'lead_created' => ['badge' => 'bg-blue-50 text-blue-700 ring-blue-200', 'icon' => 'plus'],
        'lead_updated' => ['badge' => 'bg-slate-100 text-slate-700 ring-slate-200', 'icon' => 'edit'],
        'lead_assigned' => ['badge' => 'bg-indigo-50 text-indigo-700 ring-indigo-200', 'icon' => 'user'],
        'status_changed' => ['badge' => 'bg-cyan-50 text-cyan-700 ring-cyan-200', 'icon' => 'refresh'],
        'followup_added' => ['badge' => 'bg-amber-50 text-amber-700 ring-amber-200', 'icon' => 'phone'],
        'attachment_uploaded' => ['badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'icon' => 'upload'],
        'attachment_deleted' => ['badge' => 'bg-rose-50 text-rose-700 ring-rose-200', 'icon' => 'trash'],
        'quotation_created' => ['badge' => 'bg-blue-50 text-blue-700 ring-blue-200', 'icon' => 'plus'],
        'quotation_updated' => ['badge' => 'bg-slate-100 text-slate-700 ring-slate-200', 'icon' => 'edit'],
        'quotation_status_changed' => ['badge' => 'bg-amber-50 text-amber-700 ring-amber-200', 'icon' => 'refresh'],
        'lead_marked_won' => ['badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'icon' => 'check'],
        'lead_marked_lost' => ['badge' => 'bg-rose-50 text-rose-700 ring-rose-200', 'icon' => 'x'],
        'lead_converted' => ['badge' => 'bg-violet-50 text-violet-700 ring-violet-200', 'icon' => 'spark'],
    ];

    $fileTypeStyles = [
        'pdf' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'doc' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'docx' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'xls' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'xlsx' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'default' => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];
@endphp

@section('content')
    <section class="overflow-hidden rounded-[36px] border border-white/70 bg-white/90 shadow-crm">
        <div class="bg-gradient-to-r from-crm-deep via-crm-blue to-erp-accent px-6 py-8 text-white sm:px-8">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/90">
                            {{ $lead->lead_code }}
                        </span>
                        @if ($lead->is_converted && $lead->convertedCustomer)
                            <span class="inline-flex rounded-full border border-emerald-200/40 bg-emerald-500/20 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-emerald-100">
                                Converted to Customer
                            </span>
                        @endif
                        @if ($lead->isOverdue())
                            <span class="inline-flex rounded-full border border-rose-200/40 bg-rose-500/20 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-rose-100">
                                Overdue Follow-up
                            </span>
                        @endif
                    </div>
                    <h2 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $lead->contact_person_name }}</h2>
                    <p class="mt-2 text-base text-slate-200">{{ $lead->company_name ?: 'No company name available' }}</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">{{ $lead->status->name ?? 'Unknown' }}</span>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $priorityClasses[$lead->priority] ?? 'bg-blue-50 text-blue-700' }}">
                            {{ ucfirst($lead->priority) }} Priority
                        </span>
                        <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">{{ ucfirst($lead->temperature) }} Lead</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if ($canConvertLead && !$lead->is_converted)
                        <button type="button" x-data @click="$dispatch('open-convert-modal')"
                            class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-emerald-400/15 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-400/25">
                            Convert to Customer
                        </button>
                    @endif
                    @if ($canEditLead)
                        <a href="{{ route('crm.leads.edit', $lead) }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-crm-deep transition hover:bg-slate-100">
                            Edit Lead
                        </a>
                    @endif
                    <a href="{{ route('crm.leads.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                        Back to Leads
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 border-t border-white/60 bg-slate-50/70 px-6 py-5 sm:grid-cols-2 xl:grid-cols-6 sm:px-8">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Assigned Sales Person</p>
                <p class="mt-2 text-sm font-semibold text-crm-ink">{{ $lead->assignedUser->fullname ?? 'Unassigned' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Conversion</p>
                <p class="mt-2 text-sm font-semibold text-crm-ink">
                    {{ $lead->is_converted ? 'Converted' : 'Not Converted' }}
                </p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Lead Score</p>
                <p class="mt-2 text-sm font-semibold text-crm-ink">{{ (int) $lead->lead_score }}/100</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Expected Deal Value</p>
                <p class="mt-2 text-sm font-semibold text-crm-ink">{{ $lead->expected_deal_value ? number_format((float) $lead->expected_deal_value, 2) : '0.00' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Source</p>
                <p class="mt-2 text-sm font-semibold text-crm-ink">{{ $lead->leadSource->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Last Contact</p>
                <p class="mt-2 text-sm font-semibold text-crm-ink">{{ optional($lead->last_contact_date)->format('d M Y') ?: 'Not logged' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Next Follow-up</p>
                <p class="mt-2 text-sm font-semibold {{ $lead->isOverdue() ? 'text-rose-700' : 'text-crm-ink' }}">
                    {{ optional($lead->next_followup_date)->format('d M Y') ?: 'Not scheduled' }}
                </p>
            </div>
        </div>
    </section>

    @if ($canConvertLead && !$lead->is_converted)
        <div x-data="{ open: false }" @open-convert-modal.window="open = true" x-show="open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 px-4">
            <div @click.away="open = false" class="w-full max-w-xl rounded-[32px] border border-white/70 bg-white p-6 shadow-crm">
                <h3 class="text-2xl font-semibold tracking-tight text-crm-ink">Convert Lead to ERP Customer</h3>
                <p class="mt-3 text-sm leading-6 text-crm-mute">
                    This will either link the lead to an existing ERP customer using duplicate checks or create a new ERP customer record from this lead profile.
                </p>

                <div class="mt-5 rounded-3xl border border-slate-200 bg-slate-50/80 p-5 text-sm text-crm-text">
                    <div><span class="font-semibold">Lead:</span> {{ $lead->contact_person_name }}</div>
                    <div class="mt-2"><span class="font-semibold">Company:</span> {{ $lead->company_name ?: 'N/A' }}</div>
                    <div class="mt-2"><span class="font-semibold">Status:</span> {{ $lead->status->name ?? 'Unknown' }}</div>
                    <div class="mt-2"><span class="font-semibold">Rule:</span> Only qualified or won leads can be converted.</div>
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <button type="button" @click="open = false"
                        class="inline-flex items-center justify-center rounded-2xl border border-crm-line bg-white px-5 py-3 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                        Cancel
                    </button>
                    <form action="{{ route('crm.leads.convert', $lead) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Confirm Conversion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <section class="mt-6" x-data="{ tab: 'overview' }">
        <div class="flex flex-wrap gap-3">
            <button type="button" @click="tab = 'overview'"
                :class="tab === 'overview' ? 'bg-crm-blue text-white shadow-lg shadow-blue-900/20' : 'border border-crm-line bg-white text-crm-text'"
                class="rounded-full px-5 py-2.5 text-sm font-semibold transition">
                Overview
            </button>
            <button type="button" @click="tab = 'followups'"
                :class="tab === 'followups' ? 'bg-crm-blue text-white shadow-lg shadow-blue-900/20' : 'border border-crm-line bg-white text-crm-text'"
                class="rounded-full px-5 py-2.5 text-sm font-semibold transition">
                Follow-up History
            </button>
            <button type="button" @click="tab = 'notes'"
                :class="tab === 'notes' ? 'bg-crm-blue text-white shadow-lg shadow-blue-900/20' : 'border border-crm-line bg-white text-crm-text'"
                class="rounded-full px-5 py-2.5 text-sm font-semibold transition">
                Notes / Summary
            </button>
            <button type="button" @click="tab = 'attachments'"
                :class="tab === 'attachments' ? 'bg-crm-blue text-white shadow-lg shadow-blue-900/20' : 'border border-crm-line bg-white text-crm-text'"
                class="rounded-full px-5 py-2.5 text-sm font-semibold transition">
                Attachments
            </button>
            <button type="button" @click="tab = 'quotations'"
                :class="tab === 'quotations' ? 'bg-crm-blue text-white shadow-lg shadow-blue-900/20' : 'border border-crm-line bg-white text-crm-text'"
                class="rounded-full px-5 py-2.5 text-sm font-semibold transition">
                Quotations
            </button>
            <button type="button" @click="tab = 'activity'"
                :class="tab === 'activity' ? 'bg-crm-blue text-white shadow-lg shadow-blue-900/20' : 'border border-crm-line bg-white text-crm-text'"
                class="rounded-full px-5 py-2.5 text-sm font-semibold transition">
                Activity Timeline
            </button>
        </div>

        <div class="mt-6 space-y-6" x-show="tab === 'overview'">
            <div class="grid gap-6 xl:grid-cols-12">
                <div class="space-y-6 xl:col-span-8">
                    <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                        <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Lead Overview</h3>
                        <p class="mt-1 text-sm text-crm-mute">Full profile context across contact, company, product interest, and business fit.</p>
                        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ([
                                'Contact Number' => $lead->contact_number,
                                'Alternate Number' => $lead->alternate_number ?: 'N/A',
                                'WhatsApp' => $lead->whatsapp_number ?: 'N/A',
                                'Email' => $lead->email ?: 'N/A',
                                'Country' => $lead->country ?: 'N/A',
                                'City' => $lead->city ?: 'N/A',
                                'Address' => $lead->address ?: 'N/A',
                                'Website' => $lead->website ?: 'N/A',
                                'Product Type' => $lead->productType->name ?? 'N/A',
                                'Product' => $lead->product->name ?? 'N/A',
                                'Inquiry Type' => $lead->inquiry_type ?: 'N/A',
                                'Business Type' => $lead->business_type ?: 'N/A',
                                'Required Quantity' => $lead->required_quantity ?? 'N/A',
                                'Branch Count' => $lead->branch_count ?? 'N/A',
                                'Existing System' => $lead->existing_system ?: 'N/A',
                                'Competitor Name' => $lead->competitor_name ?: 'N/A',
                                'Budget Range' => $lead->budget_range ?: 'N/A',
                                'Expected Go Live Date' => optional($lead->expected_go_live_date)->format('d M Y') ?: 'N/A',
                            ] as $label => $value)
                                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">{{ $label }}</div>
                                    <div class="mt-2 text-sm font-medium text-crm-text">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                        <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Commercial Snapshot</h3>
                        <p class="mt-1 text-sm text-crm-mute">Context that helps the sales team move the lead toward qualification and proposal.</p>
                        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ([
                                'Source' => $lead->leadSource->name ?? 'N/A',
                                'Campaign' => $lead->campaign_name ?: 'N/A',
                                'Referral Person' => $lead->referral_person_name ?: 'N/A',
                                'Preferred Contact Method' => $lead->preferred_contact_method ? ucfirst($lead->preferred_contact_method) : 'N/A',
                                'Probability' => (int) $lead->probability_percent . '%',
                                'Temperature' => ucfirst($lead->temperature),
                                'Converted' => $lead->is_converted ? 'Yes' : 'No',
                                'Follow-ups Logged' => $lead->followups_count,
                            ] as $label => $value)
                                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">{{ $label }}</div>
                                    <div class="mt-2 text-sm font-medium text-crm-text">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-6 xl:col-span-4">
                    <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                        <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Latest Follow-up</h3>
                        <p class="mt-1 text-sm text-crm-mute">Most recent activity captured against this lead.</p>
                        @if ($lead->latestFollowup)
                            <div class="mt-5 rounded-3xl border border-blue-200 bg-blue-50 p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">
                                        {{ $lead->latestFollowup->followup_type }}
                                    </span>
                                    <span class="text-sm font-medium text-blue-900">{{ $lead->latestFollowup->followup_date->format('d M Y') }}</span>
                                </div>
                                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-blue-950">{{ $lead->latestFollowup->remarks }}</p>
                                <div class="mt-4 space-y-2 text-sm text-blue-900">
                                    <div>Result: {{ $lead->latestFollowup->followup_result ?: 'Not specified' }}</div>
                                    <div>Created by: {{ $lead->latestFollowup->creator->fullname ?? 'System' }}</div>
                                    <div>Next follow-up: {{ optional($lead->latestFollowup->next_followup_date)->format('d M Y') ?: 'Not scheduled' }}</div>
                                </div>
                            </div>
                        @else
                            <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-crm-mute">
                                No follow-up activity has been logged for this lead yet.
                            </div>
                        @endif
                    </div>

                    @if ($canAssignLead)
                        <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Assignment Control</h3>
                            <p class="mt-1 text-sm text-crm-mute">Managers and admins can control ownership directly from the lead profile.</p>

                            <form action="{{ route('crm.leads.assign', $lead) }}" method="POST" class="mt-6 space-y-5">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Assign Lead To</label>
                                    <select name="assigned_to"
                                        class="mt-2 block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                                        <option value="">Unassigned</option>
                                        @foreach ($assignableUsers as $user)
                                            <option value="{{ $user->id }}" @selected((int) $lead->assigned_to === (int) $user->id)>{{ $user->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                                    Save Assignment
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                        <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Ownership Trail</h3>
                        <p class="mt-1 text-sm text-crm-mute">Internal accountability and audit context.</p>
                        <div class="mt-6 grid gap-4">
                            @foreach ([
                                'Assigned To' => $lead->assignedUser->fullname ?? 'Unassigned',
                                'ERP Customer' => $lead->convertedCustomer?->name ?? 'Not linked',
                                'Converted At' => $lead->converted_at?->format('d M Y h:i A') ?: 'Not converted',
                                'Converted By' => $lead->convertedBy->fullname ?? 'N/A',
                                'Created By' => $lead->createdBy->fullname ?? 'System',
                                'Updated By' => $lead->updatedBy->fullname ?? 'System',
                                'Created At' => $lead->created_at?->format('d M Y h:i A'),
                                'Updated At' => $lead->updated_at?->format('d M Y h:i A'),
                            ] as $label => $value)
                                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">{{ $label }}</div>
                                    <div class="mt-2 text-sm font-medium text-crm-text">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>

                        @if ($lead->convertedCustomer && $lead->convertedCustomer->slug)
                            <a href="{{ url('/editcustomers/' . $lead->convertedCustomer->slug) }}"
                                class="mt-5 inline-flex items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                                Open ERP Customer Profile
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-12" x-show="tab === 'followups'" x-cloak>
            <div class="space-y-6 xl:col-span-5">
                <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm" x-data="{ selectedType: '{{ old('followup_type', 'Call') }}' }">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Add New Follow-up</h3>
                            <p class="mt-1 text-sm text-crm-mute">Capture the interaction and keep the lead timeline current.</p>
                        </div>
                    </div>

                    @if ($canAddFollowup)
                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach ($quickFollowupOptions as $type)
                                <button type="button" @click="selectedType = '{{ $type }}'"
                                    :class="selectedType === '{{ $type }}' ? 'bg-crm-blue text-white' : 'border border-crm-line bg-white text-crm-text'"
                                    class="rounded-full px-4 py-2 text-xs font-semibold transition">
                                    {{ $type }} Note
                                </button>
                            @endforeach
                        </div>

                        <form action="{{ route('crm.leads.followups.store', $lead) }}" method="POST" class="mt-6 space-y-5">
                            @csrf
                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Follow-up Date *</label>
                                    <input type="date" name="followup_date"
                                        value="{{ old('followup_date', now()->format('Y-m-d')) }}"
                                        class="mt-2 block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Follow-up Type *</label>
                                    <select name="followup_type" x-model="selectedType"
                                        class="mt-2 block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                                        @foreach ($followupTypeOptions as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Remarks *</label>
                                <textarea name="remarks" rows="5"
                                    class="mt-2 block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm leading-6 focus:border-crm-blue focus:ring-crm-blue"
                                    placeholder="What happened in this interaction?">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Next Follow-up Date</label>
                                    <input type="date" name="next_followup_date" value="{{ old('next_followup_date') }}"
                                        class="mt-2 block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Follow-up Result</label>
                                    <input type="text" name="followup_result" value="{{ old('followup_result') }}"
                                        placeholder="Connected, no answer, demo booked"
                                        class="mt-2 block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                                </div>
                            </div>

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                                Save Follow-up
                            </button>
                        </form>
                    @else
                        <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-crm-mute">
                            You can review follow-up history here, but only users with follow-up permission can add new activity for this lead.
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6 xl:col-span-7">
                <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Follow-up Timeline</h3>
                            <p class="mt-1 text-sm text-crm-mute">Chronological activity history for this lead.</p>
                        </div>
                        <div class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                            {{ $lead->followups_count }} total
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($lead->followups as $index => $followup)
                            <div class="relative rounded-[28px] border p-5 {{ $index === 0 ? 'border-blue-200 bg-blue-50/70 shadow-crm-soft' : 'border-slate-200 bg-white' }}">
                                @if ($index === 0)
                                    <span class="absolute right-4 top-4 inline-flex rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-700 ring-1 ring-blue-200">
                                        Latest
                                    </span>
                                @endif

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-crm-blue ring-1 ring-blue-200">
                                                {{ $followup->followup_type }}
                                            </span>
                                            @if ($followup->next_followup_date && $followup->next_followup_date->isPast() && !$lead->isClosed())
                                                <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-200">
                                                    Next follow-up overdue
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-crm-text">{{ $followup->remarks }}</p>
                                    </div>
                                    <div class="min-w-[180px] space-y-2 text-sm text-crm-mute">
                                        <div><span class="font-semibold text-crm-text">Date:</span> {{ $followup->followup_date->format('d M Y') }}</div>
                                        <div><span class="font-semibold text-crm-text">Result:</span> {{ $followup->followup_result ?: 'Not specified' }}</div>
                                        <div><span class="font-semibold text-crm-text">Next:</span> {{ optional($followup->next_followup_date)->format('d M Y') ?: 'Not scheduled' }}</div>
                                        <div><span class="font-semibold text-crm-text">By:</span> {{ $followup->creator->fullname ?? 'System' }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[28px] border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm text-crm-mute">
                                No follow-up activity has been logged yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-6" x-show="tab === 'notes'" x-cloak>
            <div class="grid gap-6 xl:grid-cols-12">
                <div class="space-y-6 xl:col-span-8">
                    <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                        <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Requirement Summary</h3>
                        <p class="mt-1 text-sm text-crm-mute">Core notes that define what this prospect needs from the business.</p>
                        <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50/80 p-5">
                            <div class="whitespace-pre-line text-sm leading-7 text-crm-text">{{ $lead->requirement_summary }}</div>
                        </div>
                    </div>

                    @if ($lead->lost_reason)
                        <div class="rounded-[32px] border border-amber-200 bg-amber-50 p-6 shadow-crm-soft">
                            <h3 class="text-xl font-semibold tracking-tight text-amber-900">Lost Reason</h3>
                            <div class="mt-4 whitespace-pre-line text-sm leading-7 text-amber-900">{{ $lead->lost_reason }}</div>
                        </div>
                    @endif
                </div>

                <div class="space-y-6 xl:col-span-4">
                    <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                        <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Lead Health</h3>
                        <p class="mt-1 text-sm text-crm-mute">A compact view of the current operating position.</p>
                        <div class="mt-6 grid gap-4">
                            @foreach ([
                                'Current Status' => $lead->status->name ?? 'Unknown',
                                'Priority' => ucfirst($lead->priority),
                                'Temperature' => ucfirst($lead->temperature),
                                'Probability' => (int) $lead->probability_percent . '%',
                                'Lead Score' => (int) $lead->lead_score . '/100',
                                'Expected Deal Value' => $lead->expected_deal_value ? number_format((float) $lead->expected_deal_value, 2) : '0.00',
                            ] as $label => $value)
                                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">{{ $label }}</div>
                                    <div class="mt-2 text-sm font-medium text-crm-text">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-12" x-show="tab === 'attachments'" x-cloak>
            <div class="space-y-6 xl:col-span-4">
                <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                    <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Upload Attachments</h3>
                    <p class="mt-1 text-sm text-crm-mute">Attach proposals, requirement files, screenshots, spreadsheets, or signed documents.</p>

                    @if ($canUploadAttachment)
                        <form action="{{ route('crm.leads.attachments.store', $lead) }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Select Files</label>
                                <input type="file" name="attachments[]" multiple
                                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                                    class="mt-2 block w-full rounded-2xl border border-dashed border-crm-line bg-crm-soft px-4 py-5 text-sm text-crm-text file:mr-4 file:rounded-xl file:border-0 file:bg-crm-blue file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-crm-deep focus:border-crm-blue focus:ring-crm-blue">
                                <p class="mt-2 text-xs text-crm-mute">Supported: images, PDF, DOC, DOCX, XLS, XLSX. Max 10 MB each.</p>
                            </div>

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                                Upload Files
                            </button>
                        </form>
                    @else
                        <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-crm-mute">
                            You can review stored documents here, but attachment uploads are limited by your CRM permissions.
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6 xl:col-span-8">
                <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Lead Attachments</h3>
                            <p class="mt-1 text-sm text-crm-mute">Documents and files linked to this lead, organized in one premium workspace.</p>
                        </div>
                        <div class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                            {{ $lead->attachments->count() }} files
                        </div>
                    </div>

                    @if ($lead->attachments->isEmpty())
                        <div class="mt-6 rounded-[28px] border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
                                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16V4m0 12 4-4m-4 4-4-4M5 20h14" />
                                </svg>
                            </div>
                            <p class="mt-4 text-base font-semibold text-crm-ink">No attachments uploaded yet.</p>
                            <p class="mt-2 text-sm text-crm-mute">Upload lead documents to keep proposals, evidence, and shared files in one place.</p>
                        </div>
                    @else
                        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($lead->attachments as $attachment)
                                @php
                                    $extension = strtolower($attachment->file_extension);
                                    $typeStyle = $fileTypeStyles[$extension] ?? $fileTypeStyles['default'];
                                @endphp
                                <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-crm-soft">
                                    <div class="relative">
                                        @if ($attachment->is_image)
                                            <img src="{{ $attachment->public_url }}" alt="{{ $attachment->file_original_name }}"
                                                class="h-44 w-full object-cover">
                                        @else
                                            <div class="flex h-44 items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
                                                <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold uppercase ring-1 {{ $typeStyle }}">
                                                    {{ $attachment->file_extension ?: 'File' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-crm-ink">{{ $attachment->file_original_name }}</p>
                                                <p class="mt-1 text-xs text-crm-mute">{{ $attachment->formatted_file_size }} • {{ strtoupper($attachment->file_extension ?: 'file') }}</p>
                                            </div>
                                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold uppercase ring-1 {{ $typeStyle }}">
                                                {{ $attachment->is_image ? 'Image' : ($attachment->is_previewable ? 'Preview' : 'Document') }}
                                            </span>
                                        </div>

                                        <div class="mt-4 space-y-1 text-sm text-crm-mute">
                                            <div>Uploaded by: {{ $attachment->uploader->fullname ?? 'System' }}</div>
                                            <div>Uploaded on: {{ $attachment->created_at->format('d M Y h:i A') }}</div>
                                        </div>

                                        <div class="mt-5 flex flex-wrap gap-2">
                                            @if ($attachment->is_previewable)
                                                <a href="{{ route('crm.leads.attachments.preview', [$lead, $attachment]) }}" target="_blank"
                                                    class="inline-flex items-center justify-center rounded-xl border border-crm-line bg-white px-3 py-2 text-xs font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                                                    View
                                                </a>
                                            @endif
                                            <a href="{{ route('crm.leads.attachments.download', [$lead, $attachment]) }}"
                                                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-crm-text transition hover:bg-slate-200">
                                                Download
                                            </a>
                                            @if ($canUploadAttachment)
                                                <form action="{{ route('crm.leads.attachments.destroy', [$lead, $attachment]) }}" method="POST"
                                                    onsubmit="return confirm('Delete this attachment?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-12" x-show="tab === 'quotations'" x-cloak>
            <div class="space-y-6 xl:col-span-4">
                <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                    <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Quotation Workspace</h3>
                    <p class="mt-1 text-sm text-crm-mute">Create polished commercial proposals linked directly to this lead and keep the CRM proposal history complete.</p>

                    @if ($canCreateQuotation)
                        <a href="{{ route('crm.leads.quotations.create', $lead) }}"
                            class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                            Create New Quotation
                        </a>
                    @endif

                    <div class="mt-6 grid gap-4">
                        @foreach ([
                            'Total Quotations' => $lead->quotations->count(),
                            'Accepted Quotations' => $lead->quotations->where('status', 'accepted')->count(),
                            'Latest Quotation' => $lead->quotations->first()?->quotation_no ?? 'None yet',
                        ] as $label => $value)
                            <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">{{ $label }}</div>
                                <div class="mt-2 text-sm font-medium text-crm-text">{{ $value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-8">
                <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Lead Quotations</h3>
                            <p class="mt-1 text-sm text-crm-mute">Commercial proposals prepared for this lead, ready for review, PDF export, and client delivery.</p>
                        </div>
                        <div class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                            {{ $lead->quotations->count() }} quotations
                        </div>
                    </div>

                    @if ($lead->quotations->isEmpty())
                        <div class="mt-6 rounded-[28px] border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
                                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 4h7l5 5v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                                </svg>
                            </div>
                            <p class="mt-4 text-base font-semibold text-crm-ink">No quotations created yet.</p>
                            <p class="mt-2 text-sm text-crm-mute">Create the first proposal to attach commercial scope and pricing to this lead.</p>
                        </div>
                    @else
                        <div class="mt-6 space-y-4">
                            @foreach ($lead->quotations as $quotation)
                                @php
                                    $quotationStatusClasses = [
                                        'draft' => 'bg-slate-100 text-slate-700 ring-slate-200',
                                        'sent' => 'bg-blue-50 text-blue-700 ring-blue-200',
                                        'accepted' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                        'expired' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                    ];
                                @endphp
                                <div class="rounded-[28px] border {{ $quotation->status === 'accepted' ? 'border-emerald-200 bg-emerald-50/50' : 'border-slate-200 bg-white' }} p-5 shadow-crm-soft">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-semibold text-crm-ink">{{ $quotation->quotation_no }}</span>
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $quotationStatusClasses[$quotation->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                                    {{ $quotation->statusLabel() }}
                                                </span>
                                                @if ($quotation->status === 'accepted')
                                                    <span class="inline-flex rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">
                                                        Accepted Proposal
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mt-3 grid gap-3 text-sm text-crm-mute sm:grid-cols-2">
                                                <div>Quotation Date: {{ optional($quotation->quotation_date)->format('d M Y') }}</div>
                                                <div>Valid Until: {{ optional($quotation->valid_until)->format('d M Y') ?: 'Open ended' }}</div>
                                                <div>Total: {{ number_format((float) $quotation->total, 2) }}</div>
                                                <div>Prepared By: {{ $quotation->creator->fullname ?? 'System' }}</div>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('crm.quotations.show', $quotation) }}"
                                                class="inline-flex items-center justify-center rounded-xl border border-crm-line bg-white px-3 py-2 text-xs font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                                                View
                                            </a>
                                            <a href="{{ route('crm.quotations.pdf', $quotation) }}"
                                                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-crm-text transition hover:bg-slate-200">
                                                PDF
                                            </a>
                                            @if (!$quotation->isLocked() && $canCreateQuotation)
                                                <a href="{{ route('crm.quotations.edit', $quotation) }}"
                                                    class="inline-flex items-center justify-center rounded-xl border border-crm-line bg-white px-3 py-2 text-xs font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6" x-show="tab === 'activity'" x-cloak>
            <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold tracking-tight text-crm-ink">Activity Timeline</h3>
                        <p class="mt-1 text-sm text-crm-mute">A readable audit trail of everything important that happened on this lead.</p>
                    </div>
                    <div class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                        {{ $lead->activities->count() }} activities
                    </div>
                </div>

                <div class="mt-8 space-y-5">
                    @forelse ($lead->activities as $activity)
                        @php
                            $style = $activityTypeStyles[$activity->activity_type] ?? ['badge' => 'bg-slate-100 text-slate-700 ring-slate-200', 'icon' => 'dot'];
                        @endphp
                        <div class="relative rounded-[28px] border border-slate-200 bg-white p-5 shadow-crm-soft">
                            <div class="absolute left-6 top-6 hidden h-[calc(100%-2rem)] w-px bg-slate-200 lg:block"></div>
                            <div class="relative flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex gap-4">
                                    <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $style['badge'] }}">
                                        @switch($style['icon'])
                                            @case('plus')
                                                <span class="text-lg font-semibold">+</span>
                                                @break
                                            @case('edit')
                                                <span class="text-sm font-semibold">✎</span>
                                                @break
                                            @case('user')
                                                <span class="text-sm font-semibold">U</span>
                                                @break
                                            @case('refresh')
                                                <span class="text-sm font-semibold">↻</span>
                                                @break
                                            @case('phone')
                                                <span class="text-sm font-semibold">☎</span>
                                                @break
                                            @case('upload')
                                                <span class="text-sm font-semibold">↑</span>
                                                @break
                                            @case('trash')
                                                <span class="text-sm font-semibold">×</span>
                                                @break
                                            @case('check')
                                                <span class="text-sm font-semibold">✓</span>
                                                @break
                                            @case('x')
                                                <span class="text-sm font-semibold">!</span>
                                                @break
                                            @case('spark')
                                                <span class="text-sm font-semibold">★</span>
                                                @break
                                            @default
                                                <span class="text-sm font-semibold">•</span>
                                        @endswitch
                                    </div>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $style['badge'] }}">
                                                {{ str($activity->activity_type)->replace('_', ' ')->title() }}
                                            </span>
                                            <span class="text-xs font-medium uppercase tracking-[0.18em] text-crm-mute">
                                                {{ $activity->created_at->format('d M Y h:i A') }}
                                            </span>
                                        </div>
                                        <p class="mt-3 text-sm font-medium leading-7 text-crm-text">{{ $activity->message }}</p>
                                        <p class="mt-2 text-sm text-crm-mute">By {{ $activity->creator->fullname ?? 'System' }}</p>

                                        @if ($activity->old_value || $activity->new_value)
                                            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                                                @if ($activity->old_value)
                                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                                        <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Previous</div>
                                                        <div class="mt-2 space-y-1 text-sm text-crm-text">
                                                            @foreach ($activity->old_value as $key => $value)
                                                                <div><span class="font-semibold">{{ str($key)->replace('_', ' ')->title() }}:</span> {{ $value ?: 'N/A' }}</div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($activity->new_value)
                                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                                        <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Updated</div>
                                                        <div class="mt-2 space-y-1 text-sm text-crm-text">
                                                            @foreach ($activity->new_value as $key => $value)
                                                                <div><span class="font-semibold">{{ str($key)->replace('_', ' ')->title() }}:</span> {{ $value ?: 'N/A' }}</div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[28px] border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center text-sm text-crm-mute">
                            No activity has been logged yet for this lead.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
