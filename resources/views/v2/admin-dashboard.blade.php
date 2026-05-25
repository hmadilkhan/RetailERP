@extends('layouts.master-tailwind')

@section('title', 'Admin Dashboard V2')
@section('page_title', 'Admin Dashboard')
@section('page_subtitle', 'A separate V2 workspace for admin visibility, access control, companies, branches, and system modules.')

@section('content')
    @php
        $tones = [
            'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
            'sky' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'rose' => 'bg-rose-50 text-rose-700 ring-rose-200',
            'slate' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ];
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            @foreach ($cards as $card)
                <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="rounded-lg px-2.5 py-1 text-xs font-bold uppercase tracking-[0.16em] ring-1 {{ $tones[$card['tone']] ?? $tones['slate'] }}">
                            {{ $card['label'] }}
                        </span>
                        <span class="h-2.5 w-2.5 rounded-full bg-erp"></span>
                    </div>
                    <div class="mt-5 text-3xl font-black tracking-tight text-erp-ink">{{ number_format($card['value']) }}</div>
                    <p class="mt-2 text-sm leading-6 text-erp-mute">{{ $card['caption'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-lg border border-erp-line bg-white shadow-sm xl:col-span-2">
                <div class="border-b border-erp-line px-5 py-4">
                    <h2 class="text-base font-bold text-erp-ink">Recent Users</h2>
                    <p class="mt-1 text-sm text-erp-mute">Latest staff accounts with role and branch context.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr>
                                <th class="px-5 py-3 text-left font-bold">User</th>
                                <th class="px-5 py-3 text-left font-bold">Role</th>
                                <th class="px-5 py-3 text-left font-bold">Branch</th>
                                <th class="px-5 py-3 text-left font-bold">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentUsers as $user)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-erp-ink">{{ data_get($user, 'fullname') ?: data_get($user, 'username', 'User') }}</div>
                                        <div class="mt-1 text-xs text-erp-mute">{{ data_get($user, 'email') ?: data_get($user, 'username', '-') }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-erp-text">{{ data_get($user, 'role', '-') }}</td>
                                    <td class="px-5 py-4 text-erp-text">{{ data_get($user, 'branch_name', '-') }}</td>
                                    <td class="px-5 py-4 text-erp-mute">{{ data_get($user, 'created_at') ? \Carbon\Carbon::parse(data_get($user, 'created_at'))->format('d M Y') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-sm text-erp-mute">No user data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h2 class="text-base font-bold text-erp-ink">Module Mix</h2>
                    <p class="mt-1 text-sm text-erp-mute">Pages grouped by sidebar mode.</p>
                </div>
                <div class="space-y-3 p-5">
                    @forelse ($moduleModes as $mode)
                        @php
                            $total = max(1, $moduleModes->max('total'));
                            $percent = round((data_get($mode, 'total', 0) / $total) * 100);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-bold text-erp-ink">{{ data_get($mode, 'page_mode') ?: 'Unknown' }}</span>
                                <span class="text-erp-mute">{{ number_format(data_get($mode, 'total', 0)) }}</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-erp" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-sm text-erp-mute">No module data available.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h2 class="text-base font-bold text-erp-ink">Role Permissions</h2>
                    <p class="mt-1 text-sm text-erp-mute">Roles with the most assigned pages.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($rolePermissionRows as $row)
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <div>
                                <div class="font-bold text-erp-ink">{{ data_get($row, 'role', 'Role') }}</div>
                                <div class="mt-1 text-sm text-erp-mute">Assigned modules</div>
                            </div>
                            <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-black text-erp-ink">{{ number_format(data_get($row, 'total_pages', 0)) }}</span>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-erp-mute">No permission data available.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h2 class="text-base font-bold text-erp-ink">Branches</h2>
                    <p class="mt-1 text-sm text-erp-mute">Latest branch records with company mapping.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($branchRows as $branch)
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <div class="min-w-0">
                                <div class="truncate font-bold text-erp-ink">{{ data_get($branch, 'branch_name', 'Branch') }}</div>
                                <div class="mt-1 truncate text-sm text-erp-mute">{{ data_get($branch, 'company_name', 'Company not mapped') }}</div>
                            </div>
                            <span class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 ring-1 ring-emerald-200">
                                #{{ data_get($branch, 'branch_id', '-') }}
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-erp-mute">No branch data available.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Recent Companies</h2>
                <p class="mt-1 text-sm text-erp-mute">Quick overview of recently created companies.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-5">
                @forelse ($recentCompanies as $company)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Company</div>
                        <div class="mt-3 truncate text-base font-black text-erp-ink">{{ data_get($company, 'name', 'Company') }}</div>
                        <div class="mt-2 text-sm text-erp-mute">ID: {{ data_get($company, 'company_id', '-') }}</div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-sm text-erp-mute">No company data available.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
