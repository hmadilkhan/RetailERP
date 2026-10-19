<div class="space-y-6">
    @php
        $cellInput = 'h-9 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $btn = 'h-9 rounded-lg border border-erp-line bg-white px-3 text-sm font-bold text-erp-text transition hover:bg-slate-50';
        $btnPrimary = 'h-9 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:opacity-90';
        $statusColors = ['pending' => 'bg-amber-50 text-amber-700', 'approved' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-rose-50 text-rose-700', 'cancelled' => 'bg-slate-100 text-slate-500'];
        $money = fn ($v) => number_format((float) $v, 2);
        $tabs = ['pending' => 'Waiting for me (' . $pendingCount . ')', 'mine' => 'My requests', 'history' => 'History'] + ($isOwner ? ['rules' => 'Rules'] : []);
    @endphp

    @include('partials.branch-availability-alert')
    @if (session()->has('ap_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity>{{ session('ap_message') }}</div>
    @endif
    @error('approval')
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ $message }}</div>
    @enderror

    @if (!$enabled)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">Approvals are not switched on yet (database update pending).</div>
    @else
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Approvals</h2>
                <p class="mt-0.5 text-sm text-erp-mute">
                    When a rule matches, stock adjustments, branch transfers and vendor bills wait here instead of changing stock or balances.
                    Approving runs them; rejecting drops them. POS expenses are already paid at the till, so they only come here for review.
                    Admin and Sub-Admin are never held up and can approve everything.
                </p>
            </div>
            <div class="flex flex-wrap gap-1 border-b border-erp-line px-5 pt-3">
                @foreach ($tabs as $key => $label)
                    <button type="button" wire:click="$set('tab', '{{ $key }}')"
                            class="rounded-t-lg px-4 py-2 text-sm font-bold {{ $tab === $key ? 'border border-b-white border-erp-line bg-white text-erp-ink -mb-px' : 'text-erp-mute hover:text-erp-ink' }}">{{ $label }}</button>
                @endforeach
            </div>

            @if ($tab === 'rules')
                <div class="grid gap-3 border-b border-erp-line p-5 md:grid-cols-5">
                    <label class="block">
                        <span class="{{ $labelClass }}">What</span>
                        <select wire:model="ruleModule" class="mt-1 {{ $cellInput }}">
                            @foreach ($modules as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Branch</span>
                        <select wire:model="ruleBranch" class="mt-1 {{ $cellInput }}">
                            <option value="">All branches</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                        @error('ruleBranch') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">When value is at least (Rs)</span>
                        <input type="number" step="any" min="0" wire:model="ruleMinAmount" class="mt-1 {{ $cellInput }}">
                        @error('ruleMinAmount') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Approved by</span>
                        <select wire:model="ruleRole" class="mt-1 {{ $cellInput }}">
                            @foreach ($roles as $role)
                                <option value="{{ $role->role_id }}">{{ $role->role }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="flex items-end"><button type="button" wire:click="addRule" class="{{ $btnPrimary }}">Add Rule</button></div>
                    <p class="text-xs text-erp-mute md:col-span-5">
                        Value: stock adjustment = quantity × cost, transfer = stock cost + shipment, vendor bill = bill total, POS expense = amount.
                        People with the approver role (and Admin / Sub-Admin) are not held up by the rule. With no active rule, nothing changes.
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="px-5 py-3">What</th><th class="px-3 py-3">Branch</th><th class="px-3 py-3 text-right">From value</th><th class="px-3 py-3">Approved by</th><th class="px-3 py-3">Status</th><th class="px-5 py-3"></th>
                        </tr></thead>
                        <tbody>
                            @forelse ($rules as $rule)
                                <tr class="border-b border-erp-line" wire:key="rule-{{ $rule->id }}">
                                    <td class="px-5 py-3 font-semibold text-erp-ink">{{ $modules[$rule->module] ?? $rule->module }}</td>
                                    <td class="px-3 py-3">{{ $rule->branch_name ?? 'All branches' }}</td>
                                    <td class="px-3 py-3 text-right tabular-nums">{{ $money($rule->min_amount) }}</td>
                                    <td class="px-3 py-3">{{ $rule->role_name ?? 'Role #' . $rule->approver_role_id }}</td>
                                    <td class="px-3 py-3">{{ $rule->is_active ? 'Active' : 'Off' }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <button type="button" wire:click="toggleRule({{ $rule->id }})" class="text-xs font-bold text-erp hover:underline">{{ $rule->is_active ? 'Turn off' : 'Turn on' }}</button>
                                        <button type="button" wire:click="deleteRule({{ $rule->id }})" wire:confirm="Delete this rule?" class="ml-3 text-xs font-bold text-rose-700 hover:underline">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-erp-mute">No rules yet — nothing needs approval. Add one above.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b border-erp-line text-left {{ $labelClass }}">
                            <th class="px-5 py-3">Request</th><th class="px-3 py-3">Branch</th><th class="px-3 py-3 text-right">Value</th>
                            <th class="px-3 py-3">Requested</th><th class="px-3 py-3">Status</th><th class="px-5 py-3">{{ $tab === 'pending' ? 'Decision' : 'Details' }}</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="border-b border-erp-line align-top" wire:key="req-{{ $row->id }}">
                                    <td class="px-5 py-3">
                                        <span class="block text-xs font-bold uppercase tracking-wider text-erp-mute">{{ $modules[$row->module] ?? $row->module }}</span>
                                        <span class="font-semibold text-erp-ink">{{ $row->reference }}</span>
                                    </td>
                                    <td class="px-3 py-3">{{ $row->branch_name }}</td>
                                    <td class="px-3 py-3 text-right tabular-nums">{{ $money($row->amount) }}</td>
                                    <td class="px-3 py-3">{{ $row->requester ?? 'POS' }}<span class="block text-xs text-erp-mute">{{ \Illuminate\Support\Carbon::parse($row->created_at)->format('d M Y H:i') }}</span></td>
                                    <td class="px-3 py-3"><span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusColors[$row->status] ?? '' }}">{{ ucfirst($row->status) }}</span></td>
                                    <td class="px-5 py-3">
                                        @if ($tab === 'pending')
                                            <input type="text" wire:model="remarks.{{ $row->id }}" placeholder="Remarks (optional)" class="{{ $cellInput }} mb-2 w-56">
                                            <div class="flex gap-2">
                                                <button type="button" wire:click="approve({{ $row->id }})" wire:loading.attr="disabled" wire:confirm="Approve this request?" class="{{ $btnPrimary }}">Approve</button>
                                                <button type="button" wire:click="reject({{ $row->id }})" wire:loading.attr="disabled" wire:confirm="Reject this request?" class="{{ $btn }} text-rose-700">Reject</button>
                                            </div>
                                        @else
                                            @if ($row->decider)<span class="block text-xs">{{ ucfirst($row->status) }} by {{ $row->decider }} · {{ $row->decided_at ? \Illuminate\Support\Carbon::parse($row->decided_at)->format('d M Y H:i') : '' }}</span>@endif
                                            @if ($row->remarks)<span class="block text-xs text-erp-mute">“{{ $row->remarks }}”</span>@endif
                                            @if ($row->result)<span class="block text-xs text-erp-mute">{{ $row->result }}</span>@endif
                                            @if ($tab === 'mine' && $row->status === 'pending')
                                                <button type="button" wire:click="withdraw({{ $row->id }})" wire:confirm="Withdraw this request?" class="text-xs font-bold text-rose-700 hover:underline">Withdraw</button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-erp-mute">
                                    {{ $tab === 'pending' ? 'Nothing is waiting for your approval.' : 'No requests here.' }}
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($rows)<div class="px-5 py-3">{{ $rows->links('livewire::tailwind') }}</div>@endif
            @endif
        </div>
    @endif
</div>
