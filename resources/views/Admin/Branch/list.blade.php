@extends('Admin.layouts.master-layout')

@section('title','Branches')

@section('breadcrumtitle','Branches')

@section('navbranch','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Branches</h5>

         <a href="{{url('/create-branch')}}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Branch
              </a>

         </div>      
       <div class="card-block">
 
     <table  class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
              <th>Preview</th>
               <th>Branch Name</th>
               <th>City</th>
               <th>Mobile</th>
               <th>Email</th>
               <th>Address</th>
               <th>Action</th>
            </tr>
            </thead>
            <tbody>
              @foreach($details as $value)
                    <tr>
                        <td class="text-center">
                               <img width="42" height="42" src="{{ asset('assets/images/branch/'.(!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg' }}">
                         </td>
                          <td>{{$value->branch_name}}</td>
                          <td>{{$value->city_name}}</td>
                          <td>{{$value->branch_mobile}}</td>
                          <td>{{$value->branch_email}}</td>
                          <td>{{$value->branch_address}}</td>
                          <td class="action-icon">
                               
                                <a href="{{ url('/edit-branch') }}/{{ $value->branch_id }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->branch_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

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
        displayLength: 50,
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
          text: "Your will not be able to recover this branch again!",
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
                        url: "{{url('/remove-branch')}}",
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
                                        window.location="{{ url('/view-branch') }}";
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

