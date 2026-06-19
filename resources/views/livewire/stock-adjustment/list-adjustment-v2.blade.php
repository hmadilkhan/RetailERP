@section('page_subtitle', 'Browse stock adjustment vouchers with date, code, and branch filters.')

<div class="space-y-6">
    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Stock Adjustment Details</h2>
            <a href="{{ url('stockadjustment') }}" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">+ Create New Adjustment</a>
        </div>

        <form wire:submit="applyFilters" class="p-5">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">From</span>
                    <input type="date" id="from" wire:model="from" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">To</span>
                    <input type="date" id="to" wire:model="to" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Search ItemCode</span>
                    <input type="text" id="code" wire:model="code" placeholder="Enter product item code" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Search Product</span>
                    <input type="text" id="name" wire:model="name" placeholder="Enter product name" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Branch</span>
                    <select id="branch" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option selected value="">Select Branch</option>
                        @foreach ($branches as $value)
                            <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mt-4 flex justify-end gap-3">
                <button type="button" wire:click="clear()" class="rounded-lg border border-erp-line px-5 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</button>
                <button id="submit-button" type="button" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Search</button>
            </div>
        </form>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm" wire:loading.class="opacity-50">
        <div wire:loading class="px-5 py-4 text-sm font-semibold text-erp-mute">Loading...</div>
        <div wire:loading.remove>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Ref#</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Code</th>
                            <th class="px-5 py-3">Name</th>
                            <th class="px-5 py-3">Qty</th>
                            <th class="px-5 py-3">Cost</th>
                            <th class="px-5 py-3">Created By</th>
                            <th class="px-5 py-3">Narration</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @if (!empty($stocks))
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td class="px-5 py-3 text-erp-text">{{ !empty($stock->productstock) ? $stock->productstock->grn_id : 0 }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ date('d-m-Y', strtotime($stock->date)) }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $stock->products->item_code ?? '-' }}</td>
                                    <td class="px-5 py-3 font-semibold text-erp-ink">{{ $stock->products->product_name ?? '-' }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $stock->qty }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $stock->cost }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ !empty($stock->productstock) ? $stock->productstock->grn->user->fullname : '-' }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $stock->narration }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <a target="_blank" href="{{ route('stock.adjustment.voucher', !empty($stock->productstock) ? $stock->productstock->grn_id : '') }}" class="font-bold text-erp-dark hover:text-erp">Print</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="9" class="px-5 py-6 text-center text-sm text-erp-mute">No record found</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if (!empty($stocks))
                <div class="border-t border-erp-line px-5 py-4">
                    {{ $stocks->links() }}
                </div>
            @endif
        </div>
    </section>
</div>

@script
    <script>
        document.getElementById('submit-button').addEventListener('click', function (e) {
            e.preventDefault();

            const from = document.getElementById('from').value;
            const to = document.getElementById('to').value;
            const code = document.getElementById('code').value;
            const name = document.getElementById('name').value;
            const branch = document.getElementById('branch').value;

            @this.call('submitForm', from, to, code, name, branch);
        });
    </script>
@endscript
