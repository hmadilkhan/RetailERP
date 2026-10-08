<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $statusColors = [
            'open' => 'bg-emerald-50 text-emerald-700',
            'closed' => 'bg-slate-100 text-slate-500',
            'locked' => 'bg-rose-50 text-rose-700',
        ];
    @endphp

    @if (session()->has('fy_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.opacity>{{ session('fy_message') }}</div>
    @endif

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Fiscal Years</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Each year has 12 monthly periods. Closing and locking periods comes with financial statements.</p>
            </div>
            <button type="button" wire:click="createNextYear" wire:confirm="Create the next fiscal year?"
                    class="h-10 rounded-lg border border-erp bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">+ Next Fiscal Year</button>
        </div>

        @if ($years->isEmpty())
            <div class="px-6 py-10 text-center text-sm font-medium text-erp-mute">No fiscal year yet.</div>
        @else
            <ul class="divide-y divide-erp-line">
                @foreach ($years as $year)
                    @php $isOpen = (int) $openYearId === (int) $year->id; @endphp
                    <li wire:key="fy-{{ $year->id }}">
                        <button type="button" wire:click="toggleYear({{ $year->id }})" class="flex w-full items-center gap-4 px-5 py-4 text-left transition hover:bg-slate-50">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-bold text-erp-ink">{{ $year->name }}</span>
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $statusColors[$year->status] ?? '' }}">{{ ucfirst($year->status) }}</span>
                                    @if ((int) $currentId === (int) $year->id)
                                        <span class="rounded-full bg-erp-dark px-2 py-0.5 text-[11px] font-bold text-white">Current</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-erp-mute">{{ $year->start_date->format('d M Y') }} – {{ $year->end_date->format('d M Y') }}</div>
                            </div>
                            <svg class="h-5 w-5 shrink-0 text-slate-400 transition {{ $isOpen ? 'rotate-180 text-erp-dark' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        @if ($isOpen)
                            <div class="grid gap-2 bg-slate-50/60 px-5 pb-5 sm:grid-cols-3 lg:grid-cols-4">
                                @foreach ($year->periods as $period)
                                    @php $isNow = $period->start_date->toDateString() <= $today && $period->end_date->toDateString() >= $today; @endphp
                                    <div class="rounded-lg border bg-white px-4 py-3 {{ $isNow ? 'border-erp ring-1 ring-erp' : 'border-erp-line' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="{{ $labelClass }}">Period {{ $period->month }}</span>
                                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusColors[$period->status] ?? '' }}">{{ ucfirst($period->status) }}</span>
                                        </div>
                                        <div class="mt-1 text-sm font-bold text-erp-ink">{{ $period->start_date->format('F Y') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
