@extends('layouts.master-layout')

@section('title','Orders')

@section('breadcrumtitle','Orders Panel')

@section('navbranchoperation','active')
@section('navorder','active')

@section('content')
    <?php $id = ""; $t = ""; ?>
    <section class="panels-wells p-t-3">

    @if(Session::has('error'))
      <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Order Details</h5>
                <h5 class=""><a href="{{ url('web-orders-view') }}"><i class="text-primary text-center icofont icofont-arrow-left m-t-10 m-b-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list ">Back to list</i></a></h5>
{{--                <div class="row m-l-20">--}}
{{--                    <div class="rkmd-checkbox checkbox-rotate checkbox-ripple">--}}
{{--                        <label class="input-checkbox checkbox-primary">--}}
{{--                            <input type="checkbox" id="checkbox">--}}
{{--                            <span class="checkbox"></span>--}}
{{--                        </label>--}}
{{--                        <div class="captions">Search Delivery Orders </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Order ID</label>
                            <input type='text' class="form-control" id="receipt" name="receipt" value="{{ Request::has('receipt') ? Request::get('receipt') : '' }}" placeholder="Order Id"/>
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>
                    <div id="from" class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">From Date</label>
                            <input type='text' class="form-control" id="fromdate" name="fromdate" value="{{ Request::has('first') ? Request::get('first') : '' }}" placeholder="DD-MM-YYYY"/>
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>
                    <div id="to" class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">To Date</label>
                            <input type='text' class="form-control" id="todate" name="todate" value="{{ Request::has('second') ? Request::get('second') : '' }}" placeholder="DD-MM-YYYY"/>
                            <span class="help-block text-danger" id="dbox"></span>
                        </div>
                    </div>
                    <!--<div id="deliveryfrom" class="col-md-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="form-control-label">Delivery From Date</label>-->
                    <!--        <input type='text' class="form-control" id="del_from" name="rpdate" placeholder="DD-MM-YYYY"/>-->
                    <!--        <span class="help-block text-danger" id="rpbox"></span>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div id="deliveryto" class="col-md-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="form-control-label">Delivery To Date</label>-->
                    <!--        <input type='text' class="form-control" id="del_to" name="date" placeholder="DD-MM-YYYY"/>-->
                    <!--        <span class="help-block text-danger" id="dbox"></span>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <div class="col-md-3">
                        <label class="form-control-label">Select Customer</label>
                        <select id="customer" name="customer" data-placeholder="Select Customer" class="f-right select2">
                            <option value="">Select Customer</option>
                            @php $customer_parameter = Request::has('customer') ? Request::get('customer') : null  @endphp 
                            @foreach($customer as $value)
                                <option {{ $customer_parameter == $value->id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>




                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label class="form-control-label">Select Branch</label>
                        <select id="branch" name="branch" data-placeholder="Select Branch" class="f-right select2">
                            <option value="">Select Branch</option>
                           @php $branch_fltr = Request::has('branch') ? Request::get('branch') : null;  @endphp
                            @foreach($branch as $value)
                                <option {{ $branch_fltr == $value->branch_id ? 'selected' : '' }} value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                   @if($website != null) 
                    <div class="col-md-4">
                        <label class="form-control-label">Select Website</label>
                        <select id="website" name="website" data-placeholder="Select Website Name" class="f-right select2">
                                <option value="">Select Website</option>
                            @php $website_Id = isset($websiteId) ? $websiteId : null @endphp
                            @foreach($website as $value)
                                <option {{ $website_Id == $value->id ? 'selected' : '' }}  value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div> 
                   @endif    
                </div>

                <div class="row">

                    <div class="col-md-3 f-right">
                        <label class="form-control-label"></label>
                        <button type="button" id="fetch"  class="btn btn-success waves-effect waves-light m-t-25 m-r-10 f-right"  >
                            <i class="icofont icofont-ui-check"> </i>Fetch
                        </button>
{{--                        <button type="button" id="orderpdf"  class="btn btn-danger waves-effect waves-light m-t-25 m-r-10 f-right"  >--}}
{{--                            <i class="icofont icofont-file-pdf" > </i>PDF--}}
{{--                        </button>--}}
                    </div>
                </div>


            </div>
            <hr/>
            <div class="card-block">
               <audio id="orderSound" class="d-none" src="{{ asset('assets/sound/doorbell-sound.wav') }}"> </audio>     
                <div class="project-table">
                    <table id="order_table" class="table table-striped nowrap dt-responsive" width="100%">
                        <thead>
                        <tr>
                            <th class="d-none">#</th>
                            <th>Order#</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Branch</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Total Amount</th>
                            <!--<th>Branch</th>-->
                             <th>Status</th>
                             <th>Action</th>

                        </tr>
                        </thead>
                        <tbody>
                            @foreach($totalorders as $value)
                                <tr id="tbRow{{ $value->id }}" style="background-color:{{($value->isSeen == 1 ? '#efefef' : '')}}" >
                                    <td class="d-none">{{ $value->id }}</td>
                                    <td>{{$value->url_orderid}}</td>
                                    <td>{{ $value->date }}</td>
                                    <td>{{ /Carbon::parse($value->time )->format('h:i A') }}</td>
                                    <td>{{$value->branch}}</td>
                                    <td>{{$value->name}}</td>
                                    <td>{{$value->mobile}}</td>
                                    <td>{{number_format($value->total_amount,2)}}</td>
                                    <!--<td>-->
                                    <!--    <select id="branch{{--$value->id--}}" class="form-control select2" dataplaceholder="Select Branch" onchange="branchChange('branch{{--$value->id--}}','{{--$value->id--}}','{{--$value->receipt_no--}}')" >-->

                                    <!--        @foreach($branch as $val)-->
                                    <!--            <option {{--($val->branch_name == $value->branch ? 'selected' : '')--}} value="{{--$val->branch_id--}}">{{--$val->branch_name--}}</option>-->
                                    <!--        @endforeach-->

                                    <!--    </select>-->
                                    <!--</td>-->
                                    <td>
                                        <select id="status{{$value->id}}" class="form-control select2" dataplaceholder="Select Status" onchange="statusChange('status{{$value->id}}','{{$value->id}}','{{$value->receipt_no}}')">

                                            @foreach($orders as $val)
                                                <option  {{($val->order_status_name == $value->order_status_name ? 'selected' : '')}} value="{{$val->order_status_id}}">{{$val->order_status_name}}</option>
                                            @endforeach

                                        </select>
                                    </td>
                                    <td class='action-icon'>
                                        
                                        <input type="hidden" name="orderId{{ $value->id }}" value="{{$value->url_orderid}}">
                                        <input type="hidden" name="receiptId{{ $value->id }}" value="{{$value->receipt_no}}">
                                        <input type="hidden" name="receiptDate{{ $value->id }}" value="{{$value->date}}">
                                        <input type="hidden" name="custName{{ $value->id }}" value="{{$value->name}}">
                                        <input type="hidden" name="mobile{{ $value->id }}" value="{{$value->mobile}}">
                                        <input type="hidden" name="orderStatus{{ $value->id }}" value="{{$value->order_status_name}}">
                                        <input type="hidden" name="totalAmount{{ $value->id }}" value="{{$value->total_amount}}">
                                        <input type="hidden" name="address{{ $value->id }}" value="{{$value->address}}">
                                        <input type="hidden" name="landmark{{ $value->id }}" value="{{$value->landmark}}">
                                        <input type="hidden" name="deliveryCharge{{ $value->id }}" value="{{$value->delivery_charges}}">
                                        <input type="hidden" name="deliveryAreaName{{ $value->id }}" value="{{$value->delivery_area_name}}">
                                        <input type="hidden" name="deliveryType{{ $value->id }}" value="{{$value->delivery_type}}">
                                        
                                       <a href="{{ route('getWebsiteSaleReceiptDetails',$value->url_orderid) }}" class="m-r-1"><i class='icofont icofont-eye-alt {{($value->isSeen == 1 ? '' : 'text-primary')}}' data-toggle='tooltip' data-placement='top' title='' data-original-title='View'></i> </a>                                       
                                        
                                       <!--<i onclick='getBill("{{--$value->id--}}","{{--$value->receipt_no--}}","{{--$value->date--}}","{{--$value->name--}}","{{--$value->mobile--}}","{{--$value->order_mode--}}","{{--$value->order_status_name--}}","{{--$value->total_amount--}}","{{--$value->receive_amount--}}","{{--$value->payment_mode--}}","{{--$value->address--}}")' class='icofont icofont-eye-alt text-info' data-toggle='tooltip' data-placement='top' title='' data-original-title='View'></i>-->
                                        <!--&nbsp;-->
                                        &nbsp;<i onclick="showReceipt('{{$value->receipt_no}}')" class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Print Receipt'></i>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

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
                            <label class="f-w-600">Order Id :</label>
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
                            <label class="f-w-600">Delivery Area:</label>
                        </div>
                        <div class="col-md-9">
                            <label id="deliveryAreaName" class=""></label>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Delivery Type:</label>
                        </div>
                        <div class="col-md-9">
                            <label id="deliveryType" class=""></label>
                        </div>
                    </div>                                        
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Address:</label>
                        </div>
                        <div class="col-md-9">
                            <label id="address" class="">Muhammad Adil Khan</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Land Mark:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="landmark" class="">Land Mark</label>
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
                            <label class="f-w-600 f-left">Sub Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="subTotal" class="f-right">10000</label>
                        </div>

                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Delivery Charge :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="deliveryCharge" class="f-right">1000</label>
                        </div>

                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Total Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="totalAmount" class="f-right">10000</label>
                        </div>                        
                        <!--<div class="col-md-6">-->
                        <!--    <label class="f-w-600 f-left">Total Amount :</label>-->
                        <!--</div>-->
                        <!--<div class="col-md-6">-->
                        <!--    <label id="tamount" class="f-right">10000</label>-->
                        <!--</div>-->

                        <!--<div class="col-md-6">-->
                        <!--    <label class="f-w-600 f-left">Advance :</label>-->
                        <!--</div>-->
                        <!--<div class="col-md-6">-->
                        <!--    <label id="receive" class="f-right">1000</label>-->
                        <!--</div>-->

                        <!--<div class="col-md-6">-->
                        <!--    <label class="f-w-600 f-left">Bal. Amount :</label>-->
                        <!--</div>-->
                        <!--<div class="col-md-6">-->
                        <!--    <label id="bal" class="f-right">10000</label>-->
                        <!--</div>-->
                    </div>
                </div>
                <div class="modal-footer">
                    <!--           <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Add Product</button> -->
                </div>
            </div>
        </div>
    </div>
	
	 <div class="modal fade modal-flex" id="order-status-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="mod-title" class="modal-title">Select Rider</h4>
                </div>
                <div class="modal-body">
				<input type="hidden" id="modalreceipt" /> 
				<input type="hidden" id="modalreceiptno" />
				<input type="hidden" id="modalstatus" />
					 <select id="rider" class="form-control select2" dataplaceholder="Select Rider"  >
						@foreach($riders as $rider)
							<option  value="{{$rider->id}}">{{$rider->provider_name}}</option>
						@endforeach
					</select>
                </div>
                <div class="modal-footer">
                        <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();
        
      
        var Interval_checkOrder = setInterval(checkOrders, 10000);

        @if(Request::has('page_mode'))
           Interval_checkOrder = null;
        @endif       

        $(".btn_close_md").on('click',function(){
            Interval_checkOrder = setInterval(checkOrders, 10000);            
        })
     
		
        $('#paymentmode').change(function(){
            trf_details($('#paymentmode').val());
        });
        
        $('#fetch').click(function(){
            if($('#fromdate').val() != '' || $('#todate').val() != '' || $('#receipt').val() != '' || $('#branch').val() != '' || $('#customer').val() != '' || $('#website').val() != ''){
               window.location = "{{route('getWebOrderFilter')}}?first="+$('#fromdate').val()+"&second="+$('#todate').val()+"&customer="+$('#customer').val()+"&receipt="+$('#receipt').val()+"&branch="+$('#branch').val()+"&website="+$('#website').val()+"&page_mode=1";
            }
            
        });        

        // $('#fetch').click(function(){
   
            
        //     window.location = "{{url('web-orders-filter')}}?first="+$('#fromdate').val()+"&second="+$('#todate').val()+"&customer="+$('#customer').val()+"&receipt="+$('#receipt').val()+"&branch="+$('#branch').val();
        // });

        function branchChange(id,receipt,receiptNo){     
            $.ajax({
                url: "{{url('/change-order-branch')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",
                    receipt:receipt,
                    branch : $('#'+id).val()
                },
                success:function(result){
					orderSeen(receiptNo,receipt)
                    swal_alert("Success","Branch changed successfully","success","false")
                }
            });
        }


        function statusChange(id,receipt,receiptNo){ 

			// This is to check if the order status is dispatch i,e 6 than display modal to select rider
			if($('#'+id).val() == 6){ 
				$('#modalreceipt').val(receipt);
				$('#modalreceiptno').val(receiptNo);
				$('#modalstatus').val($('#'+id).val() );
				$('#order-status-modal').modal("show");
			}else{
				statusChangeFromDB(receipt,$('#'+id).val(),receiptNo,0)
			}
			
        }
		
		$("#btn_extra_item").click(function(){
			var receipt = $("#modalreceipt").val();
			var receiptno = $("#modalreceiptno").val();
			var status = $("#modalstatus").val();
			var rider = $("#rider").val();
			if(rider == ""){
				alert("Please select Rider");
			}else{ 
				console.log("Receipt",receipt)
				console.log("Receipt No",receiptno)
				console.log("status",status)
				console.log("rider",rider)
				statusChangeFromDB(receipt,status,receiptno,rider)
			}
		});
		
		function statusChangeFromDB(receipt,status,receiptNo,rider)
		{
			 $.ajax({
                url: "{{url('/sales/change-website-order-status')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",
                    id:receipt,
                    status : status
                },
                success:function(result){
                    console.log(result)
					orderSeen(receiptNo,receipt)
					$('#order-status-modal').modal("hide");
                    swal_alert("Success","Status changed successfully","success","false")
                }
            });
		}
		
		async function orderSeen(ReceiptNo,tbrowId){
			  $.ajax({
					url : "{{url('/order-seen')}}",
					type : "POST",
					data : {_token : "{{csrf_token()}}", receiptNo:ReceiptNo},
					dataType : 'json',
					success : function(resp){
						if(resp.status == true){
						       if($("#tbRow"+tbrowId).hasClass('bg-primary')){  
                                    $("#tbRow"+tbrowId).removeClass('bg-primary');
                               }
						}
					}
				});
		}

        function swal_alert(title,msg,type,mode) {

            swal({
                title: title,
                text: msg,
                type: type
            }, function (isConfirm) {
                if (isConfirm) {
                    if (mode === true) {
                        window.location = "{{url('/view-purchases')}}";
                    }
                }
            });
        }

        $('#fromdate,#todate').bootstrapMaterialDatePicker({
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
        // trf_details();
        function trf_details(id){

            $.ajax({
                url: "{{url('/get-web-orders')}}",
                type: 'POST',
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
                    branch:"{{session('branch')}}",
                    terminal:$('#terminal').val(),
                },
                success:function(result){
                    // console.log(result);
                    $("#order_table tbody").empty();
                    if(result != ""){


                        for(var count =0;count < result.length; count++){

                            $("#order_table tbody").append(
                                "<tr>" +
                                "<td>"+result[count].id+"</td>" +
                                "<td class='pro-name' >"+result[count].date+"</td>" +
                                "<td>"+result[count].branch+"</td>" +
                                "<td onclick='getBill("+result[count].id+",\""+ result[count].receipt_no+"\",\""+result[count].date+"\",\""+result[count].name+"\",\""+result[count].mobile+"\",\""+result[count].order_mode+"\",\""+result[count].order_status_name+"\","+result[count].total_amount+","+(result[count].receive_amount == "" ? 0 : result[count].receive_amount) +",\""+result[count].payment_mode+"\",\""+result[count].address+"\")'>"+result[count].receipt_no+"</td>" +
                                "<td>"+result[count].name+"</td>" +
                                "<td>"+result[count].delivery_date+"</td>" +
                                "<td>"+parseInt(result[count].total_amount).toLocaleString()+"</td>" +
                                "<td><select class='form-control'>"+
                                "<option>Select Value</option>"+
                                "</select></td>" +
                                ( result[count].status == 1 ?
                                        "<td><label class='tag tag-danger'>"+result[count].order_status_name +"</label></td>"
                                   :
                                        "<td><label class='tag tag-info'>"+result[count].order_status_name +"</label></td>"
                                ) +
                                "<td class='action-icon'>"+
                                (result[count].order_mode == "Take Away" ? '' : "<i onclick='ordermove("+result[count].id+")' class='icofont icofont-location-arrow text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>")+
                                "&nbsp;"+"<i onclick='getBill("+result[count].id+",\""+ result[count].receipt_no+"\",\""+result[count].date+"\",\""+result[count].name+"\",\""+result[count].mobile+"\",\""+result[count].order_mode+"\",\""+result[count].order_status_name+"\","+result[count].total_amount+","+(result[count].receive_amount == "" ? 0 : result[count].receive_amount) +",\""+result[count].payment_mode+"\",\""+result[count].address+"\")' class='icofont icofont-eye-alt text-info' data-toggle='tooltip' data-placement='top' title='' data-original-title='View'></i>&nbsp;<i onclick='trf_delete("+result[count].id+")' class='icofont icofont-ui-delete text-danger' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>"+
                                "&nbsp;<i onclick='showReceipt(\""+ result[count].receipt_no+"\")' class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Print Receipt'></i>"+
                                "</td>" +
                                "</tr>"
                            );
                        }


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

        function getBill(id,receiptno,date,custName,custMobile,type,status,tamount,receive,paymentMode,address)
        {
			console.log(address)
            $('#product-modal').modal("show");
            $('#receiptno').html(receiptno);
            $('#date').html(date);
            $('#name').html(custName);
            $('#mobile').html(custMobile);
            $('#type').html(type);
            $('#status').html(status);
			$('#address').html(address);


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
            window.location = "{{url('orders-report')}}?first="+$('#rpdate').val()+"&second="+$('#date').val()+"&status="+$('#orderstatus').val()+"customer="+$('#customer').val()+"&receipt="+$('#receipt').val();
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


        function showReceipt(ReceiptNo) {

            window.location = "{{url('print')}}"+"/"+ReceiptNo;
        }


       function getBill_website(id)
        {
            clearInterval(Interval_checkOrder);
            orderSeen($("input[name='receiptId"+id+"']").val(),id);
           
           if($("#tbRow"+id).hasClass('bg-primary')){  
                $("#tbRow"+id).removeClass('bg-primary');
           }
            
            $('#product-modal').modal("show");
            $('#receiptno').text($("input[name='orderId"+id+"']").val());
            $('#date').text($("input[name='receiptDate"+id+"']").val());
            $('#name').text($("input[name='custName"+id+"']").val());
            $('#mobile').text($("input[name='mobile"+id+"']").val());
           
            $('#status').text($("input[name='orderStatus"+id+"']").val());
            $('#address').text($("input[name='address"+id+"']").val());
            $('#landmark').text($("input[name='landmark"+id+"']").val());
            $('#deliveryCharge').text("Rs. "+numberWithCommas($("input[name='deliveryCharge"+id+"']").val()));
            $('#deliveryAreaName').text($("input[name='deliveryAreaName"+id+"']").val());
            $('#deliveryType').text($("input[name='deliveryType"+id+"']").val());

                var subTotal = parseInt($("input[name='totalAmount"+id+"']").val()) - parseInt($("input[name='deliveryCharge"+id+"']").val());
            $('#subTotal').text("Rs. "+numberWithCommas(subTotal));
            $('#totalAmount').text("Rs. "+numberWithCommas($("input[name='totalAmount"+id+"']").val()));
            

            $.ajax({
                url: "{{--route('getWebstieSaleReceiptDetails')--}}",
                type: 'GET',
                dataType:"json",
                async:true,
                data:{_token:"{{ csrf_token() }}",
                    id:id,
                },
                success:function(result){
                     console.log(result);
                    $("#tablemodal tbody").empty();
                    for(var count =0;count < result.products.length; count++){

                        var AddonArray = result.products[count].prod_addons;
                        var addonColmn = '';
                        
                        var dealArray = result.products[count].deal;
                        var dealColmn = '';                        

                          if(result.products[count].prod_variation.length == 0){
                              
                                if(dealArray.length != 0){
                                    for(var deal_count =0;deal_count < dealArray.length; deal_count++){
                                        
                                         dealColmn += '<br/><strong>'+dealArray[deal_count].name+':</strong>';
                                           for(var deal_vl_count =0;deal_vl_count < dealArray[deal_count].values.length; deal_vl_count++){
                                                   dealColmn += '<p>'+dealArray[deal_count].values[deal_vl_count].name+'</p>';
                                                   
                                              for(var dealAddon_count =0;dealAddon_count < dealArray[deal_count].values[deal_vl_count].addons.length; dealAddon_count++){
                                                   dealColmn += '<br/><strong>Addon</strong><br/><strong>'+dealArray[deal_count].values[deal_vl_count].addons[dealAddon_count].name+':</strong>'; 
                                                   for(var dealAddonVal_count =0;dealAddonVal_count < dealArray[deal_count].values[deal_vl_count].addons[dealAddon_count].values.length; dealAddonVal_count++){
                                                       var tmp = dealArray[deal_count].values[deal_vl_count].addons[dealAddon_count].values[dealAddonVal_count].name;
                                                   dealColmn += '<p>'+tmp+'</p>'; 
                                                 }
                                               }                                                   
                                                   
                                           }
                                      }
                                }                                
                                                              
                              
                                $("#tablemodal tbody").append(
                                    "<tr>" +
                                    "<td >"+result.products[count].product_name+"<br/>"+dealColmn+"</td>" +
                                    "<td >"+result.products[count].total_qty+"</td>" +
                                    "<td '>"+numberWithCommas(result.products[count].webcart_amount)+"</td>" +
                                    "</tr>"
                                ) 
                                                                                        
                            }else{
                                

                                
                                if(AddonArray.length != 0){
                                    for(var addon_count =0;addon_count < AddonArray.length; addon_count++){
                                        
                                         addonColmn += '<br/><strong>Addons</strong><br/><strong>'+AddonArray[addon_count].name+':</strong>';
                                           for(var addon_vl_count =0;addon_vl_count < AddonArray[addon_count].values.length; addon_vl_count++){
                                               var addonPrice = AddonArray[addon_count].values[addon_vl_count].price == 0 ? '' : AddonArray[addon_count].values[addon_vl_count].price;
                                                   addonColmn += '<p>'+AddonArray[addon_count].values[addon_vl_count].name+'</p>'; 
                                           }
                                      }
                                }      
                                
                                
                                for(var count2 =0;count2 < result.products[count].prod_variation.length; count2++){
                                    
                                     var VariationArray = result.products[count].prod_variation[count2].variation;
                                     var variatColmn = '';
                                        if(VariationArray.length > 0){
                                            for(var variat_count =0;variat_count < VariationArray.length; variat_count++){
                                                 variatColmn += '<br/><strong>'+VariationArray[variat_count].name+':</strong>';
                                                   for(var variat_vl_count =0;variat_vl_count < VariationArray[variat_count].values.length; variat_vl_count++){
                                                       var variatPrice = VariationArray[variat_count].values[variat_vl_count].price == 0 ? '' : VariationArray[variat_count].values[variat_vl_count].price;
                                                           variatColmn += '<p>'+VariationArray[variat_count].values[variat_vl_count].name+'</p>'; 
                                                   }
                                              }
                                        }                                      
                                            
                                        var row =   "<tr>" +
                                                    "<td >"+result.products[count].product_name+"<br/>"+result.products[count].prod_variation[count2].variate_name+"  "+variatColmn+addonColmn+"</td>" +
                                                    "<td >"+result.products[count].prod_variation[count2].total_qty+"</td>" +
                                                    "<td >"+numberWithCommas(result.products[count].webcart_amount)+"</td>" +
                                                    "</tr>";
                                        $("#tablemodal tbody").append(row); 
                               }
                            }
                            


                              
                    //         // for(var count2 =0;count2 < result.variatItem.length; count2++){
                    //         //     $("#tablemodal tbody").append(
                    //         //         "<tr>" +
                    //         //         "<td >"+result.variatItem[count2].item_name+"</td>" +
                    //         //         "<td >"+result.variatItem[count2].total_qty+"</td>" +
                    //         //         "<td '>"+parseInt(result.variatItem[count2].total_amount).toLocaleString()+"</td>" +
                    //         //         "</tr>"
                    //         //     )
                                
                    //         // }                        
                    }
                }
            });
         

        }

      function checkOrders(){
         
        $.ajax({
                url:'{{ route("checkwebsiteOrders") }}',
                type:'POST',
                data:{_token:'{{ csrf_token() }}'},
                dataType:'json',
                async:true,
                success:function(resp){
                   var sound = $("#orderSound");
                     if(resp.status){
                        // notify('You have a new order', 'success');
                        var orderLists = resp.orders;
                        var orderstatus = resp.orderStatus;
                        
                        for(var i =0;i < orderLists.length; i++){
                            if($("#tbRow"+orderLists[i].id).length == 0){
                                notify('You have a new order', 'success');
                                sound.get(0).play();
                                $("#order_table tbody").prepend(
                                 '<tr id="tbRow'+orderLists[i].id+'" class="bg-primary">'+
                                    '<td class="d-none">'+orderLists[i].id+'</td>'+
                                    '<td>'+orderLists[i].url_orderid+'</td>'+
                                    '<td>'+orderLists[i].date+'</td>'+
                                    '<td>'+orderLists[i].branch+'</td>'+
                                    '<td>'+orderLists[i].name+'</td>'+
                                    '<td>'+orderLists[i].mobile+'</td>'+
                                    '<td>'+numberWithCommas(orderLists[i].total_amount)+'</td>'+
                                    '<td><select id="status'+orderLists[i].id+'"  class="form-control select2" dataplaceholder="Select Status"></select></td>'+
                                    '<td class="action-icon">'+
                                        '<input type="hidden" name="orderId'+orderLists[i].id+'" value="'+orderLists[i].url_orderid+'">'+
                                        '<input type="hidden" name="receiptId'+orderLists[i].id+'" value="'+orderLists[i].receipt_no+'">'+
                                        '<input type="hidden" name="receiptDate'+orderLists[i].id+'" value="'+orderLists[i].date+'">'+
                                        '<input type="hidden" name="custName'+orderLists[i].id+'" value="'+orderLists[i].name+'">'+
                                        '<input type="hidden" name="mobile'+orderLists[i].id+'" value="'+orderLists[i].mobile+'">'+
                                        '<input type="hidden" name="orderStatus'+orderLists[i].id+'" value="'+orderLists[i].order_status_name+'">'+
                                        '<input type="hidden" name="totalAmount'+orderLists[i].id+'" value="'+orderLists[i].total_amount+'">'+
                                        '<input type="hidden" name="address'+orderLists[i].id+'" value="'+orderLists[i].address+'">'+
                                        '<input type="hidden" name="landmark'+orderLists[i].id+'" value="'+orderLists[i].landmark+'">'+
                                        '<input type="hidden" name="deliveryCharge'+orderLists[i].id+'" value="{'+orderLists[i].delivery_charges+'">'+
                                        '<input type="hidden" name="deliveryAreaName'+orderLists[i].id+'" value="'+orderLists[i].delivery_area_name+'">'+
                                        '<input type="hidden" name="deliveryType'+orderLists[i].id+'" value="'+orderLists[i].delivery_type+'">'+
                                        '<a href="'+location.origin+'/sales/website-order-detail/'+orderLists[i].url_orderid+'" class="m-r-1"><i class="icofont icofont-eye-alt"  data-toggle="tooltip" data-placement="top" title="" data-original-title="View"></i></a>'+
                                        '<i onclick="showReceipt(\''+orderLists[i].receipt_no+'\')" class="icofont icofont icofont-printer text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Receipt"></i>'
                                    +'</td></tr>');
                                       
                                       $("#status"+orderLists[i].id).attr("onchange","statusChange('status"+orderLists[i].id+"','"+orderLists[i].id+"','"+orderLists[i].receipt_no+"')");
                                       
                                       $("#status"+orderLists[i].id).select2();
                                       
                                    for(var c=0; c < orderstatus.length;c++){
                                         if(c == 0){
                                             $("#status"+orderLists[i].id).append("<option value=''>Select</option>");
                                         }
                                         
                                         if(orderstatus[c].order_status_name == orderLists[i].order_status_name){
                                             $("#status"+orderLists[i].id).append("<option selected value="+orderstatus[c].order_status_id+">"+orderstatus[c].order_status_name+"</option>");
                                         }else{
                                             $("#status"+orderLists[i].id).append("<option value="+orderstatus[c].order_status_id+">"+orderstatus[c].order_status_name+"</option>");
                                         }
                                        
                                    }
                            }
                        }
                        
                          //window.location="{{route('getWebOrderFilter')}}?first="+$('#fromdate').val()+"&second="+$('#todate').val()+"&customer="+$('#customer').val()+"&receipt="+$('#receipt').val()+"&branch="+$('#branch').val()+"&website="+$('#website').val()+"&page_mode=1";
                     
                         // window.location=location.origin+'/web-orders-view';
                         
                     }
                  }
                  
               })
        }
        
function numberWithCommas(number) {
    var parts = number.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}        
        

    </script>
@endsection