@extends('crm.layouts.app')

@section('title', 'CRM Dashboard')
@section('page_title', 'Executive CRM Dashboard')
@section('page_subtitle', 'Premium lead performance intelligence across acquisition, ownership, conversion, and pipeline value.')

@section('content')
    <x-crm.panel class="backdrop-blur" title="Performance Filters"
        subtitle="Refine the dashboard by period, funnel dimensions, product interest, owner, and lead status.">
        @if ($activeFilterSummary)
            <div class="mb-5 flex flex-wrap items-center gap-2 rounded-[24px] border border-crm-line bg-crm-soft/70 px-4 py-4 text-sm text-crm-text">
                <span class="text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Active Filters</span>
                <span class="inline-flex rounded-full bg-white px-3 py-1 font-medium text-crm-text ring-1 ring-slate-200">
                    {{ $activeFilterSummary }}
                </span>
            </div>
        @endif

        <form method="GET" action="{{ route('crm.dashboard') }}" data-crm-submit class="grid gap-4 lg:grid-cols-12">
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Date From</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                    class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Date To</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                    class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Source</label>
                <select name="lead_source_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All Sources</option>
                    @foreach ($leadSources as $source)
                        <option value="{{ $source->id }}" @selected(($filters['lead_source_id'] ?? '') == $source->id)>{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Product Type</label>
                <select name="product_type_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All Types</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ $type->id }}" @selected(($filters['product_type_id'] ?? '') == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Product</label>
                <select name="product_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All Products</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(($filters['product_id'] ?? '') == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Assigned User</label>
                <select name="assigned_to" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All Owners</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(($filters['assigned_to'] ?? '') == $user->id)>{{ $user->fullname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute">Lead Status</label>
                <select name="status_id" class="block w-full rounded-2xl border-crm-line bg-crm-soft px-4 py-3 text-sm focus:border-crm-blue focus:ring-crm-blue">
                    <option value="">All Statuses</option>
                    @foreach ($leadStatuses as $status)
                        <option value="{{ $status->id }}" @selected(($filters['status_id'] ?? '') == $status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-3 lg:col-span-9 lg:justify-end">
                <button type="submit" data-loading-label="Applying..."
                    class="inline-flex items-center justify-center rounded-2xl bg-crm-blue px-5 py-3 text-sm font-semibold text-white transition hover:bg-crm-deep">
                    Apply Filters
                </button>
                <a href="{{ route('crm.dashboard', ['reset_filters' => 1]) }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-crm-line bg-white px-5 py-3 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                    Reset
                </a>
            </div>
        </form>
    </x-crm.panel>

    <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($summaryCards as $card)
            <x-crm.stat-card :label="$card['label']" :value="number_format($card['value'])" :helper="$card['helper']" :tone="$card['tone']" />
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-8 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Reminder Queue</h2>
                    <p class="mt-1 text-sm text-crm-mute">A focused follow-up view for due-today and overdue conversations that need immediate sales attention.</p>
                </div>
                <a href="{{ route('crm.board') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-crm-line bg-white px-4 py-2.5 text-sm font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                    Open Pipeline
                </a>
            </div>

            <div class="mt-6 grid gap-5 lg:grid-cols-2">
                <div class="rounded-[28px] border border-blue-200 bg-blue-50/70 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-blue-900">Due Today</div>
                            <div class="mt-1 text-xs uppercase tracking-[0.22em] text-blue-700">{{ $reminders['dueToday']->count() }} leads</div>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">Today</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($reminders['dueToday'] as $lead)
                            <a href="{{ route('crm.leads.show', $lead) }}"
                                class="block rounded-3xl border border-white/80 bg-white/90 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-crm-ink">{{ $lead->contact_person_name }}</div>
                                        <div class="mt-1 text-xs uppercase tracking-[0.18em] text-crm-mute">{{ $lead->lead_code }}{{ $lead->company_name ? ' / ' . $lead->company_name : '' }}</div>
                                    </div>
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">{{ optional($lead->leadSource)->name ?? 'Direct' }}</span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-3 text-xs text-crm-mute">
                                    <span>Owner: {{ $lead->assignedUser->fullname ?? 'Unassigned' }}</span>
                                    <span>Status: {{ $lead->status->name ?? 'Unknown' }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-3xl border border-dashed border-blue-200 bg-white/70 px-5 py-10 text-center text-sm text-blue-800">
                                No follow-ups are due today in your current CRM scope.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[28px] border border-rose-200 bg-rose-50/70 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-rose-900">Overdue Follow-ups</div>
                            <div class="mt-1 text-xs uppercase tracking-[0.22em] text-rose-700">{{ $reminders['overdue']->count() }} leads</div>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-200">Attention</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($reminders['overdue'] as $lead)
                            <a href="{{ route('crm.leads.show', $lead) }}"
                                class="block rounded-3xl border border-white/80 bg-white/90 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-crm-ink">{{ $lead->contact_person_name }}</div>
                                        <div class="mt-1 text-xs uppercase tracking-[0.18em] text-crm-mute">{{ $lead->lead_code }}{{ $lead->company_name ? ' / ' . $lead->company_name : '' }}</div>
                                    </div>
                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                                        {{ optional($lead->next_followup_date)->format('d M') }}
                                    </span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-3 text-xs text-crm-mute">
                                    <span>Owner: {{ $lead->assignedUser->fullname ?? 'Unassigned' }}</span>
                                    <span>Status: {{ $lead->status->name ?? 'Unknown' }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-3xl border border-dashed border-rose-200 bg-white/70 px-5 py-10 text-center text-sm text-rose-800">
                                No overdue follow-up reminders right now. The active funnel looks healthy.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-4 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Recent Alerts</h2>
                    <p class="mt-1 text-sm text-crm-mute">Unread and recent CRM notification activity for fast executive review.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700 ring-1 ring-slate-200">
                    Live
                </span>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($reminders['recentNotifications'] as $notification)
                    <a href="{{ route('crm.notifications.open', $notification->id) }}"
                        class="block rounded-[24px] border border-slate-200 bg-slate-50/70 p-4 transition hover:border-crm-blue hover:bg-white">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-crm-ink">{{ data_get($notification->data, 'title', 'CRM Alert') }}</div>
                                <div class="mt-2 text-sm leading-6 text-crm-mute">{{ data_get($notification->data, 'message') }}</div>
                            </div>
                            @if (is_null($notification->read_at))
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-crm-blue"></span>
                            @endif
                        </div>
                        <div class="mt-3 text-xs uppercase tracking-[0.18em] text-crm-mute">
                            {{ data_get($notification->data, 'lead_code', 'Lead') }} / {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </a>
                @empty
                    <div class="rounded-[28px] border border-dashed border-slate-300 bg-slate-50 px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-crm-ink">No recent CRM alerts.</p>
                        <p class="mt-2 text-sm text-crm-mute">Assignments, reminders, and status movement will appear here automatically.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mt-6 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Report Exports</h2>
                <p class="mt-1 text-sm text-crm-mute">Download filtered CRM reports in Excel or PDF without losing the active dashboard scope.</p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                'source-wise' => 'Source-wise Report',
                'status-wise' => 'Status-wise Report',
                'sales-performance' => 'Sales Person Performance',
                'monthly-trend' => 'Monthly Leads Report',
            ] as $reportKey => $reportLabel)
                <div class="rounded-[28px] border border-crm-line bg-crm-soft/70 p-4">
                    <div class="text-sm font-semibold text-crm-ink">{{ $reportLabel }}</div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('crm.dashboard.reports.export.excel', array_merge(['report' => $reportKey], request()->query())) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-crm-blue px-4 py-2 text-xs font-semibold text-white transition hover:bg-crm-deep">
                            Excel
                        </a>
                        <a href="{{ route('crm.dashboard.reports.export.pdf', array_merge(['report' => $reportKey], request()->query())) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-crm-line bg-white px-4 py-2 text-xs font-semibold text-crm-text transition hover:border-crm-blue hover:text-crm-blue">
                            PDF
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-8 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Monthly Lead Trend</h2>
                    <p class="mt-1 text-sm text-crm-mute">Lead flow trend over time for the currently selected CRM scope.</p>
                </div>
                <div class="rounded-full bg-crm-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-crm-blue ring-1 ring-crm-line">
                    Trend
                </div>
            </div>
            <div class="mt-6 h-[320px]">
                <canvas id="crm-monthly-leads-chart"></canvas>
            </div>
        </div>

        <div class="xl:col-span-4 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Conversion Snapshot</h2>
                    <p class="mt-1 text-sm text-crm-mute">A quick read on converted, won, and still-open lead volume.</p>
                </div>
                <div class="rounded-full bg-crm-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-crm-blue ring-1 ring-crm-line">
                    Conversion
                </div>
            </div>
            <div class="mt-6 h-[320px]">
                <canvas id="crm-conversion-chart"></canvas>
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Conversion Rate</div>
                    <div class="mt-2 text-2xl font-semibold text-crm-ink">{{ number_format($reports['conversionRate']['conversion_rate'], 1) }}%</div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">Win Rate</div>
                    <div class="mt-2 text-2xl font-semibold text-crm-ink">{{ number_format($reports['conversionRate']['win_rate'], 1) }}%</div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-5 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Source Distribution</h2>
                    <p class="mt-1 text-sm text-crm-mute">Which acquisition channels are contributing the most leads.</p>
                </div>
                <div class="rounded-full bg-crm-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-crm-blue ring-1 ring-crm-line">
                    Channels
                </div>
            </div>
            <div class="mt-6 h-[320px]">
                <canvas id="crm-source-chart"></canvas>
            </div>
        </div>

        <div class="xl:col-span-7 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Sales Person Performance</h2>
                    <p class="mt-1 text-sm text-crm-mute">Compare lead ownership and won volume across assigned sales owners.</p>
                </div>
                <div class="rounded-full bg-crm-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-crm-blue ring-1 ring-crm-line">
                    Team
                </div>
            </div>
            <div class="mt-6 h-[320px]">
                <canvas id="crm-sales-performance-chart"></canvas>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-4 rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Revenue Pipeline</h2>
            <p class="mt-1 text-sm text-crm-mute">Commercial outlook from expected deal values across the active funnel.</p>
            <div class="mt-6 grid gap-4">
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-emerald-800">Open Pipeline</div>
                    <div class="mt-2 text-3xl font-semibold text-emerald-900">{{ number_format($reports['expectedRevenue']['open_pipeline'], 2) }}</div>
                </div>
                <div class="rounded-3xl border border-blue-200 bg-blue-50 p-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-800">Weighted Pipeline</div>
                    <div class="mt-2 text-3xl font-semibold text-blue-900">{{ number_format($reports['expectedRevenue']['weighted_pipeline'], 2) }}</div>
                </div>
                <div class="rounded-3xl border border-violet-200 bg-violet-50 p-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-violet-800">Won Revenue</div>
                    <div class="mt-2 text-3xl font-semibold text-violet-900">{{ number_format($reports['expectedRevenue']['won_revenue'], 2) }}</div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-600">Won vs Lost Ratio</div>
                    <div class="mt-2 text-3xl font-semibold text-crm-ink">{{ number_format($reports['wonLostRatio']['ratio'], 2) }}</div>
                    <div class="mt-2 text-sm text-crm-mute">{{ $reports['wonLostRatio']['won'] }} won / {{ $reports['wonLostRatio']['lost'] }} lost</div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-8 space-y-6">
            <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Status Summary</h2>
                        <p class="mt-1 text-sm text-crm-mute">Funnel health across the current filter set, with expected value by stage.</p>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-[24px] border border-crm-line">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50/90">
                                <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">
                                    <th class="px-5 py-4">Status</th>
                                    <th class="px-5 py-4">Lead Count</th>
                                    <th class="px-5 py-4">Expected Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($reports['statusWise'] as $row)
                                    <tr>
                                        <td class="px-5 py-4">
                                            <x-crm.status-badge :label="$row->name" :color="$row->color ?? '#114a8f'" />
                                        </td>
                                        <td class="px-5 py-4 font-medium text-crm-ink">{{ number_format($row->total) }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format((float) $row->expected_value, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-10 text-center text-sm text-crm-mute">No status data found for the selected filters.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Sales Person Performance Table</h2>
                        <p class="mt-1 text-sm text-crm-mute">Ownership quality, conversion movement, and pipeline size per sales owner.</p>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-[24px] border border-crm-line">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50/90">
                                <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">
                                    <th class="px-5 py-4">Sales Person</th>
                                    <th class="px-5 py-4">Assigned Leads</th>
                                    <th class="px-5 py-4">Won Leads</th>
                                    <th class="px-5 py-4">Converted</th>
                                    <th class="px-5 py-4">Conversion Rate</th>
                                    <th class="px-5 py-4">Pipeline Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($reports['salesPerformance'] as $row)
                                    <tr>
                                        <td class="px-5 py-4 font-semibold text-crm-ink">{{ $row->sales_person }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format($row->total_leads) }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format($row->won_leads) }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format($row->converted_leads) }}</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format($row->conversion_rate, 1) }}%</td>
                                        <td class="px-5 py-4 text-crm-text">{{ number_format((float) $row->pipeline_value, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-sm text-crm-mute">No sales performance data found for the selected filters.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Source-wise Lead Count</h2>
            <p class="mt-1 text-sm text-crm-mute">Lead acquisition volume by marketing or inbound channel.</p>
            <div class="mt-6 overflow-hidden rounded-[24px] border border-crm-line">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/90">
                            <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">
                                <th class="px-5 py-4">Source</th>
                                <th class="px-5 py-4">Lead Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($reports['sourceWise'] as $row)
                                <tr>
                                    <td class="px-5 py-4 font-medium text-crm-ink">{{ $row->label }}</td>
                                    <td class="px-5 py-4 text-crm-text">{{ number_format($row->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-10 text-center text-sm text-crm-mute">No source data found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm">
            <h2 class="text-xl font-semibold tracking-tight text-crm-ink">Product-wise Lead Count</h2>
            <p class="mt-1 text-sm text-crm-mute">Lead demand split by product and product type.</p>
            <div class="mt-6 overflow-hidden rounded-[24px] border border-crm-line">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/90">
                            <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.22em] text-crm-mute">
                                <th class="px-5 py-4">Product</th>
                                <th class="px-5 py-4">Type</th>
                                <th class="px-5 py-4">Lead Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($reports['productWise'] as $row)
                                <tr>
                                    <td class="px-5 py-4 font-medium text-crm-ink">{{ $row->product_name }}</td>
                                    <td class="px-5 py-4 text-crm-text">{{ $row->product_type_name }}</td>
                                    <td class="px-5 py-4 text-crm-text">{{ number_format($row->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center text-sm text-crm-mute">No product data found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('crm_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const chartTextColor = '#475569';
            const chartGridColor = 'rgba(148, 163, 184, 0.18)';

            const monthlyConfig = @json($charts['monthlyLeads']);
            const sourceConfig = @json($charts['sourceDistribution']);
            const conversionConfig = @json($charts['conversion']);
            const salesPerformanceConfig = @json($charts['salesPerformance']);

            const defaultScale = {
                ticks: {
                    color: chartTextColor,
                    font: {
                        size: 11
                    }
                },
                grid: {
                    color: chartGridColor
                }
            };

            new Chart(document.getElementById('crm-monthly-leads-chart'), {
                type: 'line',
                data: monthlyConfig,
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: defaultScale,
                        y: {
                            ...defaultScale,
                            beginAtZero: true,
                            precision: 0
                        }
                    }
                }
            });

            new Chart(document.getElementById('crm-source-chart'), {
                type: 'doughnut',
                data: sourceConfig,
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: chartTextColor,
                                boxWidth: 12,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('crm-conversion-chart'), {
                type: 'doughnut',
                data: conversionConfig,
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: chartTextColor,
                                boxWidth: 12,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('crm-sales-performance-chart'), {
                type: 'bar',
                data: salesPerformanceConfig,
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: chartTextColor
                            }
                        }
                    },
                    responsive: true,
                    scales: {
                        x: defaultScale,
                        y: {
                            ...defaultScale,
                            beginAtZero: true,
                            precision: 0
                        }
                    }
                }
            });
        })();
    </script>
@endpush
