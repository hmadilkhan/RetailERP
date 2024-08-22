<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Receipt No.</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->id }}</td>
            <td>{{ $invoice->receipt_no }}</td>
        </tr>
    @endforeach
    </tbody>
</table>