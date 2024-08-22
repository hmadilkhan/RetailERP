@extends('layouts.master-layout')

@section('title','Departments')

@section('breadcrumtitle','View Departments')

@section('navmanage','active')

@section('navdepartments','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Department List</h5>
         <a href="{{ url('show-departments') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Department
              </a>
         </div>      
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               <th>Department No.</th>
               <th>Branch Name</th>
               <th>Department Name</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
          
           @foreach($departments as $value)
                 <tr>
                   <td >{{$value->department_id}}</td>
                   <td >{{$value->branch_name}}</td>
                   <td >{{$value->department_name}}</td>
            
                 <td class="action-icon">
                    
                     <a href="{{ url('/edit-departments-show') }}/{{ $value->department_id }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                   <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->department_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

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
                        url: "{{url('/remove-departments')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove department.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-departments') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Department is safe :)", "error");
           }
        });
  });
 </script>

@endsection
