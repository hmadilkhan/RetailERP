
<div class="col-lg-12">
 <div class="card-block accordion-block">
	<div id="accordion" role="tablist" aria-multiselectable="true">
		@foreach($driverOrdersDetails as $key => $orderDetail)
	   <div class="accordion-panel" style="border-left: 6px solid #4caf50;">
		  <div class="accordion-heading " role="tab" id="heading{{$key}}">
			<form style="cursor:pointer;" class="form-inline m-t-5 m-l-10 m-b-5" class="accordion-msg collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}" aria-expanded="false" aria-controls="collapse{{$key}}">
				<div class="form-group">
					<label  class="form-control-label text-left">Receipt No</label>
					 <label  class="block text-left">{{$orderDetail->order->receipt_no}}</label>
				</div>
				<div class="form-group m-l-10">
					<label  class="block form-control-label f-14 text-left">Total Amount</label>
					<label  class="block  text-left">{{$orderDetail->order->amount}}</label>
				</div>
				<div class="form-group m-l-10">
					<label  class="block form-control-label text-left">Total Items</label>
					<label  class="block  text-left">{{$orderDetail->order->total_item_qty}}</label>
				</div>
				<div class="form-group m-l-10">
					<label  class="block form-control-label f-14 text-left">Customer</label>
					<label  class="block  text-left">{{$orderDetail->order->customer->name}}</label>
				</div>
				<div class="form-group m-l-10">
					<label  class="block form-control-label f-14 text-left">Customer Mobile</label>
					<label  class="block  text-left">{{$orderDetail->order->customer->mobile}}</label>
				</div>
				<div class="form-group m-l-10">
					<label  class="block form-control-label f-14 text-left">Customer Address</label>
					<label  class="block  text-left">{{$orderDetail->order->customer->address}}</label>
				</div>
				<div class="form-group m-l-10">
					<label  class="block form-control-label f-14 text-left">Status</label>
					<select id="status{{$orderDetail->order->id}}" class="form-control select2" dataplaceholder="Select Status" onchange="orderStatusChange('status{{$orderDetail->order->id}}','{{$orderDetail->order->id}}')">
						@foreach($orderStatus as $val)
							<option {{($val->order_status_id == $orderDetail->order->status ? 'selected' : '')}} value="{{$val->order_status_id}}">{{$val->order_status_name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group m-l-10">
					<label  class="block form-control-label f-14 text-left">Payment Status</label>
					<select  id="paymentStatus{{$orderDetail->order->id}}" class="form-control select2" dataplaceholder="Select Payment Status" onchange="paymentStatusChange('paymentStatus{{$orderDetail->order->id}}','{{$orderDetail->order->id}}')">
						<option value="">Select Payment Status</option>
						<option {{($orderDetail->order->payment_status == "cash" ? 'selected' : '')}} value="cash">Cash</option>
						<option {{($orderDetail->order->payment_status == "credit" ? 'selected' : '')}} value="credit">Credit</option>
						<option {{($orderDetail->order->payment_status == "partial" ? 'selected' : '')}} value="partial">Partial</option>
					</select>
				</div>
			</form>
			<hr/>
		  </div>
		  <div id="collapse{{$key}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$key}}">
			 <div class="accordion-content accordion-desc">
				<div class="row">
					<div class="col-sm-12 table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th>Item Code</th>
									<th>Product Name</th>
									<th>Price</th>
									<th>Qty</th>
									<th>Total Amount</th>
									<th>Narration</th>
								</tr>
							</thead>
							<tbody>
								@foreach($orderDetail->order->orderdetails as $key => $product)
								<tr>
									<td>{{$product->item_code}}</td>
									<td>{{$product->inventory->product_name}}</td>
									<td>{{$product->item_price}}</td>
									<td>{{$product->total_qty}}</td>
									<td>{{$product->amount}}</td>
									<td style="cursor:pointer">
										<label id="narration{{$product->receipt_detail_id}}" style="cursor:pointer" onclick="changeNarration('{{$product->receipt_detail_id}}','{{$product->narration }}')" class="block text-left">{{($product->narration == "" ? "N/A" : $product->narration) }}</label>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			 </div>
		  </div>
	   </div>
	   @endforeach
	</div>
 </div>
<div class="modal fade modal-flex" id="order-narration-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 id="mod-title" class="modal-title">Edit Narration</h4>
			</div>
			<div class="modal-body">
			<input type="hidden" id="modalReceiptDetailsId" /> 
				 <textarea id="narration" class="form-control"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn_edit_narration" class="btn btn-success waves-effect waves-light" onclick="saveNarration($('#modalReceiptDetailsId').val(),$('#narration').val())">Save</button>
			</div>
		</div>
	</div>
</div>
<!--</div>-->
</div>

<script type="text/javascript">
	var rem_id = [];
	$('.select2').select2();
	$('.table').DataTable({
		bLengthChange: true,
		displayLength: 10,
		destroy: true,
		paging:true,
		info: true,
		language: {
		  search:'', 
		  searchPlaceholder: 'Search Order',
		  lengthMenu: '<span></span> _MENU_'
		}
	});
	
	$(".mainchk").on('click',function(){
	
		if($(this).is(":checked")){
			$("#orderAssign").css("display", "block");

			$(".chkbx").each(function( index ) {
			   $(this).prop("checked",true);
			   console.log($(this).attr('id'))
			});

		}else {
			$("#orderAssign").css("display", "none");
			
			$(".chkbx").each(function( index ) {
			  $(this).prop("checked",false);
			  console.log($(this).attr('id'))
			});
		}    
	});
	
	$(".chkbx").on('click',function(){
        if($(this).is(":checked")){
          $("#orderAssign").css("display", "block");
		  $(this).prop("checked",true);
		  console.log($(this).attr('id'))
        }else {
          $("#orderAssign").css("display", "none");
		  $(this).prop("checked",false);
		  console.log($(this).attr('id'))
        }
	});
	
	function changeNarration(orderDetailsId,Narration)
	{
		$("#modalReceiptDetailsId").val(orderDetailsId);
		$("#narration").text(Narration);
		$("#order-narration-modal").modal("show");
	}
	
	function saveNarration(orderDetailsId,narration){
		$("#narration"+orderDetailsId).text(narration);
		$("#order-narration-modal").modal("hide");
		$("#narration").val("");
		saveNarrationAndStatus(orderDetailsId,narration,"","");
		
	}
	
	function orderStatusChange(selectId,orderDetailsId){
		console.log($("#"+selectId).val())
		saveNarrationAndStatus(orderDetailsId,"","",$("#"+selectId).val());
	}
	
	
	function paymentStatusChange(selectId, OrderId){
		console.log($("#"+selectId).val())
		console.log(OrderId)
		saveNarrationAndStatus(OrderId,"",$("#"+selectId).val(),"");
	}
	
	function saveNarrationAndStatus(orderDetailsId,narration,paymentStatus,mainStatus){
		$.ajax({
			url: "{{route('save.narration')}}",
			type: 'POST',
			data:{_token:"{{ csrf_token() }}",
				receipt:orderDetailsId,
				narration : narration,
				paymentStatus:paymentStatus,
				mainStatus: mainStatus
			},
			success:function(result){
				if(result.status == 200){
					swal_alert("Success",result.message,"success","false")
				}else{
					swal_alert("Error",result.message,"error","false")
				}
			}
		});
	}
	
	
	
	
</script>