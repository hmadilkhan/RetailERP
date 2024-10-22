<div>
    @if (isset($moreDetails[$index]))
        <tr>
            <td colspan="8">
                <div>
                    <!-- Display additional details for the selected stock -->
                    <strong>Stock Details:</strong>
                    {{-- {{ $moreDetails[$index]['additional_info'] }} --}}
                </div>
            </td>
        </tr>
        <tr class="m-4 p-4 bg-light">
            <th>Stock Id</th>
            <th>GRN #</th>
            <th>Qty</th>
            <th colspan="2">Balance</th>
            <th>Date</th>
            <th colspan="2">Narartion</th>
        </tr>
        @if (count($moreDetails[$index]['stock']) > 0)
            @foreach ($moreDetails[$index]['stock'] as $stockDetail)
                <tr>
                    <td>{{ $stockDetail->id }}</td>
                    <td>{{ $stockDetail->grn_number }}</td>
                    <td>{{ $stockDetail->quantity }}</td>
                    <td colspan="2">{{ $stockDetail->balance }}</td>
                    <td>{{ $stockDetail->date }}</td>
                    <td colspan="2">{{ $stockDetail->narration }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">No Record Found</td>
            </tr>
        @endif
    @endif

