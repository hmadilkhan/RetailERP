@extends('layouts.master-layout')

@section('title','Work Orders')

@section('breadcrumtitle','Create Work Orders')

@section('navworkorder','active')

@section('content')
    <section class="panels-wells">
        <div class="row">
            <div class="col-lg-7 col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-text"> Create Work Order</h5><label id="limit" class="form-control-label text-danger f-right f-24"></label>
                        <h5 class=""><a href="{{ url('/job-order') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>

                    </div>
                    <div class="card-block">
                        <input type="hidden" id="workorderid" name="workorderid" value="0">
                        <input type="hidden" id="update_id" name="update_id">
                        <input type="hidden" id="hidd_id" name="hidd_id">
                        <input type="hidden" id="limit_id" name="limit_id">
                        <input type="hidden" id="unit_cost" name="unit_cost">
                        <div class="row">


                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label">Enter Work Order Name:</label>
                                    <input type="text" name="workordername" id="workordername" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">


                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label class="form-control-label">Select Finished Good</label>
                                    <select class="select2" data-placeholder="Select Finished Good" id="finished" name="finished">
                                        <option value="">Select Finished Good</option>
                                        @if($products)
                                            @foreach($products as $value)
                                                <option value="{{$value->id}}">{{$value->department_name." | ".$value->item_code." | ".$value->product_name}}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                    <div class="form-control-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                                    <label class="form-control-label">Quantity</label>
                                    <input type="number" id="qty" name="qty" onchange="qty_change()" class="form-control" value="1" />
                                    @if ($errors->has('name'))
                                        <div class="form-control-feedback">Required field can not be blank.</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                                    <label class="form-control-label">Job Cost</label>
                                    <input readonly="readonly" type="text" id="cost" name="cost" class="form-control" />
                                    <div  class="form-control-feedback">
                                        <span class="text-info" id="jobcost_feedback">Unit Cost: </span>
                                    </div>

                                </div>
                            </div>
{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">--}}
{{--                                    <label class="form-control-label">Retail Price / Unit</label>--}}
{{--                                    <input type="number" id="rp" name="rp" class="form-control" onchange="calrp()"  />--}}
{{--                                    <div  class="form-control-feedback">--}}
{{--                                        <span class="text-info" id="rp_feedback"></span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}





                        </div>
                        <button type="button" id="btnFinalSubmit" class="btn btn-md btn-primary f-right"> <i class="icofont icofont-plus"> </i>Add to List</button>




                        <div class="row">

                            <table id="item_table" class="table table-striped" width="100%"  cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>







                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-text"> Work Order Details</h5>
                    </div>
                    <div class="card-block">

                        <table id="ordertable"  class="table table-striped" width="100%"  cellspacing="0">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty.</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                        <table id="" class="table table-responsive invoice-table invoice-total">
                            <tbody>
                            <input type="hidden" name="totalrp" id="totalrp">
                            <tr class="txt-info">
                                <th><h5>Total Job Cost :</h5></th>
                                <td><h5 id="totalcost">0.00</h5></td>
                            </tr>
                            </tbody>
                        </table>


                        <button type="button" id="btnsend" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="orderaccount()" >
                            <i class="icofont icofont-ui-check"> </i>Send to Production
                        </button>

                    </div>
                </div>
            </div>

        </div>

    </section>

    <!-- modals -->
    <div class="modal fade modal-flex" id="qty-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Quantity</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <center><label class="text-info" style="font-size: x-large;" id="itemname"></label></center>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Enter Quantity:</label>
                                <input type="text"  name="itemqty" id="itemqty" class="form-control" />
                                <input type="hidden" name="itemid" id="itemid" />
                                <input type="hidden" name="orderidmodal" id="orderidmodal" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_qty" class="btn btn-success waves-effect waves-light" onClick="updateitem()">Edit</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scriptcode_three')


    <script type="text/javascript">
        $(".select2").select2();


        $('#finished').change(function(){
            $.ajax({
                url:'{{ url("/get-job-id") }}',
                type:"POST",
                data:{_token : "{{csrf_token()}}",productId:$('#finished').val()},
                success:function(result){

                    getDetails($('#finished').val());
                    getCosting($('#finished').val())
                    getRecipyLimit();

                }
            });
        });





        function qty_change()
        {
            if ($('#qty').val() == "")
            {
                $('#qty').focus();
                swal("Error", "Please select qty first :)", "error");

            }
            else
            {
                var qty = parseFloat($('#qty').val());
                var price = parseFloat($('#amountperpeice').val());
                var total = qty * price;
                var total = Math.round(total * 100) / 100;
                $('#totalMasterCost').val(parseFloat(total));
                totalCost();
                qtychange();
            }
        }

        function totalCost()
        {
            var Cost = parseFloat($('#cost').val());
            var mastercost = parseFloat($('#totalMasterCost').val());
            var totalCost = Cost + mastercost;
            $('#totalCost').val(totalCost);
        }



        function getDetails(id){
            $.ajax({
                url : "{{url('/get-temp')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", id:id},
                dataType : 'json',
                success : function(result){
                    $("#item_table tbody").empty();
                    if(result.length > 0)
                    {
                        $.each(result, function( index, value ) {
                            $("#item_table tbody").append(
                                "<tr>" +
                                "<td>"+value.product_name +"</td>" +
                                "<td>"+value.usage_qty +"</td>" +
                                "<td>"+value.total_cost+"</td>" +
                                "</tr>"
                            );
                        });
                    }
                    else
                    {
                        $("#item_table tbody").append(
                            "<tr>" +
                            "<td ></td>" +
                            "<td ><label class='f-24'>No Data Found</label></td>" +
                            "<td ></td>" +
                            "</tr>"
                        );
                    }
                    totalCost();
                }
            });
        }





        function getCosting(id)
        {
            $.ajax({
                url : "{{url('/job-cost')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", id:id},
                dataType : 'json',
                success : function(result){
                    $('#cost').val(result);
                    $('#unit_cost').val(result);
                    $('#jobcost_feedback').html(+result);

                }
            });
        }





        $('#btnFinalSubmit').click(function(e){

            let limit = parseInt($('#limit_id').val());
            let qty = parseInt($('#qty').val());

            if($('#workordername').val() == "")
            {
                swal("Error", "Please enter work order name :)", "error");
            }
            else if ($('#finished').val() == "")
            {
                swal("Error", "Please Select Finished Good :)", "error");
            }
            else if($('#qty').val() == "" || $('#qty').val() == 0)
            {
                swal("Error", "Please select qty :)", "error");
            }
            else if($('#limit_id').val() == 0)
            {
                swal("Error", "You can not make this product. Please purchase raw materials of this product.", "error");
            }
            else if(qty > limit)
            {
                swal("Error", "You can only make "+parseInt($('#limit_id').val())+" qty:)", "error");
            }
            else if($('#rp').val() == "")
            {
                swal("Error", "Please enter retail price :)", "error");
            }

            else
            {
                $.ajax({
                    url : "{{url('/job-submit')}}",
                    type : "POST",
                    data : {_token : "{{csrf_token()}}",
                        workorderid:$('#workorderid').val(),
                        workordername:$('#workordername').val(),
                        product:$('#finished').val(),
                        qty:$('#qty').val(),
                        cost:$('#jobcost_feedback').html(),
                        // rp:$('#rp').val(),
                    },
                    dataType : 'json',
                    success : function(resp){
                        console.log(resp);
                        if(resp != 0){
                            swal({
                                title: "Operation Performed",
                                text: "Product Add Successfully!",
                                type: "success"});

                            $('#workorderid').val(resp);
                            ordertable(resp);
                            getsum(resp);
                        }
                        else{
                            swal({
                                title: "Already exsit",
                                text: "Particular Product Already exsit!",
                                type: "warning"
                            });
                        }
                    }
                });
            }
        });

        function getRecipyLimit()
        {
			console.log("Finished",$('#finished').val())
            $.ajax({
                url : "{{url('/recipy-limit')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", id:$('#finished').val()},
                success : function(result){
					console.log(result[0].totalQty);
                    $('#limit_id').val(result);
                    if(result != "")
                    {
                        $('#limit').html('');
                        $('#limit').html('You can make only '+parseInt(result[0].totalQty)+" "+result[0].uomname);
                    }
                    else
                    {
                        $('#limit').html('');
                        $('#limit').html('Recipy does not exists');
                    }
                }
            });
        }

        function qtychange()
        {
            var totalcost = parseFloat($('#qty').val()) * parseFloat($('#unit_cost').val());
            $('#cost').val(totalcost);
        }

        function swal_alert(title,msg,type,mode){
            swal({
                title: title,
                text: msg,
                type: type
            },function(isConfirm){
                if(isConfirm){
                    if(mode === true){
                        window.location = "{{url('/view-purchases')}}";
                    }
                }
            });
        }

        function calrp(){
            let rp =($('#qty').val() * $('#rp').val());
            $('#rp_feedback').html("Total RP: "+rp);

        }

        function ordertable(workorderid){
            $.ajax({
                url: "{{url('/getworkorder')}}",
                type:"GET",
                dataType:"json",
                data:{_token:"{{ csrf_token()}}",
                    workorderid:workorderid,
                },
                success:function(result){
                    console.log(result);
                    $("#ordertable tbody").empty();
                    for(var s=0;s < result.length ;s++){
                        $("#ordertable tbody").append(
                            "<tr>" +
                            "<td>"+result[s].product_name+"</td>" +
                            "<td>"+result[s].order_qty+"</td>" +
                            "<td>"+Math.round(result[s].order_qty * result[s].job_cost)+"</td>" +
                            "<td class='action-icon'><i onclick='edititem("+result[s].sub_id+","+result[s].job_order_id+","+result[s].order_qty+","+"\""+ result[s].product_name + "\")' class='icofont icofont-ui-edit text-primary' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i onclick='del_items("+result[s].sub_id+","+result[s].job_order_id+")' class='icofont icofont-ui-delete text-danger' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                            "</tr>"
                        );
                    }
                }
            });
        }

        function getsum(workorderid){
            $.ajax({
                url: "{{url('/getworkorder-sum')}}",
                type:"GET",
                dataType:"json",
                data:{_token:"{{ csrf_token()}}",
                    workorderid:workorderid,
                },
                success:function(result){

                    $('#totalcost').html(result[0].total_cost);
                    // $('#totalrp').val(result[0].total_rp );
                }
            });
        }

        function orderaccount(){

            $.ajax({
                url : "{{url('/workorder-account')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}",
                    workorderid:$('#workorderid').val(),
                    totalcost:$('#totalcost').html(),
                    // totalrp:$('#totalrp').val(),
                },
                dataType : 'json',
                success : function(resp){
                    console.log(resp);
                    if(resp){
                        swal({
                            title: "success",
                            text: "Work Order Created Successfully!",
                            type: "success"
                        },function(isConfirm){
                            if(isConfirm){
                                window.location= "{{ url('/job-order') }}";
                            }
                        });
                    }
                }
            });

        }

        function edititem(id,orderid,qty,name){
            $('#itemqty').val(qty);
            $('#itemid').val(id);
            $('#orderidmodal').val(orderid);
            $('#itemname').html(name);

            $("#qty-modal").modal("show");
        }

        function updateitem(){

            $.ajax({
                url: "{{url('/update-orderqty')}}",
                dataType:"json",
                type:"PUT",
                data:{_token:"{{ csrf_token()}}",
                    tableid:$('#itemid').val(),
                    qty:$('#itemqty').val(),
                },
                success:function(result){
                    swal({
                        title: "Operation Performed",
                        text: "Quantity Change Successfully!",
                        type: "success"});

                    ordertable($('#orderidmodal').val());
                    getsum($('#orderidmodal').val());
                    $("#qty-modal").modal("hide");
                }
            });

        }

        function del_items(id,workorderid){
            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "delete it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{url('/suborder-delete')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                tableid:id,
                                workorderid:workorderid,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Product Deleted Successfully!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            ordertable(workorderid);
                                            getsum(workorderid);
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
        }



    </script>

@endsection