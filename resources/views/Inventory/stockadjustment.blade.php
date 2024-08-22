@extends('layouts.master-layout')

@section('title','Stock Adjustment')

@section('breadcrumtitle','Stock Adjustment')
@section('navinventory','active')
@section('navadjustment','active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Stock Adjustment</h5>

            </div>
            <div class="card-block">
                <div class="row">
					@if(session('roleId') == 17 )
					<div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Select Branch</label>
							<select id="branch" name="branch" class="form-control select2">
								<option value="">Select Branch</option>
								@foreach($branches as $branch)
									<option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
								@endforeach
							</select>
                        </div>
                    </div>
					@endif
                    <div class="col-lg-{{(session('roleId') == 17 ? 3 : 6 )}} col-md-{{(session('roleId') == 17 ? 3 : 6 )}}">
                        <div class="form-group">
                            <label class="form-control-label">Enter Product Name:</label>
							<select id="product" class="js-data-example-ajax form-control select2"></select>
                            <input type="hidden" id="hiddenamount" name="hiddenamount" value="0">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">Adjustment Qty:</label>
                            <input type="Number" name="qty" id="qty" class="form-control" placeholder="0" onchange="qtychanger()" />
{{--                            <div class="form-control-feedback text-info">Enter Adjustment Quantity (Positive or negative)</div>--}}
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">Amount (Value):</label>
                            <input type="Number" name="amount" id="amount" class="form-control" placeholder="0" />

                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">Stock In Hand:</label>
                            <input type="Number" name="stock" id="stock" class="form-control" placeholder="0" readonly="true" />

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <h6>Note:</h6>
                            <ol>
                                <li>If you want to Enter Damaged/Spoilage/Wastage Stock so please Enter Quantity in Negative Figuers (-ve).</li>
                                <li>If you want to Add Stock so please Enter Quantity in Positive Figuers (+ve).</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 f-right">
                        <div class="form-group">
                            <label class="form-control-label">Enter Reason Here:</label>
                            <textarea name="reason" id="reason" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="button-group ">
                    <button type="button" id="btngrn" class="btn btn-md btn-primary waves-effect waves-light f-right" onclick="creategrn()" >   <i class="icofont icofont-plus"> </i>
                        Stock Adjustment
                    </button>
                </div>
            </div>
        </div>

        <div class="card" id="dvgrn" style="display: none;">
            <div class="card-header">
                <h5 class="card-header-text">Good Received Note (GRN)</h5>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <table id="tblgrn" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

                                <thead>
                                <tr>
                                    <th>
                                        <div class="rkmd-checkbox checkbox-rotate">
                                            <label class="input-checkbox checkbox-primary">
                                                <input type="checkbox" id="checkbox32" class="mainchk">
                                                <span class="checkbox"></span>
                                            </label>
                                            <div class="captions"></div>
                                        </div>
                                    </th>
                                    <th>GRN No.</th>
                                    <th>Product Name</th>
                                    <th>Received Qty.</th>
									<th>Date</th>
                                    <th>Time</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="button-group ">
                    <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="adjuststock()" >   <i class="icofont icofont-plus"> </i>
                        Stock Adjustment
                    </button>
                </div>

            </div>
        </div>

    </section>
    <div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Select products</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-9">
                            <input type="text" name="search" id="search" placeholder="Search" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btnSearch" class="btn btn-success">Search</button>
                        </div>
                    </div>

                    <div id="divProd" style="height:550px;overflow: scroll;" >
                        <table id="inventtbl" class="table table-striped nowrap dt-responsive " >
                            <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
{{--                <div class="modal-footer">--}}
{{--                    <button type="button" id="btnSave" class="btn btn-success waves-effect waves-light f-right">--}}
{{--                        Done--}}
{{--                    </button>--}}
{{--                </div>--}}

            </div>
        </div>
    </div>

    <div class="modal fade modal-flex" id="return-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Stock Adjustment</h4>
                </div>
                <div class="modal-body">
              <div class="row">
<div class="col-md-12">
    <input type="hidden" id="stockid" value="">
    <input type="hidden" id="hiddenqty" value="">

    <center>
<label class="form-control-label f-20 text-info text-centre" id="lblprdouctname"></label>
    </center>
</div>
                  <div class="col-md-12">
                      <label class="form-control-label">Enter Adjustment Qty.</label>
<input type="number" id="returnqty" value="" class="form-control" min="1">
             <div class="form-control-feedback" id="dvqty"></div>

                  </div>

              </div>


                </div>
                                <div class="modal-footer">
                                    <button type="button" id="btnSave" class="btn btn-success waves-effect waves-light f-right" onclick="adjuststock()">
                                        Adjust Stock
                                    </button>
                                </div>

            </div>
        </div>
    </div>
@endsection
@section('scriptcode_three')
<script type="text/javascript" src="{{ URL::asset('public/js/purchase-script.js') }} "></script>
    <script type="text/javascript">

        $("#btnSearch").click(function () {
            page = 1;
            loadProductsFromSearch(page);
        });
		$(".select2").select2({});
		$('.js-data-example-ajax').select2({
		  ajax: {
			url: '{{route("search-inventory")}}',
			dataType: 'json',
			processResults: function (data) {
				console.log(data)
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
					results: $.map(data.items, function (item) {
						let name = item.product_name + " | "+ item.item_code;
						return {
							text: name,
							id: item.id
						}
					})
				};
			}
			// Additional AJAX parameters go here; see the end of this chapter for the full code of this example
		  },
		  placeholder: 'Search for a Product',
		  minimumInputLength: 1,
		});



        function loadProductsFromSearch(page)
        {
            if(page == 1)
            {
                $("#inventtbl tbody").empty();
            }
            $.ajax({
                url : "{{url('/load-products-by-search')}}"  + "?page="+page,
                type : "GET",
                data:{search:$("#search").val()},
                success : function(result){
                    $.each(result.data, function( index, value ) {
                        $("#inventtbl tbody").append(
                            "<tr style='cursor: pointer' onclick='selectproduct("+value.id+","+"\""+ value.product_name + "\")'>"+
                            "<td>"+value.item_code+"</td>"+
                            "<td>"+value.product_name+"</td>"+
                            "</tr>"
                        );
                    });

                }
            });
        }

        function loadProducts(page)
        {
            if(page == 1)
            {
                $("#inventtbl tbody").empty();
            }
            $.ajax({
                url : "{{url('/load-products')}}"  + "?page="+page,
                type : "GET",
                success : function(result){
                    $.each(result.data, function( index, value ) {
                        $("#inventtbl tbody").append(
                            "<tr style='cursor: pointer' onclick='selectproduct("+value.id+","+"\""+ value.product_name + "\")'>"+
                            "<td>"+value.item_code+"</td>"+
                            "<td>"+value.product_name+"</td>"+
                            "</tr>"
                        );
                    });

                }
            });
        }

        function getproduct() {
            page = 1;
            loadProducts(page);
            $('#qty').val("");
            $('#details-modal').modal("show");
        }
        $("#divProd").scroll(function() {
            page = page + 1;
            // ؕؕؕcount = 0;
            if($('#search').val() != ""){
                loadProductsFromSearch(page);
            } else{
                loadProducts(page);
            }
        });
        
        function selectproduct(id,name) {

            $('#productname').val(name);
            $('#productid').val(id);
            getstock(id);
            $('#details-modal').modal("hide");

        }
		
		$("#product").change(function(){
			getstock($("#product").val());
		});
        
        function qtychanger() {
            let qty =  parseFloat($('#qty').val());
            if(qty == 0)
            {
                swal({
                    title: "Error Message",
                    text: "Please Enter Valid Quantity either -ve or +ve!!",
                    type: "error"
                });
                $('#qty').val("");
            }
            else if($('#product').val() == "")
            {
                swal({
                    title: "Error Message",
                    text: "Please Select Product First!!",
                    type: "error"
                });
            }
            else{
                if(qty < 0)
                {
                    $('#amount').val("0.00");
                    $('#btngrn').css("display","none");
                    $('#dvgrn').css("display","block");

                    getgrns($('#product').val());
                }
                else{

                    let amount = $('#hiddenamount').val();
                    amount = qty * amount;
                    $('#amount').val(amount);
                    $("#tblgrn tbody").empty();
                    $('#btngrn').css("display","block");
                    $('#dvgrn').css("display","none");

                }
            }
        }
        
        function getstock(id) {
			@if(session('roleId') == 17)
				if($('#branch').val() == "")
				{
					swal({
						title: "Error Message",
						text: "Please select branch.",
						type: "error"
					});
					$("#branch").focus();
					$("#product").val("").change();
				}
			@endif
            $.ajax({
                url : "{{url('/getstock_value')}}",
                type : "GET",
                data:{
                    productid:id,
					branch : $("#branch").val(),
                },
                success : function(resp){
                    $('#amount').val(resp[0].cost_price);
                    $('#hiddenamount').val(resp[0].cost_price);
                    $('#stock').val(resp[0].stock);
                }
            });
        }
        
        
        function getgrns(id) {
            $.ajax({
                url : "{{url('/getgrns')}}",
                type : "GET",
                data:{
                    productid:id,
                },
                success : function(result){
                    $("#tblgrn tbody").empty();
                    for(var count =0;count < result.length; count++){
                        $("#tblgrn tbody").append(
                            "<tr>" +
                            "<td>" +
                            "<div class='rkmd-checkbox checkbox-rotate'>"+
                            "<label class='input-checkbox checkbox-primary'>"+
                            "<input type='checkbox' id='"+result[count].stock_id+"' class='chkbx' onclick='chkbox("+result[count].stock_id+")'>"+
                            "<span class='checkbox'></span>"+
                            "</label>"+
                            "<div class='captions'></div>"+
                            "</td>"+
                            "<td>"+result[count].grn_id+"</td>" +
                            "<td>"+result[count].product_name+"</td>" +
                            "<td>"+result[count].balance+"</td>" +
							"<td>"+result[count].date+"</td>" +
							"<td>"+result[count].time+"</td>" +
                            // "<td class='action-icon'><a class='m-r-10' onclick='returnqtyshow("+result[count].stock_id+","+result[count].balance+","+"\""+ result[count].product_name + "\")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-ui-edit text-primary f-18' ></i></a></td>"+
                            "</tr>"
                        );
                    }

                }
            });
        }
        
        function returnqtyshow(id,qty,name) {
            $('#stockid').val(id);
            $('#returnqty').val(qty);
            $('#lblprdouctname').html(name);
            $('#hiddenqty').val(qty);
            $('#dvqty').html("Not greater than "+qty);
            $('#return-modal').modal('show');
        }
        var rem_id = [];
        
        function adjuststock() {
            if($('#reason').val() == "")
            {
                swal({
                    title: "Error Message",
                    text: "Please Enter Reason First!!",
                    type: "error"
                });
            }
            else{
                $.ajax({
                    url : "{{url('/updatestockadjustment')}}",
                    type : "POST",
                    data:{_token:"{{ csrf_token() }}",
                        stockid:rem_id,
                        qty:$('#qty').val(),
						amount:$('#amount').val(),
                        narration:$('#reason').val(),
                        branch:$('#branch').val(),
                    },
                    success : function(resp){
						console.log(resp)
                        if(resp == 1){
                            swal({
                                title: "Success",
                                text: "Operation Successfully!!",
                                type: "success"
                            },function(isConfirm){
                                if(isConfirm){
                                    window.location="{{ url('/stockadjustment') }}";
                                }
                            });
                        }
                    }
                });
            }

        }

        $(".mainchk").on('click',function(){
            rem_id = [];

            if($(this).is(":checked")){

                $(".chkbx").each(function( index ) {
                    $(this).attr("checked",true);
                        rem_id.push($(this).attr("id"));
                });

            }else {
                rem_id = [];
                $(".chkbx").each(function( index ) {
                    $(this).attr("checked",false);
                });
            }

        });

        function chkbox(id) {
            if($("#"+id).is(":checked"))
            {
                rem_id.push(id);
            }
            else{
                let index = rem_id.indexOf(id);
                rem_id.splice(index,1);
            }
        }
        
        function creategrn() {
			@if(session('roleId') == 17)
				if($('#branch').val() == "")
				{
					swal({
						title: "Error Message",
						text: "Please select branch.",
						type: "error"
					});
				}
			@endif
			if($('#product').val() == "")
            {
                swal({
                    title: "Error Message",
                    text: "Please Select Product First!!",
                    type: "error"
                });
            }
			else if($('#qty').val() <= 0 || $('#qty').val() == "")
            {
                swal({
                    title: "Error Message",
                    text: "Please Enter Valid Quantity Here!!",
                    type: "error"
                });
            }
            else if($('#reason').val() == "")
            {
                swal({
                    title: "Error Message",
                    text: "Please Enter Reason First!!",
                    type: "error"
                });
            }
			
            else {

                $.ajax({
                    url: "{{url('/creategrnadjustmnet')}}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        productid: $('#product').val(),
                        qty: $('#qty').val(),
                        cp: $('#hiddenamount').val(),
                        amount: $('#amount').val(),
                        narration: $('#reason').val(),
                        branch: $('#branch').val(),
                    },
                    success: function (resp) {
                        if (resp == 1) {
                            swal({
                                title: "Success",
                                text: "Operation Successfully!!",
                                type: "success"
                            }, function (isConfirm) {
                                if (isConfirm) {
                                    window.location = "{{ url('/stockadjustment') }}";
                                }
                            });
                        }
                    }
                });
            }
        }


</script>
@endsection

