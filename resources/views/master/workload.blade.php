@extends('layouts.master-layout')

@section('title','Master Work-Load')

@section('breadcrumtitle','Add Expense')

@section('navmaster','active')
@section('navmasterworkload','active')

@section('content')
    
  

 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Master Work-Load Details</h5>
         <a href="{{ url('create-master') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Customer" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE Master
              </a>
         
         </div>      
       <div class="card-block">
    
           <div class="project-table">
                 <table class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>
      
               <th>Master Name</th>
               <th>Category</th>
               <th>Pending Orders</th>
               <th>Actions</th>
            </tr>
         </thead>
         <tbody>
  
           @if($masters)
                        @foreach ($masters as $value)
                        <?php $count = 1; ?>
                          <tr>
                             
                             <td>{{$value->name }}</td>
                             <td>
                             @foreach($category as $cat)
                                @if($cat->master_id == $value->id )
                                     {{$cat->product_name.", "}}
                                @endif
                             @endforeach
                            </td>
                             
                               
                          
                            <td>{{$value->ordercount }}</td>
                            <td>
                              <a href="{{ url('/work-load') }}/{{ $value->id }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt text-primary f-18" ></i> </a>
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
                                        title: "Success",
                                        text: "Product Received Succussfully.",
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
              swal("Cancelled", "Product is not Received :)", "error");
           }
        });
  });
  
  </script>

@endsection