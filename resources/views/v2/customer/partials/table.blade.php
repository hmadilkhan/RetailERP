<table class="min-w-full divide-y divide-erp-line text-sm">
    <thead class="bg-erp-soft">
        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
            <th class="px-5 py-3">Customer</th>
            <th class="px-5 py-3">Balance</th>
            <th class="px-5 py-3">Mobile</th>
            <th class="px-5 py-3">CNIC</th>
            @if(session("roleId") == 2)
                <th class="px-5 py-3">Branch</th>
            @endif
            @if($status === 'active')
                <th class="px-5 py-3">App</th>
            @endif
            <th class="px-5 py-3 text-right">Action</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-erp-line">
        @forelse($customers as $value)
            <tr class="customer-row" data-search="{{ strtolower($value->name.' '.$value->mobile.' '.$value->nic) }}">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-erp-line" src="{{ asset('storage/images/customers/'.(!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" alt="{{ $value->name }}">
                        <div>
                            <div class="font-bold text-erp-ink">{{ $value->name }}</div>
                            <div class="text-xs text-erp-mute">{{ $value->customer_area ?: 'No area added' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3 font-semibold {{ $value->balance > 0 ? 'text-rose-700' : 'text-erp-text' }}">{{ number_format($value->balance, 2) }}</td>
                <td class="px-5 py-3 text-erp-text">{{ $value->mobile }}</td>
                <td class="px-5 py-3 text-erp-text">{{ $value->nic ?: '-' }}</td>
                @if(session("roleId") == 2)
                    <td class="px-5 py-3 text-erp-text">{{ $value->branch_name }}</td>
                @endif
                @if($status === 'active')
                    <td class="px-5 py-3">
                        <label class="relative inline-flex h-6 w-11 cursor-pointer items-center">
                            <input id="changeCheckbox{{ $value->id }}" onchange="changeCheckbox('changeCheckbox{{ $value->id }}','{{ $value->id }}')" type="checkbox" class="peer sr-only" {{ $value->is_mobile_app_user == 1 ? 'checked' : '' }}>
                            <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>
                            <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                        </label>
                    </td>
                @endif
                <td class="px-5 py-3 text-right">
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        @if($status === 'active')
                            <a href="{{ url('/discount-panel') }}/{{ $value->slug }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">Discount</a>
                            <a href="{{ url('/ledgerDetails') }}/{{ $value->slug }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-bold text-sky-700">Ledger</a>
                            <a href="{{ url('/editcustomers') }}/{{ $value->slug }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">Edit</a>
                            <a href="{{ url('get-customer-receipts', $value->slug) }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700">Invoices</a>
                            <button type="button" onclick="removeCustomer('{{ $value->id }}')" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700">Delete</button>
                        @else
                            <button type="button" onclick="activeCustomer('{{ $value->id }}')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">Activate</button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-5 py-8 text-center text-sm text-erp-mute">No {{ $status }} customers found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
