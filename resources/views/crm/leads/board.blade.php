@extends('crm.layouts.app')

@section('title', 'Pipeline Board')
@section('page_title', 'Sales Pipeline Board')
@section('page_subtitle', 'A premium kanban workspace for moving leads through the sales pipeline with clarity, speed, and polished CRM context.')

@php
    $priorityClasses = [
        'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'medium' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'high' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'urgent' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
@endphp

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[28px] border border-white/60 bg-white/90 p-5 shadow-crm-soft backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Visible Leads</p>
            <p class="mt-4 text-3xl font-semibold text-crm-ink">{{ number_format($summary['total']) }}</p>
            <p class="mt-2 text-sm text-crm-mute">Leads currently visible inside your secured pipeline view.</p>
        </div>
        <div class="rounded-[28px] border border-white/60 bg-white/90 p-5 shadow-crm-soft backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Active Pipeline</p>
            <p class="mt-4 text-3xl font-semibold text-crm-ink">{{ number_format($summary['active']) }}</p>
            <p class="mt-2 text-sm text-crm-mute">Leads still moving between active CRM sales stages.</p>
        </div>
        <div class="rounded-[28px] border border-white/60 bg-white/90 p-5 shadow-crm-soft backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Overdue Follow-ups</p>
            <p class="mt-4 text-3xl font-semibold text-rose-700">{{ number_format($summary['overdue']) }}</p>
            <p class="mt-2 text-sm text-crm-mute">Cards that need immediate action from the sales team.</p>
        </div>
        <div class="rounded-[28px] border border-white/60 bg-white/90 p-5 shadow-crm-soft backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Won Leads</p>
            <p class="mt-4 text-3xl font-semibold text-emerald-700">{{ number_format($summary['won']) }}</p>
            <p class="mt-2 text-sm text-crm-mute">Completed wins already secured in the pipeline.</p>
        </div>
    </section>

    <section class="mt-6 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm backdrop-blur">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Board Filters</h2>
                <p class="mt-1 text-sm text-crm-mute">Slice the pipeline by ownership, source, product focus, and lead intake period.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('crm.board') }}" class="mt-6 grid gap-4 lg:grid-cols-12">
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Assigned User</label>
                <select name="assigned_to" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All Owners</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(($filters['assigned_to'] ?? '') == $user->id)>{{ $user->fullname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Source</label>
                <select name="lead_source_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All Sources</option>
                    @foreach ($leadSources as $source)
                        <option value="{{ $source->id }}" @selected(($filters['lead_source_id'] ?? '') == $source->id)>{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Product Type</label>
                <select name="product_type_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All Product Types</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ $type->id }}" @selected(($filters['product_type_id'] ?? '') == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Date From</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                    class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Date To</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                    class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
            </div>
            <div class="flex items-end gap-3 lg:col-span-9 lg:justify-end">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                    Apply Filters
                </button>
                <a href="{{ route('crm.board') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-crm-line bg-white px-5 py-3 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="mt-6">
        <div id="crm-board-status" class="mb-4 hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>

        <div class="overflow-x-auto pb-4">
            <div class="grid min-w-[1440px] grid-cols-8 gap-4">
                @foreach ($columns as $column)
                    @php
                        $status = $column['status'];
                        $borderColor = $status->color ?: '#114a8f';
                        $softColor = $borderColor . '15';
                    @endphp
                    <div class="rounded-[30px] border border-white/70 bg-white/85 p-4 shadow-crm backdrop-blur">
                        <div class="rounded-[24px] border px-4 py-4" style="border-color: {{ $borderColor }}33; background-color: {{ $softColor }};">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em]" style="color: {{ $borderColor }};">{{ $status->name }}</div>
                                    <div class="mt-1 text-sm text-crm-mute">{{ $column['count'] }} lead{{ $column['count'] === 1 ? '' : 's' }}</div>
                                </div>
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl text-sm font-semibold"
                                    style="background-color: {{ $borderColor }}22; color: {{ $borderColor }};">
                                    {{ $column['count'] }}
                                </span>
                            </div>
                        </div>

                        <div class="crm-dropzone mt-4 space-y-4 rounded-[24px] bg-slate-50/70 p-2"
                            data-status-id="{{ $status->id }}" data-status-name="{{ $status->name }}">
                            @forelse ($column['leads'] as $lead)
                                @can('changeStatus', $lead)
                                    <article class="crm-board-card cursor-move rounded-[24px] border border-slate-200 bg-white p-4 shadow-crm-soft transition hover:-translate-y-0.5 hover:shadow-crm"
                                        draggable="true" data-lead-id="{{ $lead->id }}">
                                @else
                                    <article class="crm-board-card rounded-[24px] border border-slate-200 bg-white p-4 shadow-crm-soft">
                                @endcan
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="text-[11px] font-semibold uppercase tracking-[0.20em] text-crm-blue">{{ $lead->lead_code }}</div>
                                                <h3 class="mt-2 text-sm font-semibold leading-6 text-crm-ink">{{ $lead->contact_person_name }}</h3>
                                                <p class="text-sm text-crm-mute">{{ $lead->company_name ?: 'No company name' }}</p>
                                            </div>
                                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold ring-1 {{ $priorityClasses[$lead->priority] ?? 'bg-blue-50 text-blue-700 ring-blue-200' }}">
                                                {{ ucfirst($lead->priority) }}
                                            </span>
                                        </div>

                                        <div class="mt-4 grid gap-3">
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-3 py-3">
                                                <div class="text-[10px] font-semibold uppercase tracking-[0.18em] text-crm-mute">Source</div>
                                                <div class="mt-1 text-sm font-medium text-crm-text">{{ $lead->leadSource->name ?? 'N/A' }}</div>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-3 py-3">
                                                <div class="text-[10px] font-semibold uppercase tracking-[0.18em] text-crm-mute">Assigned To</div>
                                                <div class="mt-1 text-sm font-medium text-crm-text">{{ $lead->assignedUser->fullname ?? 'Unassigned' }}</div>
                                            </div>
                                            <div class="rounded-2xl border {{ $lead->isOverdue() ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-slate-50/80' }} px-3 py-3">
                                                <div class="text-[10px] font-semibold uppercase tracking-[0.18em] {{ $lead->isOverdue() ? 'text-rose-700' : 'text-crm-mute' }}">Next Follow-up</div>
                                                <div class="mt-1 text-sm font-medium {{ $lead->isOverdue() ? 'text-rose-800' : 'text-crm-text' }}">
                                                    {{ optional($lead->next_followup_date)->format('d M Y') ?: 'Not scheduled' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex items-center justify-between gap-3">
                                            <span class="text-xs text-crm-mute">
                                                {{ $lead->created_at?->format('d M Y') }}
                                            </span>
                                            <a href="{{ route('crm.leads.show', $lead) }}"
                                                class="inline-flex items-center justify-center rounded-xl border border-crm-line bg-white px-3 py-2 text-xs font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                                                Open
                                            </a>
                                        </div>
                                    </article>
                            @empty
                                <div class="rounded-[22px] border border-dashed border-slate-300 bg-white/60 px-4 py-10 text-center text-sm text-crm-mute">
                                    No leads in this stage.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('crm_scripts')
    <script>
        (function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const boardStatus = document.getElementById('crm-board-status');
            let draggedCard = null;
            let originalZone = null;

            const showStatus = function(message, type) {
                if (!boardStatus) {
                    return;
                }

                boardStatus.textContent = message;
                boardStatus.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800', 'border-rose-200', 'bg-rose-50', 'text-rose-800');

                if (type === 'error') {
                    boardStatus.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
                } else {
                    boardStatus.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-800');
                }
            };

            document.querySelectorAll('.crm-board-card[draggable="true"]').forEach(function(card) {
                card.addEventListener('dragstart', function() {
                    draggedCard = card;
                    originalZone = card.parentElement;
                    requestAnimationFrame(function() {
                        card.classList.add('opacity-60', 'ring-2', 'ring-crm-blue');
                    });
                });

                card.addEventListener('dragend', function() {
                    card.classList.remove('opacity-60', 'ring-2', 'ring-crm-blue');
                });
            });

            document.querySelectorAll('.crm-dropzone').forEach(function(zone) {
                zone.addEventListener('dragover', function(event) {
                    event.preventDefault();
                    zone.classList.add('ring-2', 'ring-crm-blue');
                });

                zone.addEventListener('dragleave', function() {
                    zone.classList.remove('ring-2', 'ring-crm-blue');
                });

                zone.addEventListener('drop', function(event) {
                    event.preventDefault();
                    zone.classList.remove('ring-2', 'ring-crm-blue');

                    if (!draggedCard || draggedCard.parentElement === zone) {
                        return;
                    }

                    const leadId = draggedCard.dataset.leadId;
                    const statusId = zone.dataset.statusId;
                    const statusName = zone.dataset.statusName;

                    zone.prepend(draggedCard);

                    fetch(`{{ url('/crm/leads') }}/${leadId}/board-status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            status_id: statusId
                        })
                    }).then(async function(response) {
                        const data = await response.json().catch(function() {
                            return {};
                        });

                        if (!response.ok) {
                            throw new Error(data.message || 'Status update failed.');
                        }

                        showStatus(`Lead moved to ${statusName}.`, 'success');
                    }).catch(function(error) {
                        if (originalZone) {
                            originalZone.prepend(draggedCard);
                        }
                        showStatus(error.message || 'Unable to update the lead status.', 'error');
                    });
                });
            });
        })();
    </script>
@endpush
