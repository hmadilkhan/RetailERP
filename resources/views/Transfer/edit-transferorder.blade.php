@extends('layouts.master-layout')

@section('title','Transfer Order')

@section('breadcrumtitle','Edit Transfer Order')

@section('navtransfer','active')
@section('navcreatetrf','active')

@section('content')
 <section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                  	<h5 class="card-header-text"> Edit Transfer Order |
                    </h5>
                     <span class="card-header-text">{{$getdetails[0]->transfer_id}}</span>
              <span class="card-header-text f-right">Draft</span>
                <br/>
                     <h5 class=""><a href="{{ url('/trf_list') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                     
                    </div>
                  <div class="card-block">
                         <div class="row">
                        	    <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Transfer From Branch</label>
                            <select name="branchfrm" id="branchfrm" class="form-control select2" onchange="get_products()" data-placeholder="Select Branch">
                                    <option value="">Select Branch</option>
                                    @if($headoffice)
                                      @foreach($headoffice as $value)

                <option value="{{ $value->branch_id }}">{{ $value->branch_name}}</option>

                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Destination To Branch</label>
                                <select name="branchto" id="branchto"  class="form-control select2" data-placeholder="Select Branch">
                                    <option value="">Select Branch</option>
                                    @if($branches)
                                      @foreach($branches as $value)
<option {{$value->branch_id == $getdetails[0]->branch_to ? 'selected="selected"' : '' }}
 value="{{ $value->branch_id }}">{{ $value->branch_name}}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                          <div class="col-lg-4 col-md-4">
                   <div class="form-group">
                  <label class="form-control-label">Transfer Order Date</label>
                  <input class="form-control" type="text"
                   name="trfdate" id="trfdate" placeholder="DD-MM-YYYY" value="{{$getdetails[0]->date}}" />
                    <div class="form-control-feedback"></div>
              </div>
                        </div>
                      </div>
                          <div class="row">
                              <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Select Product</label>
                                <select name="product" id="product" class="form-control select2" onchange="getstock()"data-placeholder="Select Product">
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                              <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Available Stock</label>
                                <label class="form-control-label f-right text-info" id="stock_status"></label>
                                <input class="form-control" type="text" disabled="disabled"  name="stock" id="stock"/>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                            <div class="col-md-4">
                            <div class="form-group">
                              <label class="form-control-label">Enter Transfer Quantity</label>
                              <input class="form-control" type="number" min="1" name="qty" id="qty" value="0" onchange="add_product()" />
                              <div class="form-control-feedback">
                                <p>Hit Enter to add products</p>
                              </div>
                            </div>
                        </div>


                      </div>
                        <hr>
                             <div class="row">

                              <div class="col-md-12">
                                <h5>Transfer Order Details</h5>
                            <div class="form-group">
      <table id="trftable" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>ID</th>
               <th>Product Image</th>
               <th>Product Name</th>
               <th>Transfer Quantity</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
         </tbody>
        
      
         </table>
                            </div>
                        </div>
                      </div>
                      <div class="row in">
             <div class="col-lg-12 col-sm-12 m-t-50">
                <div class="button-group ">
                      <button type="button" id="btnFinalSubmit" class="btn btn-md btn-success waves-effect waves-light  f-right" onclick="trf_status_change(8)"  >   <i class="icofont icofont-plus"> </i>
                        Submit & Placed
                    </button>

                      <button type="button" id="btndraft" class="btn btn-md btn-default waves-effect waves-light  f-right m-r-20" onclick="trf_status_change(1)"  ><i class="icofont icofont-save"></i> Save as Draft 
                    </button>
                    </div>       
                </div>  
            </div> 
                
                  </div>
               </div>

               <div class="modal fade modal-flex" id="edit-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Change Transfer Quantity</h4>
                                 </div>
                                 <div class="modal-body">
                                   <div class="row">
                                         <div class="col-md-12">
                                          <div class="form-group"> 
                                            <input type="hidden" name="itemid" id="itemid">
                                            <label class="form-control-label">Enter Quantity:</label>
                                             <input type="text"  name="updateqty" id="updateqty" class="form-control" />
                                            </div>
                                          </div>
                                      </div>   
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" id="btn_bank" class="btn btn-primary waves-effect waves-light" onClick="updatetrf($('#itemid').val(),$('#updateqty').val())">Update</button>
                                 </div>
                              </div>
                           </div>
                        </div> 

            </section>    
  
@endsection

@section('scriptcode_three')
<script type="text/javascript">
   $(".select2").select2();
     $('#trfdate').bootstrapMaterialDatePicker({
            format: 'DD-MM-YYYY',
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

     $('#trftable').DataTable({
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Product',
          lengthMenu: '<span></span> _MENU_'
   
        },
    });

     function getstock(){
       $.ajax({
            url: "{{url('/trf_stock')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            productid: $('#product').val(),
            branchid: $('#branchfrm').val(),
          },
            success:function(resp){
                if(resp){
                  if (resp[0].stock < resp[0].reminder_qty) {
                     $('#stock_status').text('Low Stock');
                  }
                  else if(resp[0].stock == null || resp[0].stock == 0){
                    $('#stock_status').text('Out of Stock');  
                  }
                  else{
                  $('#stock_status').text('In Stock');  
                  }

                  $('#stock').val(resp[0].stock);
             }
           }
          });   
     }


     function get_products(){
        $.ajax({
            url: "{{url('/get_products')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            branchid: $('#branchfrm').val(),
          },
            success:function(resp){              
                if(resp){
                $("#product").empty();
                 $("#product").append("<option value=''>Select Product</option>");
                     for(var count=0; count < resp.length; count++){
                      $("#product").append(
                      "<option value='"+resp[count].id+"'>"+resp[count].product_name+"</option>");
                     }
                    }
           }
          }); 
     }

    trf_details();

     function trf_details(){
               $.ajax({
            url: "{{url('/trf_details')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            trfid:'{{$getdetails[0]->transfer_id}}',
          },
            success:function(result){
                if(result){
                   $("#trftable tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#trftable tbody").append(
                          "<tr>" +
                            "<td class='pro-name' >"+result[count].transfer_item_id+"</td>" +
                             "<td class='text-center'><img width='42' height='42' src='../public/assets/images/products/"+((result[count].image != "") ? result[count].image : 'placeholder.jpg')+"' alt='"+result[count].image+"'/></td>" +
                            "<td>"+result[count].product_name+"</td>" +  
                            "<td>"+result[count].Transfer_Qty+"</td>" +  
                            "<td class='action-icon'><i onclick='changeqty("+result[count].transfer_item_id+","+result[count].Transfer_Qty+")' class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i onclick='trf_delete("+result[count].transfer_item_id+")' class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                          "</tr>"
                         );
                    }

                  }
             }
          }); 
     }

      function trf_delete(id){
         swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this product!",
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
                        url: "{{url('/trf_delete')}}",
                        type: 'GET',
                        data:{_token:"{{ csrf_token() }}",
                        trfid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Your Product has been deleted Successfully!!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        trf_details();
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Product is safe :)", "error");
           }
        });

      }

      function trf_status_change(statusid)
      {
        if (statusid == 8) {
          
            $.ajax({
            url: "{{url('/trf_change_status')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            id:'{{$getdetails[0]->transfer_id}}',
            statusid:statusid,
          },
            success:function(resp){
              shipment();
             }

          }); 
        }
        else{
       
        $.ajax({
            url: "{{url('/trf_change_status')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            id:'{{$getdetails[0]->transfer_id}}',
            statusid:statusid,
          },
            success:function(resp){
                if(resp){
                     swal({
              title: "Success",
              text: "Transfer Order Successfully Created!",
              type: "success"
              },function(isConfirm){
                 if(isConfirm){
                   window.location="{{ url('/trf_list') }}";
                 }
             });
                  }
             }

          });  
        }
      }

      function changeqty(id, qty){
        $("#edit-modal").modal("show");
        $("#itemid").val(id);
        $("#updateqty").val(qty);

      }

      function updatetrf(id, qty)
      { 
      $.ajax({
            url: "{{url('/qty_update')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            id:id,
            qty:qty,
          },
            success:function(resp){
                if(resp){
                     swal({
              title: "Updated",
              text: "Quantity Updated Successfully!",
              type: "success"
              },function(isConfirm){
                 if(isConfirm){
                  $("#edit-modal").modal("hide");
                     trf_details();
                 }
             });  
            }
             }

          });  
      }

      function shipment(){
        swal({
          title: "Confirmation Message?",
          text: "Do You want to Include Shipment Charges!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-info",
          confirmButtonText: "Yes plx!",
          cancelButtonText: "Cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){

              swal({
      title: "Shipment Amount!",
      text: "Enter Shipment Amount!:",
      type: "input",
      showCancelButton: true,
      closeOnConfirm: false,
      inputPlaceholder: "Should be greater than 0"
    }, function (inputValue) {
      if(inputValue > 0){
        create_dc(inputValue);

      }
      else{
        create_dc(inputValue = 0);
      }
    });
          }
        });
      }


      function create_dc(shipmentamt){
          $.ajax({
            url: "{{url('/insert_direct_chalan')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            transferid: '{{$getdetails[0]->transfer_id}}',
            branchto: $('#branchto').val(),
            shipmentamt: shipmentamt,
          },
            success:function(resp){
                if(resp){
                   window.location="{{ url('/trf_list') }}";
                }
                                 }
        });

      }

       function add_product()
     {
      let con = parseInt($('#stock').val()) < parseInt($('#qty').val());
      if (con == true) {
          swal({
              title: "Error Message",
              text: "Please Enter Valid Quantity!",
              type: "error"
         });
      }
      else if ($('#branchfrm').val() == $('#branchto').val()) {
        swal({
              title: "Error Message",
              text: "Please Select Different Destination Branch!",
              type: "error"
         });

      }
      else {
            $.ajax({
            url: "{{url('/insert_trf')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            trfid:'{{$getdetails[0]->transfer_id}}',
            trfdate: $('#trfdate').val(),
            branchfrom: $('#branchfrm').val(),
            branchto: $('#branchto').val(),
            productid:$('#product').val(),
            qty:$('#qty').val(),
          },
            success:function(resp){
                if(resp == 1){
                     swal({
              title: "Success",
              text: "Item added Successfully!!",
              type: "success"
                        });
                     trf_details();
                  }
                  else if(resp == 0){
                    swal({
              title: "Error",
              text: "Item Already Exsists",
              type: "warning"
                        });

                  }
             }

          });  
      }

     }

   </script>
@endsection