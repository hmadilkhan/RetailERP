<div class="overflow-x-auto">
    <table id="producttb" class="min-w-full divide-y divide-erp-line text-sm">
        <thead class="bg-erp-soft">
            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                <th class="px-5 py-3">Product Id</th>
                <th class="px-5 py-3">Product Code</th>
                <th class="px-5 py-3">Name</th>
                <th class="px-5 py-3">Retail</th>
                <th class="px-5 py-3">Wholesale</th>
                <th class="px-5 py-3">Online</th>
                <th class="px-5 py-3">Discount</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-center">POS</th>
                <th class="px-5 py-3 text-center">Online</th>
                <th class="px-5 py-3 text-center">Hide</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-erp-line">
            @forelse($main as $key => $value)
                <tr>
                    <td class="px-5 py-3 text-erp-text">{{ $value->product_id }}</td>
                    <td class="px-5 py-3 text-erp-text">{{ $value->item_code }}</td>
                    <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->product_name }}</td>
                    <td class="px-5 py-3 text-erp-text">{{ $value->retail_price }}</td>
                    <td class="px-5 py-3 text-erp-text">{{ $value->wholesale_price }}</td>
                    <td class="px-5 py-3 text-erp-text">{{ $value->online_price }}</td>
                    <td class="px-5 py-3 text-erp-text">{{ $value->discount_price }}</td>
                    <td class="px-5 py-3 text-erp-text">{{ $value->status }}</td>
                    <td class="px-5 py-3 text-center">
                        <select id="select{{ $key }}pos" onchange="valueChange('select{{ $key }}pos','{{ $value->status == 'inventory' ? $value->product_id : $value->pos_item_id }}','{{ $value->status }}','pos')" class="h-9 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option {{ $value->isPos == 1 ? 'selected' : '' }} value="1">Yes</option>
                            <option {{ $value->isPos == 0 ? 'selected' : '' }} value="0">No</option>
                        </select>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <select id="select{{ $key }}online" onchange="valueChange('select{{ $key }}online','{{ $value->status == 'inventory' ? $value->product_id : $value->pos_item_id }}','{{ $value->status }}','online')" class="h-9 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option {{ $value->isOnline == 1 ? 'selected' : '' }} value="1">Yes</option>
                            <option {{ $value->isOnline == 0 ? 'selected' : '' }} value="0">No</option>
                        </select>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <select id="select{{ $key }}hide" onchange="valueChange('select{{ $key }}hide','{{ $value->status == 'inventory' ? $value->product_id : $value->pos_item_id }}','{{ $value->status }}','hide')" class="h-9 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option {{ $value->isHide == 1 ? 'selected' : '' }} value="1">Yes</option>
                            <option {{ $value->isHide == 0 ? 'selected' : '' }} value="0">No</option>
                        </select>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="px-5 py-6 text-center text-sm text-erp-mute">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="border-t border-erp-line px-5 py-4">
    {!! $main->links() !!}
</div>

<script>
    function valueChange(id, recordId, table, columnname) {
        const value = document.getElementById(id).value;
        fetch("{{ url('/change-inventory-status') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ id: recordId, table, columnname, value })
        });
    }
</script>
