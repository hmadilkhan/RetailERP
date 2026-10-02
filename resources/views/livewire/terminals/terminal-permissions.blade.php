<div class="terminal-permissions-page space-y-6">
    <style>
        .terminal-permissions-page [x-cloak] { display: none !important; }
        .terminal-permissions-page .tp-scroll::-webkit-scrollbar { width: 6px; }
        .terminal-permissions-page .tp-scroll::-webkit-scrollbar-thumb { background: #d8e1ec; border-radius: 999px; }
        .terminal-permissions-page .tp-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #d8e1ec;
            border-top-color: #2E7D32;
            border-radius: 999px;
            animation: tp-spin 0.75s linear infinite;
        }
        @keyframes tp-spin { to { transform: rotate(360deg); } }
    </style>

    @php
        $selectClass = 'mt-2 h-11 w-full rounded-lg border-erp-line bg-white text-sm font-medium text-erp-ink shadow-sm focus:border-erp focus:ring-erp disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400';
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $selectedCompany = $companies->firstWhere('company_id', (int) $companyId);
        $selectedBranch = $branches->firstWhere('branch_id', (int) $branchId);
    @endphp

    @if (session()->has('permission_message'))
        <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity>
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('permission_message') }}
        </div>
    @endif

    {{-- Scope selectors --}}
    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Select Terminal</h2>
                <p class="mt-0.5 text-sm text-erp-mute">Narrow down by company and branch, then open a terminal to edit its POS permissions.</p>
            </div>
            <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
                <span class="rounded-full px-2.5 py-1 {{ $selectedCompany ? 'bg-erp-ink text-white' : 'bg-slate-100 text-slate-400' }}">{{ $selectedCompany->name ?? 'Company' }}</span>
                <svg class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="rounded-full px-2.5 py-1 {{ $selectedBranch ? 'bg-erp-ink text-white' : 'bg-slate-100 text-slate-400' }}">{{ $selectedBranch->branch_name ?? 'Branch' }}</span>
                <svg class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="rounded-full px-2.5 py-1 {{ $branchId !== '' ? 'bg-erp-dark text-white' : 'bg-slate-100 text-slate-400' }}">
                    {{ $terminalId !== '' ? ($branchTerminals->firstWhere('terminal_id', (int) $terminalId)->terminal_name ?? 'Terminal') : ($branchId !== '' ? $branchTerminals->count() . ' Terminals' : 'Terminals') }}
                </span>
            </div>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-3">
            <label class="block">
                <span class="{{ $labelClass }}">Company</span>
                <select class="{{ $selectClass }}" wire:model.live="companyId">
                    <option value="">Select company</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="{{ $labelClass }}">Branch</span>
                <select class="{{ $selectClass }}" wire:model.live="branchId" @disabled($companyId === '')>
                    <option value="">{{ $companyId === '' ? 'Select company first' : 'Select branch' }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="{{ $labelClass }}">Terminal</span>
                <select class="{{ $selectClass }}" wire:model.live="terminalId" @disabled($branchId === '')>
                    <option value="">{{ $branchId === '' ? 'Select branch first' : 'All terminals' }}</option>
                    @foreach ($branchTerminals as $terminal)
                        <option value="{{ $terminal->terminal_id }}">{{ $terminal->terminal_name }}{{ (int) $terminal->status_id !== 1 ? ' (Inactive)' : '' }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>

    {{-- Terminals --}}
    <div class="relative rounded-lg border border-erp-line bg-white shadow-sm">
        <div wire:loading.flex wire:target="companyId,branchId,terminalId,toggleTerminal"
             class="absolute inset-0 z-20 items-center justify-center rounded-lg bg-slate-50/70 backdrop-blur-[2px]">
            <div class="flex items-center gap-2 rounded-full border border-erp-line bg-white px-4 py-2 text-sm font-semibold text-erp-text shadow-sm">
                <span class="tp-spinner"></span> Loading
            </div>
        </div>

        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Terminals</h2>
            @if ($branchId !== '')
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-erp-text">{{ $terminals->count() }}</span>
            @endif
        </div>

        @if ($branchId === '')
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-50 to-slate-100 ring-1 ring-erp-line">
                    <svg class="h-7 w-7 text-erp-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="mt-4 text-sm font-bold text-erp-ink">{{ $companyId === '' ? 'Start with a company' : 'Now pick a branch' }}</h3>
                <p class="mt-1 max-w-sm text-sm text-erp-mute">Terminals for the selected branch will appear here.</p>
            </div>
        @elseif ($terminals->isEmpty())
            <div class="px-6 py-16 text-center text-sm font-medium text-erp-mute">No terminals found in this branch.</div>
        @else
            <ul class="divide-y divide-erp-line">
                @foreach ($terminals as $terminal)
                    @php
                        $isOpen = (int) $openTerminalId === (int) $terminal->terminal_id;
                        $summary = $summaries[$terminal->terminal_id] ?? null;
                        $enabled = $summary['enabled'] ?? 0;
                        $percent = $toggleTotal > 0 ? round($enabled / $toggleTotal * 100) : 0;
                        $isActive = (int) $terminal->status_id === 1;
                    @endphp
                    <li wire:key="terminal-{{ $terminal->terminal_id }}" class="{{ $isOpen ? 'bg-slate-50/60' : '' }}">
                        <button type="button" wire:click="toggleTerminal({{ $terminal->terminal_id }})"
                                class="group flex w-full items-center gap-4 px-5 py-4 text-left transition hover:bg-slate-50">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-sm font-black {{ $isOpen ? 'bg-erp-dark text-white shadow-md shadow-emerald-900/20' : 'bg-slate-100 text-erp-text ring-1 ring-erp-line' }}">
                                {{ strtoupper(\Illuminate\Support\Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $terminal->terminal_name), 0, 2)) ?: 'T' }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="truncate text-sm font-bold text-erp-ink">{{ $terminal->terminal_name }}</span>
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-bold {{ $isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $isActive ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if (!$summary)
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-700">Not configured</span>
                                    @endif
                                    @foreach (array_slice($summary['layouts'] ?? [], 0, 3) as $layout)
                                        <span class="rounded-full border border-erp-line bg-white px-2 py-0.5 text-[11px] font-semibold text-erp-text">{{ $layout }}</span>
                                    @endforeach
                                </div>
                                <div class="mt-1 truncate text-xs text-erp-mute">
                                    #{{ $terminal->terminal_id }}
                                    @if ($terminal->mac_address) · MAC {{ $terminal->mac_address }} @endif
                                    @if ($terminal->serial_no) · S/N {{ $terminal->serial_no }} @endif
                                    @if ($terminal->model_no) · {{ $terminal->model_no }} @endif
                                </div>
                            </div>

                            <div class="hidden w-44 shrink-0 sm:block">
                                <div class="flex items-baseline justify-between text-xs">
                                    <span class="font-semibold text-erp-mute">Enabled</span>
                                    <span class="font-bold text-erp-ink">{{ $enabled }}<span class="font-medium text-erp-mute">/{{ $toggleTotal }}</span></span>
                                </div>
                                <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-gradient-to-r from-erp to-erp-dark" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>

                            <svg class="h-5 w-5 shrink-0 text-slate-400 transition {{ $isOpen ? 'rotate-180 text-erp-dark' : 'group-hover:text-erp-text' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        @if ($isOpen)
                            @php
                                $sectionMeta = collect($sections)->map(fn ($section) => [
                                    'toggles' => array_keys(array_filter($section['fields'], fn ($field) => $field[1] === 'toggle')),
                                    'labels' => array_map(fn ($field) => strtolower($field[0]), array_values($section['fields'])),
                                ]);
                            @endphp
                            <div wire:key="panel-{{ $terminal->terminal_id }}" class="px-3 pb-5 sm:px-5"
                                 x-data="{
                                    tab: 'sales',
                                    q: '',
                                    meta: @js($sectionMeta),
                                    on(cols) { return cols.filter(c => $wire.perms[c]).length },
                                    hit(label) { return this.q.trim() === '' || label.includes(this.q.trim().toLowerCase()) },
                                    sectionVisible(key) { return this.q.trim() === '' ? this.tab === key : this.meta[key].labels.some(l => this.hit(l)) },
                                 }">
                                <div class="overflow-hidden rounded-xl border border-erp-line bg-white shadow-panel">
                                    {{-- Panel header --}}
                                    <div class="flex flex-col gap-4 border-b border-erp-line bg-gradient-to-r from-slate-50 via-white to-emerald-50/40 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                                        <div>
                                            <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-erp-dark">POS Permissions</div>
                                            <div class="mt-1 text-lg font-bold text-erp-ink">{{ $terminal->terminal_name }}</div>
                                            @unless ($hasRecord)
                                                <div class="mt-1 text-xs font-semibold text-amber-700">No permission record yet — saving will create one.</div>
                                            @endunless
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <div class="relative">
                                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                                                <input type="text" x-model="q" placeholder="Search permissions"
                                                       class="h-10 w-56 rounded-lg border-erp-line pl-9 text-sm shadow-sm focus:border-erp focus:ring-erp">
                                            </div>
                                            <span wire:dirty class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Unsaved changes
                                            </span>
                                            <button type="button" wire:click="openTerminal({{ $terminal->terminal_id }})"
                                                    class="h-10 rounded-lg border border-erp-line bg-white px-4 text-sm font-bold text-erp-text transition hover:border-slate-400">
                                                Reset
                                            </button>
                                            <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                                                    class="inline-flex h-10 items-center gap-2 rounded-lg bg-erp-dark px-5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 disabled:opacity-60">
                                                <span wire:loading wire:target="save" class="tp-spinner !border-white/40 !border-t-white"></span>
                                                Save Permissions
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid lg:grid-cols-[240px_1fr]">
                                        {{-- Section nav --}}
                                        <nav class="tp-scroll flex gap-1 overflow-x-auto border-b border-erp-line bg-slate-50/70 p-3 lg:block lg:max-h-[640px] lg:space-y-0.5 lg:overflow-y-auto lg:border-b-0 lg:border-r"
                                             :class="q.trim() !== '' && 'opacity-50 pointer-events-none'">
                                            @foreach ($sections as $key => $section)
                                                @php $toggleCount = count($sectionMeta[$key]['toggles']); @endphp
                                                <button type="button" @click="tab = '{{ $key }}'"
                                                        class="flex w-full shrink-0 items-center justify-between gap-3 whitespace-nowrap rounded-lg px-3 py-2 text-left text-sm font-semibold transition lg:whitespace-normal"
                                                        :class="tab === '{{ $key }}' ? 'bg-white text-erp-ink shadow-sm ring-1 ring-erp-line' : 'text-erp-mute hover:bg-white/70 hover:text-erp-text'">
                                                    <span>{{ $section['title'] }}</span>
                                                    @if ($toggleCount > 0)
                                                        <span class="rounded-md px-1.5 py-0.5 text-[11px] font-bold tabular-nums"
                                                              :class="on(meta['{{ $key }}'].toggles) > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-500'"
                                                              x-text="on(meta['{{ $key }}'].toggles) + '/{{ $toggleCount }}'"></span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </nav>

                                        {{-- Fields --}}
                                        <div class="tp-scroll min-w-0 p-5 lg:max-h-[640px] lg:overflow-y-auto">
                                            @foreach ($sections as $key => $section)
                                                @php $hasToggles = count($sectionMeta[$key]['toggles']) > 0; @endphp
                                                <section x-show="sectionVisible('{{ $key }}')" x-cloak class="mb-6 last:mb-0">
                                                    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                                                        <div>
                                                            <h3 class="text-sm font-bold text-erp-ink">{{ $section['title'] }}</h3>
                                                            <p class="mt-0.5 text-xs text-erp-mute">{{ $section['hint'] }}</p>
                                                        </div>
                                                        @if ($hasToggles)
                                                            <div class="flex items-center gap-1 text-xs font-bold" x-show="q.trim() === ''">
                                                                <button type="button" wire:click="setSection('{{ $key }}', true)" class="rounded-md px-2.5 py-1.5 text-erp-dark transition hover:bg-emerald-50">Enable all</button>
                                                                <span class="text-slate-300">|</span>
                                                                <button type="button" wire:click="setSection('{{ $key }}', false)" class="rounded-md px-2.5 py-1.5 text-erp-mute transition hover:bg-slate-100 hover:text-rose-700">Disable all</button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="grid gap-2.5 sm:grid-cols-2 2xl:grid-cols-3">
                                                        @foreach ($section['fields'] as $column => [$label, $type])
                                                            @if ($type === 'toggle')
                                                                <label wire:key="f-{{ $terminal->terminal_id }}-{{ $column }}" x-show="hit(@js(strtolower($label)))"
                                                                       class="flex cursor-pointer select-none items-center justify-between gap-3 rounded-lg border border-erp-line bg-white px-3.5 py-3 transition hover:border-slate-300 has-[:checked]:border-emerald-200 has-[:checked]:bg-emerald-50/50">
                                                                    <span class="text-sm font-semibold text-erp-text">{{ $label }}</span>
                                                                    <span class="relative inline-flex h-5 w-9 shrink-0">
                                                                        <input type="checkbox" class="peer sr-only" wire:model="perms.{{ $column }}">
                                                                        <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp-dark peer-focus-visible:ring-2 peer-focus-visible:ring-erp peer-focus-visible:ring-offset-2"></span>
                                                                        <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                                                                    </span>
                                                                </label>
                                                            @else
                                                                <label wire:key="f-{{ $terminal->terminal_id }}-{{ $column }}" x-show="hit(@js(strtolower($label)))"
                                                                       class="flex items-center justify-between gap-3 rounded-lg border border-dashed border-erp-line bg-slate-50/60 px-3.5 py-2">
                                                                    <span class="text-sm font-semibold text-erp-text">{{ $label }}</span>
                                                                    <input type="{{ $type === 'count' ? 'number' : 'text' }}" @if ($type === 'count') min="0" @endif
                                                                           wire:model="perms.{{ $column }}"
                                                                           class="h-9 {{ $type === 'count' ? 'w-24 text-right tabular-nums' : 'w-40' }} rounded-md border-erp-line bg-white text-sm font-semibold text-erp-ink shadow-sm focus:border-erp focus:ring-erp">
                                                                </label>
                                                                @error('perms.' . $column)
                                                                    <p class="-mt-1 text-xs font-semibold text-rose-700 sm:col-span-2 2xl:col-span-3">{{ $message }}</p>
                                                                @enderror
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </section>
                                            @endforeach

                                            <div x-show="q.trim() !== '' && !Object.keys(meta).some(k => sectionVisible(k))" x-cloak
                                                 class="py-12 text-center text-sm font-medium text-erp-mute">
                                                No permission matches "<span class="font-bold text-erp-ink" x-text="q"></span>".
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
