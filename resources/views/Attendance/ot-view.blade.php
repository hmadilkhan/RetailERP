@extends('layouts.master-layout')

@section('title','Over Time Formula')

@section('breadcrumtitle','Over Time Formula')

@section('navattendance','active')

@section('navot','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Over Time Formula Details</h5>
         <a href="{{url('/show-ot')}}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create OT Formula
              </a>

         </div>      
       <div class="card-block">

     <table  class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
        
         <thead>
            <tr>
              <th>Formula Id</th>
               <th>Over Time Formula</th>
               <th>Action</th>
            </tr>
            </thead>
              <tbody>
                 @foreach($getot as $value)
                    <tr>
                          <td>{{$value->OT_formulaid}}</td>
                          <td>{{$value->OTFormula}}</td>
                          <td class="action-icon">
                               
                                <a href="{{ url('/show-editot') }}/{{ $value->OT_formulaid }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->OT_formulaid }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

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
                        url: "{{url('/delete-ot')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "OT Formula Deleted Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-ot') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "OT Formula is safe :)", "error");
           }
        });
  });

</script>
@endsection

