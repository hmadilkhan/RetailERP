@extends('layouts.master-layout')

@section('title','Loan')

@section('breadcrumtitle','Loan Deduction')

@section('navloan','active')

@section('navdeduct','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Loan Deduction Rules</h5>
         <a href="{{ url('/show-loandeduct') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Rule
              </a>
         </div>     

       <div class="card-block">
 <table class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
	<thead>
		 <th>Rule No.</th>
               <th>Deduction Rule</th>
               <th>Action</th>
	</thead>
	<tbody>
		
		 @foreach($rules as $value)
                 <tr>
                   <td >{{$value->Loan_Deduct_Type_Id}}</td>
                   <td >{{$value->Loan_Deduct_type}} Months</td>
            
                 <td class="action-icon">

                     <a href="{{ url('/edit-loandeduct') }}/{{ $value->Loan_Deduct_Type_Id }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>
                   <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->Loan_Deduct_Type_Id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
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
          searchPlaceholder: 'Search Rules',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

 //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this Rule!",
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
                        url: "{{url('/delete-loandeduct')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove Employee.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-loandeduct') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Rule is safe :)", "error");
           }
        });
  });
 
</script>
@endsection