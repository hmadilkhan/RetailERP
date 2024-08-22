@extends('layouts.master-layout')

@section('title','Orders')

@section('breadcrumtitle','Orders Panel')

@section('navbranchoperation','active')
@section('navorder','active')

@section('content')

 <section class="panels-wells">
    <div class="card">
		<div class="card-header">
         <h5 class="card-header-text">Order Details</h5>
         <div class="row m-l-20">
              <div class="rkmd-checkbox checkbox-rotate checkbox-ripple">
                  <label class="input-checkbox checkbox-primary">
                  <input type="checkbox" id="checkbox">
                  <span class="checkbox"></span>
              </label>
                  <div class="captions">Search Delivery Orders </div>
              </div>
          </div>
         <div class="row">
            <div id="customernumber" class="col-md-2">
              <div class="form-group">
                    <label class="form-control-label">Mobile No.</label>
                        <input type='text' class="form-control" id="customer_number" name="customer_number" placeholder="Mobile No"/>  
                        <span class="help-block text-danger" id="customernumber"></span>  
                    </div>
            </div>
			<div id="machineorder#" class="col-md-2">
              <div class="form-group">
                    <label class="form-control-label">Machine Order No.</label>
                        <input type='text' class="form-control" id="machine_order_no" name="machine_order_no" placeholder="Machine Order No"/>  
                        <span class="help-block text-danger" id="machineorder#"></span>  
                    </div>
            </div>
			<div id="order#" class="col-md-2">
              <div class="form-group">
                    <label class="form-control-label">Order#</label>
                        <input type='text' class="form-control" id="order_no" name="order_no" placeholder="Order No"/>  
                        <span class="help-block text-danger" id="order#"></span>  
                    </div>
            </div>
           <div class="col-md-2">
             <div class="form-group">
                    <label class="form-control-label">Receipt No</label>
                            <input type='text' class="form-control" id="receipt" name="receipt" placeholder="Receipt No"/>  
                            <span class="help-block text-danger" id="rpbox"></span>  
                    </div>
           </div>
		   
           <div id="from" class="col-md-2">
             <div class="form-group">
                    <label class="form-control-label">From Date</label>
                            <input type='text' class="form-control" id="rpdate" name="rpdate" placeholder="DD-MM-YYYY"/>  
                            <span class="help-block text-danger" id="rpbox"></span>  
                    </div>
           </div>
		   
            <div id="to" class="col-md-2">
              <div class="form-group">
                    <label class="form-control-label">To Date</label>
                        <input type='text' class="form-control" id="date" name="date" placeholder="DD-MM-YYYY"/>  
                        <span class="help-block text-danger" id="dbox"></span>  
                    </div>
            </div>
			
            <div id="deliveryfrom" class="col-md-2">
             <div class="form-group">
                    <label class="form-control-label">Delivery From Date</label>
                            <input type='text' class="form-control" id="del_from" name="rpdate" placeholder="DD-MM-YYYY"/>  
                            <span class="help-block text-danger" id="rpbox"></span>  
                    </div>
           </div>
            <div id="deliveryto" class="col-md-2">
              <div class="form-group">
                    <label class="form-control-label">Delivery To Date</label>
                        <input type='text' class="form-control" id="del_to" name="date" placeholder="DD-MM-YYYY"/>  
                        <span class="help-block text-danger" id="dbox"></span>  
                    </div>
            </div>

         <!--</div>

         <div class="row">-->
             <div class="col-md-3">
                 <label class="form-control-label">Select Payment Mode</label>
                 <select id="paymentmode" name="paymentmode" class="f-right select2" data-placeholder="Select Payment Mode">
                     <option value="">Select Payment Mode</option>
                     @foreach($paymentMode as $value)
                         <option value="{{ $value->payment_id }}">{{ $value->payment_mode }}</option>
                     @endforeach
                 </select>
             </div>
             <div class="col-md-3">
                 <label class="form-control-label">Select Mode</label>
                 <select id="ordermode" name="ordermode" data-placeholder="Select Mode" class="f-right select2">
                     <option value="">Select Mode</option>
                     @foreach($mode as $value)
                         <option value="{{ $value->order_mode_id }}">{{ $value->order_mode }}</option>
                     @endforeach
                 </select>
             </div>
			 <div class="col-md-3">
                 <label class="form-control-label">Select Status</label>
                 <select id="orderstatus" name="orderstatus" data-placeholder="Select Status" class="f-right select2">
                     <option value="">Select Status</option>
                     @foreach($statuses as $status)
                         <option value="{{ $status->order_status_id }}">{{ $status->order_status_name }}</option>
                     @endforeach
                 </select>
             </div>
             <div class="col-md-3">
                 <label class="form-control-label">Select Branch</label>
                 <select id="branch" name="branch" data-placeholder="Select Branch" class="f-right select2">
                     <option value="all">All</option>
                     @foreach($branch as $value)
                         <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                     @endforeach
                 </select>
             </div>

             <div class="col-md-3" style="margin-top: 20px;">
                 <label class="form-control-label">Select Terminal</label>
                 <select id="terminal" name="terminal" data-placeholder="Select Terminal" class="f-right select2">
                     <option value="">Select Terminal</option>
                 </select>
             </div>
              <div class="col-md-3" style="margin-top: 20px;">
                 <label class="form-control-label">Select Customer</label>
                 <select id="customer" name="customer" data-placeholder="Select Customer" class="f-right select2">
                     <option value="">Select Customer</option>
                     @foreach($customer as $value)
                         <option value="{{ $value->id }}">{{ $value->name }}</option>
                     @endforeach
                 </select>
             </div>
			 <div class="col-md-3" style="margin-top: 20px;">
                 <label class="form-control-label">Select Sales Tax</label>
                 <select id="sales_tax" name="sales_tax" data-placeholder="Select Sales Tax" class="f-right select2">
                     <option value="">Select Sales Tax</option>
                     <option value="fbr">FBR</option>
                     <option value="srb">SRB</option>
                 </select>
             </div>
			 <div class="col-md-3" style="margin-top: 20px;">
                 <label class="form-control-label">Select Type</label>
                 <select id="type" name="type" data-placeholder="Select Type" class="f-right select2">
                     <option value="declaration">Declaration</option>
                     <option value="datewise">Datewise</option>
                 </select>
             </div>
         <!--</div>

         <div class="row">-->

             <div class="col-md-6 f-right">
                 <label class="form-control-label"></label>
				 <button type="button" onclick="clearSearchFields()" class="btn btn-info waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-file-pdf" > </i>Clear All
                 </button>
				 <button type="button" id="btnExcel"  class="btn btn-success waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-file-excel" > </i>Excel Export
                 </button>
				 <button type="button" id="btnPdf"  class="btn btn-danger waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-file-pdf" > </i>PDF Export
                 </button>
				 <button type="button" id="fetch"  class="btn btn-success waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-ui-check"> </i>Fetch
                 </button>
             </div>
         </div>

         
         </div>
         <hr/> 
		 @if(session("roleId") != 17)
			<div class="row dashboard-header m-l-2">
               <div class="col-lg-3 col-md-6">
                  <div class="card dashboard-product">
                     <span>Total Orders</span>
                     <h2 class="dashboard-total-products" id="totalorders">{{$totalorders[0]->totalorders}}</h2>
                     <span class="label label-warning">Orders</span>
                     <div class="side-box">
                        <i class="ti-package text-warning-color"></i>
                     </div>
                  </div>
               </div>
               
               <div class="col-lg-3 col-md-6">
                  <div class="card dashboard-product">
                     <span>Total Sales</span>
                     <h2 class="dashboard-total-products">{{session("currency")}} <span id="totalamount">{{number_format($totalorders[0]->totalamount,2)}}</span></h2>
                     <span class="label label-success">Sales</span>
                     <div class="side-box">
                        <i class="ti-direction-alt text-success-color"></i>
                     </div>
                  </div>
               </div>
            </div>
		@endif
		<hr/> 		 
		<div class="card-block">
			<div id="table_data">
			{{--@include('partials.orders_table')--}}
			</div>
		</div>
	   
	</div>
	<div class="modal fade modal-flex" id="sp-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
						<h4 id="mod-title" class="modal-title">Select Service Provider</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<input type="hidden" id="orderidforsp" name="orderidforsp" />
							<div class="col-md-12">
								<label class="form-control-label">Select Service Provider</label>
								 <select id="serviceprovider" name="serviceprovider" data-placeholder="Select Service Provider" class="f-right select2">
									 <option value="">Select Service Provider</option>
									 @foreach($serviceproviders as $provider)
										 <option value="{{ $provider->id }}">{{ $provider->provider_name }}</option>
									 @endforeach
								 </select>
							</div>
							<div class="col-md-12 m-t-5">
								<label class="form-control-label">Enter Narration</label>
								 <textarea id="narration" class="form-control"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" id="btn_assign" class="btn btn-success waves-effect waves-light">Assign</button>
					</div>
				</div>
		</div>
	</div>
	<div class="modal fade modal-flex" id="void-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<input type="hidden"  name="voidId" id="voidId" class="form-control" />
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Void Receipt</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group"> 
								<label class="form-control-label">Reason :</label>
								<input type="text"  name="reason" id="reason" class="form-control" />
								<span id="reason_message" class="text-danger"></span>
							</div>
						</div>
					</div>   
				</div>
			<div class="modal-footer">
			<button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="saveVoid()">Save Department</button>
			</div>
			</div>
		</div>
	</div> 
</section>

@endsection

@section('scriptcode_three')
<script type="text/javascript">
  $(".select2").select2();
  getTerminal();
  $("#date").val('{{date("Y-m-d")}}')
  $("#rpdate").val('{{date("Y-m-d")}}')

   $('#date,#rpdate,#del_from,#del_to').bootstrapMaterialDatePicker({
		format: 'YYYY-MM-DD',
		time: false,
		clearButton: true,

	  icons: {
		  date: "icofont icofont-ui-calendar",
		  up: "icofont icofont-rounded-up",
		  down: "icofont icofont-rounded-down",
		  next: "icofont icofont-rounded-right",
		  previous: "icofont icofont-rounded-left"
		}
    });
   
    $('#checkbox').change(function(){
		  if ($('#checkbox').is(":checked"))
		  {
			$('#from').css("display","none");
			$('#to').css("display","none");
			$('#deliveryfrom').css("display","block");
			$('#deliveryto').css("display","block");
			$('#rpdate').val('');
			$('#date').val('');
		  }
		  else
		  {
			$('#from').css("display","block");
			$('#to').css("display","block");
			$('#deliveryfrom').css("display","none");
			$('#deliveryto').css("display","none");
		  }
	});
			$('#from').css("display","block");
			$('#to').css("display","block");
			$('#deliveryfrom').css("display","none");
			$('#deliveryto').css("display","none");
			

	// $(document).ready(function(){

	 $(document).on('click', '.pagination a', function(event){
		  event.preventDefault(); 
		  var page = $(this).attr('href').split('page=')[1];
		  fetch_data(page)
		  
	 });

	 function fetch_data(page)
	 {
	  $.ajax({
		url: "{{url('get-pos-orders-new')}}"+ "?page="+page,
		type: 'GET',
		data:{
		  machineOrderNo:$('#machine_order_no').val(),
		  customerNo:$('#customer_number').val(),
		  payMode:$('#paymentmode').val(),
		  first:$('#rpdate').val(),
		  second:$('#date').val(),
		  customer:$('#customer').val(),
		  receipt:$('#receipt').val(),
		  mode:$('#ordermode').val(),
		  status:$('#orderstatus').val(),
		  deli_from:$('#del_from').val(),
		  deli_to:$('#del_to').val(),
		  branch:$('#branch').val(),
		  terminal:$('#terminal').val(),
		  order_no:$('#order_no').val(),
		  sales_tax:$('#sales_tax').val(),
		  type:$('#type').val(),
        },
	    success:function(data)
	    {
		  $('#table_data').html(data);
	    } 
	  });
	 }
	 fetch_data(1);
	
	$("#fetch").click(function(){
		fetch_data(1);
	});
	
	
	 
	// });
	
	function clearSearchFields()
	{
	  $('#paymentmode').val("").change()
	  $('#rpdate').val("")
	  $('#date').val("")
	  $('#customer').val("").change()
	  $('#receipt').val("")
	  $('#ordermode').val("").change()
	  $('#del_from').val("")
	  $('#del_to').val("")
	  $('#branch').val("").change()
	  $('#terminal').val("").change()
	  $('#order_no').val("")
	  $('#sales_tax').val("").change()
	}
	
	function assignToServiceProviderModal(receiptId)
	{
	  $('#sp-modal').modal("show");
	  $("#orderidforsp").val(receiptId);
	}
  
	$("#btn_assign").click(function(){
	  var sp = $("#serviceprovider").val();
	  var receiptId = $("#orderidforsp").val();
	  if(sp == ""){
		  alert("Please Select Service Provider")
	  }else{
		  $.ajax({
			url : "{{url('/assign-service-provider')}}",
			type : "POST",
			data : {_token : "{{csrf_token()}}", receiptId:receiptId,sp:sp,narration:$("#narration").val()},
			dataType : 'json',
			success : function(result){
				// console.log(result);
				if(result.status == 200){
					$('#sp-modal').modal("hide");
					location.reload();
				}
			}
		});
	  }
	})
	
	function showReceipt(ReceiptNo) {
		orderSeen(ReceiptNo);
	}
	
	async function orderSeen(ReceiptNo){
		$.ajax({
            url : "{{url('/order-seen')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}", receiptNo:ReceiptNo},
            dataType : 'json',
            success : function(result){
                if(result != ""){
					openReceipt(ReceiptNo)
				}
            }
        });
		openReceipt(ReceiptNo)
	}
	
	function openReceipt(ReceiptNo)
	{
		window.open("{{url('print')}}"+"/"+ReceiptNo)
	}
	
	$("#branch").change(function(){
	   getTerminal();
    })
	
	function getTerminal()
	{
		$.ajax({
			url: "{{ route('getTerminals') }}",
			type: 'POST',
			dataType: 'json',
			data: {
				_token: "{{ csrf_token() }}",
				branch: $("#branch").val(),
			},
			beforeSend: function() {
				$('#loader').removeClass('hidden')
			},
			success: function(result) {
				// console.log("Fetching Results",result);
				$('#loader').addClass('hidden')
				if (result != 0) {
					$("#terminal").empty();
					$.each(result.terminal, function() {
						$("#terminal").append('<option value="' + this.terminal_id + '"+>' + this
							.terminal_name + '</option>');
					}); 
				} 
			},
			complete: function() {
				$('#loader').addClass('hidden')
			},
			error: function(error) {
				$('#loader').addClass('hidden')
				$("#btn_search_report").attr("disabled",false);
				console.log("Error", error);
			},

		});
	}
	
	function voidReceipt(id)
	{
		$("#voidId").val(id);
		$("#void-modal").modal("show");
		// $('.alert-confirm').on('click',function(){
		
		  // swal({
			  // title: "Are you sure?",
			  // text: "Receipt will be void ??!",
			  // type: "warning",
			  // showCancelButton: true,
			  // confirmButtonClass: "btn-danger",
			  // confirmButtonText: "delete it!",
			  // cancelButtonText: "cancel plx!",
			  // closeOnConfirm: false,
			  // closeOnCancel: false
			// },
			// function(isConfirm){
			  // if(isConfirm){
						 // $.ajax({
							// url: "{{ url('make-receipt-void')}}",
							// type: 'POST',
							// data:{_token:"{{ csrf_token() }}",id:id},
							// dataType:"json",
							// success:function(resp){
								// if(resp.status == 200){
									 // swal({
											// title: "Deleted",
											// text: resp.message,
											// type: "success"
									   // },function(isConfirm){
										   // if(isConfirm){
											   // fetch_data(1)
											// window.location="{{ route('vendors.index') }}";
										   // }
									   // });
								 // }
							// }

						// });
			   // }else {
				  // swal("Cancelled", "Your receipt is safe :)", "error");
			   // }
			// });
	  // });
	}
	
	function saveVoid()
	{
		$("#reason_mesasge").html("");
		if($("#reason").val() == ""){
			$("#reason_message").html("Please select reason");
		}else{
			$.ajax({
				url: "{{ url('make-receipt-void')}}",
				type: 'POST',
				data:{_token:"{{ csrf_token() }}",id:$("#voidId").val(),reason:$("#reason").val()},
				dataType:"json",
				success:function(resp){
					if(resp.status == 200){
						 swal({
							title: "Deleted",
							text: resp.message,
							type: "success"
						   },function(isConfirm){
							   if(isConfirm){
								   fetch_data(1)
								// window.location="{{ route('vendors.index') }}";
									$("#reason_mesasge").html("");
									$("#void-modal").modal("hide");
									$("#voidId").val("")
							   }
						   });
					 }
				}

			});
		}
	}
	
	$("#btnExcel").click(function(){
		if($('input[name="fromdate"]').val() == '' && $('input[name="todate"]').val() == ''){
             $('input[name="fromdate"]').focus();
             $("#alert_fromdate").text('Please select the date'); 
         }else{
			window.open("{{url('reports/excel-export-orders-report')}}"+"?fromdate="+$("#rpdate").val()+"&todate="+$("#date").val()+"&branch="+$("#branch").val()+"&terminal="+$("#terminal").val()+"&customer="+$("#customer").val()+"&paymentmode="+$("#paymentmode").val()+"&ordermode="+$("#ordermode").val()+"&type="+$("#type").val()+"&status="+$("#orderstatus").val()+"&receipt="+$("#receipt").val()+"&machineOrderNo="+$("#machine_order_no").val()+"&order_no="+$("#order_no").val()+"&report=excel"); 
		 }
	})
	
	$("#btnPdf").click(function(){
		if($('input[name="fromdate"]').val() == '' && $('input[name="todate"]').val() == ''){
             $('input[name="fromdate"]').focus();
             $("#alert_fromdate").text('Please select the date'); 
         }else{
			window.open("{{url('reports/excel-export-orders-report')}}"+"?fromdate="+$("#rpdate").val()+"&todate="+$("#date").val()+"&branch="+$("#branch").val()+"&terminal="+$("#terminal").val()+"&customer="+$("#customer").val()+"&paymentmode="+$("#paymentmode").val()+"&ordermode="+$("#ordermode").val()+"&type="+$("#type").val()+"&status="+$("#orderstatus").val()+"&receipt="+$("#receipt").val()+"&machineOrderNo="+$("#machine_order_no").val()+"&order_no="+$("#order_no").val()+"&report=pdf"); 
		 }
		
	})
	

</script>
@endsection
