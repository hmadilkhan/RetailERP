@if(isset($record))  
 <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
	<tr>
		<td colspan="9" style="font-size:18px;font-weight:bold;">{{$branch[0]->company->name." - ".$branch[0]->branch_name." (".$branch[0]->code.")"}}</td>
	</tr>
	<tr>
		<td colspan="9" style="font-size:18px;font-weight:bold;">From {{$dates["from"]}} To {{$dates["to"]}}</td>
	</tr>
	<tr colspan="8"></tr>
	<thead>
	   <tr>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Qty</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Article</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Branch Code</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Item Name</th>
		  @if($mode == "normal")
		  <th style="background-color: #1a4567;color:white;text-align: center;">Price</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Total Amount</th>
		  @endif
		  <th style="background-color: #1a4567;color:white;text-align: center;"></th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Qty Sold</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Qty/Cur</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Total Qty</th>
	   </tr>
	</thead>
	<tbody>
	 @if($record)
		 <?php
			$actualQty = 0;
			$totalQtySold = 0;
			$calcTotalQty = 0;
			$grandTotal = 0;
			$totalQty = 0;
			$gtotal = 0;
			
		 ?>
		@foreach ($record as $value)
			<?php 
				$itemqty = 0;
				if(!empty($value->inventory) && ($value->inventory->id == 817947 or $value->inventory->id == 817992 )){
					$itemqty = $itemqty + ($value->total_qty * 2);
				}else{
					$itemqty = $itemqty + $value->total_qty;
				}
				$actualQty = $actualQty + $value->total_qty;
				$qty = $itemqty / (!empty($value->inventory) ? $value->inventory->weight_qty : 1 ) ;
				$originalqty = $value->total_qty / (!empty($value->inventory) ? $value->inventory->weight_qty : 1 );
				$totalamount = $originalqty  * $value->item_price;
				// $totalamount = $itemqty  * $value->avg_price;
				$calcTotalQty = $calcTotalQty + $qty ;
				$totalQtySold = $totalQtySold + $itemqty   ;
				$grandTotal = $grandTotal + $value->total_amount;//+ $totalamount ;
				$totalQty  = $value->total_qty * (!empty($value->inventory) ? $value->inventory->weight_qty : 1 );
				$gtotal += $totalQty ;

			?>
		   <tr>
			  <td style="text-align: center;">{{ number_format($qty,2) }}</td>
			  <td style="text-align: center;">{{$value->inventory->item_code ?? "-"}}</td>
			  <td style="text-align: center;">{{$value->order->branchrelation->code ?? "-"}}</td>
			  <td style="text-align: left;padding-left:1px;">{{$value->inventory->product_name ?? "-"}}</td>
			  @if($mode == "normal")
			  <td style="text-align: center;">{{ $value->item_price }}</td>
			  <td style="text-align: center;">{{ number_format($value->total_amount,2) }}</td>
			  @endif
			  <td style="text-align: center;"></td>
			  <td style="text-align: center;">{{ $value->total_qty }}</td>
			  <td style="text-align: center;">{{(!empty($value->inventory) ? $value->inventory->weight_qty : 1 )}}</td>
			  <td style="text-align: center;">{{$totalQty}}</td>
		   </tr>
		  @endforeach
		    @if($mode == "normal")
			  <tr ><td style="font-size:14px;font-weight:bold;background-color:black;text-align: center;color:white" colspan="7">Total</td></tr>
			@else
				<tr ><td style="font-size:14px;font-weight:bold;background-color:black;text-align: center;color:white" colspan="9">Total</td></tr>
			@endif
		  <tr>
				<td style="font-size:12px;font-weight:bold;text-align: center;">{{number_format($calcTotalQty,2) }}</td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;" colspan="3"></td>
				@if($mode == "normal")
				<td style="font-size:12px;font-weight:bold;text-align: center;">{{number_format($grandTotal,2)}}</td>
				@endif
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;">{{number_format($actualQty,2)}}</td>
				<td style="font-size:12px;font-weight:bold;text-align: center;">{{number_format($gtotal,2)}}</td>
		  </tr>
		@endif
	   
	</tbody>
 </table>
@endif	
