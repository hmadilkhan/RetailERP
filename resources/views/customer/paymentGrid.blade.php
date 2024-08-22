<table class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">
	<thead>
		<tr>
			<td>Sr#</td>
			<td>Received Amount</td>
			<td>Salesman</td>
			<td>Date</td>
			<td>Time</td>
		</tr>
	</thead>
	<tbody>
		@foreach($data AS $val)
		<tr>
			<td>{{ $loop->index +1 }}</td>
			<td>{{$val->payment_received}}</td>
			<td>{{$val->name}}</td>
			<td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $val->created_at)->format('M d, Y') }}</td>
			<td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $val->created_at)->format('g:i A') }}</td>
		</tr>
		@endforeach
	</tbody>
</table>