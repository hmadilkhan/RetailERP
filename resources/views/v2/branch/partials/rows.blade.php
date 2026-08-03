@forelse ($details as $value)
    <tr class="hover:bg-slate-50">
        <td class="px-5 py-4">
            <div class="flex items-center gap-3">
                <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200"
                    src="{{ asset('storage/images/branch/' . (!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg')) }}"
                    alt="{{ $value->branch_name }}">
                <div class="min-w-0">
                    <div class="truncate font-bold text-erp-ink">{{ $value->branch_name }}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">
                        {{ $value->code ?? 'No code' }}
                    </div>
                </div>
            </div>
        </td>
        <td class="px-5 py-4 text-erp-text">{{ $value->city->city_name ?? '-' }}</td>
        <td class="px-5 py-4">
            <div class="font-semibold text-erp-ink">{{ $value->branch_mobile ?? '-' }}</div>
            <div class="mt-1 text-xs text-erp-mute">{{ $value->branch_email ?? '-' }}</div>
        </td>
        <td class="px-5 py-4">
            <div class="flex flex-wrap gap-1">
                @forelse ($value->terminals->pluck('terminal_id')->filter() as $tid)
                    <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $tid }}</span>
                @empty
                    <span class="text-erp-mute">-</span>
                @endforelse
            </div>
        </td>
        <td class="px-5 py-4">
            <div class="flex flex-wrap gap-1">
                @forelse ($value->terminals->pluck('serial_no')->filter() as $sno)
                    <span class="rounded-md bg-sky-50 px-2 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-200">{{ $sno }}</span>
                @empty
                    <span class="text-erp-mute">-</span>
                @endforelse
            </div>
        </td>
        <td class="max-w-xs px-5 py-4 text-erp-mute">
            <div class="line-clamp-2">{{ $value->branch_address ?? '-' }}</div>
        </td>
        <td class="px-5 py-4">
            <div class="flex justify-end gap-2">
                <a href="{{ url('/branch-emails') }}/{{ Crypt::encrypt($value->branch_id) }}"
                    class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                    Emails
                </a>
                <a href="{{ url('/branch-edit') }}/{{ Crypt::encrypt($value->branch_id) }}"
                    class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                    Edit
                </a>
                <button type="button" onclick="deleteBranch('{{ $value->branch_id }}')"
                    class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                    Delete
                </button>
                <a href="{{ url('/terminals') }}/{{ Crypt::encrypt($value->branch_id) }}"
                    class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">
                    Terminal
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-5 py-14 text-center">
            <div class="text-base font-bold text-erp-ink">No branches found</div>
            <p class="mt-2 text-sm text-erp-mute">{{ $search ? 'Try a different search term.' : 'Create your first branch to get started.' }}</p>
        </td>
    </tr>
@endforelse
