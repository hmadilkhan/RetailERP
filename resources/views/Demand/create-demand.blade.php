@extends('layouts.master-layout')

@section('title','CREATE DEMAND ORDERS')

@section('breadcrumtitle','CREATE DEMAND ORDERS')

@section('navbranchoperation','active')
@section('navdemand','active')

@section('content')

<section class="panels-wells">
    <div class="panel m-b-30">
          <div class="card-header">
                <h2 class="card-header-text">Create Demand Orders </h2>
                <span class="card-header-text f-right" id="poNumber">{{$demandinfo[0]->status}}</span>
                <br/>
                <div class="new-users-more text-left p-t-10">
                              <a href="{{ url('/demand') }}" ><h6 class="m-b-0 f-w-400 text-primary"><i class="icofont icofont-arrow-left"></i>Back to list</h6></a>
                           </div>
            </div>
         <div class="panel-body">

            <div class="row">

                <!-- vendor select box -->
                <div class="col-lg-4 ">

                    <div class="form-group">
                       <label class="card-header-text">From</label> <br>
                       
                        <label class="form-control-label">Branch Manager</label><br>
                        <label class="form-control-label">{{$sender[0]->branch_name}}</label><br>
                        <label class="form-control-label">{{$sender[0]->branch_address}}</label><br>
                    </div>
                </div>
                <!-- Date -->
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="card-header-text">To</label> <br>
                    
                    <label class="form-control-label">Administrator</label><br>
                        <label class="form-control-label">{{$reciver[0]->branch_name}}</label><br>
                        <label class="form-control-label">{{$reciver[0]->branch_address}}</label><br>

                    </div>

                </div> 
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="card-header-text">Demand Order |<span id="demandid"> {{$demandinfo[0]->id}}</span></label> <br>
                    
                    <label class="form-control-label">Created on: {{$demandinfo[0]->date}}</label><br>
                       

                    </div> 
             </div>
           </div>
         </div>
 </div>        


<div class="panel">
     <div class="card-header">
         <div class="col-md-6">
          <h4 class="card-header-text m-b-20">Live Stock<p style="margin-top:-1px;">Items near to finish</p></h4>
                           <button type="button" id="btnadd" class="btn btn-primary btn-sm btn-icon waves-effect waves-light f-right"  data-toggle="modal" data-target="#product-modal">
                            <i class="icofont icofont-plus" data-toggle="tooltip" data-placement="top" title="Add Product"></i>
                        </button>
         </div>

         <div class="col-md-6">
            <h4 class="card-header-text m-b-20">Item List <p style="margin-top:-1px;">You can add 12 items in 1 demand list</p></h4>
         </div>
     </div>
     <div class="panel-body">
       <div class="row">
          <div class="col-md-6">
            

             <table  class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                 @foreach($neartofinish as $value)
                 <tr>
                   <td>{{$value->product_name}}</td>
                   <td >{{$value->balance}}</td>
                   <td><input type="number" placeholder="0" min="1" name="demandqty" id="demandqty{{$value->id}}" class="form-control" onchange="insert_items('{{$value->id}}',this.id)" /></td>   
                 </tr>
                  @endforeach
                
            </tbody>
        </table>

          </div>
          <div class="col-md-6">
            <table id="item_table" class="table invoice-detail-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

          </div>
       </div>
           <div class="row in">
             <div class="col-lg-12 col-sm-12 m-t-50">
                <div class="button-group ">
                      <button type="button" id="btnFinalSubmit" class="btn btn-md btn-primary waves-effect waves-light  f-right"  >   <i class="icofont icofont-plus"> </i>
                        Submit
                    </button>
                       <a href="{{ url('/demand') }}" id="btndraft" class="btn btn-md btn-default waves-effect waves-light  f-right m-r-20"  ><i class="icofont icofont-save"></i> Save as Draft </a>
                    </div>       
                </div>  
            </div> 
     </div>  


 

</div>

    

       
      

       
            
      
 
               
  

         
        

 <div class="modal fade modal-flex" id="product-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Extra Product Add</h4>
                                 </div>
                                 <div class="modal-body">
                                   <div class="row">
                                     <div class="col-md-7">
                                       <div class="form-group"> 
                                         <label class="form-control-label">Product</label>
                                         <select class="select2 form-control" data-placeholder="Select Product" id="product" name="product">
                                              <option value="">Select Product</option>
                                              @foreach($products as $value)
                                              <option value="{{$value->id}}">{{ $value->item_code." - ".$value->product_name }}</option>
                                              @endforeach
                                            </select>
                                            </div>
                                           </div>
                                         <div class="col-md-5">
                                          <div class="form-group"> 
                                          <label class="form-control-label">Quantity</label>  
                                             <input type="number" placeholder="0" name="qty" id="qty" class="form-control" />
                                            </div>
                                          </div>
                                      </div>   
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Add Product</button>
                                 </div>
                              </div>
                           </div>
                        </div> 


                        <div class="modal fade modal-flex" id="quantity-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-sm" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Enter Quantity</h4>
                                 </div>
                                 <div class="modal-body">
                                   
                                     
                                         <input type="hidden" name="hidd_id" id="hidd_id">
                                          <div class="form-group"> 
                                          <label class="form-control-label">Quantity</label>  
                                             <input type="number" placeholder="0" name="qtyupdate" id="qtyupdate" class="form-control" />
                                            </div>
                                          </div>
                                      
                                 
                                 <div class="modal-footer">
                                    <button type="button" id="btn_updateqty" class="btn btn-success waves-effect waves-light">Update Quantity</button>
                                 </div>
                              </div>
                           </div>
                        </div> 







    </section>   
@endsection


@section('scriptcode_three')
<script type="text/javascript">

  $(".select2").select2();



$("#btn_extra_item").on('click',function(){

    insert_items($('#product').val(),'qty');

});

$("#btn_updateqty").on('click',function(){



    update_items($('#hidd_id').val(),$("#qtyupdate").val());

});


function del_items(id){

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
                        url: "{{url('/deleteitem')}}",
                        type: 'DELETE',
                        data:{_token:"{{ csrf_token() }}",productid:id,demandid:$('#demandid').html()},
                        dataType:"json",
                        success:function(resp){
                            if(resp.r == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Item has been deleted.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                          if(resp.c == 1){
                                             window.location= "{{ url('/demand') }}";
                                          }else {
                                            get_demand_items();
                                          }
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



function  update_items(id, qty){
    if(qty > 0){
              $.ajax({
                    url : "{{url('/updateitem')}}",
                    type : "PUT",
                    data : {_token : "{{csrf_token()}}",productid:id,qty:qty},
                    success : function(resp){
                      
                        if(resp == 1){
                          get_demand_items();
                          $("#quantity-modal").modal('hide');
                          $("#qtyupdate").val(0);
                        }

                      }
                    }); 
    }else {
            alert("Quantity 0 not acceptable!");
    } 

}

  function insert_items(id, qty)
  {
       if($('#'+qty).val() > 0){


            $('#'+qty).attr('disabled','disabled');
                   
              $.ajax({
                    url : "{{url('/additems')}}",
                    type : "POST",
                    data : {_token : "{{csrf_token()}}",demandid:$('#demandid').html(),productid:id,qty:$('#'+qty).val()},
                    dataType:"json",
                    success : function(resp){
                      get_demand_items();
                      if(resp.r == 1){
                         if(resp.c == 1){
                            new_generate(resp.msg,1);
                         }else {
                            new_generate(resp.msg,2);
                         }
                      }

                      if(qty == "qty"){
                         $("#product-modal").modal("hide");
                         $('#'+qty).attr('disabled',false).val('');
                         $('#product').val('').change();
                      }
                    }
                    });
          }else {
            alert("Quantity 0 not acceptable!");
          } 


  }


  function new_generate(title,state){
     
       if(state == 1){
                  swal({
                            title: title,
                            text: "Please create new Demand Order!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "YES",
                            cancelButtonText: "NO",
                            closeOnConfirm: false,
                            closeOnCancel: false
                          },
                          function(isConfirm){
                            if(isConfirm){
                                       $.ajax({
                                          url: "{{url('/updatestatus')}}",
                                          type: 'PUT',
                                          data:{_token:"{{ csrf_token() }}",demandid:$('#demandid').html(),statusid:2},
                                          success:function(resp){
                                              if(resp == 1){
                                                   swal({
                                                          title: "Demand Created",
                                                          text: "Demand Order Created Successfully!",
                                                          type: "success"
                                                     },function(isConfirm){
                                                         if(isConfirm){
                                                               window.location= "{{ url('/demand') }}";
                                                         }
                                                     });
                                               }
                                          }

                                      });
                                
                             }else {
                                swal("Cancelled", "Your Item is safe :)", "error");
                             }
                          });
              }else if(state == 2){

                    swal("Alert!",title, "error");
              }    

  }


  function get_demand_items(){

    $.ajax({
      url : "{{url('/viewitems')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}",demandid:$('#demandid').html()},
      dataType:"json",
      success : function(result){
           
            $("#item_table tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#item_table tbody").append(
                          "<tr>" +
                            "<td class='pro-name'>"+result[count].product_name+"</td>" +
                            "<td>"+result[count].qty+"</td>" +  
                            "<td class='action-icon'><i onclick='getid("+result[count].id+")' class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i onclick='del_items("+result[count].id+")' class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                          "</tr>"
                         );
                    }

      }

    });
  }
  $(document).ready(function(){
  $("btnadd").click(function(){
    $("addproduct").toggle();
  });
});
  function getid(id){
        $("#quantity-modal").modal("show");
        $("#hidd_id").val(id);

  }

$("#btnFinalSubmit").on('click',function(){

    $.ajax({
            url: "{{url('/updatestatus')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",demandid:$('#demandid').html(),statusid:2},
            success:function(resp){
                if(resp == 1){
                     swal({
                            title: "Demand Created",
                            text: "Demand Order Created Successfully!",
                            type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                                 window.location= "{{ url('/demand') }}";
                           }
                       });
                  }
             }

          });        

});
</script> 
@endsection
