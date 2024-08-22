@if(isset($record))  
 <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
	<tr>
		<td colspan="8" style="font-size:18px;font-weight:bold;">{{$branch[0]->company->name." - ".$branch[0]->branch_name." (".$branch[0]->code.")"}}</td>
	</tr>
	<tr>
		<td colspan="8" style="font-size:18px;font-weight:bold;">From {{$dates["from"]}} To {{$dates["to"]}}</td>
	</tr>
	<tr colspan="8"></tr>
	<thead>
	   <tr>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Machine #</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Order #</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Date</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Time</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Branch</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Terminal</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Customer</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">OrderType</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Payment</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Count/Total</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Delivery Date</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Status</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Amount</th>
	   </tr>
	</thead>
	<tbody>
	 @if($record)
		 <?php
			$totalItem = 0 ;
			
		 ?>
		@foreach ($record as $value)
			<?php 
				$totalItem++;

			?>
		   <tr>
			  <td style="text-align: center;">{{$value->machine_terminal_count }}</td>
			  <td style="text-align: center;">{{$value->id}}</td>
			  <td style="text-align: center;">{{date("d M Y",strtotime($value->date))}}</td>
			  <td style="text-align: center;">{{date("H:i a",strtotime($value->time))}}</td>
			  <td style="text-align: center;">{{$value->branchrelation->branch_name}}</td>
			  <td style="text-align: center;">{{$value->terminal->terminal_name }}</td>
			  <td style="text-align: left;">{{$value->bill_print_name}}</td>
			  <td style="text-align: center;">{{$value->mode->order_mode}}</td>
			  <td style="text-align: center;">{{(!empty($value->payment) ? $value->payment->payment_mode : 0)}}</td>
			  <td style="text-align: center;">{{$value->orderdetails_count."/".$value->amount_sum}}</td>
			  <td style="text-align: center;">{{date("d-m-Y",strtotime($value->delivery_date))}}</td>
			  <td style="text-align: center;">{{$value->orderStatus->order_status_name}}</td>
			  <td style="text-align: center;">{{$value->total_amount}}</td>
		   </tr>
		  @endforeach
		  <tr>
				<td style="font-size:12px;font-weight:bold;text-align: center;">{{number_format($totalItem,2) }}</td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;" colspan="3"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
		  </tr>
		@endif
	   
	</tbody>
 </table>
@endif	
