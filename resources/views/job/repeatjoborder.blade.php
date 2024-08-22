@extends('layouts.master-layout')

@section('title','Repeat Job Order')

@section('breadcrumtitle','Add Expense')


@section('navjoborder','active')
@section('navrepeatorder','active')

@section('content')
        <section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                     <h5 class="card-header-text"> Repeat Job Order</h5><label id="limit" class="form-control-label text-danger f-right f-24"></label>
                     <h5 class=""><a href="{{ url('job-order') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                    
                    </div>
                  <div class="card-block">

    <form id="customerform" method="POST" class="form-horizontal" enctype="multipart/form-data"  action="{{ route('customer.store') }}">
       @csrf
    	<input type="hidden" id="update_id" name="update_id">
    	<input type="hidden" id="hidd_id" name="hidd_id">
      <input type="hidden" id="limit_id" name="limit_id">
      <input type="hidden" id="unit_cost" name="unit_cost">
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

                  <div class="col-md-3">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Qty</label>
                            <input type="text" id="qty" name="qty" onchange="qty_change()" class="form-control" value="1" />
                             @if ($errors->has('name'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                             @endif
                        </div>
                    </div>

                    <div class="col-lg-3">
                     <div class="form-group">
                         <label class="form-control-label">Select Master </label>
                        <select class="select2" data-placeholder="Select Master" id="master" name="master">
                            <option value="">Select Master</option>
                            @if($masters)
                              @foreach($masters as $value)
                              <option value="{{$value->id}}">{{$value->name}}</option>
                              @endforeach
                            @endif
                            
                        </select>
                         <div class="form-control-feedback"></div>
                     </div>
                  </div> 

                  <div class="col-md-3">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Amount Per Piece</label>
                            <input type="text" id="amountperpeice" onchange="qty_change()" value="0" name="amountperpeice" class="form-control" />
                             @if ($errors->has('name'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                             @endif
                        </div>
                    </div>
                    
                </div>  
                           

         </form>
         <hr/> 
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
               
             <hr/>     


              <h4>Total Calculation</h4>
          
    
                 	<div class="row ">
                 		 <div class="col-md-2">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Processing Cost</label>
                            <input readonly="readonly" type="text" id="cost" name="cost" class="form-control" />
                            
                                    <div class="form-control-feedback">Per Unit Processing Cost : <label id="puc"></label> </div>
                              
                        </div>
                    </div>

                    <div class="col-md-2">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Total Master Cost</label>
                            <input readonly="readonly" type="text" id="totalMasterCost" name="totalMasterCost" value="0" class="form-control" />
                             <div class="form-control-feedback">Per Unit Master Cost : <label id="pum"></label> </div>
                             
                        </div>
                    </div>

                    <div class="col-md-2">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Total Cost</label>
                            <input readonly="readonly" type="text" id="totalCost" name="totalCost" class="form-control" />
                                    <div class="form-control-feedback">Per Unit Total Cost: <label id="putc"></label></div>
                        </div>
                    </div>

                    <div class="col-md-2">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Retail Per Piece</label>
                            <input readonly="readonly" type="text" id="rpp" name="rpp" class="form-control" />
                             @if ($errors->has('name'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Retail Price</label>
                            <input type="text" id="rp" name="rp" class="form-control"  />
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

    $('#finished').change(function(){

    $.ajax({
          url:'{{ url("/get-job-id") }}',
          type:"POST",
          data:{_token : "{{csrf_token()}}",productId:$('#finished').val()},
          success:function(result){
            getDetails($('#finished').val());
            getCosting($('#finished').val());
            qty_change();
            getRecipyLimit();
          }
        });
    });


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
                                "<td>"+value.amount+"</td>" +
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
                        $('#puc').html(result);
                        $('#unit_cost').val(result);
                    }
                });
        }
       
      function qty_change()
      {
          setTimeout(function(){ 
            if ($('#qty').val() == "") 
            {
               $('#qty').focus();
               swal("Error", "Please select qty first :)", "error");
            }
            else
            {
            	var qty = parseFloat($('#qty').val());
            	var price = parseFloat($('#amountperpeice').val());
              $('#pum').html($('#amountperpeice').val());
              var processcost = parseFloat($('#puc').html());
              price = price * qty;
              var totalProcessingCost = parseFloat($('#puc').html()) * qty;
              $('#cost').val(totalProcessingCost);
              $('#totalMasterCost').val(price);
            	var total = totalProcessingCost + price ;
               total = Math.round(total * 100) / 100;
            	$('#totalCost').val(total);
               var perpiececost = total / parseFloat($('#qty').val());
               $('#putc').html(perpiececost);
               $('#rpp').val(perpiececost);
              totalCost(qty);
            }
          }, 1000);
        }

        function totalCost(qty)
        { 
            var Cost = parseFloat($('#cost').val());
            var mastercost = parseFloat($('#totalMasterCost').val());
            var totalCost = Cost + mastercost;
            // var perpiececost = totalCost / parseFloat($('#qty').val());

            // $('#totalCost').val(totalCost);
            // $('#rpp').val(perpiececost); 
        }

        


        $('#btnFinalSubmit').click(function(e){
          if ($('#finished').val() == "") 
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
          else if($('#qty').val() >  parseInt($('#limit_id').val()))
          {
            swal("Error", "You can only make "+parseInt($('#limit_id').val())+" qty:)", "error");  
          }
          else if($('#master').val() == "")
          {
            swal("Error", "Please select master :)", "error");
          }
          else if($('#amountperpeice').val() == "")
          {
            swal("Error", "Please enter amount per piece :)", "error");
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
                    data : {_token : "{{csrf_token()}}", finished:$('#finished').val(),qty:$('#qty').val(),master:$('#master').val(),cost:$('#cost').val(),mastercost:$('#totalMasterCost').val(),rp:$('#rp').val()},
                    dataType : 'json',
                    success : function(result){
                       	if (result == 1) 
                       	{
                       		 window.location = "{{url('/job-order')}}";
                       	}
                    }
            });
          }
        });

        function getRecipyLimit()
        {
          $.ajax({
                    url : "{{url('/recipy-limit')}}",
                    type : "POST",
                    data : {_token : "{{csrf_token()}}", id:$('#finished').val()},
                    success : function(result){
                      $('#limit_id').val(result);
                      if(result != "")
                      {
                        $('#limit').html('');
                        $('#limit').html('You can make only '+parseInt(result)+" Qty");
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



  </script>

@endsection