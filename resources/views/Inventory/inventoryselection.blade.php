@extends('layouts.master-layout')

@section('title','Display Inventory')

@section('breadcrumtitle','Display Inventory')
@section('navinventory','active')
@section('navinventorys','active')

@section('content')

	<section class="panels-wells">
		<div class="card">
            <div class="card-header">
			   <h5 class="card-header-text">Filter Inventory</h5>
			   <hr/>
                <div class="row">
                    <div class="col-md-3 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search ItemCode</label>
                            <input class="form-control" type="text" name="code" id="code"   placeholder="Enter Product ItemCode for search"/>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search Product</label>
                            <input class="form-control" type="text" name="name" id="name"   placeholder="Enter Product Name for search"/>
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
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i>Status</label>
                            <select class="select2" id="status">
                                <option value="">All</option>
                                <option value="1">Yes</option>
                                <option value="2">No</option>
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
                        <button type="button" id="search" data-placement="bottom" class="btn btn-success  waves-effect waves-light f-right m-r-10">Search</button>

                    </div>
                </div>
			</div>
			<div class="card-block">
					<div id="table_data">
						 @include('partials.inventory_table')
					</div>
			</div>
		</div>
	</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
	$(".select2").select2();
	load_department();
	$(document).ready(function(){
		
	$(document).on('click', '.pagination a', function(event){
	  event.preventDefault(); 
	  var page = $(this).attr('href').split('page=')[1];
	  fetch_data(page);
	  $(this).parents().next('.hide').toggle();
	});
	
	function fetch_data(page)
	 {
	  $.ajax({
	   url:"{{ url('fetch-inventory-data')}}?page="+page,
	   data:{
		   code : $("#code").val(),
		   name : $("#name").val(),
		   depart : $("#depart").val(),
		   sdepart : $("#subdepart").val(),
		   status : $("#status").val(),
	   },
	   success:function(data)
	   {
		$('#table_data').empty();
		$('#table_data').html(data);
	   }
	  });
	 }
	});
	
	$("#search").click(function(){
		$.ajax({
			url:"{{ url('fetch-inventory-data')}}",
			data:{
			   code : $("#code").val(),
			   name : $("#name").val(),
			   depart : $("#depart").val(),
			   sdepart : $("#subdepart").val(),
			   status : $("#status").val(),
			},
			success:function(data)
			{
				console.log("Here",data)
				$('#table_data').empty();
				$('#table_data').html(data);
			}
		});
	})
	
	function load_department()
	{
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
	
	$('#depart').change(function(){
		$.ajax({
			url: "{{ url('get_sub_departments')}}",
			type: 'POST',
			data:{_token:"{{ csrf_token() }}",id:$('#depart').val()},
			success:function(resp){

				$('#subdepart').empty();
				$("#subdepart").append("<option value=''>Select Sub Department</option>");
				$.each(resp, function( index, value ) {
					$("#subdepart").append(
						"<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
					);
				});

			}
		});
	});

</script>
@endsection