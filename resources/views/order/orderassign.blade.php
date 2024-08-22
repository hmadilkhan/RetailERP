@extends('layouts.master-layout')

@section('title','Orders')

@section('breadcrumtitle','Orders Panel')

@section('navbranchoperation','active')
@section('navorder','active')

@section('content')



	<section class="panels-wells col-md-9" >
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Order Assign Panel</h5>
         <label class="text-danger f-24 f-w-600 f-right" id="masterCount"></label>
         <a href="{{ url('/orders-view') }}" ><h6 class="m-b-0 f-w-400 text-primary"><i class="icofont icofont-arrow-left"></i>Back to list</h6></a>
     </div>
     <div class="card-block">
     	<div class="row">

           <div class="col-md-4">
            <label>Finished Good</label>
           	  <select  class="f-right select2" data-placeholder="Select Product" id="finishedgood" name="finishedgood">
                 	<option value="">Select Product</option>
	                   @foreach($items as $value)
                     @if($value->status != 2)
                       <option value="{{ $value->id }}">{{ $value->product_name }}</option>
                     @endif
	                     
	                   @endforeach
              	</select>
           </div>
            <div class="col-lg-4  col-sm-4">
                    <div class="form-group">
                         <label>Quantity</label>
                           <input type="text" placeholder="0" name="qtyfinished" id="qtyfinished"  class="form-control"   />
                        <span class="help-block"></span>
                    </div>
                </div>  
           <div class="col-md-4">
            <label>Master</label>
           		<select  class="f-right select2" data-placeholder="Select Master" id="master" name="master">
                 	<option value="">Select Master</option>
	                   @foreach($master as $value)
	                     <option value="{{ $value->id }}">{{ $value->name }}</option>
	                   @endforeach
              	</select>
           </div>
           <div class="col-md-4">
              
           </div>
         </div>
         <hr/>

         <div class="row">
                 <!-- product select box -->
                 <input type="hidden" name="updateid" id="updateid"/>
                <div class="col-lg-3  col-sm-12">
                    <div class="form-group">
                         <label>Product</label>
                           <select class="select2 form-control" data-placeholder="Select Raw Materials" id="raw" name="raw">
                            <option value="">Select Raw Materials</option>
                             @if($raw)
                                  @foreach($raw as $val)
                                    <option value="{{$val->id}}">{{$val->product_name}}</option>
                                  @endforeach
                             @endif  
                           </select>
                        <span class="help-block"></span>
                    </div>
                </div>  

                 <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Unit of Measure</label>
                           <select class="select2 form-control" data-placeholder="UOM" id="uom" name="uom">
                            <option value="">UOM</option>
                            @if($uom)
                                  @foreach($uom as $val)
                                    <option value="{{$val->uom_id}}">{{$val->name}}</option>
                                  @endforeach
                             @endif
                           </select>
                        <span class="help-block"></span>
                    </div>       
                </div>
                 <!-- qty select box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Quantity</label>
                           <input type="text" placeholder="0" name="qty" id="qty" value="1" class="form-control" onchange="qty_change()"  />
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

                <!-- Amount box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                        <label>Amount</label>
                           <input type="text" readonly="readonly"  name="amount" placeholder="0" id="amount" class="form-control" />
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

             <hr/>

             <table id="item_table" class="table invoice-detail-table" width="100%"  cellspacing="0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Of Measure</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <hr/>
        <div class="row col-md-12">
          <button type="button" id="btnfinal" class="btn btn-primary btn-md f-right">Submit</button>
        </div>
     </div>
  </section>

  <section class="panels-wells col-md-3">
        <div class="col-xl-12 col-lg-12 grid-item">
                     <div class="card">
                        <div class="card-header">
                           <h5 class="card-header-text text-center f-24">Assigned Orders</h5>
                        </div>
                        <div class="row">
                           <div class="col-sm-12">
                            @foreach($assign as $value)
                              <div class="widget-timeline">                                   
                                 <div class="media col-sm-12 " style="margin-top: -25px;">
                                    <div class="media-body" style="margin-top: -25px;">
                                       <h2 class="f-w-600 d-inline-block f-24">{{$value->product}}</h2>
                                       <span class="f-24"><b> |  <b>
                                        <a  class="f-14 text-primary" onclick="get_details('{{$value->assign_id}}','{{$value->product}}')">View Details</a></span>
                                        <p class="f-18">Qty : {{$value->qty}}<b><b></p>
                                        <p class="f-18">Master : {{$value->master}}<b><b></p>
                                       <p class=""></p>
                                    </div>
                                    <hr>
                                 </div>
                              </div>
                            @endforeach
                           </div>
                        </div>
                     </div>
                  </div>
    
        </div>
  </section>


  <div class="modal fade modal-flex" id="product-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-lg" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 id="mod-title" class="modal-title"></h4>
                                 </div>
                                 <div class="modal-body">
                                   <div class="row">
                                     <div class="col-md-12">
                                       <table id="tablemodal" class="table " width="100%"  cellspacing="0">
                                          <thead>
                                            <th>Product Name</th>
                                            <th>Unit of Measure</th>
                                            <th>Qty</th>
                                            <th>Amount</th>
                                          </thead>
                                          <tbody>
                                            
                                          </tbody>
                                       </table>
                                      </div>
                                         
                                      </div>   
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Add Product</button>
                                 </div>
                              </div>
                           </div>
                        </div> 




@endsection
@section('scriptcode_three')
<script type="text/javascript">
  
  $(".select2").select2();
  $('#finishedgood').change(function(){
    $('#finishedgood').attr('disabled','disabled');
    $.ajax({
            url: "{{url('/get-items')}}",
            type: 'POST',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            master:$('#master').val(),
            finished:$('#finishedgood').val(),
            receipt:'{{$receipt}}',
            },
            success:function(result){

              $('#item_table tbody').empty();
                $.each(result, function( index, value ) {
                        $("#item_table tbody").append(
                          "<tr>" +
                            "<td>"+value.product_name +"</td>" +
                            "<td>"+value.name +"</td>" +
                            "<td>"+value.qty+"</td>" +
                            "<td>"+value.amount+"</td>" +
                            "<td class='action-icon'><i id='btn"+index+"' onclick='updateItems("+value.sub_assign_id+","+value.product_id+","+value.uom_id+","+value.qty+","+value.amount+")' class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i id='btn"+index+"'  class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                          "</tr>"
                         );
                    });
            }
     });
    $.ajax({
            url: "{{url('/get-items-qty')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            finished:$('#finishedgood').val(),
            receipt:'{{$receipt}}',
            },
            success:function(result){
              console.log(result);
              $('#qtyfinished').val(result[0].assignQty);
            }
     });

      

    $.ajax({
            url: "{{url('/get-master-by-category')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            id:$('#finishedgood').val(),
            },
            success:function(result){
              $('#master').empty();
                for(var count=0; count < result.length; count++){
                  $("#master").append("<option value=''>Select Master</option>");
                  $("#master").append(
                    "<option value='"+result[count].id+"'>"+result[count].name+"</option>");
                 }
              // $('#qtyfinished').val(result[0].total_qty);
            }
     });
  });

  $('#master').change(function(){
         $.ajax({
              url: "{{url('/get-master-pending-orders')}}",
              type: 'POST',
              data:{_token:"{{ csrf_token() }}",
              id:$('#master').val(),
              },
              success:function(result){
                console.log(result);
                $('#masterCount').html('');
                $('#masterCount').html(""+result[0].orderCount+" Pending Orders");
                
              }
          });

      });

  var mode = "insert";
  function updateItems(id,product,uom,qty,amount)
  {
    mode = "update";
    alert(mode);
    $('#raw').val(product).change();
    $('#uom').val(uom).change();
    $('#qty').val(qty);
    $('#updateid').val(id);
  }

  $('#raw').change(function(){
  	$.ajax({
            url: "{{url('/uom-by-product')}}",
            type: 'POST',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            id:$('#raw').val(),
            finished:$('#finishedgood').val(),
            receipt:'{{$receipt}}',
          	},
            success:function(result){
            	$('#uom').val(result[0].uom_id).change();
            	$('#price').val(result[0].price);
            	let amount = parseFloat(result[0].price) * parseFloat($('#qty').val());
            	$('#amount').val(amount);
            }
        });

  });
  $('#btnSubmit').click(function(){
    if(mode == "insert")
    {
      $.ajax({
            url: "{{url('/temp-insert-master')}}",
            type: 'POST',
            dataType : 'json',
            data:{_token:"{{ csrf_token() }}",
            // master:$('#master').val(),
            // finished:$('#finishedgood').val(),
            // receipt:'{{$receipt}}',
            product:$('#raw').val(),
            uomid:$('#uom').val(),
            qty:$('#qty').val(),
            amount:$('#amount').val(),
            },
            success:function(result){
             console.log(result);
              if(result == 2)
              {
                alert("Already Exists");
              }
              else
              {
                $('#item_table tbody').empty();
                $.each(result, function( index, value ) {
                        $("#item_table tbody").append(
                          "<tr>" +
                            "<td>"+value.product_name +"</td>" +
                            "<td>"+value.name +"</td>" +
                            "<td>"+value.qty+"</td>" +
                            "<td>"+value.amount+"</td>" +
                            "<td class='action-icon'><i id='btn"+index+"' onclick='updateItems("+value.sub_assign_id+","+value.product_id+","+value.uom_id+","+value.qty+","+value.amount+")' class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i id='btn"+index+"'  class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                          "</tr>"
                         );
                    });
              }
              $('#raw').val('').change();
              $('#uom').val('').change();
              $('#price').val(result[0].price);
              $('#qty').val('1');
              $('#amount').val('');
            }
        });
    }
    else
    {
      $.ajax({
            url: "{{url('/update-assign')}}",
            type: 'POST',
            dataType : 'json',
            data:{_token:"{{ csrf_token() }}",
            master:$('#master').val(),
            finished:$('#finishedgood').val(),
            receipt:'{{$receipt}}',
            product:$('#raw').val(),
            uomid:$('#uom').val(),
            qty:$('#qty').val(),
            amount:$('#amount').val(),
            id:$('#updateid').val(),
            },
            success:function(result){

                $('#item_table tbody').empty();
                $.each(result, function( index, value ) {
                        $("#item_table tbody").append(
                          "<tr>" +
                            "<td>"+value.product_name +"</td>" +
                            "<td>"+value.name +"</td>" +
                            "<td>"+value.qty+"</td>" +
                            "<td>"+value.amount+"</td>" +
                            "<td class='action-icon'><i id='btn"+index+"' onclick='updateItems("+value.sub_assign_id+","+value.product_id+","+value.uom_id+","+value.qty+","+value.amount+")' class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i id='btn"+index+"'  class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                          "</tr>"
                         );
                    });

                $('#raw').val('').change();
                $('#uom').val('').change();
                $('#price').val(result[0].price);
                $('#qty').val('1');
                $('#amount').val('');
              
            }
        });
    }

  		
   
  	});

  function qty_change()
  {
    let qty = parseFloat($('#qty').val());
    let price = parseFloat($('#price').val());
    var amount = qty * price;
    $('#amount').val(amount);
  }

  

  $('#btnfinal').click(function(){

      $.ajax({
            url: "{{url('/get-status-changed')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            finished:$('#finishedgood').val(),
            master:$('#master').val(),
            receipt:'{{$receipt}}',
            qty: $('#qtyfinished').val(),
            },
            success:function(result){
              console.log(result);
              if (result == 1) { window.location = "{{url('order-assign',$receipt)}}";}
            }
        });
  });

  function get_details(assign,product){
    
    $('#product-modal').modal('show');
    $('#mod-title').html(product);
    $.ajax({
            url: "{{url('/get-items-details')}}",
            type: 'POST',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            assign:assign,
            },
            success:function(result){
              console.log(result);
              $('#tablemodal tbody').empty();
                $.each(result, function( index, value ) {
                  $("#tablemodal tbody").append(
                    "<tr>" +
                      "<td>"+value.product_name +"</td>" +
                      "<td>"+value.name +"</td>" +
                      "<td>"+value.qty+"</td>" +
                      "<td>"+value.amount+"</td>" +
                    "</tr>"
                   );
              });
            }
     });
  }

  

 
 </script>
@endsection