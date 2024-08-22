<table id="paymentTable" class="table dt-responsive table-striped nowrap " width="100%" cellspacing="0">
	<thead>
		<tr>
			<th>Date</th>
			<th>Time</th>
			<th>PO No</th>
			<th>Amount</th>
		</tr>
	</thead>
	<tbody>
		@foreach($history as $key => $value)
			<tr >
				<td>{{date("d M Y",strtotime($value->created_at))}}</td>
				<td>{{date("H:i",strtotime($value->created_at))}}</td>
				@if($value->po_no > 0)
					<td ><a href="{{url('view',$value->po_no)}}" target="_blank">{{ $value->po_no}}</a></td>
				@else
					<td >{{ $value->po_no}}</td>	
				@endif
				<td>{{number_format($value->debit,2)}}</td>
			</tr>
		@endforeach
	</tbody>
</table>