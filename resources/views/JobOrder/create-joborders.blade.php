@extends('layouts.master-layout')

@section('title','Job Order')

@section('breadcrumtitle','Create Job Orders')

@section('navjoborder','active')

@section('content')
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text"> Create Job Order</h5>
                <h6 class=""><a href="{{ url('/joborder') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
            </div>
            <div class="card-block">
                    <input type="hidden" id="update_id" name="update_id">
                    <input type="hidden" id="hidd_id" name="hidd_id">
                <input type="hidden" id="productmode" name="productmode">

                    <div class="row">
                        <div class="col-lg-12">
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


                    </div>

            </div>

        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text"> Select Inventory</h5>

            </div>
            <div class="card-block">
                <div class="row">
                    <!-- product select box -->
                    <div class="col-lg-4  col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Select Product</label>
                            <select class="select2 form-control" data-placeholder="Select Product" id="product"  name="product" onchange="getuom()">
                                <option value="">Select Product</option>
                                @if($raw)
                                    @foreach($raw as $value)
                                        <option value="{{$value->id}}">{{$value->department_name." | ".$value->item_code." | ".$value->product_name}}</option>
                                    @endforeach
                                @endif

                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-lg-2  col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Unit of Measure</label>
                            <input type="text" readonly="true"  name="uom" placeholder="kg" id="uom" class="form-control" />
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <!-- Amount box -->
                    <div class="col-lg-2  col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Rate</label>
                            <input type="text"  name="rate" placeholder="0" id="rate" class="form-control" />
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-lg-2  col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Weight | Quantity</label>
                            <input type="text" readonly="true"  name="weight" placeholder="0" id="weight" class="form-control" />
                            <span class="help-block"></span>
                        </div>
                    </div>
					<div class="col-lg-2  col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Used in DineIn</label>
                            <select class="select2 form-control" data-placeholder="Select DineIn" id="dinein"  name="dinein">
                                <option value="">Select DineIn</option>
								<option value="1">YES</option>
								<option value="0">NO</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6  col-sm-12">
                    </div>
                    <!-- qty select box -->
                    <div class="col-lg-2  col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Packet Quantity</label>
                            <input type="Number" min="0" placeholder="0" name="itemqty" id="itemqty" class="form-control" onchange="qty_change()"  />
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <!-- price select box -->
                    <div class="col-lg-2  col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Cost</label>
                            <input type="text" readonly="true"  placeholder="0" name="cost" id="cost" class="form-control"  />
                            <span class="help-block"></span>
                        </div>
                    </div>


                    <!-- button  -->
                    <div class="col-lg-1  col-sm-12">
                        <div class="form-group">
                            <button type="button" id="btnSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25">
                                <i class="icofont icofont-plus"> </i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table id="item_table" class="table invoice-detail-table" width="100%"  cellspacing="0">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text"> Costing Calculations</h5>

            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                            <label class="form-control-label">Ingredients Cost</label>
                            <input type="Number" readonly="true" min="0" value="0" id="ic" name="ic" class="form-control" />
                            @if ($errors->has('name'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                            <label class="form-control-label">Packing Cost</label>
                            <input type="Number" min="0" value="0" id="pc" name="pc" class="form-control" onchange="getInfraCost()" />
                            @if ($errors->has('name'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                            <label class="form-control-label">Infra-Structure Cost</label>
                            <input type="Number" min="0" id="infra" name="infra" value="0" class="form-control" onchange="getInfraCost()" />
                            @if ($errors->has('name'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                            <label class="form-control-label">Total Cost</label>
                            <input readonly="readonly" type="Number" value="0" min="0" id="totalCost" name="totalCost" class="form-control" />
                            @if ($errors->has('name'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>


                </div>
                <button type="button" id="btnFinalSubmit" class="btn btn-md btn-success f-right"> <i class="icofont icofont-check f-18"> </i>Submit Job Order</button>

            </div>
        </div>
    </section>
@endsection


@section('scriptcode_three')


    <script type="text/javascript">
        $(".select2").select2();



        var mode = "insert";
        var count = 0;

        $('#finished').change(function(){
            $.ajax({
                url:'{{ url("/chk-recipy-exists") }}',
                type:"POST",
                data:{_token : "{{csrf_token()}}",id:$('#finished').val()},
                success:function(result){
                    if(result > 0)
                    {
                        // swal_alert("Error Message !","Recipy Already Exists","error",true);
                        swal({
                                title: "Recipy Already Exists!!",
                                text: "Do you want to Make it Again??!",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "yes plx!",
                                cancelButtonText: "cancel plx!",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            },
                            function(isConfirm){
                                if(isConfirm){
                                    $.ajax({
                                        url: "{{url('/createagain-joborder')}}",
                                        type: 'GET',
                                        data:{_token:"{{ csrf_token() }}",
                                            id:$('#finished').val(),
                                        },
                                        success:function(resp){
                                            if(resp){
                                                $('#hidd_id').val(resp[0].recipy_id);
                                                // getDetails();
                                                // getCosting();
                                                // swal("Success", "Old Recipy Deleted Successfully!!", "success");
                                                $.ajax({
                                                    url: "{{url('/inactiveoldecipy')}}",
                                                    type: 'POST',
                                                    data:{_token:"{{ csrf_token() }}",
                                                        recipyid:$('#hidd_id').val(),
                                                    },
                                                    success:function(resp){
                                                        if(resp){
                                                            swal("Success", "Old Recipy Deleted Successfully!!", "success");
                                                            $('#hidd_id').val('');
                                                        }

                                                    }
                                                });
                                            }

                                        }
                                    });
                                }else {
                                    swal("Cancelled", "Operation Cancelled :)", "error");
                                }
                            });
                    }
                }
            });
        });



        $('#raw').change(function(){
            if($('#raw').val() != "")
            {
                $.ajax({
                    url:'{{ url("/get-raw-materials") }}',
                    type:"POST",
                    data:{_token : "{{csrf_token()}}",id:$('#raw').val()},
                    success:function(result){
                        $('#uom').val('').change();
                        $('#uom').val(result[0].uom_id).change();
                        $('#amount').val(result[0].cost_price);

                    }
                });
            }

        });

        function qty_change()
        {
            var qty = parseFloat($('#weight').val());
            var price = parseFloat($('#rate').val());
            var usageqty = parseFloat($('#itemqty').val());
            var total =  (price / qty) * usageqty;
            total = Math.round(total * 100) / 100;
            $('#cost').val(parseFloat(total));
        }

        $('#btnSubmit').click(function(e){
            count++;
            if($('#finished').val() == ""){
                swal_alert("Error Message !","Finish Good is required ","error",false);
            }else if($('#product').val() == ""){
                swal_alert("Error Message !","Product is required ","error",false);
            }
            else if($('#rate').val() == 0)
            {
                swal_alert("Error Message !","Rate is empty or Zero!! ","error",false);
            }
            else if($('#itemqty').val() == 0){
                swal_alert("Error Message !","Packet Quantity is required ","error",false);
            }
            else{
                if (mode == "insert")
                {

                    $.ajax({
                        url:'{{ url("/add-job") }}',
                        type:"POST",
                        data:{_token : "{{csrf_token()}}",
                         jobid:$('#hidd_id').val(),
                         id:$('#finished').val(),
                         qty:$('#qty').val(),
                         count:count
                        },
                        success:function(result){
                            console.log(result);
                            // if (result == 2)
                            // {
                            //   swal_alert("Error Message !","Recipy of this product already exists.","error",true);
                            // }
                            if (result)
                            {
                                $('#hidd_id').val(result);
                            }
                            $.ajax({
                                url:'{{ url("/add-sub-job") }}',
                                type:"POST",
                                data:{_token : "{{csrf_token()}}",
                                 id:$('#hidd_id').val(),
                                 itemid:$('#product').val(),
                                 usage:$('#itemqty').val(),
                                 amount:$('#cost').val(),
								 dineIn:$('#dinein').val(),
                                 productmode:$('#productmode').val()
                                },
                                success:function(result){
                                    $('#raw').val('').change();
                                    $('#uom').val('');
                                    $('#rate').val('');
                                    $('#weight').val('');
                                    $('#itemqty').val('');

                                    if (result == 2)
                                    {
                                        swal_alert("Error Message !","Product already exists ","error",false);

                                    }
                                    else
                                    {
                                        getDetails();
                                        getCosting();
                                    }
                                }
                            });

                        }
                    });
                }
                else
                {
                    $.ajax({
                        url : "{{url('/item-update')}}",
                        type : "POST",
                        data : {
                            _token: "{{csrf_token()}}",
                            updateid: $('#update_id').val(),
                            itemid: $('#product').val(),
                            usage: $('#itemqty').val(),
                            amount: $('#cost').val(),
							dineIn:$('#dinein').val(),
                            productmode: $('#productmode').val(),
                        },
                        success : function(result){
                            swal_alert("Success !","Updated Successfully!","success");
                            mode = "insert";
                            getDetails();
                            getCosting();
                        }
                    });
                }
            }
        });

        function getDetails(){
            $.ajax({
                url : "{{url('/load-job')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", jobid:$('#hidd_id').val()},
                dataType : 'json',
                success : function(result){
                    $("#item_table tbody").empty();
                    $.each(result, function( index, value ) {
                        $("#item_table tbody").append(
                            "<tr>" +
                            "<td>"+value.product_name +"</td>" +
                            "<td>"+value.usage_qty +"</td>" +
                            "<td>"+value.cost+"</td>" +
                            "<td class='action-icon'><i id='btn"+index+"' onclick='updateItem("+value.recipy_details_id+","+value.item_id+","+value.mode_id+","+value.usage_qty+","+value.cost+","+value.used_in_dinein+")'  class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i id='btn"+index+"' onclick='deleteItem("+value.recipy_details_id+","+value.recipy_id+")'  class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                            "</tr>"
                        );
                    });
                }
            });
        }

        function updateItem(id,itemid,productmode,qty,amount,usedIn){

            mode = "update";
            $("#product").val(itemid).change();
            $('#update_id').val(id);
            $('#itemqty').val(qty);
            $('#cost').val(amount);
			$("#dinein").val(usedIn).change();
        }
        function deleteItem(id,recipyid)
        {
            swal({
                    title: "Are you sure?",
                    text: "Do you want to Delete!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{url('/item-delete')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:id,
                                recipyid:recipyid,
                            },
                            success:function(resp){
                                if(resp == 1){

                                    swal({
                                        title: "Deleted",
                                        text: "Item Deleted Successfully.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            getDetails();
                                            getCosting();
                                        }
                                    });
                                }
                                else{
                                    swal({
                                        title: "Deleted",
                                        text: "Recipy Deleted Successfully.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location = "{{url('/joborder')}}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your Item is safe :)", "error");
                    }
                });
        }
        function getCosting()
        {
            $.ajax({
                url : "{{url('/calculate-cost')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", jobid:$('#hidd_id').val()},
                dataType : 'json',
                success : function(result){
                    $('#ic').val(result);
                    getInfraCost();
                }
            });
        }

        function getInfraCost()
        {
            var total = parseFloat($('#ic').val()) + parseFloat($('#infra').val())  + parseFloat($('#pc').val());
            console.log(total);
            $('#totalCost').val(total);
        }

        $('#btnFinalSubmit').click(function(e){

            $.ajax({
                url : "{{url('/account-add')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}",
                jobid:$('#hidd_id').val(),
                 ic:$('#ic').val(),
                 pc:$('#pc').val(),
                 infra:$('#infra').val(),
                 totalcost:$('#totalCost').val()
                },
                dataType : 'json',
                success : function(result){
                    if (result == 1)
                    {
                        window.location = "{{url('/joborder')}}";
                    }
                }
            });
        });

        function swal_alert(title,msg,type,mode){

            swal({
                title: title,
                text: msg,
                type: type
            },function(isConfirm){
                if(isConfirm){
                    if(mode === true){
                        window.location = "{{url('/create-job')}}";
                    }
                }
            });
        }
        
        function getuom() {
            $.ajax({
                url: "{{ url('getunitofmessaure')}}",
                type: 'GET',
                data:{_token:"{{ csrf_token() }}",
                productid:$('#product').val(),
                },
                success:function(resp){
                    $('#uom').val(resp[0].name);
                    $('#rate').val(resp[0].retail_price);
                    $('#weight').val(resp[0].weight_qty);
                    $('#productmode').val(resp[0].product_mode);

                }

            });
        }


    </script>
@endsection