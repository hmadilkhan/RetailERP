<table id="paymentTable" class="table dt-responsive table-striped nowrap " width="100%" cellspacing="0">
	<thead>
		<tr>
			<!-- <th>Order#</th> -->
			<th>Date</th>
			<th>Time</th>
			<th>PO No</th>
			<th>Vendor</th>
			<th>Address</th>
			<th>Due Date</th>
			<th>Amount</th>
			<th>Balance</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach($result as $key => $value)
			<tr >
				<td>{{date("d M Y",strtotime($value->vendorpurchases->date))}}</td>
				<td>{{date("H:i",strtotime($value->vendorpurchases->time))}}</td>
				<td ><a href="{{url('view',$value->vendorpurchases->purchase_id)}}" target="_blank">{{ $value->vendorpurchases->po_no}}</a></td>
				<td>{{$value->vendorpurchases->vendor["vendor_name"]}}</td>
				<td>{{$value->vendorpurchases->vendor["address"]}}</td>
				<td>{{$value->vendorpurchases->payment_date}}</td>
				<td>{{number_format($value->vendorpurchases->purchaseAccount["total_amount"],2)}}</td>
				<td>{{number_format($value->vendorpurchases->purchaseAccount["balance_amount"],2)}}</td>
				<td class='action-icon'>
					<i onclick="editDueDate('{{$value->vendorpurchases->purchase_id}}','{{$value->vendorpurchases->payment_date}}')" class='icofont icofont-ui-edit text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit Due Date'></i>
					<i onclick="viewPaymentHistory('{{$value->vendorpurchases->vendor['id']}}')" class='icofont icofont-eye-alt  text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit Due Date'></i>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
{{$result->links()}}