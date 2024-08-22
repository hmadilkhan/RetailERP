@extends('layouts.master-layout')

@section('title','Designation')

@section('breadcrumtitle','View Designation')

@section('navmanage','active')

@section('navdesignation','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Designation List</h5>
         <a href="{{ url('show-designation') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Designation
              </a>
         </div>      
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               <th>Designation Id.</th>
               <th>Designation Name</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
                @foreach($getdesg as $value)
                 <tr>
                   <td >{{$value->designation_id}}</td>
                   <td >{{$value->designation_name}}</td>
            
                 <td class="action-icon">
                    
                     <a href="{{ url('/edit-designation-show') }}/{{ $value->designation_id }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                   <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->designation_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

                 </td>          
                       
                 </tr>
                  @endforeach
     
                
           
         </tbody>
        
      
     </table>
  </div>
</div>
@endsection

@section('scriptcode_three')

<script type="text/javascript">

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
                        url: "{{url('/remove-designation')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove Designation.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-designation') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Designation is safe :)", "error");
           }
        });
  });
 </script>

@endsection
