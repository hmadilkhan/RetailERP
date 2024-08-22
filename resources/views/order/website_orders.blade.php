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
                <h5 class=""><a href="{{ url('sales/website-orders-list') }}"><i class="text-primary text-center icofont icofont-arrow-left m-t-10 m-b-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list ">Back to list</i></a></h5>
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Order Number</label>
                            <input type='text' class="form-control" id="receipt" name="receipt" placeholder="Order Number" value="{{ Request::get('receipt') }}"/>
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>
                    <div id="from" class="col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">From Date</label>
                            <input type='text' class="form-control" id="fromdate" name="fromdate" placeholder="DD-MM-YYYY"/>
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>
                    <div id="to" class="col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">To Date</label>
                            <input type='text' class="form-control" id="todate" name="todate" placeholder="DD-MM-YYYY"/>
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
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-control-label">Select Customer</label>
                        <select id="customer" name="customer" data-placeholder="Select Customer" class="f-right select2">
                            <option value="">Select Customer</option>
                            @foreach($customer as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>                    
                    <div class="col-md-4">
                        <label class="form-control-label">Select Branch</label>
                        <select id="branch" name="branch" data-placeholder="Select Branch" class="f-right select2">
                            <option value="">Select Branch</option>
                            @foreach($branch as $value)
                                <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

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
                            <th class="d-none">#</th>
                            <th>#</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                             <th>Status</th>
                             <th>Action</th>

                        </tr>
                        </thead>
                        <tbody>
                            @foreach($totalorders as $value)
                                <tr class="{{($value->isSeen == 1 ? 'bg-primary' : '')}}">
                                    <td class="d-none">{{$value->id}}</th>
                                    <td>{{$value->url_orderid}}</td>
                                    <td>{{$value->date}}</td>
                                    <td>{{$value->branch}}</td>
                                    <td>{{$value->name}}</td>
                                    <td>{{number_format($value->total_amount,2)}}</td>

                                    <td>
                                        <select id="status{{$value->id}}" class="form-control select2" dataplaceholder="Select Status" onchange="statusChange('status{{$value->id}}','{{$value->id}}','{{$value->receipt_no}}')">

                                            @foreach($orders as $val)
                                                <option  {{($val->order_status_name == $value->order_status_name ? 'selected' : '')}} value="{{$val->order_status_id}}">{{$val->order_status_name}}</option>
                                            @endforeach

                                        </select>
                                    </td>
                                    <td class='action-icon'>
                                        <input type="hidden" name="orderId{{ $value->id }}" value="{{$value->url_orderid}}">
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

                                        <i onclick='getBill("{{$value->id}}")' class='icofont icofont-eye-alt  {{($value->isSeen == 1 ? 'text-white' : 'text-primary')}}' data-toggle='tooltip' data-placement='top' title='' data-original-title='View'></i>
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
        <div class="modal-dialog modal-lg" role="document"  style="max-width: 70%;">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header">
                    <button type="button" class="close btn_close_md" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="mod-title" class="modal-title">Order Details</h4>
                </div>
                <div class="modal-body">


                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Order No :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="orderId_md" class="">1234564897978</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Date :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="date_md" class="">2012-02-12</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Customer Name:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="name_md" class="">Muhammad Adil Khan</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Contact :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="mobile_md" class="">0311-1234567</label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Delivery Area:</label>
                        </div>
                        <div class="col-md-9">
                            <label id="deliveryAreaName_md" class=""></label>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Delivery Type:</label>
                        </div>
                        <div class="col-md-9">
                            <label id="deliveryType_md" class=""></label>
                        </div>
                    </div>                                        
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Address:</label>
                        </div>
                        <div class="col-md-9">
                            <label id="address_md" class="">Muhammad Adil Khan</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Land Mark:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="landmark_md" class="">Land Mark</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Status :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="status_md" class="">Pending</label>
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
                            <label id="subTotal_md" class="f-right">10000</label>
                        </div>

                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Delivery Charge :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="deliveryCharge_md" class="f-right">1000</label>
                        </div>

                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Total Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="totalAmount_md" class="f-right">10000</label>
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

        var OrCount = '{{ count($totalorders) }}';
        
        var myInterval = setInterval(checkOrders(), 5000);

        $(".btn_close_md").on('click',function(){
            myInterval = setInterval(checkOrders(), 5000);            
        })

        $(".select2").select2();

        
        $('#paymentmode').change(function(){
            trf_details($('#paymentmode').val());
        });

        $('#fetch').click(function(){
            window.location = "{{route('getWebsiteOrderFilter')}}?first="+$('#fromdate').val()+"&second="+$('#todate').val()+"&customer="+$('#customer').val()+"&receipt="+$('#receipt').val()+"&branch="+$('#branch').val()+"&website="+$('#website').val()+"&page_mode=1";
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


        function ordermove(id)
        {
            window.location = "{{url('order-assign')}}/"+id;
        }

        function getBill(id)
        {
            clearInterval(myInterval);
            $('#product-modal').modal("show");
            $('#orderId_md').text($("input[name='orderId"+id+"']").val());
            $('#date_md').text($("input[name='receiptDate"+id+"']").val());
            $('#name_md').text($("input[name='custName"+id+"']").val());
            $('#mobile_md').text($("input[name='mobile"+id+"']").val());
           
            $('#status_md').text($("input[name='orderStatus"+id+"']").val());
            $('#address_md').text($("input[name='address"+id+"']").val());
            $('#landmark_md').text($("input[name='landmark"+id+"']").val());
            $('#deliveryCharge_md').text($("input[name='deliveryCharge"+id+"']").val());
            $('#deliveryAreaName_md').text($("input[name='deliveryAreaName"+id+"']").val());
            $('#deliveryType_md').text($("input[name='deliveryType"+id+"']").val());

                var subTotal = parseInt($("input[name='totalAmount"+id+"']").val()) - parseInt($("input[name='deliveryCharge"+id+"']").val());
            $('#subTotal_md').text("Rs. "+subTotal);
            $('#totalAmount_md').text($("input[name='totalAmount"+id+"']").val());

            $.ajax({
                url: "{{route('getWebstieSaleReceiptDetails')}}",
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

                          if(result.products[count].prod_variation.length == 0){
                                $("#tablemodal tbody").append(
                                    "<tr>" +
                                    "<td >"+result.products[count].product_name+"</td>" +
                                    "<td >"+result.products[count].total_qty+"</td>" +
                                    "<td '>"+parseInt(result.products[count].total_amount).toLocaleString()+"</td>" +
                                    "</tr>"
                                ) 
                                                                                        
                            }else{
                                for(var count2 =0;count2 < result.products[count].prod_variation.length; count2++){

                                var row =   "<tr>" +
                                            "<td >"+result.products[count].product_name+"<br/>"+result.products[count].prod_variation[count2].variate_name+"</td>" +
                                            "<td >"+result.products[count].prod_variation[count2].total_qty+"</td>" +
                                            "<td >"+result.products[count].prod_variation[count2].total_amount+"</td>" +
                                            "</tr>";

                                $("#tablemodal tbody").append(row) 

                               }
                            }

                              if(result.products[count].prod_addons.length != 0){
                                for(var addon_count =0;addon_count < result.products[count].prod_addons.length; addon_count++){
                                    var price = result.products[count].prod_addons[addon_count].price;
                                        price = price == null ? '' : parseInt(price).toLocaleString();

                                    $("#tablemodal tbody").append(
                                        "<tr>" +
                                        "<td>  "+result.products[count].prod_addons[addon_count].name+"</td>" +
                                        "<td ></td>" +
                                        "<td '>"+price+"</td>" +
                                        "</tr>"
                                    ) 
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


        // function showReceipt(ReceiptNo) {

        //     window.location = "{{url('print')}}"+"/"+ReceiptNo;
        // }
 
      function checkOrders(){

        $.ajax({
                url:'{{ route("checkwebsiteOrders") }}',
                type:'GET',
                dataType:'json',
                async:true,
                success:function(resp){

                     if(resp){
                         window.location=location.origin+'/Retail/sales/website-orders-list';
                     }
                }
                  
               })
        }

  // setInterval(function() {

  //   checkOrders()
  // }, 1000);



    </script>
@endsection