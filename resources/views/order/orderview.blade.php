@extends('layouts.master-layout')

@section('title','Orders')

@section('breadcrumtitle','Orders Panel')

@section('navbranchoperation','active')
@section('navorder','active')

@section('content')

<?php $id = ""; $t = ""; ?>
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
            <div id="order#" class="col-md-3">
              <div class="form-group">
                    <label class="form-control-label">Order#</label>
                        <input type='text' class="form-control" id="order_no" name="order_no" placeholder="Order No"/>  
                        <span class="help-block text-danger" id="order#"></span>  
                    </div>
            </div>
           <div class="col-md-3">
             <div class="form-group">
                    <label class="form-control-label">Receipt No</label>
                            <input type='text' class="form-control" id="receipt" name="receipt" placeholder="Receipt No"/>  
                            <span class="help-block text-danger" id="rpbox"></span>  
                    </div>
           </div>
           <div id="from" class="col-md-3">
             <div class="form-group">
                    <label class="form-control-label">From Date</label>
                            <input type='text' class="form-control" id="rpdate" name="rpdate" placeholder="DD-MM-YYYY"/>  
                            <span class="help-block text-danger" id="rpbox"></span>  
                    </div>
           </div>
            <div id="to" class="col-md-3">
              <div class="form-group">
                    <label class="form-control-label">To Date</label>
                        <input type='text' class="form-control" id="date" name="date" placeholder="DD-MM-YYYY"/>  
                        <span class="help-block text-danger" id="dbox"></span>  
                    </div>
            </div>
            <div id="deliveryfrom" class="col-md-3">
             <div class="form-group">
                    <label class="form-control-label">Delivery From Date</label>
                            <input type='text' class="form-control" id="del_from" name="rpdate" placeholder="DD-MM-YYYY"/>  
                            <span class="help-block text-danger" id="rpbox"></span>  
                    </div>
           </div>
            <div id="deliveryto" class="col-md-3">
              <div class="form-group">
                    <label class="form-control-label">Delivery To Date</label>
                        <input type='text' class="form-control" id="del_to" name="date" placeholder="DD-MM-YYYY"/>  
                        <span class="help-block text-danger" id="dbox"></span>  
                    </div>
            </div>
         </div>

         <div class="row">

{{--             <div class="col-md-3">--}}
{{--                 <label class="form-control-label">Select Order Status</label>--}}
{{--                 <select id="orderstatus" name="orderstatus" class="f-right select2" data-placeholder="Select Order Status">--}}
{{--                     <option value="">Select Order Status</option>--}}
{{--                     @foreach($orders as $value)--}}
{{--                         <option value="{{ $value->order_status_id }}">{{ $value->order_status_name }}</option>--}}
{{--                     @endforeach--}}
{{--                 </select>--}}
{{--             </div>--}}
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
                 <label class="form-control-label">Select Branch</label>
                 <select id="branch" name="branch" data-placeholder="Select Branch" class="f-right select2">
                     <option value="">Select Branch</option>
                     @foreach($branch as $value)
                         <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                     @endforeach
                 </select>
             </div>

             <div class="col-md-3">
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
         </div>

         <div class="row">

             <div class="col-md-3 f-right">
                 <label class="form-control-label"></label>
                 <button type="button" id="fetch"  class="btn btn-success waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-ui-check"> </i>Fetch
                 </button>
                 <button type="button" id="orderpdf"  class="btn btn-danger waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-file-pdf" > </i>PDF
                 </button>
             </div>
         </div>

         
         </div>
         <hr/>      
       <div class="card-block">
    
           <div class="project-table">
                 <table id="order_table" class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>
              <th>Order#</th>
               <th>Date</th>
               <th>Time</th>
               <th>Branch</th>
			    <!--<th>Fbr No.</th>-->
               <th>Terminal</th>
               <th>Receipt No</th>
               <th>Customer</th>
               <th>OrderType</th>
               <th>Payment</th>
               <th>Amount</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <!-- @if($orders)
                @foreach($orders as $order)
                    <tr>
                        <td>{{$order->id}}</td>
                        <td>{{$order->date}}</td>
                        <td>{{$order->time}}</td>
                        <td>{{$order->branch_name}}</td>
                        <td>{{$order->terminal_name}}</td>
                        <td>{{$order->receipt_no}}</td>
                        <td>{{$order->name}}</td>
                        <td>{{$order->order_mode}}</td>
                        <td>{{$order->payment_mode}}</td>
                        <td>{{$order->total_amount}}</td>
                        <td>
                        <i onclick='showReceipt("{{$order->receipt_no}}")' class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>
                        </td>
                    </tr>
                @endforeach
                
            @endif
             -->
         </tbody>
     </table>
     <!-- {{$orders->links()}} -->
        </div>
    </div>
   </div>
</section>
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

<div class="modal fade modal-flex" id="product-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 id="mod-title" class="modal-title">Receipt Details</h4>
                                 </div>
                                 <div class="modal-body">
                                  

                                 	<div class="row">
						         		<div class="col-md-3">
						         			<label class="f-w-600">Receipt No :</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label id="receiptno" class="">1234564897978</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label class="f-w-600 f-right">Date :</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label id="date" class="">2012-02-12</label>
						         		</div>
						         	</div>
						         	<div class="row">
						         		<div class="col-md-3">
						         			<label class="f-w-600">Customer Name:</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label id="name" class="">Muhammad Adil Khan</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label class="f-w-600 f-right">Contact :</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label id="mobile" class="">0311-1234567</label>
						         		</div>
						         	</div>	
						         	<div class="row">
						         		<div class="col-md-3">
						         			<label class="f-w-600">Order Type:</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label id="type" class="">Take Away</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label class="f-w-600 f-right">Status :</label>
						         		</div>
						         		<div class="col-md-3">
						         			<label id="status" class="">Pending</label>
						         		</div>
						         	</div>	
						           <div class="row">
						             <div class="col-md-12">
						               <table id="tablemodal" class="table " width="100%"  cellspacing="0">
						                  <thead>
						                    <th width="80%">Product Name</th>
						                    <th>Qty</th>
						                    <th>Amount</th>

						                  </thead>
						                  <tbody>
						                    
						                  </tbody>
						               </table>
						              </div>
						                 
						              </div>   

						               <hr/>
						         <div class="row">
						         		<div class="col-md-6">
						         			<label class="f-w-600 f-left">Total Amount :</label>
						         		</div>
						         		<div class="col-md-6">
						         			<label id="tamount" class="f-right">10000</label>
						         		</div>

						         		<div class="col-md-6">
						         			<label class="f-w-600 f-left">Advance :</label>
						         		</div>
						         		<div class="col-md-6">
						         			<label id="receive" class="f-right">1000</label>
						         		</div>

						         		<div class="col-md-6">
						         			<label class="f-w-600 f-left">Bal. Amount :</label>
						         		</div>
						         		<div class="col-md-6">
						         			<label id="bal" class="f-right">10000</label>
						         		</div>
						         	</div>
                                 </div>
                                 <div class="modal-footer">
                          <!--           <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Add Product</button> -->
                                 </div>
                              </div>
                           </div>
                        </div> 


@endsection
@section('scriptcode_three')
<script type="text/javascript">
  $(".select2").select2();
  // $('#orderstatus').change(function(){
  //    trf_details($('#orderstatus').val());
  // });
  $('#paymentmode').change(function(){
      trf_details($('#paymentmode').val());
  });
  $('#fetch').click(function(){
      page = 1;
      getPOSFilters(page)
  });

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
  var page = 1;
  var count = 1;
  $(window).scroll(function() {
      page++;
      if ($('#date').val() != "" || $('#rpdate').val() != "" || $('#paymentmode').val() != "" || $('#customer').val() != "" || $('#receipt').val() != "" || $('#ordermode').val() != ""|| $('#branch').val() != "" || $('#terminal').val() != "") {
          getPOSFilters(page)
      }else{
          trf_details(page);
      }

  });
trf_details(page);
 function trf_details(page){

            $.ajax({
            url: "{{url('get-pos-orders')}}"+ "?page="+page,
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            // status:$('#orderstatus').val(),
            payMode:$('#paymentmode').val(),
            first:$('#rpdate').val(),
            second:$('#date').val(),
            customer:$('#customer').val(),
            receipt:$('#receipt').val(),
            mode:$('#ordermode').val(),
            deli_from:$('#del_from').val(),
            deli_to:$('#del_to').val(),
            branch:$('#branch').val(),
            terminal:$('#terminal').val(),
          	},
            success:function(result){
                if(page == 1){
                    $("#order_table tbody").empty();
                }
                if(result != ""){
                    $.each(result.data, function( index, value ) {
                        $("#order_table tbody").append(
                            "<tr style='background-color:"+(value.isSeen ==  1 ? '#fad7a0' : '' )+"' class=' "+(value.isSeen ==  1 ? 'bg-indigo' : '' )+"'>" +
                            "<td>"+value.id+"</td>" +
                            "<td class='pro-name' >"+value.date+"</td>" +
                            "<td class='pro-name' >"+getTimeAmPm(value.time)+"</td>" +
                            "<td>"+value.branch_name+"</td>" +
                            "<td>"+value.terminal_name+"</td>" +
                            "<td >"+value.receipt_no+"</td>" +
                            "<td>"+value.name+"</td>" +
                            "<td>"+value.order_mode+"</td>" +
                            (value.payment_mode == "Customer Credit" ?
                                    "<td><label class='tag tag-danger'>"+value.payment_mode +"</label></td>"
                                    :
                                    "<td><label class='tag tag-success'>"+value.payment_mode +"</label></td>"
                            )+
                            "<td>"+parseInt(value.total_amount).toLocaleString()+"</td>" +
                            "<td class='action-icon'>"+
                            "&nbsp;<i onclick='showReceipt(\""+ value.receipt_no+"\")' class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>"+
							"&nbsp;<i onclick='assignToServiceProviderModal(\""+ value.id+"\")' class='icofont icofont icofont-business-man #3A6EFF' data-toggle='tooltip' data-placement='top' title='' data-original-title='Assign To Service Provider'></i>"+
                            "</td>" +
                            "</tr>"
                        );
                    });
                   // for(var count =0;count < result.data.length; count++){
                   //      console.log(result.data)
                   //      $("#order_table tbody").append(
                   //        "<tr>" +
                   //        "<td>"+result.data[count].id+"</td>" +
                   //          "<td class='pro-name' >"+result[count].date+"</td>" +
                   //          "<td>"+result[count].branch+"</td>" +
                   //          "<td>"+result[count].terminal_name+"</td>" +
                   //          "<td onclick='getBill("+result[count].id+",\""+ result[count].receipt_no+"\",\""+result[count].date+"\",\""+result[count].name+"\",\""+result[count].mobile+"\",\""+result[count].order_mode+"\",\""+result[count].order_status_name+"\","+result[count].total_amount+","+(result[count].receive_amount == "" ? 0 : result[count].receive_amount) +",\""+result[count].payment_mode+"\")'>"+result[count].receipt_no+"</td>" +
                   //          "<td>"+result[count].name+"</td>" +
                   //          "<td>"+result[count].order_mode+"</td>" +
                   //          (result[count].payment_mode == "Customer Credit" ?
                   //                  "<td><label class='tag tag-danger'>"+result[count].payment_mode +"</label></td>"
                   //                  :
                   //                  "<td><label class='tag tag-success'>"+result[count].payment_mode +"</label></td>"
                   //          )+
                   //          "<td>"+result[count].delivery_date+"</td>" +
                   //          "<td>"+parseInt(result[count].total_amount).toLocaleString()+"</td>" +
                   //          // ( result[count].status == 1 ?
                   //          //         "<td><label class='tag tag-danger'>"+result[count].order_status_name +"</label></td>"
                   //          //    :
                   //          //         "<td><label class='tag tag-info'>"+result[count].order_status_name +"</label></td>"
                   //          // ) +
                   //
                   //          "<td class='action-icon'>"+
                   //          (result[count].order_mode == "Take Away" ? '' : "<i onclick='ordermove("+result[count].id+")' class='icofont icofont-location-arrow text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>")+
                   //          "&nbsp;"+
                   //          "<i onclick='getBill("+result[count].id+",\""+ result[count].receipt_no+"\",\""+result[count].date+"\",\""+result[count].name+"\",\""+result[count].mobile+"\",\""+result[count].order_mode+"\",\""+result[count].order_status_name+"\","+result[count].total_amount+","+(result[count].receive_amount == "" ? 0 : result[count].receive_amount) +",\""+result[count].payment_mode+"\")' class='icofont icofont-eye-alt text-info' data-toggle='tooltip' data-placement='top' title='' data-original-title='View'></i>&nbsp;<i onclick='trf_delete("+result[count].id+")' class='icofont icofont-ui-delete text-danger' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>"+
                   //          "&nbsp;<i onclick='showReceipt(\""+ result[count].receipt_no+"\")' class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>"+
                   //
                   //          "</td>" +
                   //        "</tr>"
                   //       );
                   //  }


                  }else{
                    $("#order_table tbody").append(
                    "<tr>" +
                        "<td colspan='11' class='text-center'>No result Found</td>" +
                    "</tr>"
                    );
                }
             }
          }); 
     }

     function getTimeAmPm(time) {
         var timeString = time;
         var hourEnd = timeString.indexOf(":");
         var H = +timeString.substr(0, hourEnd);
         var h = H % 12 || 12;
         var ampm = H < 12 ? "AM" : "PM";
         timeString = h + timeString.substr(hourEnd, 3) + ampm;
         return timeString;
     }

  function getPOSFilters(page){

      $.ajax({
          url: "{{url('get-pos-filter-orders')}}"+ "?page="+page,
          type: 'GET',
          dataType:"json",
          data:{_token:"{{ csrf_token() }}",
              // status:$('#orderstatus').val(),
              payMode:$('#paymentmode').val(),
              first:$('#rpdate').val(),
              second:$('#date').val(),
              customer:$('#customer').val(),
              receipt:$('#receipt').val(),
              mode:$('#ordermode').val(),
              deli_from:$('#del_from').val(),
              deli_to:$('#del_to').val(),
              branch:$('#branch').val(),
              terminal:$('#terminal').val(),
              order_no:$('#order_no').val(),
          },
          success:function(result){

              if(page == 1){
                  $("#order_table tbody").empty();
              }
              if(result != ""){
                  $.each(result.data, function( index, value ) {
                      $("#order_table tbody").append(
                          "<tr class=' "+(value.isSeen ==  1 ? 'bg-indigo' : '' )+"'>" +
                          "<td>"+value.id+"</td>" +
                          "<td class='pro-name' >"+value.date+"</td>" +
                          "<td class='pro-name' >"+getTimeAmPm(value.time)+"</td>" +
                          "<td>"+value.branch_name+"</td>" +
                          "<td>"+value.terminal_name+"</td>" +
                          "<td >"+value.receipt_no+"</td>" +
                          "<td>"+value.name+"</td>" +
                          "<td>"+value.order_mode+"</td>" +
                          (value.payment_mode == "Customer Credit" ?
                                  "<td><label class='tag tag-danger'>"+value.payment_mode +"</label></td>"
                                  :
                                  "<td><label class='tag tag-success'>"+value.payment_mode +"</label></td>"
                          )+
                          "<td>"+parseInt(value.total_amount).toLocaleString()+"</td>" +
                          "<td class='action-icon'>"+
                          "&nbsp;<i onclick='showReceipt(\""+ value.receipt_no+"\")' class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>"+
						  "&nbsp;<i onclick='assignToServiceProviderModal(\""+ value.id+"\")' class='icofont icofont icofont-business-man #3A6EFF' data-toggle='tooltip' data-placement='top' title='' data-original-title='Assign To Service Provider'></i>"+
                          "</td>" +
                          "</tr>"
                      );
                  });
              }else{
                  $("#order_table tbody").append(
                      "<tr>" +
                      "<td colspan='11' class='text-center'>No result Found</td>" +
                      "</tr>"
                  );
              }
          }
      });
  }

     function ordermove(id)
     {
        window.location = "{{url('order-assign')}}/"+id;
     }
     function getBill(id,receiptno,date,custName,custMobile,type,status,tamount,receive,paymentMode)
     {
        // alert(id);
        $('#product-modal').modal("show");
        $('#receiptno').html(receiptno);
        $('#date').html(date);
        $('#name').html(custName);
        $('#mobile').html(custMobile);
        $('#type').html(type);
        $('#status').html(status);


        if(paymentMode == "Cash" || paymentMode == "Credit Card")
        {
            $('#tamount').html("Rs. "+tamount.toLocaleString());
            $('#receive').html('0');
            var balance = parseInt(tamount) - parseInt(receive);
            $('#bal').html('0');


        }else{
            $('#tamount').html("Rs. "+tamount.toLocaleString());
            $('#receive').html("Rs. "+receive.toLocaleString());
            var balance = parseInt(tamount) - parseInt(receive);
            $('#bal').html("Rs. "+balance.toLocaleString());
        }
        
        

        $.ajax({
            url: "{{url('/get-items-by-receipt')}}",
            type: 'POST',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            id:id,
          	},
            success:function(result){
            	$("#tablemodal tbody").empty();
            	for(var count =0;count < result.length; count++){
					$("#tablemodal tbody").append(
						"<tr>" +
                            "<td >"+result[count].product_name+"</td>" +  
                            "<td >"+result[count].total_qty+"</td>" +  
                            "<td '>"+parseInt(result[count].total_amount).toLocaleString()+"</td>" + 
                          "</tr>"
					)
				}
            }
        });

     }

  $('#orderpdf').click(function(){
    window.location = "{{url('orders-report')}}?first="+$('#rpdate').val()+"&second="+$('#date').val()+"&status="+$('#orderstatus').val()+"customer="+$('#customer').val()+"&receipt="+$('#receipt').val()+"&mode="+$('#ordermode').val()+"&delFrom="+$('#del_from').val()+"&delTo="+$('#del_to').val()+"&branch="+$('#branch').val()+"&terminal="+$('#terminal').val()+"&payMode="+$('#paymentmode').val();
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

    $('#deliveryfrom').css("display","none");
    $('#deliveryto').css("display","none");

    $('#branch').change(function (e) {
        $.ajax({
            url : "{{url('/get-terminal')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}", branch:$('#branch').val()},
            dataType : 'json',
            success : function(result){
                $("#terminal").empty();
                $.each(result, function( index, value ) {
                    $("#terminal").append(
                        "<option value='"+value.terminal_id+"'>"+ value.terminal_name+ "</option>"
                    );
                });
            }
        });
    })

  function showReceipt(ReceiptNo) {
		
		orderSeen(ReceiptNo);
        // window.location = "{{url('print')}}"+"/"+ReceiptNo;
  }
  
  async function orderSeen(ReceiptNo){
	  $.ajax({
            url : "{{url('/order-seen')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}", receiptNo:ReceiptNo},
            dataType : 'json',
            success : function(result){
                if(result != ""){
					window.location = "{{url('print')}}"+"/"+ReceiptNo;
				}
            }
        });
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
                console.log(result);
				if(result.status == 200){
					$('#sp-modal').modal("hide");
					location.reload();
				}
            }
        });
	  }
  })
  
  
  // $('#order_table').DataTable({
  //
  //     bLengthChange: true,
  //     displayLength: 50,
  //     info: true,
  //     language: {
  //         search:'',
  //         searchPlaceholder: 'Search Customer',
  //         lengthMenu: '<span></span> _MENU_'
  //
  //     }
  //
  // });
    
 </script>
@endsection
<!-- "&nbsp;<i onclick='assignToServiceProviderModal(\""+ value.id+"\")' class='icofont icofont icofont-business-man #3A6EFF' data-toggle='tooltip' data-placement='top' title='' data-original-title='Assign To Service Provider'></i>"+ -->