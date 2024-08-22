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
                            <label class="form-control-label">Receipt No</label>
                            <input type='text' class="form-control" id="receipt" name="receipt" placeholder="Receipt No"/>
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>
                    <div id="from" class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">From Date</label>
                            <input type='text' class="form-control" id="fromdate" name="fromdate" placeholder="DD-MM-YYYY"/>
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>
                    <div id="to" class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">To Date</label>
                            <input type='text' class="form-control" id="todate" name="todate" placeholder="DD-MM-YYYY"/>
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

                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <label class="form-control-label">Select Branch</label>
                        <select id="branch" name="branch" data-placeholder="Select Branch" class="f-right select2">
                            <option value="">Select Branch</option>
                            @foreach($branch as $value)
                                <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
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
{{--                        <button type="button" id="orderpdf"  class="btn btn-danger waves-effect waves-light m-t-25 m-r-10 f-right"  >--}}
{{--                            <i class="icofont icofont-file-pdf" > </i>PDF--}}
{{--                        </button>--}}
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
                            <th>Branch</th>
                            <th>Receipt No</th>
                            <th>Customer</th>
                            <th>Delivery Date</th>
                            <th>Total Amount</th>
                            <th>Branch</th>
                             <th>Status</th>
                             <th>Action</th>

                        </tr>
                        </thead>
                        <tbody>
                            @foreach($totalorders as $value)
                                <tr class="{{($value->isSeen == 1 ? 'bg-warning' : '')}}">
                                    <td>{{$value->id}}</td>
                                    <td>{{$value->date}}</td>
                                    <td>{{$value->branch}}</td>
                                    <td>{{$value->receipt_no}}</td>
                                    <td>{{$value->name}}</td>
                                    <td>{{$value->delivery_date}}</td>
                                    <td>{{number_format($value->total_amount,2)}}</td>
                                    <td>
                                        <select id="branch{{$value->id}}" class="form-control select2" dataplaceholder="Select Branch" onchange="branchChange('branch{{$value->id}}','{{$value->id}}','{{$value->receipt_no}}')" >

                                            @foreach($branch as $val)
                                                <option {{($val->branch_name == $value->branch ? 'selected' : '')}} value="{{$val->branch_id}}">{{$val->branch_name}}</option>
                                            @endforeach

                                        </select>
                                    </td>
                                    <td>
                                        <select id="status{{$value->id}}" class="form-control select2" dataplaceholder="Select Status" onchange="statusChange('status{{$value->id}}','{{$value->id}}','{{$value->receipt_no}}')">

                                            @foreach($orders as $val)
                                                <option  {{($val->order_status_name == $value->order_status_name ? 'selected' : '')}} value="{{$val->order_status_id}}">{{$val->order_status_name}}</option>
                                            @endforeach

                                        </select>
                                    </td>
                                    <td class='action-icon'>
                                        <i onclick='getBill("{{$value->id}}","{{$value->receipt_no}}","{{$value->date}}","{{$value->name}}","{{$value->mobile}}","{{$value->order_mode}}","{{$value->order_status_name}}","{{$value->total_amount}}","{{$value->receive_amount}}","{{$value->payment_mode}}","{{$value->address}}")' class='icofont icofont-eye-alt text-info' data-toggle='tooltip' data-placement='top' title='' data-original-title='View'></i>&nbsp;
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
                            <label class="f-w-600">Address:</label>
                        </div>
                        <div class="col-md-9">
                            <label id="address" class="">Muhammad Adil Khan</label>
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

		
        $('#paymentmode').change(function(){
            trf_details($('#paymentmode').val());
        });

        $('#fetch').click(function(){
            window.location = "{{url('web-orders-filter')}}?first="+$('#fromdate').val()+"&second="+$('#todate').val()+"&customer="+$('#customer').val()+"&receipt="+$('#receipt').val()+"&branch="+$('#branch').val();
        });

        function branchChange(id,receipt,receiptNo){     
            $.ajax({
                url: "{{url('/change-order-branch')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",
                    receipt:receipt,
                    branch : $('#'+id).val()
                },
                success:function(result){
					orderSeen(receiptNo)
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
                url: "{{url('/change-order-status')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",
                    receipt:receipt,
                    status : status,
					rider:rider
                },
                success:function(result){
					orderSeen(receiptNo)
					$('#order-status-modal').modal("hide");
                    swal_alert("Success","Status changed successfully","success","false")
                }
            });
		}
		
		async function orderSeen(ReceiptNo){
			  $.ajax({
					url : "{{url('/order-seen')}}",
					type : "POST",
					data : {_token : "{{csrf_token()}}", receiptNo:ReceiptNo},
					dataType : 'json',
					success : function(result){
						
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


    </script>
@endsection