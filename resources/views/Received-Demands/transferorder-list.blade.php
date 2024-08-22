 @extends('layouts.master-layout')

@section('title','Transfer Orders')

@section('breadcrumtitle','Tranfer Orders')

 @section('navbranchoperation','active')
 @section('navtransfer','active')

@section('navrequest','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Requested List of Transfer Order</h5>
        
         </div>      
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               <th>TO No.</th>               
               <th>Demanded by</th>
               <th>Generation Date</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
           @foreach($transferlist as $value)
                 <tr>
                   <td>TO-{{$value->transfer_No}}</td>
                   <td >{{$value->demanded_by}}</td>
                   <td >{{$value->date}}</td>
                   <td >
                    @if($value->name == "Draft")
                    <span class="tag tag-default">  {{$value->name }}</span>
                  @elseif($value->name == "Placed")
                    <span class="tag tag-info">  {{$value->name }}</span>
                  @elseif($value->name == "Approved")
                     <span class="tag tag-info">  {{$value->name }}</span>
                  @elseif($value->name == "Cancel")
                    <span class="tag tag-danger">  {{$value->name }}</span>
                       @elseif($value->name == "Delivered")
                    <span class="tag tag-primary">  {{$value->name }}</span>
{{--                    @elseif($value->status_name == "Completed")--}}
{{--                    <span class="tag tag-info">  {{$value->status_name }}</span>--}}
                  @endif
                  </td>
                 <td class="action-icon">
                    
                      <i class="icofont icofont-eye-alt text-primary p-r-10 f-18"  data-toggle="tooltip" data-placement="top" title="" data-original-title="View" onclick="view('{{$value->transfer_id}}')" ></i>

                       <a class="{{ $value->name == 'Delivered' ? 'disabled' : '' }} m-r-10" data-toggle="tooltip" data-placement="top" title="Create Challan" data-original-title="{{ $value->name == 'Delivered' ? 'Disabled' : 'Delete' }}"><i class="icofont icofont-plus text-{{ $value->name == 'Delivered' ? 'muted' :'danger'}} f-18"  <?php echo ($value->name == "Delivered" ?  '' : ' onclick="open_challan('.$value->transfer_id.')" '); ?>   ></i></a>

                        <a class="{{ $value->name == 'Delivered' ? 'disabled' : '' }} m-r-10" data-toggle="tooltip" data-placement="top" title="Reject Transfer Order" data-original-title="{{ $value->name == 'Delivered' ? 'Disabled' : 'Delete' }}"><i class="icofont icofont-ui-delete text-{{ $value->name == 'Delivered' ? 'muted' :'danger'}} f-18"  <?php echo ($value->name == "Delivered" ?  '' : ' onclick="reject('.$value->transfer_id.')" '); ?>   ></i></a>

                 </td> 

                 </tr>
                  @endforeach
          
                
           
         </tbody>
        
      
     </table>
  </div>
</div>

   
</section>
  
          
    @endsection
    @section('scriptcode_three')

    <script>
       $('.table').DataTable({
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Category',
          lengthMenu: '<span></span> _MENU_'
   
        },
        
 
    });
    	function open_challan(id){
        window.location="{{url('/createdeliverychallan')}}"+"/"+id; 
    		   
    	}

      function view(id){
        window.location="{{url('/showtransferdetails')}}"+"/"+id; 
      }

      function reject(id){
        swal({
          title: "Delete",
          text: "Do you want to Delete Transfer Order?",
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
              url: "{{url('/removetransferorder')}}",
              type: "PUT",
              data: {_token:"{{csrf_token()}}",
              id:id,
              statusid:3,
          },
              success:function(id){
                  if (id == 1) {
                        swal({
                              title: "Success!",
                              text: "Transfer Order Deleted Successfully :)",
                              type: "success"
                         },function(isConfirm){
                             if(isConfirm){
                              window.location="{{url('/transferlist')}}";
                             }
                         });

                   }else{
                          swal("Alert!", "Transfer Order not Deleted:)", "error");                       
                   }

              }

             });         
                                 
                    }else {
                         swal("Cancel!", "Your Transfer Order is safe:)", "error");
                    }
       });
      }
    </script>

    @endsection