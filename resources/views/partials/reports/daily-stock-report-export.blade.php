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
		  <th style="background-color: #1a4567;color:white;text-align: center;">Opening Date</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Opening Stock</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Sales</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Closing Date</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Closing Stock</th>
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
				  <td style="text-align: center;">{{$value->opening_date}}</td>
				  <td style="text-align: left;">{{$value->opening_stock}}</td>
				  <td style="text-align: left;">{{$value->sales}}</td>
				  <td style="text-align: left;">{{$value->closing_stock}}</td>
				  <td style="text-align: left;">{{$value->closing_date}}</td>
			   </tr>
			@endforeach
		@endif
	</tbody>
 </table>
@endif	
