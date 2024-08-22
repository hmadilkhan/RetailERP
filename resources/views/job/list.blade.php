@extends('layouts.master-layout')

@section('title','Job Order List')

@section('breadcrumtitle','Add Expense')

@section('navjoborder','active')
@section('navjobordercreate','active')
@section('content')

  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">RECIPY DETAILS</h5>
         <a href="{{ url('create-job') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Customer" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE NEW RECIPY
              </a>
         
         </div>      
       <div class="card-block">
    
           <div class="project-table">
                 <table class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>   
               <th>Item Name</th>

               <th>Action</th>
            </tr>
         </thead>
         <tbody>
          @if($result)
            @foreach($result as $value)
              <tr>
  
                          <td>{{$value->product_name}}</td>

                          <td class="action-icon">

                                <a href="{{ url('/view-recipy') }}/{{ $value->recipy_id }}" class="icofont icofont-eye-alt f-20  text-primary p-r-10"  data-toggle="tooltip" data-placement="top" title="" data-original-title="View"></a>

                                <a href="{{ url('/edit-job') }}/{{ $value->recipy_id }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delivery"><i class="icofont icofont-ui-edit"></i></a>

                                <!-- <i class="icofont icofont-ui-delete text-danger f-18 p-r-10 alert-confirm" data-id="{{ $value->recipy_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i> -->
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
@endsection


@section('scriptcode_three')

  <script type="text/javascript">



   $('.table').DataTable({

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
                        url: "{{url('/inactivecustomer')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                          console.log(resp);
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove customer.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{route('customer.index')}}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Recipy is safe :)", "error");
           }
        });
  });
function onDelivery(jobid,qty,itemid,received){


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
                  title: "Shipment Amount!",
                  text: "Enter Shipment Amount!:",
                  type: "input",
                  showCancelButton: true,
                  closeOnConfirm: false,
                  inputPlaceholder: "Should be greater than 0"
                }, function (inputValue) {
                  if(inputValue > 0){
                    received_finished_goods(jobid,itemid,inputValue,received,qty);
                  }
                  else{
                    swal_alert("Error Message !","Please input some value","error",false);
                  }
                });
              }
        });
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
              if(resp == 1){
                   swal({
                          title: "Success",
                          text: "Received Successfully",
                          type: "success"
                     },function(isConfirm){
                         if(isConfirm){
                          window.location="{{url('/joborder')}}";
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