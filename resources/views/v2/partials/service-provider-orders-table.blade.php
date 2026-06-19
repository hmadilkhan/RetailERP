<div class="overflow-x-auto">
    <table id="order_table" class="min-w-full divide-y divide-erp-line text-sm">
        <thead class="bg-erp-soft">
            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                <th class="px-4 py-3"><input type="checkbox" class="mainchk rounded border-erp-line text-erp focus:ring-erp"></th>
                <th class="px-4 py-3">Receipt No</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Time</th>
                <th class="px-4 py-3">Customer</th>
                <th class="px-4 py-3">Total Items</th>
                <th class="px-4 py-3">Driver</th>
                <th class="px-4 py-3">Assign Time</th>
                <th class="px-4 py-3">Service Provider</th>
                <th class="px-4 py-3">Order Status</th>
                <th class="px-4 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-erp-line">
            @forelse($data as $key => $value)
                <tr>
                    <td class="px-4 py-3"><input type="checkbox" class="chkbx rounded border-erp-line text-erp focus:ring-erp" data-id="{{ $value->serviceprovidersorders->id }}"></td>
                    <td class="px-4 py-3 font-semibold text-erp-ink">{{ $value->serviceprovidersorders->receipt_no }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ $value->serviceprovidersorders->date }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ date('H:i a', strtotime($value->serviceprovidersorders->time)) }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ $value->serviceprovidersorders->customer->name }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ $value->serviceprovidersorders->total_item_qty }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ !empty($value->serviceprovidersorders->orderassign->driver) ? $value->serviceprovidersorders->orderassign->driver->name : '' }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ !empty($value->serviceprovidersorders->orderassign) ? date('H:i a', strtotime($value->serviceprovidersorders->orderassign->created_at)) : '' }}</td>
                    <td class="px-4 py-3">
                        <select id="serviceprovider{{ $key }}" class="h-9 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" onchange="providerChange({{ $value->id }}, 'serviceprovider{{ $key }}', {{ $value->serviceprovidersorders->id }}, {{ $value->serviceprovider->id }})">
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ $provider->id == $value->serviceprovider->id ? 'selected' : '' }}>{{ $provider->provider_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <select id="orderstatus{{ $key }}" class="h-9 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" onchange="statusChange('orderstatus{{ $key }}', {{ $value->serviceprovidersorders->id }})">
                            @foreach($status as $orderstatus)
                                <option value="{{ $orderstatus->order_status_id }}" {{ $value->serviceprovidersorders->status == $orderstatus->order_status_id ? 'selected' : '' }}>{{ $orderstatus->order_status_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" onclick="showReceipt('{{ $value->serviceprovidersorders->receipt_no }}')" class="font-bold text-erp-dark hover:text-erp">Print</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="px-4 py-6 text-center text-sm text-erp-mute">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
