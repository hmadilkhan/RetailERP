@extends('layouts.master-layout')

@section('title','Master')

@section('breadcrumtitle','Add Expense')

@section('navmaster','active')
@section('navmasterdetails','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Master Details List</h5>
         <a href="{{ url('create-master') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Customer" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE Master
              </a>
         
         </div>      
       <div class="card-block">
    
           <div class="project-table">
                 <table class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>
      
               <th>Image</th>
               <th>Master Name</th>
               <th>Mobile</th>
               <th>CNIC</th>
               <!-- <th>Credit Limit</th> -->
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
      
         	 @if($details)
                        @foreach ($details as $value)
      			              <tr>
      			                  <td class="text-center">
                            <img width="42" height="42" src="{{ asset('public/assets/images/master/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                </td>
      			                 <td>{{$value->name}}</td>
      			                 <td>{{$value->mobile}}</td>
                             <td>{{$value->nic}}</td> 
 <!--                             <td>{{$value->credit_limit}}</td> -->
                             <td>{{$value->status_name}}</td>
                                <td class="action-icon">
                               
                                <a href="{{ url('/category') }}/{{ $value->id }}" class="p-r-10 f-18 text-primary" data-toggle="tooltip" data-placement="top" title="" data-original-title="Category"><i class="icofont icofont icofont-company"></i></a>
                                  
                                <a href="{{ url('/ledger-details') }}/{{ $value->id }}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ledger"><i class="icofont icofont-list"></i></a>

                                <a href="{{ url('/edit-master') }}/{{ $value->id }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

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
                        url: "{{url('/remove-master')}}",
                        type: 'POST',
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
                                        window.location="{{url('/get-masters')}}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Customer is safe :)", "error");
           }
        });
  });
  
  </script>

@endsection