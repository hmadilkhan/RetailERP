@extends('layouts.master-layout')

@section('title','Inventory')

@section('breadcrumtitle','View Inventory')
@section('navinventory','active')
@section('navinventorys','active')

@section('content')
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Upload Inventory</h5>
                <a href="{{ route('create-invent') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Inventory" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE INVENTORY</a>

                <a href="{{ url('get-sample-csv') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Download Sample" class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i class="icofont icofont-plus m-r-5" ></i> Download Sample</a>
            </div>
            <div class="card-block">
                <form method='post' action='{{url('uploadInventory')}}' enctype='multipart/form-data'>
                    {{ csrf_field() }}
                      <div class="row col-md-2 " >
                        <div class="form-group">
                            <label for="" class="checkbox-inline">Update to Retail Price</label>
                            <br/>
                            <label for="" class="checkbox-inline">
                                <input type="checkbox" name="update" id="update" class="custom-control">
                            </label>
                            @if ($errors->has('file'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>
                    <div class="row col-md-4 ">
                        <div class="form-group {{ $errors->has('file') ? 'has-danger' : '' }} ">
                            <label for="vdimg" class="form-control-label">Select File </label>
                            <br/>
                            <label for="vdimg" class="custom-file">
                                <input type="file" name="file" id="vdimg" class="custom-file-input">
                                <span class="custom-file-control"></span>
                            </label>
                            @if ($errors->has('file'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row col-md-2 " >
                        <input type='submit' class="btn btn-primary m-l-5 m-t-35" name='submit' value='Import'>

                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
					<div class="button-group col-md-12">
                        <a style="color:white;" target="_blank" href="{{URL::to('get-export-csv-for-retail-price')}}" class="btn btn-md btn-success waves-effect waves-light f-right" ><i class="icofont icofont-file-excel"> </i>
                          Export to Excel Sheet
                        </a>	
					</div>
                    <div class="col-md-2">
                        <div class="rkmd-checkbox checkbox-rotate">
                            <label class="input-checkbox checkbox-primary">
                                <input type="checkbox" id="Inactive">
                                <span class="checkbox"></span>
                            </label>
                            <div class="captions">Show In-Active Items</div>
                        </div>
						</div>
                    <div class="col-md-2">
                        <div class="rkmd-checkbox checkbox-rotate">
                            <label class="input-checkbox checkbox-primary">
                                <input type="checkbox" id="nonstock">
                                <span class="checkbox"></span>
                            </label>
                            <div class="captions">Show Non-Stock Items</div>
                        </div>
					</div>
					
                    <div class="col-md-12">
                        <div id="ddselect" class="dropdown-secondary dropdown  f-right" style="display: none;">
                            <button class="btn btn-default btn-md dropdown-toggle waves-light bg-white b-none txt-muted" type="button" id="dropdown6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icofont icofont-navigation-menu"></i> Change Settings</button>
                            <div class="dropdown-menu" aria-labelledby="dropdown6" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                <a class="dropdown-item waves-light waves-effect" id="btn_change_website" data-toggle="modal" data-target="#website-detail-modal"><i class="icofont icofont-company"></i>&nbsp;Link Website</a>
                                <div class="dropdown-divider"></div> 
                                <a class="dropdown-item waves-light waves-effect" id="btn_change_brand" data-toggle="modal" data-target="#brand-detail-modal"><i class="icofont icofont-company"></i>&nbsp;Link Brand</a> 
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="btn_change_tags" data-toggle="modal" data-target="#tags-detail-modal"><i class="icofont icofont-company"></i>&nbsp;Link Tags</a> 
                                <div class="dropdown-divider"></div>                                
                                <a class="dropdown-item waves-light waves-effect" id="btn_change_department"><i class="icofont icofont-company"></i>&nbsp;Change Department</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="change_subdept"><i class="icofont icofont-chart-flow-alt-2"></i>&nbsp;Change Sub Department</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="change_uom"><i class="icofont icofont-measure "></i>&nbsp;Change Unit of Measure</a>
                                <div class="dropdown-divider"></div>
								<a class="dropdown-item waves-light waves-effect" id="change_tax"><i class="icofont icofont-measure "></i>&nbsp;Change Tax</a>
                                
								<div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="change_price"><i class="icofont icofont-price"></i>&nbsp;Change Price</a>
								
								<div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" onclick="sunmiCloud()"><i class="icofont icofont-price"></i>&nbsp;Sunmi ESL</a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="btn_activeall"><i class="icofont icofont-ui-check"></i>&nbsp;Active All</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="btn_removeall"><i class="icofont icofont-close-line"></i>&nbsp;Inactive All</a>
								
                                <a class="dropdown-item waves-light waves-effect" id="btn_deleteall"><i class="icofont icofont-close-line"></i>&nbsp;Delete All</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row m-b-20">

                </div>
                <div class="row">
                    <div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search ItemCode</label>
                            <input class="form-control" type="text" name="code" id="code"   placeholder="Enter Product ItemCode for search"/>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search Product</label>
                            <input class="form-control" type="text" name="name" id="name"   placeholder="Enter Product Name for search"/>
                        </div>
                    </div>
					<div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search Price</label>
                            <input class="form-control" type="text" name="retail_price" id="retail_price"   placeholder="Enter Price for search"/>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Department</label>
                            <select class="select2" id="depart">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i>Sub-Department</label>
                            <select class="select2" id="subdepart">
                                <option value="">Select Sub Department</option>
                            </select>
                        </div>
                    </div>
					
					<div class="col-md-2 col-sm-12">
                        <div  class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> References</label>
                            <select class="select2" id="reference" name="reference">
                                <option value="">Select References</option>
								@foreach($references as $reference)
									<option value="{{$reference->refrerence}}">{{$reference->refrerence}}</option>
								@endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 ">

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button type="button" id="clear" data-placement="bottom" class="btn btn-warning  waves-effect waves-light f-right m-r-10">Clear</button>
						<button type="button" id="search" data-placement="bottom" class="btn btn-success  waves-effect waves-light f-right m-r-10">Search</button>
                    </div>
                </div>

                <!-- end of dropdown-secondary -->

                <!-- end of dropdown menu -->


            </div>
            <div id="table_data" class="card-block">
                @include('Inventory.inventory_table')
            </div>
        </div>
        
	@include('Inventory.partials.inventory_table_modals')
    </section>


@endsection

@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('scriptcode_three')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script> 
	<script src="https://cdn.jsdelivr.net/npm/md5-js-tools@1.0.2/lib/md5.min.js"></script>
    <script type="text/javascript">
        // $('#loader-modal').modal("show");
        $(".select2").select2();
        var departments = "";
        var rem_id = [];
        var page = 1;
        var count = 0;
        getData(page);
		
		function fetch_data(page)
			{
				let status = $("#Inactive").is(":checked") ? 2 : 1;
				let nonstock = $("#nonstock").is(":checked") ? 1 : 0;

				$.ajax({
					url:"{{ url('get-inventory-by-page')}}?page="+page,
					data:{code:$('#code').val(),name:$('#name').val(),rp:$('#retail_price').val(),dept:$('#depart').val(),sdept:$('#subdepart').val(),ref:$('#reference').val(),status:status,nonstock:nonstock},
					type: "GET",
					success:function(data)
					{
						$('#table_data').html(data);
					}
				});
			}
       
		$(document).ready(function(){

			$(document).on('click', '.pagination a', function(event){
				event.preventDefault(); 
				var page = $(this).attr('href').split('page=')[1];
				fetch_data(page);
				console.log(page);
			});
			
			
			
			$(document).on('click', '#search', function(event){
				page = 1;
				$("#table_data").empty();
				fetch_data(page);
			});
			
			$(document).on('click', '#Inactive', function(event){
				if ($("#Inactive").is(":checked")) {
					page = 1;
					$("#table_data").empty();
					fetch_data(page);
				}else{
					page = 1;
					$("#table_data").empty();
					fetch_data(page);
				}
				
			});
			$(document).on('click', '#nonstock', function(event){
				if ($("#nonstock").is(":checked")) {
					page = 1;
					$("#table_data").empty();
					fetch_data(page);
				}else{
					page = 1;
					$("#table_data").empty();
					fetch_data(page);
				}
				
			});
			
			
			
			$(document).on('click', '#clear', function(event){
				$("#code").val('');
				$("#name").val('');
				$("#retail_price").val('');
				$("#depart").val('').change();
				$("#subdepart").val('').change();
				$("#reference").val('').change();
				
				$("#table_data").empty();
				page = 1;
				fetch_data(page);
			});
		 
		});
		
		// function fetch_data(page)
		// {
			// alert("Calling")
			// $.ajax({
				// url:"{{ url('get-inventory-by-page')}}?page="+page,
				// data:{code:$('#code').val(),name:$('#name').val(),rp:$('#retail_price').val(),dept:$('#depart').val(),sdept:$('#subdepart').val(),ref:$('#reference').val()},
				// type: "GET",
				// dataType:"json",
				// success:function(data)
				// {
					// console.log(data);
					// $('#table_data').html(data);
				// }
			// });
		// }
		
		// $("#search").click(function(){
			// page = 1;
			// $("#table_data").empty();
			// fetch_data(page);
		// });
		
        function getData(page)
        {
            // $.ajax({
                // url: "{{ url('get-inventory-pagewise')}}" + "?page="+page,
                // type: 'GET',
                // dataType:"json",
                // async : 'false',
                // beforeSend:function(){
                // },
                // success:function(resp){
                    // console.log(resp);
                    // if(page == 1){
                        // $("#inventtbl tbody").empty();
                    // }
                    // tableData(resp)

                // }

            // });
        }

        function edit_route(id)
        {
			let location = "{{url('edit-invent')}}" + "/"+id;
			window.open(location)
        }

        // $("#search").click(function(){
            // page = 1;
            // $("#table_data").empty();
			// fetch_data(page);
            // if($("#Inactive").is(":checked")){
                // getInactiveInventoryBySearch(page);
			// }else if($("#nonstock").is(":checked")){
				// getNonStockInventory(page);
            // }else{
                // getProductByName(page);
            // }
        // });

        function  getProductByName(page) {
			console.log("Calling")
            $.ajax({
                url: "{{ url('get-inventory-by-name')}}"+ "?page="+page,
                type: 'GET',
                data:{code:$('#code').val(),name:$('#name').val(),rp:$('#retail_price').val(),dept:$('#depart').val(),sdept:$('#subdepart').val(),ref:$('#reference').val()},
                dataType:"json",
                success:function(resp){
  
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    tableData(resp)

                }

            });
        }

        // $('#Inactive').click(function(){
            // if ($("#Inactive").is(":checked")) {
				// $("#nonstock").prop('checked',false)
                // $("#inventtbl tbody").empty();
                // page = 1;
                // getInactiveInventory(page)
            // }else{
                // $("#inventtbl tbody").empty();
                // page = 1;
                // getData(page);
            // }
        // });
		
		// $('#nonstock').click(function(){
            // if ($("#nonstock").is(":checked")) {
				// $("#Inactive").prop('checked',false)
                // $("#inventtbl tbody").empty();
                // page = 1;
                // getNonStockInventory(page)
            // }else{
                // $("#inventtbl tbody").empty();
                // page = 1;
                // getData(page);
            // }
        // });

        function getInactiveInventory(page)
        {

            $.ajax({
                url: "{{ url('get-inactive-inventory')}}" + "?page="+page,
                type: 'GET',
                dataType:"json",
                async : 'false',
                beforeSend:function(){
                    // $('#loader-modal').modal("show");
                },
                success:function(resp){
                    // $('#loader-modal').modal("hide");
					console.log(resp)
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    tableData(resp)
                }

            });
        }
		
		function getNonStockInventory(page)
        {

            $.ajax({
                url: "{{ url('get-non-stock-inventory')}}" ,//+ "?page="+page
                type: 'GET',
				data:{code:$('#code').val(),name:$('#name').val(),rp:$('#retail_price').val(),dept:$('#depart').val(),sdept:$('#subdepart').val(),ref:$('#reference').val()},
                dataType:"json",
                async : 'false',
                beforeSend:function(){
                    // $('#loader-modal').modal("show");
                },
                success:function(resp){
                    // $('#loader-modal').modal("hide");
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    if(resp.length > 0){
                        $.each(resp, function( index, value ) {
						var stock = parseFloat(value.stock).toFixed(2);
						stock = (stock == "NaN" ? 0.00 : stock);
						var columnDeal_btn = "";
		
						if(value.is_deal == 1){
						   columnDeal_btn = "<a href='/inventory/"+value.id+"/deal-products' class='m-r-1' title='Create Deal Product'><i class='icofont icofont-plus text-success'></i></a>";
						}else{
						   columnDeal_btn = "<a href='/inventory/"+value.id+"/variable-products' class='m-r-1' title='Create Variable & Addon Product'><i class='icofont icofont-plus text-success'></i></a>"; 
						}
						
                            $("#inventtbl tbody").append(
                                "<tr>"+
                                "<td>" +
                                "<div class='rkmd-checkbox checkbox-rotate'>"+
                                "<label class='input-checkbox checkbox-primary'>"+
                                "<input type='checkbox' id='checkbox32"+value.id+"' class='chkbx' onclick='chkbox(this.id)' data-id='"+value.id+"'>"+
                                "<span class='checkbox'></span>"+
                                "</label>"+
                                "<div class='captions'></div>"+
                                "</td>"+
                                "<td>" +
                                "<a href='{{ asset('assets/images/products/')}}"+(value.product_image == "" ? "/placeholder.jpg" : +"/"+value.product_image)+"' data-toggle='lightbox'  data-footer=''>"+
                                "<img width='12' height='12' data-modal='modal-12' src='{{ asset('assets/images/products/')}}"+(value.product_image == "" ? "/placeholder.jpg" : "/"+value.product_image)+"' class='d-inline-block img-circle ' alt=''>"+
                                "</a>"+
                                "</td>"+
                                "<td>"+value.item_code+"</td>"+
                                "<td>"+value.product_name+"</td>"+
								 "<td>"+value.department_name+"</td>"+
                                "<td>"+value.sub_depart_name+"</td>"+
								"<td>"+value.actual_price+"</td>"+
								"<td>"+(value.tax_rate == null ? 0.00 : value.tax_rate)+"</td>"+
                                "<td>"+value.retail_price+"</td>"+
                                "<td>"+value.wholesale_price+"</td>"+
                                "<td>"+value.online_price+"</td>"+
								"<td>"+stock+"</td>"+
                                "<td>"+value.name+"</td>"+
                                "<td>" +
								columnDeal_btn+
								"<a onclick='edit_route(\""+value.slug+"\")' class='p-r-10 f-18 text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'><i class='icofont icofont-ui-edit'></i></a>"+
                                "</td>"+
                                "</tr>"
                            );
                        });
                    }
                    else{
                        $("#tableend").empty();
                        $("#tableend").append("")
                    }


                }

            });
        }

        function getInactiveInventoryBySearch(page)
        {

            $.ajax({
                url: "{{ url('get-inactive-inventory-by-search')}}" + "?page="+page,
                type: 'GET',
                data:{code:$('#code').val(),name:$('#name').val(),dept:$('#depart').val(),sdept:$('#subdepart').val(),ref:$('#reference').val()},
                dataType:"json",
                async : 'false',
                beforeSend:function(){
                    // $('#loader-modal').modal("show");
                },
                success:function(resp){
                    // $('#loader-modal').modal("hide");
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    tableData(resp)


                }

            });
        }


        function load_department()
        {
            $.ajax({
                url: "{{ url('get_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){

                    $('#ddldepartment').empty();
                    $("#ddldepartment").append("<option value=''>Select Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddldepartment").append(
                            "<option value="+value.department_id+">"+value.department_name+"</option>"
                        );
                    });

                }

            });
        }

        function load_uom()
        {
            $.ajax({
                url: "{{ url('get_uom')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){

                    $('#ddluom').empty();
                    $("#ddluom").append("<option value=''>Select Unit of Measure</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddluom").append(
                            "<option value="+value.uom_id+">"+value.name+"</option>"
                        );
                    });

                }

            });
        }
		
		function load_taxes()
        {
            $.ajax({
                url: "{{ url('get_taxes')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){

                    $('#ddltax').empty();
                    $("#ddltax").append("<option value=''>Select Tax</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddltax").append(
                            "<option value="+value.tax_rate+">"+value.tax_rate+"</option>"
                        );
                    });

                }

            });
        }

        function load_sub_dept(id)
        {
            $.ajax({
                url: "{{ url('get_sub_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:$("#department_modal_id").val()},
                success:function(resp){

                    $('#modalsubdept').empty();
                    $("#modalsubdept").append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#modalsubdept").append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                    });

                }

            });
        }
		
		function load_subdept(id)
        {
            $.ajax({
                url: "{{ url('get_sub_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:id},
                success:function(resp){

                    $('#ddlsubdept').empty();
                    $("#ddlsubdept").append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddlsubdept").append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                    });

                }

            });
        }

     $('#btnwebsiteSave').click(function(){

            if ($('#website_md').val() == "")
            {
                swal("Cancelled", "Please select website :)", "error");
            }
            else
            {

                $(".chkbx").each(function( index ) {
                    if($(this).is(":checked")){
                        if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                            rem_id.push($(this).data('id'));
                        }
                    }
                });

                $.ajax({
                    url: "{{ route('setProductAttribute_update')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,website:$('#website_md').val()},
                    success:function(resp){
                        if (resp == 'success')
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
                            swal("Cancelled", "Cannot change website. An error occured :)", "error");
                        }
                    }

                });//ajax end
            }//else end
        });
        
     $('#btnbrandSave').click(function(){

            if ($('#brand_md').val() == ""){
                swal("Cancelled", "Please select brand :)", "error");
            }else{
                    $(".chkbx").each(function( index ) {
                        if($(this).is(":checked")){
                            if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                                rem_id.push($(this).data('id'));
                            }
                        }
                    });
                $.ajax({
                    url: "{{ route('setProductAttribute_update') }}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,brand:$('#brand_md').val()},
                    success:function(resp, textStatus, jqXHR){
                        // console.log(jqXHR);
                        if (jqXHR.status == 200){
                            window.location = "{{ url('inventory-list') }}"
                        }else{
                            swal("Cancelled", "Cannot change brand. An error occured :)", "error");
                        }
                    }
                });//ajax end
            }//else end
        });  
        
     $('#btntagSave').click(function(){

            if ($('#tags_md').val() == ""){
                swal("Cancelled", "Please select brand :)", "error");
            }else{
                    $(".chkbx").each(function( index ) {
                        if($(this).is(":checked")){
                            if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                                rem_id.push($(this).data('id'));
                            }
                        }
                    });
                $.ajax({
                    url: "{{ route('setProductAttribute_update') }}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,tags:$('#tags_md').val()},
                    success:function(resp, textStatus, jqXHR){
                        // console.log(jqXHR);
                        if (jqXHR.status == 200){
                            window.location = "{{ url('inventory-list') }}"
                        }else{
                            swal("Cancelled", "Cannot change brand. An error occured :)", "error");
                        }
                    }
                });//ajax end
            }//else end
        });         
        

        $('#btnDptSave').click(function(){

            if ($('#ddldepartment').val() == "")
            {
                swal("Cancelled", "Please Select Department :)", "error");
            }
            else
            {

                $(".chkbx").each(function( index ) {
                    if($(this).is(":checked")){
                        if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                            rem_id.push($(this).data('id'));
                        }
                    }
                });

                $.ajax({
                    url: "{{ url('update_product_department')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,deptId:$('#ddldepartment').val()},
                    success:function(resp){
                        if (resp == 1)
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
                            swal("Cancelled", "Cannot change department. An error occured :)", "error");
                        }
                    }

                });//ajax end
            }//else end
        });


        $('#btnUomSave').click(function(){

            if ($('#ddluom').val() == "")
            {
                swal("Cancelled", "Please Select Unit of Measure :)", "error");
            }
            else
            {

                $(".chkbx").each(function( index ) {
                    if($(this).is(":checked")){
                        if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                            rem_id.push($(this).data('id'));
                        }
                    }
                });

                $.ajax({
                    url: "{{ url('update_product_uom')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,uomId:$('#ddluom').val()},
                    success:function(resp){
                        if (resp == 1)
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
                            swal("Cancelled", "Cannot change Unit of Measure. An error occured :)", "error");
                        }
                    }

                });//ajax end
            }//else end
        });
		
		$('#btnTaxSave').click(function(){
			if ($('#ddltax').val() == "")
            {
                swal("Cancelled", "Please Select Tax :)", "error");
            }
			else if($('#tax_rate_new').val() == ""){
				swal("Cancelled", "Please Enter New Tax :)", "error");
			}else{
				$('#btnTaxSave').prop('disabled',true);
				$.ajax({
                    url: "{{ url('update_product_tax')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",prev_tax:$('#ddltax').val(),new_tax:$('#tax_rate_new').val()},
                    success:function(resp){
                        if (resp == 1)
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
							$('#btnTaxSave').prop('disabled',false);
                            swal("Cancelled", "Cannot change tax. An error occured :)", "error");
                        }
                    }

                });
			}
		});

        $('#btnsubdeptSave').click(function(){

            if ($('#ddlsubdept').val() == "")
            {
                swal("Cancelled", "Please Select Sub Department :)", "error");
            }
            else
            {

                $(".chkbx").each(function( index ) {
                    if($(this).is(":checked")){
                        if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                            rem_id.push($(this).data('id'));
                        }
                    }
                });

                $.ajax({
                    url: "{{ url('update_product_subdepartment')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,subdeptId:$('#ddlsubdept').val(),deptID:$('#ddldepartment1').val()},
                    success:function(resp){
                        if (resp == 1)
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
                            swal("Cancelled", "Cannot change Sub Department. An error occured :)", "error");
                        }
                    }

                });//ajax end
            }//else end
        });

        //light box
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();

        });



        function deleteCall(id){
            // var id= $(this).data("id");
            swal({
                    title: "Are you sure?",
                    text: "This item will mark as inactive and will not be further available for sales!",
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
                            url: "{{ url('delete-invent')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",id:id,status:2},
                            success:function(resp){

                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Product Successfully Inactive.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            page = 1;
                                            fetch_data(page);

                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your product is safe :)", "error");
                    }
                });
        }

        function item_inactive(id){

            swal({
                    title: "Are you sure?",
                    text: "This item will be the part of inventory again !!!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Active it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{ url('delete-invent')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",id:id,status:1},
                            success:function(resp){

                                if(resp == 1){
                                    swal({
                                        title: "Activated",
                                        text: "Product activated Successfully .",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('inventory-list') }}";
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

        message("{{session('message')}}");
        function message(message)
        {
            if (message == 1)
            {
                notify("Import Successful", "success")
            }
            else if(message == 2)
            {
                notify("File too large. File must be less than 2MB.", "danger")
            }
            else if(message == 3)
            {
                notify("Invalid File Extension.", "danger")
            }
        }

        //Welcome Message (not for login page)
        function notify(message, type) {
            $.growl({
                message: message
            }, {
                type: type,
                allow_dismiss: true,
                label: 'Cancel',
                className: 'alert-success btn-primary',
                placement: {
                    from: 'top',
                    align: 'center'
                },
                delay: 3000,
                animate: {
                    enter: 'animated flipInX',
                    exit: 'animated flipOutX'
                },
                offset: {
                    x: 30,
                    y: 30
                }
            });
        };
        $("#btn_change_department").on('click',function(){
            load_department();
            $('#details-modal').modal("show");

            $(".chkbx").each(function( index ) {

                if($(this).is(":checked")){
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));
                    }
                }
            });

        });

        $('#change_price').click(function () {
            $('#price-modal').modal("show");
        });
		
		$('#sunmi_cloud').click(function () {
            $('#sunmi-coud-modal').modal("show");
        });

        $("#change_uom").on('click',function(){
            load_uom();
            $('#details-uom').modal("show");
        });
		$("#change_tax").on('click',function(){
            load_taxes();
            $('#change-tax-modal').modal("show");
        });

        $('#ddldepartment1').change(function(){
            load_subdept($('#ddldepartment1').val());
        });

        $("#change_subdept").on('click',function(){

            $.ajax({
                url: "{{ url('get_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){

                    $('#ddldepartment1').empty();
                    $("#ddldepartment1").append("<option value=''>Select Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddldepartment1").append(
                            "<option value="+value.department_id+">"+value.department_name+"</option>"
                        );
                    });

                }

            });
            $('#details-subdept').modal("show");
        });

        $("#btn_removeall").on('click',function(){
            var products = [];
            $(".chkbx").each(function( index ) {
                if($(this).is(":checked")){
					// console.log($(this).data('id'))
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));
                    }
                }

            });
			// console.log(rem_id)
            $.ajax({
                url: "{{url('/get_names')}}",
                type: "POST",
                data: {_token:"{{csrf_token()}}",ids:rem_id},
                async:false,
                success:function(resp){
                    for(var s=0;s < resp.length ;s++){
                        products.push(resp[s].product_name);
                    }
                }
            });

            var names = products.join();

            swal({
                title: "INACTIVE PRODUCTS",
                text: "Do you want to inactive  "+names+" ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){

                    if(rem_id.length > 0){
                        $.ajax({
                            url: "{{url('/all_invent_remove')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",inventid:rem_id,statusid:2},
                            success:function(resp){

                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products In-Active Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/inventory-list')}}";
                                        }
                                    });

                                }else{
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }

                            }

                        });
                    }

                }else{
                    swal({
                        title: "Cancel!",
                        text: "All products are still inactive :)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            window.location="{{url('/inventory-list')}}";

                        }
                    });

                }

            });


        });
		
		$("#btn_deleteall").on('click',function(){
            var products = [];
            $(".chkbx").each(function( index ) {
                if($(this).is(":checked")){
					// console.log($(this).data('id'))
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));
                    }
                }
            });

            swal({
                title: "DELETE PRODUCTS",
                text: "Do you want to delete products ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){

                    if(rem_id.length > 0){
                        $.ajax({
                            url: "{{url('/all_invent_delete')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",inventid:rem_id},
                            success:function(resp){

                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products deleted Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/inventory-list')}}";
                                        }
                                    });
                                }else{
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }
                            }

                        });
                    }

                }else{
                    swal({
                        title: "Cancel!",
                        text: "All products are still inactive :)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            window.location="{{url('/inventory-list')}}";

                        }
                    });
                }
            });
        });

//'https://sabsoft.com.pk//Retail/public/assets/samples/sample_inventory.csv',
        $('#downloadsample').click(function(){
						window.open(location.origin+"/assets/samples/sample_inventory.csv");
                        // $.ajax({
                            // url: '{{-- asset('public/assets/samples/sample_inventory.csv') --}}',
                            // method: 'GET',
                            // xhrFields: {
                                // responseType: 'blob'
                            // },
                            // success: function (data) {
                                // var a = document.createElement('a');
                                // var url = window.URL.createObjectURL(data);
                                // a.href = url;
                                // a.download = 'sample_inventory.csv';
                                // document.body.append(a);
                                // a.click();
                                // a.remove();
                                // window.URL.revokeObjectURL(url);
                            // }
                        // });
                    });
					
					
					
					

                    function Submitprice() {

                        $(".chkbx").each(function( index ) {
                            if($(this).is(":checked")){
                                if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                                    rem_id.push($(this).data('id'));
                                }
                            }
                        });


                        if ($('#pricemode').val() == "") {
                            swal({
                                title: "Error Message!",
                                text: "Please Select Mode First!",
                                type: "error"
                            });
                        }
                        else{
                            $.ajax({
                                url: "{{url('/insertnewprice')}}",
                    type: "POST",
                    data: {_token:"{{csrf_token()}}",
                        productid:rem_id,
						departmentId : $('#department_modal_id').val(),
						subDepartmentId : $('#modalsubdept').val(),
                        rp:$('#rp').val(),
                        wp:$('#wp').val(),
                        dp:$('#dp').val(),
                        op:$('#op').val(),
                        pricemode:$('#pricemode').val(),
                    },
                    success:function(resp){
                        // console.log(resp);
                        swal({
                            title: "Success!",
                            text: "Price Change Successfully!",
                            type: "success"
                        });
                        $('#price-modal').modal('hide');
                        window.location="{{url('/inventory-list')}}";
                    }

                });
            }




        }
        loadDepartment();

        function loadDepartment() {
            $.ajax({
                url: "{{ url('get_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){
                    $('#depart').empty();
                    $("#depart").append("<option value=''>Select Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#depart").append(
                            "<option value="+value.department_id+">"+value.department_name+"</option>"
                        );
                    });

                }

            });
        }

        $('#depart').change(function (e) {

            $.ajax({
                url: "{{ url('get_sub_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:$("#"+this.id).val()},
                success:function(resp){
                    $('#subdepart').empty();
                    if(resp != 0){
                        $("#subdepart").append("<option value='all'>Select Sub Department</option>");
                        $.each(resp, function( index, value ) {
                            $("#subdepart").append(
                                "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                            );
                        });
                    }else{
                        $("#subdepart").append(
                            "<option value='0'>No Data Found</option>"
                        );
                    }


                }

            });
        })

        function show_barcode(code,name,price) {
            // EMPTY CONTROLS
            $('#label_code').val('');
            $('#label_name').val('');
            $('#label_price').val('');

            // SET VALUE TO CONTROLS
            $('#label_code').val(code);
            $('#label_name').val(name);
            $('#label_price').val(price);

            $('#label-modal').modal("show");
{{--            window.location = "{{url('label')}}" + "?code="+code+"&name="+name+"&price="+price;--}}
        }

        function print_barcode() {
            var url = $('#labelsize').val() +""+ $('#labelpattern').val();

            $.ajax({
                url: "{{ url('printBarcode')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",url:url,code:$('#label_code').val(),name:$('#label_name').val(),price:$('#label_price').val(),margin1:$('#name_margin1').val(),margin2:$('#name_margin2').val(),printheader:$("#printheader").val()},
                success:function(resp){
                    window.open(resp);
                }

            });


        }
		
		function assignToVendorModal(productId)
		{
		  $('#sp-modal').modal("show");
		  $("#productidforvendors").val(productId);
		}
		
		$("#btn_assign").click(function(){
			var vendor = $("#vendor").val();
			var productId = $(productidforvendors).val();
			if(vendor == ""){
					alert("Please Select Vendors")
			  }else{
				  $.ajax({
					url : "{{url('/assign-product-to-vendors')}}",
					type : "POST",
					data : {_token : "{{csrf_token()}}", productId:productId,vendors:vendor},
					dataType : 'json',
					success : function(result){
						// console.log(result);
						if(result == 1){
							$('#sp-modal').modal("hide");
							// location.reload();
						}
					}
				});
			  }
		});

		function sunmiCloud(){
			$(".chkbx").each(function( index ) {
				if($(this).is(":checked")){
					if(jQuery.inArray($(this).data('id'), rem_id) == -1){
						rem_id.push($(this).data('id'));
					}
				}
			});
			
			$.ajax({
				url: "{{url('/sunmi-cloud')}}",
				type: "POST",
				data: {_token:"{{csrf_token()}}",
					inventory:rem_id,
				},
				success:function(resp){
					// console.log(resp);
					
						sendToSunmi(resp)
					
					// swal({
						// title: "Success!",
						// text: "Price Change Successfully!",
						// type: "success"
					// });
					// window.location="{{url('/inventory-list')}}";
				}
			});
		}
		const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		
		function generateString(length) {
			
			let result = '';
			const charactersLength = characters.length;
			for ( let i = 0; i < length; i++ ) {
				result += characters.charAt(Math.floor(Math.random() * charactersLength));
			}

			return result;
		}
		
		function getCurrentUnixTimestamp() {
			return Math.floor(Date.now() / 1000);
		}
		
		function sendToSunmi(productList){
			// console.log("PRODUCT LIST:",productList);
			let random = generateString(7);
			// console.log("RANDOM:",random);

			let timestamp = getCurrentUnixTimestamp();
			// console.log("TIMESTAMP:",timestamp);
			
			let string = 'app_id=KV1LI73MXVBAQ&product_list='+productList+'&random='+random+'&shop_id=1&timestamp='+timestamp+'&key=0XsVp45yO0vJlEbWsPPQ';
			var hash = MD5.generate(string);
			let sign = hash.toUpperCase();
			
			
			$.ajax({
			  url: "https://store.sunmi.com/openapi/product/update",
			  method : "POST",
			  data:{shop_id : 1,product_list:productList,app_id:'KV1LI73MXVBAQ',random:random,timestamp:timestamp,sign:sign},
			  cache: false,
			  success: function(response){
				console.log(response)
			  }
			});
		}
		
		
		function createDeal(id,name){
		    
		    $('#department_md,#products,#group_type').val('').trigger('change');
		    
		    if(!$('#products').attr('disabled')){
		        $('#products').attr('disabled',true);
		    }
		    
		    $('#group_name,#selection_limited').val('');
		    
		    $("#group_name_alert,#group_type_alert").text('');
		    
		    $("#inventory_id").val(id);
		    $("#inventory_name").val(name)
		    $("#createDeal-modal").modal('show');
		}
		
		function editDeal(itemId,groupId,groupName,groupType,groupLimit){
		    
		    $("#inventory_id_edit").val(itemId);
		    $("#group_id").val(groupId)
		    $("#editDeal-modal").modal('show');	
		    
		    $("#group_name_edit").val(groupName);
		    
		    $("#group_type_edit").val(groupType).trigger('change');
		    $('#selection_limited_edit').val(groupLimit);
		    
    			$.ajax({
    			  url: "{{ route('getDeal_prod_values') }}",
    			  method : "POST",
    			  data:{_token:'{{ csrf_token() }}',prod_id:itemId,group_id:groupId},
    			  dataType:'json',
    			  async: false,
    			  success: function(resp){
                      if(resp.departmentId != null){
                          $("#department_md_edit").val(resp.departmentId).trigger('change');
                           setTimeout(function() { 
                                selectedProduct(resp.productId);
                            },300);
					       
					
                      }
    			  }
    			});	
		    
		}
		
		function selectedProduct(values){
		   $("#products_md_edit").select2('val',[values]); 
		}
		
		$("#group_type").on('change',function(){
		    if($(this).val() == 'multiple'){
		        if($('#selection_limited').attr('disabled')){
		            $('#selection_limited').attr('disabled',false);
		        }
		    }else{
		        if(!$('#selection_limited').attr('disabled')){
		            $('#selection_limited').attr('disabled',true);
		        }		        
		    }
		});
		
		$("#group_type_edit").on('change',function(){
		    if($(this).val() == 'multiple'){
		        if($('#selection_limited_edit').attr('disabled')){
		            $('#selection_limited_edit').attr('disabled',false);
		        }
		    }else{
		        if(!$('#selection_limited_edit').attr('disabled')){
		            $('#selection_limited_edit').attr('disabled',true);
		        }		        
		    }
		});		
		
		
		$("#department_md").on('change',function(){
		    productload_department_wise($(this).val(),'products_md');
// 			$.ajax({
// 			  url: "{{ route('invent-list-department') }}",
// 			  method : "POST",
// 			  data:{_token:'{{ csrf_token() }}',id:$(this).val()},
// 			  cache: false,
// 			  success: function(resp){
// 			    if(resp != null){     
// 				 $("#products_md").empty();
				 
// 				 if($("#products_md").attr('disabled')){
// 				     $("#products_md").attr('disabled',false);
// 				 }
			 
//     			   $.each(resp,function(i,v){
//     			       $("#products_md").append('<option value="'+v.id+'">'+v.product_name+'</option>');
//     			   })	 
// 			    }
				 
// 			  }
// 			});		    
		    
		});
		
		
		$("#department_md_edit").on('change',function(){
		    productload_department_wise($(this).val(),'products_md_edit');
		});
		
		function productload_department_wise(departId,elementId){
			$.ajax({
			  url: "{{ route('invent-list-department') }}",
			  method : "POST",
			  data:{_token:'{{ csrf_token() }}',id:departId},
			  cache: false,
			  success: function(resp){
			    if(resp != null){     
				 $("#"+elementId).empty();
				 
				 if($("#"+elementId).attr('disabled')){
				     $("#"+elementId).attr('disabled',false);
				 }
			 
    			   $.each(resp,function(i,v){
    			       $("#"+elementId).append('<option value="'+v.id+'">'+v.product_name+'</option>');
    			   })	 
			    }
				 
			  }
			});			    
		}
		
		
		$("#btn_addDeal").on('click',function(){
		    
		    if($("#group_name").val() == ''){
		        $("#group_name_alert").text('Field is required.');
		    }else if($("#group_type").val() == ''){
		        $("#group_type_alert").text('Field is required.');
		    }else{
    			$.ajax({
    			  url: "{{ route('storeDeal_up') }}",
    			  method : "POST",
    			  data:$("#createDealForm").serialize(),
    			  async: false,
    			  success: function(resp){
                     if(resp.status == 200){
                         $("#deal_msg_md").text('Success!').addClass('alert alert-success');
                         $("#cell-4-"+$("#inventory_id").val()).empty();
                         // dealSetToInventory();
                         $("#createDeal-modal").modal('hide');
                     }else{
                         if(resp.status == 409){
                           $("#group_name_alert").text(resp.msg);
                         }
                         
                         if(resp.status == 500){
                           $("#deal_msg_md").text(resp.msg).addClass('alert alert-danger');
                         }                         
                     }
    			  }
    			});			        
		    }
		});
		
		$("#btn_updateDeal").on('click',function(){
		    
		    if($("#group_name_edit").val() == ''){
		        $("#group_name_edit_alert").text('Field is required.');
		    }else if($("#group_type_edit").val() == ''){
		        $("#group_type_edit_alert").text('Field is required.');
		    }else{
    			$.ajax({
    			  url: "{{ route('updateDeal_up') }}",
    			  method : "POST",
    			  data:$("#editDealForm").serialize(),
    			  async: false,
    			  success: function(resp){
    			     console.log(resp)
                     if(resp.status == 200){
                         $("#deal_msg_md_edit").text('Success!').addClass('alert alert-success');
                         $("#cell-4-"+$("#inventory_id_edit").val()).empty();
                         // dealSetToInventory();
                         $("#editDeal-modal").modal('hide');
                     }else{
                         if(resp.status == 409){
                           $("#group_name_edit_alert").text(resp.msg);
                         }
                         
                         if(resp.status == 500){
                           $("#deal_msg_md_edit").text(resp.msg).addClass('alert alert-danger');
                         }                         
                     }
    			  }
    			});			        
		    }
		});
		
		$("#btn_removeDeal").on('click',function(){
		    
            swal({
                title: "DELETE DEAL",
                text: "Do you want to delete deal "+$("#group_name_edit").val()+"?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                        $.ajax({
                            url: "{{route('removeDeal_up')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",inventid:$("#inventory_id").val(),group_id:$("#group_id").val()},
                            success:function(resp){

                                if (resp.status == 200) {
                                    
                                    $("#groupDeal-"+$("#group_id").val()).remove();
                                    
                                    swal("Success!", "Deal is Deleted:)", "success");
                                    $("#editDeal-modal").modal('hide');
                                    // swal({
                                    //     title: "Success!",
                                    //     text: "All Products deleted Successfully :)",
                                    //     type: "success"
                                    // },function(isConfirm){
                                    //     if(isConfirm){
                                    //         window.location="{{url('/inventory-list')}}";
                                    //     }
                                    // });
                                }else{
                                    swal("Alert!", "Deal not Deleted:)", "error");
                                }
                            }

                        });
                }else{
                    swal.close();
                    // swal({
                    //     title: "Cancel!",
                    //     text: "All products are still inactive :)",
                    //     type: "error"
                    // },function(isConfirm){
                    //     if(isConfirm){
                    //         window.location="{{url('/inventory-list')}}";

                    //     }
                    // });
                }
            });		    
		    
		})
    </script>

@endsection
