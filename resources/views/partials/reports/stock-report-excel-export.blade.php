@if(isset($record))  
 <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
	<tr>
		<td colspan="8" style="font-size:18px;font-weight:bold;">{{$branch}}</td>
	</tr>
	<tr colspan="8"></tr>
	<thead>
	   <tr>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Item Code</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Product Name</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Department</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Sub-Department</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Amount</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Qty</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">UOM</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Conversion Qty.</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Stock</th>
	   </tr>
	</thead>
	<tbody>
		@if($record)
			@foreach ($record as $value)
			   <tr>
				  <td style="text-align: left;">{{$value->item_code}}</td>
				  <td style="text-align: left;">{{$value->product_name}}</td>
				  <td style="text-align: left;">{{$value->department_name}}</td>
				  <td style="text-align: left;">{{$value->sub_depart_name}}</td>
				  <td style="text-align: center;">  {{$value->amount}}</td>
				  <td style="text-align: left;">{{$value->qty}}</td>
				  <td style="text-align: left;">{{$value->name}}</td>
				  <td style="text-align: left;">{{$value->qty * $value->weight_qty}}</td>
				  <td style="text-align: left;">{{($value->qty == 0 ? "Out of Stock" : ($value->qty > 0 && $value->qty > $value->reminder_qty ? "In Stock" : (($value->qty <= $value->reminder_qty) ? "Low Stock" : "Out Of Stock")))}}</td>
			   </tr>
			@endforeach
		@endif
	</tbody>
 </table>
@endif	
