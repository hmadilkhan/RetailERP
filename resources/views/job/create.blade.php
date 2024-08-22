@extends('layouts.master-layout')

@section('title','Job Order')

@section('breadcrumtitle','Add Expense')

@section('navjoborder','active')
@section('navjobordercreate','active')


@section('content')
        <section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                     <h5 class="card-header-text"> Create New Job Order</h5>
                     <h5 class=""><a href="{{ route('customer.index') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                    
                    </div>
                  <div class="card-block">

    <form id="customerform" method="POST" class="form-horizontal" enctype="multipart/form-data"  action="{{ route('customer.store') }}">
       @csrf
    	<input type="hidden" id="update_id" name="update_id">
    	<input type="hidden" id="hidd_id" name="hidd_id">
              <div class="row">
        	
         
                   <div class="col-lg-3">
                     <div class="form-group">
                         <label class="form-control-label">Select Finished Good</label>
                        <select class="select2" data-placeholder="Select Finished Good" id="finished" name="finished">
                            <option value="">Select Finished Good</option>
                           	@if($products)
                           		@foreach($products as $value)
                           			<option value="{{$value->id}}">{{$value->product_name}}</option>
                           		@endforeach
                           	@endif
                            
                        </select>
                         <div class="form-control-feedback"></div>
                     </div>
                  </div> 

                    
                </div>      
              

                  
                

         </form>

            
                  </div>

               </div>

                <div class="card">
                	<div class="card-header">
                     <h5 class="card-header-text"> Select Raw Material</h5>
                  
                    </div>
                 <div class="card-block">
                  <div class="row">
                 <!-- product select box -->
                <div class="col-lg-3  col-sm-12">
                    <div class="form-group">
                         <label>Product</label>
                           <select class="select2 form-control" data-placeholder="Select Raw" id="raw"  name="raw">
                           	<option value="">Select Raw</option>
                           	@if($raw)
                           		@foreach($raw as $value)
                           			<option value="{{$value->id}}">{{$value->product_name}}</option>
                           		@endforeach
                           	@endif
                            	
                           </select>
                        <span class="help-block"></span>
                    </div>
                </div>  

                 <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Unit of Measure</label>
                           <select class="select2 form-control" disabled="disabled" data-placeholder="UOM" id="uom" name="uom">
                            <option value="">UOM</option>
                           	@if($uom)
                           		@foreach($uom as $value)
                           			<option value="{{$value->uom_id}}">{{$value->name}}</option>
                           		@endforeach
                           	@endif
                           </select>
                        <span class="help-block"></span>
                    </div>       
                </div>

                <!-- Amount box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                        <label>Amount</label>
                           <input type="text" readonly="true"  name="amount" placeholder="0" id="amount" class="form-control" />
                        <span class="help-block"></span>
                    </div>       
                </div> 
                 <!-- qty select box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Quantity</label>
                           <input type="text" placeholder="0" name="itemqty" id="itemqty" class="form-control" onchange="qty_change()"  />
                        <span class="help-block"></span>
                    </div>
                </div>  
                 <!-- price select box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Price</label>
                           <input type="text" placeholder="0" name="price" id="price" class="form-control"  />
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
                           <label class="form-control-label">Processing Cost</label>
                            <input readonly="readonly" type="text" id="cost" name="cost" class="form-control" />
                             @if ($errors->has('name'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Infra-Structure Cost</label>
                            <input type="text" id="infra" name="infra" class="form-control" onchange="getInfraCost()" value="0" />
                             @if ($errors->has('name'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Total Cost</label>
                            <input readonly="readonly" type="text" id="totalCost" name="totalCost" class="form-control" />
                             @if ($errors->has('name'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                  
                 	</div>
                 	<button type="button" id="btnFinalSubmit" class="btn btn-lg btn-primary f-right"> <i class="icofont icofont-check"> </i>Submit</button>

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
                swal_alert("Error Message !","Recipy Already Exists","error",true);
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
    	var qty = parseFloat($('#itemqty').val());
    	var price = parseFloat($('#amount').val());
    	var total = qty * price;
    	var total = Math.round(total * 100) / 100;
    	$('#price').val(parseFloat(total));
    }

    $('#btnSubmit').click(function(e){
      count++;
    	if($('#finished').val() == ""){
                swal_alert("Error Message !","Product is required ","error",false);
            }else if($('#qty').val() == ""){
                swal_alert("Error Message !","Quantity is required","error",false);
            }else if($('#raw').val() == ""){
                swal_alert("Error Message !","Raw is required ","error",false);
            }else if($('#itemqty').val() == 0){
                swal_alert("Error Message !","Raw Quantity is required ","error",false);
            }else{ 

            	if (mode == "insert") 
            	{


		    	$.ajax({
			        url:'{{ url("/add-job") }}',
			        type:"POST",
			        data:{_token : "{{csrf_token()}}",jobid:$('#hidd_id').val(),id:$('#finished').val(),qty:$('#qty').val(),count:count},
			        success:function(result){
                if (result == 2) 
                {
                  swal_alert("Error Message !","Recipy of this product already exists.","error",true);
                }
			        	if (result) 
			        	{
			        		$('#hidd_id').val(result);
			        	}
			        	$.ajax({
					        url:'{{ url("/add-sub-job") }}',
					        type:"POST",
					        data:{_token : "{{csrf_token()}}",id:$('#hidd_id').val(),itemid:$('#raw').val(),usage:$('#itemqty').val(),amount:$('#price').val()},
					        success:function(result){
					        	$('#raw').val('').change();
					        	$('#uom').val('').change();
					        	$('#amount').val('');
					        	$('#itemqty').val('');
					        	$('#price').val('');

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
		                data : {_token : "{{csrf_token()}}",updateid:$('#update_id').val(),id:$('#hidd_id').val(),itemid:$('#raw').val(),usage:$('#itemqty').val(),amount:$('#price').val()},
		                success : function(result){
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
                            "<td>"+value.amount+"</td>" +
                            "<td class='action-icon'><i id='btn"+index+"' onclick='updateItem("+value.job_sub_id+","+value.item_id+","+value.uom_id+","+value.usage_qty+","+value.amount+")'  class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i id='btn"+index+"' onclick='deleteItem("+value.job_sub_id+")'  class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                          "</tr>"
                         );
                    });
                }
                });
    }

    function updateItem(id,itemid,uom,qty,amount){
         mode = "update";
         $("#raw").val(itemid).change();
         $('#update_id').val(id);
         $('#itemqty').val(qty);
         $('#price').val(amount);
    }

    function deleteItem(id)
    {
    	swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this item!",
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
                        url: "{{url('/item-delete')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
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
                   	$('#cost').val(result);
                   	getInfraCost();
                }
            });
    }

    function getInfraCost()
    {
    	var total = parseFloat($('#cost').val()) + parseFloat($('#infra').val()) ;
    	$('#totalCost').val(total);
    }

    $('#btnFinalSubmit').click(function(e){

    	$.ajax({
                url : "{{url('/account-add')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", jobid:$('#hidd_id').val(),cost:$('#cost').val(),infra:$('#infra').val(),rp:$('#rp').val()},
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


  </script>

@endsection