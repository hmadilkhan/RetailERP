@if(isset($record))  
 <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
	<tr>
		<td colspan="8" style="font-size:18px;font-weight:bold;">{{$branch}}</td>
	</tr>
	<tr colspan="8"></tr>
	<thead>
	   <tr>
		  <th style="background-color: #1a4567;color:white;text-align: center;">S.No #</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Terminal</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Receipt No</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Customer Name</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Booking Date</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Booking Amount</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Advance Amount</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Received Amount</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Received Date</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Payment Mode</th>
	   </tr>
	</thead>
	<tbody>
		@if($record)
			@foreach ($record as $key => $value)
			   <tr>
				  <td style="text-align: left;">{{++$key}}</td>
				  <td style="text-align: left;">{{$value->terminal_name}}</td>
				  <td style="text-align: left;">{{$value->receipt_no}}</td>
				  <td style="text-align: left;">{{$value->name}}</td>
				  <td style="text-align: center;">{{date("Y-m-d",strtotime($value->date))}}</td>
				  <td style="text-align: left;">{{$value->total_amount}}</td>
				  <td style="text-align: left;">{{$value->paid}}</td>
				  <td style="text-align: left;">{{$value->receive_amount}}</td>
                  <td style="text-align: center;">{{date("Y-m-d",strtotime($value->received_date))}}</td>
				  <td style="text-align: left;">{{$value->payment_mode}}</td>
			   </tr>
			@endforeach
		@endif
	</tbody>
 </table>
@endif	
