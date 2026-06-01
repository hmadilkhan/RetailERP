<div class="space-y-6">
    @if (session()->has('whatsapp_access_message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
            {{ session('whatsapp_access_message') }}
        </div>
    @endif

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Users</div>
            <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($stats['users']) }}</div>
            <p class="mt-2 text-sm text-erp-mute">Registered WhatsApp numbers</p>
        </div>
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Active Users</div>
            <div class="mt-4 text-3xl font-black text-emerald-700">{{ number_format($stats['active_users']) }}</div>
            <p class="mt-2 text-sm text-erp-mute">Currently enabled</p>
        </div>
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Access Rules</div>
            <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($stats['access_rules']) }}</div>
            <p class="mt-2 text-sm text-erp-mute">Company and branch rules</p>
        </div>
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch Scope</div>
            <div class="mt-4 text-3xl font-black text-amber-700">{{ number_format($stats['branch_scope']) }}</div>
            <p class="mt-2 text-sm text-erp-mute">Branch-specific report access</p>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-5">
        <div class="rounded-lg border border-erp-line bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">WhatsApp User</h2>
                <p class="mt-1 text-sm text-erp-mute">Create or update mobile numbers that can request reports.</p>
            </div>
            <form wire:submit.prevent="saveUser" class="grid gap-4 p-5">
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Display Name</span>
                    <input type="text" wire:model.defer="user_name" placeholder="Ali Raza"
                        class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('user_name') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Mobile Number</span>
                    <input type="text" wire:model.defer="mobile_number" placeholder="923001234567"
                        class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('mobile_number') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Status</span>
                    <select wire:model.defer="user_status" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('user_status') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <div class="flex flex-wrap justify-end gap-2 pt-2">
                    <button type="button" wire:click="resetUserForm" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</button>
                    <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">
                        {{ $editingUserId ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-erp-line bg-white shadow-sm xl:col-span-3">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Access Rule</h2>
                <p class="mt-1 text-sm text-erp-mute">Assign company-wide or branch-only report access.</p>
            </div>
            <form wire:submit.prevent="saveAccess" class="grid gap-5 p-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">WhatsApp User</span>
                    <select wire:model.defer="whatsapp_user_id" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select user</option>
                        @foreach ($userOptions as $optionUser)
                            <option value="{{ $optionUser->id }}">{{ $optionUser->mobile_number }}{{ $optionUser->name ? ' - ' . $optionUser->name : '' }}</option>
                        @endforeach
                    </select>
                    @error('whatsapp_user_id') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Company</span>
                    <select wire:model.live="company_id" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select company</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Access Level</span>
                    <select wire:model.live="access_level" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="company">Company</option>
                        <option value="branch">Branch</option>
                    </select>
                    @error('access_level') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Branch</span>
                    <select wire:model.defer="branch_id" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp disabled:bg-slate-100" {{ $access_level !== 'branch' ? 'disabled' : '' }}>
                        <option value="">Select branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-erp-ink">Status</span>
                    <select wire:model.defer="access_status" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('access_status') <span class="mt-1 block text-xs font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <div class="flex flex-wrap items-end justify-end gap-2 md:col-span-2">
                    <button type="button" wire:click="resetAccessForm" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</button>
                    <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">
                        {{ $editingAccessId ? 'Update Access' : 'Create Access' }}
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="grid gap-4 border-b border-erp-line px-5 py-4 lg:grid-cols-3">
            <label class="block">
                <span class="text-sm font-bold text-erp-ink">Search</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search mobile, name, company or branch"
                    class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </label>
            <label class="block">
                <span class="text-sm font-bold text-erp-ink">Filter Company</span>
                <select wire:model.live="companyFilter" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <option value="">All companies</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-bold text-erp-ink">Filter Scope</span>
                <select wire:model.live="scopeFilter" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <option value="">All scopes</option>
                    <option value="company">Company</option>
                    <option value="branch">Branch</option>
                </select>
            </label>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-5">
        <div class="rounded-lg border border-erp-line bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Registered Users</h2>
                <p class="mt-1 text-sm text-erp-mute">Keep the WhatsApp directory clean and active.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">User</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="min-w-36 px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-erp-ink">{{ $user->mobile_number }}</div>
                                    <div class="mt-1 text-xs text-erp-mute">{{ $user->name ?? 'No name added' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ (int) ($user->status ?? 1) === 1 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                        {{ (int) ($user->status ?? 1) === 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="min-w-36 whitespace-nowrap px-5 py-4 text-right">
                                    <button type="button" wire:click="editUser({{ $user->id }})" class="inline-flex h-9 items-center rounded-lg border border-amber-200 bg-amber-50 px-3 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                    <button type="button" wire:click="toggleUserStatus({{ $user->id }})" class="ml-2 inline-flex h-9 items-center rounded-lg border border-erp-line px-3 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                        {{ (int) ($user->status ?? 1) === 1 ? 'Disable' : 'Enable' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No WhatsApp users found yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-erp-line px-5 py-4">
                {{ $users->links() }}
            </div>
        </div>

        <div class="rounded-lg border border-erp-line bg-white shadow-sm xl:col-span-3">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Access Rules</h2>
                <p class="mt-1 text-sm text-erp-mute">Each rule controls company or branch-level report visibility.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">User</th>
                            <th class="px-5 py-3 text-left font-bold">Scope</th>
                            <th class="px-5 py-3 text-left font-bold">Company / Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Status</th>
                            <th class="min-w-36 px-5 py-3 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($accesses as $access)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-erp-ink">{{ $access->mobile_number }}</div>
                                    <div class="mt-1 text-xs text-erp-mute">{{ $access->user_name ?: 'No name added' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $access->access_level === 'branch' ? 'bg-sky-50 text-sky-700 ring-sky-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                        {{ ucfirst($access->access_level) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-erp-ink">{{ $access->company_name }}</div>
                                    <div class="mt-1 text-xs text-erp-mute">{{ $access->branch_name ?: 'All branches in company' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ (int) ($access->status ?? 1) === 1 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                        {{ (int) ($access->status ?? 1) === 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="min-w-36 whitespace-nowrap px-5 py-4 text-right">
                                    <button type="button" wire:click="editAccess({{ $access->id }})" class="inline-flex h-9 items-center rounded-lg border border-amber-200 bg-amber-50 px-3 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                    <button type="button" wire:click="toggleAccessStatus({{ $access->id }})" class="ml-2 inline-flex h-9 items-center rounded-lg border border-erp-line px-3 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                        {{ (int) ($access->status ?? 1) === 1 ? 'Disable' : 'Enable' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-erp-mute">No access rules found yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-erp-line px-5 py-4">
                {{ $accesses->links() }}
            </div>
        </div>
    </section>
</div>
