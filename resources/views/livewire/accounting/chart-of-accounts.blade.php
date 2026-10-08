<div class="space-y-6">
    @php
        $labelClass = 'text-[11px] font-bold uppercase tracking-[0.16em] text-erp-mute';
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $typeColors = [
            'Asset' => 'bg-sky-50 text-sky-700',
            'Liability' => 'bg-amber-50 text-amber-700',
            'Equity' => 'bg-violet-50 text-violet-700',
            'Revenue' => 'bg-emerald-50 text-emerald-700',
            'Expense' => 'bg-rose-50 text-rose-700',
        ];
        $isSystem = $editing && $editing->system_key;
    @endphp

    @if (session()->has('coa_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.opacity>{{ session('coa_message') }}</div>
    @endif
    @if (session()->has('coa_error'))
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">{{ session('coa_error') }}</div>
    @endif

    <div class="grid gap-3 sm:grid-cols-5">
        @foreach ($types as $t)
            <div class="rounded-lg border border-erp-line bg-white px-4 py-3 shadow-sm">
                <div class="{{ $labelClass }}">{{ $t }}</div>
                <div class="mt-1 text-xl font-black text-erp-ink">{{ $counts[$t] ?? 0 }}</div>
            </div>
        @endforeach
    </div>

    @if ($showForm)
        <div class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">{{ $editingId ? 'Edit Account' : 'New Account' }}</h2>
                @if ($isSystem)
                    <p class="mt-0.5 text-sm text-amber-700">System account — used for automatic posting. Only code and name can be changed.</p>
                @endif
            </div>
            <form wire:submit="save" class="grid gap-4 p-5 md:grid-cols-3">
                <label class="block">
                    <span class="{{ $labelClass }}">Code</span>
                    <input type="text" wire:model="code" class="{{ $inputClass }}" placeholder="e.g. 6210">
                    @error('code') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block md:col-span-2">
                    <span class="{{ $labelClass }}">Name</span>
                    <input type="text" wire:model="name" class="{{ $inputClass }}" placeholder="e.g. Electricity">
                    @error('name') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Parent Group</span>
                    <select wire:model.live="parentId" class="{{ $inputClass }} disabled:bg-slate-50" @disabled($isSystem)>
                        <option value="">— Top level —</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->code }} · {{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('parentId') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Type</span>
                    <select wire:model="type" class="{{ $inputClass }} disabled:bg-slate-50" @disabled($isSystem || $parentId !== '')>
                        @foreach ($types as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    @if ($parentId !== '') <span class="mt-1 block text-xs text-erp-mute">Same as parent group.</span> @endif
                    @error('type') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>
                <div class="flex items-end gap-5 pb-2">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-erp-text">
                        <input type="checkbox" wire:model="isGroup" class="rounded border-erp-line text-erp focus:ring-erp" @disabled($isSystem)> Group (holds other accounts)
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-erp-text">
                        <input type="checkbox" wire:model="isActive" class="rounded border-erp-line text-erp focus:ring-erp" @disabled($isSystem)> Active
                    </label>
                </div>
                @error('isGroup') <span class="block text-xs font-semibold text-rose-600 md:col-span-3">{{ $message }}</span> @enderror
                <div class="flex gap-2 md:col-span-3">
                    <button type="submit" class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
                    <button type="button" wire:click="cancel" class="h-10 rounded-lg border border-erp-line bg-white px-5 text-sm font-bold text-erp-text transition hover:bg-slate-50">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-base font-bold text-erp-ink">Chart of Accounts</h2>
            <div class="flex gap-2">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search code or name"
                       class="h-10 w-56 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <button type="button" wire:click="create" class="h-10 rounded-lg border border-erp bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">+ Account</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-5 py-3 {{ $labelClass }}">Code</th>
                        <th class="px-5 py-3 {{ $labelClass }}">Account</th>
                        <th class="px-5 py-3 {{ $labelClass }}">Type</th>
                        <th class="px-5 py-3 {{ $labelClass }}">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-erp-line">
                    @forelse ($rows as $row)
                        @php $a = $row['account']; @endphp
                        <tr wire:key="coa-{{ $a->id }}" class="{{ $a->is_active ? '' : 'opacity-50' }}">
                            <td class="whitespace-nowrap px-5 py-2.5 font-mono text-xs font-bold text-erp-text">{{ $a->code }}</td>
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-2" style="padding-left: {{ $row['depth'] * 22 }}px">
                                    @if ($a->is_group)
                                        <svg class="h-4 w-4 shrink-0 text-erp-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                                    @endif
                                    <span class="{{ $a->is_group ? 'font-bold text-erp-ink' : 'text-erp-text' }}">{{ $a->name }}</span>
                                    @if ($a->system_key)
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">System</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-2.5"><span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $typeColors[$a->type] ?? '' }}">{{ $a->type }}</span></td>
                            <td class="px-5 py-2.5 text-xs font-semibold {{ $a->is_active ? 'text-emerald-700' : 'text-slate-500' }}">{{ $a->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="whitespace-nowrap px-5 py-2.5 text-right text-xs font-bold">
                                @if ($a->is_group)
                                    <button type="button" wire:click="create({{ $a->id }})" class="text-erp-dark hover:underline">+ Sub</button>
                                @endif
                                <button type="button" wire:click="edit({{ $a->id }})" class="ml-3 text-sky-700 hover:underline">Edit</button>
                                @unless ($a->system_key)
                                    <button type="button" wire:click="toggleActive({{ $a->id }})" class="ml-3 text-amber-700 hover:underline">{{ $a->is_active ? 'Deactivate' : 'Activate' }}</button>
                                    <button type="button" wire:click="delete({{ $a->id }})" wire:confirm="Delete account {{ $a->code }} · {{ $a->name }}?" class="ml-3 text-rose-600 hover:underline">Delete</button>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-erp-mute">No accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
