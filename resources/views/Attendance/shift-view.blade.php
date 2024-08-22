@extends('layouts.master-layout')

@section('title','Office Shift')

@section('breadcrumtitle','Office Shift')

@section('navattendance','active')
@section('navshift','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Office Shift Details</h5>
         <a href="{{url('/show-shift')}}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Shift
              </a>

         </div>      
       <div class="card-block">

     <table  class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
        
         <thead>
            <tr>
               <th>Branch Name</th>
               <th>Shift Name</th>
               <th>Clock In</th>
               <th>Late Count</th>
               <th>Clock Out </th>
               <th>Early Count</th>
               <th>ATT Hours</th>
               <th>Action</th>
            </tr>
            </thead>
              <tbody>
                    @foreach($getshifts as $value)
                    <tr>
                          <td>{{$value->branch_name}}</td>
                          <td>{{$value->shiftname}}</td>
                          <td>{{date('h:i a',strtotime($value->shift_start))}}</td>
                          <td>{{($value->grace_time_in)}}</td>
                          <td>{{date('h:i a',strtotime($value->shift_end))}}</td>
                          <td>{{($value->grace_time_out)}}</td>
                          <td>{{$value->ATT_time}} Hours</td>
                          <td class="action-icon">
                               
                                <a href="{{ url('/show-editshift') }}/{{ $value->shift_id }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                       <!--          <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->shift_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i> -->

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
<script type="text/javascript">

    $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Branch',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

  //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this imaginary file!",
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
                        url: "{{url('/delete-shift')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove shift.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-shift') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Office Shift is safe :)", "error");
           }
        });
  });

</script>
@endsection

