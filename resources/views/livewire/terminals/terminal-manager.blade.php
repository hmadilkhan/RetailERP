<div class="terminal-manager-page space-y-6">
    <style>
        .terminal-manager-page details > summary::-webkit-details-marker {
            display: none;
        }

        .terminal-manager-page .terminal-table-shell {
            position: relative;
            min-height: 240px;
        }

        .terminal-manager-page .terminal-loader {
            position: absolute;
            inset: 0;
            z-index: 20;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(248, 250, 252, 0.78);
            backdrop-filter: blur(2px);
        }

        .terminal-manager-page .terminal-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid #d8e1ec;
            border-top-color: #2E7D32;
            border-radius: 999px;
            animation: terminal-spin 0.75s linear infinite;
        }

        @keyframes terminal-spin {
            to { transform: rotate(360deg); }
        }
    </style>

    @php
        $visibleCount = $terminalRows->count();
        $activeCount = $terminalRows->where('status_id', 1)->count();
        $lockedCount = $terminalRows->filter(fn ($terminal) => (int) ($terminal->is_locked ?? 0) === 1)->count();
    @endphp

    @if (session()->has('terminal_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
            {{ session('terminal_message') }}
        </div>
    @endif

    @if (session()->has('terminal_error'))
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800">
            {{ session('terminal_error') }}
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">Visible Terminals</div>
            <div class="mt-3 text-3xl font-black text-erp-ink">{{ $visibleCount }}</div>
            <div class="mt-1 text-sm font-medium text-erp-mute">Current filtered result</div>
        </div>
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">Active</div>
            <div class="mt-3 text-3xl font-black text-emerald-700">{{ $activeCount }}</div>
            <div class="mt-1 text-sm font-medium text-erp-mute">Available for operations</div>
        </div>
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">Locked</div>
            <div class="mt-3 text-3xl font-black text-amber-700">{{ $lockedCount }}</div>
            <div class="mt-1 text-sm font-medium text-erp-mute">Device lock status</div>
        </div>
    </div>

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Terminal Filters</h2>
        </div>
        <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-6">
            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Company</span>
                <select class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.live="filterCompanyId">
                    <option value="">All Companies</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch</span>
                <select class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp disabled:bg-slate-100 disabled:text-slate-400" wire:model.live="filterBranchId" {{ $filterCompanyId === '' ? 'disabled' : '' }}>
                    <option value="">{{ $filterCompanyId === '' ? 'Select company first' : 'All Branches' }}</option>
                    @foreach ($filterBranches as $branch)
                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Status</span>
                <select class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.live="statusId">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Lock</span>
                <select class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.live="lockStatus">
                    <option value="">All</option>
                    <option value="1">Locked</option>
                    <option value="0">Unlocked</option>
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Device</span>
                <select class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.live="deviceStatusFilter">
                    <option value="">All</option>
                    <option value="Online">Online</option>
                    <option value="Offline">Offline</option>
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Search</span>
                <input type="text" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.live.debounce.300ms="search" placeholder="Terminal, MAC, serial">
            </label>
        </div>
    </div>

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">{{ $terminalId ? 'Update Terminal' : 'Create New Terminal' }}</h2>
                <p class="mt-1 text-sm text-erp-mute">Assign terminal details to a company branch.</p>
            </div>
            @if ($terminalId)
                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark" wire:click="resetTerminalForm">
                    Cancel Edit
                </button>
            @endif
        </div>

        <form wire:submit.prevent="saveTerminal" class="p-5">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <label class="block xl:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Company</span>
                    <select class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.live="formCompanyId">
                        <option value="">Select Company</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('formCompanyId') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block xl:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch</span>
                    <select class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp disabled:bg-slate-100 disabled:text-slate-400" wire:model.live="formBranchId" {{ $formCompanyId === '' ? 'disabled' : '' }}>
                        <option value="">Select Branch</option>
                        @foreach ($formBranches as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    @error('formBranchId') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block xl:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Terminal Name</span>
                    <input type="text" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.defer="terminalName">
                    @error('terminalName') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block xl:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">MAC Address</span>
                    <input type="text" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.defer="macAddress">
                </label>

                <label class="block xl:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Serial Number</span>
                    <input type="text" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.defer="serialNo">
                </label>

                <label class="block xl:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Model No</span>
                    <input type="text" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" wire:model.defer="modelNo">
                </label>
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-erp px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-erp-dark disabled:opacity-60" wire:loading.attr="disabled" wire:target="saveTerminal">
                    <span wire:loading.remove wire:target="saveTerminal">{{ $terminalId ? 'Update Terminal' : 'Add Terminal' }}</span>
                    <span wire:loading wire:target="saveTerminal">Saving...</span>
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Terminal Detail</h2>
                <p class="mt-1 text-sm text-erp-mute">Manage permissions, printing, bind status and remote device locks.</p>
            </div>
            <button type="button" class="inline-flex items-center justify-center rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark disabled:opacity-60" wire:click="checkVisibleDeviceStatuses" wire:loading.attr="disabled" wire:target="checkVisibleDeviceStatuses">
                <span wire:loading.remove wire:target="checkVisibleDeviceStatuses">Check Device Statuses</span>
                <span wire:loading wire:target="checkVisibleDeviceStatuses">Checking...</span>
            </button>
        </div>

        <div class="terminal-table-shell">
            <div class="terminal-loader" wire:loading.flex wire:target="filterCompanyId,filterBranchId,statusId,lockStatus,deviceStatusFilter,search,lockTerminal,unlockTerminal,checkDeviceStatus,revealLockPassword,inactiveTerminal,reactiveTerminal,editTerminal,saveTerminal">
                <div class="inline-flex items-center gap-3 rounded-lg border border-erp-line bg-white px-4 py-3 text-sm font-bold text-erp-ink shadow-panel">
                    <span class="terminal-spinner"></span>
                    <span>Loading terminals...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Company</th>
                            <th class="px-4 py-3">Branch</th>
                            <th class="px-4 py-3">Terminal</th>
                            <th class="px-4 py-3">MAC</th>
                            <th class="px-4 py-3">Serial</th>
                            <th class="px-4 py-3">Model</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Device</th>
                            <th class="px-4 py-3">Password</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line bg-white">
                        @forelse ($terminalRows as $terminal)
                            <tr wire:key="terminal-row-{{ $terminal->terminal_id }}" class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-4 font-bold text-erp-ink">#{{ $terminal->terminal_id }}</td>
                                <td class="min-w-44 px-4 py-4 font-semibold text-erp-text">{{ $terminal->company_name }}</td>
                                <td class="min-w-40 px-4 py-4 text-erp-mute">{{ $terminal->branch_name }}</td>
                                <td class="min-w-44 px-4 py-4 font-bold text-erp-ink">{{ $terminal->terminal_name }}</td>
                                <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-erp-mute">{{ $terminal->mac_address ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-erp-mute">{{ $terminal->serial_no ?: '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-erp-mute">{{ $terminal->model_no ?: '-' }}</td>
                                <td class="px-4 py-4">
                                    @if ((int) $terminal->status_id === 1)
                                        <span class="inline-flex min-w-20 justify-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">Active</span>
                                    @else
                                        <span class="inline-flex min-w-20 justify-center rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php($deviceStatus = $deviceStatuses[$terminal->terminal_id] ?? null)
                                    @if ($deviceStatus)
                                        <span class="inline-flex min-w-24 justify-center rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $deviceStatus['label'] === 'Online' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : ($deviceStatus['label'] === 'Offline' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-amber-50 text-amber-700 ring-amber-200') }}">
                                            {{ $deviceStatus['label'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex min-w-24 justify-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200" wire:loading.remove wire:target="checkDeviceStatus({{ $terminal->terminal_id }}),checkVisibleDeviceStatuses">Not Checked</span>
                                        <span class="inline-flex min-w-24 justify-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200" wire:loading wire:target="checkDeviceStatus({{ $terminal->terminal_id }}),checkVisibleDeviceStatuses">Checking...</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    @if (!empty($terminal->lock_password))
                                        <span class="inline-flex rounded-lg {{ isset($revealedPasswords[$terminal->terminal_id]) ? 'bg-amber-50 text-amber-800 ring-amber-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }} px-3 py-1 text-xs font-black tracking-[0.18em] ring-1">
                                            {{ $revealedPasswords[$terminal->terminal_id] ?? '********' }}
                                        </span>
                                        <button type="button" class="ml-2 text-xs font-bold text-erp-dark hover:text-erp" wire:click="revealLockPassword({{ $terminal->terminal_id }})">
                                            View
                                        </button>
                                    @else
                                        <span class="text-erp-mute">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right align-top">
                                    <details class="group inline-block text-left">
                                        <summary class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark">
                                            Actions
                                        </summary>
                                        <div class="mt-2 w-56 overflow-hidden rounded-lg border border-erp-line bg-white py-2 text-sm shadow-menu">
                                            <button type="button" class="block w-full px-4 py-2 text-left font-semibold text-erp-text hover:bg-slate-50" wire:click="editTerminal({{ $terminal->terminal_id }})">Edit</button>

                                            @if ((int) $terminal->status_id === 1)
                                                <button type="button" class="block w-full px-4 py-2 text-left font-semibold text-rose-700 hover:bg-rose-50" wire:click="inactiveTerminal({{ $terminal->terminal_id }})" onclick="return confirm('Inactive this terminal?')">Inactive</button>
                                            @else
                                                <button type="button" class="block w-full px-4 py-2 text-left font-semibold text-emerald-700 hover:bg-emerald-50" wire:click="reactiveTerminal({{ $terminal->terminal_id }})">Reactive</button>
                                            @endif

                                            <a href="{{ url('/permission') }}/{{ $this->encrypted($terminal->terminal_id) }}" class="block px-4 py-2 font-semibold text-erp-text hover:bg-slate-50">Permission</a>
                                            <a href="{{ url('/printing-details') }}/{{ $this->encrypted($terminal->terminal_id) }}" class="block px-4 py-2 font-semibold text-erp-text hover:bg-slate-50">Print Details</a>
                                            <a href="{{ url('/bind-terminals') }}/{{ $this->encrypted($terminal->terminal_id) }}/{{ $this->encrypted($terminal->branch_id) }}" class="block px-4 py-2 font-semibold text-erp-text hover:bg-slate-50">Bind Terminal</a>

                                            @if ((int) ($terminal->is_locked ?? 0) === 0)
                                                <button type="button" class="block w-full px-4 py-2 text-left font-semibold text-amber-700 hover:bg-amber-50" wire:click="lockTerminal({{ $terminal->terminal_id }})">Lock Device</button>
                                            @else
                                                <button type="button" class="block w-full px-4 py-2 text-left font-semibold text-emerald-700 hover:bg-emerald-50" wire:click="unlockTerminal({{ $terminal->terminal_id }})">Unlock Device</button>
                                            @endif

                                            <button type="button" class="block w-full px-4 py-2 text-left font-semibold text-erp-text hover:bg-slate-50" wire:click="checkDeviceStatus({{ $terminal->terminal_id }})">Device Status</button>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <div class="mx-auto max-w-sm">
                                        <div class="text-base font-bold text-erp-ink">No terminals found</div>
                                        <div class="mt-1 text-sm text-erp-mute">Change filters or add a new terminal above.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border-t border-erp-line px-5 py-4">
            {{ $terminals->links('pagination::tailwind') }}
        </div>
    </div>

    <div id="terminalLockResultModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-sm rounded-lg bg-white shadow-menu">
            <div id="lockResultModalHeader" class="flex items-center justify-between rounded-t-lg px-5 py-4">
                <h3 id="terminalLockResultModalLabel" class="text-base font-bold">Result</h3>
                <button type="button" class="rounded-lg px-2 py-1 text-xl leading-none hover:bg-white/20" onclick="window.closeTerminalLockResult()">x</button>
            </div>
            <div class="px-5 py-6 text-center">
                <div id="lockResultIcon" class="mx-auto flex h-12 w-12 items-center justify-center rounded-full text-sm font-black"></div>
                <p id="lockResultMessage" class="mt-4 text-sm font-bold text-erp-ink"></p>
                <div id="lockResultPasswordWrap" class="mt-4 hidden">
                    <div class="text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">Lock Password</div>
                    <div id="lockResultPassword" class="mt-2 inline-flex rounded-lg bg-amber-50 px-4 py-2 text-sm font-black tracking-[0.24em] text-amber-800 ring-1 ring-amber-200"></div>
                </div>
            </div>
            <div class="border-t border-erp-line px-5 py-4 text-right">
                <button type="button" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark" onclick="window.closeTerminalLockResult()">Close</button>
            </div>
        </div>
    </div>

    @script
    <script>
        window.closeTerminalLockResult = function () {
            const modal = document.getElementById('terminalLockResultModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        };

        $wire.on('show-lock-result', ({ action, success, message, password }) => {
            const modal = document.getElementById('terminalLockResultModal');
            const icon = document.getElementById('lockResultIcon');
            const header = document.getElementById('lockResultModalHeader');
            const label = document.getElementById('terminalLockResultModalLabel');
            const messageEl = document.getElementById('lockResultMessage');
            const pwWrap = document.getElementById('lockResultPasswordWrap');
            const pw = document.getElementById('lockResultPassword');

            icon.textContent = success ? 'OK' : 'ERR';
            icon.className = 'mx-auto flex h-12 w-12 items-center justify-center rounded-full text-sm font-black ' + (success ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-200');
            header.className = 'flex items-center justify-between rounded-t-lg px-5 py-4 ' + (success ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white');
            label.textContent = action === 'lock' ? 'Lock Device' : 'Unlock Device';
            messageEl.textContent = message || '';

            if (success && action === 'lock' && password) {
                pw.textContent = password;
                pwWrap.classList.remove('hidden');
            } else {
                pwWrap.classList.add('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    </script>
    @endscript
</div>
