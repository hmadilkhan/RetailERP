<html lang="ur">
<head>
    <style>
        body {
            font-family: 'jameel', sans-serif;
            direction: rtl;
        }
    </style>
</head>
<body>
@if(isset($record))  
 <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
	<tr>
		<td colspan="9" style="font-size:18px;font-weight:bold;">{{$branch->company->name." - ".$branch->branch_name." (".$branch->code.")"}}</td>
	</tr>
	<tr>
		<td colspan="9" style="font-size:18px;font-weight:bold;">From {{date("d M Y",strtotime($dates["from"]))}} To {{date("d M Y",strtotime($dates["to"]))}}</td>
	</tr>
	<tr colspan="9"></tr>
	<thead>
	   <tr>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Qty</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Article</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Branch Code</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Item Name</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;"></th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Qty Sold</th>
		  <th style="background-color: #1a4567;color:white;text-align: center;">Qty/Cur</th>
	   </tr>
	</thead>
	<tbody>
	 @if($record)
		 <?php
			$actualQty = 0;
			$totalQtySold = 0;
			$calcTotalQty = 0;
			$grandTotal = 0;
			
		 ?>
		@foreach ($record as $value)
			<?php 
				$itemqty = 0;
				if($value->inventory->id == 817947 or $value->inventory->id == 817992 ){
					$itemqty = $itemqty + ($value->total_qty * 2);
				}else{
					$itemqty = $itemqty + $value->total_qty;
				}
				$actualQty = $actualQty + $value->total_qty;
				$qty = $itemqty / $value->inventory->weight_qty ;
				$originalqty = $value->total_qty / $value->inventory->weight_qty ;
				$totalamount = $originalqty  * $value->item_price;

				$calcTotalQty = $calcTotalQty + $qty ;
				$totalQtySold = $totalQtySold + $itemqty   ;
				$grandTotal = $grandTotal + $value->total_amount//+ $totalamount ;
				

			?>
		   <tr>
			  <td style="text-align: center;">{{ $qty }}</td>
			  <td style="text-align: center;">{{$value->inventory->item_code}}</td>
			  <td style="text-align: center;">{{$value->order->branchrelation->code}}</td>
			  <td style="text-align: center;">{{$value->inventory->product_name}}</td>
			  <td style="text-align: center;"></td>
			  <td style="text-align: center;">{{ $value->total_qty }}</td>
			  <td style="text-align: center;">{{$value->inventory->weight_qty}}</td>
		   </tr>
		  @endforeach
		  <tr colspan="9"><td style="font-size:14px;font-weight:bold;background-color:black;text-align: center;color:white" colspan="9">Total</td></tr>
		  <tr>
				<td style="font-size:12px;font-weight:bold;text-align: center;">{{number_format($calcTotalQty,2) }}</td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;" colspan="3"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;">{{number_format($grandTotal,2)}}</td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
				<td style="font-size:12px;font-weight:bold;text-align: center;">{{number_format($actualQty,2)}}</td>
				<td style="font-size:12px;font-weight:bold;text-align: center;"></td>
		  </tr>
		@endif
	   
	</tbody>
 </table>
@endif	
</body>
</html>