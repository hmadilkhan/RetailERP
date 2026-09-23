<div class="overflow-x-auto">
    <table id="producttb" class="min-w-full text-sm">
        <thead class="sticky top-0 bg-erp-soft">
            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                <th class="px-5 py-3">Product</th>
                <th class="px-5 py-3">Pricing</th>
                <th class="px-5 py-3">Source</th>
                <th class="px-5 py-3">Visibility</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-erp-line">
            @forelse($main as $key => $value)
                @php
                    $recordId = $value->status == 'inventory' ? $value->product_id : $value->pos_item_id;
                    $srcTable = $value->status;
                @endphp
                <tr class="transition hover:bg-erp-soft/60">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('storage/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" alt="" class="h-11 w-11 shrink-0 rounded-lg object-cover ring-1 ring-erp-line">
                            <div class="min-w-0">
                                <div class="truncate font-bold text-erp-ink">{{ $value->product_name }}</div>
                                <div class="mt-0.5 text-xs text-erp-mute">{{ $value->item_code }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <div class="text-base font-black text-erp-ink">{{ $value->retail_price }}</div>
                        <div class="mt-0.5 flex flex-wrap gap-x-3 text-xs text-erp-mute">
                            <span>W: <b class="font-bold text-erp-text">{{ $value->wholesale_price ?? '—' }}</b></span>
                            <span>O: <b class="font-bold text-erp-text">{{ $value->online_price ?? '—' }}</b></span>
                            <span>D: <b class="font-bold text-erp-text">{{ $value->discount_price ?? '—' }}</b></span>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($value->status == 'inventory')
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">Inventory</span>
                        @else
                            <span class="rounded-full bg-erp-light/30 px-2.5 py-1 text-xs font-bold text-erp-dark ring-1 ring-erp-light">POS Product</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-4">
                            <label class="flex cursor-pointer flex-col items-center gap-1" title="Show on POS">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-erp-mute">POS</span>
                                <span class="relative inline-flex">
                                    <input type="checkbox" class="peer sr-only" {{ $value->isPos == 1 ? 'checked' : '' }} onchange="toggleVisibility('{{ $recordId }}','{{ $srcTable }}','pos', this)">
                                    <span class="block h-5 w-9 rounded-full bg-slate-300 transition-colors duration-200 peer-checked:bg-erp after:absolute after:left-[2px] after:top-[2px] after:block after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform after:duration-200 peer-checked:after:translate-x-4"></span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer flex-col items-center gap-1" title="Show online">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-erp-mute">Online</span>
                                <span class="relative inline-flex">
                                    <input type="checkbox" class="peer sr-only" {{ $value->isOnline == 1 ? 'checked' : '' }} onchange="toggleVisibility('{{ $recordId }}','{{ $srcTable }}','online', this)">
                                    <span class="block h-5 w-9 rounded-full bg-slate-300 transition-colors duration-200 peer-checked:bg-erp after:absolute after:left-[2px] after:top-[2px] after:block after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform after:duration-200 peer-checked:after:translate-x-4"></span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer flex-col items-center gap-1" title="Hide item">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-erp-mute">Hide</span>
                                <span class="relative inline-flex">
                                    <input type="checkbox" class="peer sr-only" {{ $value->isHide == 1 ? 'checked' : '' }} onchange="toggleVisibility('{{ $recordId }}','{{ $srcTable }}','hide', this)">
                                    <span class="block h-5 w-9 rounded-full bg-slate-300 transition-colors duration-200 peer-checked:bg-rose-500 after:absolute after:left-[2px] after:top-[2px] after:block after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform after:duration-200 peer-checked:after:translate-x-4"></span>
                                </span>
                            </label>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-sm text-erp-mute">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="border-t border-erp-line px-5 py-4">
    {!! $main->links() !!}
</div>

<script>
    function toggleVisibility(recordId, table, columnname, el) {
        fetch("{{ url('/change-inventory-status') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ id: recordId, table: table, columnname: columnname, value: el.checked ? 1 : 0 })
        }).then(res => {
            if (!res.ok) { el.checked = !el.checked; alert('Could not update. Please try again.'); }
        }).catch(() => { el.checked = !el.checked; alert('Could not update. Please try again.'); });
    }
</script>
