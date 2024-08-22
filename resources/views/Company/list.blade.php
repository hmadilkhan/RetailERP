@extends('layouts.master-layout')

@section('title','Company')

@section('breadcrumtitle','Company')

@section('navcompany','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Companies</h5>
         <a href="{{url('/createcompany')}}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Company
              </a>

         </div>
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Company Name</th>
               <th>City</th>
               <th>Mobile</th>
               <th>Email</th>
               <th>Address</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
              @foreach($company as $value)
                    <tr>

                          <td>{{$value->name}}</td>
                          <td>{{$value->city_name}}</td>
                          <td>{{$value->mobile_contact}}</td>
                          <td>{{$value->email}}</td>
                          <td>{{$value->address}}</td>
                          <td>{{$value->status_name}}</td>
                          <td class="action-icon">

                                <a href="{{ url('/branch-edit') }}/{{ $value->company_id }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>

                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->company_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

                             </td>
                    </tr>
                    @endforeach
         </thead>
         <tbody>


         </tbody>


     </table>
  </div>
</div>
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">
  //Alert confirm
 // $('.alert-confirm').on('click',function(){
 //    var id= $(this).data("id");

 //      swal({
 //          title: "Are you sure?",
 //          text: "Your will not be able to recover this imaginary file!",
 //          type: "warning",
 //          showCancelButton: true,
 //          confirmButtonClass: "btn-danger",
 //          confirmButtonText: "delete it!",
 //          cancelButtonText: "cancel plx!",
 //          closeOnConfirm: false,
 //          closeOnCancel: false
 //        },
 //        function(isConfirm){
 //          if(isConfirm){
 //                     $.ajax({
 //                        url: "{{url('/removebranch')}}",
 //                        type: 'PUT',
 //                        data:{_token:"{{ csrf_token() }}",
 //                        id:id,
 //                        },
 //                        success:function(resp){
 //                            if(resp == 1){
 //                                 swal({
 //                                        title: "Deleted",
 //                                        text: "Do you want to remove branch.",
 //                                        type: "success"
 //                                   },function(isConfirm){
 //                                       if(isConfirm){
 //                                        window.location="{{ url('/branches') }}";
 //                                       }
 //                                   });
 //                             }
 //                        }

 //                    });

 //           }else {
 //              swal("Cancelled", "Your branch is safe :)", "error");
 //           }
 //        });
 //  });

</script>
@endsection

