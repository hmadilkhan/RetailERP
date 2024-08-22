@extends('layouts.master-layout')

@section('title','Business Policy')

@section('breadcrumtitle','View Policies')

@section('navVendorPO','active')

@section('navtax','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Tax Rules List</h5>
         <a href="{{ url('/Tax-create') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Tax Rules
              </a>
         <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>

         </div>      
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               </th>
               <th>Rule No</th>
               <th>Tax Head</th>
               <th>Percentage</th>
                <th>Purchase</th>
                <th>POS</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
          @if($details)
            @foreach($details as $value)
                    <tr>
                          <td>{{$value->id}}</td>
                          <td>{{$value->name}}</td>
                          <td>{{$value->value}}</td>
                        <td>{{($value->show_in_purchase == 0 ? 'No' : 'Yes')}}</td>
                        <td>{{($value->show_in_pos == 0 ? 'No' : 'Yes')}}</td>
                          <td>{{$value->status_name}}</td>
                          <td class="action-icon">
                               
                                <a href="{{ url('/show-tax') }}/{{ Crypt::encrypt($value->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

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

<script type="text/javascript">
   $('#demandtb').DataTable( {

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Tax Rules',
          lengthMenu: '<span></span> _MENU_'
   
        }
        
 
    } );

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
                        url: "{{url('/delete_tax')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove tax rule.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/BusinessPolicy') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Tax Rule is safe :)", "error");
           }
        });
  });




</script>

@endsection
