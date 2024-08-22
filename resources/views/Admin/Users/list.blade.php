@extends('Admin.layouts.master-layout')

@section('title','Users-Details')

@section('breadcrumtitle','View Vendor')

@section('navuser','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Users List</h5>
         <a href="{{ url('create-users') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Vendor" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE USER
              </a>
              <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
         </div>      
       <div class="card-block">
    
           <div class="project-table">
                 <table class="table table-striped nowrap dt-responsive" width="100%">
                     <thead>
                        <tr>
                           <th>Logo</th>
                           <th>Id</th>
                           <th>Full name</th>
                           <th>User name</th>
                           <th>Role</th>
                           <th>Branch</th>
                           <th>Status</th>
                           <th>Action</th>
                           
                        </tr>
                     </thead>
                     <tbody>
                      @if($getusers)
                    @foreach($getusers as $value)
                    <tr>
                         <td class="text-center">
                                   <img width="42" height="42" src="{{ asset('public/assets/images/users/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                             </td>
                          <td>{{$value->id}}</td>
                          <td>{{$value->fullname}}</td>
                          <td>{{$value->username}}</td>
                          <td>{{$value->role}}</td>
                          <td>{{$value->branch_name}}</td>
                          <td>{{$value->status_name}}</td>
                          <td class="action-icon">
                               
                                <a href="{{ url('/edit-users') }}/{{ $value->id }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->authorization_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

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
var rem_id = [];

   $('.table').DataTable({
        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Users',
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
                        url: "{{url('/delete-user')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove branch.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-users') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your branch is safe :)", "error");
           }
        });
  });

  

</script>
@endsection
