@extends('layouts.master-layout')

@section('title','Job Order List')

@section('breadcrumtitle','Add Expense')

@section('navjoborder','active')
@section('navrepeatorder','active')
@section('content')

  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Job Order List</h5>
         <a href="{{ url('repeat-job') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Customer" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE NEW JOB ORDER
              </a>
         
         </div>      
       <div class="card-block">

        <div class="row">
      <div class="col-lg-12">

         <table class="table table-striped nowrap dt-responsive" width="100%">
                                                 <thead>
                                                    <tr>   
                                                       <th>Item Name</th>
                                                       <th>Master Name</th>
                                                       <th>Order Date</th>
                                                       <th>Total Qty</th>
                                                       <th>Received Qty</th>
                                                       <th>Status</th>
                                                       <th>Action</th>
                                                    </tr>
                                                 </thead>
                                                 <tbody>
                                                   @if($production)
                                                    @foreach($production as $value)
                                                      <tr>
                                          
                                                                  <td>{{$value->product_name}}</td>
                                                                  <td>{{$value->mastername}}</td>
                                                                  <td>{{date("d F Y",strtotime($value->assDate))}}</td>
                                                                  <td>{{$value->Total_qty}}</td>
                                                                  <td>{{$value->Received_qty}}</td>
                                                                  <td>{{$value->statusname}}</td>

                                                                  <td class="action-icon">

                                                                        <i class="icofont icofont-vehicle-delivery-van f-20  text-{{($value->Total_qty == $value->Received_qty) ? 'muted' : 'success'}}" data-toggle="tooltip"  onclick="onDelivery('{{$value->job_order_id}}','{{$value->Total_qty}}','{{$value->finished_good_id}}','{{$value->Received_qty}}')" data-placement="top" title="" data-original-title="Received"></i>&nbsp;


                                                                        <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->job_order_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancel"></i>
                                                                  </td>  
                                                            </tr>
                                                    @endforeach
                                                  @endif
                                                 </tbody>
                                              </table>
                                  
                               
                             </div>
                           </div>
                         </div>
    
           
    </div>
   </div>
</section>
@endsection


@section('scriptcode_three')

  <script type="text/javascript">



   $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Job Order',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

   //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");
    alert(id);

      swal({
          title: "Are you sure?",
          text: "Your will not be able to reopen this Job Order!",
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
                        url: "{{url('/job-cancel')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                          console.log(resp);
                            if(resp == 1){
                                 swal({
                                        title: "Cancelled",
                                        text: "Job Cancelled Succesfully.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{url('job-order')}}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Job Order is safe :)", "error");
           }
        });
  });
function onDelivery(jobid,qty,itemid,received){
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
                    console.log("Receiving");
                    received_finished_goods(jobid,itemid,inputValue,received,qty);
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

 function received_finished_goods(jobid,itemid,recivedqty,received,qty)
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
          url: "{{url('/received-product')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          jobid:jobid,
          itemid:itemid,
          recivedqty:recivedqty,
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
                          window.location="{{url('/job-order')}}";
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
  
  </script>

@endsection