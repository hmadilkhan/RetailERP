 @extends('layouts.master-layout')

@section('title','Transfer Orders')

@section('breadcrumtitle','Tranfer Orders')

 @section('navbranchoperation','active')
 @section('navtransfer','active')

@section('navtransferview','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Transfer Order List</h5>
        
         </div>      
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               <th>TO No.</th>
               <th>Demand No.</th>
               <th>Transfer by Branch</th>
               <th>Requested by</th>
               <th>Generation Date</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
         	@if($details)
         	
         	 @foreach($details as $value)
                 <tr>
                   <td>TO-{{$value->transfer_No}}</td>
                   <td >DO-{{$value->demand_id}}</td>
                   <td >{{$value->trans_from}}</td>
                   <td>{{$value->trans_to}}</td>
                   <td >{{$value->date}}</td>
                   <td >
                    @if($value->status_name == "Draft")
                    <span class="tag tag-default">  {{$value->status_name }}</span>
                  @elseif($value->status_name == "Placed")
                    <span class="tag tag-success">  {{$value->status_name }}</span>
                  @elseif($value->status_name == "Approved")
                     <span class="tag tag-info">  {{$value->status_name }}</span>
                  @elseif($value->status_name == "Cancel")
                    <span class="tag tag-danger">  {{$value->status_name }}</span>
                       @elseif($value->status_name == "Delivered")
                    <span class="tag tag-primary">  {{$value->status_name }}</span>
                    @elseif($value->status_name == "Completed")
                    <span class="tag tag-info">  {{$value->status_name }}</span>
                  @endif
                  </td>
                    <td class="action-icon">
                    
                     <a href="{{ url('/view-transfer')}}/{{$value->demand_id}}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt text-primary f-18" ></i> </a>

                    <a class="{{ $value->status_name != 'Placed' ? 'disabled' : '' }} m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $value->status_name != 'Placed' ? 'Disabled' : 'Delete' }}"><i class="icofont icofont-ui-delete text-{{ $value->status_name != 'Placed' ? 'muted' :'danger'}} f-18"  <?php echo ($value->status_name != "Placed" ?  '' : ' onclick="delete_transfer('.$value->transfer_id.')" '); ?>   ></i></a>
                 </td>  
                 </tr>
                  @endforeach
           
          @endif
                
           
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

       function delete_transfer(id){
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
              statusid:7,
          },
              success:function(id){
                  if (id == 1) {
                        swal({
                              title: "Success!",
                              text: "Transfer Order Deleted Successfully :)",
                              type: "success"
                         },function(isConfirm){
                             if(isConfirm){
                              window.location="{{url('/gettransferorders')}}";
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