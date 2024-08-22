@if(isset($record))  
<div class="card card-block">
 <div class="project-table">
	 <table id="widget-product-list" class="table table-striped nowrap dt-responsive nowrap" width="100%" cellspacing="0">
		<thead>
		   <tr>
			  <th>Code</th>
			  <th>Name</th>
			  <th>Qty</th>
			  <th>Branch</th>
			  <th>Terminal</th>
			  <th>Date</th>
		   </tr>
		</thead>
		<tbody>
		 @if($record)
			@foreach ($record as $value)	
			    <tr>
				  <td>{{(!empty($value->inventory) ? $value->inventory->item_code : $value->order->id)}}</td>
				  <td class="pro-name">{{(!empty($value->inventory) ? $value->inventory->product_name : 'NA')}}</td>
				  <td>{{ $value->total_qty }}</td>
				  <td>{{ $value->order->branchrelation->branch_name }}</td>
				  <td>{{ $value->order->terminal->terminal_name }}</td>
				  <td>{{ date("d F Y",strtotime($value->order->date)) }}</td>
			    </tr>
			  @endforeach
			@endif
		</tbody>
	 </table>
  </div>
</div>
@endif	
<script type="text/javascript">
$('#widget-product-list').DataTable({
	displayLength: 50,
	info: true,
	language: {
	  search:'', 
	  searchPlaceholder: 'Search Item',
	  lengthMenu: '<span></span> _MENU_'
	}
});
// console.log("Total Receipts","{{$totalCountReceipts}}")
$("#totaldiv").css("display","block");
$("#totalorders").html("{{$totalQty}}");
// $("#totalamount").html("{{number_format($totalAmount,2)}}");
$("#totalamount").html("{{number_format($totalAmountReceipts,2)}}");
$("#totalreceipts").html("{{$totalCountReceipts}}");
</script>