<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-erp-line text-sm">
        <thead class="bg-erp-soft">
            <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                <th class="px-4 py-3">Driver Name</th>
                <th class="px-4 py-3">Loader Name</th>
                <th class="px-4 py-3">Checker Name</th>
                <th class="px-4 py-3">Vehicle Number</th>
                <th class="px-4 py-3">Total Orders</th>
                <th class="px-4 py-3">Assign Time</th>
                <th class="px-4 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-erp-line">
            @forelse($driverOrders as $driverOrder)
                <tr>
                    <td class="px-4 py-3 font-semibold text-erp-ink">{{ $driverOrder->driver->name }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ $driverOrder->loader->fullname }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ !empty($driverOrder->checker) ? $driverOrder->checker->fullname : 'N/A' }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ $driverOrder->vehicles->name }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ $driverOrder->orders }}</td>
                    <td class="px-4 py-3 text-erp-text">{{ date('H:i a', strtotime($driverOrder->created_at)) }}</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" onclick="showItems('{{ $driverOrder->driver->id }}', '{{ $driverOrder->created_at }}')" class="font-bold text-erp-dark hover:text-erp">View Items</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-sm text-erp-mute">No driver assignments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
