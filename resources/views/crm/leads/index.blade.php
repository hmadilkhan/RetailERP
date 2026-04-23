@extends('crm.layouts.app')

@section('title', 'Leads')
@section('page_title', 'Leads Pipeline')
@section('page_subtitle', 'Track inquiries, control ownership, and move every sales conversation through a cleaner premium CRM workspace.')

@php
    $priorityClasses = [
        'low' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'medium' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
        'high' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        'urgent' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
    ];
@endphp

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-crm.stat-card label="Visible Leads" :value="number_format($stats['total'])" helper="Only the pipeline you are allowed to access is counted here." tone="blue" />
        <x-crm.stat-card label="Open Pipeline" :value="number_format($stats['open'])" helper="Leads still active and moving through the sales cycle." tone="indigo" />
        <x-crm.stat-card label="Won Deals" :value="number_format($stats['won'])" helper="Commercial wins visible inside your CRM scope." tone="emerald" />
        <x-crm.stat-card label="Upcoming Follow-ups" :value="number_format($stats['followups'])" helper="Scheduled touchpoints that still need action." tone="amber" />
    </section>

    <x-crm.panel class="mt-6 backdrop-blur" title="Lead Command Center"
        subtitle="Search, filter, assign, and export the pipeline with role-aware controls.">
        <x-slot:actions>
            <div class="flex flex-wrap gap-3">
                <span class="inline-flex rounded-full bg-crm-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-crm-blue ring-1 ring-crm-line">
                    {{ $crmRoleLabel }}
                </span>

                @if ($canExportLeads)
                    <details class="relative">
                        <summary
                            class="inline-flex cursor-pointer list-none items-center justify-center gap-2 rounded-2xl border border-crm-line bg-white px-5 py-3 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                            Export Leads
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.514a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </summary>

                        <div class="absolute right-0 z-20 mt-3 w-52 overflow-hidden rounded-2xl border border-crm-line bg-white shadow-crm-soft">
                            <a href="{{ route('crm.leads.export.excel', request()->query()) }}"
                                class="block px-4 py-3 text-sm font-medium text-crm-text transition hover:bg-crm-soft">
                                Export Excel
                            </a>
                            <a href="{{ route('crm.leads.export.pdf', request()->query()) }}"
                                class="block px-4 py-3 text-sm font-medium text-crm-text transition hover:bg-crm-soft">
                                Export PDF
                            </a>
                        </div>
                    </details>
                @endif

                @if ($canCreateLead)
                    <a href="{{ route('crm.leads.create') }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-900/20 transition hover:-translate-y-0.5 hover:bg-crm-deep">
                        Add Lead
                    </a>
                @endif
            </div>
        </x-slot:actions>

        @if ($activeFilterSummary)
            <div class="mb-5 flex flex-wrap items-center gap-2 rounded-[24px] border border-crm-line bg-crm-soft/70 px-4 py-4 text-sm text-crm-text">
                <span class="text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Active Filters</span>
                <span class="inline-flex rounded-full bg-white px-3 py-1 font-medium text-crm-text ring-1 ring-slate-200">
                    {{ $activeFilterSummary }}
                </span>
            </div>
        @endif

        <form method="GET" action="{{ route('crm.leads.index') }}" data-crm-submit class="grid gap-4 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                    placeholder="Lead code, name, company, phone, email"
                    class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm text-crm-ink placeholder:text-slate-400 focus:border-crm-blue focus:ring-crm-blue">
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Source</label>
                <select name="lead_source_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All</option>
                    @foreach ($leadSources as $source)
                        <option value="{{ $source->id }}" @selected(($filters['lead_source_id'] ?? '') == $source->id)>{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Product Type</label>
                <select name="product_type_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ $type->id }}" @selected(($filters['product_type_id'] ?? '') == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Product</label>
                <select name="product_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All</option>
                    @foreach ($allProducts as $product)
                        <option value="{{ $product->id }}" @selected(($filters['product_id'] ?? '') == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Status</label>
                <select name="status_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All</option>
                    @foreach ($leadStatuses as $status)
                        <option value="{{ $status->id }}" @selected(($filters['status_id'] ?? '') == $status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">City</label>
                <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="Filter by city"
                    class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Assigned User</label>
                <select name="assigned_to" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(($filters['assigned_to'] ?? '') == $user->id)>{{ $user->fullname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-6">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Quick Filters</label>
                <div class="flex flex-wrap gap-3 rounded-[24px] border border-crm-line bg-crm-soft px-4 py-4">
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-crm-text">
                        <input type="checkbox" name="my_leads" value="1"
                            class="h-4 w-4 rounded border-slate-300 text-crm-blue focus:ring-crm-blue" @checked(!empty($filters['my_leads']))>
                        My Leads
                    </label>
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-crm-text">
                        <input type="checkbox" name="unassigned_only" value="1"
                            class="h-4 w-4 rounded border-slate-300 text-crm-blue focus:ring-crm-blue" @checked(!empty($filters['unassigned_only']))>
                        Unassigned Only
                    </label>
                    @if (!$canAssignLeads)
                        <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-crm-mute ring-1 ring-slate-200">
                            Your view is limited to assigned and self-created leads.
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-end gap-3 lg:col-span-12 lg:justify-end">
                <button type="submit" data-loading-label="Applying..."
                    class="inline-flex items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                    Apply Filters
                </button>
                <a href="{{ route('crm.leads.index', ['reset_filters' => 1]) }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-crm-line bg-white px-5 py-3 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                    Reset
                </a>
            </div>
        </form>
    </x-crm.panel>

    <x-crm.panel class="mt-6 backdrop-blur" title="Leads List"
        :subtitle="$leads->total() . ' record' . ($leads->total() === 1 ? '' : 's') . ' found'">
        <x-slot:actions>
            @if ($canBulkAssign)
                <p class="text-sm text-crm-mute">Select multiple leads to reassign them in one action.</p>
            @endif
        </x-slot:actions>

        @if ($canBulkAssign)
            <form id="crm-bulk-assign-form" action="{{ route('crm.leads.bulk-assign') }}" method="POST" data-crm-submit
                class="mb-5 rounded-[28px] border border-crm-line bg-crm-soft/70 p-4">
                @csrf
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-crm-ink">Bulk Assignment</p>
                        <p class="mt-1 text-sm text-crm-mute">Choose leads from the table, then assign them to a sales owner.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-[minmax(220px,1fr)_auto]">
                        <div>
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Assign Selected Leads To</label>
                            <select name="assigned_to" class="block w-full rounded-2xl border-crm-line bg-white px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                                <option value="">Unassigned</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" data-loading-label="Assigning..."
                            class="inline-flex items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                            Bulk Assign
                        </button>
                    </div>
                </div>
            </form>
        @endif

        <div class="overflow-hidden rounded-[28px] border border-crm-line">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/90">
                        <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">
                            @if ($canBulkAssign)
                                <th class="px-5 py-4">
                                    <input type="checkbox" id="crm-select-all"
                                        class="h-4 w-4 rounded border-slate-300 text-crm-blue focus:ring-crm-blue">
                                </th>
                            @endif
                            <th class="px-5 py-4">Lead</th>
                            <th class="px-5 py-4">Contact</th>
                            <th class="px-5 py-4">Source</th>
                            <th class="px-5 py-4">Product</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Priority</th>
                            <th class="px-5 py-4">Assigned</th>
                            <th class="px-5 py-4">Follow-up Summary</th>
                            <th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($leads as $lead)
                            <tr class="transition hover:bg-slate-50/70">
                                @if ($canBulkAssign)
                                    <td class="px-5 py-4 align-top">
                                        <input type="checkbox" value="{{ $lead->id }}"
                                            class="crm-lead-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-crm-blue focus:ring-crm-blue">
                                    </td>
                                @endif
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-crm-ink">{{ $lead->contact_person_name }}</div>
                                    <div class="mt-1 text-xs font-medium uppercase tracking-[0.18em] text-crm-blue">{{ $lead->lead_code }}</div>
                                    <div class="mt-1 text-sm text-crm-mute">{{ $lead->company_name ?: 'No company name' }}</div>
                                </td>
                                <td class="px-5 py-4 text-crm-text">
                                    <div>{{ $lead->contact_number }}</div>
                                    <div class="mt-1 text-sm text-crm-mute">{{ $lead->email ?: 'No email' }}</div>
                                    <div class="mt-1 text-sm text-crm-mute">{{ $lead->city ?: 'City not set' }}</div>
                                </td>
                                <td class="px-5 py-4 text-crm-text">{{ $lead->leadSource->name ?? 'N/A' }}</td>
                                <td class="px-5 py-4">
                                    <div class="text-crm-text">{{ $lead->productType->name ?? 'N/A' }}</div>
                                    <div class="mt-1 text-sm text-crm-mute">{{ $lead->product->name ?? 'No product selected' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <x-crm.status-badge :label="$lead->status->name ?? 'Unknown'" :color="$lead->status->color ?? '#114a8f'" />
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $priorityClasses[$lead->priority] ?? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' }}">
                                        {{ ucfirst($lead->priority) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @can('assign', $lead)
                                        <form action="{{ route('crm.leads.assign', $lead) }}" method="POST" data-crm-submit class="space-y-2">
                                            @csrf
                                            <select name="assigned_to" class="block w-full rounded-xl border-crm-line bg-white px-3 py-2 text-sm focus:border-crm-blue focus:ring-crm-blue">
                                                <option value="">Unassigned</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" @selected((int) $lead->assigned_to === (int) $user->id)>{{ $user->fullname }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" data-loading-label="Updating..."
                                                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-crm-text transition hover:bg-slate-200">
                                                Update Assignment
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-crm-text">{{ $lead->assignedUser->fullname ?? 'Unassigned' }}</div>
                                        <div class="mt-1 text-xs text-crm-mute">
                                            @if ((int) $lead->created_by === (int) auth()->id())
                                                Created by you
                                            @elseif ((int) $lead->assigned_to === (int) auth()->id())
                                                Assigned to you
                                            @else
                                                Shared visibility
                                            @endif
                                        </div>
                                    @endcan
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-2">
                                        <div class="text-sm font-medium text-crm-text">
                                            Last contact:
                                            <span class="text-crm-mute">{{ optional($lead->last_contact_date)->format('d M Y') ?: 'Not logged' }}</span>
                                        </div>
                                        <div class="text-sm font-medium text-crm-text">
                                            Next follow-up:
                                            <span class="{{ $lead->isOverdue() ? 'text-rose-700' : 'text-crm-mute' }}">
                                                {{ optional($lead->next_followup_date)->format('d M Y') ?: 'Not scheduled' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if ($lead->isOverdue())
                                                <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-200">
                                                    Overdue
                                                </span>
                                            @endif
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                                {{ $lead->followups_count }} follow-up{{ $lead->followups_count === 1 ? '' : 's' }}
                                            </span>
                                            @if ($lead->latestFollowup)
                                                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">
                                                    Latest: {{ $lead->latestFollowup->followup_type }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('crm.leads.show', $lead) }}"
                                            class="rounded-xl border border-crm-line bg-white px-3 py-2 text-xs font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                                            View
                                        </a>

                                        @can('update', $lead)
                                            <a href="{{ route('crm.leads.edit', $lead) }}"
                                                class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-crm-text transition hover:bg-slate-200">
                                                Edit
                                            </a>
                                        @endcan

                                        @can('delete', $lead)
                                            <form action="{{ route('crm.leads.destroy', $lead) }}" method="POST" data-crm-submit onsubmit="return confirm('Delete this lead?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" data-loading-label="Deleting..."
                                                    class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canBulkAssign ? 10 : 9 }}" class="px-6 py-20 text-center">
                                    <div class="mx-auto max-w-md">
                                        <x-crm.empty-state title="No leads matched the current filters."
                                            description="Try widening the search, removing limits, or resetting the filter set.">
                                            <x-slot:icon>
                                                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                                                </svg>
                                            </x-slot:icon>
                                        </x-crm.empty-state>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($leads->hasPages())
            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-crm-mute">Showing {{ $leads->firstItem() }}-{{ $leads->lastItem() }} of {{ $leads->total() }}</p>
                <div class="flex items-center gap-2">
                    @if ($leads->onFirstPage())
                        <span class="rounded-full border border-slate-200 bg-slate-100 px-4 py-2 text-sm text-slate-400">Previous</span>
                    @else
                        <a href="{{ $leads->previousPageUrl() }}"
                            class="rounded-full border border-crm-line bg-white px-4 py-2 text-sm font-medium text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                            Previous
                        </a>
                    @endif

                    @if ($leads->hasMorePages())
                        <a href="{{ $leads->nextPageUrl() }}"
                            class="rounded-full border border-crm-line bg-white px-4 py-2 text-sm font-medium text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                            Next
                        </a>
                    @else
                        <span class="rounded-full border border-slate-200 bg-slate-100 px-4 py-2 text-sm text-slate-400">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </x-crm.panel>
@endsection

@push('crm_scripts')
    <script>
        (function() {
            const selectAll = document.getElementById('crm-select-all');
            const checkboxes = document.querySelectorAll('.crm-lead-checkbox');
            const bulkForm = document.getElementById('crm-bulk-assign-form');

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                });
            }

            if (bulkForm) {
                bulkForm.addEventListener('submit', function(event) {
                    const selectedLeadIds = Array.from(checkboxes)
                        .filter(function(checkbox) {
                            return checkbox.checked;
                        })
                        .map(function(checkbox) {
                            return checkbox.value;
                        });

                    bulkForm.querySelectorAll('input[name="lead_ids[]"]').forEach(function(input) {
                        input.remove();
                    });

                    if (selectedLeadIds.length === 0) {
                        event.preventDefault();
                        alert('Select at least one lead first.');
                        return;
                    }

                    selectedLeadIds.forEach(function(leadId) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'lead_ids[]';
                        hiddenInput.value = leadId;
                        bulkForm.appendChild(hiddenInput);
                    });
                });
            }
        })();
    </script>
@endpush
