@extends('layouts.master-layout')

@section('title','Master Work-Load')

@section('breadcrumtitle','Add Expense')

@section('navmaster','active')
@section('navmasterworkload','active')

@section('content')
    
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Master Work-Load Details</h5>
         <h5 class=""><a href="{{ url('work-load') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
         
         </div>      
       <div class="card-block">
    
           <div class="project-table">
                 <table id="mainTable" class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>
               <th>Receipt No</th>
               <th>Order Date</th>
               <th>Delivery Date</th>
               <th>Finished Good</th>
               <th>Qty</th>
               <th>Received</th>
               
               <th>Action</th>
               

            </tr>
         </thead>
         <tbody>
      
         	 @if($data)
                        @foreach ($data as $value)
      			              <tr>
      			                 <td onclick="getBill('{{$value->receipt_no}}')">{{$value->receipt_no}}</td>
                             <td>{{$value->date}}</td>
                             <td>{{$value->delivery_date}}</td>
      			                 <td>{{$value->finished}}</td>
                             <td>{{$value->qty}}</td> 
                             <td>{{$value->received}}</td> 
                             
                             <td class="action-icon">
                                <i class="icofont icofont-vehicle-delivery-van f-20  text-{{($value->status > 2) ? 'muted' : 'success'}}" data-toggle="tooltip"  onclick="onDelivery('{{$value->assign_id}}','{{$value->qty}}','{{$value->finished_good_id}}','{{$value->received}}','{{$value->receipt_id}}')" data-placement="top" title="" data-original-title="Received"></i>&nbsp;


                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->assign_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancel"></i>
                            </td>                          
      			             </tr>
                     	@endforeach
                    @endif
     
         </tbody>
     </table>
        </div>
    </div>
   </div>
</section>

<div class="modal fade modal-flex" id="product-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 id="mod-title" class="modal-title">Receipt Details</h4>
                                 </div>
                                 <div class="modal-body">
                       <div class="row">
                        <div class="col-md-3">
                          <label class="f-w-600">Receipt No :</label>
                        </div>
                        <div class="col-md-3">
                          <label id="receiptno" class="">1234564897978</label>
                        </div>
                        <div class="col-md-3">
                          <label class="f-w-600 f-right">Order Date :</label>
                        </div>
                        <div class="col-md-3">
                          <label id="date" class="">2012-02-12</label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label class="f-w-600">Customer Name:</label>
                        </div>
                        <div class="col-md-3">
                          <label id="name" class="">Muhammad Adil Khan</label>
                        </div>
                        <div class="col-md-3">
                          <label class="f-w-600 f-right">Contact :</label>
                        </div>
                        <div class="col-md-3">
                          <label id="mobile" class="">0311-1234567</label>
                        </div>
                      </div>  
                      <div class="row">
                        <div class="col-md-3">
                          <label class="f-w-600">Order Type:</label>
                        </div>
                        <div class="col-md-3">
                          <label id="type" class="">Take Away</label>
                        </div>
                        <div class="col-md-3">
                          <label class="f-w-600 f-right">Status</label>
                        </div>
                        <div class="col-md-3">
                          <label id="status" class="">Pending</label>
                        </div>
                      </div>  
                       <div class="row">
                         <div class="col-md-12">
                           <table id="tablemodal" class="table " width="100%"  cellspacing="0">
                              <thead>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Amount</th>

                              </thead>
                              <tbody>
                                
                              </tbody>
                           </table>
                          </div>
                             
                          </div>   

                           <hr/>
                     <div class="row">
                        <div class="col-md-6">
                          <label class="f-w-600 f-left">Total Amount :</label>
                        </div>
                        <div class="col-md-6">
                          <label id="tamount" class="f-right">10000</label>
                        </div>
                        <div class="col-md-6">
                          <label class="f-w-600 f-left">Advance :</label>
                        </div>
                        <div class="col-md-6">
                          <label id="receive" class="f-right">1000</label>
                        </div>
                        <div class="col-md-6">
                          <label class="f-w-600 f-left">Bal. Amount :</label>
                        </div>
                        <div class="col-md-6">
                          <label id="bal" class="f-right">10000</label>
                        </div>
                      </div>
                                 </div>
                                 <div class="modal-footer">
                                    <!-- <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Add Product</button> -->
                                 </div>
                              </div>
                           </div>
                        </div> 
@endsection


@section('scriptcode_three')

  <script type="text/javascript">



   $('#mainTable').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Customer',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

   //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this customer!",
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
                        url: "{{url('/remove-master')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove customer.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{url('/get-masters')}}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Customer is safe :)", "error");
           }
        });
  });

 function onDelivery(jobid,qty,itemid,received,receipt_id){
if (qty == received) 
{
  swal_alert("Error Message !","Product Cannot be Received","error",false);
}
else{

    swal({
      title: "Confirmation Message?",
      text: "Do You want to Received this product!",
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
              title: "Received Amount!",
              text: "Enter Received Amount!:",
              type: "input",
              showCancelButton: true,
              closeOnConfirm: false,
              inputPlaceholder: "Should be greater than 0"
            }, function (inputValue) {
              if (isNaN(inputValue)) 
              {
                swal_alert("Error Message !","Input is not in correct Format","error",false);
              }
              else if(inputValue < 0){
                swal_alert("Error Message !","Negative value is not allowed","error",false);
              }
              else if(inputValue > 0){
                received_finished_goods(jobid,itemid,inputValue,received,qty,receipt_id);
              }
              else{
                swal_alert("Error Message !","Input value must be greater than zero","error",false);
              }
            });
          }else
          {
            swal("Cancelled", "User Cancelled the Operation :)", "error");
          }
    });
 }

 }

 function received_finished_goods(jobid,itemid,recivedqty,received,qty,receipt_id)
 {
  var totalQty = qty - received;
  if(recivedqty == 0)
  {
    swal_alert("Error Message !","Cannot Received Zero","error",false);
  }
  else if (recivedqty > totalQty) 
  {
    swal_alert("Error Message !","You can only received "+totalQty,"error",false);
  }
  else
  {
    $.ajax({
          url: "{{url('/received-from-master')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          id:jobid,
          itemid:itemid,
          recivedqty:recivedqty,
          receipt_id:receipt_id,
          },
          success:function(resp){
            console.log(resp);
              if(resp == 1){
                   swal({
                          title: "Success",
                          text: "Received Successfully",
                          type: "success"
                     },function(isConfirm){
                         if(isConfirm){
                          window.location="{{url('/work-load',$masterID)}}";
                         }
                     });
               }
          }

      });
  }
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

   function getBill(receiptNo)
     {
        
        $.ajax({
          url: "{{url('/get-receipt')}}",
          type: 'POST',
          async : false,
          data:{_token:"{{ csrf_token() }}",
          receipt:receiptNo,
          },
          success:function(resp){
              console.log(resp[0].receipt_no);
              $('#receiptno').html(resp[0].receipt_no);
              $('#date').html(resp[0].date);
              $('#name').html(resp[0].name);
              $('#mobile').html(resp[0].mobile);
              $('#type').html(resp[0].order_mode);
              $('#status').html(resp[0].order_status_name);
              $('#tamount').html(parseFloat(resp[0].total_amount).toLocaleString());
              $('#receive').html(parseFloat(resp[0].receive_amount).toLocaleString());
              let bal = parseFloat(resp[0].total_amount) - parseFloat(resp[0].receive_amount);
              $('#bal').html(bal.toLocaleString());

                $("#tablemodal tbody").empty();
              for(var count =0;count < resp.length; count++){
                $("#tablemodal tbody").append(
                  "<tr>" +
                                  "<td >"+resp[count].product_name+"</td>" +  
                                  "<td >"+resp[count].total_qty+"</td>" +  
                                  "<td '>0.00</td>" + 
                                "</tr>"
                      );
               }

          }

      });
        $('#product-modal').modal("show");
     }
  </script>

@endsection